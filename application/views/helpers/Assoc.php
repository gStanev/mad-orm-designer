<?php
class Zend_View_Helper_Assoc extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @var Mad_Script_Generator_Association_Abstract
	 */
	protected $_assoc;
	
	public function assoc(Mad_Script_Generator_Association_Abstract $assoc)
	{
		$this->_assoc = $assoc;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function toInputs($exclude = array())
	{
		$output = '';
		$nodeLabel = $this->_assoc->getType() . $this->_assoc->getName();
		$output .= "<input type=\"hidden\" name=\"assoc[label]\" value=\"{$nodeLabel}\">";
		
		foreach ($this->_assoc->toArray() as $key => $val) {
			if(in_array($key, $exclude)) continue;
			
			if(is_string($val)) {
				$output .= "<input type=\"hidden\" name=\"assoc[{$key}]\" value=\"{$val}\">";
			} else if(is_array($val)) {
				foreach ($val as $subKey => $subVal) {
					if(in_array($subKey, $exclude)) continue;
					
					$output.= "<input type=\"hidden\" name=\"assoc[{$key}][{$subKey}]\" value=\"{$subVal}\">";
				}
			}
		}
		
		return $output;
	}
}