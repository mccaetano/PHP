<?php
class QueBarato_API {
    public $urladdress;
    public $privatekey;
        
    public function __construct() {
        $this->urladdress = SysProperties::getPropertyValue("quebaratoapi.url");
        $this->privatekey = SysProperties::getPropertyValue("quebaratoapi.key");
    }
    
    public function GetChaveUsuario($login, $senha) {
        return base64_encode("$login:$senha");
    }
    
    public function GetUser($login) {
        $search = "/v2/user";
        if(strpos($login, '@') !== false) {
            $search .= "?email={$login}";
        } else {
            $search .= "?username={$login}";
        }        
        $url = $this->urladdress . $search;
        
        $cURL_header_data = array(
            "X-QB-Key: $this->privatekey",            
            "Accept: application/json"            
        );
        
        unset($cURL);        
        $cURL = curl_init();        
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURL_header_data);
        $cURL_response = curl_exec($cURL);
        $cURL_Error = curl_error($cURL);	
		$cURL_ErrorN = curl_errno($cURL);
        $cURL_Status_Response = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        curl_close($cURL);
        unset($cURL);
        
        if (intval($cURL_Status_Response) > 300) {
        	$errmsg = "HTTP response error ($cURL_Status_Response) for url($url) para o metodo POST em adicionar imagem , see the http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html.\n";
            $errmsg .= $cURL_Error . "\n";  
            $errmsg .= "Body: \n" . $login . "\n";   
            Logger::logerror($errmsg);
            return FALSE;
        }
        Logger::logerror("msg");
        return json_decode($cURL_response);
    }
        
    public function GetAnuncio($anuncioid) {
        $url = "$this->urladdress/v2/ad/$anuncioid";
       	$cURL_header_data = array(
            "X-QB-Key: $this->privatekey",
            "Accept: application/json"            
        );
        
       	unset($cURL);
       	$cURL = curl_init();       
        curl_setopt($cURL, CURLOPT_URL, $url);        
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURL_header_data);
        $cURL_response = curl_exec($cURL);  
		$cURL_Error = curl_error($cURL);	
		$cURL_ErrorN = curl_errno($cURL);
        $cURL_Status_Response = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        curl_close($cURL);
        unset($cURL);
                
        if (intval($cURL_Status_Response) > 300) {
        	$errmsg = "HTTP response error ($cURL_Status_Response) for url($url) para o metodo POST em adicionar imagem , see the http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html.\n";
            $errmsg .= $cURL_Error . "\n";        
            Logger::logerror($errmsg);
            return FALSE;
        }
        
        $decoded = json_decode($cURL_response);
        
        if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
        	Logger::logerror('error occured: ' . $decoded->response->errormessage);
            $errmsg .= "Body: \n" . $anuncioid . "\n"; 
        	return FALSE;
        }
        
        
        return $decoded;
    }

    public function AddAnuncio($anuncio, $autorization) {
        $quebaratiid = FALSE;
        $url = $this->urladdress . "/v2/ad";     
       
       	$cURL_header_data = array(
            "X-QB-Key: $this->privatekey",
            "Accept: application/json",
            "Authorization: Basic $autorization",
            "Content-type: application/json"
        );

        $json_anuncio = json_encode($anuncio);
                        
        unset($cURL);
        $cURL = curl_init();        
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($cURL, CURLOPT_HEADER, TRUE);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURL_header_data);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_anuncio);        
        $cURL_response = curl_exec($cURL);
		$cURL_Error = curl_error($cURL);	
		$cURL_ErrorN = curl_errno($cURL);
        $cURL_Status_Response = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        curl_close($cURL);
        unset($cURL);    
                
        if (intval($cURL_Status_Response) > 300) {
        	$errmsg = "HTTP response error ($cURL_Status_Response) for url($url) para o metodo POST em adicionar imagem , see the http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html.\n";
            $errmsg .= $cURL_Error . "\n";
            $errmsg .= "Body: \n" . $json_anuncio . "\n";
            Logger::logerror($errmsg);            
            return FALSE;
        }        
        $response = explode("\n", $cURL_response);
        
        $sresponse = "";
        foreach ($response as $item) {
        	$sresponse .= $item;
            if (sizeof(explode(': ', $item, 2)) > 1) {
                list($key, $value) = explode(': ', $item, 2);
                if ($key == "Location") {
                    list(,$item) = explode("ad/", $value);
                    $quebaratiid = trim($item);
                    break;
                }
            }
        }      
        if (!$quebaratiid) {
        	$errmsg = "Erro ao buscar o ID Quebarato.\n";
        	$errmsg .= "Response:" . $sresponse . "\n";
        	$errmsg .= "Body: \n" . $json_anuncio . "\n";
        	Logger::logerror($errmsg);
        }  
        
        return $quebaratiid;
    }
    
    public function AddImage($anuncioid, $file, $autorization) {
        $url = $this->urladdress . "/v2/ad/$anuncioid/media/image";              
        
        $cURL_header_data = array(         
            "X-QB-Key: $this->privatekey",
            "Authorization: Basic $autorization",
            "Content-type: multipart/form-data"
        );

        $pathinfo = pathinfo($file);
        
        $fileData = array (
        		'image' => '@' . $file . ';type=image/' . $pathinfo['extension']
        );
        
        unset($cURL);
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_POST, TRUE);
        curl_setopt($cURL, CURLOPT_HEADER, FALSE);
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURL_header_data);
        curl_setopt($cURL, CURLOPT_POSTFIELDS,  $fileData);        
        $cURL_response = curl_exec($cURL);
        $cURL_Error = curl_error($cURL);	
		$cURL_ErrorN = curl_errno($cURL);
        $cURL_Status_Response = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        curl_close($cURL);
        unset($cURL);
        
        if (intval($cURL_Status_Response) > 300) {
        	$errmsg = "HTTP response error ($cURL_Status_Response) for url($url) para o metodo POST em adicionar imagem , see the http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html.\n";
            $errmsg .= $cURL_Error . "\n";
            $errmsg .= "Body: \n" . json_encode($fileData) . "\n";
            Logger::logerror($errmsg);
            return FALSE;
        }
        
        return $cURL_response;
    }
    
    public function SetAnuncio($anuncio, $autorization) {        
        $url = $this->urladdress . "/v2/ad/" . $anuncio["id"];
                
        $cURL_header_data = array(
            "X-QB-Key: $this->privatekey",
            "Accept: application/json",
            "Authorization: Basic $autorization",
            "Content-type: application/json"
        );
        
        $json_anuncio= json_encode($anuncio);
        
        unset($cURL);
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURL_header_data);       
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_anuncio);
        curl_exec($cURL);
        $cURL_response = curl_exec($cURL);	
		$cURL_Error = curl_error($cURL);
		$cURL_ErrorN = curl_errno($cURL);         
        $cURL_Status_Response = curl_getinfo($cURL, CURLINFO_HTTP_CODE);    
        curl_close($cURL);
        unset($cURL);
        
        if (intval($cURL_Status_Response) > 300) {
        	$errmsg = "HTTP response error ($cURL_Status_Response) for url($url) para o metodo POST em adicionar imagem , see the http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html.\n";
            $errmsg .= $cURL_Error . "\n";
            $errmsg .= "Body: \n" . $json_anuncio . "\n";            
            Logger::logerror($errmsg);
            return false;
        }                
        return true;
    }
}

?>
