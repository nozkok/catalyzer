<?php 
namespace App\Catalyzer\Contracts;

interface BeautyfierContract{
	public static function detectBeautyfier();
	public function singularize ($plural, $shouldUc);
	public function toDataModelPath($dataModelName);
   public function toModelName($dataModelName);
   	public function toTableName($dataModelName);
    public function toFunctionName ($moduleName);
	public function beautify ($bad);
  public function toForeignModelName($dataModelName);
    public function reservedActionNames();
   	public function modelsFolder();
   	public function dataModelsFolder();
    public function toDataModelName($modelName);
}
?> 
