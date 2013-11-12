<?php
require_once 'core/SysProperties.php';
require_once 'core/Logger.php';
require_once 'core/FileIO.php';
require_once 'QueBarato_API.php';
if (isset($argv)) {
	$ad = $argv[1];
} else {
	$ad = $_GET['ad'];
}

if (isset($ad)) {
	$QuebaratoAPI = new QueBarato_API();
	echo "Dados do Anuncio:\n";
	echo  json_encode($QuebaratoAPI->GetAnuncio($ad));
} else {
	echo "necesario passar o parametro ad com o id do anuncio";
}
?>