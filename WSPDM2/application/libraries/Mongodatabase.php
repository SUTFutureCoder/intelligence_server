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
                    //不允许空密码
                    $result = array(
                    'code' => -1,
                    'message' => '不允许空密码'
                    );
                    return $result;
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
        } elseif (NULL != $user){
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
    
    /**    
     *  @Purpose:    
     *  删除集合   
     *  @Method Name:
     *  deleCollection($database, $collection, $db_username, $db_password, $db_host, $db_port)
     *  @Parameter: 
     *  string $database 数据库名称
     *  string $collection_name 集合名称
     *  string $db_username 数据库用户名
     *  string $db_password 数据库密码
     *  string $db_host 数据库地址
     *  string $db_port 数据库端口
     *  @Return: 
    */   
    public function deleCollection($database, $collection_name, $db_username, $db_password, $db_host, $db_port){
        if (!self::$_db){
            self::$_db = self::connect(1, $db_username, $db_password, $db_host, $db_port);
        }
        
        $time_point_a = microtime(TRUE);
        try{
            $result = self::$_db->$database->$collection_name->drop();
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        $time_point_b = microtime(TRUE);
        $result['time'] = number_format($time_point_b - $time_point_a, '8');
        $result['sql'] = 'use ' . $database . '<br/>' . 'db.' . $collection_name . '.drop()';                
        
        return $result;
    }    
    
    /**    
     *  @Purpose:    
     *  清除集合   
     *  @Method Name:
     *  truncateTable($database, $collection, $db_username, $db_password, $db_host, $db_port)
     *  @Parameter: 
     *  string $database 数据库名称
     *  string $collection_name 集合名称
     *  string $db_username 数据库用户名
     *  string $db_password 数据库密码
     *  string $db_host 数据库地址
     *  string $db_port 数据库端口
     *  @Return: 
    */   
    public function truncateTable($database, $collection_name, $db_username, $db_password, $db_host, $db_port){
        if (!self::$_db){
            self::$_db = self::connect(1, $db_username, $db_password, $db_host, $db_port);
        }
        
        $time_point_a = microtime(TRUE);
        try{
            $result = self::$_db->$database->$collection_name->remove();
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        $time_point_b = microtime(TRUE);
        $result['rows'] = $result['n'];
        $result['time'] = number_format($time_point_b - $time_point_a, '8');
        $result['sql'] = 'use ' . $database . '<br/>' . 'db.' . $collection_name . '.remove({})';                
        
        return $result;
    }    
    
    /**    
     *  @Purpose:    
     *  修改数据   
     *  @Method Name:
     *  updateData($database, $collection, $db_username, $db_password, $db_host, $db_port, $key, $new_data)
     *  @Parameter: 
     *  string $database 数据库名称
     *  string $collection_name 集合名称
     *  string $db_username 数据库用户名
     *  string $db_password 数据库密码
     *  string $db_host 数据库地址
     *  string $db_port 数据库端口
     *  string $id     目标数据_id
     *  array  $new_data新数据
     *  @Return: 
    */   
    public function updateData($database, $collection_name, $db_username, $db_password, $db_host, $db_port, $id, $new_data){
        if (!self::$_db){
            self::$_db = self::connect(1, $db_username, $db_password, $db_host, $db_port);
        }
        $new_data = base64_decode($new_data);
        $new_data_array = json_decode($new_data, TRUE);
        unset($new_data_array['_id']);
//        $new_data = str_replace('\"', '"', $new_data);
        $mongoId = new MongoId($id);
//        echo 'db.' . $collection_name . '.update({"_id":"' . $id . '"}, {$set:' . $new_data . '})';
        $time_point_a = microtime(TRUE);
        try{
            $result = self::$_db->$database->$collection_name->update(array('_id' => $mongoId), array('$set' => $new_data_array));
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        $time_point_b = microtime(TRUE);

        $result['key'] = $id;
        
        $result['rows'] = $result['n'];
        $result['time'] = number_format($time_point_b - $time_point_a, '8');
        $result['sql'] = 'use ' . $database . '<br/>' . 'db.' . $collection_name . '.update({"_id":"' . $id . '"}, {$set:' . $new_data . '})';                
        
        return $result;
    }    
    
    /**    
     *  @Purpose:    
     *  删除数据   
     *  @Method Name:
     *  deleData($database, $collection, $db_username, $db_password, $db_host, $db_port, $key, $new_data)
     *  @Parameter: 
     *  string $database 数据库名称
     *  string $collection_name 集合名称
     *  string $db_username 数据库用户名
     *  string $db_password 数据库密码
     *  string $db_host 数据库地址
     *  string $db_port 数据库端口
     *  string $id     目标数据_id
     *  @Return: 
    */   
    public function deleData($database, $collection_name, $db_username, $db_password, $db_host, $db_port, $id){
        if (!self::$_db){
            self::$_db = self::connect(1, $db_username, $db_password, $db_host, $db_port);
        }
        
        $mongoId = new MongoId($id);
//        echo 'db.' . $collection_name . '.update({"_id":"' . $id . '"}, {$set:' . $new_data . '})';
        
        $time_point_a = microtime(TRUE);
        try{
            $result = self::$_db->$database->$collection_name->remove(array('_id' => $mongoId));
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        $time_point_b = microtime(TRUE);

        $result['key'] = $id;
        $result['rows'] = $result['n'];
        $result['time'] = number_format($time_point_b - $time_point_a, '8');
        $result['sql'] = 'use ' . $database . '<br/>' . 'db.' . $collection_name . '.remove({"_id":"' . $id . '"})';                
        
        return $result;
    }    
    
    //执行命令
    public function execSQL($nosql_type, $nosql, $limit, $skip, $upsert, $multi, $db_username, $db_password, $db_host, $db_port, $database, $collection, $memcache = 0){
        if (!self::$_db){
            self::$_db = self::connect(1,$db_username, $db_password, $db_host, $db_port);
        }
        $result = array();
        if ($nosql_type != 'update'){
            //修改串不同于普通json
            $nosql_array = array();
            $nosql_array = json_decode($nosql, TRUE);
            if (!$nosql_array){
                return 'JSON串格式不正确';
            }
        }
        
        try{
            switch ($nosql_type){
                case 'find':
                    $time_point_a = microtime(TRUE);
                     if ($memcache){
                        if ($memcache_obj = @memcache_connect('127.0.0.1', 11211)){
                            $key = md5($nosql);
                            $time_point_a = microtime(TRUE);
                            if ($data = $memcache_obj->get($key)){
                                $time_point_b = microtime(TRUE);
                                $data['rows'] = count($data['json']);
                                $data['nosql_type'] = 'find';
                                $data['sql'] = $nosql;
                                $data['time'] = number_format($time_point_b - $time_point_a, '8') . '[memcache]';
                                return $data;
                            }
                        }
                    }
                    
                    if ($limit && $skip){
                        $result_cursor = self::$_db->$database->$collection->find($nosql_array)->limit($limit)->skip($skip);
                    } else if ($limit){
                        $result_cursor = self::$_db->$database->$collection->find($nosql_array)->limit($limit);
                    } else if ($skip){
                        $result_cursor = self::$_db->$database->$collection->find($nosql_array)->skip($skip);
                    } else {
                        $result_cursor = self::$_db->$database->$collection->find($nosql_array);
                    }
                    
                    foreach ($result_cursor as $value){
                        $result['json'][] = print_r(json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), TRUE);
                        $temp_id = '$id';
                        $result['id'][] = $value['_id']->$temp_id;
                    }
                    $time_point_b = microtime(TRUE);
                    
                    if (isset($memcache_obj) && is_object($memcache_obj)){
                        $memcache_obj->add($key, $result, MEMCACHE_COMPRESSED, 3600);
                    }
                    
                    $result['rows'] = count($result['id']);
                    $result['nosql_type'] = 'find';
                    $result['sql'] = $nosql;
                    $result['time'] = number_format($time_point_b - $time_point_a, '8');
                    
                    break;
                    
                case 'update':
                    $time_point_a = microtime(TRUE);
                    
                    $nosql = 'db.' . $collection . '.update(' . $nosql . ', ' . $upsert . ', ' . $multi . ');';
                    
                    $result = self::$_db->$database->execute($nosql);
                    
                    $time_point_b = microtime(TRUE);
                    $result['nosql_type'] = 'update';
                    $result['sql'] = $nosql;
                    $result['time'] = number_format($time_point_b - $time_point_a, '8');
                    break;
                
                case 'insert':
                    $time_point_a = microtime(TRUE);
                    
                    $nosql = 'db.' . $collection . '.insert(' . $nosql . ');';
                    $result = self::$_db->$database->execute($nosql);
                    
                    $time_point_b = microtime(TRUE);
                    
                    $result['nosql_type'] = 'insert';
                    $result['sql'] = $nosql;
                    $result['time'] = number_format($time_point_b - $time_point_a, '8');
                    break;
                
                case 'dele':
                    $time_point_a = microtime(TRUE);
                    
                    $nosql = 'db.' . $collection . '.remove(' . $nosql . ');';
                    $result = self::$_db->$database->execute($nosql);
                    
                    $time_point_b = microtime(TRUE);
                    $result['nosql_type'] = 'dele';
                    $result['sql'] = $nosql;
                    $result['time'] = number_format($time_point_b - $time_point_a, '8');
                    break;
            }
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        return $result;        
    }
    
    //增加数据
    public function insertData($database, $collection, $post_data, $db_username, $db_password, $db_host, $db_port){
        if (!self::$_db){
            self::$_db = self::connect(1,$db_username, $db_password, $db_host, $db_port);
        }

        $time_point_a = microtime(TRUE);
        
        $result = self::$_db->$database->$collection->insert($post_data);

        $time_point_b = microtime(TRUE);

        if ($result['ok']){
            $result['rows'] = 1;
        }
        
        $temp_id = '$id';
        $result['id'] = $post_data['_id']->$temp_id;
        $result['data']['array'] = print_r($post_data, TRUE);
        $result['data']['json'] = print_r(json_encode($post_data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), TRUE);
        $result['sql'] = 'db.' . $collection . '.insert(' . json_encode($post_data) . ');';;
        $result['time'] = number_format($time_point_b - $time_point_a, '8');
        
        return $result;
    }
}