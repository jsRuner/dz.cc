<?php
/**
 *    [qsbkwwf(qsbkwwf.cron_qsbkwwf)] (C)2016-2099 Powered by ���ĸ�.
 *    Version: 1.0
 *    Date: 2016-4-2 16:24
 *    Warning: Don't delete this comment
 *
 *
 *    cronname: info_cronname
 *    week: -1
 *    day:-1
 *    hour:5
 *    minute:30
 */





if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}


echo 22;
function strFilter($str){
    $str = str_replace('`', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('~', '', $str);
    $str = str_replace('!', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('@', '', $str);
    $str = str_replace('#', '', $str);
    $str = str_replace('$', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('%', '', $str);
    $str = str_replace('^', '', $str);
    $str = str_replace('����', '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('*', '', $str);
    $str = str_replace('(', '', $str);
    $str = str_replace(')', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('_', '', $str);
    $str = str_replace('����', '', $str);
    $str = str_replace('+', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('|', '', $str);
    $str = str_replace('\\', '', $str);
    $str = str_replace('[', '', $str);
    $str = str_replace(']', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace(':', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace(',', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('��', '', $str);
    $str = str_replace('?', '', $str);
    $str = str_replace('��', '', $str);
    return trim($str);
}



/**
 * �ж� �ļ�/Ŀ¼ �Ƿ��д��ȡ��ϵͳ�Դ��� is_writeable ������
 *
 * @param string $file �ļ�/Ŀ¼
 * @return boolean
 */
function new_is_writeable($file) {
    if (is_dir($file)){
        $dir = $file;
        if ($fp = @fopen("$dir/test.txt", 'w')) {
            @fclose($fp);
            @unlink("$dir/test.txt");
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    } else {
        if ($fp = @fopen($file, 'a+')) {
            @fclose($fp);
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }

    return $writeable;
}

function curl_qsbk($url)
{

    $curl = curl_init(); //����curl
    $header[] = "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0";
    $header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
    $header[] = "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Upgrade-Insecure-Requests:1";
    $header[] = "Connection: keep-alive";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//    curl_setopt($curl, CURLOPT_URL, $urls[$rand_keys]); //���������ַ
//    curl_setopt($curl, CURLOPT_URL,'http://www.qiushibaike.com/pic/'); //���������ַ
    curl_setopt($curl, CURLOPT_URL,$url); //���������ַ
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //�Ƿ���� 1 or true �ǲ���� 0  or false���
    $html = curl_exec($curl); //ִ��curl����
    curl_close($curl);

    if (empty($html)) {
        return curl_qsbk($url);
    }
    return $html;
}

loadcache('plugin');

$var = $_G['cache']['plugin'];

$fidstr = $var['htt_qsbk']['fids'];
$uidstr = $var['htt_qsbk']['uids'];
$groupstr = $var['htt_qsbk']['groups']; //�û���
$threads = $var['htt_qsbk']['threads'];
$charset_num = $var['htt_qsbk']['charset'];  // 1utf-8 2gbk
$caiji_model = $var['htt_qsbk']['caiji_model']; //1���� 2��ʾ��ͼ 3ͼ��
$imgpath = $var['htt_qsbk']['imgpath']; //Ŀ¼
$check = $var['htt_qsbk']['check'];  //1����� 2��ˡ�
$title_length = $var['htt_qsbk']['title_length']; //���ⳤ��

//����ɼ�����Ϊ0.��ִ�к���Ĳ��������ɼ���
if($threads == 0){
    return;
}
$fids =array_filter(unserialize($fidstr));
if ( is_null($fids) || empty($fids)) {
    //����ʾ������Ϣ��
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_fid'), '', 'error');
}

$uids = array_filter(explode(',',$uidstr));

$groups = array_filter(unserialize($groupstr));
$members_bygroup = C::t('common_member')->fetch_all_by_groupid($groups);//����Ļ�Ա����


if( empty($uidstr)){
    $uids = array();
    foreach($members_bygroup as $item){
        $uids[] = $item['uid'];
    }
}


if(empty($uids)){
//    return;
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_uid'), '', 'error');
}
/*
//���Ŀ¼���ڻ��߿�д���Ǵ���ģʽ��������·���ż�顣
if($caiji_model != 1 && !empty($imgpath) && !new_is_writeable($_G['setting']['attachurl'].'forum/'.$imgpath)){
    //�����Զ�����Ŀ¼�����ʧ�ܣ�������ʾ��
    //·��Ӧ���� htt_qsbk/
    $res=mkdir(iconv("UTF-8", "GBK", $_G['setting']['attachurl'].'forum/'.$imgpath),0777,true);
    if (!$res){
        return;
//        cpmsg(lang('plugin/htt_qsbk', 'error_setting_imgpath'), '', 'error');
    }
}*/



//����Ƿ񳬳���Χ��
if ($threads<0 || $threads>20) {
    //����ʾ������Ϣ��
//    return ;
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_threads'), '', 'error');
}

//����Դ��
$urls = array(
    'text_hot' => "http://www.qiushibaike.com/text/",
    'text_new' => "http://www.qiushibaike.com/textnew/",
    'pic_hot'=>"http://www.qiushibaike.com/imgrank/",
    'pic_new'=>"http://www.qiushibaike.com/pic/",
    '24h' => "http://www.qiushibaike.com/hot/",
    '8h' => "http://www.qiushibaike.com/",
);

switch($caiji_model){
    case 1:
        $urls = array(
            'text_hot' => "http://www.qiushibaike.com/text/",
            'text_new' => "http://www.qiushibaike.com/textnew/",
        );
        break;
    case 2:
        $urls = array(
            'pic_hot'=>"http://www.qiushibaike.com/imgrank/",
            'pic_new'=>"http://www.qiushibaike.com/pic/",
        );
        break;
    default:
        $urls = array(
            '24h' => "http://www.qiushibaike.com/hot/",
            '8h' => "http://www.qiushibaike.com/",
        );
        break;
}

#�����������ȡһ��
$rand_keys = array_rand($urls, 1);
$url = $urls[$rand_keys];

//��麯���Ƿ���á�
if(function_exists('curl_init') && function_exists('curl_exec')) {

    $html = curl_qsbk($url);
}else{

    cpmsg(lang('plugin/htt_qsbk', 'error_curl'), '', 'error');
}

echo 111;
echo $html;
exit();

//��������
include_once DISCUZ_ROOT . './source/plugin/htt_qsbk/include/phpQuery/phpQuery.php';
phpquery::newDocumentHTML($html, 'utf-8');
#��ȡ�����б��������Ǹ���
$articles = pq(".article");
$count = 1; //����
foreach ($articles as $article) {

    //����������������˳�ѭ����
    if($count > $threads){
        break;
    }
    $count = $count+1;

    $data = array();
    $data['content'] = pq($article)->find(".content")->text();
    $data['img'] = pq($article)->find(".thumb a img")->attr('src');

    //�����򲻻���ͼƬ�������ж�
    //��ͼ����Ҫ�жϡ�·�����⡣
    //ͼƬ����,�����ɼ���·��������������ء�������������
    $remote = 1; //Ĭ��Զ�̸���

    $imgpath = '201604/07/';

    if(!empty($data['img'])){
        if(!empty($imgpath)){
            $local_img = time().uniqid().'.png';
            $context = stream_context_create(array(
                'http' => array(
                    'timeout' => 30 //��ʱʱ�䣬��λΪ��
                )
            ));
            @file_put_contents(DISCUZ_ROOT.'./'.$_G['setting']['attachurl'].'forum/'.$imgpath.$local_img, file_get_contents($data['img'],0,$context));
            $data['img'] =$imgpath.$local_img;
            #�����·��Ҫ�� data/attament/forum/��ʼ��

            $remote = 0; //����ͼƬ���� 1��ʾԶ��ͼƬ��0��ʾ����ͼƬ��
        }

        $attachment = 2; //����,0�޸��� 1��ͨ���� 2��ͼƬ����

    }else{
        $attachment = 0; //����,0�޸��� 1��ͨ���� 2��ͼƬ����
    }

    //�޸���˲�����-2
    if($check == '2'){
        $invisible = -2; //��Ҫ���
        $displayorder = -2; //��ʾ˳��
    }else{
        $invisible = 0; //������ˡ�
        $displayorder = 0; //��Ҫ��˵�����Ϊ-2
    }

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


    //���Ʊ���ĳ��ȡ��������ݡ�ͬʱ���ݳ��ȳ�����󳤶ȡ����ȡ��
    if(!empty($data['content'] && strlen($data['content']) > $title_length )){

        $subject = cutstr($data['content'], $title_length, '');
    }else {
        $subject = $data['content'];
    }

    //����ȥ��һ�������ַ���.����������ҳ��
    $subject = strFilter($subject);



    $publishdate = time();

    if(!empty($data['img'])){
        $message = $data['content']."[img]".$_G['setting']['attachurl'].'forum/'.$data['img']."[/img]";
//        $bbcodeoff = '0'; //��ʾͼƬ��
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
        'attachment' => $attachment,
        'moderated' => 0,
        'status' => 32,
        'isgroup' => 0,
        'replycredit' => 0,
        'closed' => 0
    );
    //��������
    $tid = C::t('forum_thread')->insert($newthread, true);

    //���絽�����ͼƬ��
    //remote 0��ʾ���ء�


    C::t('forum_threadimage')->insert(array(
        'tid'=>$tid,
        'attachment'=>$data['img'],
        'remote'=>$remote,
    ),true);




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
        'invisible' => $invisible, //�Ƿ�ͨ�����
        'anonymous' => '0', //�Ƿ�����
        'usesig' => '1', //�Ƿ�����ǩ��
        'htmlon' => '0', //�Ƿ�����HTM
        'bbcodeoff' =>'0', //�Ƿ�����BBCODE
        'smileyoff' => '-1', //�Ƿ�رձ���
        'parseurloff' => '0', //�Ƿ�����ճ��URL
        'attachment' => '0',//����
        'tags' => '0',//�����ֶΣ����ڴ��tag
        'replycredit' => '0',//������û��ּ�¼
        'status' => '0'//����״̬
    ));

    if($check == '2'){
        updatemoderate('tid', $tid);
        C::t('forum_forum')->update_forum_counter($fid, 0, 0, 1);

        //������˱�
        C::t('common_moderate')->insert('tid',array(
            'id'=>$tid,
            'status' => '0',
            'dateline' => $publishdate,
        ));
        //֪ͨ��ˡ�
        manage_addnotify('verifythread');
        return ;
    }else{
        $subject = str_replace("\t", ' ', $subject);
        $lastpost = "$tid\t" . $subject . "\t" . TIMESTAMP . "\t$author";
        C::t('forum_forum')->update($fid, array('lastpost' => $lastpost));
        C::t('forum_forum')->update_forum_counter($fid, 1, 1, 1);

        //�������̳������Ҫ�����ϼ���
        if ($forum['type'] == 'sub') {
            C::t('forum_forum')->update($forum['fup'], array('lastpost' => $lastpost));
        }
    }
    //ɳ������
    C::t('forum_sofa')->insert(array('tid' => $tid,'fid' => $forum['fid']));
}
?>