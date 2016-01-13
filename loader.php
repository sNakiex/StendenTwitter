<?php
// Directory Conf
$APP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR;
// Config
require_once $APP_DIR ."/config.php";

// Config object
$config = new Config();

// Session code
session_name("stendentwitter");
session_set_cookie_params(3600 * 24 * 365);
ini_set('session.hash_function', 'sha512');
ini_set('session.hash_bits_per_character', 6);
ini_set('session.cookie_httponly', "On");
SESSION_START();

// PHP Check
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300)
	die('This framework requires PHP 5.3 or higher');

// AutoLoader, CLASSES & TWIG EXTENSIONS
spl_autoload_register(function ($class) {
	global $APP_DIR;
    $filename = $APP_DIR . "/class/". strtolower($class) .".php";
	if(file_exists($filename)){
		include_once($filename);
	}else{
		$filename = $APP_DIR . "/lib/Twig/Extension/".$class.".php";
		if(file_exists($filename)){
			include_once($filename);
		}
	}
});

// Twig engine
include_once($APP_DIR . "/lib/Twig/Autoloader.php");
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem($APP_DIR ."/template");
if($config->twig_cache){
	$twig = new Twig_Environment($loader, array(
		'debug' => $config->twig_debug,
		'cache' => $APP_DIR ."/cache/".$env
	));
}else{
	$twig = new Twig_Environment($loader, array(
		'debug' => $config->twig_debug,
		'cache' => ""
	));
}
//load extentions
$twig->addExtension(new Twig_Extension_User());

// BootStrap framework object
$cms = new bootstrap();
// Database framework object
$db = new MySQL();
if (! $db->Open($config->db_name,$config->db_host,$config->db_user,$config->db_pass)){
	echo "There was a error connecting to the database of ssa, please try again later.";
	@$db->Kill();
}
$db->ThrowExceptions = true;
// User object
$user = new user();


//Set error settings
if($config->debug){
	error_reporting(-1);
    ini_set('display_errors', '1');
}else{
	error_reporting(0);
    ini_set('display_errors', '0');
}
// Stop loading stuff below, because its media
if(strpos((string)$_SERVER['REQUEST_URI'],"media/")){
	$file = $APP_DIR."/public".$_SERVER['REQUEST_URI'];
	if (!file_exists($file)) header("HTTP/1.0 404 Not Found");
    die();
}
//Globals
$cms->addGlobal("baseurl",$config->baseurl);
$cms->addGlobal("siteTitle",$config->siteTitle);
//session/user globals
if($user->isSinged()){
	$cms->addGlobal("isSinged","1");
	$cms->addGlobal("username",$_SESSION['userName']);
}
// Routing
require_once $APP_DIR ."/class/router/route.php";
require_once $APP_DIR ."/class/router/router.php";
$router = new Router();
$router->setBasePath('');
require_once $APP_DIR ."/routes.php";
$route = $router->matchCurrentRequest();
if($route) {
	$array = $route->getTarget();
	if(isset($array["get"])){
		$_GET[$array["get"]] = true;
	}
	if(isset($array["post"])){
		$_GET[$array["post"]] = true;
	}
	foreach($route->getParameters() as $key=>$value){
		$_GET[$key] = mysql_real_escape_string($value);
		$_POST[$key] = mysql_real_escape_string($value);
	}
	$loadPage = $APP_DIR ."/controller/".$array["url"].".php";
}else{
	$loadPage = "";
}

// Begin loading the routed page
if(!empty($loadPage)){
	require_once($loadPage);
}else{
	if($config->logs){
		$t = getdate();
		$time = "[ ".$t['hours'].":".$t['minutes'].":".$t['seconds']." ] ";
		$timeFile = $t['year']."-".$t['mon']."-".$t['mday']."_";
		$fh = fopen($APP_DIR . "/logs/".$timeFile."router_noroute.log", 'a');
		$stringData = $time."No route found for: ".$_SERVER["REQUEST_URI"]."\n";
		fwrite($fh, $stringData);
		fclose($fh);
	}
	header("HTTP/1.0 404 Not Found");
	$cms->error("No route found for '".$_SERVER["REQUEST_URI"]."'");
}
?>