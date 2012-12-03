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
class Mad_Script_Generator_Association_HasManyThrough extends Mad_Script_Generator_Association_Abstract
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
	public function generateDefinition($options = array())
	{
		if(!$this->_isStandartAssocName() && !isset($options['dependent'])) {
			$options['dependent'] = 'none';
		}
		
		$options['through'] = $this->middleModel->modelName;
		
		$output = "\t\t" . '$this->' . Mad_Model_Association_Base::TYPE_HAS_MANY . '("' . $this->getName() . '"';
		$output .= $this->_generateDefinitionOpts($options);
        $output .= ');' . PHP_EOL;
       
        return $output;
	}
	
	/**
	 * @return bool
	 */
	protected function _isStandartAssocName()
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
		$mmcTabs        = $this->getTabsCollection();
        $nullTabs       = $this->getTabsNull();
        $toMethodTabs   = $this->getTabsMethodName();
        $toClassTabs    = Mad_Script_Generator_Model_Writer::computeTabs($this->assocModel->modelName);
       	$toClass		= $this->assocModel->modelName;
        $toMethod		= $this->getMethodName(); 
       	
        $output = PHP_EOL . PHP_EOL;
        $output .= ' * Has Many Through Association' . PHP_EOL;
        $output .= " * @property\tMad_Model_Collection" . Mad_Script_Generator_Model_Writer::computeTabs('Mad_Model_Collection') . "\${$toMethod} \n";
        $output .= PHP_EOL;
        $output .= " * @property\tarray" .   Mad_Script_Generator_Model_Writer::computeTabs('array') .	"\${$toMethod}Ids \n";
        $output .= " * @property\tint" .     Mad_Script_Generator_Model_Writer::computeTabs('int') .	"\${$toMethod}Count \n";
         
        $output .= " * @method\t\t{$toClass}{$toClassTabs}add{$toClass}() \n";
        $output .= " * @method\t\tNULL{$nullTabs}deleteObjects{$toClass}() \n";
        $output .= " * @method\t\t{$toClass}{$toClassTabs}create{$toClass}() \n";
         
        $output .= " * @method\t\tMad_Model_Collection{$mmcTabs}" . Mad_Support_Inflector::pluralize('replace'.$toClass) . "() \n";
        $output .= " * @method\t\tMad_Model_Collection{$mmcTabs}" . Mad_Support_Inflector::pluralize('delete'.$toClass) . "() \n";
        $output .= " * @method\t\tMad_Model_Collection{$mmcTabs}" . Mad_Support_Inflector::pluralize('clear'.$toClass) . "() \n";
        $output .= " * @method\t\tMad_Model_Collection{$mmcTabs}" . Mad_Support_Inflector::pluralize('find'.$toClass) . "() \n";
        $output .= PHP_EOL . PHP_EOL;
       
        return $output;
	}
}