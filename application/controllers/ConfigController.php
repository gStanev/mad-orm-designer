<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class ConfigController extends Mmg_Controller_Action
{	
	public function init(){}
	
	public function dbAction()
	{
		if($this->_request->isPost()) {
			try {
				$dbSettings = $this->_getApplication()->confDbSettings($this->_getParam('settings'));				
				$this->_redirect('/');
			} catch (Exception $e) {
				$this->view->message = $e->getMessage();
			}
		}
	}
	
	public function modelsAction()
	{
		if($this->_request->isPost()) {
			try {
				$this->_getApplication()->confModelsPath($this->_getParam('models_path'));
				$this->_redirect('/');
			} catch (Exception $e) {
				$this->view->message = $e->getMessage();
			}
		}
	}
	
	public function libsAction()
	{
		
	}
}