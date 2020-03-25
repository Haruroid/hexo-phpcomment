<?php
$pw = "";
if(isset($_POST["password"])) {
    $pw = htmlspecialchars($_POST["password"]);
}
$data = "";
if(isset($_POST["data"])) {
    $data = $_POST["data"];
}
$action = "";
if(isset($_POST["action"])) {
    $action = $_POST["action"];
}
$retry = "";
if(isset($_POST["retry"])) {
    $retry = $_POST["retry"];
}

$config = parse_ini_file(dirname(__FILE__)."/config.ini");
$banlistfile = dirname(__FILE__)."/banlist";
$banlist = "";

if(strcmp($config["fail2ban"],"enable") == 0){
    $banlist = file_get_contents($banlistfile);
    if(strpos($banlist,$_SERVER["REMOTE_ADDR"]) != 0){
        echo '!';
        exit(0);
    }
}

if(strcmp($pw,"") == 0){
    if(strcmp($retry,"") == 0){
        echo '<html><body><form method="post"><input type="hidden" name="retry" value="1"><input type="password" name="password"><br><button type="submit">ログイン</button></form></body></html>';
        exit(0);
    }
    exit(0);
}else{
    if(strcmp($pw,$config["password"]) != 0){
        if(strcmp($config["fail2ban"],"enable") == 0){
            $banlist .= "\n".$_SERVER["REMOTE_ADDR"];
            file_put_contents($banlistfile, $banlist);
        }
        exit(0);
    }
}

try {
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

    echo "<html>\n<head>\n";
    //!!HEAD
    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">'."\n";
    //HEAD!!
    echo "</head>\n";
?>
<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="ナビゲーションの切替">
<span class="navbar-toggler-icon"></span>
</button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <form method="post">
                <input type="hidden" name="password" value="<?php echo $pw?>">
                <a class="nav-item nav-link <?php if(strcmp($data,"") == 0 && strcmp($action,"") == 00){echo "active";} ?>" href="#" onclick="this.parentNode.submit()">HOME</a>
            </form>
            <form method="post">
                <input type="hidden" name="password" value="<?php echo $pw?>">
                <input type="hidden" name="action" value="list-pages">
                <a class="nav-item nav-link" href="#" onclick="this.parentNode.submit()">ページ一覧</a>
            </form>
        </div>
    </div>
</nav>
<form method="post" id="mainform">
    <input type="hidden" name="password" value="<?php echo $pw?>">
    <input type="hidden" id="action" name="action" value="">
    <input type="hidden" id="data" name="data" value="">
</form>
<script>
function act(action,data){
    document.getElementById("action").value=action;
    document.getElementById("data").value=data;
    document.getElementById("mainform").submit();
}
function del(){
    if(window.confirm("削除しますか？")){
        act("delete",  event.target.parentElement.parentElement.parentElement.children[0].children[0].children[0].textContent);
    }
}

function gotopage(){
    act("list-page",event.target.text);
}
</script>
<?php
    $ret = "";
    if(strcmp($action,"") != 0){
        if(strcmp($action,"list-pages") == 0){
            $stmt = $pdo->prepare("SELECT DISTINCT page FROM comments ORDER BY date DESC");
            $stmt->execute();
            $ret = $stmt->fetchAll();
            echo '<div class="container-fluid">'."\n";
            foreach($ret as $title){
                echo '  <div class="row" style="background-color: rgba(255, 0, 0, 0.1);">'."\n";
                echo '  <a class="nav-item nav-link" href="#" onclick="gotopage()">'.$title["page"].'</a>'."\n";
                echo '  </div>'."\n";
            }
            echo '</div>'."\n";
            exit(0);
        }
        if(strcmp($action,"delete") == 0){
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute([$data]);
            echo '<p>削除しました<br>リダイレクト中...</p>'."\n";
            echo '<script>'."\n";
            echo '  act("","");'."\n";
            echo '</script>'."\n";
            exit(0);
        }
        if(strcmp($action,"list-page") == 0){
            $stmt = $pdo->prepare("SELECT * FROM comments WHERE page = ?");
            $stmt->execute([$data]);
            $ret = $stmt->fetchAll();
        }
    }else{
        //トップページ
        $stmt = $pdo->prepare("SELECT * FROM comments ORDER BY date DESC");
        $stmt->execute();
        $ret = $stmt->fetchAll();
    }
?>

<div class="container-fluid">

<style>
.a-right {
    text-align: right;
}
.a-left {
    text-align: left;
}
</style>

<?php
    foreach($ret as $one){
        echo '<div>'."\n";
        echo '  <div class="row mx-auto" style="background-color: rgba(255, 0, 0, 0.1);">'."\n";
        echo '    <div class="col-sm-1 a-left"><p>'.$one["id"].'</p></div>'."\n";
        echo '    <div class="col-sm-7 a-left"><p>'.$one["name"].'</p></div>'."\n";
        echo '    <div class="col-sm-4 a-right" ><p>'.$one["date"].'</p></div>'."\n";
        echo '  </div>'."\n";
        echo '  <div class="row">'."\n";
        echo '     <div class="col-sm-12 a-left"><p>'.$one["comment"].'</p></div>'."\n";
        echo '  </div>'."\n";
        echo '  <div class="row">'."\n";
        echo '    <div class="col-sm-6 a-left"><a class="nav-item nav-link" href="#" onclick="gotopage()">'.$one["page"].'</a></div>'."\n";
        echo '    <div class="col-sm-5 a-right"><p>'.$one["ip"].'</p></div>'."\n";
        echo '    <div class="col-sm-1 a-right"><a class="nav-item nav-link" href="#" onclick="del()">DEL</a></div>'."\n";
        echo '  </div>'."\n";
        echo '</div>'."\n";
    }
?>

</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<?php
    echo "</html>\n";

}
catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

?>

