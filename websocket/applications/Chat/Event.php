<?php
/**
 * 
 * 聊天主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * @author walkor <worker-man@qq.com>
 * 
 */
use \Lib\Context;
use \Lib\Gateway;
use \Lib\StatisticClient;
use \Lib\Store;
use \Protocols\GatewayProtocol;
use \Protocols\WebSocket;
use \User\VirtualShell;


class Event
{
    //用户分组
    //$group[$group_name][$uid] = $name;
//    public $group = array();
    
    /**
     * 网关有消息时，判断消息是否完整
     */
    public static function onGatewayMessage($buffer)
    {
        return WebSocket::check($buffer);
    }
    
   /**
    * 此链接的用户没调用Gateway::notifyConnectionSuccess($uid);前（即没有得到验证），都触发onConnect
    * 已经调用Gateway::notifyConnectionSuccess($uid);的用户有消息时，则触发onMessage
    * @param string $message 是
    */
   public static function onConnect($message)
   {
       // debug
       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']} socketid:{$_SERVER['GATEWAY_SOCKET_ID']} uid:0 onConnect:".$message."\n";
       // WebSocket 握手阶段
       if(0 === strpos($message, 'GET'))
       {
           // 解析Sec-WebSocket-Key
           $Sec_WebSocket_Key = '';
           if(preg_match("/Sec-WebSocket-Key: *(.*?)\r\n/", $message, $match))
           {
               $Sec_WebSocket_Key = $match[1];
           }
           $new_key = base64_encode(sha1($Sec_WebSocket_Key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11",true));
           // 握手返回的数据
           $new_message = "HTTP/1.1 101 Switching Protocols\r\n";
           $new_message .= "Upgrade: websocket\r\n";
           $new_message .= "Sec-WebSocket-Version: 13\r\n";
           $new_message .= "Connection: Upgrade\r\n";
           $new_message .= "Sec-WebSocket-Accept: " . $new_key . "\r\n\r\n";
           
           /*
            * 获取uid，uid必须为1-42亿内的数字
            * 这里作为Demo把时间戳当成uid，高并发下这里会有小概率uid冲突，开发者可以使用自己的uid获取方法
            * 一般流程应该是通过用户名 密码从数据库中获取uid
            * 用户名密码可以放到url中作为参数传递过来，然后自行解析
            * 例如前端js这样调用 ws = new WebSocket("ws://workerman.net:7272/?name=xxx&password=xxx");
            */ 
           $uid = substr(strval(microtime(true)), 3, 10)*100;
           
           // 记录uid到gateway通信地址的映射
           Gateway::storeUid($uid);
           
           // 发送数据包到address对应的gateway，确认connection成功
           Gateway::notifyConnectionSuccess($uid);
           
           // 发送数据包到客户端 完成握手
           return Gateway::sendToCurrentUid($new_message);
       }
       // 如果是flash发来的policy请求
       elseif(trim($message) === '<policy-file-request/>')
       {
           $policy_xml = '<?xml version="1.0"?><cross-domain-policy><site-control permitted-cross-domain-policies="all"/><allow-access-from domain="*" to-ports="*"/></cross-domain-policy>'."\0";
           return Gateway::sendToCurrentUid($policy_xml);
       }
       
       return null;
   }
   
   /**
    * 当用户断开连接时
    * @param integer $uid 用户id 
    */
   public static function onClose($uid)
   {
       // [这步是必须的]删除这个用户的gateway通信地址
       Gateway::deleteUidAddress($uid);
       
       // 从用户列表中删除
       self::delUserFromList($uid);

       // debug
       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']} socketid:{$_SERVER['GATEWAY_SOCKET_ID']} uid:$uid onClose:''\n";
       
       // 广播 xxx 退出了
       //Gateway::sendToAll(WebSocket::encode(json_encode(array('type'=>'logout', 'uid'=> $uid, 'time'=>date('Y-m-d H:i:s')))));
       
   }
   
   /**
    * 有消息时
    * @param int $uid
    * @param string $message
    */
   public static function onMessage($uid, $message)
   {
        if(WebSocket::isClosePacket($message))
        {
            Gateway::kickUid($uid, '');
            self::onClose($uid);
            return;
        }
        $message = WebSocket::decode($message);
        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']} socketid:{$_SERVER['GATEWAY_SOCKET_ID']} uid:$uid onMessage:".$message."\n";
        $message_data = json_decode($message, true);
        if(!$message_data)
        {
            return ;
        }
        
        switch($message_data['type'])
        {
            // 用户登录 message格式: {type:login, name:xx} ，添加到用户，广播给所有用户xx进入聊天室
            case 'login': 
                
                if ('VirtualShell' == $message_data['group']){
                    //此处不信任任何转发，直接处理
                    $clean = array();
                    $clean['user_name'] = htmlentities(trim($message_data['name']), ENT_QUOTES);
                    $clean['password'] = htmlentities(trim($message_data['password']), ENT_QUOTES);
                    $clean['group'] = htmlentities(trim($message_data['group']), ENT_QUOTES);

                    $result = array();
                    $result = VirtualShell::CheckShellPassWord($clean['user_name'], $clean['password'], $uid);
                    if (1 == $result['id']){
                        $new_message = array(
                            '0' => 'login',
                            '1' => $result['id']
                        );
                        self::addUserToList($uid, htmlspecialchars($message_data['name']), $message_data['group']);
                    } else {
                        $new_message = array(
                            '0' => 'login',
                            '1' => $result['id'],
                            '2' => $result['message']
                        );
                    }
                    return Gateway::sendToUid($uid, WebSocket::encode(json_encode($new_message)));
                } else {
                }
                self::addUserToList($uid, htmlspecialchars($message_data['name']), $message_data['group']);
                break;
            // 用户发言 message: {type:say, to_uid:xx, content:xx}
            case 'say':
                // 私聊
//                if($message_data['to_uid'] != 'all')
//                {
//                    $new_message = array(
//                        'type'=>'say',
//                        'from_uid'=>$uid, 
//                        'to_uid'=>$message_data['to_uid'],
//                        'content'=>nl2br(htmlspecialchars($message_data['content'])),
//                        'time'=>date('Y-m-d :i:s')
//                    );
//                    return Gateway::sendToUid($message_data['to_uid'], WebSocket::encode(json_encode($new_message)));
//                }
                // 向大家说
               
                $new_message = array(
                    '0' => 'say', 
                    '1' => htmlspecialchars($message_data['name']),
                    '2' => nl2br(htmlspecialchars($message_data['content'])),
                    '3' => date('Y-m-d H:i:s'),
                );
                $new_message_encode = json_encode($new_message);
                var_dump(self::getGroupUserList());
                
                foreach (self::getGroupUserList() as $key => $value){   
                    
                    if ($key == $message_data['group']){
                        foreach ($value as $uid => $user_id){
                            var_dump($value);
                            Gateway::sendToUid($uid, WebSocket::encode($new_message_encode));
                        }                        
                    }   
                }
                return TRUE;
                
            case 'func':
                $url = $message_data['api'];
                $data = $message_data['data'];
                $message_data['key'] ? $key = $message_data['key'] : $key = null;
                $new_message = self::getApiData($url, $data, $key);                 
                return Gateway::sendToUid($uid, WebSocket::encode($new_message));
                
            case 'iframe':            
                $url = $message_data['api'];
                $data = $message_data['data'];
                $data['src'] = $message_data['src'];
                $message_data['key'] ? $key = $message_data['key'] : $key = null;
                //对应result[0]
                //$new_message[0] = 'iframe';
                //将数据部分放入result[1]
//                $new_message[1] = self::getApiData($url, $data, $key);     
                $new_message = self::getApiData($url, $data, $key);
                
                //选择器索引放入result[2]
//                $new_message[2] = $message_data['src'];
                return Gateway::sendToUid($uid, WebSocket::encode($new_message));
                
            //广播，慎用，必须验证权限
            case 'group':
                $url = $message_data['api'];
                $data = $message_data['data'];
                $data['src'] = $message_data['src'];
                //不同的group使用“|”进行标示
                $groups = array();
                $groups = explode('|', $message_data['group']);
                
                $message_data['key'] ? $key = $message_data['key'] : $key = null;   
                $new_message_encode = self::getApiData($url, $data, $key);
                $new_message = json_decode($new_message_encode);   
                
                //注意，getApiData不要json_encode!在确认无误后              
                foreach (self::getGroupUserList() as $key => $value){
                    foreach ($groups as $group_value)
                    {
                        if ($group_value == $key){
                            foreach ($value as $uid => $user_id){
                                Gateway::sendToUid($uid, WebSocket::encode($new_message_encode));
                            }                        
                        }                    
                    }
                }
                return TRUE;
                
            case 'ping':
                // 存储当前用户到用户列表
                $ping = "p";
//                var_dump(self::getGroupUserList());
                return Gateway::sendToUid($uid, WebSocket::encode(json_encode($ping)));
                
            case 'mobile':
                $url = $message_data['api'];
                $data = $message_data['data'];
                $message_data['key'] ? $key = $message_data['key'] : $key = null;
                $new_message = self::getApiData($url, $data, $key);
                //选择器索引放入result[2]
//                $new_message[2] = $message_data['src'];
                return Gateway::sendToUid($uid, WebSocket::encode($new_message));
                
            case 'shell':
                $command = $message_data['command'];
                //过滤
                $command = escapeshellcmd(trim($command));
                
                $new_message = array();
                
                if (VirtualShell::Logined($uid)){
                    VirtualShell::Exec($uid, $command);   
                                    
                } else {
                    $new_message[0] = 'shell';
                    $new_message[1] = 'Please Login First';
                    return Gateway::sendToUid($uid, WebSocket::encode(json_encode($new_message)));
                }
        }
   }
   
   /**
    * 获得用户列表
    * @todo 保存有限个
    */
   public static function getUserList()
   {
       $key = 'alluserlist';
       return Store::instance('gateway')->get($key);
   }
   
   /**
    * 获得群组用户列表
    * @todo 保存有限个
    */
   public static function getGroupUserList()
   {       
       $key = 'group';
       return Store::instance('group')->get($key);
   }
   
   /**
    * 从用户列表中删除一个用户
    * @param int $uid
    */
   public static function delUserFromList($uid)
   {
       $key = 'alluserlist';
       $user_list = self::getUserList();      
       $user_group_list = self::getGroupUserList();
       if(isset($user_list[$uid]))
       {
           foreach ($user_group_list as $group_name => $group_name_value){
               if (isset($user_group_list[$group_name][$uid])){
                   unset($user_group_list[$group_name][$uid]);
                   break;
               }               
           }
           unset($user_list[$uid]);
           Store::instance('group')->set('group', $user_group_list);
           return Store::instance('gateway')->set($key, $user_list);
       }
       return true;
   }
   
   
   /**
    * 添加到用户列表中
    * @param int $uid
    * @param string $name
    */
   public static function addUserToList($uid, $name, $group_name = NULL)
   {       
       $key = 'alluserlist';       
       $user_list = self::getUserList();   
       $user_group_list = self::getGroupUserList();
       if(!isset($user_list[$uid]))
       {
           
           $user_group_list[$group_name][$uid] = $name;
           $user_list[$uid] = $name; 
           Store::instance('group')->set('group', $user_group_list);
           return Store::instance('gateway')->set($key, $user_list);
       }
       return true;
   }
   
   public static function getApiData($url, $data, $key = null){
        $ch = curl_init();
        $timeout = 0;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REMOTE_ADDR']);   //构造来路    
        curl_setopt($ch, CURLOPT_POST, true);   
        var_dump($data);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);      
        curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($data));  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);      
        $handles = curl_exec($ch);
        //echo $handles;
        curl_close($ch);   
        return $handles;
    }      
    
}
