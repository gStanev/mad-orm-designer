<?php
/**
 * Mad Model Generator
 *
 * @author g.d.stanev@gmail.com <Georgi Stanev>
 */
class IndexController extends Zend_Controller_Action
{
	/**
	 * 
	 * @var int
	 */
	protected static $_assocsCount = 0;
    
    public function missingModelsAction()
    {
    	$parser = new Mad_Script_Generator_Parser_Db(
    			new Horde_Db_Adapter_Mysqli(array(
    					'host' => 'localhost',
    					'username' => 'root',
    					'password' => '',
    					'dbname' => ''
    			))
    	);
    
    	$this->view->modelBuilder = $modelBuilder = new Mad_Script_Generator_Model_Builder($parser);
    	$this->view->models = $modelBuilder->factoryModels();
    
    	if($tableName = $this->_getParam('tableName')) {
    		$this->view->model = $modelBuilder->factoryModel($tableName);
    	}
    }
    
    public function indexAction()
    {
    	$parser = new Mad_Script_Generator_Parser_Db(
    			new Horde_Db_Adapter_Mysqli(array(
    					'host' => 'localhost',
    					'username' => 'root',
    					'password' => '',
    					'dbname' => ''
    			))
    	);
    
    	$modelBuilder = new Mad_Script_Generator_Model_Builder($parser);
    	$writer = new Mad_Script_Generator_Model_Writer('/var/www/mad-model-generator/application/models');
    
    	foreach ($modelBuilder->factoryModels() as $model) {
    		/* @var $model Mad_Script_Generator_Model */
    		foreach ($modelBuilder->suggestionsBelongsTo($model) as $assoc) {
    			$model->addAssoc($assoc);
    		}
    
    		foreach ($modelBuilder->suggestionsHasMany($model) as $assoc) {
    			$model->addAssoc($assoc);
    		}
    			
    		foreach ($modelBuilder->suggestionsHasManyThrough($model) as $assoc) {
    			$model->addAssoc($assoc);
    		}
    			
    		foreach ($modelBuilder->suggestionsHasOne($model) as $assoc) {
    			$model->addAssoc($assoc);
    		}
    			
    		$writer->writeModel($model);
    	}
    
    	return ;
    	foreach ($modelBuilder->factoryModels() as $model) {
    		/* @var $model Mad_Script_Generator_Model */
    		echo "<br /><br /><br />------------------";
    			
    		echo "ModelName: {$model->modelName}";
    			
    		echo $this->_renderAssocs($modelBuilder->suggestionsHasOne($model), 'Has One assocs:');
    		echo $this->_renderAssocs($modelBuilder->suggestionsHasMany($model), 'Has Many assocs:');
    		echo $this->_renderAssocs($modelBuilder->suggestionsHasManyThrough($model), 'Has Many Through assocs:');
    				echo $this->_renderAssocs($modelBuilder->suggestionsBelongsTo($model), 'Belongs to:');
    							
    				echo "<br /><br /><br />";
    	}
    
    
    	echo 'Ascocs count' . self::$_assocsCount;
    	echo '<br />';
		echo "Models count" . count($modelBuilder->factoryModels());
    		die;
    }
    
    /**
    *
    * @param array $assoc
    */
    protected function _renderAssocs(array $assocs, $label)
    {
    if(!count($assocs)) return '';
    echo "<br /> ---------------------------------------------- {$label}<br />";
    foreach ($assocs as $assoc) {
    /* @var $assoc Mad_Script_Generator_Association_Abstract */
    echo "{$assoc->getName()} <br />";
     if($assoc instanceof Mad_Script_Generator_Association_HasManyThrough) {
     echo "Middle Table: {$assoc->middleModel->tableName}<br /><br />";
    }
    		
    	self::$_assocsCount++;
    	}
    	 
    	echo '<br /><br />';
    }
}

