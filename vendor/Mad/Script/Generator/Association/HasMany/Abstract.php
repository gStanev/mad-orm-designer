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
abstract class Mad_Script_Generator_Association_HasMany_Abstract  extends Mad_Script_Generator_Association_Abstract
{
	/**
	 * @return string
	 */
	protected function _commentsAccessor()
	{
		return (
				" * @property\tMad_Model_Collection" .
				Mad_Script_Generator_Model_Writer::computeTabs('Mad_Model_Collection') .
				"\${$this->getMethodName()}{$this->getTabsMethodName(1)}" .
				"Access collection of associated objects:".
				$this->_codeFormatter('$documents = $folder->documents;') .
				"<br/><br/>Assign array of associated objects and save associations:" .
				$this->_codeFormatter('$folder->documents = array(Document::find(123), Document::find(234)); <br />$folder->save();') .
				PHP_EOL
				);
	}

	/**
	 * @return string
	 */
	protected function _commentsAccessorIds()
	{
		$accessorName = "\$" . Mad_Support_Inflector::singularize($this->getMethodName()) . "Ids";

		return (
				" * @property\tarray" .
				Mad_Script_Generator_Model_Writer::computeTabs('array') .
				$accessorName .
				Mad_Script_Generator_Model_Writer::computeTabs($accessorName, 2) .
				"Access array of associated object’s primary keys: " .
				$this->_codeFormatter('$documentIds = $folder->documentIds;') .
				"<br /><br />Set associated objects by primary keys:".
				$this->_codeFormatter('$folder->documentIds = array(123, 234);<br />$folder->save();') .
				PHP_EOL
		);
	}

	/**
	 * @return string
	 */
	protected function _commentsAccessorCount()
	{
		$toSing = Mad_Support_Inflector::singularize($this->getMethodName());

		return (
				" * @property\tint" .
				Mad_Script_Generator_Model_Writer::computeTabs('int') .
				"\${$toSing}Count" .
				Mad_Script_Generator_Model_Writer::computeTabs("\${$this->getMethodName()}Count", 2) .
				"Get the count of associated objects:" .
				$this->_codeFormatter('$docCount = $folder->documentCount;') .
				PHP_EOL
				);
	}

	/**
	 * @return string
	 */
	protected function _commentsMethodAdd()
	{
		$toClass	= $this->assocModel->modelName;
		$toMethod 	= Mad_Support_Inflector::singularize($this->getName());
		$method 	= "add{$toMethod}($toClass \$" . lcfirst($toClass) . ")";

		return (
				" * @method\t\tNULL{$this->getTabsNull()}add{$toMethod}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("add{$toMethod}()", 2) .
				$method.
				Mad_Script_Generator_Model_Writer::computeTabs($method) .
				"Add an associated object to the collection and save it: " .
				$this->_codeFormatter('$folder->addDocument(Document::find(123));<br />$folder->save();') .
				PHP_EOL
		);
	}

	/**
	 * @return string
	 */
	protected function _commentsMethodBuild()
	{
		$toMethod 	= Mad_Support_Inflector::singularize($this->getName());
		$toClassTabs    = Mad_Script_Generator_Model_Writer::computeTabs($this->assocModel->modelName);
		$method  = "build{$toMethod}(array \$array)";

		return (
				" * @method\t\t{$this->assocModel->modelName}{$toClassTabs}build{$toMethod}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("build{$toMethod}()", 2) .
				$method .
				Mad_Script_Generator_Model_Writer::computeTabs($method) .
				"Add associated object by building a new one (associated object doesn’t save if you don't save master object). " .
				$this->_codeFormatter('$document = $folder->buildDocument(array(\'name\' => \'New Document\')); <br />$document->save();') .
				"<br/><br/>Available fields: <br /> {$this->assocModel->generateFieldsDocBlock()}".
				PHP_EOL
				);
	}

	/**
	 * @return string
	 */
	protected function _commentsMethodCreate()
	{
		$toMethod 	= Mad_Support_Inflector::singularize($this->getName());
		$toClassTabs    = Mad_Script_Generator_Model_Writer::computeTabs($this->assocModel->modelName);
		$method  = "create{$toMethod}(array \$array)";

		return (
				" * @method\t\t{$this->assocModel->modelName}{$toClassTabs}create{$toMethod}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("create{$toMethod}()", 2) .
				$method .
				Mad_Script_Generator_Model_Writer::computeTabs($method) .
				"Add associated object by creating a new one (saves associated object). <br/><br/>Available fields {$this->assocModel->generateFieldsDocBlock()}" .
				PHP_EOL
				);
	}

	/**
	 * @return string
	 */
	protected function _commentsMethodReplace()
	{
		$methodName = Mad_Support_Inflector::pluralize('replace' . $this->getName());

		return (
				" * @method\t\tNULL{$this->getTabsNull()}{$methodName}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("$methodName()", 2) .
				$methodName . '(array $array)' .
				Mad_Script_Generator_Model_Writer::computeTabs($methodName . 'array(array $array)').
				"Replace the associated collection with the given list. Will only perform update/inserts when necessary "  .
				$this->_codeFormatter('$folder->replaceDocuments(array(Document::find(123), Document::find(234)));<br />$folder->replaceDocuments(array(123, 234));<br />$folder->save();')  .
				PHP_EOL
				);
	}

	/**
	 * @return string
	 */
	protected function _commentsMethodDelete()
	{
		$methodName = Mad_Support_Inflector::pluralize('delete' . $this->getName());

		return  (
				" * @method\t\tNULL{$this->getTabsNull()}{$methodName}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("$methodName()", 2) .
				$methodName . '(array $array)' .
				Mad_Script_Generator_Model_Writer::computeTabs($methodName . 'array(array $array)').
				"Delete specific associated objects from the collection" .
				PHP_EOL
				);
	}

	/**
	 * @return string
	 */
	protected function _commentsMethodClear()
	{
		$methodName = Mad_Support_Inflector::pluralize('clear' . $this->getName());

		return (
				" * @method\t\tNULL{$this->getTabsNull()}{$methodName}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("$methodName()", 2) .
				$methodName . '()' .
				Mad_Script_Generator_Model_Writer::computeTabs($methodName . 'array()').
				"Clear all associated objects" .
				PHP_EOL
				);
	}

	/**
	 * @return string
	 */
	protected function _commentsMethodFind()
	{
		$methodName = Mad_Support_Inflector::pluralize('find' . $this->getName());

		return (
				" * @method\t\t{$this->assocModel->modelName}|Mad_Model_Collection" .
				Mad_Script_Generator_Model_Writer::computeTabs($methodName) . "{$methodName}()" .
				Mad_Script_Generator_Model_Writer::computeTabs("$methodName()", 2) .
				$methodName . '(string $type, array $options, array $binds = null)' .
				Mad_Script_Generator_Model_Writer::computeTabs($methodName . 'array()').
				"Search for a subset of documents within the associated collection" .
				PHP_EOL
				);
	}
}