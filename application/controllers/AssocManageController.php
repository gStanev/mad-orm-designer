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
    	$this->view->assoc = 
			Mad_Script_Generator_Association_Abstract::factory(
				$assocData['type'], 
				$this->_getModelBuilder()->factoryModel($assocData['masterModel']['tableName']), 
				$this->_getModelBuilder()->factoryModel($assocData['assocModel']['tableName'])
			);
    	
    	foreach ($opts as $opKey => $optValue) {
    		$this->view->assoc->addOption(trim($opKey), trim($optValue));
    	}
    	
    	
    	$this->_helper->layout()->disableLayout();
    }
}