<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class ModelManageController extends Mmg_Controller_Action
{
	public function updateCommentsAction()
	{	
		$writer = new Mad_Script_Generator_Model_Writer($this->_getApplication()->confModelsPath());
		$models = $this->_getModelBuilder('file')->factoryModels();
		
		$parser = $this->_getModelBuilder('db')->getParser();
		
		$this->view->updatedModels 		= array();
		$this->view->notUpdatedModels	= array();
		
		//Update model properties
		foreach ($models as $model) {
			/* @var $model Mad_Script_Generator_Model */
			$model->resetFields($parser);
			
			foreach ($model->getAssocs() as $assoc) {
				/* @var $assoc Mad_Script_Generator_Association_Abstract */
				$assoc->assocModel->resetFields($parser);
			}
			
			try {
				$oldModelContent = $writer->getModelContent($model);
				$writer->writeModel($model);
				
				if(strcmp($oldModelContent, $writer->getModelContent($model)) !== 0) {
					$this->view->updatedModels[] = $model->modelName;
				}
				
			} catch (Exception $e) {
				$this->view->notUpdatedModels[] = $model->modelName;
			}
		}
	}
	
	public function changeNameAction()
	{
		$this->view->currentModel  = $currentModel = $this->_getModelBuilder('file')->factoryModel($this->_getParam('tableName'), false);
		
		if($this->_request->isPost()) {

			$newModel = clone $currentModel;
			$newModel->modelName = $this->_getParam('modelName');
			
			$writer = new Mad_Script_Generator_Model_Writer($this->_getApplication()->confModelsPath());
			if($writer->updateModelName($currentModel, $newModel)) {
				$this->view->currentModel  = $newModel;
				$this->view->message = 'Succeeees.';	
			}
		}
	}
	
	public function showPropertiesAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_assignCurrentModelToView($this->_getModelBuilder('file'));
	}
	
	public function saveAllSuggestionsAction()
	{
		$writer = new Mad_Script_Generator_Model_Writer($this->_getApplication()->confModelsPath());
		
		$generatedModelNames = array();
		foreach ($this->_getModelBuilder()->factoryModels() as $model) {
			/* @var $model Mad_Script_Generator_Model */
			
			$this->_populateSuggestionAccosModel($model, $this->_getModelBuilder('file')->factoryModel($model->tableName));
			
			if(!count($model->getAssocs())) continue; 
			
			$writer->writeModel($model);
			$generatedModelNames[] = $this->_generateSuccessMsg($model);
		}
		
		if(count($generatedModelNames)) {
			$this->view->notyMessage = implode(', ', $generatedModelNames) . ' have been generated in path:' . $writer->modelsFolderPath;
		}
		
		$this->_forward('graph', 'index');
	}
	
	
    public function saveAction()
    {
    	$assocsData = $this->_getParam('nodes');
    	$model = Mad_Script_Generator_Model::fromArray(array_shift($assocsData));

    	$this->_saveModel($model, $assocsData);
    }
    
    public function addAssocAction()
    {
    	$assocsData = $this->_getParam('nodes');
    	$modelData  = array_shift($assocsData);
    	$model = $this->_getModelBuilder('file')->factoryModel($modelData['tableName']);
    	
    	$this->_saveModel($model, $assocsData);
    }
    
    protected function _saveModel(Mad_Script_Generator_Model $model, array $assocsData)
    {
    	$this->_disableView();
    	try {
    		$writer = new Mad_Script_Generator_Model_Writer($this->_getApplication()->confModelsPath());
    		
    		$model->resetFields($this->_getModelBuilder('db')->getParser());
    		
    		foreach ($assocsData as $assocData) {
    			
    			$assoc = Mad_Script_Generator_Association_Abstract::fromArray($assocData);
    			
    			$assoc->assocModel->resetFields($this->_getModelBuilder('db')->getParser());
    			
    			$model->addAssoc($assoc);
    		}
    	
    		$writer->writeModel($model);
    	
    		$this->_sendJson(array(
    				'id' 		=> uniqid(),
    				'error' 	=> null,
    				'result'	=> $this->_generateSuccessMsg($model)
    		));
    	} catch (Exception $e) {
    		$this->_sendJson(array(
    				'id' 		=> uniqid(),
    				'error' 	=> $e->getMessage(),
    				'result'	=> null
    		));
    	}
    }
    
    /**
     * 
     * @param Mad_Script_Generator_Model $model
     * @return string
     */
    protected function _generateSuccessMsg(Mad_Script_Generator_Model $model)
    {
    	$msg = "The model {$model->modelName} with associations:";
    	$assocNames = array();
    	foreach ($model->getAssocs() as $assoc) {
    		/* @var $assoc Mad_Script_Generator_Association_Abstract */
    		$assocNames[] = $assoc->getName();
    	}
    	
    	$msg .= implode(', ', $assocNames);
    	
    	$msg .= ' were succesfully updated';
    	
    	return $msg;
    }
}