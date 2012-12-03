<?php 
abstract class Mmg_Controller_Action extends  Zend_Controller_Action {
	
	protected function _disableView()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
}
