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


$fids = explode(',',$fidstr);
$uids = explode(',',$uidstr);




$charset_num = $var['htt_qsbk']['charset'];  // 1表示utf-8 2表示gbk

/*
$forum = C::t('forum_forum')->fetch_info_by_fid($fid);


if ($fid <= 0 || empty($forum) || $forum['status'] != 1) {
    //则显示错误信息。
    cpmsg(lang('plugin/htt_qsbk', 'error_forum'), '', 'error');
    return;
}*/



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



foreach ($articles as $article) {
    $data = array();
    $data['content'] = pq($article)->find(".content")->text();


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


    $message = $data['content'];

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
        'displayorder' => 0,
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
        'invisible' => '0', //是否通过审核
        'anonymous' => '0', //是否匿名
        'usesig' => '1', //是否启用签名
        'htmlon' => '0', //是否允许HTM
        'bbcodeoff' => '-1', //是否允许BBCODE
        'smileyoff' => '-1', //是否关闭表情
        'parseurloff' => '0', //是否允许粘贴URL
        'attachment' => '0',//附件
        'tags' => '0',//新增字段，用于存放tag
        'replycredit' => '0',//回帖获得积分记录
        'status' => '0'//帖子状态
    ));

    $subject = str_replace("\t", ' ', $subject);
    $lastpost = "$tid\t" . $subject . "\t" . TIMESTAMP . "\t$author";
    C::t('forum_forum')->update($fid, array('lastpost' => $lastpost));
    C::t('forum_forum')->update_forum_counter($fid, 1, 1, 1);
    //如果子论坛，还需要更新上级。
    if ($forum['type'] == 'sub') {
        C::t('forum_forum')->update($forum['fup'], array('lastpost' => $lastpost));
    }
}
?>