<?php
//https://qiita.com/39_isao/items/a5b4940138bced936de0
try {
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

    if(isset($_GET['page'])) {
        $page = $_GET['page'];
        $stmt = $pdo->prepare("SELECT name,comment,date FROM comments WHERE page = ?");
        $stmt->execute([$page]);
        $ret = $stmt->fetchAll();
        echo json_encode($ret);
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
?> 