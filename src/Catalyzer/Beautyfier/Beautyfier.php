<?php
namespace Catalyzer\Beautyfier;
use Catalyzer\Contracts\BeautyfierContract;


abstract class Beautyfier implements BeautyfierContract {

	protected $RESERVED_ACTION_NAMES;
    protected $MODELS_FOLDER;
    protected $DATA_MODELS_FOLDER;
    protected $SPECIAL_PLURALS;

    public static function detectBeautyfier()
    {

        switch(config('app.language'))
        { 
            case 'en':
                $strBeautyfier = new EnglishBeautyfier();
                break;
            case 'tr':
                $strBeautyfier = new TurkishBeautyfier();
                break;
            default:
                $strBeautyfier = new EnglishBeautyfier();
                break;
        }
        return $strBeautyfier;        
    }
    public function singularize( $plural, $shouldUc = false){}

    public function toDataModelPath($dataModelName)
    {        
        $dataModelPath = $this->DATA_MODELS_FOLDER.str_replace(' ','',ucwords(str_replace('_', ' ', $dataModelName)));
        return $dataModelPath;

    }
    public function toModelName($dataModelName)
    {
        return $this->singularize($dataModelName,true);
    }
    public function toForeignModelName($dataModelName)
    {
        return str_replace(' ','',$this->beautify($dataModelName));
    }
    public function toDataModelName($modelName)
    {
        return str_replace(' ','',str_replace('Ref ','',$this->beautify($modelName)));
    }
    public function toTableName($dataModelName)
    {
        $pieces = preg_split('/(?=[A-Z])/',$dataModelName);
        foreach ($pieces as  $piece) {
            if($piece != '' && isset($piece))
                $array[] = lcfirst($piece);

        }
        $tableName = implode("_",$array);
        return $tableName;
    }
    public function toFunctionName( $moduleName)
    {
        return lcfirst($moduleName);
    }
    public function beautify($bad)
    {
        return ucwords(str_replace('_',' ',$bad));
    }
    public function reservedActionNames()
    {
        return $this->RESERVED_ACTION_NAMES;
    }
    public function modelsFolder()
    {
        return $this->MODELS_FOLDER;
      
    }
    public function dataModelsFolder()
    {
         return $this->DATA_MODELS_FOLDER;
       
    }
}
?>