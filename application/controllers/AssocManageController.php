<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class AssocManageController extends Mmg_Controller_Action
{

	public function newChooseTypeAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->view->masterModel = $this->_getModelBuilder('file')->factoryModel($this->_getParam('tableName'));
		$this->view->types = $this->_getAssocTypes();	
	}
	
	public function newInitAction()
	{
		$params = $this->_getAllParams();
		$this->_helper->layout()->disableLayout();
		$this->view->masterModel 	= $masterModel 	= $this->_getModelBuilder('file')->factoryModel($params['assoc']['masterModel']['tableName']);
		$this->view->models 		= $models 		= $this->_getModelBuilder('file')->factoryModels(false);
		$this->view->assocModel 	= $assocModel 	= (isset($params['assoc']['assocModel'])) ? 
															($this->_getModelBuilder('file')->factoryModel($params['assoc']['assocModel']['tableName'])) : 
																(current($models));
		
		$this->view->assoc = Mad_Script_Generator_Association_Abstract::factory($params['assoc']['type'], $masterModel, $assocModel);
	}
	
	
    public function formAction() 
    {
    	$this->view->assoc = $this->_factoryAssociation($this->_getParam('assoc'));
    	
    	$this->_helper->layout()->disableLayout();
    }
    
    public function testAction()
    {
     	$assoc 			= $this->_factoryAssociation($this->_getParam('assoc'));
     	$modelName 		= $assoc->masterModel->modelName;
    	$assocName 		= $assoc->assocModel->modelName;
    	$assocRealName 	= $assoc->getName();
    	
     	$this->view->models = $modelName::find('all', array());
     	$this->view->assoc = $this->_factoryAssociation($this->_getParam('assoc'));
     	
     	$this->view->associations = 
     			(($masteModelData = $this->_getParam('masterModel')) && isset($masteModelData['id'])) ? 
     				((($association = $modelName::find($masteModelData['id'])->{$assoc->getMethodName()}) instanceof Mad_Model_Base) ? (new Mad_Model_Collection(new $assocName(), array($association))) : ($association)) : 
     					(new Mad_Model_Collection(new $modelName(), array()));

     	if(is_null($this->view->associations)) {
     		$this->view->associations = array();
     	}
     	
    	$this->_helper->layout()->disableLayout();
    	
    	$this->view->sqlQueries = Mad_Model_Base::logger()->getFirstWritter()->getStack();
    	$this->view->sqlQueries = array_filter($this->view->sqlQueries, function($item) {
    		if(strpos($item, 'SHOW FIELDS') === false){
    			return true;
    		}
    	});
    	
    	array_shift($this->view->sqlQueries);
    }
    

    /**
     * 
     * @return Mad_Script_Generator_Association_Abstract
     * @param array $assocData
     */
    protected function _factoryAssociation($assocData)
    {
    	$opts = (isset($assocData['options']) && is_array($assocData['options'])) ? ($assocData['options']) : (array());
    	
    	$middleModel =
	    	(isset($assocData['middleModel'])) ?
		    	($this->_getModelBuilder()->factoryModel($assocData['middleModel']['tableName'])) :
		    		(null);
    	 
    	return 
	    	Mad_Script_Generator_Association_Abstract::factory(
	    			$assocData['type'],
	    			$this->_getModelBuilder()->factoryModel($assocData['masterModel']['tableName']),
	    			$this->_getModelBuilder()->factoryModel($assocData['assocModel']['tableName']),
	    			$middleModel,
	    			$opts
	    	);
    }
}