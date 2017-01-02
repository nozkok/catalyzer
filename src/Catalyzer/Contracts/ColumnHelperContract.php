<?php 
namespace Catalyzer\Contracts;

interface ColumnHelperContract{
	public static function detectColumnHelper();
	public static function setupColumns($model);
	public static function setupForeignsAndDomestics($dataModel,$fks);
}
?>