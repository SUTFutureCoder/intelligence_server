//中日英语言包
var LANGUAGE_PACK = new Array();
LANGUAGE_PACK[0] = 'ENG';
LANGUAGE_PACK[1] = 'CHN';
LANGUAGE_PACK[2] = 'JPN';

//选定语言
if (undefined == $.LS.get('vs_language')){
    var DEFAULT_LANGUAGE = 'ENG';
} else {
    var DEFAULT_LANGUAGE = $.LS.get('vs_language');
}


//定义变量样式：ENG_ETC
//eval(DEFAULT_LANGUAGE + "_LOST_CONNECTION")
//eval(DEFAULT_LANGUAGE + "_")

//初始界面
var ENG_INIT_WELCOME = 'Welcome to use the virtual shell2';
var CHN_INIT_WELCOME = '欢迎使用第二代虚拟终端！';
var JPN_INIT_WELCOME = '第二世代の仮想ターミナルへようこそ';

var now = new Date(); 
var ENG_INIT_COPY_RIGHT = "(C) SUT-ACM 2014-" + now.getFullYear() + " *Chen";
var CHN_INIT_COPY_RIGHT = "版权所有 (C) SUT-ACM 2014-" + now.getFullYear() + " *Chen";
var JPN_INIT_COPY_RIGHT = "著作権所有 (C) SUT-ACM 2014-" + now.getFullYear() + " *Chen";

var ENG_INIT_HELP = 'USE $vs:help to get more information';
var CHN_INIT_HELP = '输入【$vs:help】以获取更多信息';
var JPN_INIT_HELP = '「$vs:help」を入力して詳細な情報を得る。';
 
//服务端关闭连接
var ENG_LOST_CONNECTION = 'Lost connection to server';
var CHN_LOST_CONNECTION = '和服务器的连接断开';
var JPN_LOST_CONNECTION = 'サーバーから切れられる';

//用户中断执行进程
var ENG_INTERRUPTED = 'User interrupt execution process';
var CHN_INTERRUPTED = '用户中断执行进程';
var JPN_INTERRUPTED = 'ユーザーの中断執行プロセス';

//说
var ENG_SAY = 'say: ';
var CHN_SAY = '说： ';
var JPN_SAY = '言う： ';

//虚拟终端将会在5秒后关闭
var ENG_SHUTDOWN = 'Virtual Shell will shutdown in 5 seconds...';
var CHN_SHUTDOWN = '虚拟终端将会在5秒后关闭...';
var JPN_SHUTDOWN = '仮想端末は 5 秒後に終了する...'; 

//再见
var ENG_GOODBYE = 'Good Bye!';
var CHN_GOODBYE = '再见！';
var JPN_GOODBYE = 'さようなら！'; 

//按下【Ctrl+C】键取消关闭进程
var ENG_SHUTDOWN_CANCEL = 'Press Ctrl+C to cancel';
var CHN_SHUTDOWN_CANCEL = '按下【Ctrl+C】键取消关闭进程';
var JPN_SHUTDOWN_CANCEL = '「CTRL + c」-をクリックして、プロセスがシャットダウンをキャンセルする'; 

//虚拟终端关闭进程已取消
var ENG_SHUTDOWN_CANCELED = 'Virtual Shell shutdown process canceled';
var CHN_SHUTDOWN_CANCELED = '虚拟终端关闭进程已取消';
var JPN_SHUTDOWN_CANCELED = '仮想端末をプロセスをシャットダウンは取り消されした'; 

//已关闭进程，请关闭虚拟终端
var ENG_SHUTDOWN_HALT = 'Now HALT, Please Close The Shell';
var CHN_SHUTDOWN_HALT = '已关闭进程，请关闭虚拟终端';
var JPN_SHUTDOWN_HALT = 'プロセスがシャットダウンした仮想端末をシャットダウンしてください。'; 

//以下是提示部分
var ENG_NOTICE_TOPIC = 'Welcome to use Virtual Shell2';
var CHN_NOTICE_TOPIC = '欢迎使用第二代虚拟终端';
var JPN_NOTICE_TOPIC = '第二世代の仮想ターミナルへようこそ';

var ENG_NOTICE_LAN = '$vs:lan - Change the language';
var CHN_NOTICE_LAN = '输入【$vs:lan】来更改语言';
var JPN_NOTICE_LAN = '「$vs:lan」を入力して言語を変更する。';

var ENG_NOTICE_CLEAR = '$vs:clear - Clean the screen';
var CHN_NOTICE_CLEAR = '输入【$vs:clear】来清除屏幕显示';
var JPN_NOTICE_CLEAR = '「$vs:clear」を入力してスクリーンの画面を取り除く。';

var ENG_NOTICE_LOGIN = '$vs:login - Login the server';
var CHN_NOTICE_LOGIN = '输入【$vs:login】来登录服务器';
var JPN_NOTICE_LOGIN = '「$vs:login」を入力してサーバーにログオンする';

var ENG_NOTICE_SAY = '$vs:say - Interactive with other users';
var CHN_NOTICE_SAY = '输入【$vs:say】来和其他用户进行交流';
var JPN_NOTICE_SAY = '「$vs:say」を入力して他のユーザーと通信する';

var ENG_NOTICE_CLEAN_HISTORY = '$vs:clean_history - Clean the command history';
var CHN_NOTICE_CLEAN_HISTORY = '输入【$vs:clean_history】来清除命令行历史';
var JPN_NOTICE_CLEAN_HISTORY = '「$vs:clean_history」を入力してコマンド・ラインの歴史を除去する。';

var ENG_NOTICE_GITHUB = '$vs:github - Visit the project source code in Github';
var CHN_NOTICE_GITHUB = '输入【$vs:github】来访问项目开源库';
var JPN_NOTICE_GITHUB = '「$vs:github」を入力して増収庫のプロジェクトを訪問する';

