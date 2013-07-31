<?php

class search{
	var $filter;
	var $result;
	function __construct(){
		
	}
	function __destruct(){
		
	}
	function sendResult(){
		echo json_encode($this->result);
		return;
	}
}
?>