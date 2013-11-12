<?php
class SessionCtlr {
	public static function StartLongSession() {
		
		if ( ! session_id() ) {
			if (!session_start()) {
				$error = error_get_last();
				Logger::logerror($error['message']);
			}
		}
		@header("Connection: Keep-Alive");
		@header("Keep-Alive: timeout=7200");
		@ini_set("default_socket_timeout", "7200");
		@ini_set("max_execution_time", "0");
		@ini_set("max_input_time", "7200");
		@ini_set("file_uploads", "1");
		@ini_set("upload_max_filesize", "150M");
		@ini_set("max_file_uploads", "1000");
		@ini_set("post_max_size", "150M");
		@ini_set("memory_limit", "1024M");
		@ini_set("session.gc_maxlifetime", "7200");
		@ini_set("session.cookie_lifetime", "0");
		@ini_set('session.gc_probability', 1);
		@ini_set('session.gc_divisor', "100");
		@ini_set("from", "contato@it-car.com.br");
		@ini_set("sendmail_from", "contato@it-car.com.br");
		@ini_set('safe_mode', 0);
		@ini_set("output_buffering", "Off");
		@ini_set('implicit_flush', 1);
		@ini_set('zlib.output_compression', 0);
		@ini_set('max_execution_time', 7200);
		@ini_set('date.timezone', 'America/Sao_Paulo');
		@ini_set('display_errors', TRUE);
		session_cache_expire(7200);
		session_cache_limiter(null);
		set_time_limit(7200);
		error_reporting(E_ALL);
		
		ignore_user_abort(TRUE);
	}
	
	public static function  FlushSession() {
		ob_flush();
		flush();
		sleep(1);
	}
	
	public static function EndSession() {		
		session_write_close();
	}
}
?>