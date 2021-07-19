<?php

namespace Modules;

require 'autoload.php';

class Response {

	public $data 	= [];
	public $json 	= [];
	public $status 	= [];

	public function __construct(array $data, int $status) {
		$this->data 	= $data;
		$this->status 	= $status;
		$this->json 	= json_encode($this->data, JSON_UNESCAPED_UNICODE);
	}
}

?>