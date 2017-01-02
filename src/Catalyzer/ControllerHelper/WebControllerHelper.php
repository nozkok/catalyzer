<?php
namespace Catalyzer\ControllerHelper;

use Catalyzer\UrlParser;
use Catalyzer\ControllerHelper\ControllerHelper;
use Catalyzer\ViewProvider\WebViewProvider;

class WebControllerHelper extends ControllerHelper
{
    public function __construct()
    {
        $this->result = "";
        $this->urlParser = new UrlParser();
    }
    public function index()
    {
        $rendered = $this->getIndex();
        $parameters = $this->urlParser->getParameters();
        return View('catalyzer.general')->with(['rendered' => $rendered, 'parameters' =>$parameters]);        
    }
    public function create()
    {
        $rendered = $this->getCreate();
        $parameters = $this->urlParser->getParameters();
        return View('catalyzer.general')->with(['rendered' => $rendered, 'parameters' =>$parameters]);        
    }
    public function show()
    {
        $rendered = $this->getShow();
        $parameters = $this->urlParser->getParameters();
        return View('catalyzer.general')->with(['rendered' => $rendered, 'parameters' =>$parameters]);        
    }
    public function edit()
    {
        $rendered = $this->getEdit();
        $parameters = $this->urlParser->getParameters();
        return View('catalyzer.general')->with(['rendered' => $rendered, 'parameters' =>$parameters]);        
    }
    public function makeShow( $dataModel, $item)
    {
        $this->result .= WebViewProvider::provideShowView($this->urlParser,$dataModel, $item);
    }
    public function makeIndex( $dataModel, $items)
    {
        $this->result .= WebViewProvider::provideIndexView($this->urlParser, $dataModel, $items);
    }
    public function makeCreate( $dataModel, $model)
    {
        $this->result = WebViewProvider::provideCreateView($this->urlParser, $dataModel, $model);
    }
    public function makeEdit( $dataModel, $item)
    {  
        $this->result = WebViewProvider::provideEditView($this->urlParser, $dataModel, $item);
    }


}
?>