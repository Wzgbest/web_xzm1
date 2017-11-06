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



	//k值验证
	public function confirm_k(){
		$success = "pass";
		$fail = "fail";
		$email = input('email','','string');
		$k = input('k','','string');


		return $success;
	}

	//测试方法
	public function test(){
		$vhallApi = new VhallApi();

		$info = $vhallApi->getActivityList();
		return $info;
	}
}