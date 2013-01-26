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
class Mad_Script_Generator_Parser_File extends Mad_Script_Generator_Parser_Abstract
{
	/**
	 * 
	 * @var array <Mad_Model_Base>
	 */
	private $_models = array();
	
	/**
	 * 
	 * @var array like array('User.php' => 'file content ...')
	 */
	protected $_filesContent = array();
	
	/**
	 * Absolute path to models folder
	 * @var string
	 */
	public  $modelsFolderPath;
	
	public function __construct($modelsFolderPath)
	{
		$this->modelsFolderPath = $modelsFolderPath;	
	}
	
	/**
	 * @return array array('filedName' => 'fieldType')
	 */
	public function getProperties($tableName)
	{
		$properties = array();
		$content = 
			$this->_getContentByModelName(
				get_class($this->getModelByTableName($tableName))
			);

		$propertySnippet = 
			substr(
				$content, 
				($start = (
						strpos($content, Mad_Script_Generator_Model_Writer::PROPS_START) + 
						strlen(Mad_Script_Generator_Model_Writer::PROPS_START)
					)
				),
				(strpos($content, Mad_Script_Generator_Model_Writer::PROPS_END) - $start)
			);
		
		foreach (explode("\n", $propertySnippet) as $propRow) {
			$propRowList = explode(" ", $propRow);
			
			if(!isset($propRowList[4]) || !isset($propRowList[3]))
				continue;
			
			$properties[str_replace('$', '', $propRowList[4])] = $propRowList[3];
		}
		
		return $properties;
	}
	
	/**
	 * @return array
	 */
	public function getTableNames()
	{
		$tableNames = array();
		foreach ($this->getModels() as $model) {
			/* @var $model Mad_Model_Base */
			$tableNames[] = $model->tableName();
		}
		
		return $tableNames;
	}
	
	/**
	 * @return array
	 */
	public function getModelNames()
	{
		$modelNames = array();
		foreach ($this->getModels() as $model) {
			/* @var $model Mad_Model_Base */
			$modelNames[] = get_class($model);
		}
		
		return $modelNames;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function _getFilesContent()
	{
		if(count($this->_filesContent)) {
			return $this->_filesContent;
		}
		
		$dirhandle = opendir($this->modelsFolderPath);
		while ($fileName = readdir($dirhandle)) {
			if(strpos($fileName, '.php') === false)
				continue;
			
			$this->_filesContent[$fileName] = file_get_contents($this->modelsFolderPath . DIRECTORY_SEPARATOR . $fileName);			
		}
		
		return $this->_filesContent;
	}
	
	/**
	 * 
	 * @param string $modelName
	 * @throws Exception
	 * @return string
	 */
	protected function _getContentByModelName($modelName)
	{
		foreach ($this->_getFilesContent() as $fileName => $fileContent) {
			if(str_replace('.php', '', $fileName) === $modelName)
				return $fileContent;
		}
		
		throw new Exception("{$modelName} doesn't exsists in folder {$this->modelsFolderPath}");
	}
	
	/**
	 * Load/Cache  in RAM models, and after that return array of models
	 * 
	 * @return array<Mad_Model_Base>
	 */
	public function getModels()
	{
		if(count($this->_models) < 1) {
			foreach ($this->_getFilesContent() as $fileName => $fileContent) {
				$obj = $this->_factoryObj($fileContent, str_replace('.php', '', $fileName));
				$this->_models[] = $obj;
			}
		}
		
		return $this->_models;
	}
	
	/**
	 * 
	 * @param string $tableName
	 * @return Mad_Model_Base
	 */
	public function getModelByTableName($tableName)
	{
		foreach ($this->getModels() as $model) {
			/* @var $model Mad_Model_Base */
			if($model->tableName() == $tableName)return $model;
		}
		
		throw new Exception("{$tableName} doesn't exists in model in folder {$this->modelsFolderPath}");
	}
	
	/**
	 * 
	 * @param string $fileContent
	 * @param string $modelName
	 * @return Mad_Model_Base
	 */
	protected function _factoryObj($fileContent, $modelName)
	{
		//prepare evaluation string
		$toEval = '';
		if(!class_exists($modelName)) {
			$toEval .= str_replace('<?php', '', $fileContent);
		}
		
		$toEval .= " \$obj = new {$modelName}();";
		
		eval($toEval);
		return $obj;
	}
}