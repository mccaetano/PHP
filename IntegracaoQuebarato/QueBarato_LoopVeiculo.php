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
require_once 'QueBarato_DBControle.php';
require_once 'QueBarato_API.php';

SessionCtlr::StartLongSession();

$QueBaratoAPI = new QueBarato_API();
$dbData = new QueBarato_DBControle();

$idveiculo = (string)$_POST['idveiculo'];
$placa = (string)$_POST['placa'];
$cnpj_con = (string)$_POST['cnpj_con'];
$preco = (string)$_POST['preco'];
$tipo = (string)$_POST['tipo'];
$cep = (string)$_POST['cep'];
$idQueBarato = (string)$_POST['idQueBarato'];
$titulo = (string)$_POST['titulo'];
$descricao = (string)$_POST['descricao'];
$arquivo = (string)$_POST['arquivo'];
$Quebarato_auth = (string)$_POST['Quebarato_auth'];
$user = (string)$_POST['user'];
$imagefile = $_POST['imagefile'];
$estoque = $_POST['estoque'];

Logger::loginfo("Lendo veiculo:" . $idveiculo . " placa: " . $placa . " cnpj: " . $cnpj_con);


if ((double)$preco <= 0.0) {
	$msg = print_r($_POST, TRUE) . "\n";
	$msg .= "Veiculo ID ($idveiculo), placa($placa) esta sem o preço, por favor revisar o XML($arquivo)";
	Logger::logerror($msg);
	die($msg);
}

if (trim(strtoupper($tipo)) != "USADO" && trim(strtoupper($tipo)) != "NOVO") {
	$msg = print_r($_POST, TRUE) . "\n";
	$msg .= "Veiculo ID ($idveiculo), placa($placa) esta com tipo diferente de Novo/Usado, por favor revisar o XML($arquivo)";
	Logger::logerror($msg);
	die($msg);
}

if (trim(strtoupper((string)$cep)) == "") {
	$msg = print_r($_POST, TRUE) . "\n";
	$msg .= "Veiculo ID ($idveiculo), placa($placa) CEP em branco, por favor revisar o XML($arquivo)";
	Logger::logerror($msg);
	die($msg);
}

$condpagamento = SysProperties::getPropertyValue("quebaratoapi.condpagamento");
if (trim((string) $condpagamento) == "") {
	$msg = print_r($_POST, TRUE) . "\n";
	$msg .= "Veiculo ID ($idveiculo), placa($placa) condição pagamento em branco, por favor revisar o XML($arquivo)";
	Logger::logerror($msg);
	die($msg);
}

$categoria = SysProperties::getPropertyValue("quebaratoapi.categoria");
if (trim((string) $categoria) == "") {
	$msg = print_r($_POST, TRUE) . "\n";
	$msg .= "Veiculo ID ($idveiculo), placa($placa) categoria em branco, por favor revisar o XML($arquivo)";
	Logger::logerror($msg);
	die($msg);
}

if (!$estoque) {
	$estoque = '1';
}

if (trim((string)$estoque) == '') {
	$estoque = '1';
} 

if ($idQueBarato == '' || $idQueBarato == '0') {
	$idQueBarato = FALSE;
}

if ($idQueBarato) {
	Logger::loginfo("Anuncio encontrado para o VeiculoID: $idveiculo, AnuncioID: $idQueBarato.");
}

#Montandno anuncio no QueBarato caso haja cadastro no Banco de dados.

if ($idQueBarato) {
	$AnuncioPub['id'] = $idQueBarato;
}

$AnuncioPub['title'] = trim($titulo);
$AnuncioPub['description'] = $descricao;
$AnuncioPub['price'] = array(
		'currency'=> 'BRL',
		'value' => doubleval($preco)
);
$AnuncioPub['condition'] = $tipo;
$AnuncioPub['locale'] = array(
		"zip" => (string) (preg_replace('/[^a-z\d ]/i', '', $cep))
);
$AnuncioPub['paymentMethods'] = array(
		array(
				'href' => $condpagamento
		)
);
$AnuncioPub['category'] = array(
		'href' => $categoria
);
$AnuncioPub['advertiser'] = array (
		'href' => "/user/$user"
);
$AnuncioPub['stock'] = array(
		'available' => $estoque
);

if (!$idQueBarato) {
	$idQueBarato = $QueBaratoAPI->AddAnuncio($AnuncioPub, $Quebarato_auth);
	if (!$idQueBarato) {
		Logger::logerror("\nXML($arquivo). Anuncio nao criando veja o log ");
		die("Anuncio nao criando veja o log<br/>");
	}
	$dbData->SetAnuncio($idveiculo, $idQueBarato, $cnpj_con);
	Logger::loginfo("Anuncio criado no QueBarato: AnuncioID: $idQueBarato, Tiulo: $titulo. ");
} else {
	$idQueBarato = $QueBaratoAPI->SetAnuncio($AnuncioPub, $Quebarato_auth);
	if (!$idQueBarato) {
		Logger::logerror("\nXML($arquivo). Anuncio nao atualizado veja o log");
		die("Anuncio nao atualizado veja o log<br/>");
	}
	Logger::loginfo("Anuncio atualizado no QueBarato: AnuncioID: $idQueBarato, Titulo: $titulo. ");
}

if ($idQueBarato) {
	$imagecount = count($imagefile);
	$maxiamge = (int)SysProperties::getPropertyValue("carga.upload.maximage");
	if ($imagecount > $maxiamge) { $imagecount = $maxiamge; }
	for ($i=0;$i<$imagecount;$i++) {
		$image_name = (string)$imagefile[$i];
		$file_down = FileIO::DownloadImage($image_name);
		if (!$file_down) {
			Logger::logwarn("\nXML($arquivo). Imagem não adicionada AnuncioID: $idQueBarato, Titulo: $titulo. Imagem: $image_name");
		} else {
			$QueBaratoAPI->AddImage($idQueBarato, $file_down, $Quebarato_auth);
			Logger::loginfo("Imagem adicionada com sucesso: AnuncioID: $idQueBarato, Titulo: $titulo. Imagem: $image_name");
		}
		unlink($file_down);
	}
}
exit(0);
?>