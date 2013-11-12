<?php
@header("Connection: Keep-Alive");
@header("Keep-Alive: timeout=900");
@ini_set('max_execution_time', 900);

var_dump(headers_list());
flush();
echo "inicio: " . date("d/m/Y H:i:s") . "<br/>\n";
flush();
for ($i=0;$i<1200;$i++) {
	sleep(1);
}
echo "<br/>\n";
echo "fim: " . date("d/m/Y H:i:s") . "<br/>\n";