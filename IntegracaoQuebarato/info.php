<?PHP
require_once 'core/SysProperties.php';
require_once 'core/Logger.php';
require_once 'core/FileIO.php';
require_once 'core/xmlLoad.php';
require_once 'core/DBImpl.php';
require_once 'core/BancoDadosFactory.php';
require_once 'core/SessionCtrl.php';
SessionCtlr::StartLongSession();
echo phpinfo();
SessionCtlr::FlushSession();
SessionCtlr::EndSession();

?>