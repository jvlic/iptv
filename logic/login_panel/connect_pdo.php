<?php

/**
 * @author jurii vologin
 * @copyright 2021
 * pdo base work
 */

class DBTransaction
{
    protected $pdo;
    public $last_insert_id;

    public function __construct($db_host, $db_user,$db_pass,$db_database,$port='3306')
    {
        if (!defined('PATH')) {
        define('PATH', $_SERVER['DOCUMENT_ROOT']);
        }
       // define('DB_NAME', $db_database);
       // define('DB_USER', $db_user);
       // define('DB_PASSWORD', $db_pass);
      //  define('DB_HOST', $db_host);
      //  define('DB_PORT', '3306');
     
        try{
       $this->pdo = new PDO("mysql:host=".$db_host.";port=".$port."; dbname=".$db_database.";charset ='UTF8'", $db_user, $db_pass);
            
      // $this->pdo = new PDO("mysql:host=".DB_HOST.";port:".DB_PORT."; dbname=".DB_NAME.";charset ='UTF8'", DB_USER, DB_PASSWORD);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->exec("set names utf8");
        $this->pdo->exec("use ".$db_database);
        } catch  (PDOException $e) {
             print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    public function startTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function insertTransaction($sql, $data)
    {
       // $this->parse_sql($sql,$data);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $this->last_insert_id = $this->pdo->lastInsertId();
    }
    public function selectDB($sql,$data)
	{
      // $this->parse_sql($sql,$data);
		$stmt = $this->pdo->prepare($sql);
        
        $stmt->execute($data);
		
		if(!$stmt){
			$this->handleError();
			}
		else{
	//	return $stmt->fetchALL($fetchMode);
        return $stmt;
		}
    }
     public function selectDB_fetchALL($sql,$data, $fetchMode = PDO::FETCH_ASSOC)
	{
    //  $this->parse_sql($sql,$data);
		$stmt = $this->pdo->prepare($sql);
        
        
        $stmt->execute($data);
		
		if(!$stmt){
			$this->handleError();
			}
		else{
		  
		$data= $stmt->fetchALL($fetchMode);
        $stmt=null;
        return $data;
		}
    }
    public function updateDB($sql,$data)
	{
	
	//	$this->parse_sql($sql,$data);
        $stmt = $this->pdo->prepare($sql);
       
        $stmt->execute($data);
		
		if(!$stmt){
			$this->handleError();
			}
		else{
		return true;
		}
    }
    public function deleteDB($sql,$data)
	{
	
	//	$this->parse_sql($sql,$data);
        $stmt = $this->pdo->prepare($sql);
       
        $stmt->execute($data);
		
		if(!$stmt){
			$this->handleError();
			}
		else{
		return true;
		}
    }
    
    public function submitTransaction()
    {
        try {
            $this->pdo->commit();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }

          return true;
    }
    /* error check */
	private function handleError()
	{
		if ($this->errorCode() != '00000')
		{
			if ($this->_errorLog == true)
			//Log::write($this->_errorLog, "Error: " . implode(',', $this->errorInfo()));
			echo json_encode($this->errorInfo());
			throw new Exception("Error: " . implode(',', $this->errorInfo()));
		}
    }
    private function parse_sql($sql,$data)
	{
		require_once(PATH."/php/base_parser.php");
        echo parser($sql,$data);
    }
}
?>