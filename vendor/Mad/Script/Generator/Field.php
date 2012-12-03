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
class Mad_Script_Generator_Field
{
	/**
	 * 
	 * @var string
	 */
	public $fieldName;

	/**
	 * 
	 * @var string
	 */
	public $fieldType;

	/**
	 * 
	 * @param string $fieldName
	 * @param string $fieldType
	 */
	public function __construct($fieldName, $fieldType)
	{
		$this->fieldName = $fieldName;
		$this->fieldType = $fieldType;
	}
}