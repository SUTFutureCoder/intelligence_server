<!DOCTYPE html>  
<html>  
    <head>  
        <title></title>
        <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
        <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">   
        <script>
            //预定义全局变量用于图表显示，图表显示和执行SQL结果一致
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
            <p>正在显示第<?= $data['start'] ?>-<?= $data['end'] ?>(共操作<?= $data['rows'] ?>行，操作消耗<?= $data['time'] ?>秒)</p>
            <p><?= $data['sql']?></p>
        </div>
        <?php endif; ?>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#view" role="tab" data-toggle="tab">浏览</a></li>
            <li role="presentation"><a href="#struct" role="tab" data-toggle="tab">结构</a></li>
            <li role="presentation"><a href="#sql" role="tab" data-toggle="tab">SQL</a></li>
            <li role="presentation"><a href="#insert" role="tab" data-toggle="tab">插入</a></li>
            <li role="presentation"><a href="#search" role="tab" data-toggle="tab">搜索</a></li>
            <li role="presentation"><a href="#chart" id="chart_prepare" role="tab" data-toggle="tab">分析</a></li>
            <li role="presentation"><a href="#operating" role="tab" data-toggle="tab">操作</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="view">
                <br/>
                <table class="table table-hover table-bordered" id="data_view">
                    <thead>
                        <tr>
                            <th>#</th>
                            <?php foreach ($data['cols'] as $col_name => $col_type):?>
                            <th id="data_view_<?= $col_name?>"><?= $col_name ?></th>
                            <script>
                                chart_data['cols'].push("<?= $col_name?>");
                            </script>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?php foreach($data['data'] as $key => $value): ?>                    
                        <tr>
                            <td class="col-sm-1"><?= $key + 1 ?></td>
                            <?php foreach($value as $table_name => $table_value): ?>   
                                <td><?=$table_value?></td>
                                <script>
                                chart_data['data'].push("<?= $table_value?>");
                                </script>
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
        
    </body>
    <script>        
        //接收母窗口传来的值
        function MotherResultRec(data) {
            if (1 == data[2]) {
                if ('group' != data[0]){
                    $("#alert").removeClass("alert-danger");
                    $("#alert").addClass("alert-success");
                    $("#alert").html("<p>共操作" + data[4]['rows'] + "行，操作消耗" + data[4]['time'] + "秒<p>" + "<p>" + data[4]['sql'] + "<p>");
                    switch (data[3]){
                        case 'ExecSQL':
                            $("#sql_result").html("");
                            $("#sql_result").append("<br/><table class=\"table table-hover table-bordered\" id=\"sql_data_view\">");
                            $("#sql_data_view").append("<thead><tr><th>#</th>");
                            
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
                                if (data[4]['cols'].length){
                                    return 0;
                                } else {
                                    $.each(data[4]['cols'], function (col_id, col_name){
                                        $("#sql_data_view thead tr").append("<th>" + col_name + "</th>");
                                    });
                                    $("#sql_data_view").append("</thead><tbody>");                        
                                    //取出数据
                                    $.each(data[4]['data'], function (i, data_item){
            //                            console.log(data_item);
                                        $("#sql_data_view tbody").append("<tr id=" + i + "><td>" + i + "</td></tr>");
                                        $.each(data_item, function (m, data_item_val){
            //                                console.log(data_item_val);
                                            $("#sql_data_view tbody #" + i).append("<td>" + data_item_val + "</td>");
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
                            alert('删除成功');
                            var data = new Array();
                            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data['group'] = 'WSPDM2';
                            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_DeleTable';
                            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>"}';
                            parent.IframeSend(data, 'group');    
                            break;
                            
                        case 'TruncateTable':
                            alert('清除成功');
                            var data = new Array();
                            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data['group'] = 'WSPDM2';
                            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_TruncateTable';
                            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>"}';
                            parent.IframeSend(data, 'group');    
                            break;
                            
                        case 'RenameTable':
                            alert('修改成功');
                            var data_rename = new Array();
                            data_rename['src'] = location.href.slice((location.href.lastIndexOf("/")));
                            data_rename['group'] = 'WSPDM2';
                            data_rename['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/B_RenameTable';
                            data_rename['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>", "database" : "<?= $data['database'] ?>", "old_table_name" : "' + data[4]['old_table_name'] + '", "new_table_name" : "' + data[4]['new_table_name'] + '"}';
                            parent.IframeSend(data_rename, 'group');    
                            break;
                    }
                    //重置表单
                    $("form").each(function () {
                        this.reset();
                    });              
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
                    }
                }
                
            } else {
                $("#alert").removeClass("alert-success");
                $("#alert").addClass("alert-danger");
                $("#alert").html("未操作");
                alert(data[3]);
            }
             
        }       
        
        //SQL输入框按钮
        //@param 
        //sql 准备执行的SQL语句
        //mode 插入到最前还是就地插入
        function sql_button(sql, mode){
            if (!mode){
                $("#sql_area").val(sql);
            } else {
                var old_sql = $("#sql_area").val();
                old_sql += sql;
                $("#sql_area").val(old_sql);
            }            
            $("#sql_area").focus();
        }
        
        //执行SQL语句
        function launch_sql(){
            switch ($("#memcache").val()){
                case 'on':
                    memcache = 1;
                    break;
                case 'off':
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
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/DeleTable';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //清除表
        function truncate_table(){
            var data = new Array();
            data['src'] = location.href.slice((location.href.lastIndexOf("/")));
            data['api'] = location.href.slice(0, location.href.lastIndexOf("/")) + '/index.php/TableInfo/TruncateTable';
            data['data'] = '{"user_key" : "<?= $user_key ?>", "user_name" : "<?= $user_name ?>",';
            data['data'] += '"table" : "<?= $data['table'] ?>", "database" : "<?= $data['database'] ?>", "db_type" : "<?= $db_type?>", "db_host" : "<?= $db_host?>", "db_port" : "<?= $db_port?>"}';
            parent.IframeSend(data);
        }
        
        //重命名
        function rename_table(){
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
    <script src="<?= base_url('./echarts/dist/echarts-all.js')?>"></script>
    <script>
    //直接在tab中无法使用echart，需要等待tab执行结束后再执行一遍
    $(function (){
        $('#chart_prepare[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $("#chart_view").remove();
            $("#chart").append('<div id="chart_view" style="height:400px"></div>');
            var myChart = echarts.init(document.getElementById('chart_view')); 
            var option = {
                title : {
                    text: '<?= $data['database']?>.<?= $data['table']?>',
                    subtext: '统计数据由SQL命令生成'
                },
                tooltip : {
                    trigger: 'axis'
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
                        data : ['周一','周二','周三','周四','周五','周六','周日']
                    }
                ],
                yAxis : [
                    {
                        type : 'value',
    //                    axisLabel : {
    //                        formatter: '{value} °C'
    //                    }
                    }
                ],
                series : [
                    {
                        name:'最高气温',
                        type:'line',
                        data:[11, 11, 15, 13, 12, 13, 10],
                        markPoint : {
                            data : [
                                {type : 'max', name: '最大值'},
                                {type : 'min', name: '最小值'}
                            ]
                        },
                        markLine : {
                            data : [
                                {type : 'average', name: '平均值'}
                            ]
                        }
                    },
                    {
                        name:'最低气温',
                        type:'line',
                        data:[1, -2, 2, 5, 3, 2, 0],
                        markPoint : {
                            data : [
                                {name : '周最低', value : -2, xAxis: 1, yAxis: -1.5}
                            ]
                        },
                        markLine : {
                            data : [
                                {type : 'average', name : '平均值'}
                            ]
                        }
                    }
                ]
            };
            
            delete option.legend.data.length;
            
            
            var chart_cols_data_length = chart_data['cols'].length;
            for (i = 0; i < chart_cols_data_length; i++){
                option.legend.data.push(chart_data['cols'][i]);  
            }
            console.log(option);
            myChart.setOption(option); 
        })
    });
    </script>
</html>