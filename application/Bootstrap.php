<?php
/**
 * 
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
	public function getConfig()
	{
		return Zend_Registry::get('app')->getOptions();	
	}
	
	protected function _initTranslation()
	{
		$conf = $this->getConfig();
		$locale = $this->getPluginResource('locale')->getLocale();
		/* @var $locale Zend_Locale */
		$locale->setLocale($conf['resources']['locale']['default']);
		$tp = $this->getPluginResource('translate');
		/* @var $tp Zend_Application_Resource_Translate */
	
	
		$lang = null;
		if (isset($_REQUEST['lang'])) {
			$lang = $_REQUEST['lang'];
		} elseif (isset($_COOKIE['lang'])) {
			$lang = $_COOKIE['lang'];
		}
	
		if ($lang && $lang !== (string) $locale && Zend_Locale::isLocale($lang)) {
			$locale->setLocale($lang);
		}
	
		$translator = $tp->init();
		/* @var $translator Zend_Translate */
	
		if (!$translator->isAvailable((string) $locale) && !$translator->isAvailable(current(explode('_', (string) $locale, 2)))) {
			/// if there is no translation available for this specific locale, this means that we do not support it so use default
			$conf = $this->getConfig();
			$locale->setLocale($conf['resources']['locale']['default']);
				
			$translator->setLocale($locale);
		}
	
	
		// explicit locales mapping
		switch (current(explode('_', (string) $locale, 2))) {
			case 'en':
				$locale->setLocale('en_GB');
				break;
			case 'bg':
				$locale->setLocale('bg_BG');
				break;
	
			case 'el':
				$locale->setLocale('el_GR');
				break;
	
	
		}
	
		$locale->setDefault($locale);

	
		Zend_Validate::setDefaultTranslator($translator);
	
		return $translator;
	
	}
}

