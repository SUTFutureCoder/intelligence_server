<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 数据库基础信息
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class BasicDbInfo extends CI_Controller{
    function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->library('session');
        $this->load->library('secure');
        $this->load->model('sql_lib');
        
        $data = array();
        $db_info = array();
        
        if (!$this->session->userdata('db_username')){
            header("Content-Type: text/html;charset=utf-8");
            echo '<script>alert("您的会话已过期，请重新登录")</script>';
            echo '<script>window.location.href= \'' . base_url() . '\';</script>'; 
        }
        
        $data = $this->sql_lib->getDbInfo();
        foreach ($data['data'] as $row){
            $db_info[$row['VARIABLE_NAME']] = $row['VARIABLE_VALUE'];
        }
        
        unset($data);
        
        $db_snap = $this->GetDbSnapShot($this->session->userdata('db_type'));
        $this->load->view('BasicDbInfoView', array('db_info' => $db_info,
                                                    'user_key' => $this->secure->CreateUserKey($this->session->userdata('db_username'), $this->session->userdata('db_password')),
                                                    'user_name' => $this->session->userdata('db_username'),                                                    
                                                    'host' => $this->session->userdata('db_host'),
                                                    'port' => $this->session->userdata('db_port'),
                                                    'type' => $this->session->userdata('db_type'),
                                                    'db_snap' => $db_snap));
    }   
    
    /**    
     *  @Purpose:    
     *  修改数据库密码
     *  
     *  @Method Name:
     *  UpdatePW()
     *  @Parameter: 
     *  $()
     *  
     *  @Return: 
     *  
     * 
    */
    public function UpdatePW(){
        //此接口仅开放于mysql
        $this->load->library('secure');
        $this->load->library('database');
        $this->load->library('data');
        $this->load->model('sql_lib');
        
        $data = array();
        
        if ($this->input->post('new_pw_confirm', TRUE) != $this->input->post('new_pw', TRUE)){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -6, '两次输入的密码不一致', 'new_pw');
        }
                
        $db = array();
        if ($this->input->post('user_name', TRUE) && $this->input->post('user_key', TRUE)){
            $db = $this->secure->CheckUserKey($this->input->post('user_key', TRUE));
            if ($this->input->post('user_name', TRUE) != $db['user_name']){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -1, '密钥无法通过安检');
            }
        } else {
            $this->data->Out('iframe', $this->input->post('src', TRUE), -7, '未检测到密钥');
        }
        
        if ($this->input->post('old_pw', TRUE) != $db['password']){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -5, '旧密码错误', 'old_pw');
        }
        
        //过滤注入
        if (strpos($this->input->post('new_pw', TRUE), '\'') !== FALSE ||
                strpos($this->input->post('new_pw', TRUE), '"') !== FALSE ||
                strpos($this->input->post('new_pw', TRUE), '\\') !== FALSE){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -4, '密码不支持特殊字符', 'new_pw');
        }
        
        $db['type'] = $this->input->post('type', TRUE);
        $db['host'] = $this->input->post('host', TRUE);
        $db['port'] = $this->input->post('port', TRUE);
        
        $conn = $this->database->connect(0, $db['type'], $db['user_name'], $db['password'], $db['host'], $db['port']);
        
        if ($conn != 1){
            $this->data->Out('iframe', $this->input->post('src', TRUE), -2, $conn['message'], 'old_pw');
        } else {
            if (!$this->sql_lib->updateUserPass($db['type'], $db['user_name'], $db['password'], $this->input->post('new_pw', TRUE), $db['host'], $db['port'])){
                $this->data->Out('iframe', $this->input->post('src', TRUE), -3, '修改失败');
            }  else {
                $this->data->Out('iframe', $this->input->post('src', TRUE), 1, 'UpdatePW');
            } 
        }
        
    }
    
    /**    
     *  @Purpose:    
     *  便利获取数据库快照列表   
     *  @Method Name:
     *  GetDbSnapShot()
     *  @Parameter: 
     *  $db_type        数据库类型
     * 
     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    private function GetDbSnapShot($db_type){
        
        $data = array();
        //先获取数据库总体快照        
        if (is_dir('/home/' . get_current_user() . '/wspdm2/snapshot/' . $db_type . '/')){
            $i_db = new FilesystemIterator('/home/' . get_current_user() . '/wspdm2/snapshot/' . $db_type . '/');            
            foreach ($i_db as $db_snap){
                $db_snap_file = new FilesystemIterator('/home/' . get_current_user() . '/wspdm2/snapshot/' . $db_type . '/' . $db_snap->getFilename() . '/');
                foreach ($db_snap_file as $db_snap_file_name){
                    if ($db_snap_file_name->isDir()){
                        continue;
                    }
                    $data[$db_snap->getFilename()][$db_snap_file_name->getFilename()] = round($db_snap_file_name->getSize() / pow(1024, 1), 2) . "KB";
                }
            }
        } else {
            //没有数据库文件夹，直接返回0
            return 0;
        }
        return $data;
    }
}