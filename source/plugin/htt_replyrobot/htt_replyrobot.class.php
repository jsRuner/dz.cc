<?php
/**
 *	[回帖机器人(htt_replyrobot.{modulename})] (C)2016-2099 Powered by 吴文付.
 *	Version: 1.0
 *	Date: 2016-4-4 10:58
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_htt_replyrobot {
    const DEBUG = 0;
    protected static $postReportAction = array('post_newthread_succeed', 'post_edit_succeed', 'post_reply_succeed',
        'post_newthread_mod_succeed', 'post_newthread_mod_succeed', 'post_reply_mod_succeed',
        'edit_reply_mod_succeed', 'edit_newthread_mod_succeed');
	//TODO - Insert your code here
    protected static $cloudAppService;
    protected static $securityService;
    protected static $securityStatus;

    public function __construct() {
        self::$cloudAppService = Cloud::loadClass('Service_App');
        self::$securityStatus = self::$cloudAppService->getCloudAppStatus('security');
        self::$securityService = Cloud::loadClass('Service_Security');
    }
    /*回复某个主题。评论和文章均存在post表中。*/
    protected function _replyNewThread($param) {
        global $_G;
        if (!$param['pid'] || !$param['tid']) {
            return false;
        }
        $tid = $param['tid']; //主题id
        $pid = $param['pid']; //评论的目标post 。这里肯定是主题的内容。

        $thread = C::t('forum_thread')->fetch($tid);
        $post = C::t('forum_post')->fetch($tid,$pid);

        $author = 'admin';
        $uid = 1;
        $publishdate = time();

        $message = '谢谢发帖，支持论坛';

        //回复内容插入。
        $reply_pid = insertpost(array(
            'fid' => $thread['fid'],
            'tid' => $tid,
            'first' => '1', //是否为首帖
            'author' => $author,
            'authorid' => $uid,
            'subject' => '',
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

        //获取帖子的位置信息。
        $maxposition = C::t('forum_post')->fetch_maxposition_by_tid($pid, $tid);

//        useractionlog($uid, 'pid');

        //抢沙发列表。存的是0回复的帖子。
        C::t('forum_sofa')->delete($tid);
        //最后回复
        C::t('forum_thread')->update($tid, array('lastposter' => $author), true);
        //主题的回复数。
        C::t('forum_thread')->increase($tid, array('replies' => 1));

        return true;
    }



}


//脚本嵌入点类。发帖Ok后触发。
class plugin_htt_replyrobot_forum extends plugin_htt_replyrobot {

    public function post_security(){
        return true;
    }

    public function post_report_message($param) {
        global $_G, $extra, $redirecturl;

        file_put_contents("1.txt",json_encode($param));

        $param['message'] = $param['param'][0];
        $param['values'] = $param['param'][2];
        //处理发帖的逻辑。todo:需要设定概率。不能每次都触发吧。
        if (in_array($param['message'], self::$postReportAction)) {
            switch ($param['message']) {
                case 'post_newthread_succeed': //发帖完毕
                    $this->_replyNewThread($param['values']);
                    break;
                default:break;
            }
        }
    }



}

?>