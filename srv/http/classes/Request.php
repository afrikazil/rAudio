<?php

class Request
{
	public string $method = '';
	public array $requestData = [];
	public function __construct()
	{
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->requestData = $this->get_request_data();
	}

	private function get_request_data ():array {
		return array_merge(empty($_POST) ? array() : $_POST, (array) json_decode(file_get_contents('php://input'), true), $_GET);
	}


	/**
	 * Send an API response
	 * @param  array $response The API response
	 * @param  int $code     The response code
	 */
	public function send_response (array $response, int $code = 200):void {
		http_response_code($code);
		echo json_encode($response);
	}
}
