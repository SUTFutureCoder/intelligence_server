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
class Basic
{   
    //此处填写user_key用户密钥生效时限(前端以小时为单位，转换后写入文件)推荐不小于2小时
    public $user_key_life = 43200;
    
}
