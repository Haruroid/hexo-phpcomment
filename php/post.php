<?php
include "zizai_captcha/check.php";

if(zizai_captcha_check()){
    try {
        $name = htmlspecialchars($_POST["Comment_name"]);
        $comment = htmlspecialchars($_POST["Comment_comment"]);
        $page = htmlspecialchars($_POST["Comment_page"]);
        $ip = $_SERVER["REMOTE_ADDR"];

        $config = parse_ini_file(dirname(__FILE__)."/config.ini");
        $pdo = new PDO('sqlite:'.$config["db_dir"]);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec("CREATE TABLE IF NOT EXISTS comments(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            comment TEXT NOT NULL,
            ip TEXT NOT NULL,
            page TEXT NOT NULL,
            date TIMESTAMP DEFAULT (datetime(CURRENT_TIMESTAMP,'localtime'))
        )");
    
        $stmt = $pdo->prepare("INSERT INTO comments(name, comment, ip, page) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name,$comment, $ip, $page]);
        print '投稿しました<br>';
        print '<script>localStorage.setItem("isFinished","true");</script>';
    }
    catch(Exception $e) {
        print 'エラー:'. $e;
        print '<script>localStorage.setItem("isFinished","false");</script>';
    }   
}else{
    print '画像認証に失敗しました。<br>';
}
print "リダイレクト中...";
$referer = htmlspecialchars($_POST["Comment_echoback"]);
print '<script>setTimeout(function(){window.location.href = "'. $referer .'"},3000);</script>';
?>
