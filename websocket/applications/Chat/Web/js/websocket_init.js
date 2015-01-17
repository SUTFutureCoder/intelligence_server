if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
WEB_SOCKET_SWF_LOCATION = "swf/WebSocketMain.swf";
WEB_SOCKET_DEBUG = true;
var ws, name, user_list={};
   // 创建websocket
    ws = new WebSocket("ws://"+document.domain+":8080/");
  // 当socket连接打开时，输入用户名
  ws.onopen = function() {
      show_prompt();
      if(!name) {
              return ws.close();
              }
      ws.send(JSON.stringify({"type":"login","name":name}));
  };
  // 当有消息时根据消息类型显示不同信息
  ws.onmessage = function(e) {
      console.log(e.data);
    var data = JSON.parse(e.data);
    switch(data['type']){
          // 展示用户列表
          case 'user_list':
              //{"type":"user_list","user_list":[{"uid":xxx,"name":"xxx"},{"uid":xxx,"name":"xxx"}]}
              flush_user_list(data);
              break;
          // 登录
          case 'login':
              //{"type":"login","uid":xxx,"name":"xxx","time":"xxx"}
              add_user_list(data['uid'], data['name']);
              say(data['uid'], 'all',  data['name']+' 加入了聊天室', data['time']);
              break;
          // 发言
          case 'say':
              //{"type":"say","from_uid":xxx,"to_uid":"all/uid","content":"xxx","time":"xxx"}
              say(data['from_uid'], data['to_uid'], data['content'], data['time']);
              break;
         // 用户退出 
          case 'logout':
              //{"type":"logout","uid":xxx,"time":"xxx"}
                     say(data['uid'], 'all', user_list['_'+data['uid']]+' 退出了', data['time']);
                     del_user_list(data['uid']);
    }
  };
  ws.onclose = function() {
      console.log("服务端关闭了连接");
  };
  ws.onerror = function() {
      console.log("出现错误");
  };