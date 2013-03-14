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
class Mad_Script_Generator_Model_Writer
{
	/**
	 * @var string
	 */
	const PROPS_START = " * ----- Properties Start -------------- ";
	
	/**
	 * @var string
	 */
	const PROPS_END = " * ------ Properties End -------------- ";
	
	/**
	 * @var string
	 */
	const ASSOCS_COMMENTS_START = " * --- START ASSOC COMMENTS --- (Association COMMENTS (Don\'t modify lines between Start and End) ";
	
	/**
	 * @var string
	 */
	const ASSOCS_COMMENTS_END = " * --- END ASSOC COMMENTS --- (Association COMMENTS (Don\'t modify lines between Start and End) ";
	
	/**
	 * @var string
	 */
	const ASSOCS_DEF_START = "/* --- START ASSOC DEFINITIONS --- (Association Definitions (Don\'t modify lines between Start and End) */";
	
	/**
	 * @var string
	 */
	const ASSOCS_DEF_END = "/* --- END ASSOC DEFINITIONS --- (Association Definitions (Don\'t modify lines between Start and End) */";
			
	
	/**
	 * Absolute path to models folder
	 * @var string
	 */
	public  $modelsFolderPath;
	
	public function __construct($modelsFolderPath)
	{
		$this->modelsFolderPath = $modelsFolderPath;	
	}	
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return string model file path
	 */
	public function writeModel(Mad_Script_Generator_Model $model)
	{
		$filePath = $this->_getModelFilePath($model);
		
		$this->_writeFile(
			$this->_generateModelContent($model, $this->_getFileContent($filePath)),
			$filePath
		);
		
		return $filePath;
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $oldModel
	 * @param Mad_Script_Generator_Model $newModel
	 * @return boolean
	 */
	public function updateModelName(Mad_Script_Generator_Model $oldModel, Mad_Script_Generator_Model $newModel)
	{
		$filePath = $this->_getModelFilePath($oldModel);
		
		$this->_writeFile(
			str_replace("class {$oldModel->modelName}", "class {$newModel->modelName}", $this->_getFileContent($filePath)),
			$filePath
		);
		
		if(rename($filePath, $this->_getModelFilePath($newModel))) {
			return true;	
		}
		
		return false;
	}
	
	/**
	 * 
	 * @param array $models <Mad_Script_Generator_Model>
	 */
	public function writeModels(array $models)
	{
		foreach ($models as $model) {
			$this->writeModel($model);
		}			
	}
	
	/**
	 * 
	 * @param string $content
	 * @param string $filePath
	 * @return bool
	 */
	protected function _writeFile($content, $filePath) 
	{
		if(file_exists($filePath)) {
			if(file_get_contents($filePath) !== $content) {
				file_put_contents($filePath, $content);	
			}
			
			return true;
		}	
		
		if(file_put_contents($filePath, $content) === false) {
			throw new Exception('fail.');
		}
	}
	
	/**
	 * @param string $filePath
	 * @return string
	 */
	protected function _getFileContent($filePath)
	{
		if(file_exists($filePath)) {
			return file_get_contents($filePath);
		}
		
		return '';
	}
	
	/**
	 * @param Mad_Script_Generator_Model $model
	 * @return string
	 */
	public function getModelContent(Mad_Script_Generator_Model $model)
	{
		return $this->_getFileContent($this->_getModelFilePath($model));
	}
	
	
	/**
	 * @param string $from
	 * @param string $to
	 * @param string $subject
	 * @param string $newContent
	 * 
	 * @return string
	 */
	protected function _replacePartOfContent($from, $to, $subject, $newContent)
	{
		$fromPos	= mb_strpos($subject, $from); 
		$toPos		= mb_strpos($subject, $to);
		
		if($fromPos === false || $toPos === false) {
			return $subject;
		}
		
		return str_replace(substr($subject, $fromPos, $toPos - $fromPos + strlen($to)), $newContent, $subject);
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return string
	 */
	protected function _getModelFilePath(Mad_Script_Generator_Model $model)
	{
		return ($this->modelsFolderPath . DIRECTORY_SEPARATOR . $model->modelName . '.php');
	}
	
	/**
	 * @param Mad_Script_Generator_Model $model
	 * @param string $currentContent
	 */
	protected function _generateModelContent(Mad_Script_Generator_Model $model, $currentContent)
	{
		if(empty($currentContent)) {
			return $this->_generateNewClass($model);
		}
		
		$currentContent = $this->_replacePartOfContent(
				self::PROPS_START,
				self::PROPS_END,
				$currentContent,
				$this->_generateProps($model)
		);
		
		$currentContent = $this->_replacePartOfContent(
				self::ASSOCS_COMMENTS_START,
				self::ASSOCS_COMMENTS_END,
				$currentContent,
				$this->_generateAssocComments($model)
		);
		
		
		$currentContent = $this->_replacePartOfContent(
			self::ASSOCS_DEF_START,
			self::ASSOCS_DEF_END,
			$currentContent,
			$this->_generateAssocsDef($model)		
		);
		
		
		return $currentContent;
	}
	
	/**
	 * @param Mad_Script_Generator_Model $model
	 * @return string
	 */
	protected function _generateNewClass(Mad_Script_Generator_Model $model)
	{
		$output = '<?php' . PHP_EOL;
		$output .= '/**' . PHP_EOL . ' *' . PHP_EOL;
		
		$output .= $this->_generateProps($model) . PHP_EOL . PHP_EOL;
		
		$output .= $this->_generateAssocComments($model) . PHP_EOL;
		
		$output .= '*/' . PHP_EOL;
		
		$output .= "class {$model->modelName} extends Mad_Model_Base { " . PHP_EOL;
		$output .= PHP_EOL . "\tprotected \$_tableName = '{$model->tableName}';" . PHP_EOL;
		$output .= PHP_EOL . PHP_EOL . "\tpublic function _initialize() {" . PHP_EOL . "\t\t";
		
		$output .= $this->_generateAssocsDef($model) . PHP_EOL;
		
		$output .= "\t}" . PHP_EOL;
			
		
		$output .= "}";
		
		return $output;
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return string
	 */
	protected function _generateProps(Mad_Script_Generator_Model $model)
	{
		$output = self::PROPS_START . PHP_EOL;
		foreach ($model->getFields() as $field) {
			/* @var $field Mad_Script_Generator_Field */
				
			$output .= " * @property " . str_replace(' ', '-', $field->fieldType) . " \${$field->fieldName}" . PHP_EOL;
		}
		$output .= self::PROPS_END;
		
		return $output;
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return string
	 */
	protected function _generateAssocsDef(Mad_Script_Generator_Model $model)
	{
		$output =  self::ASSOCS_DEF_START . PHP_EOL;
		foreach ($model->getAssocs() as $assoc) {
			/* @var $assoc Mad_Script_Generator_Association_Abstract */
			$output .= $assoc->generateDefinition();
		}
		$output .= "\t\t" . self::ASSOCS_DEF_END ;
		
		return $output;
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return string
	 */
	protected function _generateAssocComments(Mad_Script_Generator_Model $model)
	{
		$output = self::ASSOCS_COMMENTS_START . PHP_EOL;
		foreach ($model->getAssocs() as $assoc) {
			/* @var $assoc Mad_Script_Generator_Association_Abstract */
			$output .= "{$assoc->generateComments()}";
		}
		
		$output .= self::ASSOCS_COMMENTS_END;
		
		return $output;
	}
	
	/**
	 * 
	 * @param string 	$string
	 * @param int 		$additionalTabs
	 * @return string
	 */
    public static function computeTabs($string, $additionalTabs = 0)
    {
        $tabs = "";
        $length = strlen($string);
       
        for ($i = 0; $i < $additionalTabs; $i++) {
            $tabs .= "\t";
        }

        foreach (
            array(
                1    => array(20, 23),
                2    => array(16, 19),
                3    => array(12, 15),
                4    => array(8, 11),
                5    => array(4, 7),
                6    => array(0, 3),
            ) as $tabCount => $lengths
        ) {
            list($min, $max) = $lengths;
           
            if($length >= $min && $length <= $max) {
                for ($i = 0; $i < $tabCount; $i++) {
                    $tabs .= "\t";
                }
               
                return $tabs;
            }
        }
       
        return ((empty($tabs)) ? ("\t") : ($tabs));
    }
}