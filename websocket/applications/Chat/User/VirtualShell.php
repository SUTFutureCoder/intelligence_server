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
    public static function Exec($uid, $command){
        set_time_limit(0);
        
        if ('!' == $command){
            $shmid = shmop_open($uid, 'c', 0755, 32);
            shmop_write($shmid, '1', 0); 
            return 0;
        } 
        
        $handle = popen($command, 'r');
        $i = 0;
        while (!feof($handle)){
            $shmid = shmop_open($uid, 'c', 0755, 32);
            echo "-------" . shmop_read($shmid, 0, shmop_size($shmid)) . "\n\n";
            if (1 == shmop_read($shmid, 0, shmop_size($shmid) && $i)){
                pclose($handle);
                unset($handle);                
                shmop_delete($shmid);
                shmop_close($shmid);
                Gateway::sendToUid($uid, WebSocket::encode(json_encode(array(
                    'exec', 'VS_interrupted'
                ))));
                Gateway::sendToUid($uid, WebSocket::encode(json_encode(array(
                    'exec_end', 1
                )))); 
                break;
            } else {                
                $buffer = fgets($handle);            
                Gateway::sendToUid($uid, WebSocket::encode(json_encode(array(
                    'exec', $buffer
                ))));                           
            }
            shmop_close($shmid);
            $i++;
        }
        Gateway::sendToUid($uid, WebSocket::encode(json_encode(array(
            'exec_end', 1
        )))); 
        pclose($handle); 
    }
}
    