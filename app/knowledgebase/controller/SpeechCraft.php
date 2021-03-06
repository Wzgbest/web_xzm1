<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\knowledgebase\controller;

use app\common\controller\Initialize;
use app\knowledgebase\model\SpeechCraft as SpeechCraftModel;
use app\knowledgebase\model\CorporationShare as CorporationShareModel;
use app\knowledgebase\model\CorporationShareContent;

class SpeechCraft extends Initialize{
    protected $_speechCraftModel = null;
    protected $handle_max = 6;
    public function __construct(){
        parent::__construct();
        $this->_speechCraftModel = new SpeechCraftModel($this->corp_id);
    }
    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
   
        $key_word = input('key_word','','string');
        $class_id = input('class_id',0,'int');

        $userinfo = get_userinfo();
        $uid = $userinfo['userid'];
        $is_leader = $userinfo['userinfo']['is_leader'];
        $article_type = $this->_speechCraftModel->getAllArticleType();
        $all_article = $this->_speechCraftModel->getAllArticle($key_word,$class_id,1,100);
        // var_dump($userinfo);die();
        $this->assign('article_type',$article_type);
        $this->assign('all_article',$all_article);
        $this->assign('class_id',$class_id);
        $this->assign('uid',$uid);
        $this->assign('is_leader',$is_leader);
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }

    /*
    添加新文章
     */
    public function add_article(){
        if(!($this->checkRule('knowledgebase/speech_craft/index/new-speechcraft'))){
            $this->noRole(2);
        }

        $id = input('id',0,'int');
        // var_dump($id);die();
        if ($id) {
            $article_info = $this->_speechCraftModel->getOneArticleById($id);
            $article_type = $this->_speechCraftModel->getAllArticleType();
            $this->assign('article_type',$article_type);
            $this->assign('article',$article_info);
            return view('edit');
        }else{
            $article_type = $this->_speechCraftModel->getAllArticleType();
            $this->assign('article_type',$article_type);
            return view('new');
        }
    }

    public function add_class_page(){
        return view('add_class');
    }

    public function get_article_type(){
        $result = ['status'=>0,'info'=>"获取文章分类失败!"];
        $article_type = $this->_speechCraftModel->getAllArticleType();
        $result['data'] =$article_type;
        $result['status'] = 1;
        $result['info'] = "获取文章分类成功!";
        return json($result);
    }

    /*
    添加文章
     */
    public function addArticle(){
        $result = ['status'=>0,'info'=>"添加文章失败!"];

        $userinfo = get_userinfo();
        $uid = $userinfo['userid'];
        $data = [];
        $data['article_name'] = input('article_name','',"string");
        $data['article_type'] = input('article_type',1,"int");
        $data['article_class'] = input('article_class',0,"int");
        $data['article_content'] = input('article_content','',"string");
        $data['article_url'] = input('article_url','',"string");
        $data['article_text'] = input('article_text');
        $data['article_is_top'] = input('article_is_top',0,"int");
        $data['article_start_top_time'] = input('article_start_top_time','',"string");
        $data['article_end_top_time'] = input('article_end_top_time','',"string");
        $data['article_start_show_time'] = input('article_start_show_time','',"string");
        $data['article_end_show_time'] = input('article_end_show_time','',"string");
        $data['article_release_type'] = input('article_release_type',0,"int");
        $data['article_release_time'] = input('article_release_time','',"string");
        $data['article_creat_time'] = time();
        $data['article_edit_time'] = time();
        $data['add_user'] = $uid;
        // var_dump(input('post.'));
        // var_dump($data);die();
        if (empty($data['article_name'])) {
            $result['info'] = "文章名不能为空!";
            return json($result);
        }
        if (empty($data['article_class'])) {
            $result['info'] = "文章分类不能为空!";
            return json($result);
        }
        if (empty($data['article_content'])) {
            $result['info'] = "文章简介不能为空!";
            return json($result);
        }
        if ($data['article_type'] == 1) {
            if (empty($data['article_text'])) {
                $result['info'] = "文章正文不能为空!";
                return json($result);
            }
        }else{
            if (empty($data['article_url'])) {
                $result['info'] = "文章链接不能为空!";
                return json($result);
            }
        }
        if ($data['article_is_top']) {
            if (empty($data['article_start_top_time']) || empty($data['article_end_top_time'])) {
                $result['info'] = "请选择置顶时间!";
                return $result;
            }
        }
        if ($data['article_release_type']) {
            if (empty($data['article_release_time'])) {
                $result['info'] = "请选择发布时间!";
                return $result;
            }
        }

        if (!empty($data['article_start_top_time'])) {
            $data['article_start_top_time'] = strtotime(str_replace('T',' ',$data['article_start_top_time']));
        }
        if (!empty($data['article_end_top_time'])) {
            $data['article_end_top_time'] = strtotime(str_replace('T',' ',$data['article_end_top_time']));
        }
        if (!empty($data['article_start_show_time'])) {
            $data['article_start_show_time'] = strtotime(str_replace('T',' ',$data['article_start_show_time']));
        }
        if (!empty($data['article_end_show_time'])) {
            $data['article_end_show_time'] = strtotime(str_replace('T',' ',$data['article_end_show_time']));
        }
        if (!empty($data['article_release_time'])) {
            $data['article_release_time'] = strtotime(str_replace('T',' ',$data['article_release_time']));
        }

        $img = request()->file('img');
        if($img) {
            $path = ROOT_PATH . 'public' . DS . 'webroot' . DS . $this->corp_id . DS . 'images';
          
            $checkFlg = $img->check(["ext"=>config('upload_image.image_ext')]);
                //trace(var_exp($img,'$img','return'));
            if(!$checkFlg){
                exception("上传动态图片检查失败");
            }
            $info = $img->move($path);
                //trace(var_exp($info,'$info','return'));
            if($info===false){
                exception("上传动态图片失败");
            }
                //var_exp($info,'$info');
            $savename = $info->getSaveName();
            $data['img_url'] = $savename;
            
        }else{
            $data['img_url'] = '';
        }

        // var_dump($data);die();
        $id = $this->_speechCraftModel->addArticleInfo($data);
        if ($id) {
            $result['status'] = 1;
            $result['info'] = "添加文章成功";
        }
        // var_dump($id);die();
        return json($result);

    }

 /*
    编辑文章
     */
    public function editArticle(){
        $result = ['status'=>0,'info'=>"更新文章失败!"];

        $userinfo = get_userinfo();
        $id = input('article_id',0,'int');
        $uid = $userinfo['userid'];
        $data = [];
        $data['article_name'] = input('article_name','',"string");
        $data['article_type'] = input('article_type',1,"int");
        $data['article_class'] = input('article_class',0,"int");
        $data['article_content'] = input('article_content','',"string");
        $data['article_url'] = input('article_url','',"string");
        $data['article_text'] = input('article_text');
        $data['article_is_top'] = input('article_is_top',0,"int");
        $data['article_start_top_time'] = input('article_start_top_time','',"string");
        $data['article_end_top_time'] = input('article_end_top_time','',"string");
        $data['article_start_show_time'] = input('article_start_show_time','',"string");
        $data['article_end_show_time'] = input('article_end_show_time','',"string");
        $data['article_release_type'] = input('article_release_type',0,"int");
        $data['article_release_time'] = input('article_release_time','',"string");
        $data['article_creat_time'] = time();
        $data['article_edit_time'] = time();
        // $data['add_user'] = $uid;
        // var_dump(input('post.'));
        // var_dump($data);die();
        if (empty($data['article_name'])) {
            $result['info'] = "文章名不能为空!";
            return json($result);
        }
        if (empty($data['article_class'])) {
            $result['info'] = "文章分类不能为空!";
            return json($result);
        }
        if (empty($data['article_content'])) {
            $result['info'] = "文章简介不能为空!";
            return json($result);
        }
        if ($data['article_type'] == 1) {
            if (empty($data['article_text'])) {
                $result['info'] = "文章正文不能为空!";
                return json($result);
            }
        }else{
            if (empty($data['article_url'])) {
                $result['info'] = "文章链接不能为空!";
                return json($result);
            }
        }
        if ($data['article_is_top']) {
            if (empty($data['article_start_top_time']) || empty($data['article_end_top_time'])) {
                $result['info'] = "请选择置顶时间!";
                return $result;
            }
        }
        if ($data['article_release_type']) {
            if (empty($data['article_release_time'])) {
                $result['info'] = "请选择发布时间!";
                return $result;
            }
        }

        if (!empty($data['article_start_top_time'])) {
            $data['article_start_top_time'] = strtotime(str_replace('T',' ',$data['article_start_top_time']));
        }
        if (!empty($data['article_end_top_time'])) {
            $data['article_end_top_time'] = strtotime(str_replace('T',' ',$data['article_end_top_time']));
        }
        if (!empty($data['article_start_show_time'])) {
            $data['article_start_show_time'] = strtotime(str_replace('T',' ',$data['article_start_show_time']));
        }
        if (!empty($data['article_end_show_time'])) {
            $data['article_end_show_time'] = strtotime(str_replace('T',' ',$data['article_end_show_time']));
        }
        if (!empty($data['article_release_time'])) {
            $data['article_release_time'] = strtotime(str_replace('T',' ',$data['article_release_time']));
        }
        // var_dump($id);die();
        $id = $this->_speechCraftModel->editArticleInfo($id,$data);
        // var_dump($id);die();
        if ($id) {
            $result['status'] = 1;
            $result['info'] = "更新文章成功";
        }
        // var_dump($id);die();
        return json($result);

    }

    /**
     * 添加分类
     */
    public function addClass(){
        $result = ['status'=>0,'info'=>'添加分类失败!'];

        $data = [];
        $data['type_name'] = input('type_name','',"string");
        $data['creat_time'] = time();

        $class_id = $this->_speechCraftModel->addClassInfo($data);
        if ($class_id) {
            $result['status'] = 1;
            $result['info'] = "添加分类成功";
        }

        return json($result);
    }

    /**
     * 获取一篇文章信息
     * @return [type] [description]
     */
    protected function _showArticleInfo(){
        $id = input('id',0,'int');
        // var_dump($id);die();
        if(!$id){
            $this->error("参数错误！");
        }
        $article = $this->_speechCraftModel->getOneArticleById($id);
        // var_dump($article);die();
        $this->assign("article",$article);
    }

    /**
     * 显示详情页面
     * @return [type] [description]
     */
    public function show(){
        $this->_showArticleInfo();
        return view('detail');
    }

    /**
     * 删除文章
     */
    public function delete(){
        $result = ['status'=>0,'info'=>"删除失败"];
        $article_id = input("ids/a");
        // var_dump($article_id);die();
        if (empty($article_id)) {
            $result['info'] = "文章编号为空";
            return $result;
        }
        $flg = $this->_speechCraftModel->delOneArticleById($article_id);
        if ($flg > 0) {
            $result['info'] = "删除成功";
            $result['status'] = 1;
        }
        return json($result);

    }

    /**
     * 删除分类
     * @return [type] [description]
     */
    public function deleteClass(){
        $result = ['status'=>0,'info'=>"删除分类失败"];
        $class_id = input('class_id',0,'int');
        if (!$class_id) {
            $result['info'] = "分类id为空";
            return json($result);
        }
        $article_info = $this->_speechCraftModel->getAllArticle('',$class_id);
        if (!empty($article_info)) {
            $result['info'] = "该分类下还有文章,不能删除分类";
            return json($result);
        }
        $flg = $this->_speechCraftModel->deleteClassById($class_id);
        if ($flg>0) {
            $result['info'] = "删除分类成功";
            $result['status'] = 1;
        }
        return json($result);
    }

    /**
     * 修改文章分类
     */
    public function changeClass(){
        $result = ['status'=>0,'info'=>"修改失败"];
        $article_id = input('article_id/a');
        $class_id = input('class_id',0,'int');
        if (empty($article_id) || !$class_id) {
            $result['info'] = "参数错误";
            return json($result);
        }
        $data['article_class'] = $class_id;
        $flg = $this->_speechCraftModel->editArticleInfo($article_id,$data);
        if ($flg > 0) {
            $result['info'] = "修改成功";
            $result['status'] = 1;
        }else{
            $result['info'] = "分类没有变化";
        }
        return json($result);
    }

    /**
     * 修改文章置顶
     */
    public function changeIsTop(){
        $result = ['status'=>0,'info'=>"修改失败"];
        $article_id = input('article_id',0,'int');
        // $is_top = input('is_top',0,'int');
        $article_start_top_time = input('article_start_top_time','',"string");
        $article_end_top_time = input('article_end_top_time','',"string");
        if (!$article_id || !$article_start_top_time || !$article_end_top_time) {
            $result['info'] = "参数错误";
            return json($result);
        }
        $data['article_is_top'] = 1;
        $data['article_start_top_time'] = $article_start_top_time;
        $data['article_end_top_time'] = $article_end_top_time;
        $data['article_edit_time'] = time();
        if (!empty($data['article_start_top_time'])) {
            $data['article_start_top_time'] = strtotime(str_replace('T',' ',$data['article_start_top_time']));
        }
        if (!empty($data['article_end_top_time'])) {
            $data['article_end_top_time'] = strtotime(str_replace('T',' ',$data['article_end_top_time']));
        }
        $flg = $this->_speechCraftModel->editArticleInfo($article_id,$data);
        if ($flg > 0) {
            $result['info'] = "修改成功";
            $result['status'] = 1;
        }
        return json($result);
    }

    public function setTop(){
        return view('set_top');
    }

    public function deleteSpeechClass(){
        return view('delete_speech_class');
    }

    public function deleteSpeechList(){
        return view('delete_speech_list');
    }

    public function changeSpeechClass(){
        $article_type = $this->_speechCraftModel->getAllArticleType();
        $this->assign('article_type',$article_type);
        return view('change_class');
    }

    public function confirmBatchDel(){
        return view('confirm_batch_delete');
    }

    public function confirmBatchChange(){
        $article_type = $this->_speechCraftModel->getAllArticleType();
        $this->assign('article_type',$article_type);
        return view('confirm_batch_change');
    }

    /**
     * 手机接口 获取所有文章
     * @return [type] [description]
     */
    public function getAllArticle(){
        $result = ['status'=>0,'info'=>'获取信息失败!'];

        $key_word = input('key_word','','string');
        $class_id = input('class_id',0,'int');
        $page = input('page',1,'int');
        $num = input('num',20,'int');
        $all_article = $this->_speechCraftModel->getAllArticle($key_word,$class_id,$page,$num);
        foreach ($all_article as $key => $value) {
            $all_article[$key]['url'] = "/knowledgebase/speech_craft/show/id/".$value['id'];
        }

        $result['data'] = $all_article;
        $result['status'] = 1;
        $result['info'] = "获取成功!";

        return json($result);
    }

    //分享文章
    public function replayArticle(){
        $result = ['status'=>0,'info'=>"分享失败"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];

        $id = input('id',0,'int');
        if (!$id) {
            $result['info'] = "参数错误";
            return json($result);
        }
        $article = $this->_speechCraftModel->getOneArticleById($id);
        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $corporationShareContentModel = new CorporationShareContent($this->corp_id);

        $corporationShareModel->link->startTrans();
        try {
            $content["content"] = $article['article_name'];
            $content["share_url"] = "/knowledgebase/speech_craft/show/id/".$id;
            $content_id = $corporationShareContentModel->createCorporationShareContent($content);
            if(!$content_id){
                $result['info'] = "插入动态消息失败";
                exception("发布动态内容失败");
            }
            $share['pid'] = $id;
            $share["userid"] = $uid;
            $share["create_time"] = time();
            $share["content_id"] = $content_id;
            $share_id = $corporationShareModel->createCorporationShare($share);
            if(!$share_id){
                $result['info'] = "插入动态失败";
                exception("发布动态失败");
            }
            $corporationShareModel->link->commit();
        } catch (\Exception $ex) {
            $corporationShareModel->link->rollback();
            return json($result);
        }

        //发送评论消息
        $userinfos = $userinfo['userinfo'];
        $str = $userinfos['truename']."转发了你发表的内容";
        $receive_uids[] = $article['add_user'];
        if ($article['img_url']) {
            $sms['img_url'] = $article['img_url'];
        }else{
            $sms['img_url'] = '/message/images/article_default.png';
        }
        
        save_msg($str,"/knowledgebase/speech_craft/show/id/".$id,$receive_uids,5,12,$uid,$id,$sms);
        $result['status'] = 1;
        $result['info'] = "动态转发成功";
        return json($result);
    }

}