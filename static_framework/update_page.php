<?PHP
$server = "http://xzm.blu10ph.cn";
$cookie_file = './cookie.txt';
$page_list = [];
$page_list[] = ["crm","customer","add_page",""];
$page_list[] = ["crm","customer","customer_pool",""];
$page_list[] = ["crm","customer","my_customer",""];
$page_list[] = ["crm","customer","general","id/11/fr/my_customer"];
$page_list[] = ["crm","customer","show","id/11/fr/my_customer"];
$page_list[] = ["crm","customer","edit","id/11/fr/my_customer"];
$page_list[] = ["crm","customer_contact","show","customer_id/11/fr/my_customer"];
$page_list[] = ["crm","customer_contact","add_page","customer_id/11/fr/my_customer"];
$page_list[] = ["crm","customer_contact","edit_page","id/1/fr/my_customer"];
$page_list[] = ["crm","sale_chance","show","customer_id/11/fr/my_customer"];
$page_list[] = ["crm","customer_trace","show","customer_id/11/fr/my_customer"];
$page_list[] = ["crm","customer","public_customer_pool",""];
$page_list[] = ["crm","customer","customer_pool",""];
$page_list[] = ["crm","customer_import","index","type/3"];
$page_list[] = ["systemsetting","corporation","showcorpinfo",""];
$page_list[] = ["systemsetting","corporation","editcorpinfo",""];
$page_list[] = ["systemsetting","customer","index",""];
$page_list[] = ["systemsetting","customer","add_page",""];
$page_list[] = ["systemsetting","customer","edit_page","id/7"];
$page_list[] = ["systemsetting","employee","manage",""];
$page_list[] = ["systemsetting","employee","show","id/4"];
$page_list[] = ["systemsetting","employee","edit","id/4"];
$page_list[] = ["systemsetting","employee_import","index",""];
$page_list[] = ["systemsetting","role","index",""];
$page_list[] = ["systemsetting","role","rule_manage","id/1"];
$page_list[] = ["systemsetting","role","employee_list","id/1"];
$page_list[] = ["systemsetting","role","employee_show","id/1"];
$page_list[] = ["systemsetting","role","not_role_employee_list","id/1"];
$page_list[] = ["systemsetting","structure","index",""];
$page_list[] = ["systemsetting","structure","employee_list","id/1"];
$page_list[] = ["systemsetting","structure","not_struct_employee_list","id/1"];

login($server,$cookie_file);
foreach($page_list as $page){
    //echo var_export($page, true)."\r\n";
    downPageToPath($server,$page,$cookie_file);
}

function login($server,$cookie_file){
    $url = $server.'/login/index/verifylogin';
    $data = 'telephone=13311112222&password=87654321';
    //echo var_export($url, true)."\r\n";
    $curl=curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1 );
    curl_setopt($curl, CURLOPT_HEADER, 0 );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file);//把返回来的cookie信息保存在$cookie_jar文件中
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data );
    $return = curl_exec ( $curl );
    curl_close ( $curl );
    //echo var_export($return, true)."\r\n";
}
function downPageToPath($server,$page,$cookie_file){
    $url = $server.getPageUrl($page);
    $page_html = curl_get($url,$cookie_file);
    $file_path = getFilePath($page);
    echo var_export($url, true)."\r\n";
    //echo var_export($file_path, true)."\r\n";
    //echo var_export($page_html, true)."\r\n";
    file_write($file_path,$page_html);
}
function getPageUrl($page){
    return ("/".$page[0]."/".$page[1]."/".strtolower($page[2])."/".$page[3]);
}
function getFilePath($page){
    return ("./".$page[0]."/".$page[1]."/".strtolower($page[2]).".html");
}

/**
 *curl post请求
 */
function curl_post($url,$data,$cookie_file){
    $curl=curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);                
    curl_setopt($curl, CURLOPT_POST, 1 );
    curl_setopt($curl, CURLOPT_HEADER, 0 );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data );
    $return = curl_exec ( $curl );
    curl_close ( $curl );
    return $return;
}

/**
 *curl get请求
 */
function curl_get($url,$cookie_file){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);//登陆后要从哪个页面获取信息
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
    $return = curl_exec($curl);
    curl_close($curl);
    return $return;
}

function file_write($file_path,$page_html){
    $save_path = dirname($file_path);
    if (!is_dir($save_path)) {
        mkdirs($save_path);
    }
    $file = fopen($file_path, "w") or die("Unable to open file!");
    fwrite($file, $page_html);
    fclose($file);
}


/**
 * 创建多级目录
 * @param $dir string 要创建的路径,可多级
 * @return boolean
 * created by blu10ph
 */
function mkdirs($dir,$mode=0755) {
    if(!is_dir($dir)) {
        if (!mkdirs(dirname($dir))){
            return false;
        }
        if(!mkdir($dir,$mode)){
            return false;
        }
    }
    return true;
}

?>
