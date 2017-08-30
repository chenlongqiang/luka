<?php
/**
 * Created by PhpStorm.
 * User: luka-chen
 * Date: 17/7/13
 * Time: 下午2:31
 */

include './vendor/autoload.php';

error_reporting(0);
register_shutdown_function('zyfshutdownfunc');
function zyfshutdownfunc()
{
    if ($error = error_get_last()) {
        var_dump('<b>register_shutdown_function: Type:' . $error['type'] . ' Msg: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'] . '</b>');
    }
}

set_error_handler('zyferror');
function zyferror($type, $message, $file, $line)
{
    var_dump('<b>set_error_handler: ' . $type . ':' . $message . ' in ' . $file . ' on ' . $line . ' line .</b><br />');
}
require 'a.php';
