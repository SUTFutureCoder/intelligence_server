//初始界面
$(function(){
    AddMessageBox("Welcome to use the virtual shell");
    AddMessageBox("(C) SUT-ACM 2014-2015 *Chen & Bricks");
    AddMessageBox("USE $vs:help to get more information");
    AddCommandBox();
    $("input:last").focus();
})


//设定按键部分 
$(function(){
    $(this).keydown(function(e){
        //任意键聚焦
        $("input:last").focus();        
        //按下回车
        if (e.which == 13){
            $("input").attr("disabled", "disabled");
            //记录命令历史
            if (logined){
                if (undefined != $.LS.get("command_stack")){
                    now_command = command_array.length;
                }
                if (now_command != -1){      
                    command_array.push($("input:last").val());
                    now_command++;
                } else {
                    now_command++;                    
                    command_array[now_command] = $("input:last").val();
                }
                
                $.LS.set("command_stack", JSON.stringify(command_array));               
                
            }
            
            //检测是否有自定义函数
            if ('' !== $("input:last").attr("func") && undefined != $("input:last").attr("func")){     
                eval($("input:last").attr("func") + '()');
            } else {                
                //匹配VirtualShell指令集
                if (!ShellCommand($("input:last").val())){
                    //发送命令
                    ws.send('{"type":"shell","command":"' + $("input:last").val() + '"}');
                }
            }
            
            $("input:last").focus();
            //逻辑运算
        }
        
        //ctrl+C
        if (e.ctrlKey && e.which == 67){
            $("input").attr("disabled", "disabled");
            AddCommandBox();
            $("input:last").focus();           
            
            
            //判断是否正在执行
            if (1){
                ws.send('{"type":"shell","command":"!"}');
            }
        }
        
        //上下键
        if ((e.which == 38 || e.which == 40) && logined){
            CommandHistory(e.which);
        }
        
    })
 })