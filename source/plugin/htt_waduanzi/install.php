<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 吴文付 hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/4/3
 * Time: 16:55
 * description:
 *
 *
 */


if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

//检查插件需要的模块
if(function_exists('curl_init') && function_exists('curl_exec')) {
    $finish = True;
}else{
    cpmsg(lang('plugin/htt_waduanzi', 'error_curl'), '', 'error');
}


