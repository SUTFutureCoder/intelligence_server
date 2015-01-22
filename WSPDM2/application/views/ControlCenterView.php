<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>WSPDM-基于PHPWebsocket的数据库管理器</title>
<script src="http://libs.baidu.com/jquery/1.7.2/jquery.min.js"></script>
<script src="http://libs.baidu.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= base_url('jq-ui/jquery.easyui.min.js')?>"></script>
<script type="text/javascript" src="<?= base_url('js/swfobject.js')?>"></script>
<script type="text/javascript" src="<?= base_url('js/web_socket.js')?>"></script>
<script type="text/javascript" src="<?= base_url('js/json.js')?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('jq-ui/themes/cupertino/easyui.css')?>" id="swicth-style">
<link rel="stylesheet" type="text/css" href="<?= base_url('jq-ui/style.css')?>" id="swicth-style">
<link rel="stylesheet" type="text/css" href="http://libs.baidu.com/bootstrap/2.3.2/css/bootstrap.min.css">

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
        ws.send('{"type":"login","name":"DBA", "group":"WSPDM2"}');
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
                $("iframe[src='" + location.href.slice(0, location.href.lastIndexOf("/", location.href.lastIndexOf("/") - 1)) + result[1] + "']")[0].contentWindow.MotherResultRec(result);
            }
            //当src为index.php?a&b的情况
            //$("iframe[src='" + location.href.slice(0, location.href.lastIndexOf("/", location.href.lastIndexOf("/") - 1)) + result[1] + "']")[0].contentWindow.MotherResultRec(result);
            //当src为index.php/a 的情况
            //$("iframe[src='" + location.href.slice(0, location.href.lastIndexOf("/")) + result[1] + "']")[0].contentWindow.MotherResultRec(result);
            /*if ($("iframe[src='" + result[1] + "']"))
            {
                alert($("iframe[src='" + result[1] + "']").attr('scrolling'));
            }*/
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
//    var test = {}; 
//    test.type = "ping";
//    string_test = '{"type":"ping"}';     
//    ws.send(JSON.stringify(test));
//alert(JSON.stringify({"type":"ping"}));
    ws.send('{"type":"ping"}');
}    

function DeleTable(database, table_name){
//    alert();
    $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + table_name + "\"]").remove();
    $(".tabs-selected").remove();
    $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + table_name + "\"]").remove();
}

function UpdateTableName(database, old_table_name, new_table_name){
//    alert();
//alert("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + old_table_name + "\"]");
    $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + old_table_name + "\"]").html(new_table_name);
    $("a[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + old_table_name + "\"]").attr("src", location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + new_table_name);
    $(".tabs-selected span.tabs-closable").html(new_table_name);
    $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + old_table_name + "\"]").attr("src", location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + new_table_name);
    //强制刷新
    $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + new_table_name + "\"]").attr("src", $("iframe[src=\"" + location.href.slice(0, location.href.lastIndexOf("/")) + "?c=TableInfo&db=" + database + "&t=" + new_table_name + "\"]").attr('src'));
}
</script>
<body class="easyui-layout">

<div region="north" border="false" class="cs-north" style="height:30px; overflow:hidden">
    <div  style="height: 30px; top:5px; overflow: hidden; position: relative; left: 10px; float: left">
        <a href="javascript:void(0);" src="<?= base_url('index.php/daily_me')?>" class="cs-navi-tab"></a>
    </div>
    <div class="cs-north-bg"style="top:0%" >                
    <ul class="ui-skin-nav">	                    
            <li class="li-skinitem"><a class="cs-navi-tab badge badge-info" href="javascript:void(0);" src="index.php/daily_message" id="ping">正在加载</a></li>
            <li class="li-skinitem" title="gray"><span class="gray" rel="gray"></span></li>
            <li class="li-skinitem" title="pepper-grinder"><span class="pepper-grinder" rel="pepper-grinder"></span></li>
            <li class="li-skinitem" title="blue"><span class="blue" rel="blue"></span></li>
            <li class="li-skinitem" title="cupertino"><span class="cupertino" rel="cupertino"></span></li>
            <li class="li-skinitem" title="dark-hive"><span class="dark-hive" rel="dark-hive"></span></li>
            <li class="li-skinitem" title="sunny"><span class="sunny" rel="sunny"></span></li>
    </ul>	
    </div>
</div>
<div region="west" border="true" split="true" title="索引" class="cs-west">
        <div class="easyui-accordion" fit="false" border="false">
            <?php foreach ($db_list as $database => $table): ?>
                <div title="<?= $database?>">
                    <?php foreach ($table as $table_name): ?>
                        <a href="javascript:void(0);"  src="<?= base_url() ?>index.php?c=TableInfo&db=<?= $database?>&t=<?= $table_name?>" class="cs-navi-tab"><?= $table_name?></a></p>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>				
        </div>
</div>
<div id="mainPanle" region="center" border="true" border="false">
    <div id="tabs" class="easyui-tabs"  fit="true" border="false" >
        <div title="总览">
            <iframe src="<?= base_url('index.php/BasicDbInfo')?>" width="100%" height="100%" allowTransparency="true" frameBorder="0" scrolling="no">
        </div>
    </div>
</div>

<div region="south" border="false" class="cs-south">WSPDM ©沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen</div>

<div id="mm" class="easyui-menu cs-tab-menu">
        <div id="mm-tabupdate">刷新</div>
        <div class="menu-sep"></div>
        <div id="mm-tabclose">关闭</div>
        <div id="mm-tabcloseother">关闭其他</div>
        <div id="mm-tabcloseall">关闭全部</div>
</div>        
</body>
</html>