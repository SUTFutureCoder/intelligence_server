<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 
 * 用于
 * 
 *
 * @copyright  版权所有(C) 2014-2015 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    
 * @link       https://github.com/SUTFutureCoder/
*/
class Nosqldatabase{
    static private $_ci;
    static private $_db;
    
    public function __construct(){
    }
    
    
    static public function connectInit($Mongo, $db_type, $user, $passwd, $host = 'localhost', $port = NULL){
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
}