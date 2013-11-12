<?php

class QueBarato_DBControle {
    
    function GetAnuncioExiste($chave, $cnpj) {
        $db = DatabaseFactory::getConnection();        
        if (!$db) {       
            $error = error_get_last();                
            Logger::logerror($error["message"]);
            return FALSE;
        }   
        $query = "SELECT id_quebarato FROM wtb_integracao_quebarato AS t WHERE (id_veiculo = $chave) and (cnpj = '$cnpj'";
        $stmt = DBImpl::db_query($query, $db);
        if (!$stmt) {       
            $error = error_get_last();                        
            Logger::logerror($error["message"]);
            return FALSE;
        }   
        
        if (DBImpl::db_num_rows($stmt) <= 0) { return false; }
        
        $resultdata = false;
        while ($row = mssql_fetch_array($stmt)) {
            $resultdata = $row['id_quebarato'];
        }
        mssql_close($db);
        
        return $resultdata;
    }
    function SetAnuncio($veiculoID, $anuncioID, $cnpj) {
        $db = DatabaseFactory::getConnection();
        
        $query = "insert into wtb_integracao_quebarato (id_veiculo, id_quebarato, cnpj) values ($veiculoID, $anuncioID, '$cnpj')"; 
        $stmt = DBImpl::db_query($query, $db);
        if (!$stmt) {
            $error = error_get_last();
            Logger::logerror($error["message"]);
            return FALSE;
        }   
        DBImpl::db_close($db);
    }
}
?>
