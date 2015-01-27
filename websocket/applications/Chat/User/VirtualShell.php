<?php
namespace User;

use \Lib\Context;
use \Lib\Gateway;
use \Lib\StatisticClient;
use \Lib\Store;
use \Protocols\GatewayProtocol;
use \Protocols\WebSocket;

class VirtualShell{
    //VirtualShell密码验证(两种方式)
    public static function CheckShellPassWord($user_name, $password, $uid){
        $result = array();
        $ban_time = 10;
        if (!$uid || !ctype_digit($uid)){
            $result['id'] = -3;
            $result['message'] = 'Could Not Defined A User';
            return $result;
        }
        
        $check_uid_ban_time = Store::instance('VirtualShell')->get($uid);
        if ($check_uid_ban_time){
            if (time() <= $check_uid_ban_time['ban_time']){
                $result['id'] = -4;
                $result['message'] = 'The ID Was Banned For ' . $ban_time . 's';
                return $result;
            }
        }
        
        try {
            $basic_key = file_get_contents("/home/key.php");
            $basic_key = str_replace("\n", "", $basic_key);
        } catch (Exception $ex) {
            $result['id'] = -1;
            $result['message'] = 'Could Not Find Key File.';
            return $result;
        }
        switch (date("d") % 2){
            //双号
            case 0:
                $key = $basic_key;      
                $key .= ceil(date("d") / 2);
                $key .= date("H") + 20;
                $key .= date("Y");
                break;
            
            //单号
            case 1:
                $key = floor(date("d") / 2);
                $key .= date("Y");
                $key .= date("H") + 40;
                $key .= $basic_key;
                break;
        }
        
        echo $password . "\n";
        echo $key . "\n";
        
        if ($key != $password){
            $result['id'] = -2;
            $result['message'] = 'PassWord ERROR';
            
            if (!isset($memcache['continued_pass_error'])){
                $memcache['continued_pass_error'] = 1;
            }else {
                $memcache['continued_pass_error']++;
            }
            
            $ban_time = rand(pow(2, $memcache['continued_pass_error']), pow(3, $memcache['continued_pass_error']));
            $memcache['ban_time'] = time() + $ban_time;            
        } else {
            $result['id'] = 1;
            $memcache['ban_time'] = 0;
            $memcache['continued_pass_error'] = 0;
        }
        Store::instance('VirtualShell')->set($uid, $memcache);
        
        return $result;
    }
    
    //检查是否登录
    public static function Logined($uid){
        $check_uid_logined = Store::instance('VirtualShell')->get($uid);        
        if (0 == $check_uid_logined['ban_time']){
            return 1;
        } else {
            return 0;
        }
    }


    //执行函数
    public static function Socket($uid, $command){
        set_time_limit(0);
        ob_implicit_flush();
        $service_port = 10086;
        $address = '127.0.0.1';
        
        $new_message = array();
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === FALSE){
            $new_message[0] = '-1';
            $new_message[1] = 'socket_create() failed';
            return $new_message;
        }
        
        $conn = socket_connect($socket, $address, $service_port);
        if ($conn === FALSE){
            $new_message[0] = '-2';
            $new_message[1] = 'socket_connect() failed';
            return $new_message;
        }
        
        
        if ($command != '!'){
            socket_write($socket, $command, strlen($command));
        } else {
            //立即停止
            socket_write($socket, '!', 1);
            socket_close($socket);
            $new_message[0] = 'shell';
            $new_message[1] = 2;
            $new_message[2] = 'Command Canceled';
            return $new_message;
        }
        
        //添加响应上限防止无限死循环
        $max_response = 30;
        $now_response = 0;
        while (1){
            $response = socket_read($socket, 8192);
            if ($response == '#' || $now_response == $max_response){
                break;
            }
            $new_message[0] = 'shell';
            $new_message[1] = 1;
            $new_message[2] = $response;
            Gateway::sendToUid($uid, WebSocket::encode(json_encode($new_message)));
            ++$now_response;
        }
        
        socket_close($socket);
    }
    
}
    