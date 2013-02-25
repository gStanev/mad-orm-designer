<?php
class Zend_View_Helper_Navi extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @var array
	 */
	protected $_mainItems = array(
		array('controller' => 'index', 			'action' => 'index', 					'label' => 'Models'),
		array('controller' => 'index', 			'action' => 'assoc-suggestions', 		'label' => 'Association Suggestions'),
		array('controller' => 'index', 			'action' => 'graph', 					'label' => 'Graph'),
	);
	
	/**
	 *
	 * @var array
	 */
	protected $_mainSubItems = array(
			array('controller' => 'model-manage', 	'action' => 'change-name', 				'label' => 'Change Model Name')
	);
	
	/**
	 * 
	 * @return Zend_View_Helper_Navi
	 */
	public function navi()
	{
		return $this;
	}
	
	/**
	 * Main header navigation
	 * 
	 * @return string
	 */
	public function main()
	{
		$output =
			'<div class="menu_nav">
				<ul>';
		
		foreach ($this->_factoryNavi($this->_mainItems) as $page) {
			/* @var $page  Zend_Navigation_Page_Mvc */
			$class = ($page->isActive()) ? ('active') : ('');
			$output .=
					"<li class=\"{$class}\"><a href=\"{$page->getHref()}\" title=\"{$page->getLabel()}\">{$page->getLabel()}</a></li>";
		}
		
		$output .=
				'</ul>
		    	<div class="clr"></div>
		    </div>';
		
		
		return $output;
	}
	
	public function mainSub() {
		$tableName 		= Zend_Controller_Front::getInstance()->getRequest()->getParam('tableName');
		$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		$actionName 	= Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		
		ob_start();
		echo '<div class="menu_nav">
		        <ul>
		          <li><a id="save-model" href="javascript:;">' . $this->view->translate('Save Model') . '</a></li>
		          <li><a id="add-assoc" href="javascript:;" >' . $this->view->translate('Add Association') . '</a></li>';
		
		if($controllerName === 'index' && $actionName === 'index') {
			echo '<li><a href="' . 
						$this->view->url(array('controller' => 'model-manage', 'action' => 'change-name', 'tableName' => $tableName )) . '" >' . 
							$this->view->translate('Change Model Name') .'
					  </a>
				</li>';
		}
		
		//array('controller' => 'model-manage',	'action' => 'save-all-suggestions', 	'label' => 'Save All Association Suggestions')
		
		if($controllerName === 'index' && $actionName === 'assoc-suggestions' && count($this->view->models)) {
			echo '<li><a href="' .
					$this->view->url(array('controller' => 'model-manage',	'action' => 'save-all-suggestions' )) . '" >' .
						$this->view->translate('Save All Association Suggestions') .'
					</a>
				</li>';
		}
		
		
		
	/**
	 *  
		          
		        </ul>
		        <div class="clr"></div>
		      </div>
	 */
	     
	}
	

	/**
	 * Side bar navigation 
	 * 
	 * @param array<Mad_Script_Generator_Model> $models
	 */
	public function sideBar($models)
	{
		$output = 
			'<ul class="level-1">';
				
		foreach ($models as $model) { /* @var $model Mad_Script_Generator_Model */
			$class = 
				($model->tableName === Zend_Controller_Front::getInstance()->getRequest()->getParam('tableName')) ? 
					('active') : 
						('');	
			
			$output .= 
				"<li>
					<a 
						href=\"{$this->view->url(array('tableName' => $model->tableName))}\" 
						class=\"{$class}\">{$model->modelName}
					</a>
				</li>";
		}
				
		$output .=
			'</ul>';
		
		return $output;
	}
	
	

	/**
	 * @param array $items
	 * @return Zend_Navigation
	 */
	protected function _factoryNavi(array $items)
	{
		$navi = new Zend_Navigation();
		
		foreach ($items as $item) {
			$page = new Zend_Navigation_Page_Mvc();
			$page->setController($item['controller']);
			$page->setAction($item['action']);
			$page->setLabel($item['label']);
			
			$navi->addPage($page);
		}

		return $navi;
	}
}