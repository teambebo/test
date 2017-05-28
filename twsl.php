<?php
define('API_KEY','394164565:AAHaeLohHkm3UM3zWIEcskeiFYrauPQV3IY');
//----######------
function makereq($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
//##############=--API_REQ
function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }
  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }
  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = "https://api.telegram.org/bot".API_KEY."/".$method.'?'.http_build_query($parameters);
  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  return exec_curl_request($handle);
}
//----######------
//---------
$update = json_decode(file_get_contents('php://input'));
var_dump($update);
//=========
$chat_id = $update->message->chat->id;
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$name = $update->message->from->first_name;
$username = $update->message->from->username;
$textmessage = isset($update->message->text)?$update->message->text:'';
$txtmsg = $update->message->text;
$reply = $update->message->reply_to_message->forward_from->id;
$stickerid = $update->message->reply_to_message->sticker->file_id;
$admin = 330256239;
$step = file_get_contents("data/".$from_id."/step.txt");
$ban = file_get_contents('data/banlist.txt');
//-------
function SendMessage($ChatId, $TextMsg)
{
 makereq('sendMessage',[
'chat_id'=>$ChatId,
'text'=>$TextMsg,
'parse_mode'=>"MarkDown"
]);
}
function SendSticker($ChatId, $sticker_ID)
{
 makereq('sendSticker',[
'chat_id'=>$ChatId,
'sticker'=>$sticker_ID
]);
}
function Forward($KojaShe,$AzKoja,$KodomMSG)
{
makereq('ForwardMessage',[
'chat_id'=>$KojaShe,
'from_chat_id'=>$AzKoja,
'message_id'=>$KodomMSG
]);
}
function save($filename,$TXTdata)
	{
	$myfile = fopen($filename, "w") or die("Unable to open file!");
	fwrite($myfile, "$TXTdata");
	fclose($myfile);
	}
//===========
$inch = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@Aleppo_Team&user_id=".$from_id);
	
	if (strpos($inch , '"status":"left"') !== false ) {
SendMessage($chat_id,"لصناعة بوت يجب عليك اولا الاشتراك في القناة ثم اكتب
/start
القناة : 
@Aleppo_Team");
}
if (strpos($ban , "$from_id") !== false  ) {
SendMessage($chat_id,"You Are Banned From Server.🤓\nDon't Message Again...😎 \n➖➖➖➖➖➖➖➖➖➖\n انت ممنوع من استخدام البوت لا ترسل رسالة مرة اخرى...😎");
	}

elseif(isset($update->callback_query)){
    $callbackMessage = '';
    var_dump(makereq('answerCallbackQuery',[
        'callback_query_id'=>$update->callback_query->id,
        'text'=>$callbackMessage
    ]));
    $chat_id = $update->callback_query->message->chat->id;
    
    $message_id = $update->callback_query->message->message_id;
    $data = $update->callback_query->data;
    if (strpos($data, "del") !== false ) {
    $botun = str_replace("del ","",$data);
    unlink("bots/".$botun."/index.php");
    save("data/$chat_id/bots.txt","");
    save("data/$chat_id/tedad.txt","0");
    var_dump(
        makereq('editMessageText',[
            'chat_id'=>$chat_id,
            'message_id'=>$message_id,
            'text'=>"تم ازالة البوت بنجاح !\n Robot has ben deleted!",
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>"رجائا ادخل القناة - Pease join to my channel",'url'=>"https://telegram.me/Aleppo_Team"]
                    ]
                ]
            ])
        ])
    );
 }
 else {
    var_dump(
        makereq('editMessageText',[
            'chat_id'=>$chat_id,
            'message_id'=>$message_id,
            'text'=>"خطأ-Error",
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>"رجائا ادخل القناة - Pleas join to my channel",'url'=>"https://telegram.me/Aleppo_Team"]
                    ]
                ]
            ])
        ])
    );
 }
}

