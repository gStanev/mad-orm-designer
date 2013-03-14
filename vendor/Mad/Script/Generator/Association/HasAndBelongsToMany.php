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
class Mad_Script_Generator_Association_HasAndBelongsToMany extends Mad_Script_Generator_Association_Abstract
{
	/**
	 * @return Mad_Script_Generator_Association_HasAndBelongsToMany
	 */
	protected function _setDefaultName()
	{
		$this->_name = $this->assocModel->modelName;
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getAllowedOptionKeys()
	{
		return Mad_Model_Association_HasAndBelongsToMany::$validOptions;
	}

	public function generateComments()
	{
		$toClassTabs    = Mad_Script_Generator_Model_Writer::computeTabs($this->assocModel->modelName);
         
        $output = PHP_EOL;
        $output .= " * @property\t{$this->assocModel->modelName}{$toClassTabs}\${$this->getMethodName()} \n";
        $output .= " * @method\t\t{$this->assocModel->modelName}{$toClassTabs}build{$this->getMethodName()}() \n";
        $output .= " * @method\t\t{$this->assocModel->modelName}{$toClassTabs}create{$this->assocModel->modelName}() \n";
       
        return $output;
	}
}