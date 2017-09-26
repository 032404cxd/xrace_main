<?php
/**
 * @author Chen <cxd032404@hotmail.com>
 * $Id: database.php 1362 2010-01-17 11:00:03Z 闄堟檽涓?$
 */
include dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))."/CommonConfig/databaseConfig.php";

$db = array();
$db['isPersistent'] = 0;

$db['rc_config'][0] = array(
	'host' => HOST_LOCAL,
	'user' => USER_LOCAL_RC,
	'password' => PASSWORD_LOCAL_RC,
	'port' => PORT_LOCAL,
	'database' => 'rc_config',
);

$db['xrace_user'][0] = array(
	'host' => HOST_LOCAL,
	'user' => USER_LOCAL,
	'password' => PASSWORD_LOCAL,
	'port' => PORT_LOCAL,
	'database' => 'xrace_user',
);
$db['xrace_race'][0] = array(
    'host' => HOST_LOCAL,
    'user' => USER_LOCAL_RC,
    'password' => PASSWORD_LOCAL_RC,
    'port' => PORT_LOCAL,
    'database' => 'xrace_race',
);
return $db;
?>
