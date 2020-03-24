<?php
header("Content-Type: image/png");

function mozi_to_su($str,$pos){
    return hexdec(substr($str,-$pos,1));
}

if(isset($_GET["t"]) == FALSE){
    exit(0);
}

$get_t = intval($_GET["t"]);
$time = time();

$config = parse_ini_file("config.ini");
$seed = parse_ini_file("seed.ini");

if($get_t > $time || $get_t < $time - $config["expire"]){
    exit(0);
}

if($seed["last_updated"] + $config["expire"] > $get_t){
    $seed_str = $seed["new_seed"];
}else{
    $seed_str = (string) mt_rand();
    
    $h = fopen(dirname(__FILE__)."/seed.ini","w");
    fwrite($h,"last_updated=".$get_t."\n");
    fwrite($h,"new_seed='".$seed_str."'\n");
    fwrite($h,"old_seed='".$seed["new_seed"]."'\n");
    fclose($h);
}

$h = md5($get_t.$seed_str,TRUE);
$h_16 = bin2hex($h);

$symbol = array("+","/");
$alphabet = array("A","B");
$str = str_replace($symbol,$alphabet,substr(base64_encode($h),0,8));

$img = imagecreatetruecolor(256,48);
imagefilledrectangle($img,0,0,256,64,imagecolorallocate($img,0xff,0xff,0xff));

$fonts = array("Rounded-Kinokawa-Regular.otf","Kinokawa-Medium.otf");

for($cnt = 0; $cnt < $config["characters_count"]; $cnt++){
    $pos = $cnt * 3;
    $val_3 = mozi_to_su($h_16,$pos + 3);
    imagettftext($img,12 + mozi_to_su($h_16,$pos + 1),mozi_to_su($h_16,$pos + 2) * 6 - 45,$cnt * 28 + $val_3 + 16,32,imagecolorallocate($img,0x00,0x00,0x00),"font/".$fonts[$val_3 % 2],substr($str,$cnt,1));
}

$noise = imagecreatefrompng("noise/noise_". mozi_to_su($h_16,1).".png");
imagecopy($img,$noise,0,0,0,0,256,48);

imagepng($img);
?>