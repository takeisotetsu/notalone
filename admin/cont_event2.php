<?php
include_once("include.php");
date_default_timezone_set('Asia/Tokyo');
$ThisFile   = "cont_event2.php";
//$NextFile   = $_SESSION["NextJob"];
//$ReturnFile = $_SESSION['CallJob'];
//$Case1File  = "common_user_append.php";  	//ユーザ登録なしの場合のアクセスファイル名

//=============================
$acc_level  = 1;			//アクセスレベル

$ReturnFile = $ThisFile;		//戻り先のファイル名
//$_SESSION['CallJob'] = $ThisFile;	//log_in.php　からの戻り用
$_SESSION['CallJob'] = $ThisFile . "?" . $_SERVER["QUERY_STRING"];	//log_in.php　からの戻り用
include("log_in_check.php");
//=============================


//使用するテキストDB
if(isset($_GET['dir'])){
	$db_Dir   = "../" . trim($_GET['dir']);
}else{
	$db_Dir   = "../uploads/events";
}
if(isset($_GET['fname'])){
	$db_filename = trim($_GET['fname']);

	/*
	$files = explode("." , $db_filename);
	$db_Head = $files[0];
	$db_Ext  = $files[1];
	*/

	//ファイル名(filename)に　. が入っても拡張子(extension)抽出可能
	$filepath = pathinfo($db_filename);
	$db_Head = $filepath['filename'];
	$db_Ext  = $filepath['extension'];

	//print($db_Head . " / " . $db_Ext . "<br />");
}else{
	$db_Head = "201512";
	$db_Ext  = "csv";
}
$db_Table = $db_Dir . "/" . $db_Head . "." . $db_Ext;


//common_header("control EventTable");

$user_level = Access_check( $acc_level ,0,1,$ReturnFile);
//print('レベル　＝　1:一般ユーザ　2:管理ユーザ　3:システム管理者<br>');

//ファイルの存在確認
if(!file_exists($db_Table)){
	print("データベースファイルがありません");
	exit();
}

//一般ユーザーで他者のファイルにアクセスする場合は、読み出し専用（閲覧）
//javascript ReadOnly とセット
$id = $_SESSION[$USER_session];
$id_length   = strlen($id);
$head_length = strlen($db_Head);
$myfile = FALSE;
if($id_length <= $head_length){
	if(substr($db_Head,0,$id_length) == $id){
		$myfile = TRUE;
	}
}

$ReadOnly = 'false';
if($user_level == 1){
	if($myfile == FALSE){
		$ReadOnly = 'true';
	}
}
//***********************

//*************
$falename = $db_filename;
if($ReadOnly == 'true'){
	common_header("イベントファイルの閲覧<span class='sub_title'>$falename</span>");
	//echo 'あなたのファイルではありません（閲覧のみ可能）';
}else{
	common_header("イベントファイルの編集<span class='sub_title'>$falename</span>");
/*
	echo '
	<ul>
	<li>,（カンマ）"（ダブルクオーテーション）\'（シングルクオーテーション）は使用できません</li>
	<li>データを更新する場合は、必ずファイルに保存してください</li>
	</ul>
	';

	//***** Data Check ******
	echo '
	<input type="button" onClick="DataCheck()" value="イベントデータの簡易チェック">
	<div id="errorArea" style="position:fixed; top:180px; left:10px;">
		<textarea id="errorResult" rows="10" cols="100" style="background:lightyellow;" wrap="off">
		</textarea>
		<input type="button" onClick="errorAreaClose()" value="閉じる">
	</div>
	';	
	//***********************
*/
	
}

//*************

//print("<h3>【ファイル名：" . $db_Table . "】</h3>");

//db内容をjavascript用に読み込む
$DataString    = csvDatabaseRead($db_Table,1);
//print($DataString);
  

?>

<link rel="stylesheet" href="../js/jquery-ui-1.11.4.custom/jquery-ui.min.css">

