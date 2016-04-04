<?php
/**
 *	[����������(htt_replyrobot.{modulename})] (C)2016-2099 Powered by ���ĸ�.
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
    /*�ظ�ĳ�����⡣���ۺ����¾�����post���С�*/
    protected function _replyNewThread($param) {
        global $_G;
        if (!$param['pid'] || !$param['tid']) {
            return false;
        }
        $tid = $param['tid']; //����id
        $pid = $param['pid']; //���۵�Ŀ��post ������϶�����������ݡ�

        $thread = C::t('forum_thread')->fetch($tid);
        $post = C::t('forum_post')->fetch($tid,$pid);

        $author = 'admin';
        $uid = 1;
        $publishdate = time();

        $message = 'лл������֧����̳';

        //�ظ����ݲ��롣
        $reply_pid = insertpost(array(
            'fid' => $thread['fid'],
            'tid' => $tid,
            'first' => '1', //�Ƿ�Ϊ����
            'author' => $author,
            'authorid' => $uid,
            'subject' => '',
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

        //��ȡ���ӵ�λ����Ϣ��
        $maxposition = C::t('forum_post')->fetch_maxposition_by_tid($pid, $tid);

//        useractionlog($uid, 'pid');

        //��ɳ���б������0�ظ������ӡ�
        C::t('forum_sofa')->delete($tid);
        //���ظ�
        C::t('forum_thread')->update($tid, array('lastposter' => $author), true);
        //����Ļظ�����
        C::t('forum_thread')->increase($tid, array('replies' => 1));

        return true;
    }



}


//�ű�Ƕ����ࡣ����Ok�󴥷���
class plugin_htt_replyrobot_forum extends plugin_htt_replyrobot {

    public function post_security(){
        return true;
    }

    public function post_report_message($param) {
        global $_G, $extra, $redirecturl;

        file_put_contents("1.txt",json_encode($param));

        $param['message'] = $param['param'][0];
        $param['values'] = $param['param'][2];
        //���������߼���todo:��Ҫ�趨���ʡ�����ÿ�ζ������ɡ�
        if (in_array($param['message'], self::$postReportAction)) {
            switch ($param['message']) {
                case 'post_newthread_succeed': //�������
                    $this->_replyNewThread($param['values']);
                    break;
                default:break;
            }
        }
    }



}

?>