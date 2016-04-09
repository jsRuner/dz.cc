<?php
/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: check.php 34236 2013-11-21 01:13:12Z nemohou $
 */


if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}



//检查插件需要的模块.如果不存在
if(!function_exists('curl_init') || !function_exists('curl_exec')) {
    cpmsg($installlang['error_curl'], '', 'error');
    exit();
}


