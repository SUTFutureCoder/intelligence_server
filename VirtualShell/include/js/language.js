//中日英语言包
//默认语言
var DEFAULT_LANGUAGE = 'CHN';

//定义变量样式：ENG_ETC
//eval(DEFAULT_LANGUAGE + "_LOST_CONNECTION")
//eval(DEFAULT_LANGUAGE + "_")

//初始界面
var ENG_INIT_WELCOME = 'Welcome to use the virtual shell2';
var CHN_INIT_WELCOME = '欢迎使用第二代虚拟终端！';
var JPN_INIT_WELCOME = '2 番目の仮想ターミナルへようこそ ！';

var now = new Date(); 
var ENG_INIT_COPY_RIGHT = "(C) SUT-ACM 2014-" + now.getFullYear() + " *Chen";
var CHN_INIT_COPY_RIGHT = "版权所有 (C) SUT-ACM 2014-" + now.getFullYear() + " *Chen";
var JPN_INIT_COPY_RIGHT = "すべての権利予約 (C) SUT-ACM 2014-" + now.getFullYear() + " *Chen";

var ENG_INIT_HELP = 'USE $vs:help to get more information';
var CHN_INIT_HELP = '输入【$vs:help】来获取更多信息';
var JPN_INIT_HELP = '入力してください"$vs:help"詳細情報を取得するには';
 
//服务端关闭连接
var ENG_LOST_CONNECTION = 'Lost connection to server';
var CHN_LOST_CONNECTION = '和服务器的连接断开';
var JPN_LOST_CONNECTION = 'サーバーから切断されることは';

//用户中断执行进程
var ENG_INTERRUPTED = 'User interrupt execution process';
var CHN_INTERRUPTED = '用户中断执行进程';
var JPN_INTERRUPTED = 'ユーザー割り込み実行プロセス';

//说
var ENG_SAY = 'say: ';
var CHN_SAY = '说： ';
var JPN_SAY = '言う： ';

//虚拟终端将会在5秒后关闭
var ENG_SHUTDOWN = 'Virtual Shell will shutdown in 5 seconds...';
var CHN_SHUTDOWN = '虚拟终端将会在5秒后关闭...';
var JPN_SHUTDOWN = '仮想端末は 5 秒で終了.'; 

//再见
var ENG_GOODBYE = 'Good Bye!';
var CHN_GOODBYE = '再见！';
var JPN_GOODBYE = 'さようなら！'; 

//按下【Ctrl+C】键取消关闭进程
var ENG_SHUTDOWN_CANCEL = 'Press Ctrl+C to cancel';
var CHN_SHUTDOWN_CANCEL = '按下【Ctrl+C】键取消关闭进程';
var JPN_SHUTDOWN_CANCEL = '「CTRL + c」-をクリックして、プロセスがシャット ダウンをキャンセルするには'; 

//虚拟终端关闭进程已取消
var ENG_SHUTDOWN_CANCELED = 'Virtual Shell shutdown process canceled';
var CHN_SHUTDOWN_CANCELED = '虚拟终端关闭进程已取消';
var JPN_SHUTDOWN_CANCELED = '仮想端末をプロセスをシャット ダウンは取り消されました'; 

//已关闭进程，请关闭虚拟终端
var ENG_SHUTDOWN_HALT = 'Now HALT, Please Close The Shell';
var CHN_SHUTDOWN_HALT = '已关闭进程，请关闭虚拟终端';
var JPN_SHUTDOWN_HALT = '仮想端末をシャット ダウン プロセスをシャット ダウン'; 

//以下是提示部分
var ENG_NOTICE_TOPIC = 'Welcome to use Virtual Shell2';
var CHN_NOTICE_TOPIC = '欢迎使用虚拟终端2';
var JPN_NOTICE_TOPIC = '仮想ターミナル 2 へようこそ';

var ENG_NOTICE_LAN = '$vs:lan - Change the language';
var CHN_NOTICE_LAN = '输入【$vs:lan】来更改语言';
var JPN_NOTICE_LAN = '入力してください"$vs:lan"言語を変更するには';

var ENG_NOTICE_CLEAR = '$vs:clear - Clean the screen';
var CHN_NOTICE_CLEAR = '输入【$vs:clear】来清除屏幕显示';
var JPN_NOTICE_CLEAR = '入力してください"$vs:clear"ディスプレイの画面をクリアするには';

var ENG_NOTICE_LOGIN = '$vs:login - Login the server';
var CHN_NOTICE_LOGIN = '输入【$vs:login】来登录服务器';
var JPN_NOTICE_LOGIN = '入力してください"$vs:login"はサーバーにログオンする';

var ENG_NOTICE_SAY = '$vs:say - Interactive with other users';
var CHN_NOTICE_SAY = '输入【$vs:say】来和其他用户进行交流';
var JPN_NOTICE_SAY = '入力してください"$vs:say"他のユーザーと通信するには';

