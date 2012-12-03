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
class Mad_Script_Generator_Model
{
	/**
	 * 
	 * @var string
	 */
	public $tableName;
	
	/**
	 * 
	 * @var string
	 */
	public $modelName;
	
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * 
	 * @var array
	 */
	protected $_assocs = array();
	
	/**
	 * 
	 * @param string $tableName
	 * @param string $modelName
	 */
	public function __construct($tableName, $modelName) 
	{
		$this->tableName = $tableName;
		$this->modelName = $modelName;
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Association_Abstract $assoc
	 * @return Mad_Script_Generator_Model
	 */
	public function addAssoc(Mad_Script_Generator_Association_Abstract $assoc)
	{
		$this->_assocs[] = $assoc;
		
		return $this;
	}
	
	/**
	 * @return array <Mad_Script_Generator_Association_Abstract>
	 */
	public function getAssocs()
	{
		return $this->_assocs;
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Field $field
	 * @return Mad_Script_Generator_Model
	 */
	public function addField(Mad_Script_Generator_Field $field)
	{
		$this->_fields[] = $field;
		
		return $this;
	}
	
	/**
	 * @return array <Mad_Script_Generator_Field>
	 */
	public function getFields()
	{
		return $this->_fields;
	}
	
	/**
	 * 
	 * @param string $fieldName
	 * 
	 * @return Mad_Script_Generator_Field|null
	 */
	public function getFieldByName($fieldName)
	{
		foreach ($this->_fields as $field) {
			/* @var $field Mad_Script_Generator_Field */
			if($field->fieldName == $fieldName) {
				return $field;
			}
		}
		
		return null;
	}
	
	/**
	 * @return array <Mad_Script_Generator_Field>
	 */
	public function collectForeignFields()
	{
		$foreignFields = array();
		foreach ($this->getFields() as $field) {
			/* @var $field Mad_Script_Generator_Field */
			if(strpos($field->fieldName, '_id')) {
				$foreignFields[] = $field;
			}
		}
		
		return $foreignFields;
	}
	
	/**
	 * @return string
	 */
    public function generateFieldsDocBlock()
    {       
        $output = '<br />array(<br />'; 
        foreach ($this->getFields() as $field) {
        	/* @var $field Mad_Script_Generator_Field */
            $output .= "'{$field->fieldName}' => '', <br />";
        }
        $output .= ")<br />";
         
        return $output;
    }
}