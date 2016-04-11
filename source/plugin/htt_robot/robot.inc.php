<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 吴文付 hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/4/8
 * Time: 16:51
 * description:
 *
 *
 */
//http://dzgbk.cc/plugin.php?id=htt_robot:robot 访问该页面的url
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if($_POST['formhash']!= FORMHASH) {
    showmessage('undefined_action');
}


include_once DISCUZ_ROOT.'./source/plugin/htt_robot/function.inc.php';



//
//dump($_G);
//
//exit();

loadcache('plugin');
$var = $_G['cache']['plugin'];
$groupstr = $var['htt_robot']['groups']; //用户组。哪些用户组可以看到机器人。
$welcome_msg = $var['htt_robot']['welcome_msg']; //欢迎语
$tuling_key = $var['htt_robot']['tuling_key']; //key
$check = $var['htt_robot']['is_show'];  //1隐藏 2启用


$key = $tuling_key;

//$info = $_POST['msg'];
$info = $_POST['msg'];

$url = 'http://www.tuling123.com/openapi/api?key=' . $key . '&info=' . urlencode($info);

//优先使用自带的dz请求。
$replystr = dfsockopen($url);
if(empty($replystr)){
$replystr = curl_html($url);
	
}

$replyarr = json_decode($replystr, true);


//var_dump($replyarr);


echo json_encode(array('msg' => $replyarr['text']));
