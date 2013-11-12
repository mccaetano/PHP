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
require_once 'QueBarato_GeraListaArquivos.php';

$xmlload = new xmlLoad();
$QueBaratoAPI = new QueBarato_API();
$dbData = new QueBarato_DBControle();
$tempoexpera = SysProperties::getPropertyValue("carga.tempo.interacao");
$resumo["Mensagem"] = "Total de registro processados para o arquivo xml";

Logger::loginfo("Iniciando rotina.");

$geraLista = new QueBarato_GeraListaArquivos();

foreach ($geraLista->ListaArquivos() as $arquivo) {	
	
    Logger::loginfo("lendo XML: $arquivo");
    $xmldata = $xmlload->loadXML($arquivo);
    $resumo["xml"] = $arquivo;
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
            continue;
        }
        
        if (($xmldata->UsuarioQueBarato == "") || ($xmldata->SenhaQueBarato == "")) {           
            Logger::logerror("Logon do Quebarato nao informado no XML:$arquivo");
            echo "Logon do Quebarato nao informado no XML:$arquivo<br/>";
            continue;
        }
        
        $userLoginDecoded = (string)base64_decode((string)$xmldata->UsuarioQueBarato);
        $userPwdDecoded = (string)base64_decode((string)$xmldata->SenhaQueBarato);
        $Quebarato_auth = $QueBaratoAPI->GetChaveUsuario(
        		$userLoginDecoded, 
                $userPwdDecoded);
        $userData = $QueBaratoAPI->GetUser($userLoginDecoded);
        if (!$userData) {
        	Logger::loginfo("Usuario ($userLoginDecoded) no XML nao encontrado no cadastro do Quebarato.");
        	echo "Usuario ($userLoginDecoded) no XML nao encontrado no cadastro do Quebarato.<br/>";
        	continue;        	
        }        
        $user = (string) $userData->id;
        $resumo["data_inicio"] = date("Y-m-D H:i:s");
        foreach ($xmldata->concessionaria as $concessionaria) {
        	$resumo["consecionaria"] = (int)$resumo["consecionaria"] + 1;
            foreach ($concessionaria->veiculo as $veiculo) {
            	SessionCtlr::StartLongSession();
            	
            	if ((int)$resumo["veiculo"] > 42) { 
            		Logger::logerror("Número total(43) de registro maior que possivel de processamento, por favor revisar o XML($arquivo)");
            		break; 
            	}
            	
            	
            	$resumo["veiculo"] = (int)$resumo["veiculo"] + 1;
                Logger::loginfo("Lendo veiculo:" . (string) $veiculo->idveiculo . " placa: " . (string) $veiculo->placa . " cnpj: " . (string)$veiculo->cnpj_con);
                
                if ((double)$veiculo->preco <= 0.0) {
                   Logger::logerror("Veiculo ID ($veiculo->idveiculo), placa($veiculo->placa) esta sem o preço, por favor revisar o XML($arquivo)");
                   continue; 
                }
                
                if (trim(strtoupper((string) $veiculo->tipo)) != "USADO" && trim(strtoupper((string) $veiculo->tipo)) != "NOVO") {
                	Logger::logerror("Veiculo ID ($veiculo->idveiculo), placa($veiculo->placa) esta com tipo diferente de Novo/Usado, por favor revisar o XML($arquivo)");
                	continue;
                }
				                
                if (trim(strtoupper((string) $veiculo->cep)) != "") {
                	Logger::logerror("Veiculo ID ($veiculo->idveiculo), placa($veiculo->placa) CEP em branco, por favor revisar o XML($arquivo)");
                	continue;
                }
                
                $idQueBarato = trim((string) $veiculo->idQueBarato);                
                if ($idQueBarato == '' || $idQueBarato == '0') {
                	$idQueBarato = FALSE;
                }
                
                if ($idQueBarato) {
                    Logger::loginfo("Anuncio encontrado para o VeiculoID: $veiculo->idveiculo, AnuncioID: $idQueBarato.");
                }
                
                #Montandno anuncio no QueBarato caso haja cadastro no Banco de dados.
                $condpagamento = SysProperties::getPropertyValue("quebaratoapi.condpagamento");
                $categoria = SysProperties::getPropertyValue("quebaratoapi.categoria");
                
                if (!$idQueBarato) {
                	$AnuncioPub['title'] = trim((string) $veiculo->titulo);
                    $AnuncioPub_description = trim((string) $veiculo->descricao);
                    $AnuncioPub['description'] = $AnuncioPub_description;
                    $AnuncioPub['price'] = array(
                        'currency'=> 'BRL',
                        'value' => (double) $veiculo->preco
                    );
                    $AnuncioPub['condition'] = (string) $veiculo->tipo;
                    $AnuncioPub['locale'] = array(
                        "zip" => (string) (preg_replace('/[^a-z\d ]/i', '', (string) $concessionaria->cep))
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
                        'available' => 1
                    );
                                     
                    $idQueBarato = $QueBaratoAPI->AddAnuncio($AnuncioPub, $Quebarato_auth);
                    if (!$idQueBarato) {
                        Logger::logerror("Anuncio nao criando veja o log");
                        echo "Anuncio nao criando veja o log<br/>";
                        continue;
                    }
                    $dbData->SetAnuncio($veiculo->idveiculo, $idQueBarato, (string)$veiculo->cnpj_con);
                    Logger::loginfo("Anuncio criado no QueBarato: AnuncioID: $idQueBarato, Tiulo: $veiculo->titulo. ");
                    $resumo["veiculo_novo"] = (int)$resumo["veiculo_novo"] + 1;
                     
                } else {
                	$AnuncioPub['id'] = (string)$idQueBarato;
                    $AnuncioPub['title'] = trim((string) $veiculo->titulo);
                    $AnuncioPub_description = trim((string) $veiculo->descricao);
                    $AnuncioPub['description'] = $AnuncioPub_description;
                    $AnuncioPub['price'] = array(
                        'currency'=> 'BRL',
                        'value' => (double) $veiculo->preco
                    );
                    $AnuncioPub['stock'] = array(
                        'available' => 1
                    );
                    
                    $QueBaratoAPI->SetAnuncio($AnuncioPub, $Quebarato_auth);
                    if (!$idQueBarato) {
                        Logger::logerror("Anuncio nao atualizado veja o log");
                        echo "Anuncio nao atualizado veja o log<br/>";
                        continue;
                    }
                    Logger::loginfo("Anuncio atualizado no QueBarato: AnuncioID: $idQueBarato, Titulo: $veiculo->titulo. ");
                    $resumo["veiculo_alterado"] = (int)$resumo["veiculo_alterado"] + 1;                    
                }
                                
                $imagefile = $veiculo->fotos->foto;
                $imagecount = count($imagefile);
                $maxiamge = (int)SysProperties::getPropertyValue("carga.upload.maximage");
                if ($imagecount > $maxiamge) { $imagecount = $maxiamge; }
                for ($i = 0;$i < $imagecount; $i++) {  
                	$image_name = (string)$imagefile[$i];              	
                	$file_down = FileIO::DownloadImage($image_name);                	
                	if (!$file_down) {
                		Logger::logwarn("Imagem não adicionada AnuncioID: $idQueBarato, Titulo: $veiculo->titulo. Imagem: $image_name");                		
                	} else {
                		$QueBaratoAPI->AddImage($idQueBarato, $file_down, $Quebarato_auth);
                		Logger::loginfo("Imagem adicionada com sucesso: AnuncioID: $idQueBarato, Titulo: $veiculo->titulo. Imagem: $image_name");
                	}
                	unlink($file_down);
                }                
            }            
        }
        $resumo["data_final"] = date("Y-m-D H:i:s");
        $resumo_msg = "Resumo de execução\n";
        $resumo_msg = "Arquivo: " . $arquivo . "\n";
        $resumo_msg .= "Inicio: " . $resumo["data_inicio"] . "\n";
        $resumo_msg .= "Final: " . $resumo["data_final"] . "\n";
        $resumo_msg .= "Total de Concessionarias: " . $resumo["consecionaria"] . "\n";
        $resumo_msg .= "Total de Veiculos: " . $resumo["veiculo"] . "\n";
        $resumo_msg .= "Total de Veiculos Adicionados: " . $resumo["veiculo_novo"] . "\n";
        $resumo_msg .= "Total de Veiculos Atualizados: " . $resumo["veiculo_alterado"] . "\n";
        Logger::SendMail($resumo_msg);
        #sleep($tempoexpera);
    }
    $geraLista->BackupArquivo($arquivo);
}
Logger::loginfo("Finalizand Rotina de Carga com sucesso");
exit(0);
?>