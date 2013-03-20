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
class Mad_Script_Generator_Association_HasAndBelongsToMany extends Mad_Script_Generator_Association_HasMany_Abstract
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
		return $this->_generateComments(array(
			'_commentsAccessor', '_commentsAccessorIds', '_commentsAccessorCount',
			'_commentsMethodAdd',
			'_commentsMethodReplace', '_commentsMethodDelete', '_commentsMethodClear', '_commentsMethodFind'
		), 'Has-And-Belongs-To-Many Association');
	}
}