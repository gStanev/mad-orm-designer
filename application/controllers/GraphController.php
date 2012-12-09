<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class GraphController extends Mmg_Controller_Action
{
	public function init()
	{
		$this->_disableView();
		parent::init();
	}
	
	/**
	 * @param $models <Mad_Script_Generator_Model>
	 * @return void
	 */
	protected function _populateSuggestionAccos(array $models)
	{
		foreach ($models as $model) {
			/* @var $model Mad_Script_Generator_Model */
				$this->_populateSuggestionAccosModel($model);
		}
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return void
	 */
	protected function _populateSuggestionAccosModel(Mad_Script_Generator_Model $model)
	{
		foreach ($this->_getModelBuilder()->suggestionsBelongsTo($model) as $assoc) {
			$model->addAssoc($assoc);
		}
		
		foreach ($this->_getModelBuilder()->suggestionsHasMany($model) as $assoc) {
			$model->addAssoc($assoc);
		}
		
		foreach ($this->_getModelBuilder()->suggestionsHasManyThrough($model) as $assoc) {
			$model->addAssoc($assoc);
		}
		
		foreach ($this->_getModelBuilder()->suggestionsHasOne($model) as $assoc) {
			$model->addAssoc($assoc);
		}
	}
	
	
	/**
	 *
	 * @param $models array <Mad_Script_Generator_Model>
	 * @return void
	 */
	protected function  _sendJsonModelGraphData(array $models)
	{
		$nodes = array();
		$edges = array();
		foreach ($models as $model) {
			/* @var $model Mad_Script_Generator_Model */
			$nodes[$model->modelName] = $model->toArray();
			foreach ($model->getAssocs() as $assoc) {
				/* @var $assoc Mad_Script_Generator_Association_Abstract */
				$nodes[$assoc->assocModel->modelName] = $assoc->assocModel->toArray();
				$edges[$model->modelName][$assoc->assocModel->modelName] = $assoc->toArray();
			}
		}
	
		$this->_sendJson(array(
				'nodes' => $nodes,
				'edges' => $edges
		));
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return void
	 */
	protected function  _sendJsonAssocsGraphData(Mad_Script_Generator_Model $model)
	{
		$nodes = array();
		$edges = array();
		$nodes[$model->modelName] = $model->toArray();
		
		foreach ($model->getAssocs() as $assoc) {
			/* @var $assoc Mad_Script_Generator_Association_Abstract */
			$edgeLabel = $assoc->getType() . $assoc->getName();
			
			$nodes[$edgeLabel] = $assoc->toArray();
			$edges[$model->modelName][$edgeLabel] = array();
		}
	
		$this->_sendJson(array(
				'nodes' => $nodes,
				'edges' => $edges
		));
	}
	
	
	/**
	 * @param array $tables not required, if it's empty get all tables
	 */
	public function modelsAction()
	{
		$models = call_user_func(function(array $tables, Mad_Script_Generator_Model_Builder $builder) {
			if(!count($tables)) {
				return $builder->factoryModels(); 
			}
			
			$models = array();
			foreach ($tables as $tableName) {
				$models[] = $builder->factoryModel($tableName);
			}
			
			return $models;
		}, $this->_getParam('tables', array()), $this->_getModelBuilder());
		

		$this->_populateSuggestionAccos($models);
		
		$this->_sendJsonModelGraphData($models);
	}
	
	/**
	 * @param string $tableName
	 */
	public function notGeneratedAssocsAction()
	{
		$model = $this->_getModelBuilder()->factoryModel($this->_getParam('tableName'));
		$this->_populateSuggestionAccosModel($model);
		$this->_sendJsonAssocsGraphData($model);
	}

	public function generatedAction()
	{
	
	}
	
	public function forUpdateAction()
	{
	
	}
}

