<?php 
        session_start();

        //セッション情報
        $_SESSION=array();
        if(ini_get("session.use_cookies")){
            $params=session_get_cookie_params();
            setcookie(session_name(),'',time()-4200,
                     $params['path'],$params['domain'],
                     $params['secure'],$params['httpponly']
                     );
        }

        session_destroy();

        //Cookie情報削除
        setcookie('userID','',time()-3600);
        setcookie('password','',time()-3600);

        header('Location: login.php');
        exit();
?>
        