<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of pdo
 *
 * @author mamo
 */
class sql_db {
    var $dbh;
    var $stmt;
    var $errors;
    var $fields;
    
    function __construct($sqlserver, $sqluser, $sqlpassword, $database, $persistency = true){
        list($dbhost,$dbport) = explode(":",$sqlserver);
        if (!$dbport) $dbport="5432";
        $dsn = sprintf('pgsql:dbname=%s;host=%s;port=%s',$database,$dbhost,$dbport);
        
		try{
			$conn = new PDO($dsn, $sqluser, $sqlpassword);
		}
		catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
		
        $this->dbh = $conn;
        $this->errors = Array();
    }
    function sql_query($query = "", $transaction = false){
        $stmt = $this->dbh->prepare($query);
        if (!$stmt->execute()){
			$message = $stmt->errorInfo();
            $this->errors[] = Array("code" => $stmt->errorCode(),"message"=>$message[2]);
			$this->stmt = NULL;
        }
        else{
            $this->fields=Array();
            for($i=0;$i<$stmt->columnCount();$i++){
                $d = $stmt->getColumnMeta($i);
                $this->fields[] = $d["name"];
            }
            $this->stmt = $stmt;
        }
    }
    function sql_numrows(){
        if ($this->stmt){
            return $this->stmt->rowCount(); 
        }
        else {
            return -1;
        }
    }
    function sql_numfields(){
        if ($this->stmt){
            return $this->stmt->columnCount(); 
        }
        else {
            return -1;
        }
    }
    function sql_fieldname($offset=0){
        if ($this->stmt){
            $data = $this->stmt->getColumnMeta($offset);
            return $data["name"];
        }
        else {
            return NULL;
        }
    }
    function sql_fieldtype($offset = 0){
        if ($this->stmt){
            $data = $this->stmt->getColumnMeta($offset);
            return $data["native_type"];
        }
        else {
            return NULL;
        }
    }
    function sql_fetchrow(){
        if ($this->stmt){
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        }
        else {
            return NULL;
        }
    }
    function sql_fetchrowset($query_id = 0){
        if ($this->stmt){
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else {
            return NULL;
        }
    }
    function sql_fetchfield($field, $row_offset=0, $query_id = 0){
        if ($this->stmt){
            $index = array_search($field,$this->fields); 
            $values = $this->stmt->fetchAll(PDO::FETCH_ASSOC,$index);
            return $values[$row_offset];
        }
        else {
            return NULL;
        }
    }
    function sql_fetchlist($field){
        if ($this->stmt){
            $index = array_search($field,$this->fields); 
            $values = $this->stmt->fetchAll(PDO::FETCH_COLUMN,$index);
            return $values;
        }
        else {
            return NULL;
        }
    }
    
}