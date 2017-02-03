<?php
/**
 * Created by messhair.
 * Date: 2017/1/12
 */
namespace Huanxin\Controller;

use Think\Controller;
use Huanxin\Model\EmployerModel;
class HuanxinApiController extends Controller{
    private $token_uri='https://a1.easemob.com/1107161108178376/zhuowin/token';
    private $client_id='YXA6TVK18MvJEeaGhoOxEVEnmQ';
    private $client_secret='YXA65ZKnxxg8yLo7Ld4E0eXwWVCt5GM';
    private $grant_type='client_credentials';
    private $header=array();
    private $access_token=array();
    private $user_uri='https://a1.easemob.com/1107161108178376/zhuowin/users';
    private $add_user_uri='https://a1.easemob.com/1107161108178376/zhuowin/users/{owner_username}/contacts/users/{friend_username}';

    protected function _initialize(){
        if(empty($this->access_token)){
            $this->getAccessToken();
        }
        $this->header=array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->access_token,
        );
    }
    public function index(){

    }

    /**
     * 修改环信用户昵称
     * @param $user(telephone)
     * @param $nickname
     * @return array   [
            "uuid" : "06538d5a-dd2b-11e6-bb3e-697fe945caa3",
            "type" : "user",
            "created" : 1484708754085,
            "modified" : 1484710090353,
            "username" : "zhongxun_xiaoshou_jack",
            "activated" : true,
            "nickname" : "jacknickname"
         ]
     */
    public function changeHuanxinNickname($user,$nickname) {
        $uri=$this->user_uri.'/'.$user;
        $header=array(
            'Authorization: Bearer '.$this->access_token['access_token']
        );
        $body=json_encode(array('nickname'=>$nickname),true);
        $result=$this->getMessage($uri,$body,$header,'put');
        $result=json_decode($result,true);
        return $result['entities'];
    }

    /**
     *重置环信用户密码
     * @param $user(telephone)
     * @param $newpass
     * @return array [
            "action" : "set user password",
            "timestamp" : 1484709660468,
            "duration" : 28
        ]
     *
     */
    public function resetPassword($user,$newpass) {
        $uri=$this->user_uri.'/'.$user.'/password';
        $header=array(
            'Authorization: Bearer '.$this->access_token['access_token']
        );
        $body=json_encode(array(
            'newpassword'=>$newpass
        ),true);
        $result=$this->getMessage($uri,$body,$header,'put');dump($result);
        return json_decode($result,true);
    }

    /**
     * 删除单个环信用户
     * @param $user
     * @return array
     *      'action' => string 'delete' (length=6)
            'application' => string '4d52b5f0-cbc9-11e6-8686-83b111512799' (length=36)
            'path' => string '/users' (length=6)
            'uri' => string 'https://a1.easemob.com/1107161108178376/zhuowin/users' (length=53)
            'entities' =>
            array (size=1)
            0 =>
            array (size=7)
            'uuid' => string 'b0abed10-d92a-11e6-993a-fba830408f16' (length=36)
            'type' => string 'user' (length=4)
            'created' => float 1484268805729
            'modified' => float 1484548751244
            'username' => string 'zhongxun_xiaoshou_jack' (length=22)
            'activated' => boolean true
            'nickname' => string 'zhongxun_xiaoshou_jack_nickname' (length=31)
            'timestamp' => float 1484707887187
            'duration' => int 88
            'organization' => string '1107161108178376' (length=16)
            'applicationName' => string 'zhuowin' (length=7)
     */
    public function deleteSingleHuanxinUser($user) {
        $header=array(
            'Authorization: Bearer '.$this->access_token['access_token']
        );
        $result=$this->getMessage($this->user_uri.'/'.$user,'',$header,'delete');
        $result=json_decode($result,true);dump($result);
        return $result;
    }

    /**
     *  "action" : "post",
        "application" : "4d52b5f0-cbc9-11e6-8686-83b111512799",
        "path" : "/users/1959796a-da28-11e6-af03-bbe385141aa9/contacts",
        "uri" : "https://a1.easemob.com/1107161108178376/zhuowin/users/1959796a-da28-11e6-af03-bbe385141aa9/contacts",
        "entities" : [ {
            "uuid" : "19561e0a-da28-11e6-9bd5-9da815ee1170",
            "type" : "user",
            "created" : 1484377644000,
            "modified" : 1484378873865,
            "username" : "13322223333",
            "activated" : true,
            "nickname" : "杰克"
        } ],
        "timestamp":1406086326974,"duration":242,
        "organization":"1107161108178376",
        "applicationName":"zhuowin"
     * 由【后台单个注册】的用户，添加环信好友
     * @param $owner(telephone),$corp_id(公司代码)
     * @return array['message'=>'','status'=>true/false]
     */
    public function addFriend($corp_id,$owner) {
        //获取后台所有$owner未添加为环信好友的用户(先注册的用户)
        $employer=new EmployerModel(C('DB_COMMON_PREFIX').$corp_id.'.'.'employer');
        $friend_list=$employer->getFriendsList($owner);
        $add_user_uri=array();
        foreach($friend_list as $k=>$v){
            foreach($v as $key=>$val){
                $add_user_uri[].=$this->user_uri.'/'.$owner.'/contacts/users/'.$val;
            }
        }

        //依次把后台的其他用户添加为好友
        $error=array();
        foreach($add_user_uri as $url){
            $add_user=$this->getMessage($url,'',$this->header,'post');
            if($add_user['error']){
                $error[].=$add_user;
            }
//            sleep(1);
        }
        if(empty($error)){
            $info['message']='添加好友成功';
            $info['status']=true;
        }else{
            $info['message']='添加好友失败或部分失败';
            $info['status']=false;
        }
        //$this->ajaxReturn($info);
        //echo json_encode($info);
        return $info;
//        dump($error);
        dump($add_user);
    }

    /**
     * 获取环信单用户
     * @param $user(telephone)
     * @return array =>
            'uuid' => string '1959796a-da28-11e6-af03-bbe385141aa9' (length=36)
            'type' => string 'user' (length=4)
            'created' => float 1484377644022
            'modified' => float 1484548615799
            'username' => string '13311112222' (length=11)
            'activated' => boolean true
            'notification_no_disturbing' => boolean false
            'nickname' => string '朱利安123' (length=12)
     */
    public function getSingleHuanxinUser($user) {
        $header=array(
            'Authorization: Bearer '.$this->access_token['access_token']
        );
        $users=$this->getMessage($this->user_uri.'/'.$user,'',$header,'');
        $users=json_decode($users,true);
        return $users['entities'][0];
    }

    /**
     * 获取app所有用户信息
     * @return array (size=3)
        0 =>
        array (size=7)
            'uuid' => string '19561e0a-da28-11e6-9bd5-9da815ee1170' (length=36)
            'type' => string 'user' (length=4)
            'created' => float 1484377644000
            'modified' => float 1484377644000
            'username' => string '13322223333' (length=11)
            'activated' => boolean true
            'nickname' => string '杰克' (length=6)
        1 =>
        array (size=7)
            'uuid' => string '1957f2ca-da28-11e6-a912-19f83a3cbac2' (length=36)
            'type' => string 'user' (length=4)
            'created' => float 1484377644012
            'modified' => float 1484377644012
            'username' => string '13322221111' (length=11)
            'activated' => boolean true
            'nickname' => string '亚当' (length=6)
     */
    public function getAllHuanxinUsers() {
        $header=array(
            'Authorization: Bearer '.$this->access_token['access_token']
        );
        $users=$this->getMessage($this->user_uri,'',$header,'');
        $users=json_decode($users,true);
        return $users['entities'];
    }

    /**
     * 在环信中批量注册employer表中账号
     * @param $corp_id 公司代号
     * @param $users[['username'=>'13311112222','password'=>'123456'],['username'=>'13333335555','password'=>'123456']]
     * @return array ['message'=>'','status'=>true/false]
     */
    public function regMultiUser($corp_id,$users) {
//        $corp_id=session('corp_id');
        $corp_id='sdzhongxun';
        //注册环信
        $users=json_encode($users,true);
        $user_reg=$this->getMessage($this->user_uri,$users,$this->header,'post');
        $user_info=json_decode($user_reg,true);
        if($user_info['error']){
            $info['message']=$this->getError($user_info['error']);
        }else{
            $user_arr=$user_info['entities'];//所有注册成功的用户
//            更改employer表注册成功
            $user_up=array();
            foreach($user_arr as $key=>$val){
                foreach($val as $k=>$v){
                    if($k=='username'){
                        $user_up[].=$v;
                    }
                }
            }
//            dump($user_up);
            $b=$employer->saveIm($user_up);
            if($b!==false){
                $info['status']=true;
                $info['message']= '批量注册环信用户成功';
            }else{
                $info['status']=false;
                $info['message']= '批量注册环信用户失败';
            }
        }
        return $info;
//        dump($info);
    }

    /**
     * 环信注册多用户方法
     * array (size=9)
    'action' => string 'post' (length=4)
    'application' => string '4d52b5f0-cbc9-11e6-8686-83b111512799' (length=36)
    'path' => string '/users' (length=6)
    'uri' => string 'https://a1.easemob.com/1107161108178376/zhuowin/users' (length=53)
    'entities' =>
    array (size=3)
    0 =>
    array (size=7)
    'uuid' => string '19561e0a-da28-11e6-9bd5-9da815ee1170' (length=36)
    'type' => string 'user' (length=4)
    'created' => float 1484377644000
    'modified' => float 1484377644000
    'username' => string '13322223333' (length=11)
    'activated' => boolean true
    'nickname' => string '杰克' (length=6)
    1 =>
    array (size=7)
    'uuid' => string '1957f2ca-da28-11e6-a912-19f83a3cbac2' (length=36)
    'type' => string 'user' (length=4)
    'created' => float 1484377644012
    'modified' => float 1484377644012
    'username' => string '13322221111' (length=11)
    'activated' => boolean true
    'nickname' => string '亚当' (length=6)
    2 =>
    array (size=7)
    'uuid' => string '1959796a-da28-11e6-af03-bbe385141aa9' (length=36)
    'type' => string 'user' (length=4)
    'created' => float 1484377644022
    'modified' => float 1484377644022
    'username' => string '13311112222' (length=11)
    'activated' => boolean true
    'nickname' => string '朱利安' (length=9)
    'timestamp' => float 1484377644000
    'duration' => int 64
    'organization' => string '1107161108178376' (length=16)
    'applicationName' => string 'zhuowin' (length=7)
     * @param $access_token
     * @param $users
     * @return mixed
     */
    public function getMultiUserRegisterResult($username,$password) {
        $users=json_encode(array('username'=>$username,'password'=>$password));
        $this->header=array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->access_token['access_token'],
        );
        $reg_res=$this->getMessage($this->user_uri,$users,$this->header,'post');dump($reg_res);exit;
        return $reg_res;
    }

    /**
     * {
    "access_token": "YWMtsfs-1s5yEeagMWPqu9vgAAAAAAAAAAAAAAAAAAAAAAFNUrXwy8kR5oaGg7ERUSeZAgMAAAFZTxGS5ABPGgB0JmLYezGu992vK6kmnlBWfSJ7sBbN9ljaUZSdVfzoxQ",
    "expires_in": 4060381,
    "application": "4d52b5f0-cbc9-11e6-8686-83b111512799"
    }
     * 获取用户access_token
     * @return json_string
     */
    private function getAccessToken() {
        $request=array(
            'grant_type'    =>  $this->grant_type,
            'client_id'     =>  $this->client_id,
            'client_secret' =>  $this->client_secret
        );
        $request=json_encode($request);
        $access_token=$this->getMessage($this->token_uri,$request,$this->header,'post');
        $this->access_token= json_decode($access_token,true);
    }

    /**
     * 请求服务器接口
     * @param $url
     * @param $request
     * @param $header
     * @param int $second
     * @return mixed
     */
    private function getMessage($url,$request,$header,$method='',$second=180) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $second);
        if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        if(!empty($header)){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($method=='post'){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        }elseif($method=='delete'){
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'DELETE');
        }elseif($method=='put'){
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    private function getError($info) {
        $error='';
        switch($info){
            case 'invalid_grant':
                $error='用户名或者密码输入错误';
                break;
            case 'auth_bad_access_token':
                $error='无效token，或token过期';
                break;
            case 'duplicate_unique_property_exists':
                $error='用户名已存在，dddd这个用户名在该APP下已经存在';
                break;
            case 'reach_limit':
                $error='超过接口每秒调用次数，加大调用间隔或者联系商务调整限流大小';
                break;
            default:
                $error='请查看开发文档';
        }
        return $error;
    }
}