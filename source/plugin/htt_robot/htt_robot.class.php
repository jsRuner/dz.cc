<?php
/**
 *	[qsbkwwf(qsbkwwf.{modulename})] (C)2016-2099 Powered by 吴文付.
 *	Version: 1.0
 *	Date: 2016-4-2 15:35
 *  Descript: 定时任务。执行任务计划。采集笑话。插入论坛。
 *  这里需要设置版块id。
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
class plugin_htt_robot{
    //TODO - Insert your code here



    function global_footer(){
        global $_G;
        //获取插件的参数。
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $groupstr = $var['htt_robot']['groups']; //用户组。哪些用户组可以看到机器人。
        $robot_name = $var['htt_robot']['robot_name']; //机器人的名字
        $welcome_msg = $var['htt_robot']['welcome_msg']; //欢迎语
        $tuling_key= $var['htt_robot']['tuling_key']; //key
        $check = $var['htt_robot']['is_show'];  //1隐藏 2启用。
//        $_G['welcome_msg'] = $welcome_msg;

        //判断当前访问的用户组和版块。游客的显示。
        $gids  = array_filter(unserialize($groupstr));
       // $_G['groupid']; //当前用户组id
        //如果用户组符合要求。同时也设置了启用。则可以看到机器人。否则不可以。
        //1显示机器人2 隐藏机器人
        if($check == '1' && in_array($_G['groupid'],$gids)){
            include_once template('htt_robot:robot');
            return $robot_html;
        }else{
            return ;
        }






    }
}


?>