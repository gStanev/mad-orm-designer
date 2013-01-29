<?php
class Mmg_Application extends Zend_Application
{
	/**
	 * @param array $settings
	 * @throws Exception
	 * @return array
	 */
	public function confDbSettings(array $settings = array())
	{
		$allowedKeys = array('host', 'username', 'dbname', 'password');
		if(func_num_args()) {
			foreach ($settings as $key => $val) {
				if(!in_array($key, $allowedKeys)) {
					throw new Exception("{$key} it's not allowed key.Allowed keys are" . implode(', ', $allowedKeys));
				}
	
				$this->getSession()->{"db{$key}"} = $val;
			}
		}
	
		$dbSettingsIni 	= $this->getOption('database');
		$settings 		= array();
	
		
		foreach (($allowedKeys + array_keys($dbSettingsIni)) as $settingKey) {
			if(!empty($this->getSession()->{"db{$settingKey}"})) {
				$settings[$settingKey] = $this->getSession()->{"db{$settingKey}"};
				continue;
			}
				
			if(!empty($dbSettingsIni[$settingKey])) {
				$settings[$settingKey] = $dbSettingsIni[$settingKey];
				continue;
			}
				
			if($settingKey === 'password') continue;
				
			throw new Mmg_Exception_Conf_DB("DB:{$settingKey} can't be empty.");
		}
	
		@new mysqli($settings['host'], $settings['username'], $settings['password'], $settings['dbname']);
	
		// check connection
		if (mysqli_connect_errno()) {
			throw new Mmg_Exception_Conf_DB('Connect failed: '. mysqli_connect_error());
		}
	
		return $settings;
	}
	
	/**
	 * @param string $modelsPath
	 * @throws Exception
	 * @return string
	 */
	public function confModelsPath($modelsPath = null)
	{
		$settedModelsPath = '';
		if(func_num_args()) {
			$this->getSession()->modelsPath = $settedModelsPath = $modelsPath;
		}
	
		$modelsPathIni = $this->getOption('modelsPath');
		if(!empty($modelsPathIni)) {
			$settedModelsPath =  $modelsPathIni;
		}
	
		if(!empty($this->getSession()->modelsPath)) {
			$settedModelsPath = $this->getSession()->modelsPath;
		}
	
		if(empty($settedModelsPath)) {
			throw new Mmg_Exception_Conf_ModelsPath('Models path can\'t be empty.');
		} else if(!is_writable($settedModelsPath)) {
			throw new Mmg_Exception_Conf_ModelsPath('Models path must be writtable.');
		}
	
		return $settedModelsPath;
	}
	
	/**
	 * @return array
	 */
	public function confLibPaths()
	{
		return (array) $this->getOption('externalLibs');
	}
	
	/**
	 * @return Zend_Session_Namespace
	 */
	public function getSession()
	{
		$this->getBootstrap()->bootstrap('session');
		return new Zend_Session_Namespace($this->getOption('appnamespace'));
	}
}