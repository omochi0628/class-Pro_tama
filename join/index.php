<?php
        require('../dbconnect.php');

        session_start();

        //$_POSTの各配列の中身が空でないかをチェック
        //・[name],[userID],[password]が空かを確認し、$error配列を生成、中に'blanl'を入れておく。
        //　→『未入力』エラーメッセージの出力に使用
        //・[password]は、strlenファンクションで確認し、4文字未満であれば'length'を入れておく。
        //　→『文字数不足』エラーメッセージ出力に使用
        if(isset($_POST['name'],$_POST['userID'],$_POST['password'])){
            //エラー項目の確認
            if($_POST['name']==''){
                $error['name']='blank';
            }
            if($_POST['userID']==''){
                $error['userID']='blank';
            }
            if(strlen($_POST['password']) < 4){
                $error['password']='length';
            }
            if($_POST['password']==''){
                $error['password']='blank';
            }
            //ファイルチェック
            //$_FILESはファイルアップ時に、ファイルが代入される変数、フォームのname属性がキーになる→$_FILES['image']
            //連想配列でもあるので、ファイル名・一時的にアップロードされたファイル名が代入→$_FILES['image']['name']でいったん$fileName(変数)に取り出している。
            //issetで、$fileName(変数)が空でないかチェック
            //substrファンクションで拡張子を取り出す→$ext(変数)に代入
            //jpg,gif,png以外の拡張子だった場合→$error変数に'type'を代入
            $fileName=$_FILES['image']['name'];
            if(isset($fileName)){
                $ext=substr($fileName, -3);
                if($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){
                    $error['image'] = 'type';
                }
            }
            
            //重複チェック
            if(isset($_POST['userID'])){
                $sql=sprintf('SELECT COUNT(*) AS cnt FROM members WHERE userID="%s"',
                            mysqli_real_escape_string($db,$_POST['userID'])
                            );
                $record=mysqli_query($db,$sql) or die(mysqli_error($db));
                $table=mysqli_fetch_assoc($record);
                if($table['cnt'] > 0){
                    $error['userID_dup']='duplicate';
                }
            }
            
            if(empty($error)){
                //画像up
                //move_uploaded_fileファンクションを使用
                //アップロードされたファイル名を、ファイルアップロードした時間に変更(画像重複を回避)
                //拡張子をそのまま利用することが出来る。
                $image = date('YmdHis') . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'],'../member_icon/'. $image);
                
                $_SESSION['join']=$_POST;
                $_SESSION['join']['image']=$image;
                header('Location:check.php');
                exit();
            }
        }

        //書き直し
        if(isset($_REQUEST['action'])){
            if($_REQUEST['action'] == 'rewrite'){
                $_POST=$_SESSION['join'];
                $error['rewrite']=true;
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
            <small> Copyright (c) 2015 E14C2002 Fujimoto Sachiko, All Rights Reserved.</small>
            <hr>
        </footer>


    </body>

    </html>