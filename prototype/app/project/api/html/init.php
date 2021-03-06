<?php
/**
 * @author Justin.Chen <cxd032404@hotmail.com>
 *
 * $Id: init.php 15195 2014-07-23 07:18:26Z 334746 $
 */

//error_reporting(E_ALL);

define('__APP_ROOT_DIR__', dirname(dirname(__FILE__)) . '/');
define('__APP_VAR_DIR__',__APP_ROOT_DIR__ . 'var/');

define('__APP_TPL_DIR__',__APP_ROOT_DIR__ . '/tpl/');

@set_include_path(dirname(dirname(dirname(__APP_ROOT_DIR__))) . '/lib' . PATH_SEPARATOR
 . dirname(dirname(__APP_ROOT_DIR__)) . '/model' . PATH_SEPARATOR
 . dirname(dirname(__FILE__)) . '/controller' . PATH_SEPARATOR
);

require_once 'Base/Common.php';

Base_Common::init(array(
    'timezone' => 'Asia/Shanghai',
    'root_dir' => __APP_ROOT_DIR__,
    'tpl_dir' => __APP_TPL_DIR__,
    'var_dir' => __APP_VAR_DIR__,
    'file_dir' => __APP_ROOT_DIR__ . 'html/upload/',
    'file_url' => '/upload',
    'exception' => true,
    'config_file' => __APP_ROOT_DIR__ . '/etc/config.php',
    'database_file' => __APP_ROOT_DIR__ . '/etc/database.php',
    'table_file' => __APP_ROOT_DIR__ . '/etc/table.php',
));

//注册当前时间
Base_Registry::set('timestamp', time());
