<?php
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Corporation as CorporationModel;
use think\Request;

class Corporation extends Initialize
{
    public function index()
    {
    }

    /**
     * 修改公司信息
     * @return mixed
     */
    public function editCorpInfo(Request $request)
    {
        if ($request->isGet()) {
            $corpM = new CorporationModel();
            $data = $corpM->getCorporation($this->corp_id);
            return $data;
        } elseif ($request->isPost()) {
            $info['status'] = false;
            if (empty($this->corp_id)) {
                $info['message'] = '访问有误';
                return $info;
            }
            $input = $request->param();

            if (empty($input['corp_name']) ||empty($input['corp_tel']) || empty($input['corp_field'])) {
                $info['message'] = '缺少必填信息';
                return $info;
            }

            $data = [
                'corp_name' => $input['corp_name'],
                'corp_tel' => $input['corp_tel'],
                'corp_website' => $input['corp_web'],
                'corp_address' => $input['corp_addr'],
                'corp_dist' => $input['corp_dist'],
                'corp_field' => $input['corp_field'],
                'corp_product_keys' => $input['corp_product_keys'],
            ];

            $corpM = new CorporationModel();
            $res = $corpM->setCorporationInfo($this->corp_id,$data);
            if ($res >= 0) {
                $info['status'] = true;
                $info['message'] = '修改公司信息成功';
            } else {
                $info['message'] = '修改公司信息失败';
            }
            return $info;
        }
    }

    /**
     * 修改公司百度地图坐标
     * @return mixed
     */
    public function baiduLbs()
    {
        $input = input('param.');
        $info['status'] = false;
        if (empty($input['corp_location'])) {
            $info['message'] = '定位信息不能为空';
            return $info;
        }

        $data = [
            'corp_location' => $input['corp_location'],
        ];

        $corpM = new CorporationModel();
        $res = $corpM->setCorporationInfo($corp_id,$data);
        if ($res >= 0) {
            $info['message'] = '修改定位信息成功';
            $info['status'] = true;
        } else {
            $info['message'] = '修改定位信息失败';
        }
        return $info;
    }
}