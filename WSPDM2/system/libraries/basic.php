<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 基础信息储存文件
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class Basic extends CI_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    //此处填写社团名称
    public $organ_name = '啛啛喳喳';
    
    //学号长度
    public $user_number_length = 9;
    
    //此处填写登录失败重试锁定次数
    public $login_error_lock = 30;
    
    
    
}
