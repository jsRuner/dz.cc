<?php
/**
 *    [qsbkwwf(qsbkwwf.cron_qsbkwwf)] (C)2016-2099 Powered by 吴文付.
 *    Version: 1.0
 *    Date: 2016-4-2 16:24
 *    Warning: Don't delete this comment
 *
 *    cronname:糗事采集
 *    week:
 *    day:1
 *    hour:
 *    minute:
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
loadcache('plugin');

$var = $_G['cache']['plugin'];
$fidstr = $var['htt_qsbk']['fid'];
$uidstr = $var['htt_qsbk']['uid'];
$threads = $var['htt_qsbk']['threads'];
$caiji_img = $var['htt_qsbk']['caiji_img']; //1表示不采集带图片的糗事 2表示采集
$check = $var['htt_qsbk']['check'];  //1表示不审核 2表示审核。

//echo $caiji_img; //2

//echo $check; //1




if ($fidstr == '0' || $uidstr == '0') {
    //则显示错误信息。
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_fid_uid'), '', 'error');
}


if ($threads<0 || $threads>20) {
    //则显示错误信息。
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_threads'), '', 'error');
}

//如果为0.则不执行后面的操作。不采集。
if($threads == 0){
    return;
}


$fids = explode(',',$fidstr);
$uids = explode(',',$uidstr);




$charset_num = $var['htt_qsbk']['charset'];  // 1表示utf-8 2表示gbk





function curl_qsbk()
{
    $urls = array(
        'hot' => "http://www.qiushibaike.com/text/",
        'new' => "http://www.qiushibaike.com/textnew/",
        'old' => "http://www.qiushibaike.com/history/",
        '24h' => "http://www.qiushibaike.com/hot/",
        '8h' => "http://www.qiushibaike.com/",
    );
    #从数组中随机取一个
    $rand_keys = array_rand($urls, 1);
//    $html = dfsockopen($urls[$rand_keys],$ip="");
//    $html = dfsockopen('https://www.baidu.com/');
//    echo $html;
//    exit();

    $curl = curl_init(); //开启curl
    $header[] = "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0";
    $header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
    $header[] = "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Upgrade-Insecure-Requests:1";
    $header[] = "Connection: keep-alive";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//    curl_setopt($curl, CURLOPT_URL,'http://www.qiushibaike.com/pic/'); //设置请求地址
    curl_setopt($curl, CURLOPT_URL, $urls[$rand_keys]); //设置请求地址
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
    $html = curl_exec($curl); //执行curl操作
    curl_close($curl);

    if (empty($html)) {
        return curl_qsbk();
    }
    return $html;
}




if(function_exists('curl_init') && function_exists('curl_exec')) {

    $html = curl_qsbk();
}else{
    cpmsg(lang('plugin/htt_qsbk', 'error_curl'), '', 'error');
}


include_once DISCUZ_ROOT . './source/plugin/htt_qsbk/include/phpQuery/phpQuery.php';
phpquery::newDocumentHTML($html, 'utf-8');
#获取段子列表。最外面那个。
$articles = pq(".article");


$count = 1;

foreach ($articles as $article) {

    //如果超过数量。则退出循环。
    if($count > $threads){
        break;
    }
    $count = $count+1;

    $data = array();
    $data['content'] = pq($article)->find(".content")->text();

    $data['img'] = pq($article)->find(".thumb a img")->attr('src');

    //如果图片存在。但插件设置不采集,值是1。则跳过本次。
    if(!empty($data['img']) && $caiji_img == '1'){
        continue;
    }

    //修改审核参数。-2
    if($check == '2'){
        $invisible = -2; //需要审核
        $displayorder = -2; //显示顺序
    }else{
        $invisible = 0; //无须审核。
        $displayorder = 0; //需要审核的帖子为-2
    }

//    echo $invisible;
//
//    echo $displayorder;
//
//    exit();


//随机选择一个版块和用户。
    $fid_key = array_rand($fids,1);
    $uid_key = array_rand($uids,1);

    $fid = $fids[$fid_key];
    $uid = $uids[$uid_key];

    $forum = C::t('forum_forum')->fetch_info_by_fid($fid);


    $userinfo =C::t('common_member')->fetch($uid);


    $author = $userinfo['username'];

//    $author = 'admin';
//    $uid = 1;

    //转换编码。如果不是utf-8。则需要转换。默认为utf-8
    if ($charset_num != 1) {
        $data['content'] = iconv("UTF-8", "gbk", $data['content']);
    }


    $subject = cutstr($data['content'], 22, '');
    $publishdate = time();

    if(!empty($data['img'])){
        $message = $data['content']."[img]".$data['img']."[/img]";
//        $bbcodeoff = '0'; //显示图片。
    }else{
        $message = $data['content'];
//        $bbcodeoff = '-1';
    }


    $newthread = array(
        'fid' => $fid,
        'posttableid' => 0,
        'readperm' => 0,
        'price' => 0,
        'typeid' => 0,
        'sortid' => 0,
        'author' => $author,
        'authorid' => $uid,
        'subject' => $subject,
        'dateline' => $publishdate,
        'lastpost' => $publishdate,
        'lastposter' => $author,
        'displayorder' => $displayorder,
        'digest' => 0,
        'special' => 0,
        'attachment' => 0,
        'moderated' => 0,
        'status' => 32,
        'isgroup' => 0,
        'replycredit' => 0,
        'closed' => 0
    );
    //插入主题
    $tid = C::t('forum_thread')->insert($newthread, true);


    if($check == '2'){

        //插入审核表。
        C::t('common_moderate')->insert('tid',array(
            'id'=>$tid,
            'status' => '0',
            'dateline' => $publishdate,
        ));
        manage_addnotify('verifythread');
    }

    //标记为新主题。
    C::t('forum_newthread')->insert(array(
        'tid' => $tid,
        'fid' => $fid,
        'dateline' => $publishdate,
    ));

    useractionlog($uid, 'tid');
    //插入post表。这里会执行2个表操作
    $pid = insertpost(array(
        'fid' => $fid,
        'tid' => $tid,
        'first' => '1',
        'author' => $author,
        'authorid' => $uid,
        'subject' => $subject,
        'dateline' => $publishdate,
        'message' => $message,
        'useip' => getglobal('clientip'),
        'port' => getglobal('remoteport'),
        'invisible' => $invisible, //是否通过审核
        'anonymous' => '0', //是否匿名
        'usesig' => '1', //是否启用签名
        'htmlon' => '0', //是否允许HTM
        'bbcodeoff' =>'0', //是否允许BBCODE
        'smileyoff' => '-1', //是否关闭表情
        'parseurloff' => '0', //是否允许粘贴URL
        'attachment' => '0',//附件
        'tags' => '0',//新增字段，用于存放tag
        'replycredit' => '0',//回帖获得积分记录
        'status' => '0'//帖子状态
    ));



    if($check == '2'){

        C::t('forum_forum')->update_forum_counter($fid, 0, 0, 1);
    }else{
        $subject = str_replace("\t", ' ', $subject);
        $lastpost = "$tid\t" . $subject . "\t" . TIMESTAMP . "\t$author";
        C::t('forum_forum')->update($fid, array('lastpost' => $lastpost));
        C::t('forum_forum')->update_forum_counter($fid, 1, 1, 1);

        //如果子论坛，还需要更新上级。
        if ($forum['type'] == 'sub') {
            C::t('forum_forum')->update($forum['fup'], array('lastpost' => $lastpost));
        }
    }

    C::t('forum_sofa')->insert(array('tid' => $tid,'fid' => $forum['fid']));



}
?>