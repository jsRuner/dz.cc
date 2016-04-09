<?php
/**
 *    [qsbkwwf(qsbkwwf.cron_qsbkwwf)] (C)2016-2099 Powered by 吴文付.
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
    $str = str_replace('·', '', $str);
    $str = str_replace('~', '', $str);
    $str = str_replace('!', '', $str);
    $str = str_replace('！', '', $str);
    $str = str_replace('@', '', $str);
    $str = str_replace('#', '', $str);
    $str = str_replace('$', '', $str);
    $str = str_replace('￥', '', $str);
    $str = str_replace('%', '', $str);
    $str = str_replace('^', '', $str);
    $str = str_replace('……', '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('*', '', $str);
    $str = str_replace('(', '', $str);
    $str = str_replace(')', '', $str);
    $str = str_replace('（', '', $str);
    $str = str_replace('）', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('_', '', $str);
    $str = str_replace('——', '', $str);
    $str = str_replace('+', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('|', '', $str);
    $str = str_replace('\\', '', $str);
    $str = str_replace('[', '', $str);
    $str = str_replace(']', '', $str);
    $str = str_replace('【', '', $str);
    $str = str_replace('】', '', $str);
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('；', '', $str);
    $str = str_replace(':', '', $str);
    $str = str_replace('：', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('“', '', $str);
    $str = str_replace('”', '', $str);
    $str = str_replace(',', '', $str);
    $str = str_replace('，', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    $str = str_replace('《', '', $str);
    $str = str_replace('》', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('。', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('、', '', $str);
    $str = str_replace('?', '', $str);
    $str = str_replace('？', '', $str);
    return trim($str);
}



/**
 * 判断 文件/目录 是否可写（取代系统自带的 is_writeable 函数）
 *
 * @param string $file 文件/目录
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

    $curl = curl_init(); //开启curl
    $header[] = "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0";
    $header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
    $header[] = "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Upgrade-Insecure-Requests:1";
    $header[] = "Connection: keep-alive";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//    curl_setopt($curl, CURLOPT_URL, $urls[$rand_keys]); //设置请求地址
//    curl_setopt($curl, CURLOPT_URL,'http://www.qiushibaike.com/pic/'); //设置请求地址
    curl_setopt($curl, CURLOPT_URL,$url); //设置请求地址
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
    $html = curl_exec($curl); //执行curl操作
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
$groupstr = $var['htt_qsbk']['groups']; //用户组
$threads = $var['htt_qsbk']['threads'];
$charset_num = $var['htt_qsbk']['charset'];  // 1utf-8 2gbk
$caiji_model = $var['htt_qsbk']['caiji_model']; //1纯文 2表示纯图 3图文
$imgpath = $var['htt_qsbk']['imgpath']; //目录
$check = $var['htt_qsbk']['check'];  //1不审核 2审核。
$title_length = $var['htt_qsbk']['title_length']; //标题长度

//如果采集数量为0.则不执行后面的操作。不采集。
if($threads == 0){
    return;
}
$fids =array_filter(unserialize($fidstr));
if ( is_null($fids) || empty($fids)) {
    //则显示错误信息。
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_fid'), '', 'error');
}

$uids = array_filter(explode(',',$uidstr));

$groups = array_filter(unserialize($groupstr));
$members_bygroup = C::t('common_member')->fetch_all_by_groupid($groups);//该组的会员资料


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
//检查目录存在或者可写。非纯文模式且设置了路径才检查。
if($caiji_model != 1 && !empty($imgpath) && !new_is_writeable($_G['setting']['attachurl'].'forum/'.$imgpath)){
    //尝试自动创建目录。如果失败，给出提示。
    //路径应当是 htt_qsbk/
    $res=mkdir(iconv("UTF-8", "GBK", $_G['setting']['attachurl'].'forum/'.$imgpath),0777,true);
    if (!$res){
        return;
//        cpmsg(lang('plugin/htt_qsbk', 'error_setting_imgpath'), '', 'error');
    }
}*/



