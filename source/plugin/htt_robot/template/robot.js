/**
 * Created by Administrator on 2016/4/8.
 */

//�󶨵���¼���
var jq = jQuery.noConflict(); //����jquery�ĳ�ͻ��

jq(window).load(function(){

    var robot_close =  jq('#robot_container_closed');
    var robot_open =  jq('#robot_container_open');

    var send_btn = jq('#send_button') //���Ͱ�ť

    var send_input = jq('.do_area input') //����

    var msg_list = jq('.wechat') //�����б�


    //�򿪻����ˡ�
   robot_close.bind('click',function(){
       robot_open.show();
       robot_close.hide();
    })

    //�رջ����ˡ�
   /* robot_open.bind('click',function(){
        robot_open.hide();
        robot_close.show();
    })*/

    //������Ͱ�ť�¼������ul�С����ȴ������ajaj����ȴ������
    send_btn.bind('click',function(){
        var msg = send_input.val();
        console.log(msg);
        //����һ��li�����뵽�б��С�
        var me = ' <li class="me"> <span>��</span> <div>'+msg+'</div></li>';
        msg_list.append(me);

        //�жϾ��롣������

        //alert(1);


    });




});