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
abstract class Mad_Script_Generator_Association_Abstract
{
	/**
	 * 
	 * @var Mad_Script_Generator_Model
	 */
	public $masterModel;

	/**
	 * 
	 * @var Mad_Script_Generator_Model
	 */
	public $assocModel;
	
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $masterModel
	 * @param Mad_Script_Generator_Model $assocModel
	 */
	public function __construct(Mad_Script_Generator_Model $masterModel, Mad_Script_Generator_Model $assocModel)	
	{
		$this->masterModel	= $masterModel;
		$this->assocModel 	= $assocModel;
	}
	
	/**
	 * @return string
	 */
	abstract public function getName();
	
	/**
	 * @return string
	 */
	abstract public function generateComments();
	
	/**
	 * @return string
	 */
	public function getTabsNull()
	{
		return Mad_Script_Generator_Model_Writer::computeTabs('NULL');	
	}
	
	/**
	 * @return string
	 */
	public function getTabsCollection()
	{
		return Mad_Script_Generator_Model_Writer::computeTabs('Mad_Model_Collection');	
	}
	
	/**
	 * @param int $additionalTabs
	 * @return string
	 */
	public function getTabsMethodName($additionalTabs = 0)
	{
		return Mad_Script_Generator_Model_Writer::computeTabs(
			$this->getMethodName(),
			$additionalTabs
		);
	}
	
	/**
	 * @return string
	 */
	public function getMethodName()
	{
		return Mad_Support_Inflector::camelize($this->getName(), 'lower');
	}
	
	/**
	 * 
	 * @param array $options
	 */
	public function generateDefinition(array $options = array())
	{
        $output = "\t\t" . '$this->' . $this->getType() . '("' . $this->getName() . '"';
		$output .= $this->_generateDefinitionOpts($options);
        $output .= ');' . PHP_EOL;
       
        return $output;
	}
	
	/**
	 * 
	 * @param array $options
	 * @return string
	 */
	protected function _generateDefinitionOpts(array $options)
	{
		$output = '';
		if(count($options)) {
            $dump = var_export($options, true);
            $dump = str_replace("\n", "\n\t\t\t", $dump);
            $output .= ', ' . PHP_EOL . "\t\t\t";
            $output .= str_replace('0 => ', '', $dump);   
            $output .= PHP_EOL . "\t\t";
        }
        
        return $output;
	}
	
	/**
	 * @return string
	 */
	public function getType()
	{
		$classPieces = explode('_', get_class($this));
		return lcfirst(array_pop($classPieces));
	}
	
	/**
	 * 
	 * @param string $code
	 * @return string
	 */
	protected function _codeFormatter($code)
	{
		return "<br /><br />Example:<br /> <code>" . $code . "</code>";
	}
}