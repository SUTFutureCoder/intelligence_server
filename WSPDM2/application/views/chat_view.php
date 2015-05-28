<!DOCTYPE html>  
<html>  
    <head>  
        <title></title>
        <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
        <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">   
        <style>
        .submit{cursor: pointer;}
        .content{margin: 0px auto;width: 90%;border: 1px solid #BFBFBF;background: #FFF;}
        .content:after{content: ".";display: block;height: 0;clear: both; visibility: hidden; }
        .content{zoom: 1;}
        .message{border-bottom: 1px solid #BFBFBF;padding:10px;height: 400px;overflow: auto;line-height:1.8;}
        .message .msg{color: #FF0000;}
        .message .chat{color: #333;}
        .message .chat .name{font-size: 12px;color: #12c;font-weight: bold;}
        .message .chat.admin_chat .name{color: #FF0000;}
        .message .chat p{margin:0 0 10px 0;}
        .history_mess {height: 489px;overflow: auto;display: none;border-bottom: 2px solid #BFBFBF; }
        .tool{padding: 8px 10px;border-bottom: 1px solid #BFBFBF;}
        .tool span{cursor: pointer;display: inline-block;margin-right: 14px;}
        .history{position: absolute;right: 10px;}
        .send {float: left; }
        .send .chat{ position: absolute;margin: 10px;display: block;width: 450px;height: 50px;}
        .list{float:right;}
        .list{float:right;margin:10px;}
        .list h3{font-size: 14px;margin:0;}
        .list .online{font-size: 12px;padding-left:6px;font-weight: normal;}
        .list ul{width: 200px;list-style: none;margin:10px 0;padding:0;height: 130px;overflow: auto;}
        .list ul li{padding:3px 0;font-size: 12px; cursor: pointer;}
        .name_history{color:#FF0000}
        </style>
    </head>
    <body>
        <br/>
        <div class="content">
                <div class="message"></div>
                <div class="tool">
                        <span class="empty">清空记录</span>
                </div>
                <div class="send">
                    <script type="text/plain" id="myEditor" style="width:100%;height:240px;"></script>		
                        <p><input type="submit" class="submit btn btn-success" name="submit" value="发送" />[Ctrl+Enter]</p>
                </div>
                <div class="list">
                        <h3>在线用户<strong class="online">0</strong></h3>
                        <ul>
                        </ul>
                </div>
        </div>
    </body>
    <link href="<?= base_url('umeditor/themes/default/css/umeditor.css')?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" charset="utf-8" src="<?= base_url('umeditor/umeditor.config.js') ?>"></script>
    <script type="text/javascript" charset="utf-8" src="<?= base_url('umeditor/umeditor.min.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('umeditor/lang/zh-cn/zh-cn.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('js/base64.js') ?>"></script>
    <script>
        //实例化编辑器
        var um = UM.getEditor('myEditor');
        var content = '';
        $(function(){    
            $(".submit").click(function(){
                content = um.getContent();
                if (content){
                    var data = new Array();
                    data['user_name']   =   '<?= $user_name ?>';
                    data['src']         =   location.href.slice((location.href.lastIndexOf("/")));
                    data['content']     =   BASE64.encoder(content);
                    parent.IframeSay(data);
                }
                $("#myEditor").html('');
            });
            
            $('.send').keydown(function (e) {
                if ((e.ctrlKey && e.keyCode == 13) || (e.altKey && e.keyCode == 83)){
                    $('.send .submit').click();
                    return false;
                }
            });

            $('.tool .empty').click(function(){
                $(".message").html('');
            }) 
        });
        
        //接收母窗口传来的值
        function MotherResultRec(data) {
            $(".message").append('<div  class="chat"><div class="name">' + data[2] + '&nbsp;&nbsp;&nbsp;' + data[4] + '</div><p>' + data[3] + '</p></div>');
        }       
    </script>
</html>