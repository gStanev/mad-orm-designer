<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class IndexController extends Mmg_Controller_Action
{
	public function indexAction()
	{
		$this->_helper->viewRenderer->setScriptAction('index');
		$this->view->models = $this->_getModelBuilder('file')->factoryModels(false);
	}
	
    public function assocSuggestionsAction()
    {
    	$this->_helper->viewRenderer->setScriptAction('index');
    	$models = array();
    	foreach ($this->_getModelBuilder()->factoryModels() as $model) {
    		/* @var $model Mad_Script_Generator_Model  */
    		
    		$models[] = $model;
    	}
    	
    	$this->view->models = $models;
    }
}