<?php
/**
 *    [qsbkwwf(qsbkwwf.cron_qsbkwwf)] (C)2016-2099 Powered by ���ĸ�.
 *    Version: 1.0
 *    Date: 2016-4-2 16:24
 *    Warning: Don't delete this comment
 *
 *    cronname:���²ɼ�
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


if ($fidstr == '0' || $uidstr == '0') {
    //����ʾ������Ϣ��
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_fid_uid'), '', 'error');
}


if ($threads<0 || $threads>20) {
    //����ʾ������Ϣ��
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_threads'), '', 'error');
}

//���Ϊ0.��ִ�к���Ĳ��������ɼ���
if($threads == 0){
    return;
}


$fids = explode(',',$fidstr);
$uids = explode(',',$uidstr);




$charset_num = $var['htt_qsbk']['charset'];  // 1��ʾutf-8 2��ʾgbk





function curl_qsbk()
{
    $urls = array(
        'hot' => "http://www.qiushibaike.com/text/",
        'new' => "http://www.qiushibaike.com/textnew/",
        'old' => "http://www.qiushibaike.com/history/",
        '24h' => "http://www.qiushibaike.com/hot/",
        '8h' => "http://www.qiushibaike.com/",
    );
    #�����������ȡһ��
    $rand_keys = array_rand($urls, 1);
//    $html = dfsockopen($urls[$rand_keys],$ip="");
//    $html = dfsockopen('https://www.baidu.com/');
//    echo $html;
//    exit();

    $curl = curl_init(); //����curl
    $header[] = "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0";
    $header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
    $header[] = "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Upgrade-Insecure-Requests:1";
    $header[] = "Connection: keep-alive";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_URL, $urls[$rand_keys]); //���������ַ
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //�Ƿ���� 1 or true �ǲ���� 0  or false���
    $html = curl_exec($curl); //ִ��curl����
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
#��ȡ�����б��������Ǹ���
$articles = pq(".article");


$count = 1;

foreach ($articles as $article) {

    //����������������˳�ѭ����
    if($count > $threads){
        break;
    }
    $count = $count+1;

    $data = array();
    $data['content'] = pq($article)->find(".content")->text();


//���ѡ��һ�������û���
    $fid_key = array_rand($fids,1);
    $uid_key = array_rand($uids,1);

    $fid = $fids[$fid_key];
    $uid = $uids[$uid_key];

    $forum = C::t('forum_forum')->fetch_info_by_fid($fid);


    $userinfo =C::t('common_member')->fetch($uid);


    $author = $userinfo['username'];

//    $author = 'admin';
//    $uid = 1;

    //ת�����롣�������utf-8������Ҫת����Ĭ��Ϊutf-8
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
    //��������
    $tid = C::t('forum_thread')->insert($newthread, true);
    //���Ϊ�����⡣
    C::t('forum_newthread')->insert(array(
        'tid' => $tid,
        'fid' => $fid,
        'dateline' => $publishdate,
    ));

    useractionlog($uid, 'tid');
    //����post�������ִ��2�������
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
        'invisible' => '0', //�Ƿ�ͨ�����
        'anonymous' => '0', //�Ƿ�����
        'usesig' => '1', //�Ƿ�����ǩ��
        'htmlon' => '0', //�Ƿ�����HTM
        'bbcodeoff' => '-1', //�Ƿ�����BBCODE
        'smileyoff' => '-1', //�Ƿ�رձ���
        'parseurloff' => '0', //�Ƿ�����ճ��URL
        'attachment' => '0',//����
        'tags' => '0',//�����ֶΣ����ڴ��tag
        'replycredit' => '0',//������û��ּ�¼
        'status' => '0'//����״̬
    ));

    $subject = str_replace("\t", ' ', $subject);
    $lastpost = "$tid\t" . $subject . "\t" . TIMESTAMP . "\t$author";
    C::t('forum_forum')->update($fid, array('lastpost' => $lastpost));
    C::t('forum_forum')->update_forum_counter($fid, 1, 1, 1);
    //�������̳������Ҫ�����ϼ���
    if ($forum['type'] == 'sub') {
        C::t('forum_forum')->update($forum['fup'], array('lastpost' => $lastpost));
    }
}
?>