elseif ($textmessage == '🔙 عودة - Back') {
save("data/$from_id/step.txt","none");
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"🔃 مرحبا بك في القائمة الرئيسية

🔃 Welcome To Main Menu",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
  [
                   ['text'=>"العربية"],['text'=>"English"]          
]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
}
elseif ($step == 'delete') {
$botun = $txtmsg ;
if (file_exists("bots/".$botun."/index.php")) {

$src = file_get_contents("bots/".$botun."/index.php");

if (strpos($src , $from_id) !== false ) {
save("data/$from_id/step.txt","none");
unlink("bots/".$botun."/index.php");
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"تم حذف البوت الخاص بك
رجائا اصنع بوت جديد
Your Robot has ben deleted 
Please create new bot",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"/start"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
}
else {
SendMessage($chat_id,"خطأ!
لا يمكنك حذف هذا البوت !
Error 
You cant delete this bot");
}
}
else {
SendMessage($chat_id,"غير معثور عليه \n Not found");
}
}
elseif ($step == 'create bot') {
$token = $textmessage ;

			$userbot = json_decode(file_get_contents('https://api.telegram.org/bot'.$token .'/getme'));
			//==================
			function objectToArrays( $object ) {
				if( !is_object( $object ) && !is_array( $object ) )
				{
				return $object;
				}
				if( is_object( $object ) )
				{
				$object = get_object_vars( $object );
				}
			return array_map( "objectToArrays", $object );
			}

	$resultb = objectToArrays($userbot);
	$un = $resultb["result"]["username"];
	$ok = $resultb["ok"];
		if($ok != 1) {
			//Token Not True
			SendMessage($chat_id,"التوكن الخاص بك خطأ!\nYour token is invalid");
		}
		else
		{
		SendMessage($chat_id,"جاري صنع البوت ...\n Are making robots...");
		if (file_exists("bots/$un/index.php")) {
		$source = file_get_contents("bot/index.php");
		$source = str_replace("**TOKEN**",$token,$source);
		$source = str_replace("**ADMIN**",$from_id,$source);
		save("bots/$un/index.php",$source);	
		file_get_contents("http://api.telegram.org/bot".$token."/setwebhook?url=https://webhook/bots/$un/index.php");

var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"تم صناعة البوت الخاص بك بنجاح
Your Robot Has ben Created
[الدخول للبوت - start Bot](https://telegram.me/$un)",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"🔙 عودة - Back"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
		}
		else {
		save("data/$from_id/tedad.txt","1");
		save("data/$from_id/step.txt","none");
		save("data/$from_id/bots.txt","$un");
		
		mkdir("bots/$un");
		mkdir("bots/$un/data");
		mkdir("bots/$un/data/btn");
		mkdir("bots/$un/data/words");
		mkdir("bots/$un/data/profile");
		mkdir("bots/$un/data/setting");
		
		save("bots/$un/data/blocklist.txt","");
		save("bots/$un/data/last_word.txt","");
		save("bots/$un/data/pmsend_txt.txt","Message Sent!");
		save("bots/$un/data/start_txt.txt","Hello World!");
		save("bots/$un/data/forward_id.txt","");
		save("bots/$un/data/users.txt","$from_id\n");
		mkdir("bots/$un/data/$from_id");
		save("bots/$un/data/$from_id/type.txt","admin");
		save("bots/$un/data/$from_id/step.txt","none");
		
		save("bots/$un/data/btn/btn1_name","");
		save("bots/$un/data/btn/btn2_name","");
		save("bots/$un/data/btn/btn3_name","");
		save("bots/$un/data/btn/btn4_name","");
		
		save("bots/$un/data/btn/btn1_post","");
		save("bots/$un/data/btn/btn2_post","");
		save("bots/$un/data/btn/btn3_post","");
		save("bots/$un/data/btn/btn4_post","");
	
		save("bots/$un/data/setting/sticker.txt","✅");
		save("bots/$un/data/setting/video.txt","✅");
		save("bots/$un/data/setting/voice.txt","✅");
		save("bots/$un/data/setting/file.txt","✅");
		save("bots/$un/data/setting/photo.txt","✅");
		save("bots/$un/data/setting/music.txt","✅");
		save("bots/$un/data/setting/forward.txt","✅");
		save("bots/$un/data/setting/joingp.txt","✅");
		
		$source = file_get_contents("bot/index.php");
		$source = str_replace("**TOKEN**",$token,$source);
		$source = str_replace("**ADMIN**",$from_id,$source);
		save("bots/$un/index.php",$source);	
		file_get_contents("http://api.telegram.org/bot".$token."/setwebhook?url=https://webhook/bots/$un/index.php");

var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"تم صناعة البوت الخاص بك بنجاح
Your Robot Has ben Created
[للدخول للبوت - start Bot](https://telegram.me/$un)",		
                'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"🔙 عودة - Back"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
		}
}
}
elseif (strpos($textmessage, "/setvip") !== false) {
$botun = str_replace("/setvip ","",$textmessage);
SendMessage($chat_id,"$textmessage");
/*$src = file_get_contents("bots/$botun/index.php");
$nsrc = str_replace("**free**","gold",$src);
save("data/$botun/index.php",$nsrc);
SendMessage($chat_id,"Updated!");*/
}
elseif (strpos($textmessage , "/toall") !== false ) {
if ($from_id == $admin) {
$text = str_replace("/toall","",$textmessage);
$fp = fopen( "data/users.txt", 'r');
while( !feof( $fp)) {
 $users = fgets( $fp);
SendMessage($users,"$text");
}
}
else {
SendMessage($chat_id,"You Are Not Admin");
}
}
elseif (strpos($textmessage , "/feedback") !== false ) {
if ($from_id == $ban) {
$text = str_replace("/feedback","",$textmessage);
SendMessage($chat_id,"ارسل تعليقك \n Your comment has been sent");
SendMessage($admin,"هناك رسالة جديدة من احد الاعضاء : \n اسمه : $name \n معرفه : $username \n ايديه : $from_id\n رسالته : $text");
}
else {
SendMessage($chat_id,"You Are banned");
}
}
elseif (strpos($textmessage , "/report") !== false ) {
if ($from_id == $ban) {
$text = str_replace("/report","",$textmessage);
SendMessage($chat_id,"سوف يتم التأكد من بوتك في وقت لاحق \n Robots Ben is later confirmed");
SendMessage($admin,"Report : \n name: $name \n username: $username \n id: $from_id\n Bot: $text");
}
else {
SendMessage($chat_id,"You Are banned");
}
}
elseif($textmessage == '🚀 ربات های من')
{
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id,"شما هنوز هیچ رباتی نساخته اید !");
return;
}
 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"قائمة البوتات الخاص بك : ",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'url'=>"https://telegram.me/".$botname]
	]
	]
	])
	]));
}
elseif($textmessage == '🚀 my robots')
{
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id,"You still have not robots!");
return;
}
 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"Your Bot Lists : ",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'url'=>"https://telegram.me/".$botname]
	]
	]
	])
	]));
}
elseif($textmessage == '/start' )
{
if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"Please Select your Language.
➖➖➖➖➖
رجاءً اختر لغتك.",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
[
                   ['text'=>"عربي"],['text'=>"English"]          
]
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == 'تغيير اللغة' )
{
if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"Please Select your Language.
➖➖➖➖➖
رجاءً اختر لغتك.",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
[
                   ['text'=>"عربي"],['text'=>"English"]          
]
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == 'Language' )
{
if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"Please Select your Language.
➖➖➖➖➖
رجاءً اختر لغتك.",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
[
                   ['text'=>"عربي"],['text'=>"English"]          
]
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == 'English')
{

if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"Hello👋😉

🔹 WellCome To PvSyrian Robot 🌹

🔸 with this Service you can Provide Support  your Robot Mmbers , Channel , Groups and  Websites 

🔹 To Create Robot Select '🔄 Create a Robot' Button!",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
                [
                   ['text'=>"🔄 Create a Robot"],['text'=>"🚀 my robots"],['text'=>"☢ Delete a Robot"]
                ],
                [
                   ['text'=>"ℹ️ help"],['text'=>"🔰 rules"]
                ],
                     [
                   ['text'=>"✅ Send Comment"],['text'=>"⚠️ Robot Report"]
                ],
[
                   ['text'=>"Language"]          
]
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == 'عربي' )
{

if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"اهلا بك عزيزي 💡

📌┇اذهب الى @botfather 
ثم ارسل /newbot لصنع بوتك 💡

📌┇ وارسل توكن بوتك الى هنا

📌┇ وسوف يتم صنع بوت تواصل خاص بك

📌┇ارسل /info لمعرفة كيفية استخدام البوت 

📌┇(  🌐 اضغط هنا وتابع جديدنا  🌐 )[https://telegram.me/Aleppo_Team]",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
                [
                   ['text'=>"🔄 صنع بوت"],['text'=>"🚀 ربات های من"],['text'=>"☢ حذف بوت"]
                ],
                [
                   ['text'=>"ℹ️ معلومات"],['text'=>"🔰 القوانين"]
                ],
                [
                   ['text'=>"✅ اترك تعليق"],['text'=>"⚠️ الابلاغ عن خطأ"]
                ],
           [
                ['text'=>"تغير اللغة"]
            ]
                
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == '🔰 القوانين') {
SendMessage($chat_id, "1️⃣البوت للتواصع مع اعضاء قناتك او مجموعتك او للاعضاء المحظورين
2⃣نحن غير مسؤلين عن اي استخدام خاطئ للبوت
3⃣سيتم حظر اي شخص يستخدم البوت بشكل خاطئ
-");
}
elseif($textmessage == '🔰 rules') {
SendMessage($chat_id, "1⃣Bot to communicate with members of your channel or group or banned members
2⃣We are not responsible for any misuse of bot.
3⃣Anyone who uses bot will be banned incorrectly
-");
}
elseif($textmessage == 'ℹ️ معلومات') {
SendMessage($chat_id, "بوت انشاء بوتات تواصل \n من انشاء فريق \n @Aleppo_Team \n مطور البوت \n @VPS_TM \n اصدار البوت \n V1.0");
}
elseif($textmessage == 'ℹ️ help') {
SendMessage($chat_id, "Bot Create Bots continue \n Channel : \n @Aleppo_Team \n Developer : \n @VPS_TM \n Bot VERISON : \n V1.0");
}
elseif($textmessage == '✅ اترك تعليق' ) {
SendMessage($chat_id, "تعليقك يساعدنا للتحسن \n لكتابة تعليق \n /feedback \n (وهنا الرسالة)");
}
	elseif($textmessage == '⚠️ الابلاغ عن خطأ' ) {
SendMessage($chat_id, "إذا كان البوت الخاص بك يتعارض مع قوانيننا يرجى إعلامنا باستخدام الأمر التالي
/report (usernamebot)");
}
elseif($textmessage == '✅ Send Comment') {
SendMessage($chat_id, "🖊 Your Comments Help Us To Be Better.\n\n📝 To Send Comment Use this Command Below.\n\n/feedback (Your Message)");
}
	elseif($textmessage ==  '⚠️ Robot Report') {
SendMessage($chat_id, "If the robot to act contrary to our laws let us know using the following command\n/report (usernamebot)");
}
elseif ($textmessage == '☢ حذف بوت' ) {
if (file_exists("data/$from_id/step.txt")) {

}
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id"انت لا تملك اي بوت !");

}
else {
//save("data/$from_id/step.txt","delete");


 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"اختر احد هذه البوتات :",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'callback_data'=>"del ".$botname]
	]
	]
	])
	]));

