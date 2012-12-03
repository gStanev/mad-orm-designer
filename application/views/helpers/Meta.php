<?php
class Zend_View_Helper_Meta extends Zend_View_Helper_Abstract
{
	public function meta()
	{
		return $this;
	}
	
	public function keywords()
	{
		return $this->_meteTag(
			'keywords', 
			(empty($this->view->metaKeywords)) ? 
						($this->view->translate('content.meta.keywords')) : 
							(preg_replace('/(\s)/', ', ', $this->view->metaKeywords) . ', ' . $this->view->translate('content.meta.keywords'))
		) . PHP_EOL;
	}
	
	public function description()
	{			
		return $this->_meteTag(
			'description', 
			(empty($this->view->metaDesc)) ? 
				($this->view->translate('content.meta.description')) : 
					($this->view->truncate($this->view->metaDesc, 150, ',') . $this->view->translate('content.meta.shortDesc'))
		) . PHP_EOL;
	}
	
	public function title()
	{
		return $this->view->headTitle($this->view->translate('content.meta.titlePrefix') . $this->view->metaTitle) . PHP_EOL;
	}
	
	protected  function _meteTag($type, $content)
	{
		return "<meta name=\"{$this->view->escape($type)}\" content=\"{$this->view->escape($content)}\" />";
	}
}