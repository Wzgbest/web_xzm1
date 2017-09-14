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

class SpeechCraft extends Initialize{
    protected $_speechCraftModel = null;
    protected $handle_max = 6;
    public function __construct(){
        parent::__construct();
        $this->_speechCraftModel = new SpeechCraftModel($this->corp_id);
    }
    public function index(){
   
        $key_word = input('key_word','','string');
        $class_id = input('class_id',0,'int');
        $article_type = $this->_speechCraftModel->getAllArticleType();
        $all_article = $this->_speechCraftModel->getAllArticle($key_word,$class_id,1,100);
        // var_dump($all_article);die();
        $this->assign('article_type',$article_type);
        $this->assign('all_article',$all_article);
        // $this->assign('now_time',time());
        return view();
    }

    /*
    添加新文章
     */
    public function add_article(){
        $article_type = $this->_speechCraftModel->getAllArticleType();

        $this->assign('article_type',$article_type);
        return view('new');
    }

    public function add_class_page(){
        return view('add_class');
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
            $result['message'] = "文章名不能为空!";
            return json($result);
        }
        if (empty($data['article_class'])) {
            $result['message'] = "文章分类不能为空!";
            return json($result);
        }
        if (empty($data['article_content'])) {
            $result['message'] = "文章简介不能为空!";
            return json($result);
        }
        if ($data['article_type'] == 1) {
            if (empty($data['article_text'])) {
                $result['message'] = "文章正文不能为空!";
                return json($result);
            }
        }else{
            if (empty($data['article_url'])) {
                $result['message'] = "文章链接不能为空!";
                return json($result);
            }
        }
        if ($data['article_is_top']) {
            if (empty($data['article_start_top_time']) || empty($data['article_end_top_time'])) {
                $result['message'] = "请选择置顶时间!";
                return $result;
            }
        }
        if ($data['article_release_type']) {
            if (empty($data['article_release_time'])) {
                $result['message'] = "请选择发布时间!";
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
        // var_dump($data);die();
        $id = $this->_speechCraftModel->addArticleInfo($data);
        if ($id) {
            $result['status'] = 1;
            $result['info'] = "添加文章成功";
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

}