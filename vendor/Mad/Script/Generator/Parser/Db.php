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
class Mad_Script_Generator_Parser_Db extends Mad_Script_Generator_Parser_Abstract
{
	/**
	 * 
	 * @var Horde_Db_Adapter_Abstract
	 */
	protected $_dbConnection;
	
	public function __construct(Horde_Db_Adapter_Abstract $dbConnection)
	{
		$this->_dbConnection = $dbConnection;
	}
	
	/**
	 * 
	 * @param string $tableName
	 * @return array array('fieldName' => 'fieldType')
	 */
	public function getProperties($tableName)
	{
		$existTable = $this->_dataCollector(
			$this->_dbConnection->execute('SHOW TABLES LIKE "' . mysql_real_escape_string($tableName) . '"')
		);
		
		if(!count($existTable)) {
			throw new Exception("Table name {$tableName} doesn't exists");
		}
		
		$fields = array();
		$collectedData = $this->_dataCollector(
			$this->_dbConnection->execute('SHOW COLUMNS FROM ' . mysql_real_escape_string($tableName)),
			'fetch_object'
		);

		foreach ($collectedData as $data) {
			$fields[$data->Field] = $data->Type;
		}
		
		return $fields;
	}
	
	/**
	 * @return array
	 */
	public function getTableNames()
	{
		return $this->_dataCollector($this->_dbConnection->execute('SHOW TABLES;'));
	}
	
	/**
	 * 
	 * @param mysqli_resul $queryResult
	 * @return array
	 */
	protected function _dataCollector(mysqli_result $queryResult, $handle = 'fetch_row')
	{
		$results = array();
		while ($row = $queryResult->{$handle}()) {
			if($handle == 'fetch_row') {
				$row = current($row);
			}
			
			$results[]  = $row;
		}
		
		return $results;
	}
	
	/**
	 * @return array
	 */
	public function getModelNames()
	{
		$modelNames = array();
		foreach ($this->getTableNames() as $tableName) {
			$modelNames[] = Mad_Support_Inflector::classify($tableName);
		}
		
		return $modelNames;
	}
}