<?php 
namespace Catalyzer\Contracts;

interface ControllerHelperContract{
	public function getIndex();
	public function getShow();
	public function getCreate();
	public function getEdit();
}
?>