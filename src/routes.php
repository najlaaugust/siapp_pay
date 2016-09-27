<?php

$app->get('/courses', function ($request, $response, $args) {
	$this->logger->addInfo("get courses");

	$courseInfo = $this->dbguy->getCourseInfo();

	if (empty($courseInfo)) {
		die("died");
	}

	header("Content-Type: application/json");
	echo json_encode($courseInfo);
	exit;
});


$app->get('/currentappinfo', function ($request, $response, $args) {
	$this->logger->addInfo("get app info");

	$appInfo = $this->dbguy->getAppInfo();

	if (empty($appInfo)) {
		die("died");
	}

	header("Content-Type: application/json");
	echo json_encode($appInfo);
	exit;		
});

$app->get('/currentappinfo/{name}', function ($request, $response, $args) {
	$name = $args['name'];
	$this->logger->addInfo("get specific app info - " . $name);
	
	$appInfo = $this->dbguy->getAppInfo();
	$appInfoValue = "";

	foreach ( $appInfo as $key => $value ) {
		if ($key == $name) {
			$appInfoValue = $value;
		}
	}
	
	if (empty($appInfoValue)) {
		die("died");
	}
	
	header("Content-Type: application/json");
	echo json_encode($appInfoValue);
	exit;		
});

$app->get('/isvalid/{ConfirmNum}/{LastName}', function ($request, $response, $args) {
	$this->logger->addInfo("Check if exists in database");
	
	//$ConfirmNum ="HWAN-2016-0140";
	//$LastName ="HWANG";
	$ConfirmNum = $args['ConfirmNum'];
	$LastName = $args['LastName'];
	
	//query user info
	$userInfo = $this->dbguy->getUserInfo($ConfirmNum, $LastName);
	//print_r($userInfo);
	
	if (empty($userInfo)) {
		header("Content-Type: application/json");
		echo json_encode(array('notfound' => 'notfound'));
		exit;
	}	
	
	header("Content-Type: application/json");
	
	//header("Content-Type: application/json;charset=utf-8");
	
	// Collect what you need in the $data variable.
	/*
	$json = json_encode($userInfo, JSON_FORCE_OBJECT);
	if ($json === false) {
		// Avoid echo of empty string (which is invalid JSON), and
		// JSONify the error message instead:
		$json = json_encode(array("jsonError", json_last_error_msg()));
		if ($json === false) {
			// This should not happen, but we go all the way now:
			$json = '{"jsonError": "unknown"}';
		}
		// Set HTTP response status code to: 500 - Internal Server Error
		http_response_code(500);
	}
	echo $json;	
	*/
	echo json_encode($userInfo);
	exit;	
});

$app->get('/reginfo/{OnlineID}', function ($request, $response, $args) {
	$this->logger->addInfo("get registration info");

	//$OnlineID ="200";
	$OnlineID = (int)$args['OnlineID'];

	//query reg info
	$regInfo = $this->dbguy->getRegInfo($OnlineID);

	if (empty($regInfo)) {
		die("died");
	}
	
	header("Content-Type: application/json");
	echo json_encode($regInfo);
	exit;	
});

$app->get('/reginfo_foremail/{Confirm_Num}', function ($request, $response, $args) {
	$Confirm_Num = $args['Confirm_Num'];
	
	$this->logger->addInfo("get registration info for email-".$Confirm_Num);

	//query reg info
	$regInfo = $this->dbguy->getRegInfo_ForEmail($Confirm_Num);

	if (empty($regInfo)) {
		die("died");
	}

	header("Content-Type: application/json");
	echo json_encode($regInfo);
	exit;
});


$app->post('/webpay/new', function ($request, $response) {
	$this->logger->addInfo("Add payment record to database");
	
	$data = $request->getParsedBody();
	
	$payment_data = [];
	$payment_data[] = filter_var($data['PNREF'], FILTER_SANITIZE_STRING);
	$payment_data[] = filter_var($data['RESULT'], FILTER_SANITIZE_STRING);	
	$payment_data[] = filter_var($data['RESPMSG'], FILTER_SANITIZE_STRING);
	$payment_data[] = filter_var($data['AMOUNT'], FILTER_SANITIZE_STRING);
	$payment_data[] = filter_var($data['CUSTID'], FILTER_SANITIZE_STRING);
	$payment_data[] = new DateTime();

	$this->dbguy->insertWebPayFlowLog($payment_data);

});

$app->post('/registration/update', function($request, $response) {
	$this->logger->addInfo("Update registration record in database");
	
	$data = $request->getParsedBody();
	
	$payment_data = [];
	$payment_data[] = filter_var($data['PNREF'], FILTER_SANITIZE_STRING);
	$payment_data[] = "PayPal";
	$payment_data[] = new DateTime();
	$payment_data[] = filter_var($data['NAME'], FILTER_SANITIZE_STRING);
	$payment_data[] = filter_var($data['EMAIL'], FILTER_SANITIZE_STRING);
	$payment_data[] = filter_var($data['PHONE'], FILTER_SANITIZE_STRING);
	
	$address = filter_var($data['ADDRESS'], FILTER_SANITIZE_STRING);
	$city = filter_var($data['CITY'], FILTER_SANITIZE_STRING);
	$state = filter_var($data['STATE'], FILTER_SANITIZE_STRING);
	$zip = filter_var($data['ZIP'], FILTER_SANITIZE_STRING);
	$country = filter_var($data['COUNTRY'], FILTER_SANITIZE_STRING);
	
	$payment_data[] = sprintf('%1$s %2$s, %3$s %4$s %5$s', $address, $city, $state, $zip, $country);
	$payment_data[] = "PAID";
	
	$payment_data[] = filter_var($data['CUSTID'], FILTER_SANITIZE_STRING);
	
	$this->dbguy->updateWebApplications($payment_data);
});