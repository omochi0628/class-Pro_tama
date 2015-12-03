<?php
require('dbconnect.php');
$sql_g =sprintf("SELECT * FROM genres");
            $record=mysqli_query($db,$sql_g) or die (mysqli_error($db));
            $nameArray = array();
             while($row = mysqli_fetch_assoc($record)){
                $nameArray[] = $row['name'];
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
                <h1>class_Pro_tama</h1>
            </header>

            <!--main-->
            <main>
                <form action="" method="get">
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

            </main>
            <!--fotter-->
            <footer>
                <hr>
                <small> Copyright (c) 2015 E14C2002 Fujimoto Sachiko, All Rights Reserved.</small>
                <hr>
            </footer>


        </body>

        </html>