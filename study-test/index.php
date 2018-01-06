<?php

function warn($arr)
{
    if (!is_array($arr) || empty($arr)) {
        trigger_error('input not array', E_USER_WARNING);
        return false;
    }
    echo 'OK';
    return true;
}

try {
    warn('test');
} catch (\Exception $e) {
    echo $e->getMessage();
    exit;
}
