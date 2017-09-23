<?php
/**
 * Created by messhair
 * Date: 17-2-27
 */
namespace app\huanxin\service;

use app\common\model\Employee;
use app\common\model\Structure;

class Api
{
    private $token_uri = 'https://a1.easemob.com/1107161108178376/zhuowin/token';
    private $client_id = 'YXA6TVK18MvJEeaGhoOxEVEnmQ';
    private $client_secret = 'YXA65ZKnxxg8yLo7Ld4E0eXwWVCt5GM';
    private $grant_type = 'client_credentials';
    private $header = array();
    private $access_token = array();
    private $user_uri = 'https://a1.easemob.com/1107161108178376/zhuowin/users';
    private $add_user_uri = 'https://a1.easemob.com/1107161108178376/zhuowin/users/{owner_username}/contacts/users/{friend_username}';
    private $group_uri = 'https://a1.easemob.com/1107161108178376/zhuowin/chatgroups';

    public function __construct()
    {
        if (empty($this->access_token)) {
            $this->getAccessToken();
        }
        $this->header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->access_token['access_token'],
        );
    }

    public function index()
    {

    }

    /**
     * 修改环信用户昵称
     * @param $user telephone
     * @param $nickname 真实姓名
     * @return array   [
     * "uuid" : "06538d5a-dd2b-11e6-bb3e-697fe945caa3",
     * "type" : "user",
     * "created" : 1484708754085,
     * "modified" : 1484710090353,
     * "username" : "zhongxun_xiaoshou_jack",
     * "activated" : true,
     * "nickname" : "jacknickname"
     * ]
     */
    public function changeHuanxinNickname($user, $nickname)
    {
        $uri = $this->user_uri . '/' . $user;
        $header = array(
            'Authorization: Bearer ' . $this->access_token['access_token']
        );
        $body = json_encode(array('nickname' => $nickname), true);
        $result = $this->getMessage($uri, $body, $header, 'put');
        $result = json_decode($result, true);
        return $result['entities'];
    }

    /**
     *重置环信用户密码
     * @param $user telephone
     * @param $newpass 新密码
     * @return array [
     * "action" : "set user password",
     * "timestamp" : 1484709660468,
     * "duration" : 28
     * ]
     *
     */
    public function resetPassword($user, $newpass)
    {
        $uri = $this->user_uri . '/' . $user . '/password';
        $header = array(
            'Authorization: Bearer ' . $this->access_token['access_token']
        );
        $body = json_encode(array(
            'newpassword' => $newpass
        ), true);
        $result = $this->getMessage($uri, $body, $header, 'put');
        return json_decode($result, true);
    }

    /**
     * 删除单个环信用户
     * @param $user telephone
     * @return array
     *      'action' => string 'delete' (length=6)
     * 'application' => string '4d52b5f0-cbc9-11e6-8686-83b111512799' (length=36)
     * 'path' => string '/users' (length=6)
     * 'uri' => string 'https://a1.easemob.com/1107161108178376/zhuowin/users' (length=53)
     * 'entities' =>
     * array (size=1)
     * 0 =>
     * array (size=7)
     * 'uuid' => string 'b0abed10-d92a-11e6-993a-fba830408f16' (length=36)
     * 'type' => string 'user' (length=4)
     * 'created' => float 1484268805729
     * 'modified' => float 1484548751244
     * 'username' => string 'zhongxun_xiaoshou_jack' (length=22)
     * 'activated' => boolean true
     * 'nickname' => string 'zhongxun_xiaoshou_jack_nickname' (length=31)
     * 'timestamp' => float 1484707887187
     * 'duration' => int 88
     * 'organization' => string '1107161108178376' (length=16)
     * 'applicationName' => string 'zhuowin' (length=7)
     */
    public function deleteSingleHuanxinUser($user)
    {
        $header = array(
            'Authorization: Bearer ' . $this->access_token['access_token']
        );
        $result = $this->getMessage($this->user_uri . '/' . $user, '', $header, 'delete');
        $result = json_decode($result, true);
        return $result;
    }

    public function deleteMultiHuanxinUser($users){
        $result = [];
        foreach ($users as $user){
            $result[] = $this->deleteSingleHuanxinUser($user);
            usleep(100000);
        }
        return $result;
    }

    /**
     * 由【后台单个注册】的用户，添加环信好友
     * @param $owner string|array 用户tel或含tel的数组
     * @param $corp_id (公司代码)
     * @return array['message'=>'','status'=>true/false]
     *
     * "action" : "post",
     * "application" : "4d52b5f0-cbc9-11e6-8686-83b111512799",
     * "path" : "/users/1959796a-da28-11e6-af03-bbe385141aa9/contacts",
     * "uri" : "https://a1.easemob.com/1107161108178376/zhuowin/users/1959796a-da28-11e6-af03-bbe385141aa9/contacts",
     * "entities" : [ {
     * "uuid" : "19561e0a-da28-11e6-9bd5-9da815ee1170",
     * "type" : "user",
     * "created" : 1484377644000,
     * "modified" : 1484378873865,
     * "username" : "13322223333",
     * "activated" : true,
     * "nickname" : "杰克"
     * } ],
     * "timestamp":1406086326974,"duration":242,
     * "organization":"1107161108178376",
     * "applicationName":"zhuowin"
     */
    public function addFriend($corp_id, $owner)
    {
        //获取后台所有$owner未添加为环信好友的用户(先注册的用户)
        $employee = new Employee($corp_id);
        $add_user_uri = array();
        if (is_array($owner)) {
            foreach ($owner as $k => $v) {
                $friend_list_p = $employee->getFriendsList($v['telephone']);
                foreach ($friend_list_p as $kk=>$vv) {
                    $add_user_uri[] .= $this->user_uri . '/' . $v['telephone'] . '/contacts/users/' . $vv['telephone'];
                }
            }
        }else{
            $friend_list = $employee->getFriendsList($owner);
            foreach ($friend_list as $k => $v) {
                $add_user_uri[] .= $this->user_uri . '/' . $owner . '/contacts/users/' . $v['telephone'];
            }
        }
        /**
         * $add_user_uri=array(4) {
                [0] => string(92) "https://a1.easemob.com/1107161108178376/zhuowin/users/13311112222/contacts/users/13322223333"
                [1] => string(92) "https://a1.easemob.com/1107161108178376/zhuowin/users/13311112222/contacts/users/13322221111"
                [2] => string(92) "https://a1.easemob.com/1107161108178376/zhuowin/users/13311112222/contacts/users/13322225555"
                [3] => string(92) "https://a1.easemob.com/1107161108178376/zhuowin/users/13311112222/contacts/users/13322226666"
            }
         */
        //依次把后台的其他用户添加为好友
        $error = array();
        foreach ($add_user_uri as $url) {
            $add_user = $this->getMessage($url, '', $this->header, 'post');
            if (isset($add_user['error'])) {
                $error[] .= $add_user;
            }
            usleep(100000);
        }
        if (empty($error)) {
            $info['message'] = '添加好友成功';
            $info['status'] = true;
        } else {
            $info['message'] = '添加好友失败或部分失败';
            $info['status'] = false;
            $info['error'] = $error;
        }
        return $info;
    }

    /**
     * 获取环信单用户
     * @param $user telephone
     * @return array =>
     * 'uuid' => string '1959796a-da28-11e6-af03-bbe385141aa9' (length=36)
     * 'type' => string 'user' (length=4)
     * 'created' => float 1484377644022
     * 'modified' => float 1484548615799
     * 'username' => string '13311112222' (length=11)
     * 'activated' => boolean true
     * 'notification_no_disturbing' => boolean false
     * 'nickname' => string '朱利安123' (length=12)
     */
    public function getSingleHuanxinUser($user)
    {
        $header = array(
            'Authorization: Bearer ' . $this->access_token['access_token']
        );
        $users = $this->getMessage($this->user_uri . '/' . $user, '', $header, '');
        $users = json_decode($users, true);
        return $users['entities'][0];
    }

    /**
     * 获取app所有用户信息
     * @return array (size=3)
    0 =>
     * array (size=7)
     * 'uuid' => string '19561e0a-da28-11e6-9bd5-9da815ee1170' (length=36)
     * 'type' => string 'user' (length=4)
     * 'created' => float 1484377644000
     * 'modified' => float 1484377644000
     * 'username' => string '13322223333' (length=11)
     * 'activated' => boolean true
     * 'nickname' => string '杰克' (length=6)
     * 1 =>
     * array (size=7)
     * 'uuid' => string '1957f2ca-da28-11e6-a912-19f83a3cbac2' (length=36)
     * 'type' => string 'user' (length=4)
     * 'created' => float 1484377644012
     * 'modified' => float 1484377644012
     * 'username' => string '13322221111' (length=11)
     * 'activated' => boolean true
     * 'nickname' => string '亚当' (length=6)
     */
    public function getAllHuanxinUsers()
    {
        $header = array(
            'Authorization: Bearer ' . $this->access_token['access_token']
        );
        $users = $this->getMessage($this->user_uri, '', $header, '');
        $users = json_decode($users, true);
        return $users['entities'];
    }

    /**
     * 在环信中批量注册employee表中账号
     * @param $corp_id string 公司代号
     * @param $username string 手机号
     * @param $password string 密码
     * @param $nickname string 昵称
     * @return array ['message'=>'','status'=>true/false]
     */
    public function regUser($corp_id, $username, $password="87654321",$nickname=""){
        $info = ['status'=>false,'message'=>'注册环信用户失败'];
        $user_info["username"] = $username;
        $user_info["password"] = $password;
        if($nickname){
            $user_info["nickname"] = $nickname;
        }
        //注册环信
        $user_json = json_encode($user_info, true);
        $user_reg = $this->getMessage($this->user_uri, $user_json, $this->header, 'post');
        $user_info = json_decode($user_reg, true);
        if (isset($user_info['error'])) {
            $info['message'] = $this->getError($user_info['error']);
        } else {
            $user_arr = $user_info['entities'];//注册成功的用户
            //TODO 不应该在这里更新数据库,改为调用这个接口前开启数据库事务,先更新数据库,再请求这个接口,接口返回成功提交事务,失败回滚事务,保持一致性
            $b = $this->updateImRegInfoToDataBase($corp_id,$user_arr);
            if ($b >0) {
                $info['status'] = true;
                $info['message'] = '注册环信用户成功';
            }
        }
        return $info;
    }

    /**
     * 修改用户的环信密码
     * @param  string $corp_id  公司代号
     * @param  string $username 用户名称
     * @param  string $password 密码
     * @return [type]           [description]
     */
    public function updatePassword($corp_id,$username,$newpassword){
        $info = ['status'=>false,'message'=>'更新环信密码失败'];
        $user_info['newpassword'] = $newpassword;

        //更新环信密码
        $user_json = json_encode($user_info,true);
        $update_uri = $this->user_uri."/".$username."/password";
        $user_reg = $this->getMessage($update_uri,$user_json,$this->header,'put');
         $user_info = json_decode($user_reg, true);
        if (isset($user_info['error'])) {
            $info['message'] = $this->getError($user_info['error']);
        } else {
            $info['status'] = true;
            $info['message'] = '环信用户密码更新成功';
        }
    
        return $info;
    }

    /**
     * 在环信中批量注册employee表中账号
     * @param $corp_id 公司代号
     * @param $users [
     * ['username'=>'13311112222','password'=>'123456','nickname'=>'张三'],
     * ['username'=>'13333335555','password'=>'123456','nickname'=>'李四']
     * ]
     * @return array ['message'=>'','status'=>true/false]
     */
    public function regMultiUser($corp_id, $users)
    {
        //注册环信
        $users = json_encode($users, true);
        $user_reg = $this->getMessage($this->user_uri, $users, $this->header, 'post');
        $user_info = json_decode($user_reg, true);
        if (isset($user_info['error'])) {
            $info['message'] = $this->getError($user_info['error']);
        } else {
            $user_arr = $user_info['entities'];//所有注册成功的用户
            $b = $this->updateImRegInfoToDataBase($corp_id,$user_arr);
            if ($b >0) {
                $info['status'] = true;
                $info['message'] = '批量注册环信用户成功';
            } else {
                $info['status'] = false;
                $info['message'] = '批量注册环信用户失败';
            }
        }
        return $info;
    }

    /**
     * 环信创建群组
     * @param  array   $members      群组成员，此属性为可选的，但是如果加了此项，数组元素至少一个（注：群主jma1不需要写入到members里面）
     * @param  [type]  $groupname    群组名称，此属性为必须的
     * @param  [type]  $desc         群组描述，此属性为必须的
     * @param  boolean $public       是否是公开群，此属性为必须的
     * @param  integer $maxusers     群组成员最大数（包括群主），值为数值类型，默认值200，最大值2000，此属性为可选的
     * @param  boolean $members_only 加入群是否需要群主或者群管理员审批，默认是false
     * @param  boolean $allowinvites 是否允许群成员邀请别人加入此群。 true：允许群成员邀请人加入此群，false：只有群主或者管理员才可以往群里加人
     * @param  [type]  $owner        群组的管理员，此属性为必须的
     * @return [type]                [description]
     */
    public function createGroup($corp_id,$structureid,$members=[],$groupname,$desc,$owner,$public=false,$maxusers=200,$members_only=false,$allowinvites=false){
        $info = ['status'=>0,'message'=>'创建群组失败'];

        if (!$groupname || !$desc || !$owner) {
            $info['message'] = '参数有误';
            return $info;
        }

        $body['groupname'] = $groupname;
        $body['desc'] = $desc;
        $body['public'] = $public;
        $body['maxusers'] = $maxusers;
        $body['members_only'] = $members_only;
        $body['allowinvites'] = $allowinvites;
        $body['owner'] = $owner;
        $body['members'] = $members;

        $body_json = json_encode($body,true);
        $group_info = $this->getMessage($this->group_uri,$body_json,$this->header,'post');
        $group = json_decode($group_info,true);
        if (isset($group['error'])) {
            $info['message'] = $group['error_description'];
        } else {
            $group_id = $group['data']['groupid'];//
            $a = $this->updateGroupId($corp_id,$structureid,$group_id);
            if ($a > 0) {
                $info['message'] = '群组注册成功';
                $info['status'] = 1;
            }else{
                $info['message'] = '群组注册失败';
            }
            
        }

        return $info;
    }

    /**
     * 更新群组信息
     * @param  [type] $groupinfo 跟新信息
     *  "groupname", //群组名称，修改时值不能包含斜杠（"/"）。
        "description" //群组描述，修改时值不能包含斜杠（"/"）。
        "maxusers"//群组成员最大数（包括群主），值为数值类型
     * @return [type]          [description]
     */
    public function updateGroupInfo($groupid,$groupinfo=[]){
        $info = ['status'=>0,'message'=>'跟新信息失败'];

        if (!$groupid || !$groupinfo) {
            $info['message'] = '参数错误';
            return $info;
        }
        $res_json = json_encode($groupinfo,true);
        $uri = $this->group_uri.'/'.$groupid;
        $request_info = $this->getMessage($uri,$res_json,$this->header,'put');
        $result_info = json_decode($request_info,true);

        if (isset($result_info['error'])) {
            $info['message'] = $result_info['error_description'];
        }else{
            $info['status'] = 1;
            $info['message'] = '更新成功';
            $info['data']  = $result_info['data'];
        }

        return $info;

    }

    /**
     * 删除部门群组
     * @param  [type] $groupid 群组id
     * @return [type]          [description]
     */
    public function deleteGroup($groupid){
        $info = ['status'=>0,'message'=>'删除失败'];

        $uri = $this->group_uri."/".$groupid;
        $request_info = $this->getMessage($uri,'',$this->header,'delete');
        $result_info = json_decode($request_info,true);

        if (isset($result_info['error'])) {
            $info['message'] = $result_info['error_description'];
        }else{
            $info['status'] = 1;
            $info['message'] = '删除成功';
            $info['data']  = $result_info['data'];
        }

        return $info;
    }

    /**
     * 群组添加单个员工
     * @param  $groupid  群组id   
     * @param [type] $username 用户环信名
     */
    public function addOneEmployee($groupid='',$username=''){
        if (!$groupid || !$username) {
            $info['status'] = 0;
            $info['message'] = '参数错误';
            return $info;
        }

        $uri = $this->group_uri."/".$groupid."/users/".$username;
        $request_info = $this->getMessage($uri,'',$this->header,'post');
        $result_info = json_decode($request_info,true);

        if (isset($result_info['error'])) {
            $info['status'] = 0;
            $info['message'] = '员工添加失败';
            $info['error'] = $result_info['error'];
        }else{
            $info['status'] = 1;
            $info['message'] = '员工添加成功';
            $info['data'] = $result_info['data'];
        }

        return $info;
    }

    /**
     * 删除单个群组成员
     * @param  [type] $groupid  群组id
     * @param  [type] $username 员工环信名
     * @return [type]           [description]
     */
    public function deleteOneEmployee($groupid='',$username=''){
        if (!$groupid || !$username) {
            $info['status'] = 0;
            $info['message'] = '参数错误';
            return $info;
        }

        $uri = $this->group_uri."/".$groupid."/users/".$username;
        $request_info = $this->getMessage($uri,'',$this->header,'delete');
        $result_info = json_decode($request_info,true);

        if (isset($result_info['error'])) {
            $info['status'] = 0;
            $info['message'] = '员工删除失败';
            $info['error'] = $result_info['error'];
        }else{
            $info['status'] = 1;
            $info['message'] = '员工删除成功';
            $info['data'] = $result_info['data'];
        }

        return $info;
    }

    /**
     * 一个员工添加到多个群组中
     * @param  string $username 员工环信名称
     * @param  array  $groupids 群组数组
     * @return [type]           [description]
     */
    public function addUserFromMoreGroup($username='',$groupids=[]){

        if (!$username || !$groupids) {
            $info['status'] = 0;
            $info['message'] = '参数错误';
            return $info;
        }

        foreach ($groupids as $key => $value) {
            $result = $this->addOneEmployee($value,$username);
            if (isset($result['error'])) {
                $info[$value]['error'] = $result['error'];
                $info[$value]['message'] = '该群添加失败'; 
                $info[$value]['status'] = 0;
            }else{
                $info[$value]['status'] = 1;
                $info[$value]['message'] = '该群添加成功';
                $info[$value]['data'] = $result['data'];
            }

            usleep(3000000);
        }

        return $info;
    }

    /**
     * 一个员工从多个群组中删除
     * @param  string $username 员工环信名称
     * @param  array  $groupids 群组数组
     * @return [type]           [description]
     */
    public function deleteUserFromMoreGroup($username='',$groupids=[]){

        if (!$username || !$groupids) {
            $info['status'] = 0;
            $info['message'] = '参数错误';
            return $info;
        }

        foreach ($groupids as $key => $value) {
            $result = $this->deleteOneEmployee($value,$username);
            if (isset($result['error'])) {
                $info[$value]['error'] = $result['error'];
                $info[$value]['message'] = '该群删除失败'; 
                $info[$value]['status'] = 0;
            }else{
                $info[$value]['status'] = 1;
                $info[$value]['message'] = '该群删除成功';
                $info[$value]['data'] = $result['data'];
            }

            usleep(3000000);
        }

        return $info;
    }

    /**
     * 批量添加员工
     * @param [type] $groupid 群组id
     * @param array  $users   员工名称 $this->corpro_id.$userid
     */
    public function addAllUsers($groupid,$usernames=[]){

        if (!$groupid || !$usernames) {
            $info['status'] = 0;
            $info['message'] = '参数错误';
        }

        $uri = $this->group_uri."/".$groupid."/users";
        $request['usernames'] = $usernames; 
        $request_json = json_encode($request,true);
        $result = $this->getMessage($uri,$request_json,$this->header,'post');
        $result_json = json_decode($result,true);

        if (isset($result_json['error'])) {
            $info['status'] = 0;
            $info['error'] = $result_json['error'];
            $info['message'] = '添加失败';
        }else{
            $info['status'] = 1;
            $info['data'] = $result_json['data'];
            $info['message'] = '添加成功';
        }

        return $info;

    }

    /**
     * 批量删除用户
     * @param  [type] $groupid   群组id
     * @param  [type] $usernames 删除姓名数组
     * @return [type]            [description]
     */
    public function deleteAllUsers($groupid,$usernames=[]){
        if (!$groupid || !$usernames) {
                $info['status'] = 0;
                $info['message'] = '参数错误';
            }    

            $uri = $this->group_uri."/".$groupid."/users/";
            foreach ($usernames as $key => $value) {
                $uri .= $value.",";
            }
            $result_info = $this->getMessage($uri,'',$this->header,'delete');
            $result_json = json_decode($result_info,true);

            if (isset($result_json['error'])) {
                $info['error'] = $result_json['error'];
                $info['status'] = 0;
                $info['message'] = '删除失败';
            }else{
                $info['data'] = $result_json['data'];
                $info['status'] = 1;
                $info['message'] = '删除成功';
            }

            return $info;

    }

    private function updateImRegInfoToDataBase($corp_id,$user_arr){
    //更改employee表注册成功
        $user_up = array();
        foreach ($user_arr as $key => $val) {
            foreach ($val as $k => $v) {
                if ($k == 'username') {
                    $user_up[] .= $v;
                }
            }
        }
        $employee = new Employee($corp_id);
        return $employee->saveIm($user_up);
    }

    /**
     * 更新表中部门群组id
     * @param  [type] $corp_id     [description]
     * @param  [type] $structureid [description]
     * @param  [type] $groupid     [description]
     * @return [type]              [description]
     */
    private function updateGroupId($corp_id,$structureid,$groupid){
        $structure = new Structure($corp_id);
        return $structure->upGroupId($structureid,$groupid);
    }

    /**
     * {
     * "access_token": "YWMtsfs-1s5yEeagMWPqu9vgAAAAAAAAAAAAAAAAAAAAAAFNUrXwy8kR5oaGg7ERUSeZAgMAAAFZTxGS5ABPGgB0JmLYezGu992vK6kmnlBWfSJ7sBbN9ljaUZSdVfzoxQ",
     * "expires_in": 4060381,
     * "application": "4d52b5f0-cbc9-11e6-8686-83b111512799"
     * }
     * 获取用户access_token
     * @return json_string
     */
    private function getAccessToken()
    {
        $request = array(
            'grant_type' => $this->grant_type,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret
        );
        $request = json_encode($request);
        $access_token = $this->getMessage($this->token_uri, $request, $this->header, 'post');
        $this->access_token = json_decode($access_token, true);
    }

    /**
     * 请求服务器接口
     * @param $url
     * @param $request
     * @param $header
     * @param int $second
     * @return mixed
     */
    private function getMessage($url, $request, $header, $method = '', $second = 180)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $second);
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        } elseif ($method == 'delete') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif ($method == 'put') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    private function getError($info)
    {
        $error = '';
        switch ($info) {
            case 'invalid_grant':
                $error = '用户名或者密码输入错误';
                break;
            case 'auth_bad_access_token':
                $error = '无效token，或token过期';
                break;
            case 'duplicate_unique_property_exists':
                $error = '用户名已存在，dddd这个用户名在该APP下已经存在';
                break;
            case 'reach_limit':
                $error = '超过接口每秒调用次数，加大调用间隔或者联系商务调整限流大小';
                break;
            default:
                $error = '请查看开发文档';
        }
        return $error;
    }
}