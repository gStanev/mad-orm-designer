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
    	$models = array();
    	foreach ($this->_getModelBuilder()->factoryModels() as $model) {
    		/* @var $model Mad_Script_Generator_Model  */
    		if(class_exists($model->modelName)) continue;
    		
    		$models[] = $model;
    	}
    	
    	$this->view->models = $models;
    }
    
    public function generatedAction()
    {	
    	$this->_helper->viewRenderer->setScriptAction('index');
    	$this->view->models = $this->_getModelBuilder('file')->factoryModels();
    }
}