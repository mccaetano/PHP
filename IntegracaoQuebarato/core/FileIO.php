<?php
class FileIO {
	static function DownloadImage($file) {
		
		$pathinfo = pathinfo($file);		
		$fileOut = "tmp/" . $pathinfo["basename"];
		unset($ch);
		$ch = curl_init();		
		curl_setopt($ch,CURLOPT_URL,$file);		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);		
		$data = curl_exec($ch);
		$cURL_Error = curl_error($ch);		
		$cURL_ErrorN = curl_errno($ch);
		$cURL_Status_Response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		unset($ch);

		if ($cURL_ErrorN > 0) {
			$errmsg = "HTTP response error ($cURL_Status_Response) for url($url) para o metodo POST em adicionar imagem , see the http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html.\n";
			$errmsg .= $cURL_Error . "\n";
			$errmsg .= "Body: \n" . var_dump($file) . "\n";
			Logger::logerror($errmsg);
			return FALSE;
		}
		
		if ($data) {		
			$fh = fopen($fileOut,'w');		
			fwrite($fh,$data);		
			fclose($fh);		
		} else {
			$fileOut = FALSE;
		}
		
		return realpath($fileOut);
	} 
	
	static function convertToUTF8($file) {
		$pathinfo = pathinfo($file);
		$fileOut = "tmp/" . $pathinfo["basename"];
		
		$fs_in = fopen($file, "r");
		$data = fread($fs_in, filesize($file)); 
		if (!$data) {
			$errmsg = "Erro lendo o arquivo ($file).\n";
			Logger::logerror($errmsg);
			return FALSE;
		}
		fclose($fs_in);
		$fs_ou = fopen($fileOut,'w');
		fwrite($fs_ou, utf8_encode($data));
		fclose($fs_ou);		
		
		return realpath($fileOut);
	}
}

?>