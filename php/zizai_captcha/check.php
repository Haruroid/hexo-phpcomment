<?php
function zizai_captcha_check($input_str = NULL,$key_time = NULL){
    if($input_str === NULL){
        if(isset($_POST["zizai_captcha_check"])){
            $input_str = $_POST["zizai_captcha_check"];
        }else{
            return FALSE;
        }
    }
    
    if($key_time === NULL){
        if(isset($_POST["zizai_captcha_time"])){
            $key_time = $_POST["zizai_captcha_time"];
        }else{
            return FALSE;
        }
    }
    
    // mb_language("uni");
    // mb_internal_encoding("utf-8");
    // mb_http_input("auto");
    // mb_http_output("utf-8");
    
    // $input_str = mb_convert_kana($input_str,"r");
    
    $time = time();
    $post_t = intval($key_time);
    
    $config = parse_ini_file(dirname(__FILE__)."/config.ini");
    $seed = parse_ini_file(dirname(__FILE__)."/seed.ini");
    
    if($post_t > $time || $post_t < $time - $config["expire"]){
        return FALSE;
    }
    
    $symbol = array("+","/");
    $alphabet = array("A","B");
    
    if($seed["last_updated"] <= $post_t){
        $seed_str = $seed["new_seed"];
    }else{
        $seed_str = $seed["old_seed"];
    }
    
    $str = str_replace($symbol,$alphabet,strtoupper(substr(base64_encode(md5($post_t.$seed_str,TRUE)),0,$config["characters_count"])));
    
    if(strtoupper($input_str) !== $str){
        return FALSE;
    }
    
    return TRUE;
}
?>