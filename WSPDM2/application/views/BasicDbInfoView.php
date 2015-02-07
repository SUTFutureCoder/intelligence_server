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
        <div class="panel panel-info">
            <div class="panel-heading">数据库快照</div>
            <div class="panel-body">
                <?php if (count($db_snap)): ?>
                <?php foreach ($db_snap as $db_name => $db_snap_name):?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= $db_name ?></h3>
                    </div>
                    <table class="table table-condensed table-hover" id="db_snap">
                        <?php foreach ($db_snap_name as $db_snap_key => $db_snap_value): ?>
                        <tr file="snap_1_<?= $db_snap_key?>">
                            <td class="col-sm-8"><a><?= $db_snap_key?></a></td>
                            <td class="col-sm-2"><a><?= $db_snap_value?></a></td>
                            <td class="col-sm-1"><button type="button" class="btn btn-danger btn-sm" onclick="snap_dele(1, '<?= $db_name ?>', '<?= $db_snap_key?>')">删除快照</button></td>
                            <td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_rewind(1, '<?= $db_name ?>', '<?= $db_snap_key?>')">恢复</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endforeach; ?>  
                <?php endif; ?>
            </div>
        </div>
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
                <p><a href="https://github.com/SUTFutureCoder/intelligence_server/tree/master/WSPDM2" target="_blank" >项目主页</a></p>
                <p><a href="https://github.com/SUTFutureCoder/WSPDM/wiki/" target="_blank" >wiki</a></p>
            </div>
        </div>  
        <div class="modal fade " id="danger_confirm_modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">确认操作</h4>
                </div>        
                <div class="modal-body" id="danger_confirm_body">     
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>            
                    <button type="button" class="btn btn-danger" onclick="" id="danger_confirm">确认</button>                
                </div>
            </div>
            </div>
        </div> 
    </body>
    <script>
        
        //接收母窗口传来的值
        function MotherResultRec(data) {
            switch (data[3]){
                case 'UpdatePW':
                    alert(data[4]);
                    if (1 == data[2]) {
                        $("form").each(function () {
                            this.reset();
                        });
                        window.parent.window.location.href = '../index.php';
                    }  
                    break;
                    
                case 'DeleSnapShot':
                    $("#danger_confirm_modal").modal('hide');   
                    var delesnapshot = new Array();
                    delesnapshot['src'] = location.href.slice((location.href.lastIndexOf("/")));
                    delesnapshot['group'] = 'WSPDM2';
                    delesnapshot['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/TableInfo/B_DeleSnapShot';
                    delesnapshot['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "snap_type" : "' + data[4]['type'] + '", "snap_name" : "' + data[4]['name'] + '"}';
                    parent.IframeSend(delesnapshot, 'group');  
                    break;
                
                case 'RewindSnapshot':
                    $("#danger_confirm_modal").modal('hide');
                    alert('回滚成功，请结束其他未执行的操作并刷新页面以进行深度重载');

                    break;
                
                case 'B_DeleSnapShot':
                    if ('database' == data[4]['type']){
                        $('#db_snap [file="snap_1_' + data[4]['name'] + '"]').remove();
                    }
                    break;
            }
            
            if (1 != data[2]){
                alert(data[3]);
                if (data[4]) {
                    $("#" + data[4]).focus();
                }
            }
            
            
        }
        
        //密码更新
        function PW_update(){
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href + '/UpdatePW';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "type" : "<?= $type?>", "host" : "<?= $host?>", "port" : "<?= $port?>", "old_pw" : "' + $("#old_pw").val() + '",';
            data['data'] += '"new_pw" : "' + $("#new_pw").val() + '", "new_pw_confirm" : "' + $("#new_pw_confirm").val() + '"}';
            parent.IframeSend(data);
        }
        
        //删除快照
        function snap_dele(type, database, name){
            $("#danger_confirm").removeAttr('disabled');
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">删除快照</a>操作吗？</h4>');
            $("#danger_confirm").attr('onclick', 'snap_dele_exec(' + type + ', "' + database + '", "' + name + '")');
            $("#danger_confirm_modal").modal('show');
        }

        //执行删除快照
        function snap_dele_exec(type, database, name){
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/TableInfo/DeleSnapshot';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"snap_type" : "' + type + '", "db_type" : "<?= $type?>", "database" : "' + database + '", "snap_name" : "' + name + '"}';
            parent.IframeSend(data);
        }
        
        
        //回滚快照
        function snap_rewind(type, database, name){
            $("#danger_confirm").removeAttr('disabled');
            $("#danger_confirm").html('确认');
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">回滚快照</a>操作吗？</h4>');
            $("#danger_confirm").attr('onclick', 'snap_rewind_exec(' + type + ', "' + database + '", "' + name + '")');
            $("#danger_confirm_modal").modal('show');
        }

        //执行回滚快照
        function snap_rewind_exec(type, database, name){
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/TableInfo/RewindSnapshot';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"snap_type" : "' + type + '", "db_type" : "<?= $type?>", "database" : "' + database + '", "snap_name" : "' + name + '"}';
            parent.IframeSend(data);
        }
    </script>
</html>