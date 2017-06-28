<?php

interface Interface_iData {
	
	public function getDataById($id);
	
	public function selectData();
	
	public function createNew($inputArray = array());
	
	public function updateRow($inputArray = array());
	
	public function deleteRow($id);
}

?>