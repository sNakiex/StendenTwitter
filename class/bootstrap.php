<?php
class bootstrap {
    /**
     * Voeg een variable toe aan een Twig template
     * Na executie van deze functie zal de daarop volgende twig templates de variable bevatten
     * @author Raymon Boer
     * @param string	$a	Een gekozen ID voor de Twig om de variable te vangen
     * @param variable	$b	De variable zelf
     */
    function addGlobal($a,$b){
        GLOBAL $twig;
        $twig->addGlobal($a,$b);
        return 1;
    }
    /**
     * Laad een twig template
     * twig zal de gekozen template laden met aangegeven add_globals variablen
     * @author Raymon Boer
     * @param string	$page	Twig template naam
     */
    function loadPage($page){
        GLOBAL $twig;
        $template  = $twig->loadTemplate($page);
        $template->display(array());
        return 1;
    }
    /**
     * Laadt de error pagina
     * @author Raymon Boer
     * @param string	$page	Twig template naam
     */
    function error($text,$die=true){
        $this->addGlobal("error",$text);
        $this->addGlobal("url",$_SERVER);
        $this->loadPage("error.twig");
        if($die){
            $this->kill();
        }
    }
    function redirect($to,$secs=0,$ext=0){
        GLOBAL $config;
        if($secs!=0){
            $this->notify('You will be redirected in '.$secs. ' seconds');
        }
        if($ext==1){
            $redirectCode =  '
			<meta http-equiv="refresh" content="'.$secs.';URL='.$to.'">
			';
        }else{
            $redirectCode =  '
			<meta http-equiv="refresh" content="'.$secs.';URL='.$config->baseurl.'/'.$to.'">
			';
        }
        $this->addGlobal("redirectCode",$redirectCode);
        return 1;
    }

    static public function safe_string_escape($str){
        $len=strlen($str);
        $escapeCount=0;
        $targetString='';
        for($offset=0;$offset<$len;$offset++) {
            switch($c=$str{$offset}) {
                case "'":
                    if($escapeCount % 2 == 0) $targetString.="\\";
                    $escapeCount=0;
                    $targetString.=$c;
                    break;
                case '"':
                    if($escapeCount % 2 == 0) $targetString.="\\";
                    $escapeCount=0;
                    $targetString.=$c;
                    break;
                case '\\':
                    $escapeCount++;
                    $targetString.=$c;
                    break;
                default:
                    $escapeCount=0;
                    $targetString.=$c;
            }
        }
        return $targetString;
    }

    /**
     * Stopt het script.
     * @author Raymon Boer
     * @param string	$page	Twig template naam
     */
    function kill(){
        die();
    }

    function session($key){
        GLOBAL $_SESSION;
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }else{
            return 0;
        }
    }

    function setSession($key,$value){
        GLOBAL $_SESSION;
        $_SESSION[$key]=$value;
        return 1;
    }
}
?>