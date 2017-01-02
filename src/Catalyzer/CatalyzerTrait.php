<?php 
namespace Catalyzer;

use Catalyzer\ControllerHelper\WebControllerHelper;
use Catalyzer\ControllerHelper\SimpleControllerHelper;

trait CatalyzerTrait {
	protected $controllerHelper;

	public function setControllerHelper($name)
	{
		switch($name)
		{
			case 'web':
        		$this->controllerHelper = new WebControllerHelper();
				break;
			case 'simple':
        		$this->controllerHelper = new SimpleControllerHelper();
				break;
			case 'json':
        		$this->controllerHelper = new JSONControllerHelper();
				break;
			default:
        		$this->controllerHelper = new WebControllerHelper();
        		break;
		}
	}
	public function catalyzeIndex()
	{
		return $this->controllerHelper->index();
	}
	public function catalyzeCreate()
	{
		return $this->controllerHelper->create();
	}
	public function catalyzeShow()
	{
		return $this->controllerHelper->show();
	}
	public function catalyzeEdit()
	{
		return $this->controllerHelper->edit();
	}
	public function catalyzeStore($request)
	{
		return $this->controllerHelper->storeAction($request );
	}
	public function catalyzeUpdate($request)
	{
		return $this->controllerHelper->updateAction($request );
	}
	public function catalyzeRestore($request)
	{
		return $this->controllerHelper->restore($request );
	}
	public function catalyzeDestroy()
	{
		return $this->controllerHelper->destroyAction();
	}
}