<?php
require_once 'core/SysProperties.php';
require_once 'core/Logger.php';
require_once 'core/FileIO.php';
require_once 'QueBarato_API.php';
if (isset($argv)) {
	$login = $argv[1];
} else {
	$login = $_GET['login'];
}

if (isset($login)) {
	$QuebaratoAPI = new QueBarato_API();
	echo "Dados do Usuario:\n"; 	
	echo  json_encode($QuebaratoAPI->GetUser($login));
} else {
	echo "necesario passar o parametro login";
}
?>