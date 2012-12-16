<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class AssocManageController extends Mmg_Controller_Action
{

    public function formAction() 
    {
    	$assocData = $this->_getParam('assoc');
    	$opts = (isset($assocData['options']) && is_array($assocData['options'])) ? ($assocData['options']) : (array());
    	$middleModel = 
    		(isset($assocData['middleModel'])) ? 
    			($this->_getModelBuilder()->factoryModel($assocData['masterModel']['tableName'])) : 
    				(null);
    	
    	$this->view->assoc = 
			Mad_Script_Generator_Association_Abstract::factory(
				$assocData['type'], 
				$this->_getModelBuilder()->factoryModel($assocData['masterModel']['tableName']), 
				$this->_getModelBuilder()->factoryModel($assocData['assocModel']['tableName']),
				$middleModel,
				$opts
			);
    	
    	$this->_helper->layout()->disableLayout();
    }
}