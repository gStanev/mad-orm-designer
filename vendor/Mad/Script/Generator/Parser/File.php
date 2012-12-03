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
	 * @return array array('filedName' => 'fieldType')
	 */
	abstract public function getProperties($tableName);
	
	/**
	 * @return array
	 */
	abstract public function getTableNames();
}