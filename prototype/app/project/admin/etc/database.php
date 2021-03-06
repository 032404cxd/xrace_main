<?php
/**
 * @author Chen <cxd032404@hotmail.com>
 * $Id: database.php 1362 2010-01-17 11:00:03Z 闄堟檽涓?$
 */
include dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))."/CommonConfig/databaseConfig.php";

$db = array();
$db['isPersistent'] = 0;
$db['xrace'][0] = array(
	'host' => HOST_LOCAL,
	'user' => USER_LOCAL,
	'password' => PASSWORD_LOCAL,
	'port' => PORT_LOCAL,
	'database' => 'xrace',
);

$db['xrace_config'][0] = array(
	'host' => HOST_LOCAL,
	'user' => USER_LOCAL,
	'password' => PASSWORD_LOCAL,
	'port' => PORT_LOCAL,
	'database' => 'xrace_config',
);

$db['xrace_bm'][0] = array(
	'host' => HOST_LOCAL,
	'user' => USER_LOCAL,
	'password' => PASSWORD_LOCAL,
	'port' => PORT_LOCAL,
	'database' => 'xrace_bm',
);

$db['xrace_user'][0] = array(
	'host' => HOST_LOCAL,
	'user' => USER_LOCAL,
	'password' => PASSWORD_LOCAL,
	'port' => PORT_LOCAL,
	'database' => 'xrace_user',
);
$db['mylaps'][0] = array(
    'host' => HOST_LOCAL,
    'user' => USER_LOCAL,
    'password' => PASSWORD_LOCAL,
    'port' => PORT_LOCAL,
    'database' => 'mylaps',
);
$db['mylapstest'][0] = array(
    'host' => HOST_LOCAL,
    'user' => USER_LOCAL,
    'password' => PASSWORD_LOCAL,
    'port' => PORT_LOCAL,
    'database' => 'mylapstest',
);
$db['qcode'][0] = array(
    'host' => HOST_LOCAL,
    'user' => USER_LOCAL,
    'password' => PASSWORD_LOCAL,
    'port' => PORT_LOCAL,
    'database' => 'qcode',
);
return $db;
?>
