<?php
/**
 * User: blu10ph
 */
namespace workflow;
use workflow\db\WorkflowDB;
class WorkflowForm{
    protected $DBillage;
    protected $flow_item_id = 0;
    protected $form_item = [];
    protected static $type = [];
    // 验证类型别名
    protected $alias = [
        '>' => 'gt', '>=' => 'egt', '<' => 'lt', '<=' => 'elt', '=' => 'eq', 'same' => 'eq',
    ];
    public function __construct(WorkflowDB $DBillage){
        $this->DBillage = $DBillage;
    }
    public function init($flow_item_id){
        $this->flow_item_id = $flow_item_id;
        $form_item_list = $this->DBillage->getWorkFlowFormItem($flow_item_id);
        foreach ($form_item_list as $form_item){
            if(!empty($form_item["form_item_enum"])){
                $form_item["form_item_enum_arr"] = explode(",",$form_item["form_item_enum"]);
            }
            $this->form_item[$flow_item_id][$form_item["id"]] = $form_item;
        }
        var_exp($this->form_item,'$this->form_item');
    }
    public function getFormItem($flow_item_id){
        if($this->flow_item_id!=$flow_item_id){
            $this->init($flow_item_id);
        }
        return $this->form_item[$flow_item_id];
    }
    public function getFormDataCheck($flow_item_id,$input){
        $result = ['status'=>0 ,'info'=>"表单验证未通过！"];
        $form_item_list = $this->getFormItem($flow_item_id);
        foreach ($form_item_list as $form_item){
            $condition = $form_item["form_item_condition"];
            $rules = explode('|', $condition);
            $value = isset($input[$form_item["form_item_name"]])?$input[$form_item["form_item_name"]]:'';
            foreach ($rules as $rule) {
                $type = '';
                $info = '';
                $flg = false;
                if (strpos($rule, ':')) {
                    list($type, $rule) = explode(':', $rule, 2);
                    if (isset($this->alias[$type])) {
                        // 判断别名
                        $type = $this->alias[$type];
                    }
                    $info = $type;
                } elseif (method_exists($this, $rule)) {
                    $type = $rule;
                    $info = $rule;
                    $rule = '';
                } else {
                    $type = 'is';
                    $info = $rule;
                }
                // 如果不是require 有数据才会行验证
                if (0 === strpos($info, 'require') || (!is_null($value) && '' !== $value)) {
                    // 验证类型
                    $callback = isset(self::$type[$type]) ? self::$type[$type] : [$this, $type];
                    // 验证数据
                    $flg = call_user_func_array($callback, [$value, $rule, $input]);
                } else {
                    $flg = true;
                }
                if(!$flg){
                    $result['info'] = $form_item["form_item_title"]."不符合规则!";
                    $result['data'] = $form_item["form_item_name"];
                    return $result;
                }
            }
        }
        $result['status'] = 1;
        $result['info'] = "表单验证成功！";
        return $result;
    }
    public function getProcessDataArr($data_array){
        $data_idx = [];
        foreach ($data_array as $data){
            $data_idx[$data["process_data_name"]] = $data["process_data_value"];
        }
        return $data_idx;
    }
    public function getFormNewData($flow_item_id,$input_array,$data_idx){
    }
    public function getFormUpdateData($flow_item_id,$input_array,$data_idx){
    }
    public function getFormDataFromArray($flow_item_id,$input_array,$data_idx){
        if(!isset($this->form_item[$flow_item_id])){
            $this->init($flow_item_id);
        }
        $form_data = [];
        var_exp($this->form_item[$flow_item_id],'$this->form_item[$flow_item_id]');
        foreach ($this->form_item[$flow_item_id] as $form_item_name=>$form_item){
            $tmp = '';
            if(isset($input_array[$form_item_name])){
                $tmp = $input_array[$form_item_name];
            }elseif(isset($data_idx[$form_item_name])){
                $tmp = $data_idx[$form_item_name];
            }
            $form_data[$form_item_name] = $tmp;
        }
        return $form_data;
    }

    /**
     * 验证是否大于等于某个值
     * @access protected
     * @param mixed     $value  字段值
     * @param mixed     $rule  验证规则
     * @return bool
     */
    protected function egt($value, $rule)
    {
        return $value >= $rule;
    }

    /**
     * 验证是否大于某个值
     * @access protected
     * @param mixed     $value  字段值
     * @param mixed     $rule  验证规则
     * @return bool
     */
    protected function gt($value, $rule)
    {
        return $value > $rule;
    }

