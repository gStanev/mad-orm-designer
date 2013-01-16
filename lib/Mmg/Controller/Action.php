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
	
	public function init()
	{
		parent::init();
		$this->_generateAllModels();
	}
	
	protected function _disableView()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
	
	/**
	 * Generate models which doesn't exists 
	 * 
	 * @return void
	 */
	protected function _generateAllModels()
	{
		$writer = new Mad_Script_Generator_Model_Writer($this->_getApplication()->getOption('modelsPath'));
		
		foreach ($this->_getModelBuilder('db')->factoryModels() as $model) {
			/* @var $model Mad_Script_Generator_Model  */
			if(class_exists($model->modelName)) continue;
		
			$writer->writeModel($model);
		}
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
	 * @param string $parserType 'db', 'file'
	 * @return Mad_Script_Generator_Model_Builder
	 */
	protected function _getModelBuilder($parserType = 'db')
	{

		if(
			!($this->_modelBuilder instanceof Mad_Script_Generator_Model_Builder) ||
			(
				($this->_modelBuilder->getParser() instanceof Mad_Script_Generator_Parser_Db && $parserType == 'file') ||
				($this->_modelBuilder->getParser() instanceof Mad_Script_Generator_Parser_File && $parserType == 'db')
			)
		) {
			$this->_loadModelBuilder($parserType);		
		}
	
		return $this->_modelBuilder;
	}
	
	/**
	 * @param string $parserType 'db', 'file'
	 * @return 
	 */
	private function _loadModelBuilder($parserType)
	{
		if($parserType === 'db') { 
			$parser = new Mad_Script_Generator_Parser_Db(
					new Horde_Db_Adapter_Mysqli(
							$this->_getApplication()->getOption('database')
					)
			);
		} else if($parserType === 'file') {
			$parser = new Mad_Script_Generator_Parser_File(
				$this->_getApplication()->getOption('modelsPath')
			);
		} else {
			throw new Exception('Parser type must be "db" or "file"');
		}
		
		$this->_modelBuilder = new Mad_Script_Generator_Model_Builder($parser);
	}
	
	/**
	 * @return Zend_Application
	 */
	protected function _getApplication()
	{
		return  Zend_Registry::get('app');
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function _getAssocTypes()
	{
		return  array(
				Mad_Model_Association_Base::TYPE_BELONGS_TO,
				Mad_Model_Association_Base::TYPE_HAS_MANY,
				Mad_Model_Association_Base::TYPE_HAS_MANY_THROUGH,
				Mad_Model_Association_Base::TYPE_HAS_ONE,
		);
	}
	
	/**
	 *
	 * @param Mad_Script_Generator_Model $model
	 * @return void
	 */
	protected function _populateSuggestionAccosModel(
			Mad_Script_Generator_Model $notGeneratedModel,
			Mad_Script_Generator_Model $generatedModel
	) {
		//suggestionsHasOne
		foreach (array('suggestionsBelongsTo', 'suggestionsHasMany', 'suggestionsHasManyThrough') as $method) {
			foreach ($this->_getModelBuilder()->{$method}($notGeneratedModel) as $assoc) {
				if($generatedModel->issetAssoc($assoc))
					continue;
					
				$notGeneratedModel->addAssoc($assoc);
			}
		}
	}
}
