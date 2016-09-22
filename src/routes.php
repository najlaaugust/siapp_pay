<?php

$app->get('/isvalid', function ($request, $response, $args) {
	$this->logger->addInfo("Check if exists in database");
	
	$ConfirmNum ="HWAN-2016-0140";
	$LastName ="HWANG";
	
	//query user info
	$userInfo = $this->dbguy->getUserInfo($ConfirmNum, $LastName);
	//print_r($userInfo);
	
	if (empty($userInfo))
		echo "nothing";
	else
		echo "something";

	$response->getBody()->write("yes");
	return $response;
});

$app->get('/reginfo/{OnlineID}', function ($request, $response, $args) {
	$this->logger->addInfo("get registration info");

	//$OnlineID ="200";
	$OnlineID = (int)$args['OnlineID'];

	//query reg info
	$regInfo = $this->dbguy->getRegInfo($OnlineID);

	if (empty($regInfo))
		echo "nothing";
		else
				print_r($regInfo);

	$response->getBody()->write("yes");
	return $response;
});


$app->post('/webpay/new', function ($request, $response) {
	$this->logger->addInfo("Add payment record to database");
	
	$data = $request->getParsedBody();
	
	$payment_data = [];
	$payment_data['transaction_id'] = filter_var($data['PNREF'], FILTER_SANITIZE_STRING);
	$payment_data['result_code'] = filter_var($data['RESULT'], FILTER_SANITIZE_STRING);	
	$payment_data['result_msg'] = filter_var($data['RESPMSG'], FILTER_SANITIZE_STRING);
	$payment_data['transaction_amount'] = filter_var($data['AMOUNT'], FILTER_SANITIZE_STRING);
	$payment_data['confirm_num'] = filter_var($data['CUSTID'], FILTER_SANITIZE_STRING);
	$payment_data['transaction_date'] = new DateTime();



});

$app->post('/registration/update', function($request, $response) {
	$this->logger->addInfo("Update registration record in database");
	
	$data = $request->getParsedBody();
	
	$payment_data = [];
	$payment_data['CCNumber'] = filter_var($data['PNREF'], FILTER_SANITIZE_STRING);
	$payment_data['CCType'] = "PayPal";
	$payment_data['CCDate'] = new DateTime();
	$payment_data['CCName'] = filter_var($data['NAME'], FILTER_SANITIZE_STRING);
	$payment_data['CCEmail'] = filter_var($data['EMAIL'], FILTER_SANITIZE_STRING);
	$payment_data['CCPhone'] = filter_var($data['PHONE'], FILTER_SANITIZE_STRING);
	
	$address = filter_var($data['ADDRESS'], FILTER_SANITIZE_STRING);
	$city = filter_var($data['CITY'], FILTER_SANITIZE_STRING);
	$state = filter_var($data['STATE'], FILTER_SANITIZE_STRING);
	$zip = filter_var($data['ZIP'], FILTER_SANITIZE_STRING);
	$country = filter_var($data['COUNTRY'], FILTER_SANITIZE_STRING);
	
	$payment_data['CCAddress'] = sprintf("%1$s %2$s, %3$s %4$s %5$s", $address, $city, $state, $zip, $country);
	$payment_data['PaymentStat'] = "PAID";
	
	$payment_data['Confirm_Num'] = filter_var($data['CUSTID'], FILTER_SANITIZE_STRING);
	

});