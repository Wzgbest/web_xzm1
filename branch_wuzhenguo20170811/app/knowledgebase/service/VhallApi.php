<?php 
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------

namespace app\knowledgebase\service;

use app\knowledgebase\model\LiveShow;
// use app\common\model\Structure;

class VhallApi
{	
	private $base_url = 'http://e.vhall.com/api/vhallapi/v2/';
	private $secret_key = "84cf773a64e7f188be20b5e4064fff43";
	private $app_key = "04b23a05121ae10e4cb826c65a24babf";
	private $common_param = array();

	public function __construct()
    {
        if (empty($this->common_param)) {
            $this->common_param = array(
            	'auth_type'=>2,
            	'app_key'=>$this->app_key,
            	'signed_at'=>time(),
            	);
        }
    }

	public function index(){

	}

	/**
	 * 创建活动
	 * @param  [type]  $cropid             [description]
	 * @param  [type]  $subject            活动主题
	 * @param  [type]  $start_time         活动开始时间
	 * @param  [type]  $introduction       [description]
	 * @param  [type]  $user_id            [description]
	 * @param  integer $use_global_k       [description]
	 * @param  integer $exist_3rd_auth     [description]
	 * @param  [type]  $auth_url           [description]
	 * @param  [type]  $failure_url        [description]
	 * @param  [type]  $topics             [description]
	 * @param  [type]  $layout             [description]
	 * @param  [type]  $type               [description]
	 * @param  integer $auto_record        [description]
	 * @param  integer $is_chat            [description]
	 * @param  [type]  $host               [description]
	 * @param  integer $buffer             [description]
	 * @param  integer $is_allow_extension [description]
	 * @return [type]                      [description]
	 * subject	string	是	<50个字符,活动主题
		start_time	int	是	Linux时间戳,活动开始时间
		user_id	int	否	通过第三方创建用户接口获取的微吼用户ID，子账号创建活动时此参数必填，管理员账号创建活动时忽略此参数
		use_global_k	int	否	默认为0不开启，1为开启,是否针对此活动开启全局K值配置
		exist_3rd_auth	int	否	默认为0不开启，1为开启,是否开启第三方K值验证查看说明
		auth_url	string	否	http://domain,<256个字符,第三方K值验证接口URL(exist_3rd_auth为1必填)
		failure_url	string	否	http://domain,<256个字符,第三方K值验证失败跳转URL(可选)
		introduction	string	否	<1024个字符,活动描述
		topics	string	否	直播话题标签字段,以”,”(半角符号) 分割可以多个,标签最多为6个,单个标签不超过8个字 格式例: “商务,教育,视频教育”
		layout	int	否	1为单视频,2为单文档,3为文档+视频,观看布局
		type	int	否	0为公开,1为非公开,个人公开/非公开活动
		auto_record	int	否	0为否,1为是(默认为否),是否自动回放
		is_chat	int	否	0为是,1为否(默认为是),是否开启聊天
		host	string	否	<50个字符,可为空,主持人姓名
		buffer	int	否	>0的数字,可为空,直播延时，单位为秒，默认为3
		is_allow_extension	int	否	默认为1表示开启并发扩展包，传其他参数表示不开启，流量套餐或没有并发扩展包时忽略此参数
	 */
	public function createActivity($cropid,$subject,$start_time,$introduction=null,$user_id=null,$use_global_k=0,$exist_3rd_auth=0,$auth_url='',$failure_url='',$topics='',$layout='',$type='',$auto_record=0,$is_chat=0,$host='',$buffer=3,$is_allow_extension=0){
		$info = [
		'status'=>0,
		'message'=>"创建活动失败",
		];
		if (!$cropid || !$subject || !$start_time) {
			$info['error'] = "活动设置参数存在错误";
			return $info;
		}
		if ($exist_3rd_auth == 1 && !$auth_url) {
			$info['error'] = "设置了k值验证，请填写验证地址";
			return $info;
		}
		$url = $this->base_url."webinar/create";
		$data['subject'] = $subject;
		$data['start_time'] = $start_time;
		if ($user_id) {
			$data['user_id'] = $user_id;
		}
		if ($use_global_k) {
			$data['use_global_k'] = $use_global_k;
		}
		if ($exist_3rd_auth) {
			$data['exist_3rd_auth'] = $exist_3rd_auth;
		}
		if ($auth_url) {
			$data['auth_url'] = $auth_url;
		}
		if ($failure_url) {
			$data['failure_url'] = $failure_url;
		}
		if ($introduction) {
			$data['introduction'] = $introduction;
		}
		if ($topics) {
			$data['topics'] = $topics;
		}
		if ($layout) {
			$data['layout'] = $layout;
		}
		if ($type) {
			$data['type'] = $type;
		}
		if ($auto_record) {
			$data['auto_record'] = $auto_record;
		}
		if ($is_chat) {
			$data['is_chat'] = $is_chat;
		}
		if ($host) {
			$data['host'] = $host;
		}
		if ($buffer) {
			$data['buffer'] = $buffer;
		}
		if ($is_allow_extension) {
			$data['is_allow_extension'] = $is_allow_extension;
		}

		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);
		if ($j_data['code'] == 200) {
			$flg = $this->addOneActive($cropid,$j_data['data'],$data['subject'],$introduction);
			if ($flg) {
				$info['status'] = 1;
				$info['message'] = "创建活动成功";
				$info['data'] = $j_data['data'];
			}else{
				$info['error'] = "插入数据表失败";
			}
			
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
		
	}

