<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 手机端页面
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2015 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    
 * @link       https://github.com/SUTFutureCoder/
*/
class Mobile extends CI_Controller{
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
        $this->load->view('mobile_login_view');
    }
    
    
    public function MobileControlCenter(){
        $this->load->library('session');
        $this->load->model('sql_lib');
        $data = array();
        
        if (!$this->session->userdata('db_username')){
            header("Content-Type: text/html;charset=utf-8");
            echo '<script>alert("无法确认您的身份，请重新登录")</script>';
            echo '<script>window.location.href= \'' . base_url() . '\'index.php\'mobile;</script>'; 
        }
              
        $db_table_list = $this->sql_lib->getDbTableList();
        foreach ($db_table_list['data'] as $db_id => $db_array){
            $data[$db_array['TABLE_SCHEMA']][] = $db_array['TABLE_NAME'];
        }
        
        $this->load->view('mobile_control_center', array('db_list' => $data, 'db_username' => $this->session->userdata('db_username')));
    }
}