var ENG_NOTICE_CLEAN_HISTORY = '$vs:clean_history - Clean the command history';
var CHN_NOTICE_CLEAN_HISTORY = '输入【$vs:clean_history】来清除命令行历史';
var JPN_NOTICE_CLEAN_HISTORY = '入力してください"$vs:clean_history"コマンドライン履歴をクリアするには';

var ENG_NOTICE_GITHUB = '$vs:github - Visit the project source code in Github';
var CHN_NOTICE_GITHUB = '输入【$vs:github】来访问项目开源库';
var JPN_NOTICE_GITHUB = '入力してください"$vs:github"アクセス プロジェクト オープン ソース ライブラリへ';

var ENG_NOTICE_AUTHOR = '$vs:author - Show the authors of the Virtual Shell';
var CHN_NOTICE_AUTHOR = '输入【$vs:author】来显示项目开发成员';
var JPN_NOTICE_AUTHOR = '入力してください"$vs:author"プロジェクト開発のメンバーを表示するには';

var ENG_NOTICE_BYE = '$vs:bye - Shutdown the Virtual Shell';
var CHN_NOTICE_BYE = '输入【$vs:bye】来关闭虚拟终端';
var JPN_NOTICE_BYE = '入力してください"$vs:bye"仮想端末をシャット ダウンするには';

var ENG_NOTICE_ARROW = 'PRESS - ↑/↓ - Display or switch the history of commands';
var CHN_NOTICE_ARROW = '按下【↑或↓】来显示或切换历史记录';
var JPN_NOTICE_ARROW = '歴史を切り替える表示する「↑/↓」キーを押します';

//作者信息
var ENG_AUTHOR_1 = '====================================================';
var CHN_AUTHOR_1 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_1 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_2 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Virtual Shell2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_2 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_2 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_3 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Shenyang University of Technology&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_3 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_3 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_4 = '====================================================';
var CHN_AUTHOR_4 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_4 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_5 = '■&nbsp;&nbsp;&nbsp;&nbsp;Interface Designer & Websocket Kernel Engineer&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_5 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_5 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_6 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;System Interaction API Engineer&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_6 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_6 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_7 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*Chen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_7 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_7 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_8 = '====================================================';
var CHN_AUTHOR_8 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_8 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_9 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Thanks For Using Virtual Shell&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_9 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_9 = '歴史を切り替える表示する「↑/↓」キーを押します';

var ENG_AUTHOR_10 = '====================================================';
var CHN_AUTHOR_10 = '按下【↑或↓】来显示或切换历史记录';
var JPN_AUTHOR_10 = '歴史を切り替える表示する「↑/↓」キーを押します';

//登录
var ENG_COMMAND_LOGIN = 'Login:';
var CHN_COMMAND_LOGIN = '登录用户名：';
var JPN_COMMAND_LOGIN = 'ユーザーのログイン名:';

//密码
var ENG_COMMAND_PSW = 'Password:';
var CHN_COMMAND_PSW = '密码：';
var JPN_COMMAND_PSW = 'パスワード:';

//成功清除历史记录
var ENG_HISTORY_CLEAN = 'Clean succeed';
var CHN_HISTORY_CLEAN = '成功清除历史记录';
var JPN_HISTORY_CLEAN = '成功の履歴を消去';

//历史记录清除出错
var ENG_HISTORY_CLEAN_ERR = 'Clean ERROR';
var CHN_HISTORY_CLEAN_ERR = '历史记录清除出错';
var JPN_HISTORY_CLEAN_ERR = '履歴クリーンアップ エラー';

//未识别的内部命令
var ENG_VS_COMMAND_NOT_REC = 'Internal command is not recognized, please enter "$vs:help" or "$vs:?" to query the command set';
var CHN_VS_COMMAND_NOT_REC = '未识别的内部命令，请输入【$vs:help】或【$vs:?】查询命令集';
var JPN_VS_COMMAND_NOT_REC = '内部コマンドが認識されないを入力してください"$vs:help"または"$vs:?"クエリ コマンド セット';

//未输入用户名
var ENG_USER_NAME_EMPTY = 'Undefined User Name';
var CHN_USER_NAME_EMPTY = '未输入用户名';
var JPN_USER_NAME_EMPTY = 'ユーザー名を入力しません。';

//拒绝登录
var ENG_USER_ACCESS_DENY = 'Access Deined';
var CHN_USER_ACCESS_DENY = '拒绝登录';
var JPN_USER_ACCESS_DENY = 'ログインは拒否されました';

//请先登录
var ENG_USER_LOGIN_FIRST_PLEASE = 'Please Login First';
var CHN_USER_LOGIN_FIRST_PLEASE = '请先登录';
var JPN_USER_LOGIN_FIRST_PLEASE = 'ログインしてください。';

