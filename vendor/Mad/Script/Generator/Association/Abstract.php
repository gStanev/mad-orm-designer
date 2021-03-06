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
abstract class Mad_Script_Generator_Association_Abstract
{
	/**
	 *
	 * @var Mad_Script_Generator_Model
	 */
	public $masterModel;

	/**
	 *
	 * @var Mad_Script_Generator_Model
	 */
	public $assocModel;

	/**
	 *
	 * @var array
	 */
	protected $_options = array();

	/**
	 *
	 * @var string
	 */
	protected  $_name;

	/**
	 *
	 * @param Mad_Script_Generator_Model $masterModel
	 * @param Mad_Script_Generator_Model $assocModel
	 */
	public function __construct(Mad_Script_Generator_Model $masterModel, Mad_Script_Generator_Model $assocModel)
	{
		$this->masterModel	= $masterModel;
		$this->assocModel 	= $assocModel;
	}

	public function getName()
	{
		if($this->_name === null) {
			$this->_setDefaultName();
		}

		return $this->_name;
	}

	/**
	 *
	 * @param string $name
	 * @return Mad_Script_Generator_Association_Abstract
	 */
	public function setName($name)
	{
		$this->_name = $name;

		return $this;
	}

	/**
	 * @return Mad_Script_Generator_Association_Abstract
	 */
	abstract protected function _setDefaultName();

	/**
	 * @return string
	 */
	abstract public function generateComments();

	/**
	 * @return array
	 */
	abstract public function getAllowedOptionKeys();

	/**
	 * @return string
	 */
	public function getTabsNull()
	{
		return Mad_Script_Generator_Model_Writer::computeTabs('NULL');
	}

	/**
	 * @return string
	 */
	public function getTabsCollection()
	{
		return Mad_Script_Generator_Model_Writer::computeTabs('Mad_Model_Collection');
	}

	/**
	 * @param int $additionalTabs
	 * @return string
	 */
	public function getTabsMethodName($additionalTabs = 0)
	{
		return Mad_Script_Generator_Model_Writer::computeTabs(
			$this->getMethodName(),
			$additionalTabs
		);
	}

	/**
	 * @param string $firstLetter 'lower', 'upper'
	 * @return string
	 */
	public function getMethodName($firstLetter = 'lower')
	{
		return Mad_Support_Inflector::camelize($this->getName(), $firstLetter);
	}

	/**
	 *
	 * @return string
	 */
	public function generateDefinition()
	{
        $output = "\t\t" . '$this->' . $this->getType() . '("' . $this->getName() . '"';
		$output .= $this->_generateDefinitionOpts();
        $output .= ');' . PHP_EOL;

        return $output;
	}

	/**
	 *
	 * @return string
	 */
	protected function _generateDefinitionOpts()
	{
		$output = '';
		if(count($this->getOptions())) {
            $dump = var_export($this->getOptions(), true);
            $dump = str_replace("\n", "\n\t\t\t", $dump);
            $output .= ',' . PHP_EOL . "\t\t\t";
            $output .= str_replace('0 => ', '', $dump);
            $output .= PHP_EOL . "\t\t";
        }

        return $output;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		$classPieces = explode('_', get_class($this));
		return lcfirst(array_pop($classPieces));
	}

	/**
	 *
	 * @return string
	 */
	public function getNodeName()
	{
		return $this->getType() . $this->getName();
	}


