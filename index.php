<?php
$msqlserver = "localhost";
$msqluser = "user2";
$msqlpass = "1234";
$msqldb = 'test2';
session_start();
$request = $_SERVER['REQUEST_URI'];
$request = explode('?', $request)[0];

/* With Session
if(!isset($_SESSION['id']) && isset($_COOKIE['token'])) {
    $token = explode(':', $_COOKIE['token']);
    if($token[0] == 'm.khoshdel81@gmail.com' && $token[1] == md5("$2y$10$9wTMSdtOhQYbxHrd71jgz.snf62xQjwEkTiHXv5sjuugpdwqK6SGu")) {
        $_SESSION['id'] = "1";
    }
} */

// with cookie
$token = [];
if (isset($_COOKIE['token'])) {
    $split = explode(':', $_COOKIE['token']);
    $token = [
        'user' => $split[0],
        'pass' => $split[1]
    ];
}

if($request == '' || $request == '/') {
    require './pages/main.php';
}
elseif($request == '/login' || $request == '/login/') {
    if(isset($token['user'])) {
        header('Location: http://'.$_SERVER['HTTP_HOST'].'/dashboard');
    }
    require './pages/login.php';
}
elseif($request == '/signup' || $request == '/signup/') {
    if(isset($token['user'])) {
        header('Location: http://'.$_SERVER['HTTP_HOST'].'/dashboard');
    }
    require './pages/signup.php';
}
elseif($request == '/logout' || $request == '/logout/') {
    //session_destroy();
    setcookie("token", "", time() - 3600);
    header('Location: http://'.$_SERVER['HTTP_HOST'].'/login');
}
elseif(preg_match('/^\/dashboard/', $request)) {
    /* //with Session
    if(!isset($_SESSION['id'])) {
        header('Location: http://'.$_SERVER['HTTP_HOST'].'/login');
    }
    */
    // With Cookie
    if(!isset($token['user'])) {
        header('Location: http://'.$_SERVER['HTTP_HOST'].'/login');
    } else {
        $conn = new mysqli($msqlserver, $msqluser, $msqlpass, $msqldb);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT id, email, `password` FROM user WHERE email='".$token['user']."'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($token['pass'] == md5($row['password'])) {
                $token['id'] = $row['id'];
            } else {
                setcookie("token", "", time() - 3600);
                header('Location: http://'.$_SERVER['HTTP_HOST'].'/login');
            }
        } else {
            setcookie("token", "", time() - 3600);
            header('Location: http://'.$_SERVER['HTTP_HOST'].'/login');
        }
        $conn->close();
    }
    require './pages/dashboard.php';
}
elseif (preg_match('/^\/products(\/)/', $request)) {
    $request = str_replace('/products', '', $request);
    $part = explode('/', $request);
    $productID = $part[1];
    if (preg_match('/^\/[0-9]+$/', $request)) {
        require './pages/details.php';
    } elseif (preg_match('/^\/[0-9]+\/Update$/', $request)) {
        echo $request;
    }
} elseif($request == '/post') {
    require './post.php';
}
else {
    echo "404";
}
?>