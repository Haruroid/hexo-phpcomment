<?php
class zizai_captcha_config{
    private $config;
            
    function __construct(){
        $this->config = parse_ini_file(dirname(__FILE__)."/config.ini");
    }
    
    private function save(){
        $h = fopen(dirname(__FILE__)."/config.ini","w");
        fwrite($h,"expire=".addslashes($this->config["expire"])."\n");
        fwrite($h,"characters_count=".addslashes($this->config["characters_count"])."\n");
        fclose($h);
    }
    
    function set_expire($expire){
        $expire = intval($expire);
        if($expire < 1){
            $expire = 60;
        }
        
        $this->config["expire"] = $expire;
        
        $this->save();
    }
    
    function get_expire(){
        return $this->config["expire"];
    }
    
    function set_characters_count($characters_count){
        $characters_count = intval($characters_count);
        
        if($characters_count > 8){
            $characters_count = 8;
        }elseif($characters_count < 1){
            $characters_count = 1;
        }
        
        $this->config["characters_count"] = $characters_count;
        
        $this->save();
    }
    
    function get_characters_count(){
        return $this->config["characters_count"];
    }
}
?>