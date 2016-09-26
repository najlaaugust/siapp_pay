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
	
	private function getDataset($theQuery)
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
	
		$result_array = array();
		
		while( $obj = sqlsrv_fetch_object( $stmt)) {
			$result_array[$obj->name] = $obj->value;
		}		
		
		sqlsrv_free_stmt( $stmt);
		sqlsrv_close( $conn);
	
		return $result_array;
	}	
	

	private function executeStoredProcedure($theQuery)
	{
		$conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
		if( $conn === false )
		{
			echo "Unable to connect.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
	
		$stmt = sqlsrv_query($conn, $theQuery);
		if( $stmt === false )
		{
			echo "Error in executing query.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
	
		$result_array = array();
		
		while( $obj = sqlsrv_fetch_object( $stmt)) 
		{
			//echo "something";
			$result_array[] = array(
					"course_id" => $obj->course_id, 
					"session_no" => $obj->session_no, 
					"session_name" => $obj->session_name,
					"schedule_time" => $obj->schedule_time,
					"course_full_name" => $obj->course_full_name,
					"credits" => $obj->credits,
					"schedule_date" => $obj->schedule_date,
					"instructor" => $obj->instructor,
					"course_number" => $obj->course_number,
			);
		}
		
		sqlsrv_free_stmt( $stmt);
		sqlsrv_close( $conn);
		return $result_array;
	}
		
	
	private function getData($theQuery, array $params)
	{
		$conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
		if( $conn === false )
		{
			echo "Unable to connect.</br>";
			die( print_r( sqlsrv_errors(), true));
		}	

		$stmt = sqlsrv_query( $conn, $theQuery, $params);
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
	
	private function insertData($theQuery, array $params)
	{
		$conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
		if( $conn === false )
		{
			echo "Unable to connect.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		
		$stmt = sqlsrv_query( $conn, $theQuery, $params);
		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		}

		sqlsrv_free_stmt( $stmt );
		sqlsrv_close( $conn);
	}	
	
	private function updateData($theQuery, array $params)
	{	
		$conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
		if( $conn === false )
		{
			echo "Unable to connect.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
				
		$reslt = sqlsrv_query($conn, $theQuery, $params);
		if($reslt === false )
			die( FormatErrors( sqlsrv_errors() ) );
		
		sqlsrv_free_stmt( $reslt );
		sqlsrv_close( $conn );
	}	
	
	public function getCourseInfo() {
		$tsql = "EXEC dbo.usp_get_course_schedule";	
		$courses = $this->executeStoredProcedure($tsql);
		return $courses;
	}
		
		
	public function getAppInfo() {
		$tsql = "SELECT 	name, value from app_config";
	
		$row = $this->getDataset($tsql);
		return $row;
	}	
	
	public function getUserInfo($ConfirmNum, $LastName) {
		 $tsql = "SELECT 	a.*,  s.state_name AS StateName, cn.country_name AS CountryName
					 FROM 	dbo.web_applications AS a LEFT OUTER JOIN us_states AS s ON
					 UPPER(a.[state])	= s.state_id LEFT OUTER JOIN dbo.countries AS cn ON
					 UPPER(a.country)	= cn.country_id
					 WHERE	UPPER(confirm_num) 	= ? AND
					 LOWER(lname) 		= ?";

		$row = $this->getData($tsql, array($ConfirmNum, $LastName));
        return $row;
	}
	
	public function getRegInfo($OnlineID) {
		$tsql = "SELECT 	a.OnlineID, a.Confirm_Num, a.fname, a.mname, a.lname, 
			        		a.address1, a.address2, a.address3, a.city, a.[state], 
			                a.zip, a.country, a.hphone, a.email, a.PayAmount,
			                s.state_name AS StateName, cn.country_name AS CountryName,
			                (SELECT CAST([value] AS INT) FROM dbo.app_config WHERE [name] = 'current_app_year') AS CurrentYear
			        FROM 	dbo.web_applications AS a LEFT OUTER JOIN us_states AS s ON
			                UPPER(a.[state])	= s.state_id LEFT OUTER JOIN dbo.countries AS cn ON
			                UPPER(a.country)	= cn.country_id
			        WHERE	a.OnlineID	= ?";
        $row = $this->getData($tsql, array($OnlineID));
        return $row;			        		
	}
	
	public function insertWebPayFlowLog(array $data) {
		
		//$transaction_id = $data['transaction_id'];
		//$result_code = $data['result_code'];
		//$result_msg = $data['result_msg'];
		//$transaction_amount = $data['transaction_amount'];
		//$confirm_num = $data['confirm_num'];
		//$transaction_date = $data['transaction_date'];		
		
 	 	//$tsql = "INSERT INTO web_payflow_log (transaction_id, result_code, result_msg, transaction_amount, confirm_num, transaction_date)
		 //			VALUES ('$transaction_id', '$result_code', '$result_msg', '$transaction_amount', '$confirm_num', '$transaction_date')";

		$tsql = "INSERT INTO web_payflow_log (transaction_id, result_code, result_msg, transaction_amount, confirm_num, transaction_date) 
					VALUES (?, ?, ?, '?, ?, ?)";
		
 	 	$this->insertData($tsql, $data);
		 		
	}
	
	public function updateWebApplications(array $data) {

		/*$CCNumber = $data['CCNumber'];
		$CCType = $data['CCType'];
		$CCDate = $data['CCDate'];
		$CCName = $data['CCName'];
		$CCEmail = $data['CCEmail'];
		$CCPhone = $data['CCPhone'];
		$CCAddress = $data['CCAddress'];
		$PaymentStat = $data['PaymentStat'];
		$Confirm_Num = $data['Confirm_Num'];
		
		
		$tsql = "UPDATE 	web_applications SET					 	
					CCNumber		= '$CCNumber',
					CCType			= '$CCType',
					CCDate			= '$CCDate',
					CCName 			= '$CCName',
					CCEmail 		= '$CCEmail',
					CCPhone			= '$CCPhone',
					CCAddress		= '$CCAddress',
					PaymentStat		= '$PaymentStat'
					WHERE 	Confirm_Num		= '$Confirm_Num'";*/
		$tsql = "UPDATE 	web_applications SET
					CCNumber		= ?,
					CCType			= ?,
					CCDate			= ?,
					CCName 			= ?,
					CCEmail 		= ?,
					CCPhone			= ?,
					CCAddress		= ?,
					PaymentStat		= ?'
					WHERE 	Confirm_Num		= ?";		
		
		$this->updateData($tsql, $data);
	}
}