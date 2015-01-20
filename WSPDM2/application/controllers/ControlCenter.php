<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 
 * 控制面板
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class ControlCenter extends CI_Controller{
    function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->library('session');
        $this->load->model('sql_lib');
        $data = array();
        
        if (!$this->session->userdata('db_username')){
            header("Content-Type: text/html;charset=utf-8");
            echo '<script>alert("'. $this->session->userdata('db_username') .'")</script>';
            echo '<script>window.location.href= \'' . base_url() . '\';</script>'; 
        }
              
//        $conn = $this->database->connect();
//        $db_list = $conn->query('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA');        
        
       
        $db_table_list = $this->sql_lib->getDbTableList();
        foreach ($db_table_list['data'] as $db_id => $db_array){
            $data[$db_array['TABLE_SCHEMA']][] = $db_array['TABLE_NAME'];
        }
        
        $this->load->view('ControlCenterView', array('db_list' => $data));
        
    }
}