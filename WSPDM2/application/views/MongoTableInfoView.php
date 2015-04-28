<!DOCTYPE html>  
<html>  
    <head>  
        <title></title>
        <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">   
        <script>
            //预定义全局变量用于图表显示，图表显示和执行SQL结果一致
            //也用于修改时的面板生成
            var chart_data = new Array();
            chart_data['cols'] = new Array();
            chart_data['data'] = new Array();
        </script>
    </head>
    <body>
        <br/>
        <?php if (!$data):?>
        <div class="alert alert-danger" role="alert" id="alert">未操作</div>
        <?php else: ?>
        <div class="alert alert-success" role="alert" id="alert">
            <p>正在显示第<?= $data['start'] ?>-<?= $data['start'] + $data['limit'] ?>(共操作<?= $data['rows'] ?>行，操作消耗<?= $data['time'] ?>秒)</p>
            <p><?= $data['command']?></p>
        </div>
        <?php endif; ?>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#view" role="tab" data-toggle="tab">浏览&nbsp;
                <div class="btn-group  btn-group-xs" role="group" aria-label="...">
                    <button type="button" class="btn btn-default" id="view_tab_button_array">Array</button>
                    <button type="button" class="btn btn-default" id="view_tab_button_json">Json</button>
                </div></a></li>
            <li role="presentation"><a href="#struct" role="tab" data-toggle="tab">结构</a></li>
            <li role="presentation"><a href="#js" id="sql_tab" role="tab" data-toggle="tab">JavaScript&nbsp;
                <div class="btn-group  btn-group-xs" role="group" aria-label="...">
                    <button type="button" class="btn btn-default" id="js_tab_button_array">Array</button>
                    <button type="button" class="btn btn-default" id="js_tab_button_json">Json</button>
                </div></a></li>
            <li role="presentation"><a href="#insert" role="tab" data-toggle="tab">插入</a></li>
            <li role="presentation"><a href="#search" role="tab" data-toggle="tab">搜索</a></li>
            <li role="presentation"><a href="#operating" role="tab" data-toggle="tab">操作</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active col-lg-11 col-lg-offset-1" id="view">
                <br/>
                <br/>
                <?php foreach($data['data'] as $value): ?>
                <div class="panel panel-default" id="data_<?= $value['_id'] ?>">
                    <div class="panel-heading"><?= $data['data_sum']-- ?></div>
                    <div class="panel-body">
                        <pre class="view_value_array"><?= print_r($value, TRUE) ?></pre>
                        <pre class="view_value_json"><?= print_r(json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), TRUE) ?></pre>
                    </div>
                    <div class="panel-footer">
                        <button type="button" class="btn btn-primary btn-xs" onclick="data_update_button(0, <?= $value['_id'] ?>)">更新</button>
                        <button type="button" class="btn btn-danger btn-xs" onclick="data_dele_button(0, <?= $value['_id'] ?>)">删除</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="struct">
                <br/>
                <table class="table table-hover table-bordered" id="struct_view">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>操作</th>
                            <th>名字</th>
                        </tr>
                    </thead>
                    <tbody>                        
                    <?php $i = 0; ?>
                    <?php foreach ($data['cols'] as $col_name): ?>                    
                        <tr id="struct_view_col_<?=$col_name?>">
                            <td><?= ++$i ?></td>
                            <td>
                            <?php if ($col_name != '_id'): ?>
                            <a onclick="dele_col_name('<?=$col_name?>')" style="color:red">删除</a>
                            <?php endif; ?>
                            </td>
                            <td><?= $col_name ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="js">
                <br/>
                <div class="col-sm-8">
                    <textarea class="form-control" rows="5" id="sql_area"></textarea>
                    <br/>                    
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="memcache"> memcache缓存查询结果
                        </label>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="launch_sql()">执行</button>
                </div>
                <div class="col-sm-4">
                    <table class="table table-hover table-bordered" id="sql_table_list">                       
                        <tbody>                       
                        <?php foreach ($data['cols'] as $col_name): ?>                    
                            <tr id="sql_col_name_<?= $col_name?>">
                                <td onclick="sql_button(' <?= $col_name ?> ', 1)"><a><?= $col_name ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <hr>
                <div id="sql_result">
                    
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="insert">                
                <table class="table table-hover table-bordered" >                       
                    <tbody>  
                        <form role="form" id="insert_list">
                        <?php foreach ($data['cols'] as $col_name): ?>  
                            <?php if($col_name == '_id'): 
                                continue;
                            endif; ?>
                        <tr id="insert_<?= $col_name ?>">
                            <td><?= $col_name ?>
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                <div class="form-group">                                    
                                    <textarea class="form-control" name="<?= $col_name ?>" rows="2" id="insert_<?= $col_name ?>_val"></textarea>                                    
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </form>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary btn-lg btn-block" onclick="insert()">插入</button>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="search">
                <table class="table table-hover table-bordered" id="search_panel"> 
                    <thead>
                        <tr>
                            <td>字段</td>
                            <td>运算符</td>
                            <td>值</td>
                        </tr>
                    </thead>
                    <tbody>                       
                    <?php foreach ($data['cols'] as $col_name): ?>                    
                        <tr id="search_col_<?= $col_name ?>">
                            <td class="search_col_name"><?= $col_name ?></td>
                            <td><select class="form-control search-form-select">
                                    <option value="LIKE">LIKE</option>
                                    <option value="LIKE %...%">LIKE %...%</option>
                                    <option value="NOT LIKE">NOT LIKE</option>
                                    <option value="=">=</option>
                                    <option value="!=">!=</option>
                                    <option value="= ''">= ''</option>
                                    <option value="!= ''">!= ''</option>
                                    <option value="IN (...)">IN (...)</option>
                                    <option value="NOT IN (...)">NOT IN (...)</option>
                                    <option value="BETWEEN">BETWEEN</option>
                                    <option value="NOT BETWEEN">NOT BETWEEN</option>
                                    <option value="IS NULL">IS NULL</option>
                                    <option value="IS NOT NULL">IS NOT NULL</option>
                                </select></td>
                            <td><form role="form">
                                <div class="form-group">
                                  <input type="text" id="search_value_<?= $col_name ?>" class="form-control search-form-val">
                                </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" onclick="search()" class="btn btn-primary btn-lg btn-block">搜索</button>
                <hr>
                <div id="search_result">
                    
                </div>
            </div>            
            <div role="tabpanel" class="tab-pane fade" id="operating">                
                <br/>                
                <div class="panel panel-warning">
                    <div class="panel-heading">修改集合名</div>
                    <div class="panel-body">
                        <form role="form">
                            <div class="form-group">
                              <input type="text" id="new_table_name" class="form-control">
                            </div>                            
                        </form>
                        <button type="button" class="btn btn-info"  onclick="rename_table()">修改集合名</button>
                    </div>
                </div>
                <div class="panel panel-danger">
                    <div class="panel-heading">危险地带</div>
                    <div class="panel-body">
                        <button type="button" class="btn btn-warning col-sm-offset-5"  onclick="truncate_table()">清除集合</button>
                        <button type="button" class="btn btn-danger col-sm-offset-11"  onclick="dele_table()">删除集合</button>
                    </div>
                </div>  
            </div>
        </div>
        <div class="modal fade " id="data_update_modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="data_update_title"></h4>
                </div>        
                <div class="modal-body" id="data_update_body">     
                    <form role="form" id="data_update_list">
                    <table class="table table-hover table-bordered">
                    </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>            
                    <button type="button" class="btn btn-danger" id="data_update_confirm">确认</button>                
                </div>
            </div>
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
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <script src="<?= base_url('js/localstorage.js') ?>"></script>
    <script>        
        //接收母窗口传来的值
        function MotherResultRec(data) {
            if (1 == data[2]) {
                if ('iframe' == data[0]){
                    $("#alert").removeClass("alert-danger");
                    $("#alert").addClass("alert-success");
                    if (undefined != data[4]['rows']){
                        $("#alert").html("<p>共操作" + data[4]['rows'] + "行，操作消耗" + data[4]['time'] + "秒<p>" + "<p>" + data[4]['sql'] + "<p>");
                    } else {
                        $("#alert").html("<p>操作消耗" + data[4]['time'] + "秒<p>" + "<p>" + data[4]['sql'] + "<p>");
                    }
                    
                    switch (data[3]){
                        case 'ExecSQL':
                            $("#sql_result").html("");
                            $("#sql_result").append("<br/><table class=\"table table-hover table-bordered\" id=\"sql_data_view\">");
                            $("#sql_data_view").append("<thead><tr id=\"sql_exec_col_name\"><th>#</th>");
                            
                            if (data[4]['sql'].substr(0, 6) != 'SELECT' && data[4]['sql'].substr(0, 6) != 'select'){
                                sql = data[4]['sql'];
                                col = data[4]['rows'];

                                var data = new Array();
                                data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                                data['group'] = 'WSPDM2_Mongo';
                                data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_ReFreshTable';
                                data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "sql" : "' + sql + '", "col" : "' + col + '"}';
                                parent.IframeSend(data, 'group');    
                                break;
                            } else {
                                //取出字段
                                if (!data[4]['cols'].length){
                                    return 0;
                                } else {
                                    $.each(data[4]['cols'], function (col_id, col_name){
                                        $("#sql_data_view thead tr").append("<th>" + col_name + "</th>");
                                    });
                                    $("#sql_data_view").append("</thead><tbody>");                        
                                    //取出数据
                                    $.each(data[4]['data'], function (i, data_item){
            //                            console.log(data_item);
                                        $("#sql_data_view tbody").append("<tr id=sql_exec_" + i + "><td>" + (i + 1) + "<button type=\"button\" class=\"btn btn-primary btn-xs\" onclick=\"data_update_button(1, " + i + ")\">修改</button><button type=\"button\" class=\"btn btn-danger btn-xs\" onclick=\"data_dele_button(1, " + i + ")\">删除</button></td></tr>");
                                        $.each(data_item, function (m, data_item_val){
            //                                console.log(data_item_val);
                                            $("#sql_data_view tbody #sql_exec_" + i).append("<td>" + data_item_val + "</td>");
                                        })   
                                    })
                                    $("#sql_data_view").append("</tbody></table>");
                                }
                                
                                //准备显示图表
                                chart_data['cols'] = data[4]['cols'];
                                chart_data['data'] = data[4]['data'];
                            }
                            break;    

                        case 'DeleCol':
                            var col_id = $("#struct_view_col_" + data[4]['col_name']).prevAll().length;
                            var col_name = data[4]['col_name'];
                            $("#struct_view_col_" + col_name).remove();

                            //从1开始计
                            $("#data_view tr td:nth-of-type(" + (2 + col_id) + ")").remove();

                            $("#insert_" + col_name).remove();

                            $("#sql_col_name_" + col_name).remove();

                            $("#search_col_" + col_name).remove();
                            //开始广播

                            var data = new Array();
                            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data['group'] = 'WSPDM2_Mongo';
                            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_DeleCol';
                            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "col_name" : "' + col_name + '"}';
                            parent.IframeSend(data, 'group');                      

                            break;
                            
                        case 'InsertData':
                            
                            if ($("#data_view tbody tr").length){
                                last_id = $("#data_view tbody tr:last-child td:nth-of-type(" + (1) + ")").html();
                            } else {
                                last_id = 0;
                            }
                            
                            $("#data_view tbody").append("<tr><td>" + (++last_id) + "</td></tr>");
                            
                            td_num = $("#data_view thead tr th").length - 1;
                            
                            for (i = 0; i < td_num; i++){
                                $("#data_view tbody tr:last-child").append("<td></td>");
                            }
                            $.each(data[4]['data']['data'][0], function(col_name, value){
                                col_num = $("#data_view_" + col_name).prevAll().length;
                                $("#data_view tbody tr:last-child td:nth-of-type(" + (1 + col_num) + ")").html(value);
                            });  
                            
                            $("#insert_list")[0].reset();
                            break;
                            
                        case 'SearchData':
                            $("#search_result").html("");
                            $("#search_result").append("<br/><table class=\"table table-hover table-bordered\" id=\"search_data_view\">");
                            $("#search_data_view").append("<thead><tr><th>#</th>");
                            //取出字段
                            $.each(data[4]['cols'], function (i, col_name){
                                $("#search_data_view thead tr").append("<th>" + col_name + "</th>");
                            });
                            $("#search_data_view").append("</thead><tbody>");                        
                            //取出数据
                            $.each(data[4]['data'], function (i, data_item){
    //                            console.log(data_item);
                                $("#search_data_view tbody").append("<tr id=" + i + "><td>" + i + "</td></tr>");
                                $.each(data_item, function (m, data_item_val){
    //                                console.log(data_item_val);
                                    $("#search_data_view tbody #" + i).append("<td>" + data_item_val + "</td>");
                                })   
                            })
                            $("#search_data_view").append("</tbody></table>");    
                            break;
                            
                        case 'DeleTable':
                            $("#danger_confirm_modal").modal('hide');
                            alert('删除成功');
                            var data = new Array();
                            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data['group'] = 'WSPDM2_Mongo';
                            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_DeleTable';
                            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "collection" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>"}';
                            parent.IframeSend(data, 'group');    
                            break;
                            
                        case 'TruncateTable':
                            $("#danger_confirm_modal").modal('hide');
                            alert('清除成功');
                            var data = new Array();
                            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data['group'] = 'WSPDM2_Mongo';
                            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_TruncateTable';
                            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "table" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>"}';
                            parent.IframeSend(data, 'group');    
                            break;
                            
                        case 'RenameTable':
                            $("#danger_confirm_modal").modal('hide');
                            alert('修改成功');
                            var data_rename = new Array();
                            data_rename['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data_rename['group'] = 'WSPDM2_Mongo';
                            data_rename['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_RenameTable';
                            data_rename['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "database" : "<?= $data['database'] ?>", "old_table_name" : "' + data[4]['old_table_name'] + '", "new_table_name" : "' + data[4]['new_table_name'] + '"}';
                            parent.IframeSend(data_rename, 'group');    
                            break;
                            
                        case 'UpdateData':
                            $("#data_update_modal").modal('hide');   
                            update_data_display();
                            break;
                            
                        case 'DeleData':
                            $("#danger_confirm_modal").modal('hide');
                            dele_data_display();
                            break;
                            
                        case 'SnapShot':
                            var snapshot = new Array();
                            snapshot['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            snapshot['group'] = 'WSPDM2_Mongo';
                            snapshot['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_SnapShot';
                            snapshot['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "snap_type" : "' + data[4]['type'] + '", "snap_name" : "' + data[4]['name'] + '", "snap_size" : "' + data[4]['size'] + '"}';
                            parent.IframeSend(snapshot, 'group');  
                            break;
                            
                        case 'DeleSnapShot':
                            $("#danger_confirm_modal").modal('hide');  
                            var delesnapshot = new Array();
                            delesnapshot['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            delesnapshot['group'] = 'WSPDM2_Mongo';
                            delesnapshot['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_DeleSnapShot';
                            delesnapshot['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "snap_type" : "' + data[4]['type'] + '", "snap_name" : "' + data[4]['name'] + '"}';
                            parent.IframeSend(delesnapshot, 'group');  
                            break;
                            
                        case 'RewindSnapshot':
                            $("#danger_confirm_modal").modal('hide');
                            alert('回滚成功，请结束其他未执行的操作并刷新页面以进行深度重载');                            
                            var Rewind = new Array();
                            Rewind['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            Rewind['group'] = 'WSPDM2_Mongo';
                            Rewind['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_RewindSnapShot';
                            Rewind['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "snap_type" : "' + data[4]['type'] + '", "database" : "' + data[4]['database'] + '", "table" : "<?= $data['collection'] ?>"}';
                            parent.IframeSend(Rewind, 'group'); 
                            break;
                    }
                    //重置表单
                    $("form").each(function () {
                        this.reset();
                    });              
                } else if('rewind_snap' == data[0]){
                    //快照回滚广播
                    $("#alert").removeClass("alert-success");
                    $("#alert").addClass("alert-danger");
                    $("#alert").append("<br/>警告！数据因回滚快照而发生更改！<br/><br/><button type=\"button\" class=\"btn btn-success\" onclick=\" window.location.href='" + location.href + "'\">重新加载以消除脏数据</button>");
                } else {
                //广播接收
                    switch (data[3]){  
                        case 'B_ReFreshTable':
                            if ('<?= $user_name ?>' != data[4]['user_name']){
                                $("#alert").removeClass("alert-success");
                                $("#alert").addClass("alert-danger");
                                $("#alert").append("<br/>警告！数据由其他用户发生更改！<br/>使用SQL语句" + data[4]['sql'] + "<br/>影响行数" + data[4]['col'] + "<br/><br/><button type=\"button\" class=\"btn btn-success\" onclick=\" window.location.href='" + location.href + "'\">重新加载以消除脏数据</button>");
                            }
                            break;
                            
                        case 'B_DeleCol':
                            if ($("#struct_view_col_" + data[4]).length){
                                var col_id = $("#struct_view_col_" + data[4]).prevAll().length;
                                $("#struct_view_col_" + data[4]).remove();

                                //从1开始计
                                $("#data_view tr td:nth-of-type(" + (2 + col_id) + ")").remove();
                                $("#insert_" + data[4]).remove();
                                $("#sql_col_name_" + data[4]).remove();
                                $("#search_col_" + data[4]).remove();
                            }
                            break;        
                            
                        case 'B_DeleTable':
                            parent.DeleTable(data[4]['database'], data[4]['table']);
                            break;
                        
                        case 'B_TruncateTable':
                            $("#data_view tbody").empty();
                            break;
                            
                        case 'B_RenameTable':
                            parent.UpdateTableName(data[4]['database'], data[4]['old_table_name'], data[4]['new_table_name']);
                            break;
                            
                        case 'B_DeleSnapShot':
                            if ('table' == data[4]['type']){
                                $('#table_snap [file="snap_0_' + data[4]['name'] + '"]').remove();
                            } else {
                                $('#db_snap [file="snap_1_' + data[4]['name'] + '"]').remove();
                            }
                            break;
                            
                        case 'B_SnapShot':
                            if ('table' == data[4]['type']){                                
                                $("#table_snap").append('<tr file="snap_0_' + data[4]['name'] + '"><td class="col-sm-8"><a>' + data[4]['name'] + '</a></td><td class="col-sm-2"><a>' + data[4]['size'] + '</a></td><td class="col-sm-1"><button type="button" class="btn btn-danger btn-sm" onclick="snap_dele(0, \'' + data[4]['name'] + '\')">删除快照</button></td><td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_rewind(0, \'' + data[4]['name'] + '\')">恢复</button></td></tr>');
                            } else {                                
                                $("#db_snap").append('<tr file="snap_1_' + data[4]['name'] + '"><td class="col-sm-8"><a>' + data[4]['name'] + '</a></td><td class="col-sm-2"><a>' + data[4]['size'] + '</a></td><td class="col-sm-1"><button type="button" class="btn btn-danger btn-sm" onclick="snap_dele(1, \'' + data[4]['name'] + '\')">删除快照</button></td><td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_rewind(1, \'' + data[4]['name'] + '\')">恢复</button></td></tr>');
                            }
                            break;
                    }
                }
                
            } else {
                $("#alert").removeClass("alert-success");
                $("#alert").addClass("alert-danger");
                $("#alert").html("未操作");
                alert(data[3]);
            }
             
        }       
        
        
        //切换显示方式
        $(function(){
            //LocalStorage
            var view = ''
            if (view = $.LS.get('WSPDM2_mongo_view')){
                if (view == 'array'){
                    $(".view_value_array").removeAttr('hidden');
                    $(".view_value_json").attr('hidden', 'hidden');
                } else if (view == 'json'){
                    $(".view_value_json").removeAttr('hidden');
                    $(".view_value_array").attr('hidden', 'hidden');
                }
            } else {
                $.LS.set('WSPDM2_mongo_view', 'array');
                $(".view_value_array").removeAttr('hidden');
                $(".view_value_json").attr('hidden', 'hidden');
            }
            
            //用户选择方式
            $("#view_tab_button_array").click(function(){
                $.LS.set('WSPDM2_mongo_view', 'array');
                $(".view_value_array").removeAttr('hidden');
                $(".view_value_json").attr('hidden', 'hidden');
            });
            
            $("#view_tab_button_json").click(function(){
                $.LS.set('WSPDM2_mongo_view', 'json');
                $(".view_value_json").removeAttr('hidden');
                $(".view_value_array").attr('hidden', 'hidden');
            });
        });
        
        //用于JQuery在光标位置插入内容
        //http://www.poluoluo.com/jzxy/201110/144708.html
        (function($){
            $.fn.extend({
                insertAtCaret: function(myValue){
                    var $t=$(this)[0];
                    if (document.selection) {
                        this.focus();
                        sel = document.selection.createRange();
                        sel.text = myValue;
                        this.focus();
                    }
                    else
                    if ($t.selectionStart || $t.selectionStart == '0') {
                        var startPos = $t.selectionStart;
                        var endPos = $t.selectionEnd;
                        var scrollTop = $t.scrollTop;
                        $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
                        this.focus();
                        $t.selectionStart = startPos + myValue.length;
                        $t.selectionEnd = startPos + myValue.length;
                        $t.scrollTop = scrollTop;
                    }
                    else {
                        this.value += myValue;
                        this.focus();
                    }
                }
            })
        })(jQuery);
        
        //SQL输入框按钮
        //@param 
        //sql 准备执行的SQL语句
        //mode 插入到最前还是就地插入
        function sql_button(sql, mode){
            $("#sql_area").insertAtCaret(sql);
            $("#sql_area").focus();
        }
        
        //执行SQL语句
        function launch_sql(){
            switch ($("#memcache").prop('checked')){
                case true:
                    memcache = 1;
                    break;
                case false:
                    memcache = 0;
                    break;
            }
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/ExecSQL';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"sql" : "' + $("#sql_area").val() + '", "memcache" : "' + memcache + '", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //删除字段
        function dele_col_name(col_name){
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/DeleCol';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"col_name" : "' + col_name + '", "database" : "<?= $data['database'] ?>", "table" : "<?= $data['collection']?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //添加数据
        function insert(){
            //序列化表单
            var values = $("#insert_list").serializeArray();
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['group'] = 'WSPDM2_Mongo';
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/InsertData';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "database" : "<?= $data['database'] ?>", "table" : "<?= $data['collection'] ?>", "data" : {';
            $.each(values, function(i, field){
                if (0 != i){
                    data['data'] += ', ';
                }
                data['data'] += '"' + field.name + '":"' + field.value + '"';
            })
            data['data'] += '}, "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data, 'group');
        }
        
        //搜索
        function search(){
            select = new Array;
            
            i = 0;
            col_name = new Array;
            $(".search_col_name").each(function(){
                col_name[i] = $(this).html();
                i++;
            });
            
            i = 0;
            $(".search-form-select").each(function(){
                select[i] = $(this).val();
                i++;
            });
                       
            
            form_data = new Array;
                        
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/SearchData';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "database" : "<?= $data['database'] ?>", "table" : "<?= $data['collection'] ?>", "data" : {';            
            
            i = 0;
            n = 0;
            $(".search-form-val").each(function(){
                if ($(this).val() != '' || select[n] == "= ''" || select[n] == "!= ''" || select[n] ==  "IS NULL" || select[n] ==  "IS NOT NULL"){
                    if (0 != i){
                        data['data'] += ', ';                        
                    }
                    data['data'] += '"' + i + '" : {"col":"' + col_name[n] + '", "cmd":"' + select[n] + '", "val":"' + $(this).val() + '"}';
                    i++;
                }
                n++;
            })                     
            data['data'] += '},"db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
            
            col_name = form_data = select =  null;
        }
        
        //删除表
        function dele_table(){
            $("#danger_confirm").removeAttr('disabled');
            $("#danger_confirm").html('确认');
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">删除集合</a>操作吗？</h4>');
            $("#danger_confirm").attr('onclick', 'dele_table_exec()');
            $("#danger_confirm_modal").modal('show');
        }
        
        //执行删除表
        function dele_table_exec(){
            $("#danger_confirm").html('<span class="glyphicon glyphicon-flash" aria-hidden="true"></span>正在处理中，请稍候...');
            $("#danger_confirm").attr('disabled', 'disabled');
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/DeleTable';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"collection" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //清除表
        function truncate_table(){
            $("#danger_confirm").removeAttr('disabled');
            $("#danger_confirm").html('确认');
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">清除表</a>操作吗？</h4>');
            $("#danger_confirm").attr('onclick', 'truncate_table_exec()');
            $("#danger_confirm_modal").modal('show');
        }
        
        //执行清除表
        function truncate_table_exec(){
            $("#danger_confirm").html('<span class="glyphicon glyphicon-flash" aria-hidden="true"></span>正在处理中，请稍候...');
            $("#danger_confirm").attr('disabled', 'disabled');
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/TruncateTable';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"table" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //重命名
        function rename_table(){
            $("#danger_confirm").removeAttr('disabled');
            $("#danger_confirm").html('确认');
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">重命名表</a>操作吗？</h4>');
            $("#danger_confirm").attr('onclick', 'dele_table_exec()');
            $("#danger_confirm_modal").modal('show');
        }
        
        //执行重命名
        function rename_table_exec(){
            $("#danger_confirm").html('<span class="glyphicon glyphicon-flash" aria-hidden="true"></span>正在处理中，请稍候...');
            $("#danger_confirm").attr('disabled', 'disabled');
            if ($("#new_table_name").val() != ''){
                var data = new Array();
                data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/RenameTable';
                data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
                data['data'] += '"old_table_name" : "<?= $data['collection'] ?>", "new_table_name" : "' + $("#new_table_name").val() + '", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
                parent.IframeSend(data);
            }
        }
        
    </script>
    <script>
    //数据修改
    var data_update_length = 0;
    var data_update_col_name = new Array();
    var data_update_old_data = new Array();   
    var data_update_new_data = new Array();
    var data_update_source = 0;
    var data_update_key = 0;
    //显示修改窗口
    function data_update_button(source, key){
    //防止执行失败导致重复push
        data_update_length = 0;
        data_update_col_name = [];
        data_update_old_data = [];   
        data_update_new_data = [];
        data_update_source = 0;
        data_update_key = 0;
        
        $("#data_update_confirm").html('确认');        
        $("#data_update_confirm").removeAttr('disabled');   
    //source来源：0为data_view 1为SQL查询页 
        if (!source){
            $("#data_update_title").html('修改第' + (key + 1) + '行数据');
        } else {
            $("#data_update_title").html('修改第' + (key + 1) + '行数据' + "<br/><br/><a style='color:red'>注意：未选取主键将会导致多行数据修改</a>");
        }
        
        $("#data_update_confirm").attr("onclick", "data_update_confirm(" + source + "," + key + ")");  
        
        if (!source){
            data_update_length = $("#data_view tbody #data_" + key + " td").length - 1;
            for (var i = 1; i <= data_update_length; i++){    
                data_update_col_name.push($("#data_col_name th:eq(" + i + ")").html());      
                data_update_old_data.push($("#data_view tbody #data_" + key + " td:eq(" + i + ")").html());
                if ("checkbox" == $(".data_update_val:eq(" + (i - 1) + ")").attr("type")){
                    if (data_update_old_data[data_update_old_data.length - 1] == 1){
                        $(".data_update_val:eq(" + (i - 1) + ")").attr("checked", "checked");
                    }
                } else {
                    $(".data_update_val:eq(" + (i - 1) + ")").val(data_update_old_data[data_update_old_data.length - 1]);
                }
            }
            $("#data_update_modal").modal('show');
        } else {
            $(".data_update_tr").hide();            
            for (i in chart_data['cols']){
                $("#data_update_" + chart_data['cols'][i]).show();
            }
            
            data_update_length = $("#sql_data_view tbody #sql_exec_" + key + " td").length - 1;
            for (var i = 1; i <= data_update_length; i++){    
                data_update_old_data.push($("#sql_data_view tbody #sql_exec_" + key + " td:eq(" + i + ")").html());
                if ("checkbox" == $(".data_update_val:eq(" + (i - 1) + ")").attr("type")){
                    if (data_update_old_data[data_update_old_data.length - 1] == 1){
                        $(".data_update_val:eq(" + (i - 1) + ")").attr("checked", "checked");
                    }
                } else {
                    $(".data_update_val:eq(" + (i - 1) + ")").val(data_update_old_data[data_update_old_data.length - 1]);
                }
            }
        }
        $("#data_update_modal").modal('show');
    }
    
    //执行修改过程
    function data_update_confirm(source, update_key){
        
        $("#data_update_confirm").html('<span class="glyphicon glyphicon-flash" aria-hidden="true"></span>正在处理中，请稍候...');
        $("#data_update_confirm").attr('disabled', 'disabled');
        //source来源：0为data_view 1为SQL查询页  
        var data = new Array();
        data['src'] = location.href.slice((location.href.lastIndexOf("/")));
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/UpdateData';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"table" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>", "old_data" : {';
        
        i = 0;
        while (null != (old_data = data_update_old_data.shift())){
            //获取style中display为style="display: table-row;"
            if (i != 0){
                data['data'] += ', ';
            }
            data['data'] += '"' + i  + '" : "' + old_data + '"';
            i++;
        }
        
        data['data'] += '}, "col_name" : {';
        
        if (!source){
            i = 0;
            while (null != (col_name = data_update_col_name.shift())){                
                if (i != 0){
                    data['data'] += ', ';
                }
                data['data'] += '"' + i  + '" : "' + col_name + '"';
                i++;
            }
        } else {
            i = 0;
            for (key in chart_data['cols']){
                if (i != 0){
                    data['data'] += ', ';
                }
                data['data'] += '"' + i  + '" : "' + chart_data['cols'][key] + '"';
                i++;
            }
        }
        
        data['data'] += '}, "new_data" : {';
        
        for (i = 0; i < data_update_length; i++){
            if (i != 0){
                data['data'] += ', ';
            }
            
            if ("checkbox" == $(".data_update_tr:visible .data_update_val:eq(" + i + ")").attr('type')){                
                if (true == $(".data_update_tr:visible .data_update_val:eq(" + i + ")").prop('checked')){   
                    data_update_new_data.push("1");
                    data['data'] += '"' + i  + '" : "1"';
                } else {                    
                    data_update_new_data.push("0");
                    data['data'] += '"' + i  + '" : "0"';
                }
            } else {
                data_update_new_data.push($(".data_update_tr:visible .data_update_val:eq(" + i + ")").val());
                data['data'] += '"' + i  + '" : "' + data_update_new_data[data_update_new_data.length - 1] + '"';
            }
        }
        data['data'] += '}}';
        parent.IframeSend(data);
        
        data_update_source = source;
        data_update_key = update_key;
    }
    
    //更新显示数据
    function update_data_display(){
        if (!data_update_source){
            for (var i = 1; i <= data_update_length; i++){ 
                $("#data_view tbody #data_" + data_update_key + " td:eq(" + i + ")").html(data_update_new_data[i - 1]);
            }
        } else {            
            for (var i = 1; i <= data_update_length; i++){    
                $("#sql_exec_" + data_update_key + " td:eq(" + i + ")").html(data_update_new_data[i - 1]);
            }
        }
        
        //回收内存
        delete data_update_length;
        delete data_update_col_name;
        delete data_update_old_data;   
        delete data_update_new_data;
        delete data_update_source;
        delete data_update_key;
    }
    
    //显示删除窗口
    function data_dele_button(source, key){
        $("#danger_confirm").html('确认');
        $("#danger_confirm").removeAttr('disabled');
        if (!source){
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">删除行</a>操作吗？<br/><br/><a style="color:red">*[如未指定主键将会删除多行数据]</a></h4>');
        } else {
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">删除表</a>操作吗？<br/><br/><a style="color:red">*[如未选定主键将会删除多行数据]</a></h4>');
        }

        $("#danger_confirm").attr('onclick', 'dele_data_exec(' + source + ', ' + key + ')');
        $("#danger_confirm_modal").modal('show');
    }
    
    //执行删除    
    var data_dele_source = 0;
    var data_dele_key = 0;
    var data_dele_col_name = new Array();
    function dele_data_exec(source, dele_key){
    //防止重复push
        data_dele_source = 0;
        data_dele_key = 0;
        data_dele_col_name = [];
        if (!source){
            data_dele_length = $("#data_view tbody #data_" + dele_key + " td").length - 1;
            for (var i = 1; i <= data_update_length; i++){    
                data_dele_col_name.push($("#data_col_name th:eq(" + i + ")").html());      
            }            
        } else {
            data_dele_length = $("#sql_data_view tbody #sql_exec_" + dele_key + " td").length - 1;
        }
        
        //source来源：0为data_view 1为SQL查询页  
        var data = new Array();
        data['src'] = location.href.slice((location.href.lastIndexOf("/")));
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/DeleData';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"table" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>", "old_data" : {';
        
        for (i = 0; i < data_dele_length; i++){
            //获取style中display为style="display: table-row;"
            if (i != 0){
                data['data'] += ', ';
            }
            if (!source){
                data['data'] += '"' + i  + '" : "' + $("#data_view tbody #data_" + dele_key + " td:eq(" + (i + 1) + ")").html() + '"';
            } else {
                data['data'] += '"' + i  + '" : "' + $("#sql_data_view tbody #sql_exec_" + dele_key + " td:eq(" + (i + 1) + ")").html() + '"';
            }
        }
        
        data['data'] += '}, "col_name" : {';        
        
        if (!source){
            for(i = 0; i < data_dele_length; i++){                
                if (i != 0){
                    data['data'] += ', ';
                }
                data['data'] += '"' + i  + '" : "' + $("#data_col_name th:eq(" + (i + 1) + ")").html() + '"';
            }
        } else {
            i = 0;
            for (key in chart_data['cols']){
                if (i != 0){
                    data['data'] += ', ';
                }
                data['data'] += '"' + i  + '" : "' + chart_data['cols'][key] + '"';
                i++;
            }
        }
        
        data['data'] += '}}';
        parent.IframeSend(data);
        
        data_dele_source = source;
        data_dele_key = dele_key;
    }
    
    
    //处理删除前端
    function dele_data_display(){
        if (!data_dele_source){
            $("#data_view tbody #data_" + data_dele_key).remove();
        } else {           
            $("#sql_exec_" + data_dele_key).remove();
        }
    }    
    </script>
</html>