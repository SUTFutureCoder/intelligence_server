<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * SQL代码库
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class Sql_lib extends CI_Model{
    static private $_ci;
    static private $_db;
            
    function __construct() {
        parent::__construct();
    }
    
    //获取数据库、表列表
    public function getDbTableList(){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        $this->database->connect();
                
        return self::$_ci->database->query('SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES', 0);
    }
    
    //获取数据库、表列表
    public function getDbInfo(){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        $this->database->connect();
        return self::$_ci->database->query('SELECT * FROM information_schema.GLOBAL_VARIABLES WHERE '
                . ' VARIABLE_NAME = "COLLATION_SERVER" '
                . ' OR VARIABLE_NAME = "CHARACTER_SET_SYSTEM" '
                . ' OR VARIABLE_NAME = "VERSION_COMPILE_OS" '
                . ' OR VARIABLE_NAME = "DEFAULT_STORAGE_ENGINE" '
                . ' OR VARIABLE_NAME = "PROTOCOL_VERSION" '
                . ' OR VARIABLE_NAME = "STORAGE_ENGINE" '
                . ' OR VARIABLE_NAME = "VERSION_COMPILE_OS" '
                . ' OR VARIABLE_NAME = "VERSION_COMPILE_MACHINE" '
                . ' OR VARIABLE_NAME = "GENERAL_LOG_FILE" '
                . ' OR VARIABLE_NAME = "SOCKET" '
                . ' OR VARIABLE_NAME = "VERSION"', 0);
    }
    
    //修改用户密码
    public function updateUserPass($db_type, $db_username, $db_old_password, $db_password, $db_host, $db_port){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        self::$_db = NULL;
        self::$_db = $this->database->connect(1, $db_type, $db_username, $db_old_password, $db_host, $db_port);   
        $result = self::$_ci->database->query('UPDATE mysql.user SET password=PASSWORD(' . self::$_db->quote($db_password) . ') WHERE User=' . self::$_db->quote($db_username) . '');
        self::$_ci->database->query("FLUSH PRIVILEGES");        
        return $result;
    }
}