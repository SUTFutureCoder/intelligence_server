<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 安全
 * 负责用户的密钥解密，权限验证，密码验证
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class Secure{
    
    /**    
     *  @Purpose:    
     *  验证传入的时间是否正确   
     *  @Method Name:
     *  CheckDateTime($date_time)
     *  @Parameter: 
     *  $date_time 需要检测的date('Y-m-d H:i:s')时间 
     *  @Return: 
     *      0|不正确
     *      时间戳|正确
    */ 
    public function CheckDateTime($date_time){
        if (!preg_match("/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/",$date_time)){
            return 0;
        } else {
            return strtotime($date_time);            
        }
    }
    
    /**    
     *  @Purpose:    
     *  根据用户密钥获取用户角色   
     *  @Method Name:
     *  CheckRole($encrypted_key)    
     *  @Parameter: 
     *  $encrypted_key 已加密的用户密钥 
     *  @Return: 
     *  用户角色或0
    */ 
    public function CheckRole($encrypted_key){
        //在自定义类库中初始化CI资源
        $CI =& get_instance();       
        
        $CI->load->library('encrypt');
        $CI->load->database();
        //检查是否存在此用户
        $CI->db->where('user_id', $this->CheckUserKey($encrypted_key));
        $query = $CI->db->get('user');        
        if (!$query->num_rows()){
            return 0;
        }
        //获取用户的角色
        $role = array();
        $CI->db->where('user_id', $this->CheckUserKey($encrypted_key));
        $CI->db->from('re_user_role');
        $CI->db->join('role', 'role.role_id = re_user_role.role_id');
        $query = $CI->db->get();
        $role = array_merge($role, $query->result_array());
        
        return $role['role_name'];
    }
        
    /**    
     *  @Purpose:    
     *  检验传入的用户id生成定时密钥   
     *  @Method Name:
     *  CreateUserKey($db_username, $db_passwd) 
     *  @Parameter: 
     *  $db_username 用户数据库用户名
     *  $db_passwd   用户数据库密码
     *  @Return: 
     *  0|无此用户或生成失败
     *  $user_key|加密用户密钥
    */ 
    public function CreateUserKey($db_username, $db_passwd){
        //在自定义类库中初始化CI资源
        $CI =& get_instance();               
        $CI->load->library('encrypt');
        return $CI->encrypt->encode($user_key = time() . $db_username . '|' . $db_passwd);
    }    
        
    /**    
     *  @Purpose:    
     *  检验传入的用户密钥是否合法或过期   
     *  @Method Name:
     *  CheckUserKey($encrypted_key) 
     *  @Parameter: 
     *  $encrypted_key 用户密钥
     *  @Return: 
     *  0|已过期或是非法的用户密钥
     *  $db['user_name']|数据库账户
     *  $db['password']|数据库密码
    */ 
    public function CheckUserKey($encrypted_key){
        //在自定义类库中初始化CI资源
        $CI =& get_instance();       
        $CI->load->library('encrypt');
        $CI->load->library('basic');
        //替換空格爲加號
        $encrypted_key  = str_replace(' ', '+', $encrypted_key);
        $encrypted_time = substr($CI->encrypt->decode($encrypted_key), 0, strlen(time()));
        //过期的密钥
        if (time() - $encrypted_time >= $CI->basic->user_key_life){
            return 0;
        }
        
        $db['user_name'] = substr($CI->encrypt->decode($encrypted_key), strlen(time()), strpos($CI->encrypt->decode($encrypted_key), '|') - strlen(time()));
        $db['password'] = substr($CI->encrypt->decode($encrypted_key), strpos($CI->encrypt->decode($encrypted_key), '|') + 1);
        
        return $db;
    }    

    /**    
     *  @Purpose:    
     *  更改密码   
     *  @Method Name:
     *  UpdateUserPass($user_mixed, $user_password) 
     *  @Parameter: 
     *  $conn 数据库连接
     *  $db_username 数据库用户名
     *  $db_password 数据库密码
     *  @Return: 
     *  0|
     *  1|
     *  2|查无此人
     *  
     * 
     *  
    */ 
    public function UpdateUserPass($conn, $db_username, $db_password){
        //在自定义类库中初始化CI资源
        $CI =& get_instance();       
        $sql = "UPDATE mysql.user SET password=PASSWORD('$db_password') WHERE User='$db_username'";        
        if ($result = mysqli_query($conn, $sql)){
            $sql = 'FLUSH PRIVILEGES';
            $result = mysqli_query($conn, $result);
            return 1;            
        } else {
            return 0;
        }
    }
    
}