	//插入一条直播数据
	private function addOneActive($cropid,$show_num,$show_title,$show_intro){
		$liveShowM = new LiveShow($cropid);
		$data['show_num'] = $show_num;
		$data['show_title'] = $show_title;
		if ($show_intro) {
			$data['show_intro'] = $show_intro;
		}
		$data['create_time'] = time();
		$flg = $liveShowM->addActivity($data);
		return $flg;
	}

	/**
	 * 获取发起直播的页面
	 * @param  sting  $webinar_id  活动id
	 * @param  int $is_sec_auth 是否开启地址安全验证，0位不开启，1位开启，默认为0，开启后获取的地址仅单次有效，离开地址再次进入需重新获取
	 * @return [type]               [description]
	 */
	public function startActivity($webinar_id,$is_sec_auth=0){
		$info = ['status'=>0,'message'=>"发起直播失败"];

		$url = $this->base_url."webinar/start";
		if (!$webinar_id) {
			$info['error'] = "请填写活动id";
			return $info;
		}
		$data['webinar_id'] = $webinar_id;
		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);
		if ($j_data['code'] == 200) {
			$info['status'] = 1;
			$info['message'] = "获取活动页面成功";
			$info['data'] = $j_data['data'];
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}

	/**
	 * 获取活动列表
	 * @param  [int] $user_id 子账号对应的微吼用户user_id（该参数仅在需要获取单个子账号下的活动时传入，且type需传1，传2、3无效）
	 * @param  [int] $type    1为所请求账号下的全部活动，2为所请求账号的子账号下的全部活动，3为所请求账号及其子账号下的全部活动
	 * @param  [int] $pos     数字,设置从第几条数据开始获取，如果是第一条数据（pos=0）
	 * @param  [int] $limit   数字,每次返回条数
	 * @param  [int] $status  1:直播进行中,2:预约中,3:结束，4：点播，5：结束且有自动回放 不传递此参数则为所有活动,（如需组合查询,可将该值写成json字符串的形式。如status为[1,2](注意，4，5 不能使用数组形式)代表同时获取活动状态,活动状态
	 * @return [type]          [description]
	 */
	public function getActivityList($user_id=null,$type=null,$pos=null,$limit=null,$status=null){
		$info = ['status'=>0,'message'=>"获取直播列表失败"];
		if ($user_id && $type != 1) {
			$info['error'] = "活动类型选择错误";
			return $info;
		}
		$url = $this->base_url."webinar/list";
		$data = [];
		if ($user_id > 0) {
			$data['user_id']  =$user_id;
		}
		if ($type > 0) {
			$data['type'] = $type;
		}
		if ($pos != null) {
			$data['pos'] = $pos;
		}
		if ($limit > 0) {
			$data['limit'] = $limit;
		}
		if ($status > 0) {
			$data['status'] = $status;
		}

		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);

		if ($j_data['code'] == 200) {
			$info['status'] = 1;
			$info['message'] = "获取列表成功";
			$info['data'] = $j_data['data'];
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;

	}

