<?php

namespace Catalyzer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Http\Models\User;
use Catalyzer\UrlParser;
use Catalyzer\Beautyfier\Beautyfier;
use Carbon\Carbon;

trait SuperModel 
{
    use SoftDeletes;

    private $foreignData;
    private $domesticDatas;

   
    public function setForeignData($foreignData)
    {
        $this->foreignData = $foreignData;
    }
    public function addForeignData($data)
    {   
        $key = array_keys($data)[0];
        $this->foreignData[$key] = $data[$key];
    }
    public function getForeignData()
    {
        return $this->foreignData;
    }

    public function getItemWithDomestics($id){
        $item = $this;
        $domestics = $this->getDomestics();
        if(isset($domestics))
        {
            foreach ($domestics as $key => $domestic) 
            {
                if(method_exists($this,$domestic->functionName))
                    $item = $item->with($domestic->functionName);
                else{

                    var_dump("ERROR: From SuperModel.php ".$domestic->functionName. ' does not exists in '.get_class($this));
                }
            }     
        } 
        $item = $item->find($id);
        return $item;
    }

    public function getItems(UrlParser $urlParser)
    {
        $items = $this;
        $parameters = $urlParser->getParameters();
        $withStatement = "";
        if(isset($urlParser->first()->id))
            $items = $items->whereId($urlParser->first()->id);

        if (sizeof($parameters) != 1)
        {
            $keys = array_keys($parameters);
            $parameters = array_reverse($parameters);
            $array = [];
            $anotherFunction;
            foreach ($parameters as $key => $parameter)
            {
                if($key != sizeof($parameters)-1)
                {
                    if(isset($anotherFunction))
                    {
                        $anotherFunction = array($parameter->functionName => function($query) use ($parameter,$anotherFunction) {$query->whereId($parameter->id)->with($anotherFunction);});
                    }
                    else
                    {
                         $anotherFunction = array($parameter->functionName => function($query) use ($parameter){
                                if(isset($parameter->id))
                                    $query->whereId($parameter->id)->whereNull('deleted_at');
                                else{
                                    $query->whereNull('deleted_at');
                                }

                            }
                        );
                    }
                }   
            }
        $items = $items->with($anotherFunction);

        }

        $items = $items->whereNull('deleted_at')->get();

        return $items;
    }


    public function setForeignsData(UrlParser $urlParser){

        $foreigns = $urlParser->last()->dataModel->getForeigns();
        $beautyfier = Beautyfier::detectBeautyfier();

        if(isset($foreigns))
        {
            foreach ($foreigns as $key=>$foreign) 
            {
                $array= [];
                $foreignModelName = $foreign->foreignModelName;
                $modelPath = $beautyfier->modelsFolder();
                $modelPath = $modelPath.$foreignModelName;

                $foreignDatas = (new $modelPath)->orderBy('id')->get();
                $array[$foreign->columnName] = $foreignDatas;
                $this->addForeignData($array);  
            }     
        } 
    }

    public function getDeletedByAttribute($value)
    {
        return (isset($value) && $value != -1)? User::find($value)->name:'-';

    }
    public function getRememberTokenAttribute($value)
    {
        return (isset($value) && $value != -1) ? $value: '-';
    }
    public function getCreatedByAttribute($value)
    {
        //return $value;
        return (isset($value) && $value != -1) ? User::find($value)->name: '-';
    }
    public function getCreatedAtAttribute($value)
    {
        //return date("d-m-Y H:i:s", strtotime($value ));
        return (isset($value) && $value != -1) ? date("d-m-Y H:i:s", strtotime($value )) :'-';
    }
    public function getDeletedAtAttribute($value)
    {
        //return date("d-m-Y H:i:s", strtotime($value ));
        return (isset($value) && $value != -1) ? date("d-m-Y H:i:s", strtotime($value )) :'-';
    }
    public function getUpdatedAtAttribute($value)
    {
        //return date("d-m-Y H:i:s", strtotime($value ));
        return (isset($value) && $value != -1) ? date("d-m-Y H:i:s", strtotime($value )) :'-';
    }
}
