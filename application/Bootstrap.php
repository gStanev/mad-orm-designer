<?php
/**
 * 
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
	public function getConfig()
	{
		return Zend_Registry::get('app')->getOptions();	
	}
}