	/**
	 * 跟新活动信息
	 * @param  [type] $params 
	 * webinar_id	int	是	活动ID,9位数字
subject	string	否	活动主题,<50个字符
start_time	int	否	活动开始时间,Linux时间戳
use_global_k	int	否	默认为0不开启，1为开启,是否针对此活动开启全局K值配置
exist_3rd_auth	int	否	是否开启第三方K值验证查看说明,默认为0不开启，1为开启
auth_url	string	否	
introduction	string	否	活动描述,<1024个字符
topics	string	否	直播话题标签字段,以”,”(半角符号) 分割可以多个,标签最多为6个,单个标签不超过8个字 格式例: “商务,教育,视频教育”
layout	int	否	观看布局,1为单视频,2为单文档,3为文档+视频
is_open	int	否	活动公开状态,0为公开,1为非公开
auto_record	int	否	是否自动回放,0为否,1为是
is_chat	int	否	是否开启聊天,0为是,1为否
host	string	否	主持人姓名,<50个字符,可为空
buffer	int	否	直播延时,>0的数字,可为空
	 * @return [type]         [description]
	 */			
	public function updateActivity($cropid,$params=[]){
		$info = ['status'=>0,'message'=>"更新信息失败"];
		if (empty($params)) {
			$info['error'] = "请更新正确的信息";
			return $info;
		}
		$url = $this->base_url."webinar/update";
		$send_data = http_build_query($this->getSendData($params));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);
		if ($j_data['code'] == 200) {
			if (isset($params['subject']) || isset($params['introduction'])) {
				if (isset($params['subject'])) {
					$data['show_title'] = $params['subject'];
				}
				if (isset($params['introduction'])) {
					$data['show_intro'] = $params['introduction'];
				}
				$flg = $this->updateOneActive($cropid,$params['webinar_id'],$data);
				if ($flg) {
					$info['status'] = 1;
					$info['message'] = "跟新成功";
					$info['data'] = $j_data['data'];
				}else{
					$info['error'] = "跟新数据表失败";
				}
			}else{
				$info['status'] = 1;
				$info['message'] = "跟新成功";
				$info['data'] = $j_data['data'];
			}
			
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}
	//跟新数据表活动信息
	private function updateOneActive($cropid,$webinar_id,$data){
		$liveShowM = new LiveShow($cropid);
		$flg = $liveShowM->updateActivity($webinar_id,$data);
		return $flg;
	}

	/**
	 * 停止活动
	 * @param  int $webinar_id 活动id
	 * @return [type]             [description]
	 */
	public function stopAcvitvity($webinar_id){
		$info = ['status'=>0,'message'=>"结束活动失败"];

		if (!$webinar_id) {
			$info['error'] = "请传入正确的活动id";
			return $info;
		}
		$url = $this->base_url."webinar/stop";
		$data['webinar_id'] = $webinar_id;

		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);

		if ($j_data['code'] == 200) {
			$info['status'] = 1;
			$info['message'] = "停止成功";
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}

	/**
	 * 删除活动
	 * @param  int $webinar_id 活动id
	 * @return [type]             [description]
	 */
	public function delAcvitvity($cropid,$webinar_id){
		$info = ['status'=>0,'message'=>"删除活动失败"];

		if (!$webinar_id) {
			$info['error'] = "请传入正确的活动id";
			return $info;
		}
		$url = $this->base_url."webinar/delete ";
		$data['webinar_id'] = $webinar_id;

		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);

		if ($j_data['code'] == 200) {
			$flg = $this->delOneActivity($cropid,$webinar_id);
			if ($flg) {
				$info['status'] = 1;
				$info['message'] = "删除成功";
				$info['data'] = $j_data['data'];
			}else{
				$info['error'] = "删除数据表失败,请联系管理员";
			}
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}
	//删除直播信息数据表
	private function delOneActivity($cropid,$webinar_id){
		$liveShowM = new LiveShow($cropid);
		$flg = $liveShowM->delActive($webinar_id);
		return $flg;
	}

	/**
	 * 设置封面==============================待验证
	 * @param [type] $webinar_id [description]
	 */
	public function setActivewImage($webinar_id,$image){
		$info = ['status'=>0,'message'=>"上传封面失败"];

		if (!$webinar_id || !$image) {
			$info['error'] = "传入的数据出现错误";
			return $info;
		}

		$url = $this->base_url."webinar/activeimage";
		$data['webinar_id'] = $webinar_id;
		$data['image'] = $image;
		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);

		if ($j_data['code'] == 200) {
			// $flg = $this->updateOneActive($webinar_id,)
			$info['status'] = 1;
			$info['message'] = "上传封面成功";
			$info['data'] = $j_data['data'];
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}

