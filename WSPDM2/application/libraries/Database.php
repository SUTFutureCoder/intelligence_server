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
                    $driver = 'mysql:host=' . $host;
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
        if (NULL != self::$_ci->session->userdata('db_username')){
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
     *  $sql         sql语句
     *  $record           是否返回记录行数和时间
     *  @Return: 
     *  $data['sql'] sql语句
     *  $data['rows'] 影响行数
     *  $data['time'] 消耗时间
     *  $data['data'] 取出数据
    */   
    public function query($sql, $record = 1){
        if (!self::$_db){
            self::$_db = self::connect();
        }
        
        $data = array();
        
        try {
            
            if ($record){
                $data['sql'] = $sql;
                $time_potin_a = microtime(TRUE);
            }
            
            $result = self::$_db->query($sql);
            $result->setFetchMode(PDO::FETCH_ASSOC);
            
            while ($row = $result->fetch()){
                $data['data'][] = $row; 
            }
            
            if ($record){
                //记录返回记录数
                $data['rows'] = $result->rowCount();
                $time_potin_b = microtime(TRUE);
                $data['time'] = number_format($time_potin_b - $time_potin_a, '8');
            }
            
            return $data;        
        } catch (Exception $ex) {
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
     *  $record           是否返回记录行数和时间
     *  @Return: 
     *  $data['sql'] sql语句
     *  $data['rows'] 影响行数
     *  $data['time'] 消耗时间
     *  $data['data'] 取出数据
    */ 
}