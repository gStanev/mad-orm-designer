<?php 
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
abstract class Mmg_Controller_Action extends  Zend_Controller_Action {
	
	/**
	 * @var Mad_Script_Generator_Model_Builder
	 */
	protected $_modelBuilder;
	
	protected function _disableView()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
	
	/**
	 * 
	 * @param array $output
	 * @return void
	 */
	protected function _sendJson(array $output)
	{
		$this->getResponse()
			->setHeader('Content-Type', 'application/json')
			->setBody(Zend_Json::encode($output));
	}
	
	/**
	 * @return Mad_Script_Generator_Model_Builder
	 */
	protected function _getModelBuilder()
	{
		if(!($this->_modelBuilder instanceof Mad_Script_Generator_Model_Builder)) {
			$this->_modelBuilder =
				new Mad_Script_Generator_Model_Builder(
					new Mad_Script_Generator_Parser_Db(
						new Horde_Db_Adapter_Mysqli(
							$this->_getApplication()->getOption('database')
						)
					)
				);
		}
	
		return $this->_modelBuilder;
	}
	
	/**
	 * @return Zend_Application
	 */
	protected function _getApplication()
	{
		return  Zend_Registry::get('app');
	}
}
