<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 套件的入口文件
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
         
class Index extends CI_Controller{
    function __construct() {
        parent::__construct();
    }
    
    /**    
     *  @Purpose:    
     *  安装界面和登录界面的切换    
     *  @Method Name:
     *  Index()    
     *  @Parameter: 
     *     
     *  @Return: 
     *  
     * :WARNING: 请不要地址末尾加上index.php打开 :WARNING:
    */
    public function Index()
    {   
        $this->load->view('login_view');
    }
    
    /**    
     *  @Purpose:    
     *  密码验证并后续处理    
     *  @Method Name:
     *  PassCheck()    
     *  @Parameter: 
     *  post db_username 数据库用户名
     *  post db_password 数据库密码    
     *  @Return: 
     *  json 状态码及状态说明
    */
    public function PassCheck(){
        $this->load->library('session');
        $this->load->library('database');
        
        $clean = array();
        if (!$this->input->post('db_username', TRUE)){
            echo json_encode(array(
                '0' => -1,
                '1' => '请填写数据库用户名'
            ));
            exit();
        }
        
        
        if (!$this->input->post('db_password', TRUE)){
            echo json_encode(array(
                '0' => -2,
                '1' => '请填写数据库密码'
            ));
            exit();
        }
        
        if (!$this->input->post('db_type', TRUE)){
            echo json_encode(array(
                '0' => -3,
                '1' => '请填写数据库类型'
            ));
            exit();
        }
        
        if (NULL != $this->input->post('db_port', TRUE)){
            $clean['port'] = $this->input->post('db_port', TRUE);
        } else {
            $clean['port'] = NULL;
        }
        
        if (NULL != $this->input->post('db_host', TRUE)){
            $clean['host'] = $this->input->post('db_host', TRUE);
        } else {
            $clean['host'] = 'localhost';
        }
        
        $conn_result = $this->database->connectInit(0, $this->input->post('db_type', TRUE), $this->input->post('db_username', TRUE), $this->input->post('db_password', TRUE), $clean['host'], $clean['port']);
        if ($conn_result == 1){
            
            $this->session->set_userdata('db_username', $this->input->post('db_username', TRUE));
            $this->session->set_userdata('db_password', $this->input->post('db_password', TRUE));
            $this->session->set_userdata('db_type', $this->input->post('db_type', TRUE));
            $this->session->set_userdata('db_host', $clean['host']);
            $this->session->set_userdata('db_port', $clean['port']);
            echo json_encode(array(
                '0' => 1
            ));
            exit();
        } else {
            echo json_encode(array(
                '0' => $conn_result['code'],
                '1' => $conn_result['message']
            ));
            exit();
        }
        
        return 0;
    }

}