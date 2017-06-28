<?php

interface Interface_iForm {
	
	public function getDataById($id);
	
	public function selectData();
	
	public function createNew(Zend_Form $dataForm);
	
	public function updateRow(Zend_Form $dataForm);
	
	public function deleteRow($id);
}

?>