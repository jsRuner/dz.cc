<?php
/**
 *	[qsbkwwf(qsbkwwf.{modulename})] (C)2016-2099 Powered by ���ĸ�.
 *	Version: 1.0
 *	Date: 2016-4-2 15:35
 *  Descript:������
 */
//
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
        $check = $var['htt_robot']['is_show'];  //1�ɼ� 2���ɼ���
        $other_people = $var['htt_robot']['other_people'];  //1�ɼ� 2���ɼ���
//        $_G['welcome_msg'] = $welcome_msg;

        //�жϵ�ǰ���ʵ��û���Ͱ�顣�ο͵���ʾ��
        $gids  = array_filter(unserialize($groupstr));
       // $_G['groupid']; //��ǰ�û���id
        //����û������Ҫ��ͬʱҲ���������á�����Կ��������ˡ����򲻿��ԡ�
        //1��ʾ������2 ���ػ����� .
        //�ֻ���û�в��ԡ����ػ����ˡ�
        //δע�����û��groupid�������ж����δ�գ���˵�����ο͡�

        //�жϴ���
        /*
         * 1���Ƿ��ֻ��ˡ����򷵻ؿ�
         * 2���Ƿ�����ʾ  �����򷵻ؿ�
         * 3��·���Ƿ�ɼ��� �ǡ��򷵻�  �����ˡ������Ĳ����жϡ�
         * 4��·�˲��ɼ���������жϡ��û��顣
         * 5���������û��顣���ؿա�
         * 6����� �������ݡ�
         *
         * */
//        include_once template('htt_robot:robot');
//        return $robot_html;

        if(checkmobile()){
            return '';
        }

        //ע��������ȡ��
        if(!($check == '1') ){
            return '';
        }


        if($other_people == 1){
            include_once template('htt_robot:robot');
            return $robot_html;
        }

        if(!in_array($_G['groupid'],$gids)){
            return '';
        }

        include_once template('htt_robot:robot');
        return $robot_html;

    }
}







?>