//检查是否超出范围。
if ($threads<0 || $threads>20) {
    //则显示错误信息。
//    return ;
    cpmsg(lang('plugin/htt_qsbk', 'error_setting_threads'), '', 'error');
}

//数据源。
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

#从数组中随机取一个
$rand_keys = array_rand($urls, 1);
$url = $urls[$rand_keys];

//检查函数是否可用。
if(function_exists('curl_init') && function_exists('curl_exec')) {

    $html = curl_qsbk($url);
}else{

    cpmsg(lang('plugin/htt_qsbk', 'error_curl'), '', 'error');
}

//echo 111;
//echo $html;
//exit();

//解析数据
include_once DISCUZ_ROOT . './source/plugin/htt_qsbk/include/phpQuery/phpQuery.php';
phpquery::newDocumentHTML($html, 'utf-8');
#获取段子列表。最外面那个。
$articles = pq(".article");
$count = 1; //计数
foreach ($articles as $article) {

    //如果超过数量。则退出循环。
    if($count > $threads){
        break;
    }
    $count = $count+1;

    $data = array();
    $data['content'] = pq($article)->find(".content")->text();
    $data['img'] = pq($article)->find(".thumb a img")->attr('src');

    //纯文则不会有图片。无须判断
    //纯图则需要判断。路径问题。
    //图片存在,则必须采集。路径存在则进入下载。否则引入外链
    $remote = 1; //默认远程附件

    $imgpath = '201604/07/';

    if(!empty($data['img'])){
        if(!empty($imgpath)){
            $local_img = time().uniqid().'.png';
            $context = stream_context_create(array(
                'http' => array(
                    'timeout' => 30 //超时时间，单位为秒
                )
            ));
            @file_put_contents(DISCUZ_ROOT.'./'.$_G['setting']['attachurl'].'forum/'.$imgpath.$local_img, file_get_contents($data['img'],0,$context));
            $data['img'] =$imgpath.$local_img;
            #这里的路径要从 data/attament/forum/开始。

            $remote = 0; //主题图片表中 1表示远程图片。0表示本地图片。
        }

        $attachment = 2; //附件,0无附件 1普通附件 2有图片附件

    }else{
        $attachment = 0; //附件,0无附件 1普通附件 2有图片附件
    }

    //修改审核参数。-2
    if($check == '2'){
        $invisible = -2; //需要审核
        $displayorder = -2; //显示顺序
    }else{
        $invisible = 0; //无须审核。
        $displayorder = 0; //需要审核的帖子为-2
    }

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


    //控制标题的长度。存在内容。同时内容长度超过最大长度。则截取。
    if(!empty($data['content'] && strlen($data['content']) > $title_length )){

        $subject = cutstr($data['content'], $title_length, '');
    }else {
        $subject = $data['content'];
    }

    //标题去掉一次特殊字符串.否则引发首页四
    $subject = strFilter($subject);



    $publishdate = time();

    if(!empty($data['img'])){
        $message = $data['content']."[img]".$_G['setting']['attachurl'].'forum/'.$data['img']."[/img]";
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
        'attachment' => $attachment,
        'moderated' => 0,
        'status' => 32,
        'isgroup' => 0,
        'replycredit' => 0,
        'closed' => 0
    );
    //插入主题
    $tid = C::t('forum_thread')->insert($newthread, true);

    //假如到主题的图片表
    //remote 0表示本地。


    C::t('forum_threadimage')->insert(array(
        'tid'=>$tid,
        'attachment'=>$data['img'],
        'remote'=>$remote,
    ),true);




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
        updatemoderate('tid', $tid);
        C::t('forum_forum')->update_forum_counter($fid, 0, 0, 1);

        //插入审核表。
        C::t('common_moderate')->insert('tid',array(
            'id'=>$tid,
            'status' => '0',
            'dateline' => $publishdate,
        ));
        //通知审核。
        manage_addnotify('verifythread');
        return ;
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
    //沙发数据
    C::t('forum_sofa')->insert(array('tid' => $tid,'fid' => $forum['fid']));
}
?>