<?php
        require('../dbconnect.php');

        session_start();

  
        if(isset($_POST['name'],$_POST['userID'],$_POST['password'])){//$_POSTの各配列の中身が空でないかをチェック
            //エラー項目の確認
            if($_POST['name']==''){//['name']が空白の場合、$error['name']生成、'blanl'を入れる→未入力
                $error['name']='blank';
            }
            if($_POST['userID']==''){//['userID']が空白の場合、$error['userID']生成、'blanl'を入れる→未入力
                $error['userID']='blank';
            }
            if(strlen($_POST['password']) < 4){//『strlen』文字数チェック、4文字未満ならば、$error['password']を生成、'length'を入れる→文字数不足
                $error['password']='length';
            }
            if($_POST['password']==''){//['password']空白の場合、$error['userID']生成、'blanl'を入れる→未入力
                $error['password']='blank';
            }

            
            //iconファイル
            $fileName=$_FILES['image']['name'];//ファイル名・一時的にupされたファイル名代入→$_FILES['image']['name']で$fileNameに取り出す
            if(isset($fileName)){//$fileNameが空でないかチェック
                $ext=substr($fileName, -3);//substrファンクションで拡張子を取り出す→$ext(変数)に代入
                if($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){//jpg,gif,png以外の拡張子だった場合、$error変数に'type'を代入→拡張子エラー
                    $error['image'] = 'type';
                }
            }
            
            //重複チェック
            if(isset($_POST['userID'])){//['userID']が空でないかチェック
                $sql=sprintf('SELECT COUNT(*) AS cnt FROM members WHERE userID="%s"',
                            mysqli_real_escape_string($db,$_POST['userID'])//mysqli_real_escape_string→無害化
                            );//DBにuserIDの件数を探しに行く
                $record=mysqli_query($db,$sql) or die(mysqli_error($db));
                $table=mysqli_fetch_assoc($record);//mysqli_query・mysqli_fetch_assoc→$table変数を取り出す
                if($table['cnt'] > 0){//『COUNT(*) AS』：件数、$table['cnt']で件数を取得→件数が1以上だったら、重複エラー
                    $error['userID_dup']='duplicate';
                }
            }
            
            if(empty($error)){//入力項目に異常なし
                
                //画像up
                $image = date('YmdHis') . $_FILES['image']['name'];//ファイル名を、ファイルupした時間に変更(画像重複を回避),拡張子をそのまま利用可能
                move_uploaded_file($_FILES['image']['tmp_name'],'../member_icon/'. $image);//member_iconに保存
                
                $_SESSION['join']=$_POST;
                $_SESSION['join']['image']=$image;//生成したファイル名をセッションに保存
                header('Location:check.php');
                exit();
            }
        }

        //書き直し
        if(isset($_REQUEST['action'])){//書き直す場合
            if($_REQUEST['action'] == 'rewrite'){
                $_POST=$_SESSION['join'];//消えたフォーム内容を、$_SESSION['join']で書き戻す
                $error['rewrite']=true;//$error['rewrite']→画像再指定エラーを出すため
            }
        }
?>

    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>class_Pro_tama</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../css/index.css" media="all">
    </head>

    <body>
        <!--header-->
        <header>
            <h1>class Pro_tama</h1>
        </header>

        <!--main-->
        <main>
            <h2>会員登録</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <dl>
                    <!--ニックネーム------------>
                    <dt>ニックネーム<span class="required">必須</span></dt>
                    <dd>
                        <input type="text" name="name" size="35" maxlength="255" value="<?php
                    if(isset($_POST['name']))
                    echo htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'); ?>">
                        <?php if (isset($error['name'])): ?>
                            <p class="error">※ニックネームを入力してください。</p>
                            <?php endif; ?>
                    </dd>
                    <!--メアド------------->
                    <dt>ユーザーID<span class="required">必須(半角英数字)</span></dt>
                    <dd>
                        <input type="text" name="userID" size="35" maxlength="255" value="<?php
                    if(isset($_POST['userID']))
                    echo htmlspecialchars($_POST['userID'],ENT_QUOTES,'UTF-8'); ?>">
                        <?php if (isset($error['userID'])): ?>
                        <p class="error">※ユーザーIDを入力してください。</p>
                        <?php endif; ?>
                        <?php if (isset($error['userID_dup'])): ?>
                        <p class="error">※このユーザーIDは既に登録済みです。</p>
                        <?php endif; ?>
                    </dd>
                    <!--パスワード------------->
                    <dt>パスワード<span class="required">必須</span></dt>
                    <dd>
                        <input type="text" name="password" size="10" maxlength="20" value="<?php
                        if(isset($_POST['password']))
                        echo htmlspecialchars($_POST['password'],ENT_QUOTES,'UTF-8'); ?>">
                        <?php if (isset($error['password'])): ?>
                        <p class="error">※4文字以上のパスワードを入力して下さい。</p>
                        <?php endif; ?>
                    </dd>
                    <!--アイコン------------->
                    <dt>アイコン画像</dt>
                    <dd>
                        <input name="image" type="file" id="image" size="35">
                        <?php if(isset($error['image'])): ?>
                        <p class="error">画像を再指定して下さい。(.jpg/.png/.gif)</p>
                        <?php endif; ?>
                        <?php if (isset($error['rewrite'])): ?>
                        <p class="error">画像の再指定をお願いします。</p>
                        <?php endif; ?>
                    </dd>
                </dl>
                <div>
                    <input type="submit" value="登録確認" id="completion">
                </div>
            </form>
        </main>
        <!--fotter-->
        <footer>
            <hr>
            <small> Copyright (c) 2015 Fujimoto Sachiko, All Rights Reserved.</small>
            <hr>
        </footer>


    </body>

    </html>