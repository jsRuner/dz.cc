/**
 * Created by Administrator on 2016/4/8.
 */

//�󶨵���¼���
var jq = jQuery.noConflict(); //����jquery�ĳ�ͻ��

function keepBottom(){

}

jq(window).load(function(){

    var robot_close =  jq('#robot_container_closed'); //���������
    var close_btn =  jq('#close_btn'); //�رհ�ť
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
    close_btn.bind('click',function(){
        robot_open.hide();
        robot_close.show();
    })

    //������Ͱ�ť�¼�������ul�С����ȴ������ajaj����ȴ������
    send_btn.bind('click',function(){
        var msg = send_input.val();
        console.log(msg);
        //����һ��li�����뵽�б��С�
        var me = ' <li class="me"> <span>��</span> <div>'+msg+'</div></li>';
        msg_list.append(me);
        //���һ�¹�������λ�á�
        console.log(msg_list[0].scrollTop)
        console.log(msg_list[0].scrollHeight) //�������ĸ߶ȡ�����������١����޸ľ��붥��
        msg_list[0].scrollTop = msg_list[0].scrollTop+42*2;

        //���ð�ť�����ⷢ�Թ��졣
        send_btn.attr('disabled','disabled')

        //ajax�����̨��
        jq.ajax({
            type: 'POST',
            url: 'http://dzgbk.cc/plugin.php?id=htt_robot:robot',
            data: {msg:msg},
            success: function(data){

                var robot = ' <li class="robot"> <span>СС��</span> <div>'+data.msg+'</div></li>';
                msg_list.append(robot);
            },
            dataType: 'json',
            error:function(XMLHttpRequest, textStatus, errorThrown){
                var robot = ' <li class="robot"> <span>СС��</span> <div>��ѽ,�Դ������ˣ�����ϵ�ҵ�����</div></li>';
                msg_list.append(robot);
                //msg_list[0].scrollTop = msg_list[0].scrollTop+330;
            },
            complete:function(XMLHttpRequest, textStatus){
                //������ɺ󡣿�����ť��
                send_btn.attr('disabled',false)
            }



        });



    });




});