	/**
	 * 添加嘉宾/助理权限接口
	 * @param [type] $webinar_id 9位数字,活动ID
	 * @param [type] $users      用户ID,多个用户ID,已英文逗号分割，一次最多条目为200
	 * @param [int] $role_name  用户角色 1 助理 2嘉宾
	 */
	public function addAuthor($webinar_id,$users,$role_name){
		$info = ['status'=>0,'message'=>"设置嘉宾/助理失败"];

		if (!$webinar_id || !$users || !$role_name) {
			$info['error'] = "传入正确的参数";
			return $info;
		}

		$url = $this->base_url."guest/add-authorization";
		$data['webinar_id'] = $webinar_id;
		$data['users'] = $users;
		$data['role_name'] = $role_name;
		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);

		if ($j_data['code'] == 200) {
			$info['status'] = 1;
			$info['message'] = "设置成功";
			// $info['']
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}

	/**
	 * 取消嘉宾/助理权限接口
	 * @param  [int] $webinar_id 9位数字,活动ID
	 * @param  [string] $email      <60个字符，邮箱格式,注意正确填写为设置“嘉宾/助理”的邮箱
	 * @param  [int] $user_id    用户ID
	 * @return [type]             [description]
	 */
	public function cancelAuth($webinar_id,$email='',$user_id=''){
		$info = ['status'=>0,'message'=>"取消失败"];

		if (!$webinar_id || (!$email && !$user_id)) {
			$info['error'] = "参数错误";
			return $info;
		}

		$url = $this->base_url."guest/cancel-url";
		$data['webinar_id'] = $webinar_id;
		if ($email) {
			$data['email'] = $email;
		}
		if ($user_id) {
			$data['user_id'] = $user_id;
		}
		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data);

		if ($j_data['code'] == 200) {
			$info['status'] = 1;
			$info['message'] = "取消成功";
			$info['data'] = $j_data['data'];
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}


	//====================================
	//====================================
	//用户管理
	
	public function addUser($third_user_id,$pass=87654321,$phone='',$name='',$email='',$head='',$customized_field='',$customized_value=''){
		$info = ['status'=>0,'message'=>"添加用户失败"];

		if (!$third_user_id || !$pass) {
			$info['error'] = "账号或者密码不能为空";
			return $info;
		}

		$url = $this->base_url."user/register";
		$data['third_user_id'] = $third_user_id;
		$data['pass'] = $pass;
		if ($phone) {
			$data['phone'] = $phone;
		}
		if ($name) {
			$data['name'] = $name;
		}
		if ($email) {
			$data['email'] = $email;
		}
		if ($head) {
			$data['head'] = $head;
		}
		if ($customized_field) {
			$data['customized_field'] = $customized_field;
		}
		if ($customized_value) {
			$data['customized_value'] = $customized_value;
		}

		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);

		if ($j_data['code'] == 200) {
			$info['status'] = 1;
			$info['message'] = "添加成功";
			$info['data'] = $j_data['data'];
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}




	/**
	 * 全局配置第三方K值验证URL
	 * @param  [type]  $exist_3rd_auth 默认为0不开启，1为开启,是否开启第三方K值验证查看说明
	 * @param  [type]  $auth_url       http://domain,<256个字符,第三方K值验证接口URL(exist_3rd_auth为1必填)
	 * @param  [type]  $failure_url   http://domain,<256个字符,第三方K值验证失败跳转URL(可选)
	 * @param  integer $cover_chil     是否覆盖子账号，1为覆盖，0为不覆盖，默认为0
	 * @return [type]                  [description]
	 */
	public function authK($exist_3rd_auth,$auth_url,$failure_url=null,$cover_chil=0){
		$info = ['status'=>0,'message'=>"配置失败"];

		$url = $this->base_url."webinar/whole-auth-url";
		$data = [];
		if (!$exist_3rd_auth) {
			$data['exist_3rd_auth'] = $exist_3rd_auth;
		}
		if (!$auth_url) {
			$data['auth_url'] = $auth_url;
		}
		if (!$failure_url) {
			$data['failure_url'] = $failure_url;
		}
		if (!$cover_chil) {
			$data['cover_chil'] = $cover_chil;
		}

		$send_data = http_build_query($this->getSendData($data));
		$get_data = $this->getData($url,$send_data);
		$j_data = json_decode($get_data,true);

		if ($j_data['code'] == 200) {
			$info['status'] = 1;
			$info['message'] = "设置成功";
		}else{
			$info['error'] = $j_data['msg'];
		}

		return $info;
	}

