<?php
namespace Catalyzer\ViewProvider;
use Catalyzer\Contracts\ViewProviderContract;

class WebViewProvider implements ViewProviderContract {

	public static function provideIndexView($urlParser, $dataModel, $items)
    {   
        return View('catalyzer.components.table')->with([
            'moduleName' => $dataModel->getName(),
            'action' => $urlParser->getAction(),
            'tableFields' => $dataModel->getTableFields(),
            'columns' => $dataModel->getColumns(),
            'items' => $items,
            'dataModel' =>$dataModel
            ])->render();  
    }
    public static function provideCreateView($urlParser, $dataModel, $model)
    {

        return View('catalyzer.components.form')->with([
            'moduleName' => $dataModel->getName(),
            'action' => $urlParser->getAction(),
            'formFields' => $dataModel->getFormFields() ,
            'columns'   => $dataModel->getColumns(),
            'foreigns'  => $dataModel->getForeigns(),
            'foreignDatas' => $model->getForeignData(),
            ])->render();
    }
    public static function provideShowView($urlParser, $dataModel, $item)
    {
        return View('catalyzer.components.show')->with([
            'moduleName' =>$dataModel->getName(),
            'action' => 'Show',
            'columns' => $dataModel->getColumns(),
            'item'=> $item,
            'hidden' => $dataModel->getHiddenFields(),
            'dataModel' =>$dataModel,
            ])->render();
    }
    public static function provideEditView($urlParser,$dataModel, $item)
    {

        return View('catalyzer.components.form')->with([
            'moduleName' =>$dataModel->getName(),
            'action' => $urlParser->getAction(),
            'formFields' => $dataModel->getFormFields(),
            'columns' => $dataModel->getColumns(),
            'foreigns'  => $dataModel->getForeigns(),
            'foreignDatas' => $item->getForeignData(),
            'item'=> $item
            ])->render();
    }

}
?>