<?php //<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script> ?>
<script type="text/javascript" src="../js/csvdatabase2.js?ver=160120"></script>
<script type="text/javascript" src="../js/sha256.js"></script>

<script type="text/javascript" src="../js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

<script type="text/javascript">

//DataShow Mode Check

<?php
//ReadOnly mode
print("var ReadOnly = " . $ReadOnly . ";" ); 
?>

//DataBase 宣言 ***** php側でデータ読み込み

<?php 
//datadir
print("var DataDir   ='" . $db_Dir  . "';\n" );
?>
<?php
print("var DataHead  ='" . $db_Head . "';\n" );
?>
<?php 
print("var DataExt   ='" . $db_Ext  . "';\n" );
?>
<?php 
//datatable
print("var DataTable ='" . $db_Table . "';\n" );
?>

<?php
//配列情報の設定（イベントファイル）
print("var DataArray    =" . $DataString  );
?>

<?php

//イベントファイルのフィールド定義を読み出す
//個別に手動で定義することも可能
$fields_File = "../localhost/eventfields.csv";
$result = fieldDataRead($fields_File);
echo $result;

?>

/*
//**************
// 呼び出し側のｐｈｐファイルで定義
//フィールド構造ファイルの読み出し、または手動で設定
//フィールド名
var Fno = new Array();
Fno[0] = 'no';
Fno[1] = 'memo';

//フィールドのラベル
var Flabel = new Array();
Flabel['no'] = 'No';
Flabel['eventtitle'] = 'イベントタイトル';

//フィールドの表示幅
var Field_etc = new Array();
Field_etc['no']           = 50;
Field_etc['eventtitle'] = 200;
Field_etc['where']      = 150;
Field_etc['place']      = 120;
Field_etc['whom']       = 150;
Field_etc['what']       = 300;
Field_etc['who']        = 200;
Field_etc['contact']    = 150;
Field_etc['fee']        = 50;
Field_etc['when']       = 100;
Field_etc['openTime']   = 80;
Field_etc['closeTime']  = 80;
Field_etc['tag1']       = 100;
Field_etc['url']        = 150;
*/

//フィールドの入力タイプ
var Ftype = new Array();
Ftype['what']    = "text";
Ftype['contact'] = "text";


//**************
var whenFieldName = "when";
var whenFieldNo   = DataFieldNo(whenFieldName);
var inputId = "#Mydate";

var whereFieldName = "where";
var whereFieldNo   = DataFieldNo(whereFieldName);

var tagFieldName  = "tag1";
var tagFieldNo    = DataFieldNo(tagFieldName);

var uidFieldName  = "uid";
var uidFieldNo    = DataFieldNo(uidFieldName);

//login user name
//var uidHead       = "<?php echo $id ?>";

//login timestamp 16
var uidHead;
function getUidHead(){
	var date = new Date();
	var uidHead = Math.floor( date.getTime() / 1000 ).toString(16);
	return uidHead;
}
var uidClass      = "evt";


/* befor UID //////
function setOption(){
	//var buff = '<br /><br /><input type="button" onClick="inputSupportOpen()" value="入力補助を表示する"/>';
	//return buff;
	return "";
}
*/


function setOption(){
	var option = "";
	option += "<div style='display:none'>";
	option += "<br /><input type='button' value='UIDの生成' onclick='getUID()' />";
	option += "<br /><input type='text'   name='keta' id='keta' size='2' value='3' />桁";
	option += "<input type='hidden' name='kazu' id='kazu' size='1' value='1' />";
	option += "<input type='checkbox' name='suuji' id='suuji' checked />数字";
	option += "<input type='checkbox' name='small' id='small' />英語小文字";
	option += "<input type='checkbox' name='big'   id='big' />英語大文字";
	option += "</div>";
	return option;
}


