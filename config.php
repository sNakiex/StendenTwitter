<?php
class config{
      // private constructor function to prevent external instantiation
      public function __construct() {}
      public $siteTitle = "Stenden Twitter";
      public $baseurl = "http://stendentwitter.com";
      public $reportSiteErrors = 1;
      public $reportSiteErrorsAdmin = 0;
      public $APC_cache = 0;
      public $debug = 0;
      public $logs = 0;
      public $twig_debug = 0;
      public $twig_cache = 0;
      public $banAttemptMinutes = 0.1;
      public $db_host = "localhost";
      public $db_name = "twitter";
      public $db_user = "root";
      public $db_pass = "";
      public $bcrypt_salt = '$2y$15$';
	}
?>