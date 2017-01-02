<?php 

namespace Catalyzer\DataModel;

use Catalyzer\Contracts\DataModelContract;
use Catalyzer\SuperCacheHelper;
use Catalyzer\Beautyfier\Beautyfier;

abstract class DataModel implements DataModelContract
{
	protected $name;
	protected $model;
	protected $table;

	protected $tableFields = ['*'];
	protected $formFields = ['*'];
	protected $hiddenFields;

    protected $columns;
    protected $foreigns;
    protected $domestics;
    protected $rules;

    protected $pivot = false;


	public function __construct()
	{ 
		$this->setName();
		SuperCacheHelper::rememberModel($this);

	}
	public function getName()
	{
		return $this->name;
	}
	public function setName()
	{
		$name = get_class($this);
		$beautyfier = Beautyfier::detectBeautyfier();

		$this->name = substr($name, strrpos($name, '\\') + 1);
		if(!isset($this->model))
		{
			if(!$this->pivot)
				$this->model = $beautyfier->toModelName($this->name);        
			else 
				$this->model = 'Ref'.$beautyfier->toModelName($this->name);        
 
		}
		if(!isset($this->table))
		{
			if(!$this->pivot)
				$this->table = $beautyfier->toTableName($this->name);     
			else
				$this->table = 'ref_'.$beautyfier->toTableName($this->name);     
	        
		}
		if(!isset($this->hiddenFields))
		{	
			$modelPath = $beautyfier->modelsFolder().$this->model;
			$hiddenFields = (new $modelPath)->getHidden();
			$this->hiddenFields = $hiddenFields;
		}
	}
	public function getModelName()
	{
		return $this->model;
	}
	public function getTable()
	{
		return $this->table;
	}
	public function getColumns()
	{
		return $this->columns;
	}
	public function getRules()
	{
		return $this->rules;
	}
	public function getFormFields()
	{
        return $this->formFields;   
	}
	public function getTableFields()
	{
        return $this->tableFields;
	}
	public function getHiddenFields()
	{
		return $this->hiddenFields;
	}
	public function setRules($rules){
		$this->rules = $rules;
	}
	public function setColumns($columns){
		$this->columns = $columns;
	}

    public function setForeigns($foreigns)
    {
        $this->foreigns = $foreigns;
    }
    public function setFormFields(array $formFields)
    {
       
        $this->formFields = $formFields;   

    }
    public function setTableFields(array $tableFields)
    {
       
        $this->tableFields = $tableFields;   

    }
    public function getForeigns()
    {                   

        return $this->foreigns;
    }

    public function setDomestics($domestics)
    {
        $this->domestics = $domestics;
    }

    public function getDomestics()
    {

        return $this->domestics;
    }


}