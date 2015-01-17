//已登录标示符
var logined = 0;

//动态添加消息
function AddMessageBox() {
    var data = arguments[0] ? arguments[0] : "";
    var color = arguments[1] ? arguments[1] : "white";
    $("#main").append("<br/><div class=\"message_box\" style=\"color:" + color + "\">" + data + "</div>");
}

//动态添加消息
function AddTickerMessageBox() {
    var data = arguments[0] ? arguments[0] : "";
    var color = arguments[1] ? arguments[1] : "white";
    $("#main").append("<br/><div class=\"message_box ticker\" style=\"color:" + color + "\">" + data + "</div>");
}

//动态添加命令输入部分
function AddCommandBox() {
    if (undefined != $.LS.get("user")) {
        user_name = $.LS.get("user_name");
    } else {
        user_name = "GUEST:$";
//        user_name = "lin@lin-SUTACM:~$";
    }

    $("#main").append("<br/><div class=\"command_box\"><div class=\"command_title\"><a>" + user_name + "</a></div><div class=\"command_area\"><input type=\"text\" name=\"command\"></div></div>");
    $("input:last").focus();
}

//动态添加命令输入部分
function AddAnyCommandBox() {
    var title = arguments[0] ? arguments[0] : "";
    var command_function = arguments[1] ? arguments[1] : "";
    var type = arguments[2] ? arguments[2] : "text";
    $("#main").append("<br/><div class=\"command_box\"><div class=\"command_title\" id=\"" + title + "\"><a>" + title + "</a></div><div class=\"command_area\"><input type=\"" + type + "\" func=\"" + command_function + "\" name=\"command\"></div></div>");
    $("input:last").focus();
}


//处理写命令
function ShellCommand(command) {
    command = $.trim(command);
    var shellcommand = new Array();
    shellcommand = command.split(":");
    
    switch (shellcommand[0]) {
        
        //VirtualShell命令集
        case "$vs":
            switch (shellcommand[1]) {
                //清除屏幕
                case "clear":
                    $("#main").empty();
                    AddCommandBox();
                    break;

                    //关闭VirtualShell
                case "bye":
                    AddMessageBox("Virtual Shell will shutdown in 5 seconds", "red");
                    AddMessageBox("Good Bye!");
                    AddMessageBox("Press Ctrl+C to cancel");
                    shutdown = 5;

                    shutdowntimer = setInterval(function () {
                        if ($("input:last").val() != command) {
                            AddMessageBox("Virtual Shell shutdown process canceled");
                            clearInterval(shutdowntimer);
                        }

                        if (shutdown) {
                            shutdown--;
                        } else {
                            AddMessageBox("GoodBye");
                            window.opener = null;
                            window.open('', '_self', '');
                            window.close();
                        }
                    }, 1000);
                    break;

                    //访问github主页
                case "github":
                    window.open("https://github.com/bricksfx/gd_virtual_shell");
                    AddCommandBox();
                    break;

                    //显示VirtualShell指令集
                case "help":
                    AddMessageBox("Welcome to use Virtual Shell", "red");
                    AddMessageBox("$vs:clear - Clean the screen");                    
                    AddMessageBox("$vs:login - Login the server");
                    AddMessageBox("$vs:say - Interactive with other users");
                    AddMessageBox("$vs:clean_history - Clean the command history");
                    AddMessageBox("$vs:github - Visit the project source code in Github");
                    AddMessageBox("$vs:author - Show the authors of the Virtual Shell");
                    AddMessageBox("$vs:bye - Shutdown the Virtual Shell");                   
                    
                    AddCommandBox();
                    break;

                    //显示作者信息
                case "author":
                    AddMessageBox("====================================================", "red");
                    AddMessageBox("■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Virtual Shell&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■");
                    AddMessageBox("■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Shenyang University of Technology&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■", "#00CCFF");
                    AddMessageBox("====================================================", "red");
                    AddMessageBox("■&nbsp;&nbsp;&nbsp;&nbsp;Interface Designer & Websocket Kernel Engineer&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■");
                    AddMessageBox("■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*Chen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■", "#FFFF00");
                    AddMessageBox("====================================================", "red");
                    AddMessageBox("■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;System Interaction API Engineer&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■");
                    AddMessageBox("■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bricks&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■", "#FFFF00");
                    AddMessageBox("====================================================", "red");
                    AddMessageBox("■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Thanks For Using Virtual Shell&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■");
                    AddMessageBox("====================================================", "red");
                    AddCommandBox();
                    break;

                    //用户登录
                case "login":
                    $("#main").empty();
                    AddAnyCommandBox("Login:", "UserName");
                    if (undefined != $.LS.get("user_name")) {
                        $("input:last").val($.LS.get("user_name"));
                    }
                    
                    break;

                    //和其他管理员对话
                case "say":
                    AddAnyCommandBox("say:", "UserSay");
                    break;

                    //清除操作历史记录
                    //推荐退出前清除
                case "clean_history":
                    $.LS.remove("command_stack");
                    if (undefined == $.LS.get("command_stack")) {
                        AddMessageBox("Clean succeed");
                    } else {
                        AddMessageBox("Clean ERROR", "red");
                    }
                    break;
            }
            break;
        default:
            //非命令集，发送到服务器
            return 0;
            break;
    }
    return 1;
}

//获取历史命令
var command_array = new Array();
var now_command = -1;
if (undefined != $.LS.get("command_stack")) {
    command_array = JSON.parse($.LS.get("command_stack"));
    //当前命令
    now_command = command_array.length - 1;
}

//处理上下按键
//获取上命令
//direction = 38; 向上
//direction = 40; 向下
function CommandHistory(direction) {
    switch (direction) {
        case 38:
            if (now_command > 0) {
                $("input:last").val(command_array[now_command--]);
            }
            break;

        case 40:
            if (now_command + 1 <= command_array.length - 1) {
                $("input:last").val(command_array[++now_command]);
            }
            break;
    }
}

//用户登录
function UserName(){
    user_name = $("input:last").val();
    $.LS.set("user_name", user_name);
    AddAnyCommandBox("Password:", "UserPassword", "password");
}

function UserPassword(){
    if (undefined != $.LS.get("user_name")){
        user_name = $.LS.get("user_name");
        ws.send('{"type":"login","name":"' + user_name + '", "password":"' + $("input:last").val() + '", "group":"VirtualShell"}');
    } else {
        AddMessageBox("Undefined User Name", "red");
        AddMessageBox("Access Deined", "red");
        AddAnyCommandBox("Login:", "UserName");
        $("input:last").focus();
        return ;
    }
}

//用户交流
function UserSay(){
    if (undefined != $.LS.get("user_name") && logined){
        user_name = $.LS.get("user_name");
        ws.send('{"type":"say","name":"' + user_name + '","group":"VirtualShell","content":"' + $("input:last").val() + '"}');
    } else {
        AddMessageBox("Please Login First", "red");
        AddAnyCommandBox("Login:", "UserName");
        $("input:last").focus();
        return ;
    }
}