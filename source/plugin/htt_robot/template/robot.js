/**
 * Created by Administrator on 2016/4/8.
 */

//�󶨵���¼���
var jq = jQuery.noConflict(); //����jquery�ĳ�ͻ��



function keepBottom(){

}

jq(window).load(function(){

    var robot_close =  jq('#robot_container_closed'); //���������
    //var close_btn =  jq('#close_btn'); //�رհ�ť
    var close_btn =  jq('#robot_container_open .title .headBtn'); //�رհ�ť

    var robot_open =  jq('#robot_container_open');

    //var send_btn = jq('#send_button') //���Ͱ�ť
    var send_btn = jq('#sendBtn') //���Ͱ�ť

    //var send_input = jq('.do_area input') //����
    var send_input = jq('#inputMsg') //����

    var msg_list = jq('.wechat') //�����б�


    var host = window.location.host; //����

    var robot_name = jq('#robot_container_open > div.title > span').text();

    //console.log(host);

    //�򿪻����ˡ�
   robot_close.bind('click',function(){
       robot_open.show();
       robot_close.hide();
    })

    //�رջ����ˡ�
    close_btn.bind('click',function(){

        var xx = confirm("����,����ֻ�йرչ��ܣ�Ҫ�뿪����?�����ء�����")
        robot_open.hide();
        robot_close.show();
    })

    //ִ�������߼���
    function sendmsg (){
        var msg = send_input.val();
        if(msg ==''){
            alert('���ݲ�����Ϊ��Ӵ')
            return;
        }else{
            send_input.val('');
        }


        console.log(msg);
        //���һ�¹�������λ�á�
        console.log(msg_list[0].scrollTop)
        console.log(msg_list[0].scrollHeight) //�������ĸ߶ȡ�����������١����޸ľ��붥��

        var sh = msg_list[0].scrollHeight;

        //����һ��li�����뵽�б��С�
        var me = ' <li class="me"> <span>��</span> <div>'+msg+'</div></li>';
        msg_list.append(me);



        //���ð�ť�����ⷢ�Թ��졣
        //send_btn.attr('disabled','disabled')

        //ajax�����̨��
        jq.ajax({
            type: 'POST',
            url: 'http://'+host+'/plugin.php?id=htt_robot:robot',
            data: {msg:msg},
            success: function(data){

                var robot = ' <li class="robot"> <span>'+robot_name+'</span> <div>'+data.msg+'</div></li>';
                msg_list.append(robot);
                console.log(msg_list[0].scrollTop)
                console.log(msg_list[0].scrollHeight) //�������ĸ߶ȡ�����������١����޸ľ��붥��
                var eh = msg_list[0].scrollHeight;
                msg_list[0].scrollTop = msg_list[0].scrollTop+(eh-sh);
            },
            dataType: 'json',
            error:function(XMLHttpRequest, textStatus, errorThrown){
                var robot = ' <li class="robot"> <span>'+robot_name+'</span> <div>��ѽ,�Դ������ˣ�����ϵ�ҵ�����</div></li>';
                msg_list.append(robot);
                //msg_list[0].scrollTop = msg_list[0].scrollTop+330;

            },
            complete:function(XMLHttpRequest, textStatus){
                //������ɺ󡣿�����ť��
                send_btn.attr('disabled',false)

            }



        });





    }



    //������Ͱ�ť�¼������ul�С����ȴ������ajaj����ȴ������
    send_btn.bind('click',sendmsg);

    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==27){ // �� Esc
            //Ҫ��������
        }
        if(e && e.keyCode==113){ // �� F2
            //Ҫ��������
        }
        if(e && e.keyCode==13){ // enter ��
            //Ҫ��������
            if(!robot_open.is(":hidden")){
                sendmsg();
            }
        }
    };




});