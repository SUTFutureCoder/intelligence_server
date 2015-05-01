<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 数据库连接、基本信息类
 * 
 */
class Database{        
    static private $_db;
    static private $_ci;
    

    public function __construct(){            
    }
    
    static public function connectInit($PDO, $db_type, $user, $passwd, $host = 'localhost', $port = NULL){
        if (!self::$_ci){
            self::$_ci =& get_instance();
        }
        $result = array();
        if (!self::$_db){
            try{
            //构建数据库连接DSN
            switch ($db_type){
                case 'MySQL':
                    $driver = 'mysql:host=' . $host . ';charset=utf8';
                    break;
                case 'MSSQL':
                    $driver = 'mssql:host=' . $host;
                    break;
                case 'ODBC':
                    $driver = 'odbc:Driver={SQL Server};Server=' . $host . ';';
                    break;
                case 'cubrid':
                    $dirver = 'cubrid:host=' . $host . ';';
                    break;
                case 'IBM':
                    $driver = 'DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=' . $host .';';
                    break;
                case 'firebird':
                    $driver = 'firebird:host=' . $host;
                    break;
                //需要配置odbc.ini
                case 'INFORMIX':
                    $driver = 'informix:DSN=Infdrv33';
                    break;
                case 'MS SQL Server':
                    $driver = 'sqlsrv:Server=' . $host . ';';
                    break;                    
                case '4D':
                    $driver = '4D:host=' . $host . ';';                    
                    break;                                    
            } 
            if ($db_type == 'PostgreSQL'){
                if (NULL != $port){
                    self::$_db = new PDO('pgsql:host=' . $host . ';port=' . $port .'user=' . $user .';password=' . $passwd . '');
                } else {
                    self::$_db = new PDO('pgsql:host=' . $host . ';user=' . $user .';password=' . $passwd . '');
                }
            } elseif ($db_type == 'Oracle'){
                if (NULL != $port){
                    self::$_db = new PDO('oci:dbname=' . $host . '/', $user, $passwd);
                } else {
                    self::$_db = new PDO('oci:dbname=' . $host . ':' . $port . '/', $username, $passwd, $options);
                }
            } else {
                self::$_db = new PDO($driver, $user, $passwd);
            }
            
            if (!self::$_db){
                $result = array(
                    'code' => -1,
                    'message' => '不支持的数据库驱动类型'
                );
                return $result;
            }
            
            } catch (PDOException $e){
                $result = array(
                    'code' => -2,
                    'message' => $e->getMessage()
                );
                return $result;
            }
        }
        
        if ($PDO){
            return self::$_db;        
        } else {
            return 1;
        }
    }
    
    
    /**    
     *  @Purpose:    
     *  进行数据库连接语句   
     *  @Method Name:
     *  connect($PDO = 0, $db_type = NULL, $user = NULL, $passwd = NULL, $host = 'localhost', $port = NULL)
     *  @Parameter: 
     *  $PDO = 0              是否返回PDO对象
     *  $db_type = NULL       SQL数据库类型
     *  $user = NULL          SQL用户名
     *  $passwd = NULL        SQL密码
     *  $host = 'localhost'   SQL地址
     *  $port = NULL          SQL端口
     * 
     * :NOTICE:在session和传值间切换
     * 
     *  @Return: 
     *  0 | 连接失败
    */   
    static public function connect($PDO = 0, $db_type = NULL, $user = NULL, $passwd = NULL, $host = 'localhost', $port = NULL){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('session');
        }
        if (NULL != self::$_ci->session->userdata('db_username') && (NULL == $db_type && NULL == $user && NULL == $passwd)){
            $result = self::connectInit($PDO, self::$_ci->session->userdata('db_type'),
                                    self::$_ci->session->userdata('db_username'),
                                    self::$_ci->session->userdata('db_password'),
                                    self::$_ci->session->userdata('db_host'),
                                    self::$_ci->session->userdata('db_port'));
        } elseif (NULL != $db_type && NULL != $user && NULL != $passwd){
            $result = self::connectInit($PDO, $db_type, $user, $passwd, $host, $port);
        } else {
            return 0;
        }
        
