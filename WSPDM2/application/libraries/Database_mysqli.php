<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 数据库连接、基本信息类(仅mysqli)
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    1.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class Database_mysqli{
    
    /**    
     *  @Purpose:    
     *  登录数据库
     *  
     *  @Method Name:
     *  dbConnect($user_name = NULL, $password = NULL)    
     *  @Parameter: 
     *  $user_name 数据库用户名
     *  $password  数据库密码
     *  @Return: 
     *  状态码|状态
     *      0|失败连接
     *      $conn|数据库连接
     * 
    */
    public function dbConnect($user_name = NULL, $password = NULL){
        $CI =& get_instance();
        
        if ($user_name != NULL && $password != NULL){
            $conn = mysqli_connect('localhost', $user_name, $password);        
        } else {
            $CI->load->library('session');   
            try{
                $conn = mysqli_connect('localhost', $CI->session->userdata('db_username'), $CI->session->userdata('db_password'));        
            } catch (Exception $ex) {
                return 0;
            }    
        }
        
        if (mysqli_connect_errno($conn)){
            return 0;
        } else {
            //设置编码，防止出现中文问号
            mysqli_set_charset($conn, 'utf8');
            return $conn;
        }
        
    }
    
    /**    
     *  @Purpose:    
     *  获取数据库、及各个表
     *  
     *  @Method Name:
     *  getDbList($conn)    
     *  @Parameter: 
     *  $conn 数据库连接
     *  @Return: 
     *  数据库列表
     * 
    */
    public function getDbList($conn){
        error_reporting(0);
        $sql = 'SHOW DATABASES';
        $temp = array();
        if ($result = mysqli_query($conn, $sql)){
            while ($obj = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $sql_tables = 'SHOW TABLES FROM ' . $obj['Database'];
                $result_tables = mysqli_query($conn, $sql_tables);
                while ($obj_tables = mysqli_fetch_array($result_tables, MYSQL_ASSOC)){
                    $temp = array_values($obj_tables);                    
                    $data[$obj['Database']][] = $temp[0];
                }                
            }
            mysqli_free_result($result);
        }
        error_reporting(1);
//        var_dump($data);
        return $data;

    }
    
    /**    
     *  @Purpose:    
     *  执行雷达，用于返回影响了行数和SQL语句以及数据
     *  
     *  @Method Name:
     *  execSQL($conn, $sql, $record = 1)    
     *  @Parameter: 
     *  $conn 数据库连接
     *  $sql  sql语句
     *  $record 记录模式，默认开启
     *  @Return: 
     *  $data['sql'] sql语句
     *  $data['rows'] 影响行数
     *  $data['time'] 消耗时间
     *  $data['data'] 取出数据
     * 
    */
    public function execSQL($conn, $sql, $record = 1, $result_data = 0){
        error_reporting(0);
        if ($record){
            $data = array();
            $data['sql'] = $sql;
            $time_point_a = microtime(true);
            if ($result = mysqli_query($conn, $sql)){  
                //获取列名列表
                if ($cols = mysqli_fetch_fields($result)){
                    foreach ($cols as $col_item){
                        $data['cols'][$col_item->name]['type'] = $col_item->type;                        
                        $data['cols'][$col_item->name]['length'] = $col_item->length;                        
                        $data['cols'][$col_item->name]['charset'] = $col_item->charsetnr;                        
                    }
                }
                
                if ('SELECT' != substr(trim($sql), 0, 6)){
                    $data['rows'] = mysqli_affected_rows($conn);
                } else {
                    $data['rows'] = mysqli_num_rows($result);
                }
                
                while ($obj = mysqli_fetch_array($result, MYSQL_ASSOC)){                                  
                    $data['data'][] = $obj;
                }
                $time_point_b = microtime(true);
                $data['time'] = number_format($time_point_b - $time_point_a, '8');
            } else {
                if ('SELECT' != substr(trim($sql), 0, 6)){
                    $data['rows'] = mysqli_affected_rows($conn);
                } else {
                    $data['rows'] = mysqli_num_rows($result);
                }
                $time_point_b = microtime(true);
                $data['time'] = number_format($time_point_b - $time_point_a, '8');
            }
        } else {
            if ($result = mysqli_query($conn, $sql)){
                if ($result_data){
                    while ($obj = mysqli_fetch_array($result, MYSQL_ASSOC)){
                        $data[] = $obj;
                    }
                    return $data;
                }                
                return 1;
            } else {
                return 0;
            }
        }
        mysqli_free_result($result);
        error_reporting(1);
        return $data;
    }
    
    /**    
     *  @Purpose:    
     *  获取Mysql基础信息
     *  
     *  @Method Name:
     *  getDbInfo($conn)    
     *  @Parameter: 
     *  $conn 数据库连接
     *  @Return: 
     *  数据库列表
     * 
    */
    public function getDbInfo($conn){
        error_reporting(0);
        $data = array();
        //字符集对象
        $data['char_set'] = mysqli_get_charset($conn);
        //MySQL 客户端库版本
        $data['client_info'] = mysqli_get_client_info($conn);
        //将 MySQL 客户端库版本作为整数返回。
        $data['client_version'] = mysqli_get_client_version($conn);
        //MySQL 服务器主机名和连接类型。
        $data['host_info'] = mysqli_get_host_info($conn);
        //MySQL 协议版本。
        $data['proto_info'] = mysqli_get_proto_info($conn);
        //MySQL 服务器版本。      
        $data['server_info'] = mysqli_get_server_info($conn);
        //将 MySQL 服务器版本作为整数返回。
        $data['server_version'] = mysqli_get_server_version($conn);
        error_reporting(1);
        return $data;

    }
    
    
    
}