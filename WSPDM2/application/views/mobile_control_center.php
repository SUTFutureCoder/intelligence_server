<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>WSPDM2-基于PHPWebsocket的数据库管理器</title>
<script src="http://nws.oss-cn-qingdao.aliyuncs.com/jquery.min.js"></script>
<script src="http://nws.oss-cn-qingdao.aliyuncs.com/bootstrap.min.js"></script>  
<script type="text/javascript" src="<?= base_url('js/swfobject.js')?>"></script>
<script type="text/javascript" src="<?= base_url('js/web_socket.js')?>"></script>
<script type="text/javascript" src="<?= base_url('js/json.js')?>"></script>
<link href="http://nws.oss-cn-qingdao.aliyuncs.com/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .modal{
        position: relative;
        left: 45%;
        width: 750px;
    }
</style>
<script>
if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
WEB_SOCKET_SWF_LOCATION = "swf/WebSocketMain.swf";
WEB_SOCKET_DEBUG = true;
var ws, ping, name = 'null', user_list={};

    // 创建websocket
    ws = new WebSocket("ws://"+document.domain+":8080/");
    
    // 当socket连接打开时，输入用户名
    ws.onopen = function() {  
        <?php if ($db_type == 'MongoDB'): ?>
            ws.send('{"type":"login","name":"<?= $db_username ?>", "group":"WSPDM2_Mongo"}');
        <?php else: ?>
            ws.send('{"type":"login","name":"<?= $db_username ?>", "group":"WSPDM2"}');
        <?php endif;?>
        setInterval("getping()",1000);
    };

    // 当有消息时根据消息类型显示不同信息
    ws.onmessage = function(e) {  
    console.log(e.data);
    var result = JSON.parse(e.data);  
    console.log(result);
    if (result[0] != 'p')
    {
        //alert(result[1]);
    }
    else
    {
        $(".btn").removeAttr("disabled");
        $(".btn").attr("value", "一键配置"); 
    }
    
    switch (result[0])
    {        
        case "p":    //ping
            var date = new Date();
            i = 0;                    
            ping = date.getTime() - ping;
            //alert(date.getTime());
            $("#ping").html("ping:" + ping + "ms");
            break;
                
        case "iframe":
        case "group":     
            //当src为index.php/a 的情况location.href.slice(0, location.href.lastIndexOf("/"))
            if ($("iframe[src='" + location.href.slice(0, location.href.lastIndexOf("/")) + result[1] + "']")[0]){
                $("iframe[src='" + location.href.slice(0, location.href.lastIndexOf("/")) + result[1] + "']")[0].contentWindow.MotherResultRec(result);
            } else {
                //当src为index.php?a&b的情况                
//                $("iframe[src='" + location.href.slice(0, location.href.lastIndexOf("/", location.href.lastIndexOf("/") - 1)) + result[1] + "']")[0].contentWindow.MotherResultRec(result);
                $(".container iframe")[0].contentWindow.MotherResultRec(result);
            }
            break;
        
        case 'rewind_snap':
            //回滚数据库快照操作后广播刷新
            length = $("iframe[src^='" + result[1] + "']").length;
            for (i = 0; i < length; i++){
                $("iframe[src^='" + result[1] + "']")[i].contentWindow.MotherResultRec(result);
            }
            break;
    }

};
ws.onclose = function() {
    console.log("服务端关闭了连接");
};
ws.onerror = function() {
    console.log("出现错误");
};    

</script>
<script>     
//                            alert(location.href.slice(0, location.href.lastIndexOf("/")));
function IframeSend(data, type) { 
    type = arguments[1] ? arguments[1] : "iframe";
    if (!data['group']){
        ws.send('{"type":"' + type + '","api":"' + data['api'] + '","src":"' + data['src'] + '","data":' + data['data'] + '}');
    } else {        
        ws.send('{"type":"' + type + '","api":"' + data['api'] + '","src":"' + data['src'] + '","data":' + data['data'] + ',"group":"' + data['group'] + '"}');
    }
    
}
function getping(){ 
    var date = new Date();
    ping = date.getTime(); 
    ws.send('{"type":"ping"}');
}    

function DeleTable(database, table_name){
//    alert();
<?php if ($db_type == 'MongoDB'): ?>
    $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + table_name + "\"]").remove();
    $(".tabs-selected").remove();
    $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + table_name + "\"]").remove();
<?php else: ?>
    $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + table_name + "\"]").remove();
    $(".tabs-selected").remove();
    $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + table_name + "\"]").remove();
<?php endif;?>
}

function UpdateTableName(database, old_table_name, new_table_name){
//    alert();
//alert("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + old_table_name + "\"]");
    <?php if ($db_type == 'MongoDB'): ?>
        $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + old_table_name + "\"]").html(new_table_name);
        $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + old_table_name + "\"]").attr("src", location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + new_table_name);
        $(".tabs-selected span.tabs-closable").html(new_table_name);
        $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + old_table_name + "\"]").attr("src", location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + new_table_name);
        //强制刷新
        $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + new_table_name + "\"]").attr("src", $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=MongoTableInfo&db=" + database + "&col=" + new_table_name + "\"]").attr('src'));
    
    <?php else:?>     
        $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + old_table_name + "\"]").html(new_table_name);
        $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + old_table_name + "\"]").attr("src", location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + new_table_name);
        $(".tabs-selected span.tabs-closable").html(new_table_name);
        $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + old_table_name + "\"]").attr("src", location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + new_table_name);
        //强制刷新
        $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + new_table_name + "\"]").attr("src", $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + new_table_name + "\"]").attr('src'));
    <?php endif;?>
}

function updateIframe(drop_item){
    $(".container iframe").attr('src', drop_item.attr('src'));
    $(".navbar-collapse").animate({height:"1px"}, 'slow').addClass('collapse').removeClass('in');
}
</script>
<body>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">WSPDM2</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <?php foreach ($db_list as $database => $table): ?>
                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" href="#"><?= $database ?><span class="glyphicon glyphicon-menu-down"></span></a>
                    <ul class="dropdown-menu" role="menu">
                    <?php foreach ($table as $table_name): ?>
                        <?php if ($db_type == 'MongoDB'):?>
                            <li><a src="<?= base_url() ?>index.php?c=MongoTableInfo&db=<?= $database?>&col=<?= $table_name?>" onclick="updateIframe($(this))" href="#"><?= $table_name?></a></li>
                        <?php else:?>
                            <li><a src="<?= base_url() ?>index.php?c=TableInfo&db=<?= $database?>&t=<?= $table_name?>" onclick="updateIframe($(this))" href="#"><?= $table_name?></a></li>
                        <?php endif;?>
                    <?php endforeach; ?>
                    </ul>
                </li>
                <?php endforeach; ?>	
            </ul>            
        </div><!--/.nav-collapse -->
    </div>
</nav>
<div class="container">
    <iframe src="<?= base_url('index.php/BasicDbInfo')?>" frameborder="0" style="position: absolute; height: 92%; top:8%; left:0; border: none;" width="100%">
</div>  
</body>
</html>