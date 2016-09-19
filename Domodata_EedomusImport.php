<?php

require './vendor/autoload.php';


//setting TimeZone
date_default_timezone_set("Europe/Paris");

// Autoloading api classes
function DomoData_autoloader($class) {
	if (file_exists('./Library/freebox/'. strtolower($class) . '.php')){
		include './Library/freebox/' . strtolower($class) . '.php';
	}
	else{
		include './Library/' . strtolower($class) . '.class.php';
	}
}
spl_autoload_register('DomoData_autoloader');

// Tell log4php to use our configuration file.
Logger::configure('configLog.php');

// Fetch a logger, it will inherit settings from the root logger
$log = Logger::getRootLogger();
$log->setLevel(LoggerLevel::getLevelDebug());

$log->info("------------------------- NEW CONNECTION --------------------------------"); 

// config File read
$configFile=new ReadConfigFile;

//DB Connexion
$Db=new Db($configFile);

//eedomus Init
$eedomus = new eeDomus($configFile);
$eedomus_apiuser  =$configFile->showParam('Eedomus','eedomus_apiuser');
$eedomus_apisecret=$configFile->showParam('Eedomus','eedomus_apisecret');
$eedomus->setLoginInfo($eedomus_apiuser,$eedomus_apisecret);

//General Parameter
$DelayBetweenApiCalls = $configFile->showParam('General','DelayBetweenApiCalls');

/*--------------------------------------
 * Get Periphs Data through API
 ----------------------------------------*/
// insert into DB characteristics of devices
$Db->DbLoadCaracteristiques($eedomus);
    
//
//chargement dernières données ou historique de tous les périphs ayant Synchro = TRUE
$Db->DbLoadPeriphsData($eedomus,$DelayBetweenApiCalls,$log);

// Close DB Connexion
//	TODO Close DB Connexion
?>
