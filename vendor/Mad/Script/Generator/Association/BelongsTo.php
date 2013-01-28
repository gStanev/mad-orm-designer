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
class Mad_Script_Generator_Association_BelongsTo extends Mad_Script_Generator_Association_Abstract
{
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->assocModel->modelName;
	}	
	
	/**
	 * @return array
	 */
	public function getAllowedOptionKeys()
	{
		return Mad_Model_Association_BelongsTo::$validOptions;
	}
	
	public function generateComments()
	{
		return $this->_generateComments(array(
			'_commentsAccessor', '_commentsBuild', '_commentsCreate'
		), 'Belongs to Association.');	
	}
	
	
	/**
	 * @return string
	 */
	protected function _commentsAccessor()
	{
		$toClassTabs    = Mad_Script_Generator_Model_Writer::computeTabs($this->assocModel->modelName);
		
		return (
				" * @property\t{$this->assocModel->modelName}{$toClassTabs}\${$this->getMethodName()} " .
				"Access the associated object::".
				$this->_codeFormatter('$folder = $document->folder;') .
				"<br/><br/>Assign the associated object and save it::" .
				$this->_codeFormatter('$document->folder = Folder::find(123);$document->save();') .
				PHP_EOL
		);
	}
	
	/**
	 * @return string
	 */
	protected function _commentsBuild()
	{
		$toClass		= $this->assocModel->modelName;
		$toClassTabs    = Mad_Script_Generator_Model_Writer::computeTabs($toClass);
		$method  		= "build{$toClass}(array \$array)";
		
		return (
				" * @method\t\t{$toClass}{$toClassTabs}build{$toClass}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("build{$toClass}()", 2) .
				$method .
				Mad_Script_Generator_Model_Writer::computeTabs($method) .
				"Build a new object to use in the association and save it.<br/><br/>Available fields {$this->assocModel->generateFieldsDocBlock()}" .
				$this->_codeFormatter('$folder = $document->buildFolder(array(\'name\' => \'New Folder\')); $document->save()') .
				PHP_EOL
		);
	}
	
	/**
	 * @return string
	 */
	protected function _commentsCreate()
	{
		$toClass		= $this->assocModel->modelName;
		$toClassTabs    = Mad_Script_Generator_Model_Writer::computeTabs($toClass);
		$method  		= "create{$toClass}(array \$array)";
		
		return (
				" * @method\t\t{$toClass}{$toClassTabs}create{$toClass}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("create{$toClass}()", 2) .
				$method .
				Mad_Script_Generator_Model_Writer::computeTabs($method) .
				"Create a new object to use in the association and save it. build new object to use as association & save new association.<br /> This option will automatically save the associated object, but !not!.<br /> the actual association with the current object until you use save().<br/><br/>Available fields {$this->assocModel->generateFieldsDocBlock()}" .
				$this->_codeFormatter('$folder = $document->createFolder(array(\'name\' => \'New Folder\')); $document->save()') .
				PHP_EOL
		);
	}
}