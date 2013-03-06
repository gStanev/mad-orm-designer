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
		$this->_assignModelsToView();
		$this->_assignCurrentModelToView();	
		
	}
	
    public function assocSuggestionsAction()
    {
    	$this->_helper->viewRenderer->setScriptAction('index');
    	$this->_assignModelsToView('db');
    	
    	
    	
    	//filter models which have association suggestions
    	$models = array();
    	$modelBuiler = $this->_getModelBuilder('file');
    	$modelBuiler->factoryModels();
    	
    	foreach ($this->view->models as $model) {
    		/* @var $model Mad_Script_Generator_Model */    		
    		$this->_populateSuggestionAccosModel($model, $modelBuiler->searchModel($model->tableName));		
    		
    		if(count($model->getAssocs())) $models[] = $model;
    	}
    	
    	$this->view->models = $models;
    	
    	$this->_assignCurrentModelToView();
    }
    
    public function graphAction()
    {
    	$this->_helper->viewRenderer->setScriptAction('index');
    	$this->_assignCurrentModelToView();
    	$this->_assignModelsToView();
    	$this->view->isVisibleSideBar = false;
    }
    
    public function modelAssocsAction()
    {
    	$this->_helper->viewRenderer->setScriptAction('index');	 
    	
    	$this->_assignModelsToView('file');
    	$this->_assignCurrentModelToView();
    }
    
    protected function _assignCurrentModelToView()
    {
    	if($this->_getParam('tableName')) {
    		$this->view->currentModel = $this->_getModelBuilder()->factoryModel($this->_getParam('tableName'));
    		return;
    	}
    	
    	if(is_array($this->view->models) && count($this->view->models)) {
    		$this->view->currentModel = current($this->view->models);
    	}
    }
    
    /**
     * 
     * @param string $parserType 'file', 'db'
     * @return void
     */
    protected function _assignModelsToView($parserType = 'file')
    {

    	if($parserType === 'file') {
    		$models = $this->_getModelBuilder('file')->factoryModels(false);
    		$this->view->models = array();
    		 
    		foreach ($models as $model) {
    			/* @var $model Mad_Script_Generator_Model */
    			$this->view->models[$model->tableName] = $model;
    		}
    		 
    		ksort($this->view->models);
    		
    		return;
    	}
    	
    	
    	if($parserType === 'db') {
    		$models = array();
    		foreach ($this->_getModelBuilder()->factoryModels(true) as $model) {
    			/* @var $model Mad_Script_Generator_Model  */
    			$models[$model->tableName] = $model;
    		}
    		
    		$this->view->models = $models;
    		
    		return;
    	}
    	
    	throw new Exception("Parser type : {$parserType} is not supported.");
    }
}