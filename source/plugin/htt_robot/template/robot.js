/**
 * Created by Administrator on 2016/4/8.
 */

//绑定点击事件。
var jq = jQuery.noConflict(); //消除jquery的冲突。

function keepBottom(){

}

jq(window).load(function(){

    var robot_close =  jq('#robot_container_closed'); //聊天的容器
    var close_btn =  jq('#close_btn'); //关闭按钮
    var robot_open =  jq('#robot_container_open');

    var send_btn = jq('#send_button') //发送按钮

    var send_input = jq('.do_area input') //内容

    var msg_list = jq('.wechat') //聊天列表


    //打开机器人。
   robot_close.bind('click',function(){
       robot_open.show();
       robot_close.hide();
    })

    //关闭机器人。
    close_btn.bind('click',function(){
        robot_open.hide();
        robot_close.show();
    })

    //执行聊天逻辑。
    function sendmsg (){
        var msg = send_input.val();
        console.log(msg);
        //构造一个li。插入到列表中。
        var me = ' <li class="me"> <span>我</span> <div>'+msg+'</div></li>';
        msg_list.append(me);
        //输出一下滚动条的位置。
        console.log(msg_list[0].scrollTop)
        console.log(msg_list[0].scrollHeight) //滚动条的高度。如果超出多少。则修改距离顶部
        msg_list[0].scrollTop = msg_list[0].scrollTop+42*2;

        //禁用按钮。避免发言过快。
        send_btn.attr('disabled','disabled')

        //ajax请求后台。
        jq.ajax({
            type: 'POST',
            url: 'http://dzgbk.cc/plugin.php?id=htt_robot:robot',
            data: {msg:msg},
            success: function(data){

                var robot = ' <li class="robot"> <span>小小付</span> <div>'+data.msg+'</div></li>';
                msg_list.append(robot);
            },
            dataType: 'json',
            error:function(XMLHttpRequest, textStatus, errorThrown){
                var robot = ' <li class="robot"> <span>小小付</span> <div>哎呀,脑袋坏掉了，请联系我的主人</div></li>';
                msg_list.append(robot);
                //msg_list[0].scrollTop = msg_list[0].scrollTop+330;
            },
            complete:function(XMLHttpRequest, textStatus){
                //请求完成后。开启按钮。
                send_btn.attr('disabled',false)
            }



        });



    }



    //点击发送按钮事件。添加ul中。并等待结果。ajaj请求等待结果。
    send_btn.bind('click',sendmsg);

    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==27){ // 按 Esc
            //要做的事情
        }
        if(e && e.keyCode==113){ // 按 F2
            //要做的事情
        }
        if(e && e.keyCode==13){ // enter 键
            //要做的事情
            sendmsg();
        }
    };




});