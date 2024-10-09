<?php
error_reporting(-1);
ini_set('display_errors', 'On');

include 'classes/Request.php';

$request = new Request();

$request->send_response($request->requestData);

//// All other request methods
//send_response(array(
//	'code' => 405,
//	'status' => 'failed',
//	'message' => 'Method not allowed'
//), 405);
