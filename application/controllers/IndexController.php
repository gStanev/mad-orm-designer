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
    	$this->view->models = $this->_getModelBuilder()->factoryModels();
    }
}

