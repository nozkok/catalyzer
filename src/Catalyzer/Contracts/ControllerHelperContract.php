<?php 
namespace App\Catalyzer\Contracts;

interface ControllerHelperContract{
	public function getIndex();
	public function getShow();
	public function getCreate();
	public function getEdit();
}
?>