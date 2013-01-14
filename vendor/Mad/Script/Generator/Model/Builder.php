<?php
/**
 * Mad Model Generator
 *
 * @category   Mad
 * @package    Mad_Script_Generator
 * @copyright  (c) 2007-2009 Maintainable Software, LLC
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @author 		g.d.stanev@gmail.com <Georgi Stanev>
 */
class Mad_Script_Generator_Model_Builder
{
	/**
	 * 
	 * @var Mad_Script_Generator_Parser_Abstract
	 */
	protected $_parser;
	
	/**
	 * @var array <Mad_Script_Generator_Model>
	 */
	protected $_models = array();
	
	/**
	 * 
	 * @param Mad_Script_Generator_Parser_Abstract $parser
	 */
	public function __construct(Mad_Script_Generator_Parser_Abstract $parser)
	{
		$this->_parser = $parser;
	}
	
	/**
	 * 
	 * @param string	$tableName
	 * @param bool		$addAssocs 	
	 * @return Mad_Script_Generator_Model
	 */
	public function factoryModel($tableName, $addAssocs = true)
	{
		$assignAttributes = 
			function(
				Mad_Script_Generator_Parser_Abstract $parser, 
				Mad_Script_Generator_Model $model
			) {
				foreach ($parser->getProperties($model->tableName) as $fieldName => $fieldType) {
					$model->addField(
						new Mad_Script_Generator_Field($fieldName, $fieldType)
					);
				}
			};
		
		
		if($this->_parser instanceof Mad_Script_Generator_Parser_Db) {
		
			$model = new Mad_Script_Generator_Model(
				$tableName,
				Mad_Support_Inflector::classify($tableName)
			);

			$assignAttributes($this->_parser, $model);

			return $model;
		}
				
		if($this->_parser instanceof Mad_Script_Generator_Parser_File) {

			/* @var $realModel Mad_Model_Base */
			$model = new Mad_Script_Generator_Model(
				$tableName, 
				get_class($realModel = $this->_parser->getModelByTableName($tableName))
			);

			if($addAssocs) {
				//Add associations to current model
				foreach (
					((is_array($realModel->getAssociationList())) ? ($realModel->getAssociationList()) : (array())) 
					as $assocName => $realAssoc
				) {
					//describing real variables
					$assocType 		= $realAssoc[0];
					$assocOptions 	= $realAssoc[1];
					
					$realAssocModelName = (isset($assocOptions['className'])) ?
											 ($assocOptions['className']) :
												 (Mad_Support_Inflector::singularize($assocName));
	
					if(!class_exists($realAssocModelName)) 
						continue;
					
					/* @var $realAssocModel Mad_Model_Base */
					$realAssocModel = new $realAssocModelName();
					$assocModel = new Mad_Script_Generator_Model($realAssocModel->tableName(), $realAssocModelName);
					
					//extract middle model if exists
					if(isset($assocOptions['through'])) {
						$middleModelName = $assocOptions['through'];
						/* @var $realMidleModel Mad_Model_Base */
						$realMidleModel = new $middleModelName();
						$middleModel = new Mad_Script_Generator_Model($realMidleModel->tableName(), $middleModelName);
						$assocType = Mad_Model_Association_Base::TYPE_HAS_MANY_THROUGH;
					} else {
						$middleModel = null;
					}
					
					//add assoc to model
					$model->addAssoc(
						Mad_Script_Generator_Association_Abstract::factory(
							$assocType, $model, $assocModel,$middleModel, $assocOptions
						)
					);
				}
			
			}
			
			$assignAttributes($this->_parser, $model);
			
			return $model;
		}
		
		throw new Exception("It's not yet implemented.");
	}
	
	/**
	 * @param bool $addAssocs
	 * @return array <Mad_Script_Generator_Model>
	 */
	public function factoryModels($addAssocs = true)
	{
		if(count($this->_models)) {
			return $this->_models;
		}
		
		foreach ($this->_parser->getTableNames() as $tableName) {	
			$this->_models[] = $this->factoryModel($tableName, $addAssocs);
		}
		
		return $this->_models;
	}
	
	/**
	 * 
	 * @return Mad_Script_Generator_Parser_Abstract
	 */
	public function getParser()
	{
		return $this->_parser;
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @param Closure $factory
	 * @return array <Mad_Script_Generator_Association_Abstract>
	 */
	
	protected function _crowHasOneAndMany(Mad_Script_Generator_Model $model, Closure $factory)
	{
		$modelForeignField = Mad_Support_Inflector::foreignKey($model->tableName);
		$assocs = array();
		foreach ($this->factoryModels() as $currentModel) {
			/* @var $currentModel Mad_Script_Generator_Model */
			if($currentModel->getFieldByName($modelForeignField)) {
				$assocs[] = $factory($model, $currentModel);
			}
		}
		
		return $assocs;
	}
	
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return array <Mad_Script_Generator_Association_HasOne>
	 */
	public function suggestionsHasOne(Mad_Script_Generator_Model $model)
	{
		return $this->_crowHasOneAndMany($model, function(Mad_Script_Generator_Model $model, Mad_Script_Generator_Model $currentModel){
			return new Mad_Script_Generator_Association_HasOne($model, $currentModel);
		});
	}
	
	/**
	 * @return Mad_Script_Generator_Model $model
	 * @return array <Mad_Script_Generator_Association_HasMany>
	 */
	public function suggestionsHasMany(Mad_Script_Generator_Model $model)
	{
		return $this->_crowHasOneAndMany($model, function(Mad_Script_Generator_Model $model, Mad_Script_Generator_Model $currentModel){
			return new Mad_Script_Generator_Association_HasMany($model, $currentModel);
		});		
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return Ambigous <multitype:, Mad_Script_Generator_Association_BelongsTo>
	 */
	public function suggestionsBelongsTo(Mad_Script_Generator_Model $model)
	{
		$assocs = array();
		foreach ($model->collectForeignFields() as $field) {
			/* @var $field Mad_Script_Generator_Field */
			$fieldName = str_replace('_id', '', $field->fieldName);
			
			try {
				$assocModel = $this->factoryModel(Mad_Support_Inflector::tableize($fieldName));	
			} catch (Exception $e) { continue; }
			
			$assocs[] = new Mad_Script_Generator_Association_BelongsTo($model, $assocModel);
		}
		
		return $assocs;
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return array <Mad_Script_Generator_Association_Through>
	 */
	public function suggestionsHasManyThrough(Mad_Script_Generator_Model $model)
	{
		$assocs = array();
		
		foreach ($this->suggestionsHasMany($model) as $hasMany) {
			/* @var $hasMany Mad_Script_Generator_Association_HasMany */
				
			foreach ($hasMany->assocModel->collectForeignFields() as $foreignKey) {
				
				/* @var $foreignKey Mad_Script_Generator_Field */
				if(Mad_Support_Inflector::foreignKey($model->tableName) == $foreignKey->fieldName) continue;
				
				$tableName = Mad_Support_Inflector::foreignKeyToTableName($foreignKey->fieldName);
				
				try {
					$assocModel = $this->factoryModel($tableName);	
				} catch (Exception $e) {continue;}
				
				/* @var $assoc Mad_Script_Generator_Association_HasManyThrough */
				$assoc = new Mad_Script_Generator_Association_HasManyThrough($model, $assocModel, $hasMany->assocModel);
				$assoc->addOption('className', $assocModel->modelName);
				$assoc->addOption('through', Mad_Support_Inflector::pluralize($hasMany->assocModel->modelName));
				$assocs[] = $assoc;
			}
		}
		
		return $assocs;
	}
}