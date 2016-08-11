<?php
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', 'C:'.DS.'wamp'.DS.'www'.DS.'chat'.DS);
defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.'includes' . DS);
defined('AJAX') ? null : define('AJAX', SITE_ROOT.'ajax' . DS);
defined('PUBLIC_DIR') ? null : define('PUBLIC_DIR', SITE_ROOT.'public' . DS);
defined('URL') ? null : define('URL', "http://localhost/chat/");
?>