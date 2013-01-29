<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class ModelManageController extends Mmg_Controller_Action
{
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
    	
    		foreach ($this->_getModelBuilder('db')->getParser()->getProperties($model->tableName) as $fieldName => $fieldType) {
    			$model->addField(new Mad_Script_Generator_Field($fieldName, $fieldType));
    		}
    		
    		foreach ($assocsData as $assocData) {
    			
    			$assoc = Mad_Script_Generator_Association_Abstract::fromArray($assocData);
    			foreach ($this->_getModelBuilder('db')->getParser()->getProperties($assoc->assocModel->tableName) as $fieldName => $fieldType) {
    				$assoc->assocModel->addField(new Mad_Script_Generator_Field($fieldName, $fieldType));
    			}
    			
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
	 * @param string $modelName
	 * @param array $assocOpt
	 * @return Mad_Script_Generator_Association_Abstract
	 */
    protected function _buildAssoc($modelName, array $assocOpt)
    {	
    	$masterModel = new Mad_Script_Generator_Model($assocOpt['masterModel']['tableName'], $assocOpt['masterModel']['modelName']);
    	$assocModel  = new Mad_Script_Generator_Model($assocOpt['assocModel']['tableName'], $assocOpt['assocModel']['modelName']);
    	
    	if($assocOpt['type'] == Mad_Model_Association_Base::TYPE_HAS_MANY_THROUGH) {
    		return 
    			new Mad_Script_Generator_Association_HasManyThrough(
    				$masterModel,
    				$assocModel,
    				new Mad_Script_Generator_Model($tableName, $modelName, $assocOpt['middleModel'])
    			);
    	}
    	
    	if($assocOpt['type'] == Mad_Model_Association_Base::TYPE_HAS_MANY) {
    		return new Mad_Script_Generator_Association_HasMany($masterModel, $assocModel);
    	}
    	
    	if($assocOpt['type'] == Mad_Model_Association_Base::TYPE_HAS_ONE) {
    		return new Mad_Script_Generator_Association_HasOne($masterModel, $assocModel);
    	}
    	
    	if($assocOpt['type'] == Mad_Model_Association_Base::TYPE_BELONGS_TO) {
    		return new Mad_Script_Generator_Association_BelongsTo($masterModel, $assocModel);
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