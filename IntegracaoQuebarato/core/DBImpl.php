<?php
class DBImpl {    
    static function db_connect($host, $usr, $pwd, $databasename) {
        $db_dialect = SysProperties::getPropertyValue('bancodados.drive');
        $return = null;        
        switch ($db_dialect) {
            case 'mysql': {
                $return = mysql_connect($host, $usr, $pwd);
                if (!$return) {
                    Logger::logerror(mysql_error());
                    die(mysql_error());
                }
                mysql_select_db($databasename, $return);
                break;
            }
            case 'mysqli': {
                $return = mysqli_connect($host, $usr, $pwd, $databasename);
                if (!$return) {
                    Logger::logerror(mysqli_error($return));
                    die(mysqli_error($return));
                }
                break;
            }        
            case 'mssql': {
                $return = mssql_connect($host, $usr, $pwd);
                if (!$return) {
                    Logger::logerror(mssql_get_last_message());
                    die(mssql_get_last_message());
                }
                mssql_select_db($databasename);
                
                break;
            }       
            case 'sqlsrv': {
                $return = sqlsrv_connect($host, array('UID' => $usr, 'PWD' => $pwd, 'Database' => $databasename));
                if (!$return) {
                    $sql_errors = sqlsrv_errors();
                    Logger::logerror($sql_errors[0]["message"]);
                    die($sql_errors[0]["message"]);
                }
                break;
            }
        }
        return $return;
    }
    
    static function db_query($query, $conn) {
        $db_dialect = SysProperties::getPropertyValue('bancodados.drive');
        $return = null;        
        switch ($db_dialect) {
            case 'mysql': {
                $return = mysql_query($query, $conn);
                if (!$return) {
                    Logger::logerror(mysql_error());
                    die(mysql_error());
                }
                break;
            }
            case 'mysqli': {
                $return = mysqli_query($conn, $query);
                if (!$return) {
                    Logger::logerror(mysqli_error($return));
                    die(mysqli_error($return));
                }
                break;
            }        
            case 'mssql': {
                $return = mssql_query($query, $conn);
                if (!$return) {
                    Logger::logerror(mssql_get_last_message());
                    die(mssql_get_last_message());
                }
                break;
            }       
            case 'sqlsrv': {
                $return = sqlsrv_query($conn, $query);
                if (!$return) {
                    $sql_errors = sqlsrv_errors();
                    Logger::logerror($sql_errors[0]["message"]);
                    die($sql_errors[0]["message"]);
                }
                break;
            }
        }
        return $return;
    }
    
    static function db_fetch_array($result) {
        $db_dialect = SysProperties::getPropertyValue('bancodados.drive');
        $return = null;        
        switch ($db_dialect) {
            case 'mysql': {
                $return = mysql_fetch_array($result);                
                break;
            }
            case 'mysqli': {
                $return = mysqli_fetch_array($result);
                break;
            }        
            case 'mssql': {
                $return = mssql_fetch_array($result);
                break;
            }       
            case 'sqlsrv': {
                $return = sqlsrv_fetch_array($result);
                break;
            }
        }
        return $return;
    }
    
    static function db_fetch_assoc($result) {
        $db_dialect = SysProperties::getPropertyValue('bancodados.drive');
        $return = null;        
        switch ($db_dialect) {
            case 'mysql': {
                $return = mysql_fetch_assoc($result);
                break;
            }
            case 'mysqli': {
                $return = mysqli_fetch_assoc($result);
                break;
            }        
            case 'mssql': {
                $return = mssql_fetch_assoc($result);
                break;
            }       
            case 'sqlsrv': {
                $return = false;
                break;
            }
        }
        return $return;
    }
    
    static function db_close($conn) {
       $db_dialect = SysProperties::getPropertyValue('bancodados.drive');
       switch ($db_dialect) {
            case 'mysql': {
                mysql_close($conn);
                break;
            }
            case 'mysqli': {
                mysqli_close($conn);
                break;
            }        
            case 'mssql': {
                mssql_close($conn);
                break;
            }       
            case 'sqlsrv': {
                sqlsrv_close($conn);
                break;
            }
        }
    }
    
    static function db_num_rows($result) {
        $db_dialect = SysProperties::getPropertyValue('bancodados.drive');
        $return = null;        
        switch ($db_dialect) {
            case 'mysql': {
                $return = mysql_num_rows($result);
                break;
            }
            case 'mysqli': {
                $return = mysqli_num_rows($result);
                break;
            }        
            case 'mssql': {
                $return = mssql_num_rows($result);
                break;
            }       
            case 'sqlsrv': {
                $return = sqlsrv_num_rows($result);
                break;
            }
        }
        return $return;
    }
    
    static function db_execute($query, $conn) {
        $db_dialect = SysProperties::getPropertyValue('bancodados.drive');
        $return = null;        
        switch ($db_dialect) {
            case 'mysql': {
                $return = mysql_query($query, $conn);
                if (!$return) {
                    Logger::logerror(mysql_error());
                    die(mysql_error());
                }
                break;
            }
            case 'mysqli': {
                $return = mysqli_query($conn, $query);
                if (!$return) {
                    Logger::logerror(mysqli_error($return));
                    die(mysqli_error($return));
                }
                break;
            }        
            case 'mssql': {
                $return = mssql_execute($query, $conn);
                if (!$return) {
                    Logger::logerror(mssql_get_last_message());
                    die(mssql_get_last_message());
                }
                break;
            }       
            case 'sqlsrv': {
                $return = sqlsrv_execute($conn, $query);
                if (!$return) {
                    $sql_errors = sqlsrv_errors();
                    Logger::logerror($sql_errors[0]["message"]);
                    die($sql_errors[0]["message"]);
                }
                break;
            }
        }
        return $return;
    } 
}

?>
