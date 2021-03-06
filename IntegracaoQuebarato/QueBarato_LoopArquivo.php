<?php

require_once 'core/SysProperties.php';
require_once 'PHPMailer/class.smtp.php';
require_once 'PHPMailer/class.phpmailer.php';
require_once 'core/Logger.php';
require_once 'core/SessionCtrl.php';
require_once 'core/FileIO.php';
require_once 'core/xmlLoad.php';
require_once 'core/DBImpl.php';
require_once 'core/BancoDadosFactory.php';
require_once 'core/HTTPRequest.php';
require_once 'QueBarato_GeraListaArquivos.php';
SessionCtlr::StartLongSession();

$geraLista = new QueBarato_GeraListaArquivos();
$listaaqruivos = $geraLista->ListaArquivos();
Logger::loginfo("Total de arquivos a processar:" . count($listaaqruivos));

foreach ($listaaqruivos as $arquivo) {	

	Logger::loginfo("Arquivo encontrado: $arquivo");
	$fields = array("arquivo" => $arquivo);
	HTTPRequest::PostDataAssync("QueBarato_LoopXML.php", $fields);
}

exit(0);
?>