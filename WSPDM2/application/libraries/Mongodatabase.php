<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 
 * 用于连接mongodb【】
 * 
 *
 * @copyright  版权所有(C) 2014-2015 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    
 * @link       https://github.com/SUTFutureCoder/
*/
class Mongodatabase{
    static private $_ci;
    static private $_db;
    
    public function __construct(){
    }
    
    
    static public function connectInit($Mongo, $user, $passwd, $host = 'localhost', $port = NULL){
        if (!self::$_ci){
            self::$_ci =& get_instance();
        }
        $result = array();
        if (!self::$_db){
            //检查是否支持Mongodb
            if (!class_exists('Mongo', FALSE) && !class_exists('MongoClient', FALSE)){
                $result = array(
                    'code' => -1,
                    'message' => '您尚未安装php_mongo module,请到PHP.net查看相关安装手册'
                );
                return $result;
            }
            try{
                $server = 'mongodb://';
                
                if ($user && $passwd){
                    $server .= $user . ':' . $passwd . '@' . $host;
                } else {
                    $server .= $host;
                }
                
                if ($port){
                        $server .= ':' . $port;
                }
//                $result = array(
//                    'code' => -1,
//                    'message' => $server
//                );
//                return $result;
//                
                if (class_exists('MongoClient', FALSE)){
                    self::$_db = new MongoClient($server);
                } else {
                    self::$_db = new Mongo($server);
                }
                
                if (!self::$_db){
                    $result = array(
                        'code' => -1,
                        'message' => '失败的数据库连接'
                    );
                    return $result;
                }
            } catch (MongoException $e){
                $result = array(
                    'code' => -2,
                    'message' => $e->getMessage()
                );
                return $result;
            }
        }
        
        if ($Mongo){
            return self::$_db;        
        } else {
            return 1;
        }
    }
    
    
    
    /**    
     *  @Purpose:    
     *  进行数据库连接语句   
     *  @Method Name:
     *  connect($Mongo = 0, $user = NULL, $passwd = NULL, $host = 'localhost', $port = NULL)
     *  @Parameter: 
     *  $Mongo = 0              是否返回Mongo对象
     *  $user = NULL            用户名
     *  $passwd = NULL          密码
     *  $host = 'localhost'     地址
     *  $port = NULL            端口
     * 
     * :NOTICE:在session和传值间切换
     * 
     *  @Return: 
     *  0 | 连接失败
    */   
    static public function connect($Mongo = 0, $user = NULL, $passwd = NULL, $host = 'localhost', $port = NULL){
        if (!self::$_ci){
            self::$_ci =& get_instance();
            self::$_ci->load->library('session');
        }
        if (NULL != self::$_ci->session->userdata('db_username')){
            $result = self::connectInit($Mongo, 
                                    self::$_ci->session->userdata('db_username'),
                                    self::$_ci->session->userdata('db_password'),
                                    self::$_ci->session->userdata('db_host'),
                                    self::$_ci->session->userdata('db_port'));
        } elseif (NULL != $user && NULL != $passwd){
            $result = self::connectInit($Mongo, $user, $passwd, $host, $port);
        } else {
            return 0;
        }
        
        return $result;
    }
    
    /**    
     *  @Purpose:    
     *  获取数据库列表   
     *  @Method Name:
     *  getDbList()
     *  @Parameter: 
     * 
     *  @Return: 
     *  array $db_list
    */ 
    public function getDbList(){
        if (!self::$_db){
            self::$_db = self::connect(1);
        }
        
        $dbs = self::$_db->listDBs();
        $db_list = array();
        foreach ($dbs['databases'] as $value){
            $db_list[] = $value['name'];
        }
        return $db_list;
    }
    
    
    /**    
     *  @Purpose:    
     *  获取数据库、表列表   
     *  @Method Name:
     *  getDbCollectionList()
     *  @Parameter: 
     * 
     * :NOTICE:在session和传值间切换
     * 
     *  @Return: 
     *  0 | 连接失败
    */   
    public function getDbCollectionList(){
        if (!self::$_db){
            self::$_db = self::connect(1);
        }
        
        //获取数据库列表
        $db_list = array();
        $db_list = $this->getDbList();
        
        $data = array();
        foreach ($db_list as $value){
            $collection = self::$_db->$value->getCollectionNames();
            foreach ($collection as $collection_names){
                $data[$value][] = $collection_names;
            }
        }
        return $data;
    }
    
    /**    
     *  @Purpose:    
     *  获取数据库信息   
     *  @Method Name:
     *  getDbInfo()
     *  @Parameter: 
     * 
     * :NOTICE:在session和传值间切换
     * 
     *  @Return: 
     *  0 | 连接失败
    */   
    public function getDbInfo(){
        if (!self::$_db){
            self::$_db = self::connect(1);
        }
        
        $temp_db = 'admin';
        $data = self::$_db->$temp_db->command(array('serverStatus' => 1));
        
        $data = array_merge($data, self::$_db->$temp_db->execute('db.serverCmdLineOpts()'));
        return $data;
    }
    
    /**    
     *  @Purpose:    
     *  获取数据库集合数据   
     *  @Method Name:
     *  getCollectionData()
     *  @Parameter: 
     * 
     *  @Return: 
    */   
    public function getCollectionData($db_name, $collection_name, $offset = 0, $limit = 30, $record = 1){
        if (!self::$_db){
            self::$_db = self::connect(1);
        }
        
        $data = array();
        $data['data'] = array();
        if ($record){
            $data['command'] = 'use ' . $db_name . '<br/>' . 'db.' . $collection_name . '.find().sort({_id:-1}).limit(' . $limit . ').skip(' . $offset . ')';
            $time_point_a = microtime(TRUE);
        }
        
        $data['cols'] = array();
        
        if ($cursor = self::$_db->$db_name->$collection_name->find()->sort(array('_id' => -1))->limit($limit)->skip($offset)){
            foreach ($cursor as $value){
                foreach (array_keys($value) as $cols){
                    if (!in_array($cols, $data['cols'])){                        
                        $data['cols'][] = $cols;
                    }
                }
                $data['data'][] = $value;
            }
            
            if ($record){
                $data['rows'] = count($data['data']);
                $time_point_b = microtime(TRUE);
                $data['time'] = number_format($time_point_b - $time_point_a, '8');
            }
            return $data;
        } else {
            return 0;
        }
    }    
    
    /**    
     *  @Purpose:    
     *  获取数据库集合数据总数   
     *  @Method Name:
     *  getCollectionDataSum($db_name, $collection_name)
     *  @Parameter: 
     *  string $db_name 数据库名称
     *  string $collection_name 集合名称
     *  @Return: 
     *  int $sum    总数
    */   
    public function getCollectionDataSum($db_name, $collection_name){
        if (!self::$_db){
            self::$_db = self::connect(1);
        }
        
        return self::$_db->$db_name->$collection_name->count();
    }    
}