function setData(field){
	if(field == 'when'){
		var getdate = $(inputId).val();
		$('#Mydata_' + whenFieldNo).val(getdate);
	}
	
	if(field == 'where'){
		var locId = "#wherelist";
		var getlocation = $(locId).val();
		$('#Mydata_' + whereFieldNo).val(getlocation);		
	}

	if(field == 'tag'){
		var tagId = "#taglist";
		var gettarget = $(tagId).val();
		$('#Mydata_' + tagFieldNo).val(gettarget);		
	}

	if(field == 'uid'){
		var uidId = "#uidlist";
		var gettarget = $(uidId).val();
		$('#Mydata_' + uidFieldNo).val(gettarget);		
	}
}

function inputSupportOpen(){
	$('#inputSupport').css('visibility' , 'visible');
}

function inputSupportClose(){
	$('#inputSupport').css('visibility' , 'hidden');
}

//カレンダー（detepicker）による誕生日入力
$(function() {
	var idname = inputId;

	$(idname).datepicker({
		//showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		dateFormat:'yy/MM/dd'
	});

	$(idname).datepicker("option", "showOn", 'button');
	$(idname).datepicker("option", "buttonImageOnly", true);
	$(idname).datepicker("option", "buttonImage", '../images/ico_calendar.png');

	$(idname).datepicker("option", "showButtonPanel", true);

});

//datepicker　日本語化オプション
$(function($){
    $.datepicker.regional['ja'] = {
        closeText: '閉じる',
        prevText: '<前',
        nextText: '次>',
        currentText: '今日',
        monthNames: ['01','02','03','04','05','06',
        '07','08','09','10','11','12'],
        monthNamesShort: ['1月','2月','3月','4月','5月','6月',
        '7月','8月','9月','10月','11月','12月'],
        dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
        dayNamesShort: ['日','月','火','水','木','金','土'],
        dayNamesMin: ['日','月','火','水','木','金','土'],
        weekHeader: '週',
        dateFormat: 'yy/mm/dd',
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: '年'};
    $.datepicker.setDefaults($.datepicker.regional['ja']);
});


//イベントデータの簡易チェック
//check field ***************
var titleField = "eventtitle";

var whereField = "where";
var whenField  = "when";
var openField  = "openTime";
var closeField = "closeTime";
var tagField   = "tag1";

var tagId   = "target_id";
var tagName = "target_label";
var locId   = "location_name";

var CR = "\n"; 
//***************************

