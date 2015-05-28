<?php
require_once('../xajax/xajax_core/xajax.inc.php');
$xajax = new xajax();
$xajax->setFlag('debug', true);
function savetodb($form) {
	$host = 'localhost';
	$username = 'my_user';
	$password = 'my_pass';
	$database = 'test_db';
	$connect = mysql_connect($host, $username, $password);
	mysql_select_db($database, $connect);
    $title = $form["title"];
	$editor1 = $form["editor1"];
	//
	$sql = "INSERT INTO draft (`title`, `content`) VALUES ('$title', '$editor1')";
	$result = mysql_query($sql, $connect);
	$_SESSION['article_id'] = mysql_insert_id($connect);
	$objResponse = new xajaxResponse();
	$objResponse->assign("autosavemsg","innerHTML", "<br />Saved successfully!"); 
    return $objResponse; 
}
$xajax->registerFunction('savetodb');
$xajax->processRequest();
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<meta name="Description" content="" />
<?php
$xajax->printJavascript('../xajax');
?>
</head>
<body>
<form name="form" id="form" enctype="multipart/form-data" action="">
<?php 
echo '<label>Title</label><input type="text" name="title" id="title"><br />';
require_once('fckeditor/fckeditor.php');
$editor = new FCKeditor('editor1') ;
$editor->BasePath = 'fckeditor/' ;
$editor->ToolbarSet	= 'Basic' ;
$editor->Value		= '' ;
$editor->Create() ;
?>
<input type="button" name="grabar" onclick="SaveData();" value="Grabar Datos">
</form>

<div id="autosavemsg"></div>

<script language="Javascript">
function SaveData() {
  var oEditor = FCKeditorAPI.GetInstance('editor1');
  xajax.$('editor1').value = oEditor.GetXHTML(true);
  xajax_savetodb(xajax.getFormValues('form'));
}
</script>
<script language="Javascript">
//Interval var AutoSaveTime=20000;
//time object
var AutoSaveTimer;
SetAutoSave();
function SetAutoSave() {
  AutoSaveTimer=setInterval("SaveData();",AutoSaveTime);
} 
</script>
</body>
</html>