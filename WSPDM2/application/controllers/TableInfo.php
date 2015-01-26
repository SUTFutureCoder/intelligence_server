<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 获取、操作表数据
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2015 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt   GPL2.0 License
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
        $this->load->model('sql_lib');
        
        $data = array();
        
        $data['start'] = 0;
        $data['end'] = 30;        
        $data['table'] = htmlentities($this->input->get('t', TRUE), ENT_QUOTES);
        $data['database'] = htmlentities($this->input->get('db', TRUE), ENT_QUOTES);
        
        //获取浏览数据
        $data_temp = array();
        if (0 == ($data_temp = $this->sql_lib->getTableData($data['database'], 
                $data['table'], 
                $data['start'], 
                $data['end']))){
            echo '<script>alert("该表不存在");</script>';
            return 0;
        } else {
            $data = array_merge($data, $data_temp);
        }
        
        unset($data_temp);
        $data = array_merge($data, $this->sql_lib->getColData($data['database'], $data['table']));
        
        $this->load->view('TableInfoView', array('data' => $data,
                            'user_key' => $this->secure->CreateUserKey($this->session->userdata('db_username'),
                                    $this->session->userdata('db_password')),
                            'user_name' => $this->session->userdata('db_username'),
                            'db_type' => $this->session->userdata('db_type'),
                            'db_host' => $this->session->userdata('db_host'),
                            'db_port' => $this->session->userdata('db_port')));
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
     *  POST sql      SQL指令
     *  POST memcache 使用缓存
     *  POST db_type  数据库类型
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     *  
     *  @Return: 
     *  状态码|说明
     *      1|data
     *      0|错误信息
     * 
     *  
    */ 
    public function ExecSQL(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->model('sql_lib');
        
        $data = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$sql = trim($this->input->post('sql'), TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'SQL命令不能为空', 'sql_area');
        }
        
        if (!$this->input->post('db_type', TRUE) || !$db['user_name']){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -4, 'SQL信息缺失，请重新登录');
        }
        
        
        switch (substr($sql, 0, 6)){
            case 'SELECT':
            case 'select':
                $data = $this->sql_lib->execSQL($query = 1, $this->input->post('sql', TRUE), 
                        $this->input->post('db_type', TRUE), 
                        $db['user_name'], 
                        $db['password'], 
                        $this->input->post('db_host', TRUE),
                        $this->input->post('db_port', TRUE), 
                        $this->input->post('memcache', TRUE));

                if (is_string($data)){
                    $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'SQL语句出错,出错信息:' . $data);
                }

                if (isset($data['data'])){
                    foreach ($data['data'] as $key => $value){
                        foreach ($value as $col_name => $col_value){
                            $data['cols'][] = $col_name;
                        }
                        break;
                    }
                } else {
                    $data['cols'] = 0;
                }
                
                
                break;
                
            default :
                $data = $this->sql_lib->execSQL($query = 0, $this->input->post('sql', TRUE), 
                        $this->input->post('db_type', TRUE), 
                        $db['user_name'], 
                        $db['password'], 
                        $this->input->post('db_host', TRUE), 
                        $this->input->post('db_port', TRUE));
                if (is_string($data)){
                    $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'SQL语句出错,出错信息:' . $data);
                }
                break;
        } 
            
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
     *  POST db_type  数据库类型
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function InsertData(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->model('sql_lib');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }        
        
        if (!$this->input->post('db_type', TRUE) || !$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('table', TRUE) ||
                null == $this->input->post('data', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'SQL信息缺失，请重新登录');
        }
        
        //取出post进的数据
        $post_data = $this->input->post('data', TRUE);
        
        $data = $this->sql_lib->insertData($this->input->post('database', TRUE), 
                $this->input->post('table', TRUE),
                $post_data,
                $this->input->post('db_type', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE));
        
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'SQL语句出错,出错信息:' . $data);
        }
        
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
     *  POST db_type  数据库类型
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function SearchData(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->model('sql_lib');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }        
        
        if (!$this->input->post('db_type', TRUE) || !$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('table', TRUE) ||
                null == $this->input->post('data', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'SQL信息缺失，请重新登录');
        }
        
        //取出post进的数据
        $post_data = $this->input->post('data', TRUE);
        
        $data = $this->sql_lib->searchData($this->input->post('database', TRUE),
                $this->input->post('table', TRUE),
                $post_data,
                $this->input->post('db_type', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE));
        
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'SQL语句出错,出错信息:' . $data);
        }      
        if (isset($data['data'])){
            foreach ($data['data'][0] as $key => $value){  
                $data['cols'][] = $key;   
            }
        }
        
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
     *  POST db_type  数据库类型
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function DeleCol(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->model('sql_lib');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$this->input->post('db_type', TRUE) || !$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('table', TRUE) || 
                null == $this->input->post('col_name', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'SQL信息缺失，请重新登录');
        }
                
        //执行SQL语句，为记录模式
        //ALTER TABLE `activity` DROP `act_section`
        $data = $this->sql_lib->deleCol($this->input->post('database', TRUE), 
                $this->input->post('table', TRUE), 
                $this->input->post('col_name', TRUE), 
                $this->input->post('db_type', TRUE), 
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE));
                
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'SQL语句出错,出错信息:' . $data);
        }
        
        $data['col_name'] = $this->input->post('col_name', TRUE);
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
     *  POST db_type  数据库类型
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function DeleTable(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->model('sql_lib');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$this->input->post('db_type', TRUE) || !$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('table', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'SQL信息缺失，请重新登录');
        }
        
        //执行SQL语句，为记录模式
        $data = $this->sql_lib->deleTable($this->input->post('database', TRUE),
                $this->input->post('table', TRUE),
                $this->input->post('db_type', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE));
        
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'SQL语句出错,出错信息:' . $data);
        }
        
        $data['table'] = $this->input->post('table', TRUE);
        $data['database'] = $this->input->post('database', TRUE);
        
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
     *  POST db_type  数据库类型
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function TruncateTable(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->model('sql_lib');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$this->input->post('db_type', TRUE) || !$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('table', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'SQL信息缺失，请重新登录');
        }
        
        //执行SQL语句，为记录模式
        $data = $this->sql_lib->truncateTable($this->input->post('database', TRUE),
                $this->input->post('table', TRUE),
                $this->input->post('db_type', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE));
        
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'SQL语句出错,出错信息:' . $data);
        }
        
        $data['table'] = $this->input->post('table', TRUE);
        $data['database'] = $this->input->post('database', TRUE);
        
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
     *  POST db_type  数据库类型
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function RenameTable(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->model('sql_lib');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$this->input->post('db_type', TRUE) || !$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('old_table_name', TRUE) || 
                null == $this->input->post('new_table_name', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'SQL信息缺失，请重新登录');
        }

        //执行SQL语句，为记录模式
        $data = $this->sql_lib->renameTable($this->input->post('database', TRUE),
                $this->input->post('old_table_name', TRUE),
                $this->input->post('new_table_name', TRUE),
                $this->input->post('db_type', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE));
        
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'SQL语句出错,出错信息:' . $data);
        }
        $data['old_table_name'] = $this->input->post('old_table_name', TRUE);
        $data['new_table_name'] = $this->input->post('new_table_name', TRUE);
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'RenameTable', $data);
               
    }
    
    /**    
     *  @Purpose:    
     *  广播刷新表（表数据已被更改）   
     *  @Method Name:
     *  B_ReFreshTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST sql      其他用户执行的sql指令
     *  POST col      其他用户执行的sql指令影响的行数
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function B_ReFreshTable(){
        $this->load->library('secure');
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
        
        $data = array();
        $data['sql'] = $this->input->post('sql', TRUE);
        $data['col'] = $this->input->post('col', TRUE);
        $data['user_name'] = $this->input->post('user_name', TRUE);
        
        $this->data->Out('group', $this->input->post('src', TRUE), 1, 'B_ReFreshTable' ,  $data);
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