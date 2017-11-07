<?php
namespace app\knowledgebase\controller;

use app\common\controller\Initialize;
use app\knowledgebase\service\VhallApi as VhallApi;
class LiveShow extends Initialize
{
	public function index(){
		$vhallApi = new VhallApi();

		$act_list = $vhallApi->getActivityList();
		if (!empty($act_list['data']['lists'])) {
			foreach ($act_list['data']['lists'] as &$value) {
				$value['url'] = "http://e.vhall.com/webinar/inituser/".$value['webinar_id'];
			}
			
			$this->assign('list',$act_list['data']);
		}
		// var_dump($act_list['data']['lists']);die();
		
		return view();
	}

	//web获取直播的页面
	public function start_tv(){
		$id = input('id',0,'int');

		$vhallApi = new VhallApi();
		$info = $vhallApi->startActivity($id);
		return $info;
	}





	//k值验证
	public function confirm_k(){
		$success = "pass";
		$fail = "fail";
		$email = input('email','','string');
		$k = input('k','','string');

		if ($k == $_SESSION['k']) {
			return $success;
		}

		return $fail;
	}






	//测试方法
	public function test(){
		$vhallApi = new VhallApi();

		$info = $vhallApi->startActivity("484061604");
		return $info;
	}
}