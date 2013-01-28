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
class Mad_Script_Generator_Association_HasMany extends Mad_Script_Generator_Association_HasMany_Abstract
{
	/**
	 * @return string
	 */
	public function getName()
	{
		return Mad_Support_Inflector::pluralize($this->assocModel->modelName);
	}
	
	/**
	 * @return array
	 */
	public function getAllowedOptionKeys()
	{
		return Mad_Model_Association_HasMany::$validOptions;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Mad_Script_Generator_Association_Abstract::generateComments()
	 */
	public function generateComments()
	{   
		return $this->_generateComments(array(
				'_commentsAccessor', '_commentsAccessorIds', '_commentsAccessorCount',
				'_commentsMethodAdd', '_commentsMethodBuild', '_commentsMethodCreate',
				'_commentsMethodReplace', '_commentsMethodDelete', '_commentsMethodClear', '_commentsMethodFind'
		), 'Has Many Association');     
	}
}