var ENG_NOTICE_AUTHOR = '$vs:author - Show the authors of the Virtual Shell';
var CHN_NOTICE_AUTHOR = '输入【$vs:author】来显示项目开发成员';
var JPN_NOTICE_AUTHOR = '「$vs:author」を入力してプロジェクト開発のメンバーを表示する';

var ENG_NOTICE_BYE = '$vs:bye - Shutdown the Virtual Shell';
var CHN_NOTICE_BYE = '输入【$vs:bye】来关闭虚拟终端';
var JPN_NOTICE_BYE = '「$vs:bye」を入力して仮想端末をシャットダウンする';

var ENG_NOTICE_ARROW = 'PRESS - ↑/↓ - Display or switch the history of commands';
var CHN_NOTICE_ARROW = '按下【↑或↓】来显示或切换历史记录';
var JPN_NOTICE_ARROW = '「↑/↓」キーを押して歴史の切り替えを表示する';

//作者信息
var ENG_AUTHOR_1 = '========================================================';
var CHN_AUTHOR_1 = '====================================================';
var JPN_AUTHOR_1 = '============================================================';

var ENG_AUTHOR_2 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Virtual Shell2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_2 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;第二代虚拟终端&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_2 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;第二世代の仮想端末&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;■';

var ENG_AUTHOR_3 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Shenyang University of Technology&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_3 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;沈阳工业大学&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_3 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;瀋陽工業大学&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;■';

var ENG_AUTHOR_4 = '========================================================';
var CHN_AUTHOR_4 = '====================================================';
var JPN_AUTHOR_4 = '============================================================';

var ENG_AUTHOR_5 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Interface Designer & Websocket Kernel Engineer&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_5 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;界面设计 & Websocket 核心工程师&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_5 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;コアと Websocket インターフェイス デザイン エンジニア&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';

var ENG_AUTHOR_6 = '■&nbsp;&nbsp;System Interaction API Engineer & Global Language Support&nbsp;&nbsp;■';
var CHN_AUTHOR_6 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;系统交互接口工程师 & 国际化语言支持&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_6 = '■&nbsp;&nbsp;システム インターフェイス エンジニア、国際化と言語サポート&nbsp;&nbsp;&nbsp;■';

var ENG_AUTHOR_7 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*Chen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_7 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*Chen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_7 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*Chen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';

var ENG_AUTHOR_8 = '========================================================';
var CHN_AUTHOR_8 = '====================================================';
var JPN_AUTHOR_8 = '============================================================';

var ENG_AUTHOR_9 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Special Thanks&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_9 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;特别感谢&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_9 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;心から感謝の意を&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';


var ENG_AUTHOR_10 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Japanese Professional Translation Support&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_10 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日语专业翻译支持&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_10 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日本語の翻訳サポート&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';

var ENG_AUTHOR_11 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;☆RYUU☆ - Majoring In Japanese&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_11 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;☆RYUU☆ - 日语专业&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_11 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;☆RYUU☆ - 日本語教育専攻&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';

var ENG_AUTHOR_12 = '========================================================';
var CHN_AUTHOR_12 = '====================================================';
var JPN_AUTHOR_12 = '============================================================';

var ENG_AUTHOR_13 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Thanks For Using Virtual Shell&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var CHN_AUTHOR_13 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;感谢使用虚拟终端&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';
var JPN_AUTHOR_13 = '■&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;仮想端末を使用していただきありがとうございます。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;■';

var ENG_AUTHOR_14 = '========================================================';
var CHN_AUTHOR_14 = '====================================================';
var JPN_AUTHOR_14 = '============================================================';

//登录
var ENG_COMMAND_LOGIN = 'Login:';
var CHN_COMMAND_LOGIN = '登录用户名：';
var JPN_COMMAND_LOGIN = 'ユーザー名をログインする:';

//密码
var ENG_COMMAND_PSW = 'Password:';
var CHN_COMMAND_PSW = '密码：';
var JPN_COMMAND_PSW = 'パスワード:';

//成功清除历史记录
var ENG_HISTORY_CLEAN = 'Clean succeed';
var CHN_HISTORY_CLEAN = '成功清除历史记录';
var JPN_HISTORY_CLEAN = '歴史の記録を除去することは成功する。';

//历史记录清除出错
var ENG_HISTORY_CLEAN_ERR = 'Clean ERROR';
var CHN_HISTORY_CLEAN_ERR = '历史记录清除出错';
var JPN_HISTORY_CLEAN_ERR = '歴史記録を除去ことはミスをおかす。';

//未识别的内部命令
var ENG_VS_COMMAND_NOT_REC = 'Internal command is not recognized, please enter "$vs:help" or "$vs:?" to query the command set';
var CHN_VS_COMMAND_NOT_REC = '未识别的内部命令，请输入【$vs:help】或【$vs:?】查询命令集';
var JPN_VS_COMMAND_NOT_REC = '内部コマンドが認識されなくて「$vs:help」または「$vs:?」を入力してコマンド セットクエリする。';

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

//切换语言提示
var ENG_LAN = 'Language ID:';
var CHN_LAN = '语言ID：';
var JPN_LAN = '言語のid：';

//语言包切换
var ENG_LAN_SELECT = 'Please select language by insert the id of languages';
var CHN_LAN_SELECT = '请输入语言id以切换语言';
var JPN_LAN_SELECT = '言語を変更する言語 ID を入力してください。';

//语言包切换成功
var ENG_LAN_SUCCESS = 'Language changed successfully!';
var CHN_LAN_SUCCESS = '切换语言成功';
var JPN_LAN_SUCCESS = '言語の切り替え成功';

