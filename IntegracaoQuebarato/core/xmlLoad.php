<?php

class xmlLoad {
	public $data;
	
	public function loadXML($urladdress) {
		libxml_use_internal_errors(true);
		
		$data = simplexml_load_file($urladdress);
				
		if (!$data) {
			$errors = libxml_get_errors();                        
			foreach ($errors as $error) {
				Logger::logerror((string)$error->message);
			}

			libxml_clear_errors();
		}
		
		return $data;
	}
}
?>