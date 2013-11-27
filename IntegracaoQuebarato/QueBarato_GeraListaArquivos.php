<?php
class QueBarato_GeraListaArquivos {
    private $tipo_Leitura;
    
    function __construct() {
        $this->tipo_Leitura = SysProperties::getPropertyValue("carga.tipo.origem");
    }
    
    function ListaArquivos() {        
        $listaarquivos = FALSE;
        if ($this->tipo_Leitura == "arquivo") {
            $listaarquivos[] = SysProperties::getPropertyValue("carga.arquivo.nome");
        } else {
            $dirin = SysProperties::getPropertyValue("carga.diretorio.leitura");
			
            $files = scandir($dirin);
            
            unset($listaarquivos);
            for ($i=0; $i<count($files); $i++) {
            	$file = $files[$i];
                if ($file != '.' && $file != '..' && $file != '') {
                    $listaarquivos[] = realpath($dirin . "/" . $file);
                }
            }
            
        }                
        return $listaarquivos;
    }
    
    function BackupArquivo($file) {
        $dirout = SysProperties::getPropertyValue("carga.diretorio.backup");
        $pathinto = pathinfo($file);
        $filename = $pathinto["filename"];
        $extension = $pathinto["extension"];
        $data=date("Ymd_His");
        if ($this->tipo_Leitura == "arquivo") {
            $handle = fopen($file, "r");
            $content = stream_get_contents($handle);
            fclose($handle);
            $handleout = fopen("$dirout/$filename". "_$data.$extension", "w+");
            fwrite($handleout, $content);
            fclose($handleout);
        } else {            
            copy($file , "$dirout/$filename" . "_$data.$extension");
            unlink($file);
        }
    }
    
}
?>