	/**
	 * 处理发送的数据sign
	 * @param  arrty $params 除sign之外的参数
	 * @return array         所有请求参数
	 */
	private function getSendData($params){
		if (empty($params)) {
			$params = $this->common_param;
		}else{
			//请求参数和公共参数合并 除 sign
			$params = array_merge($this->common_param,$params);
		}
		$data = $params;
		// 按参数名升序排列
		ksort($params);
		
		// 将键值组合
		array_walk($params,function(&$value,$key){
			$value = $key . $value;
		});
		 
		// 拼接,在首尾各加上$secret_key,计算MD5值
		$sign = md5($this->secret_key . implode('',$params) . $this->secret_key);
		//将处理后的sign添加到参数中
		$data['sign'] = $sign;
		// var_dump($data);die();
		return $data;
	}

	/**
	 * vhall请求数据格式
	 * @param  string $url  请求地址
	 * @param  json $data 请求参数
	 * @return json       返回数据
	 */
	private function getData($url,$data){
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;//操作成功，返回{'code':200,'data':'123456789'}
	}

	/**
	 * 获取错误信息
	 */
	// private function getErrorMessage($code){
	// 	$error = '';
 //        switch ($code) {
 //            case 10000:
 //                $error = '账号或密码为空';
 //                break;
 //            case 10001:
 //                $error = '账号或密码错误';
 //                break;
 //            case 10002:
 //                $error = 'Vhall正在审核API接入权限，接口暂不可用';
 //                break;
 //            case 10010:
 //                $error = '无此活动信息';
 //                break;
 //            case 10011:
 //                $error = '活动信息与用户信息不匹配';
 //                break;
 //            case 10012:
 //                $error = '必填字段缺失';
 //                break;
 //            case 10013:
 //                $error = '开始时间晚于结束时间';
 //                break;
 //            case 10014:
 //                $error = '主题为空或者超过50个字符';
 //                break;
 //            case 10015:
 //                $error = '公共密码格式错误，6-20位英文字母、数字或组合';
 //                break;
 //            case 10016:
 //                $error = '存在第三方K值验证，但是接口地址auth_url为空';
 //                break;
 //            case 10017:
 //                $error = '没有活动ID';
 //                break;
 //            case 10018:
 //                $error = '没有相关权限';
 //                break;
 //            case 10019:
 //                $error = '没有相关信息';
 //                break;
 //            case 10020:
 //                $error = '活动组织者不能以嘉宾身份进入';
 //                break;
 //            case 10021:
 //                $error = '邮箱格式不正确';
 //                break;
 //            case 10022:
 //                $error = '姓名超过30个字符';
 //                break;
 //            case 10023:
 //                $error = '没有录播ID';
 //                break;
 //            case 10024:
 //                $error = '活动状态不是进行中';
 //                break;
 //            case 10025:
 //                $error = '结束失败，稍候重试';
 //                break;
 //            case 10026:
 //                $error = '数据格式错误';
 //                break;
 //            case 10027:
 //                $error = '没有问卷ID';
 //                break;
 //            case 10028:
 //                $error = '活动进行中不能获取';
 //                break;
 //            case 10101:
 //                $error = '获取条目不能超过1000';
 //                break;
 //            case 10500:
 //                $error = '内部错误，稍候重试';
 //                break;
 //            case 10059:
 //                $error = '活动标题不能超过30个字符';
 //                break;
 //            case 10053:
 //                $error = '回放设置参数错误';
 //                break;
 //            case 10054:
 //                $error = '聊天设置参数错误';
 //                break;
 //            case 10056:
 //                $error = 'buffer设置不能小于0';
 //                break;
 //            case 10003:
 //                $error = '没有可用的扩展包';
 //                break;
 //            case 10103:
 //                $error = '第三方用户对象不存在';
 //                break;
 //            case 10104:
 //                $error = '子账号信息不存在';
 //                break;
 //            case 12100:
 //                $error = '单个话题标签不能超过8个字';
 //                break;
 //            case 12101:
 //                $error = '最多只可添加6个话题标签';
 //                break;        
 //            default:
 //                $error = '请查看开发文档';
 //        }
 //        return $error;
 //    }
}

?>