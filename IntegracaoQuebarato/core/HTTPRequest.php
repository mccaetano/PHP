<?php
class HTTPRequest {
	public static function PostData($page, $postData) {
		$domain = $_SERVER['HTTP_HOST'];
		$prefix = 'http://'; #array_key_exists('HTTPS', $_SERVER) ? 'https://' : 'http://';
		$context = $_SERVER["REQUEST_URI"];
		
		if (count(explode("/", $context)) > 1) {
			$contexts = explode("/", $context);
			$context = "/$contexts[1]/";
		} else {
			$context = "/";
		} 
		$url = $prefix.$domain.$context.$page;
		
		unset($curl);
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		
		$result = curl_exec($curl);
		$return = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if (intval($return) > 400) {
			if (is_array($result)) {
				$msg = "Erro HTTP(" . $return . "): \n";
				for ($i=0;$i<count($result);$i++) {
					$msg .= $result[$i] . "\n";
				}
				Logger::logerror($msg);
			} else {
				Logger::logerror("Erro HTTP(" . $return . "): " . $result);
			}
		}
		curl_close($curl);
		
		return $result;
	}
	
	public static function PostDataAssync($page, $postData) {
		$domain = $_SERVER['HTTP_HOST'];
		$prefix = 'http://'; #array_key_exists('HTTPS', $_SERVER) ? 'https://' : 'http://';
		$context = $_SERVER["REQUEST_URI"];
		
		if (count(explode("/", $context)) > 1) {
			$contexts = explode("/", $context);
			$context = "/$contexts[1]/";
		} else {
			$context = "/";
		} 
		$url = $prefix.$domain.$context.$page;
		
		unset($curl);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS,  $postData);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 3 );
				
		$result = curl_exec($curl);
		$return = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if (intval($return) > 400) {
			if (is_array($result)) {
				$msg = "Erro HTTP(" . $return . "): \n";
				for ($i=0;$i<count($result);$i++) {
					$msg .= $result[$i] . "\n";
				}
				Logger::logerror($msg);
			} else {
				Logger::logerror("Erro HTTP(" . $return . "): " . $result);
			}
		}
		
		curl_close($curl);
		
		return TRUE;
	}
}
?>