<?php
class Zend_View_Helper_Model extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @var Mad_Model_Base
	 */
	protected $_model;
	
	/**
	 * 
	 * @param Mad_Model_Base $model
	 * @return Zend_View_Helper_Model
	 */
	public function model(Mad_Model_Base $model)
	{
		$this->_model = $model;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getLabel()
	{
		
		foreach (array('name', 'label', 'title', 'first_name', 'last_name', 'email', 'username', 'description') as $field) {
			if(in_array($field, $this->_model->attributeNames()) && !empty($this->_model->{$field}))
				return ('<strong>' . ucfirst($field) . '</strong>' . ':' . $this->_model->{$field});
		}
		 
		return ("<strong>Id:</strong>{$this->_model->id}");
	}
}