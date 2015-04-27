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
        <div class="panel panel-info">
            <div class="panel-heading">数据库服务器</div>
            <div class="panel-body">
                <p>MongoDB版本：<?= $db_info['version']?></p>
                <p>用户：<?= $this->session->userdata('db_username')?></p>
                <p>系统用户：<?= $db_info['host']?></p>
                <p>配置文件：<?= $db_info['retval']['argv'][0] . '  ' . $db_info['retval']['argv'][1] . '  ' . $db_info['retval']['argv'][2] ?></p>
                <p>Host：<?= $db_info['retval']['parsed']['bind_ip'] ?></p>
                <p>数据存储位置：<?= $db_info['retval']['parsed']['dbpath'] ?></p>
                <p>日志文件位置：<?= $db_info['retval']['parsed']['logpath'] ?></p>
            </div>
        </div>     
        <br/>
        <div class="panel panel-info">
            <div class="panel-heading">其他</div>
            <div class="panel-body">        
                <p>正常运行时间：<?= $db_info['uptime']?>s</p>
                <p>物理内存使用：<?= $db_info['mem']['resident'] ?>M</p>
                <p>机器位数：<?= $db_info['mem']['bits'] ?></p>
                <p>当前活动状态连接数：<?= $db_info['connections']['current'] ?></p>
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
                    var Rewind = new Array();
                    Rewind['src'] = location.href.slice((location.href.lastIndexOf("/")));
                    Rewind['group'] = 'WSPDM2';
                    Rewind['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/TableInfo/B_RewindSnapShot';
                    Rewind['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "snap_type" : "' + data[4]['type'] + '", "database" : "' + data[4]['database'] + '"}';
                    parent.IframeSend(Rewind, 'group'); 
                    break;
                
                case 'B_DeleSnapShot':
                    if ('database' == data[4]['type']){
                        $('#db_snap [file="snap_1_' + data[4]['name'] + '"]').remove();
                    }
                    break;
                    
                case 'B_RewindSnapShot':
                    alert('回滚快照成功，请结束其他未执行的操作并刷新页面以进行深度重载');
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