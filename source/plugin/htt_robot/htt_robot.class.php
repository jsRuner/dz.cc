<?php
/**
 *	[qsbkwwf(qsbkwwf.{modulename})] (C)2016-2099 Powered by ���ĸ�.
 *	Version: 1.0
 *	Date: 2016-4-2 15:35
 *  Descript: ��ʱ����ִ������ƻ����ɼ�Ц����������̳��
 *  ������Ҫ���ð��id��
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
class plugin_htt_robot{
    //TODO - Insert your code here



    function global_footer(){
        include_once template('htt_robot:robot');
        return $robot_html;

    }
}

/*
class plugin_htt_robot_forum extends plugin_htt_robot
{
    function forumdisplay_top_output()
    {
        global $_G;

        include_once template('htt_robot:robot');
        return $robot_html;
    }
}*/


?>