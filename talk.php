<?php 
        session_start();
        
        require('dbconnect.php');

        if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
            //ログイン中
            $_SESSION['time'] = time();
            
            $sql=sprintf('SELECT * FROM members WHERE id=%d',
                        mysqli_real_escape_string($db,$_SESSION['id'])
                        );
            $record=mysqli_query($db,$sql) or die (mysqli_error($db));
            $member=mysqli_fetch_assoc($record);
        }else{
            //ログインしていない
            header('Location: login.php');
            exit();
        }

        //投稿を記録
        if(!empty($_POST)){
            if($_POST['message'] != ''){
                $sql=sprintf('INSERT INTO posts SET member_id=%d,message="%s",r_post_id=%d,created=NOW()',
                             mysqli_real_escape_string($db,$member['id']),
                             mysqli_real_escape_string($db,$_POST['message']),
                             mysqli_real_escape_string($db,$_POST['r_post_id'])
                            );
                mysqli_query($db,$sql) or die(mysqli_error($db));
                
                header('Location: talk.php');
                exit();
            }
        }

        //投稿取得
        $page=@$_REQUEST['page'];
        if($page == ''){
            $page=1;
        }
        $page=max($page,1);

        //最終ページを取得
        $sql='SELECT COUNT(*) AS cnt FROM posts';
        $record=mysqli_query($db,$sql);
        $table=mysqli_fetch_assoc($record);
        $maxPage=ceil($table['cnt']/5);
        $page=min($page,$maxPage);

        $start=($page-1)*5;
        $start=max(0,$start);

        $sql=sprintf('SELECT m.name,m.icon,p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT %d,5',
                    $start);
        $posts=mysqli_query($db,$sql) or die(mysqli_error($db));

        //返信
        if(isset($_REQUEST['res'])){
            $sql=sprintf('SELECT m.name, m.icon, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=%d ORDER BY p.created DESC',
                    mysqli_real_escape_string($db,$_REQUEST['res'])
                    );
            $record=mysqli_query($db,$sql) or die(mysqli_error($db));
            $table=mysqli_fetch_assoc($record);
            $message='@'.$table['name'].''.$table['message'];
        }

        //チェックボックス
         $sql_g =sprintf("SELECT * FROM genres");
         $record=mysqli_query($db,$sql_g) or die (mysqli_error($db));
         $nameArray = array();
            while($row = mysqli_fetch_assoc($record)){
                $nameArray[] = $row['name'];
            }
//        
//        if(isset($_GET["chk"])){
//        }else{
//            $chk=$_GET["chk"];
//            for()
//        }

            
            
        //htmlspecialcharsのショートカット
        function h($value){
            return htmlspecialchars($value,ENT_QUOTES,'UTF-8');
        }

       function makeLink($body, $link_title = null){
                $pattern = '/(?<!href=")https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+/';
                $body = preg_replace_callback($pattern, function($matches) use ($link_title) {
                    $link_title = $link_title ?: $matches[0];
                    return "<a href=\"{$matches[0]}\">$link_title</a>";
                }, $body);
            return $body;
        }

?>

    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>class_Pro_tama</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/talk.css" media="all">
    </head>

    <body>
        <!--header-->
        <header>
            <h1>class Pro_tama</h1>
        </header>

        <!--talk-->

        <div style="tect-aline:right"><a href="logout.php">ログアウト</a></div>
        <div id="wrapper">
            <div id="talk">
                <form action="" method="post">
                    <dl>
                        <dt><h2><?php echo htmlspecialchars($member['name']); ?>さんの質問</h2></dt>
                        <p class="gengo">質問内容</p>
                        <dd>
                            <textarea name="message" cols="50" rows="5"<?php if(isset($message)) echo htmlspecialchars($message,ENT_QUOTES,'UTF-8'); ?>>
                            </textarea>
                            <input type="hidden" name="r_post_id" value="<?php echo htmlspecialchars($_REQUEST['res'],ENT_QUOTES,'UTF-8'); ?>">
                        </dd>
                        <p class="gengo">言語選択</p>
                        <div class="scrollbox">
                            <ul class="lists">
                            <?php
                                $i = 1;
                                foreach($nameArray as $name) {
                                    echo 
                                    "<li class='list'>".
                                        "<input type='checkbox' name='chk[]' valie=".$i.">".$name.
                                    "</li>";
                                $i++;
                                }  
                            ?>
                            </ul>
                        </div>
                    </dl>
                    <div>
                        <input type="submit" value="投稿する">
                    </div>
                </form>
                <hr>

                <?php 
            while($post=mysqli_fetch_assoc($posts)):
            ?>
                    <div id="msg">
                        <img src="member_icon/<?php echo h($post['icon']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>">
                        <p>
                            <?php if(isset($post['message']))echo makeLink(h($post['message'])); ?>
                                <span class="name">(<?php echo h($post['name']); ?>)</span> [<a href="talk.php?res=<?php echo h($post['id']); ?>">Re</a>]
                        </p>
                        <p class="day">
                            <a href="view.php?id=<?php echo h($post['id']); ?>">
                                <?php echo h($post['created']); ?>
                            </a>
                            <?php if($post['r_post_id'] > 0): ?>
                            <a href="view.php?id=<?php echo h($post['r_post_id']); ?>">返信元のメッセージ</a>
                            <?php endif; ?>
                            <?php if($_SESSION['id'] == $post['member_id']): ?>
                            [<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color:#F33;">削除</a>]
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php endwhile; ?>

                        <ul class="paging">
                            <?php if($page > 1) { ?>
                            <li class="list"><a href="talk.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
                            <?php }else{ ?>
                            <li class="idou">前のページへ</li>
                            <?php } ?>
                            <?php if($page < $maxPage){ ?>
                            <li class="idou"><a href="talk.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
                            <?php }else{ ?>
                            <li class="idou">次のページへ</li>
                            <?php } ?>
                        </ul>
            </div>
            <div id=tags>
               <div class="tag">
               <ul class="tagslist">
                       <?php
                        $i = 1;
                        foreach($nameArray as $name) {
                        echo 
                            "<li class='list'>".
                            "<input type='checkbox' name='chk[]' valie=".$i.">".$name.
                            "</li>";
                            $i++;
                        }  
                    ?>
               </ul>
            </div>
        </div>
    </body>

    </html>