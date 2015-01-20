<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/

class test_memcached_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
    
    public function testMc(){
        $this->load->driver('cache');
        $key = 'testmckey';
        $data = time();
        
        if ($this->cache->memcached->is_supported()){
            echo 'supported memcached';
        } else {
            echo 'not supported memcached';
        }
        
        echo '<br/><br/>';
        
        $is_success = $this->cache->memcached->save($key, $data);
        
        if ($is_success){
            echo 'save success';
        } else {
            echo 'save false';
        }
        
        echo '<br/>============<br/>';
        
        $str = $this->cache->memcached->get($key);
        var_dump('testMC str = ' . $str);
        var_dump($str);
        
    }
    
}