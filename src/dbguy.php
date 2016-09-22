<?php

class dbguy {
	
	private $serverName;
	//private $uid;
	//private $pwd;
	//private $database;
	
	private $connectionInfo;
	
	public function __construct($serverName,$uid,$pwd,$database) {
		$this->serverName = $serverName;
		//$this->uid = $uid;
		//$this->pwd = $pwd;
		//$this->database = $database;
		
		$this->connectionInfo = array("UID"=>$uid, "PWD"=>$pwd, "Database"=>$database);
	}
	
	private function getData($theQuery)
	{
		$conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
		if( $conn === false )
		{
			echo "Unable to connect.</br>";
			die( print_r( sqlsrv_errors(), true));
		}	

		$stmt = sqlsrv_query( $conn, $theQuery);
		if( $stmt === false )
		{
			echo "Error in executing query.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		
		$row = sqlsrv_fetch_array($stmt);
	
		sqlsrv_free_stmt( $stmt);
		sqlsrv_close( $conn);
		
		return $row;
	}
	
	public function getUserInfo($ConfirmNum, $LastName) {
		 $tsql = "SELECT 	a.*,  s.state_name AS StateName, cn.country_name AS CountryName
					 FROM 	dbo.web_applications AS a LEFT OUTER JOIN us_states AS s ON
					 UPPER(a.[state])	= s.state_id LEFT OUTER JOIN dbo.countries AS cn ON
					 UPPER(a.country)	= cn.country_id
					 WHERE	UPPER(confirm_num) 	= '$ConfirmNum' AND
					 LOWER(lname) 		= '$LastName'";

		$row = $this->getData($tsql);
		 
        return $row;
	}
}