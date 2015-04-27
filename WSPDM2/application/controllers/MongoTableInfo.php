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
}