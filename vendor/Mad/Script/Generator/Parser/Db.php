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
	 * @var Horde_Db_Adapter_Mysql_Schema
	 */
	protected $_schema;
	
	public function __construct(Horde_Db_Adapter_Mysql_Schema $schema)
	{
		$this->_schema = $schema;
	}
	
	/**
	 * 
	 * @param string $tableName
	 * @return array array('fieldName' => 'fieldType')
	 */
	public function getFields($tableName)
	{
		$fields = array();
		foreach ($this->_schema->columns($tableName) as $column) {
			/* @var $column Horde_Db_Adapter_Mysql_Column */	
			$comment = $column->getSqlType();
			if($column->getComment()) {
				$comment .= "<br />{$column->getComment()}";
			} 
			
			$fields[] = new Mad_Script_Generator_Field($column->getName(), $column->getType(), $comment);
		}
		
		return $fields;
	}
	
	/**
	 * @return array
	 */
	public function getTableNames()
	{
		return $this->_schema->tables();
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