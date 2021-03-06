<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<title>欢迎使用WSPDM2-基于PHP Websocket的数据库管理器</title>
        <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
        <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="<?= base_url('js/swfobject.js')?>"></script>
        <script type="text/javascript" src="<?= base_url('js/web_socket.js')?>"></script>
        <script type="text/javascript" src="<?= base_url('js/json.js')?>"></script>
        <script type="text/javascript" src="<?= base_url('js/jquery.form.js')?>"></script>
        <style>
            body
            {
                background-color: #eee;
                margin-bottom: 60px;
            }
            .footer {
              position: absolute;
              bottom: 0;
              width: 100%;
              height: 80px;
            }
        </style>
        <script>
        
        if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
        WEB_SOCKET_SWF_LOCATION = "swf/WebSocketMain.swf";
        WEB_SOCKET_DEBUG = true;
        var ws, ping, ping_interval, reconnect_interval, name = 'null', user_list={}, connect = 0;        
        
//        try {
        ws = new WebSocket("ws://"+document.domain+":8080/");
           
          // 当socket连接打开时，输入用户名
          ws.onopen = function() {  
              connect = 1;
              $("#db_username, #db_password, #db_type, #db_host, #db_port, .btn").removeAttr("disabled");
          };
          
          // 当有消息时根据消息类型显示不同信息
          ws.onmessage = function(e) {
            var result = JSON.parse(e.data);  
            
            if (result[0] != "1"){
                switch (result[0])
                {                
                    case "p":    //ping
                        var date = new Date();
                        ping = date.getTime() - ping;
                        //alert(date.getTime());
                        $("#ping").html(ping + "ms");
                        break;
                    default:
                        alert(result[1]);
                }          
            } else {
                location.href='<?= base_url("index.php/ControlCenter")?>';
            }
            
                        
          };
          ws.onclose = function() {
                console.log("服务端关闭了连接");                
                $("#db_username, #db_password, #db_type, #db_host, #db_port, .btn").attr("disabled", "disabled");
                alert("和服务器断开连接");
              //clearInterval(ping_interval);
              //reconnect_interval = setInterval("reconnect()",1000);              
          };
          ws.onerror = function() {
              console.log("出现错误");              
          };          
        </script>    
        <script>
            var options = {
                dataType : "json",
                beforeSubmit : function (){
                    $(".btn").html("正在提交中，请稍后");
                    $(".btn").attr("disabled", "disabled");
                },
                success : function (result){
                    switch (result[0])
                    {
                        case 1:
                            if (!connect){
                                alert('无法连接WebSocket服务器，请使用控制台开启Workerman');
                                $(".btn").html("登录");
                                $(".btn").removeAttr("disabled");  
                                return ;
                            }
                            location.href='<?= base_url("index.php/mobile/MobileControlCenter")?>';
                            break;
                        default:
                            alert(result[1]);
                    }                        
                    
                    $(".btn").html("登录");
                    $(".btn").removeAttr("disabled");                    
                }
            };
            
            $(".form-signin").ajaxForm(options);  
        </script>
</head>
<body>
    <div>
        <form action="<?= base_url("index.php/index/PassCheck")?>" class="form-signin" role="form" method="post">
        <h2 class="form-signin-heading">欢迎使用WSPDM2</h2>
        <br/>
        <div class="form-group">            
            <input type="text" name="db_username" id="db_username" class="form-control" placeholder="数据库账号" disabled="disabled" required="" autofocus="">
            <input type="password" name="db_password" id="db_password" class="form-control" placeholder="数据库密码" disabled="disabled">            
            <select class="form-control" name="db_type" id="db_type" disabled="disabled">
                <option>MySQL</option>    
                <option>MongoDB</option>    
                <option>Oracle</option>    
                <option>MS SQL Server</option>    
                <option>MSSQL</option>    
                <option>ODBC</option>    
                <option>IBM</option>    
                <option>cubrid</option>    
                <option>firebird</option>    
                <option>INFORMIX</option>    
                <option>4D</option>    
                <option>PostgreSQL</option>    
            </select>
        <hr>
        <input type="text" name="db_host" id="db_host" disabled="disabled" class="form-control" placeholder="数据库地址（选填）">
        <input type="text" name="db_port" id="db_port" disabled="disabled" class="form-control" placeholder="数据库端口（选填）">             
        </div>
        <br/>
        <button type="submit" disabled="disabled"  class="btn btn-lg btn-primary btn-block">登录数据库</button>
        </form>
    </div>
</body>
</html>