<?php
namespace App\Catalyzer\Contracts;

interface ViewProviderContract{
	public static function provideIndexView( $urlParser, $dataModel, $items);
    public static function provideCreateView($urlParser, $dataModel, $model);
    public static function provideShowView($urlParser,$dataModel, $item);
    public static function provideEditView($urlParser,$dataModel, $item);
}
?>