/*
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"اختر احد البوتات لحذفهه : ",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[
            	['text'=>$botname]
            	],
                [
                   ['text'=>"🔙 العودة"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		])); */
}
}
elseif ($textmessage == '☢ Delete a Robot' ) {
if (file_exists("data/$from_id/step.txt")) {

}
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id,"Do robots have not");

}
else {
//save("data/$from_id/step.txt","delete");


 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"Select one of your robots:",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'callback_data'=>"del ".$botname]
	]
	]
	])
	]));

/*
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"حدد احد هذه البوتات : ",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[
            	['text'=>$botname]
            	],
                [
                   ['text'=>"🔙 العودة"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		])); */
}
}
elseif ($textmessage == '☢ Delete a Robot' ) {
if (file_exists("data/$from_id/step.txt")) {

}
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id,"Do robots have not");

}
else {
//save("data/$from_id/step.txt","delete");


 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"Select one of your robots:",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'callback_data'=>"del ".$botname]
	]
	]
	])
	]));

/*
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"اختر احد البوتات لحذفها : ",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[
            	['text'=>$botname]
            	],
                [
                   ['text'=>"🔙 العودة"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		])); */
}
}
elseif ($textmessage == '🔄 صنع بوت' ) {

$tedad = file_get_contents("data/$from_id/tedad.txt");
if ($tedad >= 1) {
SendMessage($chat_id,"لديك الكثير من البوتات
يجب عليك حذف احدها لانشاء آخر ! $tedad");
return;
}
save("data/$from_id/step.txt","create bot");
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"ارسل التوكن الخاص بك :",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"🔙 العودة - Back"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
}
elseif ($textmessage == '🔄 Create a Robot') {

$tedad = file_get_contents("data/$from_id/tedad.txt");
if ($tedad >= 1) {
SendMessage($chat_id," The number of robots you $ tedad
Each person can only build other robots you can build a robot");
return;
}
save("data/$from_id/step.txt","create bot");
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"Please send your token ",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"🔙 العودة - Back"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
}
	elseif (strpos($textmessage , "/ban" ) !== false ) {
if ($from_id == $admin) {
$text = str_replace("/ban","",$textmessage);
$myfile2 = fopen("data/banlist.txt", 'a') or die("Unable to open file!");	
fwrite($myfile2, "$text\n");
fclose($myfile2);
SendMessage($admin,"كلمة المرور $text تم حضر هاذا المستخدم لألغاء الحضر ارسل/n/unban $text");
}
else {
SendMessage($chat_id,"⛔️ انت لست مشرف.");
}
}

elseif (strpos($textmessage , "/unban" ) !== false ) {
if ($from_id == $admin) {
$text = str_replace("/unban","",$textmessage);
			$newlist = str_replace($text,"",$ban);
			save("data/banlist.txt",$newlist);
SendMessage($admin,"لكمة المرور $text تم الغاء حضر هاذ المستخدم !");
}
else {
SendMessage($chat_id,"⛔️ انت لست مشرف.");
}
}
	
else
{
SendMessage($chat_id,"تعذر العثور على رسالتك ❌\n Your command could not be found❌");
}
?>
