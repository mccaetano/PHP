<?php

require_once 'core/SysProperties.php';
require_once 'PHPMailer/class.smtp.php';
require_once 'PHPMailer/class.phpmailer.php';
require_once 'core/Logger.php';
require_once 'core/SessionCtrl.php';
require_once 'core/HTTPRequest.php';
SessionCtlr::StartLongSession();

echo "<p>para ver o trace <a href='info.txt'>clique aqui</a></p>";
echo "<p>para ver o erros <a href='error.txt'>clique aqui</a></p>";
ob_flush();
flush();
sleep(1);

#Rotina para rodar background
#include 'Quebarato_Main.php';

#Rotina para rodar online;
HTTPRequest::PostDataAssync('QueBarato_LoopArquivo.php', array("none" => ""));

?>