<?php
header("Content-Type: text/javascript");
?>
var zizai_captcha_start_time = new Date;
var zizai_captcha_interval_id = null;

<?php
$time = time();
print "var zizai_captcha_start_time_stamp = ".$time."\n";
?>


function zizai_captcha_set_timer(){
<?php
$config = parse_ini_file("config.ini");

print "    var expire = ".$config["expire"].";\n";
?>
    
    if(zizai_captcha_interval_id !== null){
        clearInterval(zizai_captcha_interval_id);
    }
    
    var ima = new Date();
    var s_time = ima.getTime();
    
    var keika = 0;
    
    var i_id = setInterval(function(){
        ima = new Date();
        keika = (ima.getTime() - s_time) / 1000;
        
        if(keika >= expire){
            clearInterval(i_id);
            alert("認証用画像の有効時間を超過したため、画像を更新します。");
            zizai_captcha_reload();
        }
    },1000);
    
    zizai_captcha_interval_id = i_id;
}

function zizai_captcha_reload(){
    var ima = new Date();
    var keika = Math.floor((ima.getTime() - zizai_captcha_start_time)/1000);
    
    var time_stamp = zizai_captcha_start_time_stamp + keika;
<?php
print "    document.getElementById('zizai_captcha_image').src = '".dirname($_SERVER["REQUEST_URI"])."/image.php?t='+time_stamp;";
?>
    document.getElementById('zizai_captcha_time').value = time_stamp;
    
    zizai_captcha_set_timer();
}

function zizai_captcha_get_html(){
<?php
print "    return '<input type=\"hidden\" name=\"zizai_captcha_time\" value=\"".$time."\" id=\"zizai_captcha_time\">";
print "<img src=\"".dirname($_SERVER["REQUEST_URI"])."/image.php?t=".$time."\" alt=\"\" id=\"zizai_captcha_image\">";
print "<img src=\"".dirname($_SERVER["REQUEST_URI"])."/reload.png\" onclick=\"zizai_captcha_reload();return false\" style=\"cursor: pointer;\"><br>";
print "<input type=\"text\" name=\"zizai_captcha_check\" style=\"width: 256px; font-size: x-large;\"><br>';\n";
?>
}

function zizai_captcha_show(){
    document.write(zizai_captcha_get_html());
    
    zizai_captcha_set_timer();
}