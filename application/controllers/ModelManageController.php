<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class ModelManageController extends Mmg_Controller_Action
{
    public function saveAction()
    {
    	$this->_disableView();
    	$writer = new Mad_Script_Generator_Model_Writer(ROOT_PATH . '/application/models');
    	
    	$nodes = $this->_getParam('nodes');
    	
    	foreach ($this->_getParam('edges') as $modelName => $assocs) {
    		$model = new Mad_Script_Generator_Model($nodes[$modelName]['tableName'], $modelName);
    		
    		//Add fields to model
    		foreach ($nodes[$modelName]['fields'] as $field) {
    			$model->addField(
    				new Mad_Script_Generator_Field($field['fieldName'], $field['fieldType'])		
    			);
    		}
    		
    		//Add assocs to model
    		foreach ($assocs as $assocModelName => $assocOpt ) {
    			$model->addAssoc($this->_buildAssoc($assocModelName, $assocOpt));
    		}
    		
    		$writer->writeModel($model);
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
}