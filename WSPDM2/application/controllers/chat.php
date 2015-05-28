<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 用于显示聊天界面
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2015 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    
 * @link       https://github.com/SUTFutureCoder/
*/
class Chat extends CI_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->library('session');
        $this->load->helper('string'); 
        if (!$this->session->userdata('db_username')){
            header("Content-Type: text/html;charset=utf-8");
            echo '<script>alert("您的会话已过期，请重新登录")</script>';
            echo '<script>window.parent.location.href= \'' . base_url() . '\';</script>'; 
            exit();
        }
        
        $this->load->view('chat_view', array(
            'user_name' => $this->session->userdata('db_username') . '_' . random_string('alnum', 4),
        ));
    }
}