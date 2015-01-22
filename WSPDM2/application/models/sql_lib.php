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
    static private $_ci = null;
    static private $_db = null;
            
    function __construct() {
        parent::__construct();
    }
    
    //获取数据库、表列表
    public function getDbTableList(){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        self::$_ci->database->connect();
                
        return self::$_ci->database->query('SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES', 0);
    }
    
    //获取数据库、表列表
    public function getDbInfo(){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        self::$_ci->database->connect();
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
        self::$_db = self::$_ci->database->connect(1, $db_type, $db_username, $db_old_password, $db_host, $db_port);   
        $result = self::$_ci->database->query('UPDATE mysql.user SET password=PASSWORD(' . self::$_db->quote($db_password) . ') WHERE User=' . self::$_db->quote($db_username) . '');
        self::$_ci->database->query("FLUSH PRIVILEGES");        
        return $result;
    }
    
    //获取表数据
    public function getTableData($db_name, $table_name, $offset = 0, $limit = 30, $db_type = null, $db_username = null, $db_password = null, $db_host = null, $db_port = null){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        if (!self::$_db){
            self::$_db = self::$_ci->database->connect(1, $db_type, $db_username, $db_password, $db_host, $db_port);
        }        
        
        $data = array();
        
        if ($data = self::$_ci->database->page($db_name, $table_name, $offset, $limit)){
            return $data;
        } else {
            return 0;
        }
    }
    
    //获取列数据
    public function getColData($db_name, $table_name, $db_type = null, $db_username = null, $db_password = null, $db_host = null, $db_port = null){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        if (!self::$_db){
            self::$_db = self::$_ci->database->connect(1, $db_type, $db_username, $db_password, $db_host, $db_port);
        } 
        
        $data = array();
        
        //注意可能会出现不兼容的情况
        $sql = 'SELECT COLUMN_NAME, COLUMN_DEFAULT, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, NUMERIC_SCALE, COLLATION_NAME, COLUMN_TYPE, COLUMN_KEY, EXTRA, PRIVILEGES, COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ' . self::$_db->quote($db_name) . ' AND TABLE_NAME = ' . self::$_db->quote($table_name);
        if ($temp = self::$_ci->database->query($sql, 0)){
            foreach ($temp['data'] as $key => $colinfo){
                $data['cols'][$colinfo['COLUMN_NAME']]['type'] = $colinfo['DATA_TYPE'];
                switch ($colinfo['DATA_TYPE']){
                    case 'varchar':
                    case 'text':
                    case 'char':
                    case 'tinytext':
                    case 'mediumtext':
                    case 'longtext':                    
                        $data['cols'][$colinfo['COLUMN_NAME']]['length'] = $colinfo['CHARACTER_MAXIMUM_LENGTH'];
                        break;
                    
                    case 'int':
                    case 'smallint':
                    case 'tinyint':
                    case 'bigint':
                    case 'mediumint':
                        $data['cols'][$colinfo['COLUMN_NAME']]['length'] = $colinfo['NUMERIC_PRECISION'];
                        break;
                    
                    case 'decimal':
                    case 'float':
                    case 'double':
                    case 'real':
                        $data['cols'][$colinfo['COLUMN_NAME']]['length'] = $colinfo['NUMERIC_PRECISION'] . '.' . $colinfo['NUMERIC_SCALE'];
                        break;
                    default :
                        $data['cols'][$colinfo['COLUMN_NAME']]['length'] = NULL;
                        break;
                }
                
                //列默认值
                $data['cols'][$colinfo['COLUMN_NAME']]['default'] = $colinfo['COLUMN_DEFAULT'];
                
                //列是否可为空
                $data['cols'][$colinfo['COLUMN_NAME']]['nullable'] = $colinfo['IS_NULLABLE'];
                
                //列字符集
                $data['cols'][$colinfo['COLUMN_NAME']]['charset'] = $colinfo['COLLATION_NAME'];
                
                //类型和长度
                $data['cols'][$colinfo['COLUMN_NAME']]['type_length'] = $colinfo['COLUMN_TYPE'];
                
                //UNI或PRI
                $data['cols'][$colinfo['COLUMN_NAME']]['key'] = $colinfo['COLUMN_KEY'];
                
                //auto_increment
                $data['cols'][$colinfo['COLUMN_NAME']]['auto'] = $colinfo['EXTRA'];
                
                //权限
                $data['cols'][$colinfo['COLUMN_NAME']]['right'] = $colinfo['PRIVILEGES'];
                
                //注释
                $data['cols'][$colinfo['COLUMN_NAME']]['comment'] = trim($colinfo['COLUMN_COMMENT']);
                
            }
            
            return $data;
        } else {
            return 0;
        }
    }
    
    //执行命令
    public function execSQL($query, $sql, $db_type, $db_username, $db_password, $db_host, $db_port, $memcache = 0){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        self::$_ci->database->connect(0, $db_type, $db_username, $db_password, $db_host, $db_port);
        
        if ($query){
            //SELECT
            return self::$_ci->database->query($sql, 1, $memcache);
        } else {
            return self::$_ci->database->exec($sql, 1);
        }
    }
    
    //删除列
    public function deleCol($database, $table, $col_name, $db_type, $db_username, $db_password, $db_host, $db_port){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        self::$_ci->database->connect(0, $db_type, $db_username, $db_password, $db_host, $db_port);
        
        $sql = 'ALTER TABLE ' . $database . '.' . $table . ' DROP COLUMN ' . $col_name . ' ';
        return self::$_ci->database->exec($sql, 1);
    }
    
    //增加元组
    public function insertData($database, $table, $post_data, $db_type, $db_username, $db_password, $db_host, $db_port){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        if (!self::$_db){
            self::$_db = self::$_ci->database->connect(1, $db_type, $db_username, $db_password, $db_host, $db_port);
        }
        
        
        $sql = "INSERT INTO $database.$table (";
        $sql_value = 'VALUES (';        
        $sql_result = "SELECT * FROM $database.$table WHERE ";
        $i = 0;
        $r = 0;
        foreach ($post_data as $key => $value){
            if ($i){
                $sql .= ', ';
                $sql_value .= ', ';
            }
            
            $sql .= "$key";
            if ('on' == $value){
                $value = 1;
            }
            $sql_value .= self::$_db->quote($value);
            
            if ($value){   
                if ($r){
                    $sql_result .= ' AND ';
                }
                $sql_result .= " $key = " . self::$_db->quote($value) . " ";
                ++$r;
            }
            ++$i;
        }
        
        $sql .= ") " . $sql_value . '); ';
        
        $sql_result .= ' LIMIT 1';
        
        $data = $this->database->exec($sql, 1);
        $data['data'] = $this->database->query($sql_result, 0);
        
        return $data;
    }
    
    //搜索
    public function searchData($database, $table, $post_data, $db_type, $db_username, $db_password, $db_host, $db_port){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('database');
        }
        
        if (!self::$_db){
            self::$_db = self::$_ci->database->connect(1, $db_type, $db_username, $db_password, $db_host, $db_port);
        }
        
        //初始化搜索字段和命令存储数组
        $search = array();
        
        $sql_search = "SELECT * FROM $database.$table WHERE ";
        
        //初始化计数器
        $i = 0;
        foreach ($post_data as $post_data_item){
            $col = $post_data_item['col'];
            $cmd = $post_data_item['cmd'];
            $val = $post_data_item['val'];
            if ($i != 0){
                $sql_search .= ' AND ';
            }

            switch ($cmd){
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $val = explode(',', $val, 2);
                    $sql_search .= $col . ' ' . $cmd . ' ' . self::$_db->quote($val[0]) . ' AND ' . self::$_db->quote($val[1]) . '';
                    break;
                
                case 'LIKE %...%':
                    $sql_search .= $col . ' LIKE "%' . $val . '%" ';
                    break;
                
                case 'IN (...)':
                    $val = explode(',', $val);
                    $sql_search .= $col . ' IN(';
                    //计数器
                    $in = 0;
                    foreach ($val as $in_item){
                        if ($in){
                            $sql_search .= ', ';
                        }
                        $sql_search .= " " . self::$_db->quote($in_item) . " ";
                        ++$in;
                    }
                    $sql_search .= ') ';
                    break;
                    
                case 'NOT IN (...)':
                    $val = explode(',', $val);
                    $sql_search .= $col . ' NOT IN(';
                    //计数器
                    $in = 0;
                    foreach ($val as $in_item){
                        if ($in){
                            $sql_search .= ', ';
                        }
                        $sql_search .= " " . self::$_db->quote($in_item) . " ";
                        ++$in;
                    }
                    $sql_search .= ') ';
                    break;
                
                case "= ''":
                case "!= ''":
                case 'IS NULL':
                case 'IS NOT NULL':
                    $sql_search .= $col . ' ' . $cmd . ' ';
                    break;
                
                default :
                    $sql_search .= $col .  ' ' . $cmd . ' ' . self::$_db->quote($val) . ' ';
                    break;
            }
            ++$i;
        }
        
        
        return  $this->database->query($sql_search, 1);
    }
}