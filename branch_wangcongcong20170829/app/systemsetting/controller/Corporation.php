<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Corporation as CorporationModel;
use app\common\model\Business;
use think\Request;

class Corporation extends Initialize
{
    public function index()
    {
        return view();
    }

    /**
     * 获取公司信息并绑定到模板
     * @return array
     * created by blu10ph
     */
    protected function _showCorpInfo(){
        $data = CorporationModel::getCorporation($this->corp_id);
        $business = new Business($this->corp_id);
        $business_list = $business->getAllBusiness();
        $corpInfo = ['business_list'=>$business_list,'data'=>$data];
        $this->assign($corpInfo);
        return $corpInfo;
    }

    /**
     * 显示公司信息
     * @return mixed
     * created by blu10ph
     */
    public function showCorpInfo(){
        $this->_showCorpInfo();
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }

    /**
     * 修改公司信息页面
     * @return mixed
     * created by messhair
     */
    public function editCorpInfo(){
        if(!($this->checkRule('systemsetting/corporation/showcorpinfo/edit'))){
            $this->noRole(2);
        }
        $this->_showCorpInfo();
        return view();
    }

    /**
     * 修改公司信息
     * @return mixed
     * created by blu10ph
     */
    public function updateCorpInfo(Request $request){
        $info = ['status'=>0,"message"=>"修改公司信息时发生错误!"];
        $input = $request->param();

        if (
            empty($this->corp_id)
            || empty($input['corp_name'])
            ||empty($input['corp_tel'])
            || empty($input['corp_field'])
        ) {
            $info['message'] = '参数错误!';
            return $info;
        }

        $data = [
            'corp_name' => $input['corp_name'],
            'corp_tel' => $input['corp_tel'],
            'corp_website' => $input['corp_web'],
            'corp_address' => $input['corp_addr'],
            'corp_dist' => $input['corp_dist'],
            'corp_lng' => $input['corp_lng'],
            'corp_lat' => $input['corp_lat'],
            'corp_field' => $input['corp_field'],
            'corp_product_keys' => $input['corp_product_keys'],
        ];

        $corpM = new CorporationModel($this->corp_id);
        $res = $corpM->setCorporationInfo($this->corp_id,$data);
        if ($res >= 0) {
            $info['status'] = 1;
            $info['message'] = '修改公司信息成功';
        } else {
            $info['message'] = '修改公司信息失败';
        }
        return $info;
    }

    /**
     * 修改公司百度地图坐标
     * @return mixed
     * created by messhair
     */
    public function baiduLbs()
    {
        $input = input('param.');
        $info['status'] = false;
        if (empty($input['corp_lat']) || empty($input['corp_lng'])) {
            $info['message'] = '定位信息不能为空';
            return $info;
        }

        $data = [
            'corp_lat' => $input['corp_lat'],
            'corp_lng' => $input['corp_lng'],
        ];

        $corpM = new CorporationModel($this->corp_id);
        $res = $corpM->setCorporationInfo($this->corp_id,$data);
        if ($res >= 0) {
            $info['message'] = '修改定位信息成功';
            $info['status'] = true;
        } else {
            $info['message'] = '修改定位信息失败';
        }
        return $info;
    }
}
