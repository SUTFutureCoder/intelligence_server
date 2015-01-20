<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 获取、操作表数据
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class TableInfo extends CI_Controller{
    function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->library('session');
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->model('tableinfo_model');
        
        $conn = $this->database->dbConnect($this->session->userdata('db_username'), $this->session->userdata('db_password'));
        
        $data = $this->tableinfo_model->getTableData($conn, $this->input->get('db', TRUE), $this->input->get('t', TRUE), 0, 30);
        $data['table'] = htmlentities($this->input->get('t', TRUE), ENT_QUOTES);
        $data['database'] = htmlentities($this->input->get('db', TRUE), ENT_QUOTES);
        $data['start'] = 0;
        $data['end'] = 29;
        
        $this->load->view('TableInfoView', array('data' => $data,
                            'user_key' => $this->secure->CreateUserKey($this->session->userdata('db_username'), $this->session->userdata('db_password')),
                            'user_name' => $this->session->userdata('db_username')));
    } 
       
    /**    
     *  @Purpose:    
     *  执行SQL语句   
     *  @Method Name:
     *  ExecSQL()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST sql      SQL指令
     *  
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function ExecSQL(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$this->input->post('sql', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'SQL命令不能为空');
        }
        
        //连接数据库
        $conn = $this->database->dbConnect($db['user_name'], $db['password']);
        
        //过滤数据库名
        $database = mysqli_real_escape_string($conn, $this->input->post('database', TRUE));
        
        //连接数据库，非记录模式
        $sql = 'USE ' . $database;
        $this->database->execSQL($conn, $sql, 0);
        
        //执行SQL语句，为记录模式
        $sql = $this->input->post('sql', TRUE);
        $data = $this->database->execSQL($conn, $sql, 1);
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'ExecSQL', $data);
               
    }
    
       
    /**    
     *  @Purpose:    
     *  插入行   
     *  @Method Name:
     *  InsertData()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST table      表名
     *  POST array data 插入数据（key => value）
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function InsertData(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }        
        
        //连接数据库
        $conn = $this->database->dbConnect($db['user_name'], $db['password']);
        
        //过滤数据库名
        $database = mysqli_real_escape_string($conn, $this->input->post('database', TRUE));
        
        //过滤表名
        $table = mysqli_real_escape_string($conn, $this->input->post('table', TRUE));
        
        //连接数据库，非记录模式
        $sql = 'USE ' . $database;
        $this->database->execSQL($conn, $sql, 0);
        
        //取出post进的数据
        $post_data = $this->input->post('data', TRUE);
        
        $sql = "INSERT INTO $table (";
        $sql_value = 'VALUES (';        
        $sql_result = "SELECT * FROM $table WHERE ";
        $i = 0;
        $r = 0;
        foreach ($post_data as $key => $value){
            if ($i){
                $sql .= ', ';
                $sql_value .= ', ';
            }
            
            $sql .= "$key";
            if ('on' == $value){
                $value = 1;
            }
            $sql_value .= "'$value'";
            
            if ($value){   
                if ($r){
                    $sql_result .= ' AND ';
                }
                $sql_result .= " $key = '$value' ";
                ++$r;
            }
            ++$i;
        }
        
        $sql .= ") " . $sql_value . '); ';
        
        $sql_result .= ' LIMIT 1';
        //执行SQL语句，为记录模式
        $data = $this->database->execSQL($conn, $sql, 1);   
        
        $data['data'] = $this->database->execSQL($conn, $sql_result, 0, 1);
//        $data['data'] = $sql_result;
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'InsertData', $data);
               
    }
    
    
       
    /**    
     *  @Purpose:    
     *  搜索数据   
     *  @Method Name:
     *  SearchData()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST table      表名
     *  POST array data 搜索数据（key => value）
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function SearchData(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }        
        
        //连接数据库
        $conn = $this->database->dbConnect($db['user_name'], $db['password']);
        
        //过滤数据库名
        $database = mysqli_real_escape_string($conn, $this->input->post('database', TRUE));
        
        //过滤表名
        $table = mysqli_real_escape_string($conn, $this->input->post('table', TRUE));
        
        //连接数据库，非记录模式
        $sql = 'USE ' . $database;
        $this->database->execSQL($conn, $sql, 0);
        
        //取出post进的数据
        $post_data = $this->input->post('data', TRUE);
        
        //初始化搜索字段和命令存储数组
        $search = array();
        
        
        
        $sql_search = "SELECT * FROM $table WHERE ";
        
        //初始化计数器
        $i = 0;
        foreach ($post_data as $post_data_item){
            $col = mysqli_real_escape_string($conn, $post_data_item['col']);
            $cmd = mysqli_real_escape_string($conn, $post_data_item['cmd']);
            $val = mysqli_real_escape_string($conn, $post_data_item['val']);
            if ($i != 0){
                $sql_search .= ' AND ';
            }

            switch ($cmd){
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $val = explode(',', $val, 2);
                    $sql_search .= $col . ' ' . $cmd . ' "' . $val[0] . '" AND "' . $val[1] . '"';
                    break;
                
                case 'LIKE %...%':
                    $sql_search .= $col . ' LIKE "%' . $val . '%" ';
                    break;
                
                case 'IN (...)':
                    $val = explode(',', $val);
                    $sql_search .= $col . ' IN(';
                    //计数器
                    $in = 0;
                    foreach ($val as $in_item){
                        if ($in){
                            $sql_search .= ', ';
                        }
                        $sql_search .= "'" . $in_item . "'";
                        ++$in;
                    }
                    $sql_search .= ') ';
                    break;
                    
                case 'NOT IN (...)':
                    $val = explode(',', $val);
                    $sql_search .= $col . ' NOT IN(';
                    //计数器
                    $in = 0;
                    foreach ($val as $in_item){
                        if ($in){
                            $sql_search .= ', ';
                        }
                        $sql_search .= "'" . $in_item . "'";
                        ++$in;
                    }
                    $sql_search .= ') ';
                    break;
                
                case "= ''":
                case "!= ''":
                case 'IS NULL':
                case 'IS NOT NULL':
                    $sql_search .= $col . ' ' . $cmd . ' ';
                    break;
                
                default :
                    $sql_search .= $col .  ' ' . $cmd . ' "' . $val . '" ';
                    break;
            }
            ++$i;
        }
        
        //执行SQL语句，为记录模式
        $data = $this->database->execSQL($conn, $sql_search, 1);   
        
        $data['data'] = $this->database->execSQL($conn, $sql_search, 0, 1);
//        $data['data'] = $sql_result;
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'SearchData', $data);
               
    }
    
    
    
    /**    
     *  @Purpose:    
     *  删除列   
     *  @Method Name:
     *  DeleCol()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST table    操作表
     *  POST col_name   列名
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function DeleCol(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        //连接数据库
        $conn = $this->database->dbConnect($db['user_name'], $db['password']);
        
        //过滤数据库名
        $database = mysqli_real_escape_string($conn, $this->input->post('database', TRUE));
        $table = mysqli_real_escape_string($conn, $this->input->post('table', TRUE));
        //过滤表名
        $col_name = mysqli_real_escape_string($conn, $this->input->post('col_name', TRUE));

        //连接数据库，非记录模式
        $sql = 'USE ' . $database;
        $this->database->execSQL($conn, $sql, 0);
        
        //执行SQL语句，为记录模式
        //ALTER TABLE `activity` DROP `act_section`
        $sql = 'ALTER TABLE ' . $table . ' DROP COLUMN ' . $col_name . ' ';
        $data = $this->database->execSQL($conn, $sql, 1);
        $data['col_name'] = $col_name;
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'DeleCol', $data);
               
    }    
    
    /**    
     *  @Purpose:    
     *  删除表   
     *  @Method Name:
     *  DeleTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST table    操作表
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function DeleTable(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        //连接数据库
        $conn = $this->database->dbConnect($db['user_name'], $db['password']);
        
        //过滤数据库名
        $database = mysqli_real_escape_string($conn, $this->input->post('database', TRUE));
        $table = mysqli_real_escape_string($conn, $this->input->post('table', TRUE));

        //连接数据库，非记录模式
        $sql = 'USE ' . $database;
        $this->database->execSQL($conn, $sql, 0);
        
        //执行SQL语句，为记录模式
        //ALTER TABLE `activity` DROP `act_section`
        $sql = 'DROP TABLE ' . $table . ' ';
        $data = $this->database->execSQL($conn, $sql, 1);
        $data['table'] = $table;
        $data['database'] = $database;
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'DeleTable', $data);
               
    }
    
    /**    
     *  @Purpose:    
     *  清除表   
     *  @Method Name:
     *  TruncateTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST table    操作表
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function TruncateTable(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        //连接数据库
        $conn = $this->database->dbConnect($db['user_name'], $db['password']);
        
        //过滤数据库名
        $database = mysqli_real_escape_string($conn, $this->input->post('database', TRUE));
        $table = mysqli_real_escape_string($conn, $this->input->post('table', TRUE));

        //连接数据库，非记录模式
        $sql = 'USE ' . $database;
        $this->database->execSQL($conn, $sql, 0);
        
        //执行SQL语句，为记录模式
        //ALTER TABLE `activity` DROP `act_section`
        $sql = 'TRUNCATE ' . $table . ' ';
        $data = $this->database->execSQL($conn, $sql, 1);
        $data['table'] = $table;
        $data['database'] = $database;
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'TruncateTable', $data);
               
    }
    
    /**    
     *  @Purpose:    
     *  修改表名   
     *  @Method Name:
     *  RenameTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST old_table_name    操作表(旧表名)
     *  POST new_table_name    新表名
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function RenameTable(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        //连接数据库
        $conn = $this->database->dbConnect($db['user_name'], $db['password']);
        
        //过滤数据库名
        $database = mysqli_real_escape_string($conn, $this->input->post('database', TRUE));
        $old_table_name = mysqli_real_escape_string($conn, $this->input->post('old_table_name', TRUE));
        $new_table_name = mysqli_real_escape_string($conn, $this->input->post('new_table_name', TRUE));

        //连接数据库，非记录模式
        $sql = 'USE ' . $database;
        $this->database->execSQL($conn, $sql, 0);
        
        //执行SQL语句，为记录模式
        //ALTER TABLE `activity` DROP `act_section`
        $sql = "RENAME TABLE $database.$old_table_name TO $database.$new_table_name";
        $data = $this->database->execSQL($conn, $sql, 1);
        $data['old_table_name'] = $old_table_name;
        $data['new_table_name'] = $new_table_name;
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'RenameTable', $data);
               
    }
    
    /**    
     *  @Purpose:    
     *  广播删除列   
     *  @Method Name:
     *  B_DeleCol()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST col_name   列名
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function B_DeleCol(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                return 0;
            }
        } else {
            return 0;
        }       
        $this->data->Out('group', $this->input->post('src', TRUE), 1, 'B_DeleCol' ,  $this->input->post('col_name', TRUE));
    }
    
    /**    
     *  @Purpose:    
     *  广播删除表   
     *  @Method Name:
     *  B_DeleTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 数据库名
     *  POST table 表
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function B_DeleTable(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                return 0;
            }
        } else {
            return 0;
        }       
        
        $data['database'] = $this->input->post('database', TRUE);
        $data['table'] = $this->input->post('table', TRUE);
        $this->data->Out('group', $this->input->post('src', TRUE), 1, 'B_DeleTable', $data);
    }
    
    /**    
     *  @Purpose:    
     *  广播清除表   
     *  @Method Name:
     *  B_TruncateTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 数据库名
     *  POST table 表
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function B_TruncateTable(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                return 0;
            }
        } else {
            return 0;
        }       
        
        $data['database'] = $this->input->post('database', TRUE);
        $data['table'] = $this->input->post('table', TRUE);
        $this->data->Out('group', $this->input->post('src', TRUE), 1, 'B_TruncateTable', $data);
    }
    
    /**    
     *  @Purpose:    
     *  广播重命名表   
     *  @Method Name:
     *  B_RenameTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 数据库名
     *  POST old_table_name 旧表名
     *  POST new_table_name 新表名
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function B_RenameTable(){
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                return 0;
            }
        } else {
            return 0;
        }       
        
        $data['database'] = $this->input->post('database', TRUE);
        $data['new_table_name'] = $this->input->post('new_table_name', TRUE);
        $data['old_table_name'] = $this->input->post('old_table_name', TRUE);
        $this->data->Out('group', $this->input->post('src', TRUE), 1, 'B_RenameTable', $data);
    }
}