    /**
     * 验证是否小于等于某个值
     * @access protected
     * @param mixed     $value  字段值
     * @param mixed     $rule  验证规则
     * @return bool
     */
    protected function elt($value, $rule)
    {
        return $value <= $rule;
    }

    /**
     * 验证是否小于某个值
     * @access protected
     * @param mixed     $value  字段值
     * @param mixed     $rule  验证规则
     * @return bool
     */
    protected function lt($value, $rule)
    {
        return $value < $rule;
    }

    /**
     * 验证是否等于某个值
     * @access protected
     * @param mixed     $value  字段值
     * @param mixed     $rule  验证规则
     * @return bool
     */
    protected function eq($value, $rule)
    {
        return $value == $rule;
    }

    /**
     * 验证字段值是否为有效格式
     * @access protected
     * @param mixed     $value  字段值
     * @param string    $rule  验证规则
     * @param array     $data  验证数据
     * @return bool
     */
    protected function is($value, $rule, $data = [])
    {
        switch ($rule) {
            case 'require':
                // 必须
                $result = !empty($value) || '0' == $value;
                break;
            case 'accepted':
                // 接受
                $result = in_array($value, ['1', 'on', 'yes']);
                break;
            case 'date':
                // 是否是一个有效日期
                $result = false !== strtotime($value);
                break;
            case 'alpha':
                // 只允许字母
                $result = $this->regex($value, '/^[A-Za-z]+$/');
                break;
            case 'alphaNum':
                // 只允许字母和数字
                $result = $this->regex($value, '/^[A-Za-z0-9]+$/');
                break;
            case 'alphaDash':
                // 只允许字母、数字和下划线 破折号
                $result = $this->regex($value, '/^[A-Za-z0-9\-\_]+$/');
                break;
            case 'chs':
                // 只允许汉字
                $result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}]+$/u');
                break;
            case 'chsAlpha':
                // 只允许汉字、字母
                $result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u');
                break;
            case 'chsAlphaNum':
                // 只允许汉字、字母和数字
                $result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u');
                break;
            case 'chsDash':
                // 只允许汉字、字母、数字和下划线_及破折号-
                $result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\_\-]+$/u');
                break;
            case 'activeUrl':
                // 是否为有效的网址
                $result = checkdnsrr($value);
                break;
            case 'ip':
                // 是否为IP地址
                $result = $this->filter($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
                break;
            case 'url':
                // 是否为一个URL地址
                $result = $this->filter($value, FILTER_VALIDATE_URL);
                break;
            case 'float':
                // 是否为float
                $result = $this->filter($value, FILTER_VALIDATE_FLOAT);
                break;
            case 'number':
                $result = is_numeric($value);
                break;
            case 'integer':
                // 是否为整型
                $result = $this->filter($value, FILTER_VALIDATE_INT);
                break;
            case 'email':
                // 是否为邮箱地址
                $result = $this->filter($value, FILTER_VALIDATE_EMAIL);
                break;
            case 'boolean':
                // 是否为布尔值
                $result = in_array($value, [true, false, 0, 1, '0', '1'], true);
                break;
            case 'array':
                // 是否为数组
                $result = is_array($value);
                break;
            default:
                if (isset(self::$type[$rule])) {
                    // 注册的验证规则
                    $result = call_user_func_array(self::$type[$rule], [$value]);
                } else {
                    // 正则验证
                    $result = $this->regex($value, $rule);
                }
        }
        return $result;
    }

    /**
     * 使用filter_var方式验证
     * @access protected
     * @param mixed     $value  字段值
     * @param mixed     $rule  验证规则
     * @return bool
     */
    protected function filter($value, $rule)
    {
        if (is_string($rule) && strpos($rule, ',')) {
            list($rule, $param) = explode(',', $rule);
        } elseif (is_array($rule)) {
            $param = isset($rule[1]) ? $rule[1] : null;
        } else {
            $param = null;
        }
        return false !== filter_var($value, is_int($rule) ? $rule : filter_id($rule), $param);
    }

    /**
     * 使用正则验证数据
     * @access protected
     * @param mixed     $value  字段值
     * @param mixed     $rule  验证规则 正则规则或者预定义正则名
     * @return mixed
     */
    protected function regex($value, $rule)
    {
        if (isset($this->regex[$rule])) {
            $rule = $this->regex[$rule];
        }
        if (0 !== strpos($rule, '/') && !preg_match('/\/[imsU]{0,4}$/', $rule)) {
            // 不是正则表达式则两端补上/
            $rule = '/^' . $rule . '$/';
        }
        return 1 === preg_match($rule, (string) $value);
    }
}