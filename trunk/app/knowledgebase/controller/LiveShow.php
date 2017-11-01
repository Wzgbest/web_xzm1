<?php
namespace app\knowledgebase\controller;

use app\common\controller\Initialize;
use app\knowledgebase\service\VhallApi as VhallApi;
class LiveShow extends Initialize
{
	public function index(){
		$vhallApi = new VhallApi();

		$act_list = $vhallApi->getActivityList();
		
		foreach ($act_list['data']['lists'] as &$value) {
			$value['url'] = "http://e.vhall.com/webinar/inituser/".$value['webinar_id'];
		}
		// var_dump($act_list['data']['lists']);die();
		$this->assign('list',$act_list['data']);
		return view();
	}

	public function test(){
		$vhallApi = new VhallApi();

		$info = $vhallApi->startActivity("484061604");
		return $info;
	}
}