<?php
class Mmg_Cache_File extends Zend_Cache_Backend_File
{
	/**
	 * 
	 * Enter description here ...
	 * @var Mmg_Cache_File
	 */
	private static $_instance = null;

	/**
	 * 
	 * Enter description here ...
	 * @return Mmg_Cache_File
	 */
	public static function getInstance()
	{
		if(!(self::$_instance instanceof Mmg_Cache_File)) {
			self::$_instance = new Mmg_Cache_File(array(
				'cache_dir' => APP_PATH . '/../tmp/cache'
			));	
		}
		
		return self::$_instance;
	}
	
	public function set($index, $newval)
	{
		
		$index = str_replace('/', '-', $index);
		$this->save($newval, $index);
		return $this;
	}
	
	public function get($index)
	{
		$index = str_replace('/', '-', $index);
		return $this->load($index);
	}
}