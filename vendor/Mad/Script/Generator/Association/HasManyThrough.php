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
	 * @return Mad_Script_Generator_Association_HasManyThrough
	 */
	protected function _setDefaultName()
	{		
		$this->_name = ($this->_isStandartAssocName()) ? 
						(Mad_Support_Inflector::pluralize($this->assocModel->modelName)) : 
							(
								Mad_Support_Inflector::pluralize($this->assocModel->modelName) . 
								Mad_Support_Inflector::pluralize('Through' . $this->middleModel->modelName)
							);
		
		return $this;
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
			'_commentsMethodAdd', '_commentsMethodCreate',
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
	
	/**
	 * @return string
	 */
	protected function _commentsMethodReplace()
	{
		
		$examples = '   Manage MIDDLE (through) table of relations
				      
				      The manage actions are 
				      - Add row in middle table if doesn\'t exists in DB but i passed in @param $assocFKs
				      - Delete row in middle table if exists in DB but it\'s not passed in @param $assocFKs
				      - Update row in middle table if exists in DB AND is passed in parameter
				      
				      Example: (Tables: users - users_addresses - addresses) => Relation $user->hasMany(\'Addresses\', array(\'through\' => \'UsersAddresses\'))
				      
				      - Add (If there is no rows with user_id = 1 and address_id = 1 || 2 || 3, new three rows will be added)
				        / @var $user User /
				       ' . $this->_codeFormatter('$user = User:find(1);
				        
				        $user->replaceAddresses(array(1, 2, 3), function($addressId){
				        	return(\'fieldFromUsersAddress\' => $value);
				        });') .' 
				        
				       - Delete (If there is rows with user_id = 1 and address_id = 1 and 2 and 3 ), the row with user_id = 1 and address_id = 3 will be removed
				        ' . $this->_codeFormatter('$user->replaceAddresses(array(1, 2), function($addressId){
				        	return(\'fieldFromUsersAddress\' => $value);
				        });') . '
				        
				        
				       - Update Every row from intersection between existing rows and ids from @param $assocFKs';
		
		$examples = str_replace(PHP_EOL, '<br />', $examples);
		
		$methodName = Mad_Support_Inflector::pluralize('replace' . $this->getName());
		 
		return (
				" * @method\t\tNULL{$this->getTabsNull()}{$methodName}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("$methodName()", 2) .
				$methodName . '(array $array)' .
				Mad_Script_Generator_Model_Writer::computeTabs($methodName . 'array(array $array)').
				"Replace the associated collection with the given list. Will only perform update/inserts when necessary "  .
				$examples  .
				PHP_EOL
		);
	}
}