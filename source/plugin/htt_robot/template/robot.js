/**
 * Created by Administrator on 2016/4/8.
 */

//绑定点击事件。
var jq = jQuery.noConflict(); //消除jquery的冲突。

jq(window).load(function(){

    var robot_close =  jq('#robot_container_closed');
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
   /* robot_open.bind('click',function(){
        robot_open.hide();
        robot_close.show();
    })*/

    //点击发送按钮事件。添加ul中。并等待结果。ajaj请求等待结果。
    send_btn.bind('click',function(){
        var msg = send_input.val();
        console.log(msg);
        //构造一个li。插入到列表中。
        var me = ' <li class="me"> <span>我</span> <div>'+msg+'</div></li>';
        msg_list.append(me);

        //判断距离。滚动。

        //alert(1);


    });




});