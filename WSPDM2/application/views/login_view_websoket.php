<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<title>欢迎使用WSPDM-基于PHP Websocket的数据库管理器</title>
        <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
        <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="<?= base_url('js/swfobject.js')?>"></script>
        <script type="text/javascript" src="<?= base_url('js/web_socket.js')?>"></script>
        <script type="text/javascript" src="<?= base_url('js/json.js')?>"></script>
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
        var ws, ping, ping_interval, reconnect_interval, name = 'null', user_list={};        
        
        //connect();
        //function connect(){
            ws = new WebSocket("ws://"+document.domain+":8080/");   
        //}
        
        
        //断线重连
        /*function reconnect(){
            if (1 != ws.readyState){
                alert('yoo');
                connect();
            } else {
                ping_interval = setInterval("getping()",1000);
                clearInterval(reconnect_interval);
            }         
        }*/
        //显示连接状态
        function getping(){ 
            var date = new Date();
            ping = date.getTime();              
            ws.send(JSON.stringify({"type":"ping"}));                      
        }       
          // 当socket连接打开时，输入用户名
          ws.onopen = function() {  
              ping_interval = setInterval("getping()",1000);
              //ws.send(JSON.stringify({"type":"noajax","name":name}));
          };
          
          // 当有消息时根据消息类型显示不同信息
          ws.onmessage = function(e) {
            console.log(e.data);
            var result = JSON.parse(e.data);  
            console.log(result);
            
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
                location.href='<?= base_url("index.php/control_center")?>';
            }
            
                        
          };
          ws.onclose = function() {
              console.log("服务端关闭了连接"); 
              //clearInterval(ping_interval);
              //reconnect_interval = setInterval("reconnect()",1000);              
          };
          ws.onerror = function() {
              console.log("出现错误");              
          };          
        </script>        
</head>
<body>
    <div class="col-sm-8 col-sm-offset-2">
        <form role="form">
        <h2 class="form-signin-heading">欢迎使用WSPDM</h2>
        <br/>
        <div class="form-group">            
        <input type="text" id="db_username" class="form-control " placeholder="数据库账号" required="" autofocus="">
        <input type="password"  id="db_password" class="form-control" placeholder="数据库密码" required="">            
        </div>
        <br/>
        <input onclick="CheckPW()" class="btn btn-lg btn-primary btn-block" value="登录数据库">
        </form>
    </div>
    <div class="footer">
      <div class="container">
          <p class="text-muted">Ping:<a id="ping"></a><br/>WSPDM<br/>版权所有(C) 2014-<?= date('Y')?> 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen<br/>Released under the GPL V3.0 License</p>
      </div>
    </div>
    <script>
    function CheckPW() {
        var data = new Array();
        data['type'] = 'func';
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/index/PassCheck';
        data['data'] = '{"db_username" : "' + $("#db_username").val() + '", "db_password" : "' + $("#db_password").val() + '"}';
        //console.log(data);
        ws.send('{"type":"' + data['type'] + '","api":"' + data['api'] + '","data":' + data['data'] + '}');
    }
    </script>
</body>
</html>