<?php
class Zend_View_Helper_BodyClassName extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @return string like default-graph-index
	 */
	public function bodyClassName()
	{
		return implode(
			'-', 
			array(
				Zend_Controller_Front::getInstance()->getRequest()->getModuleName(),
				Zend_Controller_Front::getInstance()->getRequest()->getControllerName(),
				Zend_Controller_Front::getInstance()->getRequest()->getActionName()
			)		
		);
	}
}