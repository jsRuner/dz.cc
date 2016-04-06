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
    cpmsg(lang('plugin/htt_qsbk', 'error_curl'), '', 'error');
}

//安装计划脚本。默认每天的5点30执行采集。
if(file_exists(DISCUZ_ROOT . './source/plugin/htt_baoman/cron/cron_htt_baoman.php')) {
    $data = array(
        'available' => 1,
        'type' => 'plugin',
        'name' => $installlang['info_cronname'],
        'filename' => 'htt_baoman:cron_htt_baoman.php',
        'weekday' => -1,
        'day' => -1,
        'hour' => 5,
        'minute' => 30,
    );

    //如果存在。则更新，否则插入
    $cronid = C::t('common_cron')->get_cronid_by_filename('htt_baoman:cron_htt_baoman.php');
    if($cronid){
        C::t('common_cron')->update($cronid,$data);
    }else{
        C::t('common_cron')->insert($data, true, false, false);
    }

}



