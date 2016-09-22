<?php

$app->get('/isvalid', function ($request, $response) {
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