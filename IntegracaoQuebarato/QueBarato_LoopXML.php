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
require_once 'QueBarato_API.php';
require_once 'QueBarato_GeraListaArquivos.php';
SessionCtlr::StartLongSession();

$xmlload = new xmlLoad();
$QueBaratoAPI = new QueBarato_API();
$geraLista = new QueBarato_GeraListaArquivos();

$arquivo = (string)$_POST['arquivo'];
$resumo["Mensagem"] = "Total de registro processados para o arquivo xml";

Logger::loginfo("lendo XML: $arquivo");
$xmldata = $xmlload->loadXML($arquivo);
#$geraLista->BackupArquivo($arquivo);
$resumo["xml"] = $arquivo;
$resumo["data_inicio"] = date("Y-m-D H:i:s");
$resumo["consecionaria"] = 0;
$resumo["veiculo"] = 0;
$resumo["veiculo_novo"] = 0;
$resumo["veiculo_alterado"] = 0;

if (!is_null($xmldata)) {
	if ((string)$xmldata->APIQueBarato != "") {
		$QueBaratoAPI->privatekey = $xmldata->APIQueBarato;
	}

	if ((string)$QueBaratoAPI->privatekey == "") {
		Logger::logerror("Chave API do Quebarato nao informado");
		echo "Chave API do Quebarato nao informado<br/>";
		exit(0);
	}
	
	if (($xmldata->UsuarioQueBarato == "") || ($xmldata->SenhaQueBarato == "")) {
		Logger::logerror("Logon do Quebarato nao informado no XML:$arquivo");
		echo "Logon do Quebarato nao informado no XML:$arquivo<br/>";
		exit(0);
	}
	$userLoginDecoded = (string)base64_decode((string)$xmldata->UsuarioQueBarato);
	$userPwdDecoded = (string)base64_decode((string)$xmldata->SenhaQueBarato);
	$Quebarato_auth = $QueBaratoAPI->GetChaveUsuario(
			$userLoginDecoded,
			$userPwdDecoded);			
	$userData = $QueBaratoAPI->GetUser($userLoginDecoded);
	if (!$userData) {
		Logger::logerror("Usuario ($userLoginDecoded) no XML nao encontrado no cadastro do Quebarato. XML($arquivo)");
		echo "Usuario ($userLoginDecoded) no XML nao encontrado no cadastro do Quebarato. XML($arquivo)<br/>";
		exit(0);
	}
	$user = (string) $userData->id;
		
	foreach ($xmldata->concessionaria as $concessionaria) {
		$resumo["consecionaria"] = (int)$resumo["consecionaria"] + 1;
		foreach ($concessionaria->veiculo as $veiculo) {
			/*
			 if ((int)$resumo["veiculo"] > 42) {
			Logger::logerror("N�mero total(43) de registro maior que possivel de processamento, por favor revisar o XML($arquivo)");
			break;
			}
			*/
			
			$fotos = array();
			$imagefile = $veiculo->fotos->foto;
			for ($i=0;$i<count($imagefile);$i++) {
				$fotos[$i] = trim((string)$imagefile[$i]);
			}
				
			$fields = array(
					"idveiculo" => (string) $veiculo->idveiculo,
					"placa" => (string) $veiculo->placa,
					"cnpj_con" => (string) $veiculo->cnpj_con,
					"preco" => (string) $veiculo->preco,
					"tipo" => (string) $veiculo->tipo,
					"cep" => (string) $concessionaria->cep,
					"idQueBarato" => (string) $veiculo->idQueBarato,
					"titulo" => (string) $veiculo->titulo,
					"descricao" => (string)$veiculo->descricao,
					"arquivo" => (string)$arquivo,
					"Quebarato_auth" => (string)$Quebarato_auth,
					"cep" => (string)$concessionaria->cep,
					"estoque" => (string) $veiculo->estoque,
					"user" => (string)$user,
					"imagefile" => $fotos
			);
			HTTPRequest::PostData("QueBarato_LoopVeiculo.php", http_build_query($fields));
			if (trim($veiculo->idQueBarato) == "0" || trim($veiculo->idQueBarato) == "") {
				$resumo["veiculo_novo"] = intval($resumo["veiculo_novo"]) + 1;
			} else {
				$resumo["veiculo_alterado"] = intval($resumo["veiculo_alterado"]) + 1;
			}
			$resumo["veiculo"] = intval($resumo["veiculo"]) + 1;
			unset($fields);
		}
	}
	$resumo["data_final"] = date("Y-m-D H:i:s");
	$resumo_msg = "Resumo de execucao ($arquivo)\n";
	$resumo_msg .= "Arquivo: " . $arquivo . "\n";
	$resumo_msg .= "Inicio: " . $resumo["data_inicio"] . "\n";
	$resumo_msg .= "Final: " . $resumo["data_final"] . "\n";
	$resumo_msg .= "Total de Concessionarias: " . $resumo["consecionaria"] . "\n";
	$resumo_msg .= "Total de Veiculos: " . $resumo["veiculo"] . "\n";
	$resumo_msg .= "Total de Veiculos Adicionados: " . $resumo["veiculo_novo"] . "\n";
	$resumo_msg .= "Total de Veiculos Atualizados: " . $resumo["veiculo_alterado"] . "\n";
	Logger::loginfo($resumo_msg);
	Logger::SendMail($resumo_msg);

}
exit(0);
?>