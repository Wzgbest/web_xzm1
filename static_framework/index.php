<?PHP
$request_uri_array = explode("/",$_SERVER["REQUEST_URI"]);
$file_url = "";
if($_SERVER["REQUEST_URI"]=="/"){
	$request_uri_array = ["","index","index","index"];
}
if(count($request_uri_array)>=4){
	session_start();
	$file_url = "./".$request_uri_array[1]."/".$request_uri_array[2];
	$file_array = explode(",",$request_uri_array[3]);
	if(count($file_array)==2 && $file_array[1]=='html'){
		$file_url .= "/".$request_uri_array[3];
	}else{
		$file_url .= "/".$request_uri_array[3].".html";
	}
	$file_url = strtolower($file_url);
	if($file_url=="./login/index/verifylogin.html"){
		$_SESSION['login_flg']=1;
		echo '<script language="javascript">top.location="/";</script>';
		exit;
	}
	if(!isset($_SESSION['login_flg'])){
		echo file_get_contents("./login/index/index.html");
		exit;
	}
	if(file_exists($file_url)){
		echo file_get_contents($file_url);
		exit;
	}else{
		echo '{"status":1,"info":"操作成功！","message":"操作成功！"}';
		exit;
	}
}else{
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	echo '{"status":0,"info":"未找到页面!","message":"未找到页面!"}';
	exit;
}
?>
