<?php
$AnuncioPub = array (
    'title' => 'Teste de anuncio',
    'description' => '<p>teste de anuncio descricao teste de anuncio com descricao comprida de muitos caracteres<\/p>',    
/*    'price' => array (
        'currency' => 'BRL', 
        'amount' => '1', 
    ),
    'condition' => 'novo',  
 */   'locale' => array (
        'zip' => '03506000'
    )/*,
    'paymentMethods' => array (
        'href' => '/payment-method/offline/3' 
    ),
    'category' => array (
        'href' => '/category/178',
        'name' => 'Consultores em informática'
    )
*/);

$data_string = json_encode($AnuncioPub);

$cURL = curl_init("http://api.quebarato.com/v2/ad");        
$cURL_header_data = array(
    'X-QB-Key: df54ef4848ba421c30354d60465d54ed',
    'Accept: application/json',
    "Authorization: Basic bWNjYWV0YW5vOk1jY25tZHNjMDE=",    
    'Content-type: application/json'
);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURL_header_data);


curl_setopt($cURL, CURLOPT_POSTFIELDS, $data_string);

echo "Envio: \n";
echo var_dump($data_string);

$cURL_response = curl_exec($cURL);

$retorno_code = (string)curl_getinfo($cURL, CURLINFO_HTTP_CODE);
echo "Retorno: " . $retorno_code . "\n";
if ($retorno_code == '200')
echo (string)$cURL_response;
?>