function DataCheck(){

	var buff ="";
	var fieldNo;
	var eventdate;
	var mdata;
	var data;
	var msg;

	//配列情報の設定（イベントファイル）
	//0 はフィールド名、1 からスタート
	for(var i = 1; i < DataArray.length ; i++){
		msg = "";

		//*****
		fieldNo = DataFieldNo(whenField);
		mdata = DataArray[i][fieldNo];
		eventdate = mdata;
		data = DataArray[i][fieldNo].split("/");
		if(data.length != 3){
			msg += whenField + "(" + mdata + ")のデータエラー　/　";
		}else{
			if(data[0].length != 4){
				msg += whenField + "(" + data[0] + ")年の桁数エラー /　";
			}
			if(data[1].length != 2){
				msg += whenField + "(" + data[1] + ")月の桁数エラー /　";
			}
			if(data[2].length != 2){
				msg += whenField + "(" + data[2] + ")日の桁数エラー /　";
			}
		}


		//*****
		fieldNo = DataFieldNo(openField);
		mdata = DataArray[i][fieldNo];
		data = DataArray[i][fieldNo].split(":");
		if(data.length < 3 ){
			msg += openField + "(" + mdata + ")のデータエラー　/　";
		}else{
			if(data[0].length != 2){
				msg += openField + "(" + data[0] + ")時の桁数エラー /　";
			}
			if(data[1].length != 2){
				msg += openField + "(" + data[1] + ")分の桁数エラー /　";
			}
		}

		//*****
		fieldNo = DataFieldNo(closeField);
		mdata = DataArray[i][fieldNo];
		data = DataArray[i][fieldNo].split(":");
		if(data.length < 3 ){
			msg += closeField + "(" + mdata + ")のデータエラー　/　";
		}else{
			if(data[0].length != 2){
				msg += closeField + "(" + data[0] + ")時の桁数エラー /　";
			}
			if(data[1].length != 2){
				msg += closeField + "(" + data[1] + ")分の桁数エラー /　";
			}
		}

		//******
		fieldNo = DataFieldNo(tagField);
		data = DataArray[i][fieldNo];
		if(data != ""){
			var find = 0;
			for(var s = 0 ; s < targetArray.length ; s++){
				if(targetArray[s][tagId] == data){
					find = 1;
					break;
				}
			}
			if(find == 0){
				msg += tagField + "(" + data + ")と一致するラベルがありません / ";
			}
		}

		//******
		fieldNo = DataFieldNo(whereField);
		data = DataArray[i][fieldNo];
		var find = 0;
		for(var s = 0 ; s < locationArray.length ; s++){
			if(locationArray[s][locId] == data){
				find = 1;
				break;
			}
		}
		if(find == 0){
			msg += whereField + "(" + data + ")と一致する名前がありません / ";
		}


		//*****************
		if(msg != ""){
			//fieldNo = DataFieldNo("no");
			//buff += i + "行目 no:" + DataArray[i][fieldNo] + "　=　";

			fieldNo = DataFieldNo(titleField);
			buff += i + "行目 :" + DataArray[i][fieldNo] + "[" + eventdate + "]　=　";

			buff += msg; 

			buff += CR;
		}
	}

	$("#errorArea").css("display" , "block");
	$("#errorResult").html(buff);
}

function errorAreaClose(){
	$("#errorArea").css("display" , "none");
}


//*****************
var targetTable   = "../localhost/target.csv";
var locationTable = "../localhost/location.csv";

var targetArray   = new Array();
var locationArray = new Array();

var where_option="";
var tags_option="";
//タグセレクトボックスのoption要素を設定
setRensouArray(targetTable , targetArray , function(){
	var ren = targetArray;
	for(var i = 0 ; i < ren.length ; i++){
		tags_option += '<option value="'+ren[i][tagId]+'">'+ren[i][tagName] + "(" + ren[i][tagId] + ")"+'</option>';
	}
});
//場所セレクトボックスのoption要素を設定
setRensouArray(locationTable , locationArray , function(){
	var ren = locationArray;
	for(var i = 0 ; i < ren.length ; i++){
		where_option += '<option value="'+ren[i][locId]+'">'+ren[i][locId]+'</option>';
	}
});

/*
$(function() {
	//連想配列の設定
	set_tagSelect($("#taglist"));
	set_placeSelect($("#wherelist"));
});
//タグセレクトボックスの設定
function set_tagSelect(target_select){
	setRensouArray(targetTable , targetArray , function(){
		//入力補助機能
		var ren = targetArray;
		for(var i = 0 ; i < ren.length ; i++){
			target_select.append($("<option>").val(ren[i][tagId]).html(ren[i][tagName] + "(" + ren[i][tagId] + ")" ));
		}
	});
}
//場所セレクトボックスの設定
function set_placeSelect(target_select){
	setRensouArray(locationTable , locationArray , function(){
		//入力補助機能
		var ren = locationArray;
		for(var i = 0 ; i < ren.length ; i++){
			target_select.append($("<option>").val(ren[i][locId]).html(ren[i][locId]));
		}
	});
}
*/

//csvデータを読み込み、連想配列に設定する
function setRensouArray(table , rensouArray , cr){
	csvToArray( table , function(data) {

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			rensouArray.push(rensou);
		}
		cr();
	});	
}

