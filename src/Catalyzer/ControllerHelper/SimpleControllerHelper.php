<?php
namespace Catalyzer\ControllerHelper;

use Catalyzer\UrlParser;
use Catalyzer\ControllerHelper\ControllerHelper;


class SimpleControllerHelper extends ControllerHelper{

    public function __construct()
    {
        $this->result = new \stdClass();
        $this->urlParser = new UrlParser();
    }
    public function index()
    {
        $this->getIndex();
        return $this->result;
    }
    public function create()
    {
        $this->getCreate();
        return $this->result;
    }
    public function show()
    {
        $this->getShow( );
        return $this->result;
    }
    public function edit()
    {
        $this->getEdit();
        return $this->result;
    }
    public function makeShow( $dataModel, $item)
    {
        $name = $dataModel->getName();
        $this->result->$name = $item;
    }
    public function makeIndex( $dataModel, $items)
    {
        $name = $dataModel->getName();
        $this->result->$name = $items->all();
    }
    public function makeCreate( $dataModel, $model)
    {
        $name = $dataModel->getName();
        $this->result->$name = $model;
    }
    public function makeEdit( $dataModel, $item)
    {  
        $name = $dataModel->getName();
        $this->result->$name =  $item;
    }
}
?>