<?php
class Mmg_Cache_Object
{
	private static $_instance = null;
	
	/**
	 * 
	 * @var ArrayObject
	 */
	private $_storage;
	
	private function __construct()
	{
		$this->_storage = new ArrayObject();	
	}
	
	public static function getInstance()
	{
		if(!(self::$_instance instanceof Mmg_Cache_Object)) {
			self::$_instance = new Mmg_Cache_Object();
		}
		
		return self::$_instance;
	}
	
	public function set($index, $newval)
	{
		$this->_storage->offsetSet($index, $newval);
		return $this;
	}
	
	public function get($index)
	{
		return $this->_storage->offsetGet($index);
	}
}