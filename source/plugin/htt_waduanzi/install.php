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


if(function_exists('curl_init') && function_exists('curl_exec')) {
    $finish = True;
}else{
    cpmsg(lang('plugin/htt_qsbk', 'error_curl'), '', 'error');
}