//CSVファイルの読み込み
function csvToArray(filename, cb) {
	//キャッシュしない
	$.ajaxSetup({
		cache: false
	});

	$.get(filename, function(csvdata) {
		//CSVのパース作業
		//CRの解析ミスがあった箇所を修正しました。
		//以前のコードだとCRが残ったままになります。
		// var csvdata = csvdata.replace("\r/gm", ""),
		csvdata = csvdata.replace(/\r/gm, "");

		var line = csvdata.split("\n"),
		ret = [];
		for (var i in line) {
        		//空行はスルーする。
        		if (line[i].length == 0) continue;

        		var row = line[i].split(",");
        		ret.push(row);
      		}
      		cb(ret);
	});
}

//uid をチェックして返す（ユニーク）
function getUID(){
	var comp = 0;
	var booking = 0;
	var returnUid = "";

	while(comp == 0){
		returnUid = makeUID();
		for(var i = 1 ; DataArray.length <= i ; i++){
			if(DataArray[i][uidFieldNo] == returnUid){
				//重複あり
				booking = 1;
				//alert("重複しています");
				break;
			}
		}
		//重複なし
		if(booking == 0){
			comp = 1;
		}
	}

	return returnUid;	
}

//var uidHead , var uidClass 
//uidをランダム発生
function makeUID( ){
  	//エラーフラグ
  	err = "off";

  	keta = chgMessHalf($('#keta').val());
  	kazu = chgMessHalf($('#kazu').val());

  	//文字定義
  	moji = "";
  	if($('#suuji').prop('checked')){
    		moji += "0123456789";
  	}
  	if($('#small').prop('checked')){
    		moji += "abcdefghijklmnopqrstuvwxyz";
  	}
  	if($('#big').prop('checked')){
    		moji += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  	}
  	if(err == "off"){
    		uid = "";
    		//ユニークIDの生成
    		for(i=0; i< kazu; i++){
      			for(j=0; j< keta; j++){
        			num = Math.floor(Math.random() * moji.length);
       				uid += moji.charAt(num);
      			}
      			uid += "\n";
    		}
		var emt  = $('#Mydata_' + uidFieldNo);

		//UID の形式
		//emt.val( uidHead + uidClass + uid);
		//emt.val( getUidHead() + uidClass + uid);
		emt.val( uidClass + getUidHead() + uid);

 	} else {
    		alert("数字を入力してください。");
  	}//end makeUID
}

//make random uid
//半角数字変換用文字定義
var half = "0123456789";
var full = "０１２３４５６７８９";
function chgMessHalf(VAL){

  	messIn = VAL;
  	messOut = "";

  	for(i=0; i<messIn.length; i++){
    		oneStr = messIn.charAt(i);
    		num = full.indexOf(oneStr,0);
    		oneStr = num >= 0 ? half.charAt(num) : oneStr;
    		messOut += oneStr;
  	}

  	//数字か空かチェック
  	if(isNaN(messOut) || messOut==""){
    		err = "on";
  	}

  	return messOut;
}

</script>


<!--body onload="ShowData()"-->
<?php //<body onload="init()">  ?>
<script> window.onload = function() { init(); } </script>

<div id="cont_area">
</div>
<?php /*
<div id="inputSupport">
	開催日の設定<br />
	<input type="text" id="Mydate" size="12" value="" readonly><br />
	<input type="button" id="MydateSet" onClick="setData('when')" value="設定する" />
	<hr />

	施設名の選択<br />
	<select id="wherelist">
	</select><br />
	<input type="button" id="MywhereSet" onClick="setData('where')" value="設定する" />
	<hr />
	
	任意区分の選択<br />
	<select id="taglist">
	</select><br />
	<input type="button" id="MytagSet" onClick="setData('tag')" value="設定する" />
	<hr />
	<input type="button" onClick="inputSupportClose()" value="閉じる" />
</div>
*/ ?>

<div id="list">
</div>

<?php common_menu(1); ?>

<?php include_once 'include_footer.php'; ?>