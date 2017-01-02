<?php 
namespace Catalyzer;
use Route;
use Illuminate\Support\Str;

use Catalyzer\Beautyfier\Beautyfier;

class UrlParser
{
    private $url;
    private $path;
    private $action;
    private $parameters = [];

	public function __construct()
	{   
        $this->beautyfier = Beautyfier::detectBeautyfier();
        $this->setUrl();
        $this->parseParameters($this->beautyfier);
        $this->setAction();
	}

    private function setUrl()
    {
        $this->path = \Request::path();
        $this->url = \Request::url();
    }

    private function parseParameters(Beautyfier $beautyfier)
    {
        $parameters = Route::current()->parameters();

        $reservedActionNames = $beautyfier->reservedActionNames();
        foreach ($parameters as $key => $value) 
        {
            if( !in_array(Str::lower($value),$reservedActionNames) )
            {   
                if( Str::contains($key,'module') )
                    $moduleNames[] =  $value;
                else if( Str::contains($key,'id') )
                    $ids[] = $value;
            }
        }   
        foreach ($moduleNames as $key => $value) 
        {
            $modelClass = new \stdClass();
            $dataModelName = $beautyfier->toDataModelPath($value);
            if(class_exists($dataModelName))
                $dataModel = (new $dataModelName); 
            else
            {
                throw new \Exception("There is no Data Model named: ".$value,1);
            }
            $modelClass->dataModel = $dataModel;
            $modelClass->modelName = $dataModel->getModelName();
            $modelClass->id = (isset($ids[$key]))? $ids[$key] : null;
            $modelClass->modelNameWithPath = $beautyfier->modelsFolder().$modelClass->modelName;
            $modelClass->functionName = $beautyfier->toFunctionName($modelClass->modelName);
            $this->parameters[$key] = $modelClass;

        }
    }

    public function getParameters()
    {
        return $this->parameters;
    }

	private function setAction()
    {
        $this->action = ucfirst(explode('@',Route::getCurrentRoute()->getActionName())[1]);
    }

    public function getAction()
    {
        return $this->action;
    }

    public function first()
    {
        return array_values($this->parameters)[0];
    }
    
    public function last()
    {
        return array_values($this->parameters)[sizeof($this->parameters)-1];

    }
}

?>