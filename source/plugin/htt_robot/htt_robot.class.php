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
        global $_G;
        //��ȡ����Ĳ�����
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $groupstr = $var['htt_robot']['groups']; //�û��顣��Щ�û�����Կ��������ˡ�
        $robot_name = $var['htt_robot']['robot_name']; //�����˵�����
        $welcome_msg = $var['htt_robot']['welcome_msg']; //��ӭ��
        $tuling_key= $var['htt_robot']['tuling_key']; //key
        $check = $var['htt_robot']['is_show'];  //1���� 2���á�
//        $_G['welcome_msg'] = $welcome_msg;

        //�жϵ�ǰ���ʵ��û���Ͱ�顣�ο͵���ʾ��
        $gids  = array_filter(unserialize($groupstr));
       // $_G['groupid']; //��ǰ�û���id
        //����û������Ҫ��ͬʱҲ���������á�����Կ��������ˡ����򲻿��ԡ�
        //1��ʾ������2 ���ػ�����
        if($check == '1' && in_array($_G['groupid'],$gids)){
            include_once template('htt_robot:robot');
            return $robot_html;
        }else{
            return ;
        }






    }
}


?>