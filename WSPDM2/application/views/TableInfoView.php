<!DOCTYPE html>  
<html>  
    <head>  
        <title></title>
        <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
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
            <p><?= $data['sql']?></p>
        </div>
        <?php endif; ?>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#view" role="tab" data-toggle="tab">浏览</a></li>
            <li role="presentation"><a href="#struct" role="tab" data-toggle="tab">结构</a></li>
            <li role="presentation"><a href="#sql" id="sql_tab" role="tab" data-toggle="tab">SQL</a></li>
            <li role="presentation"><a href="#insert" role="tab" data-toggle="tab">插入</a></li>
            <li role="presentation"><a href="#search" role="tab" data-toggle="tab">搜索</a></li>
            <li role="presentation"><a href="#chart" id="chart_tab" role="tab" data-toggle="tab">分析</a></li>
            <li role="presentation"><a href="#backup" role="tab" data-toggle="tab">云备份</a></li>
            <li role="presentation"><a href="#operating" role="tab" data-toggle="tab">操作</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="view">
                <br/>
                <table class="table table-hover table-bordered" id="data_view">
                    <thead>
                        <tr id="data_col_name">
                            <th>#</th>
                            <?php foreach ($data['cols'] as $col_name => $col_type):?>
                            <th><?= $col_name ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?php foreach($data['data'] as $key => $value): ?>                    
                        <tr id="data_<?= $key ?>">
                            <td><?= $key + 1 ?>
                                <button type="button" class="btn btn-primary btn-xs" onclick="data_update_button(0, <?= $key ?>)">修改</button>
                                <button type="button" class="btn btn-danger btn-xs" onclick="data_dele_button(0, <?= $key ?>)">删除</button>
                            </td>
                            <?php foreach($value as $table_name => $table_value): ?>   
                                <td><?=$table_value?></td>                                
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                
            </div>
            <div role="tabpanel" class="tab-pane fade" id="struct">
                <br/>
                <table class="table table-hover table-bordered" id="struct_view">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>操作</th>
                            <th>名字</th>
                            <th>类型长度</th>
                            <th>字符集</th>
                            <th>注释</th>
                        </tr>
                    </thead>
                    <tbody>                        
                    <?php $i = 0; ?>
                    <?php foreach ($data['cols'] as $col_name => $col_type): ?>                    
                        <tr id="struct_view_col_<?=$col_name?>">
                            <td><?= ++$i ?></td>
                            <td><a onclick="dele_col_name('<?=$col_name?>')" style="color:red">删除</a></td>
                            <td><?= $col_name ?>
                            <?php if ('UNI' == $data['cols'][$col_name]['key']): ?>
                                <span class="label label-primary">唯</span>
                            <?php elseif ('PRI' == $data['cols'][$col_name]['key']): ?>
                                <span class="label label-danger">主</span>
                            <?php endif;?>
                            <?php if ('auto_increment' == $data['cols'][$col_name]['auto']): ?>
                                <span class="label label-success">增</span>
                            <?php endif;?>
                            <?php if ('YES' == $data['cols'][$col_name]['nullable']): ?>
                                <span class="label label-default">空</span>
                            <?php endif;?>
                            </td>
                            <td><?= $data['cols'][$col_name]['type_length']?></td>
                            <td><?= $data['cols'][$col_name]['charset']?></td>
                            <td><?= $data['cols'][$col_name]['comment']?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="sql">
                <br/>
                <div class="col-sm-8">
                    <textarea class="form-control" rows="5" id="sql_area"></textarea>
                    <br/>
                    <button type="button" class="btn btn-default" onclick="sql_button('SELECT ', 0)">SELECT</button>
                    <button type="button" class="btn btn-default" onclick="sql_button('SELECT * FROM <?= $data['database']?>.<?= $data['table'] ?> WHERE ', 0)">SELECT *</button>
                    <button type="button" class="btn btn-default" onclick="sql_button('UPDATE ', 0)">UPDATE</button>
                    <button type="button" class="btn btn-default" onclick="sql_button('INSERT INTO <?= $data['database']?>.<?= $data['table'] ?> ', 0)">INSERT</button>
                    <button type="button" class="btn btn-warning" onclick="sql_button('DELETE FROM <?= $data['database']?>.<?= $data['table'] ?> ', 0)">DELETE</button>
                    <button type="button" class="btn btn-danger" onclick="sql_button('DROP ', 0)">DROP</button>
                    <br/>
                    <br/>                    
                    <button type="button" class="btn btn-default" onclick="sql_button(' FROM ', 1)">FROM</button>
                    <button type="button" class="btn btn-danger" onclick="sql_button(' <?= $data['database']?>.<?= $data['table'] ?> ', 1)">DATABASE.TABLE</button>
                    <button type="button" class="btn btn-default" onclick="sql_button(' WHERE ', 1)">WHERE</button>
                    <button type="button" class="btn btn-default" onclick="sql_button(' SET ', 1)">SET</button>
                    <button type="button" class="btn btn-default" onclick="sql_button(' VALUES ', 1)">VALUES</button>
                    <button type="button" class="btn btn-default" onclick="sql_button(' AND ', 1)">AND</button>
                    <button type="button" class="btn btn-default" onclick="sql_button(' OR ', 1)">OR</button>
                    <br/>
                    <br/> 
                    <button type="button" class="btn btn-default" onclick="sql_button(' ORDER BY ', 1)">ORDER BY</button>
                    <button type="button" class="btn btn-default" onclick="sql_button(' GROUP BY ', 1)">GROUP BY</button>
                    <button type="button" class="btn btn-default" onclick="sql_button(' HAVING BY ', 1)">HAVING</button>
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
                        <?php foreach ($data['cols'] as $col_name => $col_type): ?>                    
                            <tr id="sql_col_name_<?= $col_name?>">
                                <td onclick="sql_button(' <?= $col_name ?> ', 2)"><a><?= $col_name ?></a></td>
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
                        <?php foreach ($data['cols'] as $col_name => $col_type): ?>                         
                        <tr id="insert_<?= $col_name ?>">
                            <td><?= $col_name ?>
                            <?php if ('UNI' == $data['cols'][$col_name]['key']): ?>
                                <span class="label label-primary">唯</span>
                            <?php elseif ('PRI' == $data['cols'][$col_name]['key']): ?>
                                <span class="label label-danger">主</span>
                            <?php endif;?>
                            <?php if ('auto_increment' == $data['cols'][$col_name]['auto']): ?>
                                <span class="label label-success">增</span>
                            <?php endif;?>
                            <?php if ('YES' == $data['cols'][$col_name]['nullable']): ?>
                                <span class="label label-default">空</span>
                            <?php endif;?>                                
                            <?php if ($data['cols'][$col_name]['comment']): ?>
                                [<?= $data['cols'][$col_name]['comment']?>]
                            <?php endif;?>
                            
                            </td>
                            <td><?= $data['cols'][$col_name]['type_length'] ?></td>
                            <td>
                                <div class="form-group">
                                    <?php if ('tinyint' == $data['cols'][$col_name]['type'] || 'int(1)' == $data['cols'][$col_name]['type_length']): ?>
                                        <input type="checkbox" name="<?= $col_name ?>" class="form-control cbx" id="insert_<?= $col_name ?>_val">
                                    <?php elseif ('text' == $data['cols'][$col_name]['type'] || $data['cols'][$col_name]['length'] >= 25): ?>
                                        <textarea class="form-control" name="<?= $col_name ?>" rows="3" id="insert_<?= $col_name ?>_val"></textarea>
                                    <?php else: ?>
                                        <input type="text" class="form-control" name="<?= $col_name ?>" id="insert_<?= $col_name ?>_val">
                                    <?php endif; ?>
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
                    <?php foreach ($data['cols'] as $col_name => $col_type): ?>                    
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
            <div role="tabpanel" class="tab-pane fade" id="chart">
                <br/>    
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-1 control-label">横坐标</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="chart_x_select">
                            </select>
                        </div>
                        
                        <div class="col-sm-4">
                            <label>
                                <input type="checkbox" id="chart_square_switch">面积图
                            </label>
                        </div>
                    </div>                    
                </form>                                
                <div id="chart_view" style="height:400px">
                    
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="backup">                
                <br/>
                <div class="panel panel-info">
                    <div class="panel-heading">创建快照</div>
                    <div class="panel-body">                        
                        <button type="button" class="btn btn-lg btn-block btn-info"  onclick="set_snapshot(0)">创建表快照</button>
                        <br/>
                        <button type="button" class="btn btn-lg btn-block btn-info"  onclick="set_snapshot(1)">创建数据库快照</button>
                        <hr/>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><a style="color:red"><?= $data['table'] ?></a>表快照</h3>
                            </div>
                            <table class="table table-condensed table-hover" id="table_snap">
                                <?php if (isset($snapshot['table'])): ?>
                                <?php foreach ($snapshot['table'] as $table_snap_name => $table_snap_value):?>
                                    <tr file="snap_0_<?= $table_snap_name?>">
                                        <td class="col-sm-8"><a><?= $table_snap_name?></a></td>
                                        <td class="col-sm-2"><a><?= $table_snap_value['size']?></a></td>
                                        <td class="col-sm-1"><button type="button" class="btn btn-danger btn-sm" onclick="snap_dele(0, '<?= $table_snap_name?>')">删除快照</button></td>
                                        <td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_rewind(0, '<?= $table_snap_name?>')">恢复</button></td>
                                        <td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_download(0, '<?= $table_snap_name?>')">下载</button></td>
                                    </tr>
                                <?php endforeach; ?>  
                                <?php endif; ?>
                            </table>
                        </div>
                        <br/>   
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><a style="color:red"><?= $data['database'] ?></a>数据库快照</h3>
                            </div>
                            <table class="table table-condensed table-hover" id="db_snap">
                                <?php if (isset($snapshot['db'])): ?>
                                <?php foreach ($snapshot['db'] as $db_snap_name => $db_snap_value):?>
                                    <tr file="snap_1_<?= $db_snap_name?>">
                                        <td class="col-sm-8"><a><?= $db_snap_name?></a></td>
                                        <td class="col-sm-2"><a><?= $db_snap_value['size']?></a></td>
                                        <td class="col-sm-1"><button type="button" class="btn btn-danger btn-sm" onclick="snap_dele(1, '<?= $db_snap_name?>')">删除快照</button></td>
                                        <td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_rewind(1, '<?= $db_snap_name?>')">恢复</button></td>
                                        <td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_download(1, '<?= $db_snap_name?>')">下载</button></td>
                                    </tr>
                                <?php endforeach; ?>  
                                <?php endif; ?>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="operating">                
                <br/>                
                <div class="panel panel-warning">
                    <div class="panel-heading">修改表名</div>
                    <div class="panel-body">
                        <form role="form">
                            <div class="form-group">
                              <input type="text" id="new_table_name" class="form-control">
                            </div>                            
                        </form>
                        <button type="button" class="btn btn-info"  onclick="rename_table()">修改表名</button>
                    </div>
                </div>
                <div class="panel panel-danger">
                    <div class="panel-heading">危险地带</div>
                    <div class="panel-body">
                        <button type="button" class="btn btn-warning col-sm-offset-5"  onclick="truncate_table()">清除表</button>
                        <button type="button" class="btn btn-danger col-sm-offset-11"  onclick="dele_table()">删除表</button>
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
                    <?php foreach ($data['cols'] as $col_name => $col_type): ?>                                                 
                        <tr id="data_update_<?= $col_name ?>" class="data_update_tr" >
                            <td class="col-sm-2"><strong><?= $col_name ?></strong><br/>
                            <?php if($data['cols'][$col_name]['comment']):?>
                                <small class="primary">[<?= $data['cols'][$col_name]['comment'] ?>]</small><br/>
                            <?php endif;?>
                            <?php if ('UNI' == $data['cols'][$col_name]['key']): ?>
                                <span class="label label-primary">唯</span>
                            <?php elseif ('PRI' == $data['cols'][$col_name]['key']): ?>
                                <span class="label label-danger">主</span>
                            <?php endif;?>
                            <?php if ('auto_increment' == $data['cols'][$col_name]['auto']): ?>
                                <span class="label label-success">增</span>
                            <?php endif;?>
                            <?php if ('YES' == $data['cols'][$col_name]['nullable']): ?>
                                <span class="label label-default">空</span>
                            <?php endif;?>    
                            </td>
                            <td><?= $data['cols'][$col_name]['type_length'] ?></td>
                            <td>
                                <div class="form-group">
                                    <?php if ('tinyint' == $data['cols'][$col_name]['type'] || 'int(1)' == $data['cols'][$col_name]['type_length']): ?>
                                        <input type="checkbox" class="form-control cbx data_update_val">
                                    <?php elseif ('text' == $data['cols'][$col_name]['type'] || $data['cols'][$col_name]['length'] >= 25): ?>
                                        <textarea class="form-control data_update_val" rows="3"></textarea>
                                    <?php else: ?>
                                        <input type="text" class="form-control data_update_val">
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
                                data['group'] = 'WSPDM2';
                                data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_ReFreshTable';
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
                            data['group'] = 'WSPDM2';
                            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_DeleCol';
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
                            data['group'] = 'WSPDM2';
                            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_DeleTable';
                            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>"}';
                            parent.IframeSend(data, 'group');    
                            break;
                            
                        case 'TruncateTable':
                            $("#danger_confirm_modal").modal('hide');
                            alert('清除成功');
                            var data = new Array();
                            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data['group'] = 'WSPDM2';
                            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_TruncateTable';
                            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>"}';
                            parent.IframeSend(data, 'group');    
                            break;
                            
                        case 'RenameTable':
                            $("#danger_confirm_modal").modal('hide');
                            alert('修改成功');
                            var data_rename = new Array();
                            data_rename['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data_rename['group'] = 'WSPDM2';
                            data_rename['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_RenameTable';
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
                            snapshot['group'] = 'WSPDM2';
                            snapshot['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_SnapShot';
                            snapshot['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "snap_type" : "' + data[4]['type'] + '", "snap_name" : "' + data[4]['name'] + '", "snap_size" : "' + data[4]['size'] + '"}';
                            parent.IframeSend(snapshot, 'group');  
                            break;
                            
                        case 'DeleSnapShot':
                            $("#danger_confirm_modal").modal('hide');  
                            var delesnapshot = new Array();
                            delesnapshot['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            delesnapshot['group'] = 'WSPDM2';
                            delesnapshot['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_DeleSnapShot';
                            delesnapshot['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "snap_type" : "' + data[4]['type'] + '", "snap_name" : "' + data[4]['name'] + '"}';
                            parent.IframeSend(delesnapshot, 'group');  
                            break;
                            
                        case 'RewindSnapshot':
                            $("#danger_confirm_modal").modal('hide');
                            alert('回滚成功，请结束其他未执行的操作并刷新页面以进行深度重载');                            
                            var Rewind = new Array();
                            Rewind['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            Rewind['group'] = 'WSPDM2';
                            Rewind['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_RewindSnapShot';
                            Rewind['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "snap_type" : "' + data[4]['type'] + '", "database" : "' + data[4]['database'] + '", "table" : "<?= $data['table'] ?>"}';
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
                                $("#table_snap").append('<tr file="snap_0_' + data[4]['name'] + '"><td class="col-sm-8"><a>' + data[4]['name'] + '</a></td><td class="col-sm-2"><a>' + data[4]['size'] + '</a></td><td class="col-sm-1"><button type="button" class="btn btn-danger btn-sm" onclick="snap_dele(0, \'' + data[4]['name'] + '\')">删除快照</button></td><td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_rewind(0, \'' + data[4]['name'] + '\')">恢复</button></td><td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_download(0, \'' + data[4]['name'] + '\')">下载</button></td></tr>');
                            } else {                                
                                $("#db_snap").append('<tr file="snap_1_' + data[4]['name'] + '"><td class="col-sm-8"><a>' + data[4]['name'] + '</a></td><td class="col-sm-2"><a>' + data[4]['size'] + '</a></td><td class="col-sm-1"><button type="button" class="btn btn-danger btn-sm" onclick="snap_dele(1, \'' + data[4]['name'] + '\')">删除快照</button></td><td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_rewind(1, \'' + data[4]['name'] + '\')">恢复</button></td><td class="col-sm-1"><button type="button" class="btn btn-success btn-sm" onclick="snap_download(0, \'' + data[4]['name'] + '\')">下载</button></td></tr>');
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
            switch (mode){
                case 0:
                    $("#sql_area").val(sql);
                    break;
                case 1:
                    var old_sql = $("#sql_area").val();
                    old_sql += sql;
                    $("#sql_area").val(old_sql);
                    break;
                case 2:
                    //在光标处插入
                    $("#sql_area").insertAtCaret(sql);
                    break;
            }
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
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/ExecSQL';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"sql" : "' + $("#sql_area").val() + '", "memcache" : "' + memcache + '", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //删除字段
        function dele_col_name(col_name){
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/DeleCol';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"col_name" : "' + col_name + '", "database" : "<?= $data['database'] ?>", "table" : "<?= $data['table']?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //添加数据
        function insert(){
            //序列化表单
            var values = $("#insert_list").serializeArray();
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['group'] = 'WSPDM2';
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/InsertData';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "database" : "<?= $data['database'] ?>", "table" : "<?= $data['table'] ?>", "data" : {';
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
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/SearchData';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "database" : "<?= $data['database'] ?>", "table" : "<?= $data['table'] ?>", "data" : {';            
            
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
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">删除表</a>操作吗？</h4>');
            $("#danger_confirm").attr('onclick', 'dele_table_exec()');
            $("#danger_confirm_modal").modal('show');
        }
        
        //执行删除表
        function dele_table_exec(){
            $("#danger_confirm").html('<span class="glyphicon glyphicon-flash" aria-hidden="true"></span>正在处理中，请稍候...');
            $("#danger_confirm").attr('disabled', 'disabled');
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/DeleTable';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
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
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/TruncateTable';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //重命名
        function rename_table(){
            $("#danger_confirm").removeAttr('disabled');
            $("#danger_confirm").html('确认');
            $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">重命名表</a>操作吗？</h4>');
            $("#danger_confirm").attr('onclick', 'rename_table_exec()');
            $("#danger_confirm_modal").modal('show');
        }
        
        //执行重命名
        function rename_table_exec(){
            $("#danger_confirm").html('<span class="glyphicon glyphicon-flash" aria-hidden="true"></span>正在处理中，请稍候...');
            $("#danger_confirm").attr('disabled', 'disabled');
            if ($("#new_table_name").val() != ''){
                var data = new Array();
                data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/RenameTable';
                data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
                data['data'] += '"old_table_name" : "<?= $data['table'] ?>", "new_table_name" : "' + $("#new_table_name").val() + '", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
                parent.IframeSend(data);
            }
        }
        
    </script>
    <script src="<?= base_url('./echarts/dist/echarts-all.js') ?>"></script>
    <script>
    //直接在tab中无法使用echart，需要等待tab执行结束后再执行一遍
    $(function (){
        $('#chart_tab[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $("#chart_x_select").html('');
            var chart_cols_data_length = chart_data['cols'].length;
            for (i = 0; i < chart_cols_data_length; i++){
                $("#chart_x_select").append('<option>' + chart_data['cols'][i] + '</option>');
            }
            
            if (!chart_data['cols'].length){
                alert('开始分析前请输入SQL命令,您可以直接点击“执行”按钮');
                $("#sql_area").html("SELECT * FROM <?= $data['database']?>.<?= $data['table'] ?>");
                $("#sql_tab").tab('show');
            } else {
                setChart(chart_data['cols'][0]);
            }            
        })
        
        $('#chart_x_select,#chart_square_switch').change(function (e) {               
//            alert($("#chart_x_select").val());
            setChart($("#chart_x_select").val());
        })
    });
    
    //设置显示
    function setChart(x){        
        var chart_square_switch = $("#chart_square_switch").prop('checked');
        var myChart = echarts.init(document.getElementById('chart_view')); 
        var option = {
            title : {
                text: '<?= $data['database']?>.<?= $data['table']?>',
                subtext: '统计数据由SQL命令生成'
            },
            tooltip : {
                trigger: 'axis',
//                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
//                    type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
//                }
            },
            legend: {
                //类别
                data:[]
            },
            toolbox: {
                show : true,
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            xAxis : [
                {
                    type : 'category',
                    boundaryGap : false,
                    data : [' ']
                }
            ],
            yAxis : [
                {
                    type : 'value',
                }
            ],
            series : [
            ]
        };

        //legend
        option.legend.data.length = 0;
        //series
        option.series.length = 0;
        for (i in chart_data['cols']){
            option.legend.data.push(chart_data['cols'][i]);
            //根据legend生成i组series数组等待填充
            option.series[i] = new Object();
            option.series[i].name = chart_data['cols'][i];
            option.series[i].type = 'line';
            option.series[i].data = new Array();
            
            
            if (true === chart_square_switch){
                option.series[i].itemStyle = new Object();
                option.series[i].itemStyle.normal = new Object();
                option.series[i].itemStyle.normal.areaStyle = new Object();
                option.series[i].itemStyle.normal.areaStyle.type = 'default';
                option.series[i].smooth = true;
            }
            
            option.series[i].markPoint = new Object();
            option.series[i].markPoint.data = new Array();
            option.series[i].markPoint.data[0] = new Object();
            option.series[i].markPoint.data[0].type = 'max';
            option.series[i].markPoint.data[0].name = '最大值';
            
            option.series[i].markPoint.data[1] = new Object();
            option.series[i].markPoint.data[1].type = 'min';
            option.series[i].markPoint.data[1].name = '最小值';

//            option.series[i].markLine = new Object();
//            option.series[i].markLine.data = new Array();
//            option.series[i].markLine.data[0] = new Object();
//            option.series[i].markLine.data[0].type = 'average';
//            option.series[i].markLine.data[0].name = '平均值';            
        }
        
        if (chart_data['data'].length != 0){
            option.xAxis[0].data.length = 0;
        }
        
        //大型for循环   
        for (i in chart_data['data']){
        //xAxis
            if ("undefined" !== chart_data['data'][i][x]){
                option.xAxis[0].data.push(chart_data['data'][i][x]);
            } else {
                alert('横坐标错误');
                return 0;
            }
            
        //series
            var m = 0;
            for (n in chart_data['data'][i]){                                 
                option.series[m].data.push(chart_data['data'][i][n]);
                m++;
            }
            
        }
        console.log(option);
        myChart.setOption(option); 
    }
    
    
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
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/UpdateData';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>", "old_data" : {';
        
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
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/DeleData';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>", "old_data" : {';
        
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
    
    //设置快照
    function set_snapshot(snap_type){
        var data = new Array();
        data['src'] = location.href.slice((location.href.lastIndexOf("/")));
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/SetSnapShot';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"snap_type" : "' + snap_type + '", "table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
        parent.IframeSend(data);
    }
    
    //删除快照
    function snap_dele(type, name){
        $("#danger_confirm").removeAttr('disabled');
        $("#danger_confirm").html('确认');
        $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">删除快照</a>操作吗？</h4>');
        $("#danger_confirm").attr('onclick', 'snap_dele_exec(' + type + ', "' + name + '")');
        $("#danger_confirm_modal").modal('show');
    }
    
    //执行删除快照
    function snap_dele_exec(type, name){
        var data = new Array();
        data['src'] = location.href.slice((location.href.lastIndexOf("/")));
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/DeleSnapshot';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"snap_type" : "' + type + '", "table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "snap_name" : "' + name + '"}';
        parent.IframeSend(data);
    }
    
    //回滚快照
    function snap_rewind(type, name){
        $("#danger_confirm").removeAttr('disabled');
        $("#danger_confirm").html('确认');
        $("#danger_confirm_body").html('<h4><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>确认执行<a style="color:red">回滚快照</a>操作吗？</h4>');
        $("#danger_confirm").attr('onclick', 'snap_rewind_exec(' + type + ', "' + name + '")');
        $("#danger_confirm_modal").modal('show');
    }
    
    //下载快照
    function snap_download(type, name){
        if ($("#snap_download_iframe").length){
            $("#snap_download_iframe").remove();
        } else {
            $("#backup").append("<iframe id='snap_download_iframe' hidden='hidden' src='" + location.href.slice(0, location.href.lastIndexOf("/")) + "/index.php?c=TableInfo&m=DownloadSnapshot&user_key=<?= $user_key ?>&user_name=<?= $user_name ?>&database=<?= $data['database'] ?>&db_type=<?= $db_type?>&snap_type=" + type + "&table=<?= $data['table'] ?>&snap_name=" + name + "'>");
        }
    }
    
    //执行回滚快照
    function snap_rewind_exec(type, name){
        var data = new Array();
        data['src'] = location.href.slice((location.href.lastIndexOf("/")));
        data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/RewindSnapshot';
        data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
        data['data'] += '"snap_type" : "' + type + '", "table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "snap_name" : "' + name + '"}';
        parent.IframeSend(data);
    }
    </script>
</html>