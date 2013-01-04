<?php
// Define path to application directory
defined('APP_PATH')
    || define('APP_PATH', realpath(dirname(__FILE__) . '/../application'));
    
defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));    

// Define application environment
defined('APP_ENV')
    || define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'development'));
    
defined('MAD_ENV')
    || define('MAD_ENV', APP_ENV);    
    
defined('MAD_ROOT')
    || define('MAD_ROOT', ROOT_PATH);   


defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', ROOT_PATH . '/public');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
	ROOT_PATH . '/lib',	
	ROOT_PATH . '/vendor',
	ROOT_PATH . '/application/models' //@TODO: do this with config value
)));

/**
 * Zend_Config_Ini is not readable ?!? WHY
 */
spl_autoload_register(function($className){
	$fileName = str_replace('_', '/', $className) . '.php'; 
	if (@fopen($fileName, 'r', true)){
		require_once $fileName;
	}
});

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APP_ENV,
    APP_PATH . '/configs/application.ini'
);

Mad_Model_Base::establishConnection(
	$application->getOption('database')
	+ array('cache' => Mmg_Cache_Object::getInstance())	
);
////////////////////////////////////////////////////////////
// INIT MAD
////////////////////////////////////////////////////////
// initialization required by framework


// initialize the default loger. writers and filters are specified in the environment files.
$GLOBALS['MAD_DEFAULT_LOGGER'] = null;
if(in_array(APP_ENV, array('development', 'staging'))) {
	$GLOBALS['MAD_DEFAULT_LOGGER'] = new Zend_Log(new Zend_Log_Writer_Stack());
}

Zend_Registry::set('app', $application);

			
			
//$GLOBALS['MAD_DEFAULT_LOGGER']->addHandler($writer);

/* @var $config Mad_Madness_Configuration */
// priority filters