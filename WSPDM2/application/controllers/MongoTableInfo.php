<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 获取、操作nosql数据
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2015 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    
 * @link       https://github.com/SUTFutureCoder/
*/
class MongoTableInfo extends CI_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->library('session');
        $this->load->library('secure');
        $this->load->library('mongodatabase');
        
        if (!$this->session->userdata('db_username')){
            header("Content-Type: text/html;charset=utf-8");
            echo '<script>alert("您的会话已过期，请重新登录")</script>';
            echo '<script>window.parent.location.href= \'' . base_url() . '\';</script>'; 
            exit();
        }
        
        $data = array();
        $data['start'] = 0;
        $data['limit'] = 30;
        $data['collection'] = htmlentities($this->input->get('col', TRUE), ENT_QUOTES);
        $data['database'] = htmlentities($this->input->get('db', TRUE), ENT_QUOTES);
        
        //获取浏览数据
        $data_temp = array();
        if (0 == ($data_temp = $this->mongodatabase->getCollectionData($data['database'],
                $data['collection'],
                $data['start'],
                $data['limit']))){
            echo '<script>alert("该集合不存在")</script>';
            return 0;
        } else {
            $data = array_merge($data, $data_temp);
        }
        
        $data['data_sum'] = $this->mongodatabase->getCollectionDataSum($data['database'], $data['collection']);
        unset($data_temp);
        
        $this->load->view('MongoTableInfoView', array(
            'data' => $data,
            'user_key' => $this->secure->CreateUserKey($this->session->userdata('db_username'), $this->session->userdata('db_password')),
            'user_name' => $this->session->userdata('db_username'),
            'db_type' => $this->session->userdata('db_type'),
            'db_host' => $this->session->userdata('db_host'),
            'db_port' => $this->session->userdata('db_port'),
//            'snapshot' => $backup_list
            
        ));
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
     *  POST collection    操作集合
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
        $this->load->library('mongodatabase');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('collection', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, '数据库信息缺失');
        }
        
        $data = $this->mongodatabase->deleCollection($this->input->post('database', TRUE),
                $this->input->post('collection', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE));
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, '删除集合出错,出错信息:' . $data);
        }
        
        $data['collection'] = $this->input->post('collection', TRUE);
        $data['database'] = $this->input->post('database', TRUE);
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'DeleTable', $data);
               
    }
    
    
    /**    
     *  @Purpose:    
     *  广播删除集合   
     *  @Method Name:
     *  B_DeleTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 数据库名
     *  POST collection 集合名
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
        $data['table'] = $this->input->post('collection', TRUE);
        $this->data->Out('group', $this->input->post('src', TRUE), 1, 'B_DeleTable', $data);
    }
    
    
    /**    
     *  @Purpose:    
     *  清除集合   
     *  @Method Name:
     *  TruncateTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST table    操作集合
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
        $this->load->library('mongodatabase');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('collection', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, '数据库信息缺失，请重新登录');
        }
        
        //执行SQL语句，为记录模式
        $data = $this->mongodatabase->truncateTable($this->input->post('database', TRUE),
                $this->input->post('collection', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE));
        
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, '清除集合数据出错,出错信息:' . $data);
        }
        
        $data['collection'] = $this->input->post('collection', TRUE);
        $data['database'] = $this->input->post('database', TRUE);
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'TruncateTable', $data);
               
    }
    
    /**    
     *  @Purpose:    
     *  修改数据   
     *  @Method Name:
     *  UpdateData()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST table    操作表
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     *  POST key    目的_id
     *  POST new_data Array 新数据数组
     * 
     *  @Return: 
     *  状态码|说明
     *      0|修改失败或未更改
     *      1|修改成功
     * 
     * 
     *  
    */ 
    public function UpdateData(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->library('mongodatabase');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('collection', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, '数据库信息缺失');
        }
        
        $new_data = array();
        
        $new_data = $this->input->post('new_data', TRUE);
        
        $data = array();
        $data = $this->mongodatabase->updateData($this->input->post('database', TRUE),
                $this->input->post('collection', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE),
                $this->input->post('key', TRUE),
                $new_data);    
                
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, '修改出错,出错信息:' . $data);
        }
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'UpdateData', $data);
    }
    
    
    /**    
     *  @Purpose:    
     *  广播修改数据  
     *  @Method Name:
     *  B_UpdateData()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST key      目标_id
     *  POST new_data 新数据 base64
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function B_UpdataData(){
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
        
        $data['id'] = $this->input->post('key', TRUE);
        $data['sql'] = base64_decode($this->input->post('sql', TRUE));
        $data['col'] = $this->input->post('col', TRUE);
        $data['new_data'] = base64_decode($this->input->post('new_data', TRUE));
        
        $this->data->Out('group', $this->input->post('src', TRUE), 1, 'B_UpdateData', $data);
    }
    
    /**    
     *  @Purpose:    
     *  执行noSQL语句   
     *  @Method Name:
     *  ExecSQL()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST nosql      noSQL指令
     *  POST nosql_type noSQL指令操作
     *  POST memcache 使用缓存
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     *  POST database 数据库
     *  POST collection 集合名
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
        $this->load->library('mongodatabase');
        
        $data = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$nosql = $this->input->post('nosql', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, 'nosql JSON串不能为空', 'sql_area');
        }
        
        if (!$db['user_name']){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -4, '数据库信息缺失');
        }
        
        
        switch ($this->input->post('nosql_type', TRUE)){
            case 'find':
            case 'update':
            case 'dele':
                $data = $this->mongodatabase->execSQL($this->input->post('nosql_type', TRUE), 
                        base64_decode($this->input->post('nosql', TRUE)), 
                        $db['user_name'], 
                        $db['password'], 
                        $this->input->post('db_host', TRUE),
                        $this->input->post('db_port', TRUE), 
                        $this->input->post('database', TRUE),
                        $this->input->post('collection', TRUE),
                        $this->input->post('memcache', TRUE));
                break;
                
            default :
                if (is_string($data)){
                    $this->data->Out('iframe', $this->input->post('src', TRUE), 0, 'nosql操作类型出错');
                }
                break;
        } 
            
        
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, '语句出错,出错信息:' . $data);
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
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'ExecSQL', $data);
               
    }
    
    
    /**    
     *  @Purpose:    
     *  删除数据   
     *  @Method Name:
     *  DeleData()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST database 操作数据库
     *  POST collection    操作表
     *  POST db_host  数据库地址
     *  POST db_port  数据库端口
     *  POST key      操作_id
     *  @Return: 
     *  状态码|说明
     *      0|修改失败或未更改
     *      1|修改成功
     * 
     * 
     *  
    */ 
    public function DeleData(){
        $this->load->library('secure');
        $this->load->library('data');
        $this->load->library('mongodatabase');
        
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, '未检测到密钥');
        }
        
        if (!$db['user_name'] || 
                null == $this->input->post('database', TRUE) || 
                null == $this->input->post('collection', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -3, '数据库信息缺失，请重新登录');
        }
        
        $data = array();
        $data = $this->mongodatabase->deleData($this->input->post('database', TRUE),
                $this->input->post('collection', TRUE),
                $db['user_name'],
                $db['password'],
                $this->input->post('db_host', TRUE),
                $this->input->post('db_port', TRUE),
                $this->input->post('key', TRUE));    
        
        if (is_string($data)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), 0, '删除出错,出错信息:' . $data);
        }
        
        $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'DeleData', $data);
    }
    
    
    /**    
     *  @Purpose:    
     *  广播删除表
     *  @Method Name:
     *  B_ReFreshTable()
     *  @Parameter: 
     *  POST user_name 数据库用户名
     *  POST user_key 用户密钥
     *  POST src      目标地址
     *  POST key      操作_id
     *  POST sql      使用命令
     *  POST col      影响行数
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function B_DeleData(){
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
        
        $data['id'] = $this->input->post('key', TRUE);
        $data['user_name'] = $db['user_name'];
        $data['sql'] = base64_decode($this->input->post('sql', TRUE));
        $data['col'] = $this->input->post('col');
        
        $this->data->Out('group', $this->input->post('src', TRUE), 1, 'B_DeleData', $data);
    }
    
}