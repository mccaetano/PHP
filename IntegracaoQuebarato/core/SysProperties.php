<?php

class SysProperties {
    
    public static function getPropertyValue($propertyname) {
        $fileini = dirname($_SERVER['SCRIPT_FILENAME']) . "/sys.properties";               
        $inifile = parse_ini_file($fileini, FALSE);        
        return $inifile[$propertyname];
    }
}

?>
