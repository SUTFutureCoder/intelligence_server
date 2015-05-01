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
            var data_sum = <?= $data['data_sum'] ?>;
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
            <li role="presentation"><a href="#js" id="sql_tab" role="tab" data-toggle="tab">Json</a></li>
            <li role="presentation"><a href="#insert" role="tab" data-toggle="tab">插入</a></li>
            <li role="presentation"><a href="#search" role="tab" data-toggle="tab">搜索</a></li>
            <li role="presentation"><a href="#operating" role="tab" data-toggle="tab">操作</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active col-lg-11 col-lg-offset-1" id="view">
                <br/>
                <br/>
                <?php foreach($data['data'] as $value): ?>
                <div class="panel panel-default view_panel" id="data_<?= $value['_id'] ?>">
                    <div class="panel-heading"><?= $data['data_sum']-- ?></div>
                    <div class="panel-body">
                        <pre class="view_value_array"><?= print_r($value, TRUE) ?></pre>
                        <pre class="view_value_json"><?= print_r(json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), TRUE) ?></pre>
                    </div>
                    <div class="panel-footer">
                        <button type="button" class="btn btn-primary btn-xs" onclick="data_update_button(0, '<?= $value['_id'] ?>')">更新</button>
                        <button type="button" class="btn btn-danger btn-xs" onclick="data_dele_button('<?= $value['_id'] ?>')">删除</button>
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
                            <th>名字</th>
                        </tr>
                    </thead>
                    <tbody>                        
                    <?php $i = 0; ?>
                    <?php foreach ($data['cols'] as $col_name): ?>                    
                        <tr class="struct_view_col" id="struct_view_col_<?=$col_name?>">
                            <td><?= ++$i ?></td>
                            <td><?= $col_name ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="js">
                <br/>
                <div class="col-sm-12">
                <div class="col-sm-8">
                    <textarea class="form-control" rows="5" id="sql_area">{
    
}</textarea>
                    <br/>
                    <div class="btn-group js_drop">
                            <button type="button" class="btn btn-default dropdown-toggle" id="js_drop_button" drop-type="find" data-toggle="dropdown" aria-expanded="false">
                                查询 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu " role="menu">
                                <li><a onclick="change_js_drop('find')">查询</a></li>
                                <li><a onclick="change_js_drop('update')">修改</a></li>
                                <li><a onclick="change_js_drop('insert')">添加</a></li>
                                <li class="divider"></li>
                                <li><a onclick="change_js_drop('dele')">删除</a></li>
                            </ul>
                    </div>
                    <button type="button" class="btn btn-default" onclick='sql_button("    ", 2)'>tab</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$gt\" : \"", 2)'>></button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$gte\" : \"", 2)'>>=</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$lt\" : \"", 2)'><</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$lte\" : \"", 2)'><=</button>
                    <button type="button" class="btn btn-default" onclick='sql_button("\" ", 2)'>"</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" } ", 2)'>}</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(", ", 2)'>,</button>
                    <br/>
                    <br/>
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control col-lg-1 js_find_control_button" id="sql_limit" placeholder="数据量">
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control col-lg-1 js_find_control_button" id="sql_skip" placeholder="偏移量">
                        </div>
                    </form>
                    <br/>                    
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$ne\" : \"", 1)'>!=</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$in\" : [\"", 1)'>in</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$nin\" : [\"", 1)'>not in</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$not\" : {\"", 1)'>not</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$mod\" : \"", 1)'>%</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$all\" : [\"", 1)'>all</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$size\" : \"", 1)'>size</button>
                    <button type="button" class="btn btn-default" onclick='sql_button(" : {\"$exists\" : \"", 1)'>exists</button>
                    <br/>
                    <br/> 
                    <button type="button" class="btn btn-default js_update_button" disabled="disabled" onclick='sql_button("$set : { ", 1)'>set</button>
                    <button type="button" class="btn btn-default js_update_button" disabled="disabled" onclick='sql_button("$inc : { ", 1)'>inc</button>
                    <button type="button" class="btn btn-danger js_update_button" disabled="disabled" onclick='sql_button("$unset : { ", 1)'>unset</button>     
                    <button type="button" class="btn btn-danger js_update_button" disabled="disabled" onclick='sql_button("$pop : { ", 1)'>pop</button>     
                    <button type="button" class="btn btn-danger js_update_button" disabled="disabled" onclick='sql_button("$pull : { ", 1)'>pull</button>     
                    <button type="button" class="btn btn-danger js_update_button" disabled="disabled" onclick='sql_button("$pullAll : { ", 1)'>pullAll</button>     
                    <button type="button" class="btn btn-default js_update_button" disabled="disabled" onclick='sql_button("$push : { ", 1)'>push</button>
                    <button type="button" class="btn btn-default js_update_button" disabled="disabled" onclick='sql_button("$pushAll : { ", 1)'>pushAll</button>
                    <button type="button" class="btn btn-default js_update_button" disabled="disabled" onclick='sql_button("$addToSet : { ", 1)'>addToSet</button>
                    <br/> 
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="js_find_control_button" id="memcache"> memcache缓存查询结果
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="js_update_button" disabled="disabled" id="sql_upsert">upsert
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="js_update_button" disabled="disabled" id="sql_multi">multi
                        </label>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="launch_sql()">执行</button>
                </div>
                <div class="col-sm-4">
                    <table class="table table-hover table-bordered" id="sql_table_list">                       
                        <tbody>                       
                        <?php foreach ($data['cols'] as $col_name): ?>                    
                            <tr id="sql_col_name_<?= $col_name?>">
                                <td onclick='sql_button(" \"<?= $col_name ?>\" ", 1)'><a><?= $col_name ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                </div>
                <hr>
                <br/>
                <div id="sql_result">
                    
                </div>
            
            </div>
            <div role="tabpanel" class="tab-pane fade" id="insert">                
                <table class="table table-hover table-bordered" >                       
                    <tbody>  
                        <form role="form" id="insert_list">
                        <tr>
                            <td>字段名</td>
                            <td>使用字段</td>
                            <td>值</td>
                        </tr>
                        <?php foreach ($data['cols'] as $col_name): ?>  
                            <?php if($col_name == '_id'): 
                                continue;
                            endif; ?>
                        <tr class="insert_field" id="insert_<?= $col_name ?>">
                            <td class="insert_field_colname" colname="<?= $col_name ?>"><?= $col_name ?>
                            </td>
                            <td>
                                <div class="checkbox">
                                    <label>
                                        <input class="insert_field_checkbox" type="checkbox" checked="checked">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">                                    
                                    <textarea class="form-control insert_field_val"  rows="2" id="insert_<?= $col_name ?>_val"></textarea>                                    
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
                                    <option value="=">=</option>
                                    <option value="!=">!=</option>
                                    <option value=">">></option>
                                    <option value=""><</option>
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
                <!-- <div class="panel panel-warning">
                    <div class="panel-heading">修改集合名</div>
                    <div class="panel-body">
                        <form role="form">
                            <div class="form-group">
                              <input type="text" id="new_table_name" class="form-control">
                            </div>                            
                        </form>
                        <button type="button" class="btn btn-info"  onclick="rename_table()">修改集合名</button>
                    </div>
                </div> -->
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
                    <textarea rows="15" class="form-control" id="data_update_area"></textarea>
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
    <script src="<?= base_url('js/base64.js') ?>"></script>
    <script>  
        //更新数据指示器
        var updateDataIndicator = 0;
        var deleDataIndicator = 0;
        var refreshDataIndicator = 0;
        var insertDataIndicator = 0;
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
                            
                            if (data[4]['nosql_type'] != 'find'){
                                refreshDataIndicator = 1;
                                sql = data[4]['sql'];
                                col = data[4]['rows'];
                                
                                var data = new Array();
                                data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                                data['group'] = 'WSPDM2_Mongo';
                                data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_ReFreshTable';
                                data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "sql" : "' + BASE64.encoder(sql) + '", "col" : "' + col + '"}';
                                parent.IframeSend(data, 'group');    
                                break;
                            } else {
                                //取出字段
                                if (!data[4]['id'].length){
                                    return 0;
                                } else {                    
                                    //取出数据
                                    $.each(data[4]['json'], function (i, data_item){
            //                            console.log(data_item);
                                        $("#sql_result").append('<br/><div class="panel panel-default " id="sql_result_' + data[4]['id'][i] + '"></div>');
                                        $('#sql_result_' + data[4]['id'][i]).append('<div class="panel-heading">' + data[4]['rows']-- + '</div><div class="panel-body"><pre>' + data_item + '</pre></div><div class="panel-footer"><button type="button" class="btn btn-primary btn-xs" onclick="data_update_button(1, \'' + data[4]['id'][i] + '\')">更新</button><button type="button" class="btn btn-danger btn-xs" onclick="data_dele_button(\'' + data[4]['id'][i] + '\')">删除</button></div>');
                                    })
                                }
                                
                                //准备显示图表
                                chart_data['cols'] = data[4]['cols'];
                                chart_data['data'] = data[4]['data'];
                            }
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
                            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "collection" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>"}';
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
                            updateDataIndicator = 1
                            if (data[4]['rows']){
                                var data_update = new Array();
                                data_update['src'] = location.href.slice((location.href.lastIndexOf("/")));
                                data_update['group'] = 'WSPDM2_Mongo';
                                data_update['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_UpdataData';
                                data_update['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
                                data_update['data'] += '"key" : "' + data[4]['key'] + '", "sql" : "' + BASE64.encoder(data[4]['sql']) + '", "col" : "' + data[4]['rows'] + '", "new_data" : "';
                                data_update['data'] += BASE64.encoder($("#data_update_area").val());
                                data_update['data'] += '"}';
                                parent.IframeSend(data_update, 'group');    
                            }
                            
                            break;
                            
                        case 'DeleData':
                            $("#danger_confirm_modal").modal('hide');
                            deleDataIndicator = 1;
                            if (data[4]['rows']){
                                var data_dele = new Array();
                                data_dele['src'] = location.href.slice((location.href.lastIndexOf("/")));
                                data_dele['group'] = 'WSPDM2_Mongo';
                                data_dele['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/B_DeleData';
                                data_dele['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
                                data_dele['data'] += '"key" : "' + data[4]['key'] + '", "sql" : "' + BASE64.encoder(data[4]['sql']) + '", "col" : "' + data[4]['rows'] + '"}';
                                parent.IframeSend(data_dele, 'group');    
                            }
                            
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
                        case 'B_UpdateData':
                            $("#data_" + data[4]['id'] + " .panel-body .view_value_json").html(data[4]['new_data']);
                            $("#data_update_area").val($("#sql_result #sql_result_" + data[4]['id'] + " pre").html(data[4]['new_data']));
                            
                            if (!updateDataIndicator){
                                $("#alert").removeClass("alert-success");
                                $("#alert").addClass("alert-danger");
                                $("#alert").html("数据发生更改<br/>修改_id:" + data[4]['id'] + "<br/>使用nosql语句<br/>" + data[4]['sql'] + "<br/>影响行数" + data[4]['col']);
                            }
                            updateDataIndicator = 0;
                            break;
                            
                        case 'B_DeleData':
                            $("#data_" + data[4]['id']).remove();
                            $("#sql_result #sql_result_" + data[4]['id']).remove();
                            if (!deleDataIndicator){
                                $("#alert").removeClass("alert-success");
                                $("#alert").addClass("alert-danger");
                                $("#alert").html("数据发生删除<br/>删除_id:" + data[4]['id'] + "<br/>使用nosql语句<br/>" + data[4]['sql'] + "<br/>影响行数" + data[4]['col']);
                            }
                            deleDataIndicator = 0;
                            break;
                            
                        case 'B_InsertData':
                            if (!insertDataIndicator){
                                $("#alert").removeClass("alert-success");
                                $("#alert").addClass("alert-danger");
                                $("#alert").html("数据发生增添<br/>新增_id:" + data[4]['id'] + "<br/>使用nosql语句<br/>" + data[4]['sql'] + "<br/>影响行数" + data[4]['rows']);
                            } else {
                                $("#alert").removeClass("alert-danger");
                                    $("#alert").addClass("alert-success");
                                    if (undefined != data[4]['rows']){
                                        $("#alert").html("<p>共操作" + data[4]['rows'] + "行，操作消耗" + data[4]['time'] + "秒<p>" + "<p>" + data[4]['sql'] + "<p>");
                                    } else {
                                        $("#alert").html("<p>操作消耗" + data[4]['time'] + "秒<p>" + "<p>" + data[4]['sql'] + "<p>");
                                    }
                            }
                            $(".view_panel:first").before('<div class="panel panel-default view_panel" id="data_' + data['4']['id'] + '"></div>');
                            $("#data_" + data[4]['id']).append('<div class="panel-heading">' + ++data_sum + '</div><div class="panel-body"><pre class="view_value_array">' + data[4]['data']['array'] + '</pre><pre class="view_value_json">' + data[4]['data']['json'] + '</pre></div><div class="panel-footer"><button type="button" class="btn btn-primary btn-xs" onclick="data_update_button(0, \'' + data[4]['id'] + '\')">更新</button><button type="button" class="btn btn-danger btn-xs" onclick="data_dele_button(\'' + data[4]['id'] + '\')">删除</button></div>');
                            insertDataIndicator = 0;
                            break;
                        case 'B_ReFreshTable':
                            if (!refreshDataIndicator){
                                $("#alert").removeClass("alert-success");
                                $("#alert").addClass("alert-danger");
                                $("#alert").append("<br/>警告！数据由其他用户发生更改！<br/>使用nosql语句" + data[4]['sql'] + "<br/><br/><button type=\"button\" class=\"btn btn-success\" onclick=\" window.location.href='" + location.href + "'\">重新加载以消除脏数据</button>");
                            }
                            refreshDataIndicator = 0;
                            break;
                            
                        case 'B_DeleTable':
                            parent.DeleTable(data[4]['database'], data[4]['table']);
                            break;
                        
                        case 'B_TruncateTable':
                            $(".view_panel").remove();
                            $(".struct_view_col").remove();
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
        
        //选择下拉选择框
        function change_js_drop(command){
            switch (command){
                case 'find':
                    $("#js_drop_button").html('查询 <span class="caret"></span>').attr('drop-type', 'find');
                    $('.js_find_control_button').removeAttr("disabled");
                    $(".js_update_button").attr("disabled", "disabled");
                    $("#sql_area").html("{\n\
    \n\
}");   
                    break;
                case 'update':
                    $("#js_drop_button").html('修改 <span class="caret"></span>').attr('drop-type', 'update');
                    $('.js_find_control_button').attr("disabled", "disabled");
                    $(".js_update_button").removeAttr("disabled");
                    $("#sql_area").html("{\n\
    \n\
},{\n\
    \n\
}");    
                    break;
                case 'insert':
                    $("#js_drop_button").html('插入 <span class="caret"></span>').attr('drop-type', 'insert');
                    $('.js_find_control_button').attr("disabled", "disabled");
                    $(".js_update_button").attr("disabled", "disabled");
                    $("#sql_area").html("{\n\
    \n\
}");    
                    break;
                case 'dele':
                    $("#js_drop_button").html('删除 <span class="caret"></span>').attr('drop-type', 'dele');
                    $('.js_find_control_button').attr("disabled", "disabled");
                    $(".js_update_button").attr("disabled", "disabled");
                    $("#sql_area").html("{\n\
    \n\
}");   
                    break;
            }
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
            data['data'] += '"nosql_type" : "' + $("#js_drop_button").attr('drop-type') + '", "sql_limit" : "' + $("#sql_limit").val() + '", "sql_skip" : "' + $("#sql_skip").val() + '", "sql_upsert" : "' + $("#sql_upsert").prop('checked') + '", "sql_multi" : "' + $("#sql_multi").prop('checked') + '", "nosql" : "' + BASE64.encoder($("#sql_area").val()) + '", "memcache" : "' + memcache + '", "database" : "<?= $data['database'] ?>", "collection" : "<?= $data['collection'] ?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data); 
        }
        
        //添加数据
        function insert(){
            //序列化表单
            var insert_values = $(".insert_field");
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['group'] = 'WSPDM2_Mongo';
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/InsertData';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "database" : "<?= $data['database'] ?>", "collection" : "<?= $data['collection'] ?>", "data" : {';
            var i = 0;
            insert_values.each(function(){
                if ($(this).find('.insert_field_checkbox').prop('checked') == false){
                    return true;
                }
                
                if (0 != i){
                    data['data'] += ', ';
                }
                data['data'] += '"' + $(this).find('.insert_field_colname').attr('colname') + '":"' + $(this).find('.insert_field_val').val() + '"';
                i++;
            });
            data['data'] += '}, "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data, 'group');
            insertDataIndicator = 1;
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
            data['data'] += '}, "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
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
            data['data'] += '"collection" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
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
                data['data'] += '"old_table_name" : "<?= $data['collection'] ?>", "new_table_name" : "' + $("#new_table_name").val() + '", "database" : "<?= $data['database'] ?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
                parent.IframeSend(data);
            }
        }
        
    </script>
    <script>
    //显示修改窗口
    function data_update_button(source, key){
        $("#data_update_confirm").html('确认');        
        $("#data_update_confirm").removeAttr('disabled');   
    //source来源：0为data_view 1为SQL查询页 
        $("#data_update_title").html('修改_id为' + (key) + '的数据');
        
        $("#data_update_confirm").attr("onclick", "data_update_confirm('" + key + "')");  
        
        if (!source){
            $("#data_update_area").val($("#data_" + key + " .view_value_json").html());
        } else {
            $("#data_update_area").val($("#sql_result #sql_result_" + key + " pre").html());
        }
        
        $("#data_update_modal").modal('show');
    }
    
    //执行修改过程
    function data_update_confirm(update_key){
        
        $("#data_update_confirm").html('<span class="glyphicon glyphicon-flash" aria-hidden="true"></span>正在处理中，请稍候...');
        $("#data_update_confirm").attr('disabled', 'disabled');
        //source来源：0为data_view 1为SQL查询页  
        var data = new Array();
        data['src'] = location.href.slice((location.href.lastIndexOf("/")));
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/UpdateData';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"collection" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>", "key" : "' + update_key + '", "new_data" : "';
        data['data'] += BASE64.encoder($("#data_update_area").val());
        data['data'] += '"}';
//        console.log(data['data']);
        parent.IframeSend(data);
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
        
    }
    
    //显示删除窗口
    function data_dele_button(key){
        $("#danger_confirm").html('确认');
        $("#danger_confirm").removeAttr('disabled');
        $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">删除数据</a>操作吗？<br/></h4>');
        $("#danger_confirm").attr('onclick', 'dele_data_exec("' + key + '")');
        $("#danger_confirm_modal").modal('show');
    }
    
    //执行删除    
    function dele_data_exec(dele_key){
        var data = new Array();
        data['src'] = location.href.slice((location.href.lastIndexOf("/")));
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/MongoTableInfo/DeleData';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"collection" : "<?= $data['collection'] ?>", "database" : "<?= $data['database'] ?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>", "key" : "' + dele_key + '"}';
        parent.IframeSend(data);
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