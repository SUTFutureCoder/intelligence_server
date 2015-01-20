<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 测试场
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class Test extends CI_Controller{
    function __construct() {
        parent::__construct();
    }
    
    public function TestSubStr(){
        $str = time() . 'username|password';
        $user_name = substr($str, strlen(time()), strpos($str, '|') - strlen(time()));
        echo $user_name;
        echo "<br/>";
        echo strpos($str, '|');
        $encrypted_password = substr($str, strpos($str, '|') + 1);
        echo $encrypted_password;
    }
    
    public function TestMS(){
        $a = microtime(true);
        sleep(2);
        $b = microtime(true);
        echo number_format($b - $a, '8');
    }
}