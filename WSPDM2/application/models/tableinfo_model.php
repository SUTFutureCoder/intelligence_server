<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 操作表数据
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/

class Tableinfo_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
    
    /**    
     *  @Purpose:    
     *  登录数据库   
     *  @Method Name:
     *  getTableData($conn, $db, $table, $limit = NULL, $offset = NULL)
     *  @Parameter: 
     *  $conn   数据库连接
     *  $db     数据库名称
     *  $table  表名称
     *  $offset 偏移量
     *  $limit  数据量

     *  @Return: 
     *  状态码|说明
     *      data
     * 
     *  
    */ 
    public function getTableData($conn, $db, $table, $offset = NULL, $limit = NULL){
        $this->load->library('database');
        $db = mysqli_real_escape_string($conn, $db);
        $table = mysqli_real_escape_string($conn, $table);
        
        $sql = "USE $db";
        if ($this->database->execSQL($conn, $sql, 0)){
            $sql = "SELECT * FROM $table LIMIT $offset, $limit";
            if ($data = $this->database->execSQL($conn, $sql, 1)){
                return $data;
            } 
        }
        return 0;
    }
}