<?php
class DatabaseFactory {
            
    static function getConnection() {
        $Host = SysProperties::getPropertyValue("bancodados.servidor");
        $User = SysProperties::getPropertyValue("bancodados.usuario");
        $Pass = SysProperties::getPropertyValue("bancodados.senha");
        $dbname = SysProperties::getPropertyValue("bancodados.basedados");

        return DBImpl::db_connect($Host, $User, $Pass, $dbname);
    }    
}
?>
