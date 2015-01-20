<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 登录数据库
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class Index_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
    
    /**    
     *  @Purpose:    
     *  登录数据库   
     *  @Method Name:
     *  dbLogin
     *  @Parameter: 
     *  username 数据库用户名
     *  password 数据库密码
     *  @Return: 
     *  状态码|说明
     *      0|登录失败
     *      1|登录成功
     * 
     *  
    */ 
    public function dbLogin($username, $password){
        //取消错误报告
        error_reporting(0);
        $conn = mysqli_connect('localhost', $username, $password);
        
        if (mysqli_connect_errno($conn)){
            echo json_encode(array(
                '0' => 0,
                '1' => mysqli_connect_error()
            ));   
            exit();
        }
        
        return 1;
    }
}