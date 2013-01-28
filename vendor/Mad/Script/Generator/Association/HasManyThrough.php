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
class Mad_Script_Generator_Association_HasManyThrough extends Mad_Script_Generator_Association_HasMany_Abstract
{
	/**
	 * 
	 * @var Mad_Script_Generator_Model
	 */
	public $middleModel;
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $masterModel
	 * @param Mad_Script_Generator_Model $assocModel
	 * @param Mad_Script_Generator_Model $middleModel
	 */
	public function __construct(
		Mad_Script_Generator_Model $masterModel, 
		Mad_Script_Generator_Model $assocModel, 
		Mad_Script_Generator_Model $middleModel
	)	
	{
		parent::__construct($masterModel, $assocModel);
		$this->middleModel = $middleModel;
	}
	
	/**
	 * @return array
	 */
	public function getAllowedOptionKeys()
	{
		return Mad_Model_Association_HasManyThrough::$validOptions;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{		
		if($this->_isStandartAssocName()) {
			return Mad_Support_Inflector::pluralize($this->assocModel->modelName);
		}
		
		return (
			Mad_Support_Inflector::pluralize($this->assocModel->modelName) . 
			Mad_Support_Inflector::pluralize('Through' . $this->middleModel->modelName)
		);
	}	
	
	
	/**
	 * 
	 * @param array $options
	 * @return string
	 */
	public function generateDefinition()
	{
		if(!$this->_isStandartAssocName() && !$this->issetOption('dependent')) {
			$this->addOption('dependent', 'none');
		}
		
		$this->addOption('through', $this->middleModel->modelName);
		
		$output = "\t\t" . '$this->' . Mad_Model_Association_Base::TYPE_HAS_MANY . '("' . $this->getName() . '"';
		$output .= $this->_generateDefinitionOpts();
        $output .= ');' . PHP_EOL;
       
        return $output;
	}
	
	/**
	 * @return bool
	 */
	public function _isStandartAssocName()
	{
		return in_array(
			$this->middleModel->modelName,
			array(
				(Mad_Support_Inflector::pluralize($this->masterModel->modelName) . Mad_Support_Inflector::singularize($this->assocModel->modelName)), 
				(Mad_Support_Inflector::pluralize($this->assocModel->modelName) . Mad_Support_Inflector::singularize($this->masterModel->modelName))
			)
		); 
	}
	
	
	public function generateComments()
	{
		return $this->_generateComments(array(
			'_commentsAccessor', '_commentsAccessorIds', '_commentsAccessorCount',
			'_commentsMethodAdd', '_commentsMethodBuild', '_commentsMethodCreate',
			'_commentsMethodReplace', '_commentsMethodDelete', '_commentsMethodClear', '_commentsMethodFind'
		), 'Has Many Through Association');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Mad_Script_Generator_Association_Abstract::toArray()
	 * @return array
	 */
	public function toArray()
	{
		$toArray = parent::toArray();
		$toArray['middleModel'] = array(
			'modelName'	=> $this->middleModel->modelName,
			'tableName'	=> $this->middleModel->tableName
		);
		
		return $toArray;
	}
}