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
		$this->_assignCurrentModelToView();		
		$models = $this->_getModelBuilder('file')->factoryModels(false);
		$this->view->models = array();
		
		foreach ($models as $model) {
			/* @var $model Mad_Script_Generator_Model */
			$this->view->models[$model->tableName] = $model;
		}

		ksort($this->view->models);		
	}
	
    public function assocSuggestionsAction()
    {
    	$this->_helper->viewRenderer->setScriptAction('index');
    	$this->_assignCurrentModelToView();
    	$models = array();
    	foreach ($this->_getModelBuilder()->factoryModels() as $model) {
    		/* @var $model Mad_Script_Generator_Model  */
    		
    		$models[] = $model;
    	}
    	
    	$this->view->models = $models;
    }
    
    
    protected function _assignCurrentModelToView()
    {
    	if($this->_getParam('tableName')) {
    		$this->view->currentModel = $this->_getModelBuilder()->factoryModel($this->_getParam('tableName'));
    	}
    }
}