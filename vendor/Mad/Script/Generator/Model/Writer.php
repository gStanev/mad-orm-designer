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
	 * 
	 * @var string
	 */
	const PROPERTIES_START = " * ----- Properties Start -------------- \n";
	
	/**
	 * 
	 * @var string
	 */
	const PROPERTIES_END = " * ------ Properties End -------------- \n";
	
	
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
			$this->_generateModelContent($model),
			$filePath
		);
		
		return $filePath;
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
	 * 
	 * @param Mad_Script_Generator_Model $model
	 * @return string
	 */
	protected function _getModelFilePath(Mad_Script_Generator_Model $model)
	{
		return ($this->modelsFolderPath . DIRECTORY_SEPARATOR . $model->modelName . '.php');
	}
	
	/**
	 * 
	 * @param Mad_Script_Generator_Model $model
	 */
	protected function _generateModelContent(Mad_Script_Generator_Model $model)
	{
		$output = '<?php' . PHP_EOL;
		$output .= '/**
					*' . PHP_EOL;
		
		$output .= self::PROPERTIES_START;
		foreach ($model->getFields() as $field) {
			/* @var $field Mad_Script_Generator_Field */
			
			$output .= " * @property {$field->fieldType} \${$field->fieldName} \n";
		}
		$output .= self::PROPERTIES_END;
		
		foreach ($model->getAssocs() as $assoc) {
			/* @var $assoc Mad_Script_Generator_Association_Abstract */
			$output .= "{$assoc->generateComments()} \n";
		}
		
		$output .= '*/' . PHP_EOL;
		
		$output .= "class {$model->modelName} extends Mad_Model_Base { \n";
		$output .= "\n\n\tpublic function _initialize() {" . PHP_EOL;
		
		foreach ($model->getAssocs() as $assoc) {
			/* @var $assoc Mad_Script_Generator_Association_Abstract */
			$output .= $assoc->generateDefinition();
		}
		
		$output .= "\n\n\t}" . PHP_EOL;
			
		
		$output .= "}";
		
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