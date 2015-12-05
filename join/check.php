<?php
        session_start();
        require('../dbconnect.php');

        if(!isset($_SESSION['join'])){
            header('Location:index.php');
            exit();
        }
        
        if(!empty($_POST)){//登録処理
            $sql=sprintf('INSERT INTO members SET name="%s",userID="%s",password="%s",icon="%s",created="%s"',
                        mysqli_real_escape_string($db,$_SESSION['join']['name']),//ニックネームを無害化
                        mysqli_real_escape_string($db,$_SESSION['join']['userID']),//ユーザIDを無害化
                        mysqli_real_escape_string($db,sha1($_SESSION['join']['password'])),//パスワードを暗号化
                        mysqli_real_escape_string($db,$_SESSION['join']['image']),//アイコン画像を無害化
                        date('Y-m-d H:i:s')
                        );
            mysqli_query($db,$sql) or die(mysqli_error($db));
            unset($_SESSION['join']);//セッションから入力情報を削除
            header('Location:thanks.php');
            exit();
        }
?>

    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>class Pro_tama</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../css/index.css" media="all">
    </head>

    <body>

        <!--header-->
        <header>
            <h1>class_Pro_tama -会員登録-</h1>
        </header>

        <!--main-->
        <main>
            <h2>登録内容確認</h2>
            <form action="" method="post">
                <input type="hidden" name="action" value="submit">
                <dl>
                    <!--ニックネーム------------>
                    <dt>ニックネーム</dt>
                        <?php echo htmlspecialchars($_SESSION['join']['name'],ENT_QUOTES,'UTF-8'); ?>
                    <!--メアド------------->
                    <dt>メールアドレス</dt>
                        <?php echo htmlspecialchars($_SESSION['join']['userID'],ENT_QUOTES,'UTF-8'); ?>
                    <!--パスワード------------->
                    <dt>パスワード</dt>
                        【*******】
                    <!--アイコン------------->
                    <dt>アイコン画像</dt>
                        <img src="../member_icon/<?php echo htmlspecialchars($_SESSION['join']['image'],ENT_QUOTES,'UTF-8'); ?>" width="100" height="100" alt="">
                </dl>
                <div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> |
                    <input type="submit" value="登録する">
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