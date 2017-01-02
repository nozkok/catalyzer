<?php
namespace App\Catalyzer\ControllerHelper;

use App\Catalyzer\UrlParser;
use App\Catalyzer\Contracts\ControllerHelperContract;
use Validator;
use Redirect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class ControllerHelper implements ControllerHelperContract{
	protected $result;
	protected $urlParser;

    public function getIndex()
    {
    	$urlParser = $this->urlParser;
        $models = $urlParser->getParameters();
        $firstModelPath = $urlParser->first()->modelNameWithPath;
        $lastModelPath = $urlParser->last()->modelNameWithPath;
        $firstModel =(new $firstModelPath);//for all items
        $items = $firstModel->getItems($urlParser);
        $item = null;
        foreach ($models as  $key => $model) 
        {    
            $dataModel = $model->dataModel;

            if(isset($model->id) )
            {
                if($key == 0){
                    $item = $items[0];
                }
                else
                {
                    $relation = $model->functionName;
                    $item = $item->$relation;
                    $item = $item[0];

                }
                $this->makeShow($dataModel, $item);
            }
            else
            {

                if(sizeof($models) != 1)
                {
                    $relation = $model->functionName;
                    $item = $item->$relation;
                    $items = $item; 
                }
                $this->makeIndex($dataModel, $items);
                      
            }
        }   
        return $this->result;
    }

    public function getShow()
    {
    	$urlParser = $this->urlParser;
        $models = $urlParser->getParameters();
        $firstModelPath = $urlParser->first()->modelNameWithPath;
        $lastModelPath = $urlParser->last()->modelNameWithPath;

        $firstModel =(new $firstModelPath);//for all items
        $items = $firstModel->getItems($urlParser);
        $item = null;

        foreach ($models as  $key => $model) 
        {    
            $dataModel = $model->dataModel;

            if(isset($model->id) )
            {
                if($key == 0){
                    $item = $items[0];
                }
                else
                {
                    $relation = $model->functionName;
                    $item = $item->$relation;
                    $item = $item[0];

                }
                $this->makeShow($dataModel, $item);


            }
        }                    
        return $this->result;
    }
    public function getCreate()
    {
    	$urlParser = $this->urlParser;
        $lastModelPath = $urlParser->last()->modelNameWithPath;
        $model = (new $lastModelPath);
        $model->setForeignsData($urlParser);//sending last parameter with dataModel inside of it
        $dataModel = $urlParser->last()->dataModel;
        $this->makeCreate( $dataModel, $model);
        return $this->result;

    }
    public function getEdit()
    {
    	$urlParser = $this->urlParser;
        $models = $urlParser->getParameters();
        $firstModelPath = $urlParser->first()->modelNameWithPath;

        $firstModel =(new $firstModelPath);

        $items = $firstModel->getItems($urlParser);
        $item = null;
        foreach ($models as  $key => $model) 
        {   
            $dataModel = $model->dataModel;

            if(isset($model->id))
            {

                if($key == 0){
                    $item = $items[0];
                }
                else
                {
                    $relation = $model->functionName;
                    $item = $item->$relation;
                    $item = $item[0];
                }
            }
            else
            {
                $relation = $model->functionName;
                $item = $item->$relation;
            }
        }
        $item->setForeignsData($urlParser);
        $this->makeEdit( $dataModel, $item);
        return $this->result;

    }

    public function save($request, $item= null)
    {
    	$urlParser = $this->urlParser;

        $lastModelNameWithPath = $urlParser->last()->modelNameWithPath;
        $dataModel = $urlParser->last()->dataModel;
        if(!isset($item))
            $item = (new $lastModelNameWithPath);
        $columns = $dataModel->getColumns();
        $formFields = $dataModel->getFormFields();

        foreach ($formFields as $key => $value) {
            if($value != 'password')
                $item->$value = $request->input($columns[$value]->name);
            else
            {
                $originals = $item->getOriginal();

                if(empty($originals))//create
                {
                    $item->$value = bcrypt($request->input($columns[$value]->name)) ;

                }
                else if ($originals['password'] != $request->input($columns[$value]->name) )
                {
                    $item->$value = bcrypt($request->input($columns[$value]->name)) ;
                }
            }
        }
        $item->save();
    }
    public function storeAction($request)
    {
    	$urlParser = $this->urlParser;

        $dataModel = $urlParser->last()->dataModel;
        $lastModelNameWithPath = $urlParser->last()->modelNameWithPath;
        $model = (new $lastModelNameWithPath);
        $validator = Validator::make($request->all(), $dataModel->getRules());
        if ($validator->fails()) {
            return Redirect::to('/'.$dataModel->getName().'/create')
                ->withErrors($validator)->with('fail',$dataModel->getModelName().' could not created.');
        } else {
            $this->save($request);
            return Redirect::to($dataModel->getName())->with('success',$dataModel->getName().' has been created.');
        }
    }
    public function updateAction($request)
    {
    	$urlParser = $this->urlParser;

        $lastModel = $urlParser->last();
        $dataModel = $urlParser->last()->dataModel;

        $lastModelNameWithPath = $urlParser->last()->modelNameWithPath;
        $model = (new $lastModelNameWithPath);


        $validator = Validator::make($request->all(), $dataModel->getRules());
        if ($validator->fails()) {
            return Redirect::to('/'.$dataModel->getName().'/'.$id.'/edit')
                ->withErrors($validator)
                ->with('fail',$dataModel->getModelName().' could not updated.');
        } else {    
            $item = $model->find($lastModel->id);
            $this->save($request,$item);
            return Redirect::to($dataModel->getName())->with('success',$dataModel->getName().' has been updated.');
        }
    }
    public function restore($request)
    {
    	$urlParser = $this->urlParser;

        $lastModelNameWithPath = $urlParser->last()->modelNameWithPath;
        $item = (new $lastModelNameWithPath)->find( $urlParser->last()->id);
        $dataModel = $urlParser->last()->dataModel;

            $item->deleted_at = null;
            $item->deleted_by = -1;
            $item->save();

        return Redirect::to($dataModel->getName())->with('success',$dataModel->getName().' has been restored.');
    
    }
    public function destroyAction()
    {
    	$urlParser = $this->urlParser;
        $lastModelNameWithPath = $urlParser->last()->modelNameWithPath;
        $item = (new $lastModelNameWithPath)->find( $urlParser->last()->id);
        $dataModel = $urlParser->last()->dataModel;
        $item->deleted_at = Carbon::now();
        $item->deleted_by = Auth::user()->id;
        $item->save();
        return Redirect::to($dataModel->getName())->with('success',$dataModel->getName().' has been deleted.');
	
    }

}
?>