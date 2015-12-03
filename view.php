<?php 
        session_start();
        
        require('dbconnect.php');
        
        if(empty($_REQUEST['id'])){
            header('Location: talk.php');
            exit();
        }

        //投稿取得
        $sql=sprintf('SELECT m.name, m.icon, p.* FROM members m,posts p WHERE m.id=p.member_id AND p.id=%d ORDER BY p.created DESC',
                    mysqli_real_escape_string($db,$_REQUEST['id'])
                    );
        $posts=mysqli_query($db,$sql) or die(mysqli_error($db));
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
            <h1>class_Pro_tama</h1>
        </header>

        <!--main-->
        <main>
            <p>&laquo;<a href="talk.php">一覧に戻る</a></p>
            <?php if($post=mysqli_fetch_assoc($posts)): ?>
                <div class="msg">
                    <img src="member_icon/<?php echo htmlspecialchars($post['icon'],ENT_QUOTES,'UTF-8'); ?>" width="48" height="48" alt="<?php echo htmlspecialchars($post['name'],ENT_QUOTES,'UTF-8'); ?>">
                    <p>
                        <?php if(isset($post['message']))echo htmlspecialchars($post['message'],ENT_QUOTES,'UTF-8'); ?>
                            <span class="name">(<?php echo htmlspecialchars($post['name'],ENT_QUOTES,'UTF-8'); ?>)</span> [
                            <a href="talk.php?res=<?php echo htmlspecialchars($post['id'],ENT_QUOTES,'UTF-8'); ?>">Re</a>]
                    </p>
                    <p class="day">
                        <?php echo htmlspecialchars($post['created'],ENT_QUOTES,'UTF-8'); ?>
                    </p>
                </div>
                <?php else: ?>
                    <p>その投稿は削除されたか、URLが間違っています。</p>
                <?php endif; ?>
        </main>
        <!--fotter-->
        <footer>
            <hr>
            <small> Copyright (c) 2015 E14C2002 Fujimoto Sachiko, All Rights Reserved.</small>
            <hr>
        </footer>


    </body>

    </html>