<?php
namespace Catalyzer\Contracts;

interface DataModelContract
{
	public function __construct();
	public function getName();
	public function setName();
	public function getModelName();
	public function getTable();
	public function getColumns();
	public function getRules();
	public function getFormFields();
	public function getTableFields();
	public function getHiddenFields();
	public function setRules($rules);
	public function setColumns($columns);
    public function setForeigns($foreigns);
    public function setFormFields(array $formFields);
    public function setTableFields(array $tableFields);
    public function getForeigns();
    public function setDomestics($domestics);
    public function getDomestics();
}
?>