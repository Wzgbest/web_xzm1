/*
Navicat MySQL Data Transfer

Source Server         : xzm
Source Server Version : 50621
Source Host           : 192.168.102.200:3307
Source Database       : guguocrm

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2017-09-28 14:26:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for guguo_app_alipay_trade
-- ----------------------------
DROP TABLE IF EXISTS `guguo_app_alipay_trade`;
CREATE TABLE `guguo_app_alipay_trade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) DEFAULT NULL COMMENT '公司表id',
  `userid` int(11) DEFAULT NULL COMMENT '用户id',
  `money` int(13) DEFAULT NULL COMMENT '充值金额，单位分',
  `out_trade_no` varchar(64) DEFAULT NULL COMMENT '系统生成的订单号',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '0未充值，1充值成功',
  `pay_time` int(11) DEFAULT NULL COMMENT '充值成功时间',
  `trade_no` varchar(64) DEFAULT NULL COMMENT '支付宝内部订单号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_app_alipay_trade
-- ----------------------------
INSERT INTO `guguo_app_alipay_trade` VALUES ('1', '1', '2', '20000', 'guguo_app_pay1490085658396691', '1490085658', '0', null, null);
INSERT INTO `guguo_app_alipay_trade` VALUES ('2', '1', '2', '20000', 'zhknovel1489997627', '1490086282', '1', '1490147694', null);

-- ----------------------------
-- Table structure for guguo_bank_type
-- ----------------------------
DROP TABLE IF EXISTS `guguo_bank_type`;
CREATE TABLE `guguo_bank_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_bank_type
-- ----------------------------
INSERT INTO `guguo_bank_type` VALUES ('1', '工商银行');
INSERT INTO `guguo_bank_type` VALUES ('2', '建设银行');
INSERT INTO `guguo_bank_type` VALUES ('3', '农业银行');
INSERT INTO `guguo_bank_type` VALUES ('4', '中国银行');
INSERT INTO `guguo_bank_type` VALUES ('5', '招商银行');
INSERT INTO `guguo_bank_type` VALUES ('6', '浦发银行');
INSERT INTO `guguo_bank_type` VALUES ('7', '民生银行');
INSERT INTO `guguo_bank_type` VALUES ('8', '兴业银行');
INSERT INTO `guguo_bank_type` VALUES ('9', '光大银行');
INSERT INTO `guguo_bank_type` VALUES ('10', '广发银行');
INSERT INTO `guguo_bank_type` VALUES ('11', '农村合作银行');
INSERT INTO `guguo_bank_type` VALUES ('12', '农村信用社');
INSERT INTO `guguo_bank_type` VALUES ('13', '中信银行');
INSERT INTO `guguo_bank_type` VALUES ('14', '齐鲁银行');
INSERT INTO `guguo_bank_type` VALUES ('15', '交通银行');
INSERT INTO `guguo_bank_type` VALUES ('16', '华夏银行');
INSERT INTO `guguo_bank_type` VALUES ('17', '邮政储蓄银行');
INSERT INTO `guguo_bank_type` VALUES ('18', '平安银行');

-- ----------------------------
-- Table structure for guguo_corporation
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation`;
CREATE TABLE `guguo_corporation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corp_id` varchar(64) DEFAULT NULL COMMENT '公司标识id,英文，对应分库后缀',
  `corp_name` varchar(80) DEFAULT NULL COMMENT '公司名称，中文',
  `corp_tel` varchar(16) DEFAULT NULL COMMENT '公司电话',
  `corp_address` varchar(128) DEFAULT NULL COMMENT '公司所在地',
  `corp_lat` double(10,6) DEFAULT '0.000000' COMMENT '公司所在地经纬度',
  `corp_lng` double(10,6) DEFAULT '0.000000' COMMENT '公司所在地经纬度',
  `corp_dist` varchar(128) DEFAULT NULL COMMENT '详细定位，具体位置名称',
  `corp_inc` varchar(256) DEFAULT NULL COMMENT '公司工商备案信息',
  `create_time` int(11) DEFAULT NULL COMMENT '公司首次录入时间',
  `corp_legal_person` varchar(128) DEFAULT NULL COMMENT '公司法人',
  `create_ip` varchar(128) DEFAULT NULL,
  `corp_legal_person_tel` varchar(16) DEFAULT NULL,
  `corp_website` varchar(128) DEFAULT NULL COMMENT '公司官网，逗号分隔',
  `corp_fund` bigint(20) DEFAULT NULL,
  `corp_establish_time` int(11) DEFAULT NULL,
  `corp_left_money` bigint(20) unsigned DEFAULT '0' COMMENT '公司账户余额',
  `corp_frozen_money` bigint(20) unsigned DEFAULT '0' COMMENT '公司账户冻结金额',
  `corp_reserved_money` bigint(20) unsigned DEFAULT '0' COMMENT '保留金额(所有员工的公司账户的总额)',
  `corp_reserved_frozen_money` bigint(20) unsigned DEFAULT '0' COMMENT '公司冻结保留金额',
  `corp_field` varchar(64) DEFAULT NULL COMMENT '公司所属行业',
  `corp_product_keys` varchar(160) DEFAULT NULL COMMENT '产品关键词',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation
-- ----------------------------
INSERT INTO `guguo_corporation` VALUES ('1', 'sdzhongxun', '山东中迅网络传媒有限公司', '从事百度旗下产品销售等业务', '山东潍坊潍城区', '36.713175', '119.113755', '金艺大厦', '工商备案号：AAAAAA', '1484209455', '公司法人', '127.0.0.1', '13333336666', 'http://www.baidusd.com', '10000000', '1484209455', '9653200', '1174200', '8600', '1529800', '1', '百度推广,网络建站,baidu');
INSERT INTO `guguo_corporation` VALUES ('2', 'sdzhonghu', '山东中呼信息科技有限公司', '智能400电话,短信平台', '山东潍坊奎文区', '36.713175', '119.113755', '联通大厦', null, null, null, null, null, 'hhhhhhh', null, null, '0', '0', '0', '0', '1', '哈哈哈还是');

-- ----------------------------
-- Table structure for guguo_corporation_cash
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_cash`;
CREATE TABLE `guguo_corporation_cash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) DEFAULT NULL COMMENT '公司表id',
  `money` int(11) DEFAULT NULL COMMENT '金额变动单位分,存入正值，取款负值',
  `create_time` int(11) DEFAULT NULL COMMENT '记录创建时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '1取出  2存入',
  `remark` varchar(45) DEFAULT NULL COMMENT '备注',
  `to_userid` int(11) DEFAULT NULL COMMENT '取现的员工id，针对自己公司员工',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation_cash
-- ----------------------------
INSERT INTO `guguo_corporation_cash` VALUES ('2', '1', '-100', '1490061272', '1', '员工提现', '2');
INSERT INTO `guguo_corporation_cash` VALUES ('3', '1', '-100', '1490061314', '1', '员工提现', '2');
INSERT INTO `guguo_corporation_cash` VALUES ('4', '1', '-100', '1490061342', '1', '员工提现', '2');

-- ----------------------------
-- Table structure for guguo_corporation_field
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_field`;
CREATE TABLE `guguo_corporation_field` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(9) NOT NULL DEFAULT '0',
  `cate_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类类型1一，2二，3三',
  `cate_name` varchar(64) NOT NULL COMMENT '分类名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation_field
-- ----------------------------
INSERT INTO `guguo_corporation_field` VALUES ('1', '0', '1', 'it行业');
INSERT INTO `guguo_corporation_field` VALUES ('2', '1', '2', '硬件');
INSERT INTO `guguo_corporation_field` VALUES ('3', '1', '2', '软件');
INSERT INTO `guguo_corporation_field` VALUES ('4', '2', '3', 'pc机');
INSERT INTO `guguo_corporation_field` VALUES ('5', '2', '3', '平板');
INSERT INTO `guguo_corporation_field` VALUES ('6', '2', '3', '手机');

-- ----------------------------
-- Table structure for guguo_email_smtp
-- ----------------------------
DROP TABLE IF EXISTS `guguo_email_smtp`;
CREATE TABLE `guguo_email_smtp` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `email_preg` varchar(16) NOT NULL COMMENT 'email服务器识别',
  `email_host` varchar(32) DEFAULT NULL COMMENT '邮件类型',
  `smtp_port` mediumint(9) DEFAULT '25' COMMENT '发信服务器端口',
  `smtp_server` varchar(64) NOT NULL COMMENT '发信服务器地址',
  PRIMARY KEY (`id`),
  KEY `email_type` (`email_host`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_email_smtp
-- ----------------------------
INSERT INTO `guguo_email_smtp` VALUES ('1', 'qiye163mx', null, '25', 'smtp.qiye.163.com');
INSERT INTO `guguo_email_smtp` VALUES ('2', 'ym.163', null, '25', 'smtp.ym.163.com');
INSERT INTO `guguo_email_smtp` VALUES ('3', 'sinamail.sina', null, '25', 'smtp.sina.com.cn');
INSERT INTO `guguo_email_smtp` VALUES ('4', 'vip.sina', null, '25', 'smtp.vip.sina.com');
INSERT INTO `guguo_email_smtp` VALUES ('5', '163mx', null, '25', 'smtp.163.com');
INSERT INTO `guguo_email_smtp` VALUES ('6', '126mx', null, '25', 'smtp.126.com');
INSERT INTO `guguo_email_smtp` VALUES ('7', 'yeahmx', null, '25', 'smtp.yeah.net');
INSERT INTO `guguo_email_smtp` VALUES ('8', 'qq.com', null, '25', 'smtp.qq.com');
INSERT INTO `guguo_email_smtp` VALUES ('9', 'mxbiz', null, '25', 'smtp.exmail.qq.com');
INSERT INTO `guguo_email_smtp` VALUES ('10', 'sohumx', null, '25', 'smtp.sohu.com');
INSERT INTO `guguo_email_smtp` VALUES ('11', 'renren.com', null, '25', 'smtp.renren.com');
INSERT INTO `guguo_email_smtp` VALUES ('12', '139.com', null, '25', 'smtp.139.com');
INSERT INTO `guguo_email_smtp` VALUES ('13', 'gmail-smtp', null, '25', 'smtp.gmail.com');
INSERT INTO `guguo_email_smtp` VALUES ('14', 'hotmail.com', null, '25', 'smtp.live.com');
INSERT INTO `guguo_email_smtp` VALUES ('15', 'kaixin001.com', null, '25', 'smtp.kaixin001.com');
INSERT INTO `guguo_email_smtp` VALUES ('16', '189.21cn.com', null, '25', 'smtp.189.cn');
INSERT INTO `guguo_email_smtp` VALUES ('17', 'am0.yahoodns.net', null, '25', 'smtp.yahoo.com');
INSERT INTO `guguo_email_smtp` VALUES ('18', 'cdn.163.net', null, '25', 'smtp.163.net');
INSERT INTO `guguo_email_smtp` VALUES ('19', 'tom.com', null, '25', 'smtp.tom.com');
INSERT INTO `guguo_email_smtp` VALUES ('20', 'mx.aol.com', null, '25', 'smtp.aol.com');
INSERT INTO `guguo_email_smtp` VALUES ('21', '263.net', null, '25', 'smtp.qiye.163.com');
INSERT INTO `guguo_email_smtp` VALUES ('22', 'qiye163mx', null, '25', 'smtp.263.net');
INSERT INTO `guguo_email_smtp` VALUES ('23', 'global-mail.cn', null, '25', 'smtp.global-mail.cn');

-- ----------------------------
-- Table structure for guguo_flow_settings
-- ----------------------------
DROP TABLE IF EXISTS `guguo_flow_settings`;
CREATE TABLE `guguo_flow_settings` (
  `flow_id` tinyint(1) unsigned DEFAULT NULL COMMENT '流程id',
  `flow_name` varchar(32) DEFAULT NULL COMMENT '流程名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_flow_settings
-- ----------------------------
INSERT INTO `guguo_flow_settings` VALUES ('1', '有意向');
INSERT INTO `guguo_flow_settings` VALUES ('2', '上门拜访');
INSERT INTO `guguo_flow_settings` VALUES ('3', '申请合同');
INSERT INTO `guguo_flow_settings` VALUES ('4', '申请成单');
INSERT INTO `guguo_flow_settings` VALUES ('5', '申请发票');

-- ----------------------------
-- Table structure for guguo_live_show
-- ----------------------------
DROP TABLE IF EXISTS `guguo_live_show`;
CREATE TABLE `guguo_live_show` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `show_title` varchar(128) DEFAULT NULL COMMENT '直播标题',
  `show_type` tinyint(1) DEFAULT '1' COMMENT '直播类型1普通视频2在线直播',
  `follower_num` int(11) NOT NULL DEFAULT '0' COMMENT '报名人数',
  `is_fee` tinyint(1) NOT NULL COMMENT '0免费1收费',
  `pay_fee` int(11) DEFAULT NULL COMMENT '收费金额,单位分',
  `show_intro` varchar(255) DEFAULT NULL COMMENT '简介',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_live_show
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_live_show_comment
-- ----------------------------
DROP TABLE IF EXISTS `guguo_live_show_comment`;
CREATE TABLE `guguo_live_show_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corp_id` varchar(64) DEFAULT NULL COMMENT '公司标识',
  `show_id` int(11) NOT NULL COMMENT '直播id',
  `userid` int(11) NOT NULL COMMENT '员工id',
  `content` varchar(255) NOT NULL COMMENT '评论内容',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_live_show_comment
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_user_corporation
-- ----------------------------
DROP TABLE IF EXISTS `guguo_user_corporation`;
CREATE TABLE `guguo_user_corporation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corp_name` varchar(128) DEFAULT NULL COMMENT '公司代号',
  `telephone` varchar(16) DEFAULT NULL COMMENT '用户电话号码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_user_corporation
-- ----------------------------
INSERT INTO `guguo_user_corporation` VALUES ('1', 'sdzhongxun', '13322223333');
INSERT INTO `guguo_user_corporation` VALUES ('2', 'sdzhongxun', '13322221111');
INSERT INTO `guguo_user_corporation` VALUES ('3', 'sdzhongxun', '13311112222');
INSERT INTO `guguo_user_corporation` VALUES ('4', 'sdzhongxun', '13322225555');
INSERT INTO `guguo_user_corporation` VALUES ('5', 'sdzhongxun', '13322226667');
INSERT INTO `guguo_user_corporation` VALUES ('6', 'sdzhongxun', '13311111111');
INSERT INTO `guguo_user_corporation` VALUES ('7', 'sdzhongxun', '13311113333');
INSERT INTO `guguo_user_corporation` VALUES ('8', 'sdzhongxun', '13311115555');
INSERT INTO `guguo_user_corporation` VALUES ('9', 'sdzhongxun', '13311116666');
INSERT INTO `guguo_user_corporation` VALUES ('10', 'sdzhongxun', '13311118888');
INSERT INTO `guguo_user_corporation` VALUES ('11', 'sdzhongxun', '13311119999');
INSERT INTO `guguo_user_corporation` VALUES ('18', 'sdzhongxun', '13399999997');
INSERT INTO `guguo_user_corporation` VALUES ('24', 'sdzhongxun', '13345021406');
INSERT INTO `guguo_user_corporation` VALUES ('25', 'sdzhongxun', '13345021406');
INSERT INTO `guguo_user_corporation` VALUES ('26', 'sdzhongxun', '13345021406');
INSERT INTO `guguo_user_corporation` VALUES ('27', 'sdzhongxun', '13345021406');
INSERT INTO `guguo_user_corporation` VALUES ('28', 'sdzhongxun', '13345021406');
INSERT INTO `guguo_user_corporation` VALUES ('29', 'sdzhongxun', '18012121212');
INSERT INTO `guguo_user_corporation` VALUES ('30', 'sdzhongxun', '13366666666');
INSERT INTO `guguo_user_corporation` VALUES ('33', 'sdzhongxun', '15678987656');
INSERT INTO `guguo_user_corporation` VALUES ('34', 'sdzhongxun', '15678987654');
INSERT INTO `guguo_user_corporation` VALUES ('35', 'sdzhongxun', '15656565656');
INSERT INTO `guguo_user_corporation` VALUES ('37', 'sdzhongxun', '15698765432');
INSERT INTO `guguo_user_corporation` VALUES ('38', 'sdzhongxun', '18618888888');
INSERT INTO `guguo_user_corporation` VALUES ('39', 'sdzhongxun', '15612323211');
INSERT INTO `guguo_user_corporation` VALUES ('50', 'sdzhongxun', '15858585518');
INSERT INTO `guguo_user_corporation` VALUES ('55', 'sdzhongxun', '15655558888');
INSERT INTO `guguo_user_corporation` VALUES ('56', 'sdzhongxun', '13322227777');
INSERT INTO `guguo_user_corporation` VALUES ('63', 'sdzhongxun', '15556565667');
INSERT INTO `guguo_user_corporation` VALUES ('64', 'sdzhongxun', '15591919191');
INSERT INTO `guguo_user_corporation` VALUES ('65', 'sdzhongxun', '15612344321');
INSERT INTO `guguo_user_corporation` VALUES ('69', 'sdzhongxun', '18756788765');
INSERT INTO `guguo_user_corporation` VALUES ('70', 'sdzhongxun', '15809877890');
SET FOREIGN_KEY_CHECKS=1;