        return $result;
    }


    /**    
     *  @Purpose:    
     *  执行SELECT语句   
     *  @Method Name:
     *  query($sql, $record = 1) 
     *  @Parameter: 
     *  $sql        sql语句
     *  $record     是否返回记录行数和时间
     *  $memcache   是否缓存
     *  @Return: 
     *  $data['sql'] sql语句
     *  $data['rows'] 影响行数
     *  $data['time'] 消耗时间
     *  $data['data'] 取出数据
    */   
    public function query($sql, $record = 1, $memcache = 0){
        if (!self::$_db){
            self::$_db = self::connect();
        }
        
        $data = array();
        
        if ($memcache){
            if ($memcache_obj = @memcache_connect('127.0.0.1', 11211)){
                $key = md5($sql);
                $time_point_a = microtime(TRUE);
                if ($data = $memcache_obj->get($key)){
                    $time_point_b = microtime(TRUE);
                    $data['time'] = number_format($time_point_b - $time_point_a, '8') . '[memcache]';
                    return $data;
                }
            }
        }
        
        try {
            
            if ($record){
                $data['sql'] = $sql;
                $time_point_a = microtime(TRUE);
            }
            
            $result = self::$_db->query($sql);
            if (FALSE === $result){
                $error = array();
                $error = self::$_db->errorInfo();
                return $error[2];
            } else {
                $result->setFetchMode(PDO::FETCH_ASSOC);
            }
            
            
            while ($row = $result->fetch()){
                $data['data'][] = $row; 
            }
            
            if ($record){
                //记录返回记录数
                $data['rows'] = $result->rowCount();
                $time_point_b = microtime(TRUE);
                $data['time'] = number_format($time_point_b - $time_point_a, '8');
            }
            
            if (isset($memcache_obj) && is_object($memcache_obj)){
                $memcache_obj->add($key, $data, MEMCACHE_COMPRESSED, 3600);
            }
            
            return $data;        
        } catch (PDOException $ex) {
            return $ex->getMessage();
        }            
    }
    
    /**    
     *  @Purpose:    
     *  执行INSERT/UPDATE/DELETE语句   
     *  @Method Name:
     *  exec($sql, $last_insert_id = 0) 
     *  @Parameter: 
     *  $sql         sql语句
     *  $record           是否返回影响记录行数和时间
     *  $last_insert_id   动态生成的主键值
     *  @Return: 
     *  $data['sql'] sql语句
     *  $data['rows'] 影响行数
     *  $data['time'] 消耗时间
     *  $data['data'] 取出数据
    */ 
    public function exec($sql, $record = 0, $last_insert_id = 0){
        if (!self::$_db){
            self::$_db = self::connect();
        }
        
        $data = array();
        
        try {            
            if ($record){
                $data['sql'] = $sql;
                $time_point_a = microtime(TRUE);
            }
            
            
            $row = self::$_db->exec($sql);
            
            if (FALSE === $row){
                $error = array();
                $error = self::$_db->errorInfo();
                return $error[2];
            } 
            
            if ($last_insert_id){
                $data['last_id'] = self::$_db->lastInsertId();
            }     
            
            if ($record){
                //记录返回记录数
                $data['rows'] = $row;
                $time_point_b = microtime(TRUE);
                $data['time'] = number_format($time_point_b - $time_point_a, '8');
            }            
            return $data;        
        } catch (Exception $ex) {
            return $ex->getMessage();
        }     
    }
    
    
    /**    
     *  @Purpose:    
     *  分页取数据语句   
     *  @Method Name:
     *  page($db_name, $table_name, $offset, $limit, $record = 1) 
     *  @Parameter: 
     *  $db_name          数据库名
     *  $talbe_name       表名
     *  $offset           偏移量
     *  $limit            取数据数量
     *  $record           记录模式（默认打开）
     *  @Return: 
     *  $data['sql'] sql语句
     *  $data['rows'] 影响行数
     *  $data['time'] 消耗时间
     *  $data['data'] 取出数据
    */ 
    
    public function page($db_name, $table_name, $offset = 0, $limit = 30, $record = 1){
        if (!self::$_db){
            self::$_db = self::connect();
        }
        
        $data = array(); 
        $data['data'] = array();
        if ($record){
            $data['sql'] = "SELECT * FROM $db_name.$table_name";
            $time_point_a = microtime(TRUE);
        }
            
        $stmt = self::$_db->prepare("SELECT * FROM $db_name.$table_name", array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        if ($stmt->execute()){
            for ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_REL, $offset); $row !== false && $limit-- > 0; $row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $data['data'][] = $row;
            }
            if ($record){
                //记录返回记录数
                $data['rows'] = count($data['data']);
                $time_point_b = microtime(TRUE);
                $data['time'] = number_format($time_point_b - $time_point_a, '8');
            }            
            return $data;
        } else {
            return 0;
        }
    }
}