<?php
namespace Catalyzer\ControllerHelper;

use Catalyzer\UrlParser;
use Catalyzer\Contracts\ControllerHelperContract;
use Catalyzer\ViewProvider\JSONViewProvider;


class JSONControllerHelper implements ControllerHelperContract{

    public function index($urlParser)
    {
        $rendered = $this->getIndex($urlParser);
            
    }
    public function create($urlParser)
    {
        $rendered = $this->getCreate($urlParser);
        return View('catalyzer.general')->with(['rendered'=>$rendered,'parameters' =>$urlParser->getParameters()]);
    }
    public function show($urlParser)
    {
        $rendered = $this->getShow($urlParser );
        return View('catalyzer.general')->with(['rendered' => $rendered,'parameters' =>$urlParser->getParameters()]);
    }
    public function edit($urlParser)
    {
        $rendered = $this->getEdit($urlParser );
        return View('catalyzer.general')->with(['rendered' => $rendered,'parameters' =>$urlParser->getParameters()]);
    }
    public function makeShow($urlParser, $dataModel, $item)
    {
        $this->result .= WebViewProvider::provideShowView($urlParser,$dataModel, $item);
    }
    public function makeIndex($urlParser, $dataModel, $items)
    {
        $this->result .= WebViewProvider::provideIndexView($urlParser, $dataModel, $items);
    }
    public function makeCreate($urlParser, $dataModel, $model)
    {
        $this->result = WebViewProvider::provideCreateView($urlParser, $dataModel, $model);
    }
    public function makeEdit($urlParser, $dataModel, $item)
    {  
        $this->result = WebViewProvider::provideEditView($urlParser, $dataModel, $item);
    }
}
?>