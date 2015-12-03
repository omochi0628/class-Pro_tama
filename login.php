<?php
        require('dbconnect.php');

        session_start();

        if(isset($_COOKIE['userID'])){
            $_POST['userID'] = $_COOKIE['userID'];
            $_POST['password'] = $_COOKIE['password'];
            $_POST['save'] = 'on';
        }
        
//        if(isset($_POST['userID'],$_POST['password'])){
            //ログイン処理
            if(isset($_POST['userID']) && isset($_POST['password'])){
                    $sql=sprintf('SELECT * FROM members WHERE userID="%s" AND password="%s"',
                                mysqli_real_escape_string($db,$_POST['userID']),
                                mysqli_real_escape_string($db,sha1($_POST['password']))
                                );
                    $record=mysqli_query($db,$sql) or die(mysqli_error($db));
                    if($table=mysqli_fetch_assoc($record)){
                        //ログイン成功
                        $_SESSION['id']=$table['id'];
                        $_SESSION['time']=time();
                        
//                        ログイン情報を記録
                        if($_POST['save'] == 'on'){
                            setcookie('userID',$_POST['userID'],time()+60*60*24*14);
                            setcookie('password',$_POST['password'],time()+60*60*24*14);
                        }
                        header('Location: talk.php');
                        exit();
                    }else{
                        $error['login_feiled']='failed';
                    }
            }else{
                $error['login_blank']='blank';
            }
?>

    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>class_Pro_tama</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/index.css" media="all">
    </head>

    <body>
        <!--header-->
        <header>
            <h1>class Pro_tama</h1>
        </header>

        <!--main-->
        <main>
            <h2>ログイン</h2>
            <div id=lead>
                <p>ユーザーIDとパスワードを入力してください。</p>
            </div>
            <form action="" method="post">
                <dl>
                    <!--メアド------------>
                    <dt>ユーザーID</dt>
                    <dd>
                        <input type="text" name="userID" size="35" maxlength="255" value="<?php if(isset($_POST['userID']))
                        echo htmlspecialchars($_POST['userID']); ?>">
                        <?php if(isset($error['login_blank'])): ?>
                        <p class="error">*メールアドレスとパスワードを入力して下さい。</p>
                        <?php endif; ?>
                        <?php if (isset($error['login_feiled'])): ?>
                        <p class="error">*ログイン失敗。正しく入力して下さい。</p>
                        <?php endif;
//                        echo $error['login_blank'].$error['login_feiled'];
                        ?>
                    </dd>
                    <!--パスワード------------->
                    <dt>パスワード</dt>
                    <dd>
                        <input type="password" name="password" size="35" maxlength="255"<?php
                        if(isset($_POST['password']))
                        echo htmlspecialchars($_POST['password']); ?>>
                    </dd>
                    <!--自動ログイン------------->
                    <dt>ログイン情報記録</dt>
                    <dd>
                        <input name="save" type="checkbox" id="save" value="on">
                        <label for="save">次回から自動ログインする</label>
                    </dd>
                </dl>
                <div>
                    <input type="submit" value="ログイン">
                </div>
            </form>
        </main>
        <!--fotter-->
        <footer>
            <hr>
            <small> Copyright (c) 2015 E14C2002 Fujimoto Sachiko, All Rights Reserved.</small>
            <hr>
        </footer>


    </body>

    </html>