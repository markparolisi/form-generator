<?php
/*
*  Sets directory constants and registers autoloader
*  This file MUST be included in the index.php
*/

define('DS', DIRECTORY_SEPARATOR);
define('FORM_APP',  realpath(dirname(__FILE__)) . DS);
define('APP_DIR',   FORM_APP . 'app' . DS);
define('INI_DIR',   FORM_APP . 'ini' . DS);
define('TESTS_DIR', FORM_APP . 'tests' . DS);
define('JS_DIR', '/js' . DS);
define('ERROR_LOG_DIR', FORM_APP . 'error_logs' . DS);
define('SUCCESS_LOG_DIR', FORM_APP . 'form_logs' . DS);


require_once(APP_DIR . 'Form_Core.php');