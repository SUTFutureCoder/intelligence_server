<!DOCTYPE html>  
<html>  
    <head>  
        <title></title>
        <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
        <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">         
    </head>
    <body>
        <br/>
        <?php if ('MySQL' == $type): ?>
        <div class="modal fade modal-passupdate" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">修改密码</h4>
                    </div>
                    <div class="modal-body">  
                        <form role="form">
                            <div class="form-group">
                                <label for="old_pw">旧密码</label>
                                <input type="password" class="form-control" id="old_pw">
                            </div>
                            <div class="form-group">
                                <label for="new_pw">新密码</label>
                                <input type="password" class="form-control" id="new_pw" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label for="new_pw_confirm">新密码重复</label>
                                <input type="password" class="form-control" id="new_pw_confirm" placeholder="Password">
                            </div>                            
                        </form>
                    </div>
                    <div class="modal-footer">      
                        <button type="button" onclick="PW_update()" class="btn btn-danger">修改</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">常规设置</div>
            <div class="panel-body">
                <p><a data-toggle="modal" data-target=".modal-passupdate">修改密码</a></p>
            </div>
        </div> 
        <?php endif; ?>
        <br/>
        <div class="panel panel-info">
            <div class="panel-heading">数据库服务器</div>
            <div class="panel-body">
                <p>服务器版本：<?= $db_info['VERSION']?></p>
                <p>协议版本：<?= $db_info['PROTOCOL_VERSION']?></p>
                <p>用户：<?= $this->session->userdata('db_username')?></p>
                <p>默认存储引擎：<?= $db_info['DEFAULT_STORAGE_ENGINE']?></p>
                <p>存储引擎：<?= $db_info['STORAGE_ENGINE']?></p>
            </div>
        </div>         
        <br/>
        <div class="panel panel-info">
            <div class="panel-heading">网站服务器</div>
            <div class="panel-body">
                <p>系统字符集：<?= $db_info['CHARACTER_SET_SYSTEM']?></p>
                <p>编译环境：<?= $db_info['VERSION_COMPILE_OS']?></p>
                <p>编译机系统：<?= $db_info['VERSION_COMPILE_OS'] . $db_info['VERSION_COMPILE_MACHINE']?></p>
                <p>连接器位置：<?= $db_info['SOCKET']?></p>
                <p>日志位置：<?= $db_info['GENERAL_LOG_FILE']?></p>
            </div>
        </div>         
        <div class="panel panel-info">
            <div class="panel-heading">WSPDM</div>
            <div class="panel-body">
                <p><a href="https://github.com/SUTFutureCoder/WSPDM" target="_blank" >项目主页</a></p>
                <p><a href="https://github.com/SUTFutureCoder/WSPDM/wiki/" target="_blank" >wiki</a></p>
            </div>
        </div>          
    </body>
    <script>
        
        //接收母窗口传来的值
        function MotherResultRec(data) {
            alert(data[3]);
            if (1 == data[2]) {
                $("form").each(function () {
                    this.reset();
                });
                window.parent.window.location.href = '../index.php';
            }            
            if (data[4]) {
                $("#" + data[4]).focus();
            }
        }
        function PW_update(){
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href + '/UpdatePW';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "type" : "<?= $type?>", "host" : "<?= $host?>", "port" : "<?= $port?>", "old_pw" : "' + $("#old_pw").val() + '",';
            data['data'] += '"new_pw" : "' + $("#new_pw").val() + '", "new_pw_confirm" : "' + $("#new_pw_confirm").val() + '"}';
            parent.IframeSend(data);
        }
    </script>
</html>