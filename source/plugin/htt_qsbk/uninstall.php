<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) exit('Access Denied!');



//п╤ть╪ф╩╝╫е╠╬║ё

$cronid = C::t('common_cron')->get_cronid_by_filename('htt_qsbk:cron_htt_qsbk.php');
if($cronid){
    C::t('common_cron')->delete($cronid);
}


$finish = TRUE;

?>