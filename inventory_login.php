<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>


<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪库存管理-仓库</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
    <!-- <script type="text/javascript" src="js/alert.js"></script> -->
    <style>
        

*{padding: 0;margin: 0;}

/* 清除浮动 */
.clearfix:after {content: "";display: table;clear: both;}
html, body { height: 100%; }
body {    font-family:"Microsoft YaHei"; background:#EBEBEB; background:url(../images/stardust.png);       font-weight: 300;  font-size: 15px;  color: #333;overflow: hidden;}
a {text-decoration: none; color:#000;}
a:hover{color:#F87982;}

/*home*/
#home{padding-top:50px;}

/*logint界面*/
#login{ padding:10px 10px 10px; width:100%; background:#FFF; margin:auto;
border-radius: 3px;
box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);
}

.current1{
-moz-transform: scale(0);
-webkit-transform: scale(0);
-o-transform: scale(0);
-ms-transform: scale(0);
transform: scale(0);
-moz-transition: all 0.4s ease-in-out;
-webkit-transition: all 0.4s ease-in-out;
-o-transition: all 0.4s ease-in-out;
transition: all 0.4s ease-in-out;
}


.current{
-moz-transform: scale(1);
-webkit-transform: scale(1);
-o-transform: scale(1);
-ms-transform: scale(1);
transform: scale(1);

}
#login h3{ font-size:28px; line-height:25px; font-weight:300; letter-spacing:3px; margin-bottom:20px;  text-align:center;}
#login label{  display:block; height:35px; padding:0 10px; font-size:18px; line-height:35px;  background:#EBEBEB; margin-bottom:10px;position:relative;}
#login label input{  font:20px/20px "Microsoft YaHei"; background:none;  height:20px; border:none; margin:7px 0 0 10px;width:245px;outline:none ; letter-spacing:normal;  z-index:1; position:relative;  }
#login label  span{ display:block;  height:35px; color:#F30; width:100px; position:absolute; top:0; left:190px; text-align:right;padding:0 10px 0 0; z-index:0; display:none; }
#login button{ font-family:"Microsoft YaHei"; cursor:pointer; width:300px;  height:35px; background:#FE4E5B; border:none; font-size:14px; line-height:30px;  letter-spacing:3px; color:#FFF; position:relative; margin-top:10px;
-moz-transition: all 0.2s ease-in;
-webkit-transition: all 0.2s ease-in;
-o-transition: all 0.2s ease-in;
transition: all 0.2s ease-in;}
#login button:hover{ background:#F87982; color:#000;}

/*头像*/
.avator{
    display:block;
    margin:0 auto 20px;
    border-radius:50%;
}


    </style>
</head>
    
    
<body>
    

<div id="home">
    <form id="login" class="current1" method="post">
        <h3>用户登入</h3>
        <label >
            所属仓库:<select id="warehouse_id" style="width:12.5em;height:2em;">
            </select>
        </label>
        <label>用户名<input id="username" type="text" name="name" style="width:215px;" /><span>用户名为空</span></label>
        <label>密码<input id="password" type="password" name="pass"  /><span>密码为空</span></label>
        <button type="button" id="login">登入</button>
    </form>
</div>


</body>



<script>
    $(document).ready(function(){

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getWarehouseId'
            },
            success : function (response){

                if(response){
                    var jsonData = eval(response);
                    var html = '<option value=0>-请选择所在仓库-</option>';
                    $.each(jsonData, function(index, value){
                        html += '<option value='+ value.warehouse_id +' >' + value.title + '</option>';
                    });

                    $('#warehouse_id').html(html);
                }

            }
        });
    });





    $(function(){
        $("#login").addClass("current");

        /**
         * 正则检验邮箱
         * email 传入邮箱
         * return true 表示验证通过
         */
        function check_email(email) {
            if (/^[\w\-\.]+@[\w\-]+(\.[a-zA-Z]{2,4}){1,2}$/.test(email)) return true;
        }


        //input 按键事件
        $("input[name]").keyup(function(e){
            //禁止输入空格  把空格替换掉
            if($(this).attr('name')=="pass"&&e.keyCode==32){
                $(this).val(function(i,v){
                    return $.trim(v);
                });
            }
            if($.trim($(this).val())!=""){
                $(this).nextAll('span').eq(0).css({display:'none'});
            }
        });


        //错误信息
        var succ_arr=[];

        //input失去焦点事件
        $("input[name]").focusout(function(e){

            var msg="";
             if($.trim($(this).val())==""){
                  if($(this).attr('name')=='name'){
                          succ_arr[0]=false;
                          msg="登入名为空";
                  }else if($(this).attr('name')=='pass'){
                           succ_arr[1]=false;
                           msg="密码为空";
                  }

            }else{
                  if($(this).attr('name')=='name'){
                          succ_arr[0]=true;

                  }else if($(this).attr('name')=='pass'){
                           succ_arr[1]=true;

                  }
            }
            var a=$(this).nextAll('span').eq(0);
            a.css({display:'block'}).text(msg);
        });


        //Ajax用户注册
        $("button[id='login']").click(function(){
            $("input[name]").focusout();  //让所有的input标记失去一次焦点 来设置msg信息
            for (x in succ_arr){if(succ_arr[x]==false) return;}
            // $("#login").removeClass("current");
            var data=$('#login').serialize(); //序列化表单元素

            var username = $("#username").val();
            var password = $("#password").val();
            var warehouse_id = $("#warehouse_id").val();
            var warehouse_title =  $("#warehouse_id").text();

            var ver = 0;
            <?php if(@$_GET['ver'] == 'db' || @$_GET['return'] == 'l.php'){ ?>
            ver = 'db';
            <?php } ?>
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'inventory_login',
                    username : username,
                    password : password,
                    warehouse_id : warehouse_id,
                    warehouse_title :warehouse_title,
                    ver : ver
                },
                success : function (response , status , xhr){

                    console.log(response);
                    var jsonData = $.parseJSON(response);
                    if(jsonData.status == 1){
                        alert("用户不存在或密码错误或所选仓库错误");
                    }
                    if(jsonData.status == 2){
                          location.href = "<?php echo $_GET['return'];?>?auth=xsj2015inv";
                    }
                }
            });

        });
        /**
         有兴趣的可以到这里 自行发送Ajax请求 实现注册功能
         */


    });





</script>



</html>