	/**
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'name'		=> $this->getName(),
			'type' 		=> $this->getType(),
			'options'	=> $this->getOptions(),
			'masterModel'	=> array(
				'modelName'	=> $this->masterModel->modelName,
				'tableName'	=> $this->masterModel->tableName,
			),
			'assocModel'	=> array(
				'modelName'	=> $this->assocModel->modelName,
				'tableName'	=> $this->assocModel->tableName
			)
		);
	}

	/**
	 *
	 * @param array $data
	 * @return Mad_Script_Generator_Association_Abstract
	 */
	public static function fromArray(array $data)
	{
		$middleModel =
			(isset($data['middleModel'])) ?
				(new Mad_Script_Generator_Model($data['middleModel']['tableName'], $data['middleModel']['modelName'])) :
					(null);

		$options = (isset($data['options']) && is_array($data['options'])) ? ($data['options']) : (array());

		$assoc = self::factory(
			$data['type'],
			new Mad_Script_Generator_Model($data['masterModel']['tableName'], $data['masterModel']['modelName']),
			new Mad_Script_Generator_Model($data['assocModel']['tableName'], $data['assocModel']['modelName']),
			$middleModel,
			$options
		);

		if(isset($data['name'])) {
			$assoc->_name = $data['name'];
		}

		return $assoc;
	}

	/**
	 *
	 * @param string $type
	 * @param Mad_Script_Generator_Model $masterModel
	 * @param Mad_Script_Generator_Model $assocModel
	 * @param Mad_Script_Generator_Model $assocModel
	 * @param array $options
	 * @return Mad_Script_Generator_Association_Abstract
	 */
	public static function factory(
		$type,
		Mad_Script_Generator_Model $masterModel,
		Mad_Script_Generator_Model $assocModel,
		Mad_Script_Generator_Model $middleModel = null,
		array $options = array()
	) {
		if($type == Mad_Model_Association_Base::TYPE_BELONGS_TO) {
			$assoc = new Mad_Script_Generator_Association_BelongsTo($masterModel, $assocModel);
		}

		if($type == Mad_Model_Association_Base::TYPE_HAS_MANY) {
			$assoc = new Mad_Script_Generator_Association_HasMany($masterModel, $assocModel);

		}

		if($type == Mad_Model_Association_Base::TYPE_HAS_MANY_THROUGH) {
			$assoc = new Mad_Script_Generator_Association_HasManyThrough($masterModel, $assocModel, $middleModel);
		}

		if($type == Mad_Model_Association_Base::TYPE_HAS_ONE) {
			$assoc = new Mad_Script_Generator_Association_HasOne($masterModel, $assocModel);
		}

		if($type == Mad_Model_Association_Base::TYPE_HAS_AND_BELONGS_TO_MANY) {
			$assoc = new Mad_Script_Generator_Association_HasAndBelongsToMany($masterModel, $assocModel);
		}

		$assoc->addOptions($options);

		return $assoc;
	}


	/**
	 *
	 * @param string $code
	 * @return string
	 */
	protected function _codeFormatter($code)
	{
		return "<br /><br />Example:<br /> <code>" . $code . "</code>";
	}

	/**
	 *
	 * @param array $methodNames of current class which should generate comments
	 * @param string $header
	 * @return string
	 */
	public function _generateComments($methodNames, $header)
	{
		$output = PHP_EOL . PHP_EOL . ' * ' . $header . PHP_EOL;

		foreach ($methodNames as $commentsBuilder) {
			$output .= $this->{$commentsBuilder}();
		}

		$output .= PHP_EOL;

		return $output;
	}

	/**
	 *
	 * @param string $optionKey
	 * @param string $optionValue
	 * @throws InvalidArgumentException
	 * @return Mad_Script_Generator_Association_Abstract
	 */
	public function addOption($optionKey, $optionValue)
	{
		$this->_options[$optionKey] = $optionValue;

		//skip validation for special word for hasManyThrough assoc
		if($optionKey !== 'through'){
			Mad_Support_Base::assertValidKeys($this->_options, $this->getAllowedOptionKeys());
		}

		return $this;
	}

	/**
	 *
	 * @param array $options array($optionKey => $optionValue)
	 *
	 * @return Mad_Script_Generator_Association_Abstract
	 */
	public function addOptions(array $options)
	{
		foreach ($options as $optionKey => $optionValue) {
			$this->addOption($optionKey, $optionValue);
		}

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 *
	 * @param string $optionKey
	 * @return bool
	 */
	public function issetOption($optionKey)
	{
		return isset($this->_options[$optionKey]);
	}
}