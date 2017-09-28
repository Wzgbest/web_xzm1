/*
Navicat MySQL Data Transfer

Source Server         : xzm
Source Server Version : 50621
Source Host           : 192.168.102.200:3307
Source Database       : guguo_sdzhongxun

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2017-09-28 14:26:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for guguo_bill_setting
-- ----------------------------
DROP TABLE IF EXISTS `guguo_bill_setting`;
CREATE TABLE `guguo_bill_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_type` varchar(32) NOT NULL COMMENT '发票类型',
  `need_tax_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否需要公司税号',
  `product_type` varchar(128) DEFAULT NULL COMMENT '产品类型id，逗号分隔',
  `bank_type` varchar(128) DEFAULT NULL COMMENT '银行类型',
  `max_bill` decimal(13,2) DEFAULT NULL COMMENT '最大发票金额，单位元',
  `handle_1` int(11) NOT NULL COMMENT '一审人，角色',
  `handle_2` int(11) DEFAULT NULL COMMENT '二审人，角色',
  `handle_3` int(11) DEFAULT NULL COMMENT '三审人，角色',
  `handle_4` int(11) DEFAULT NULL COMMENT '四审人，角色',
  `handle_5` int(11) DEFAULT NULL COMMENT '五审人，角色',
  `handle_6` int(11) DEFAULT NULL COMMENT '六审人，角色',
  `create_bill_num_1` int(11) DEFAULT '0' COMMENT '一审是否生成发票号，0否',
  `create_bill_num_2` int(11) DEFAULT '0' COMMENT '二审是否生成发票号，0否',
  `create_bill_num_3` int(11) DEFAULT '0' COMMENT '三审是否生成发票号，0否',
  `create_bill_num_4` int(11) DEFAULT '0' COMMENT '四审是否生成发票号，0否',
  `create_bill_num_5` int(11) DEFAULT '0' COMMENT '五审是否生成发票号，0否',
  `create_bill_num_6` int(11) DEFAULT '0' COMMENT '六审是否生成发票号，0否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_bill_setting
-- ----------------------------
INSERT INTO `guguo_bill_setting` VALUES ('1', '建站类', '1', 'pc,mobi,wx', '阿斯顿,的撒', '123.00', '6', '5', '4', '0', '0', '0', '1', '0', '0', '0', '0', '0');
INSERT INTO `guguo_bill_setting` VALUES ('2', '百度类', '0', '大搜', '工商银行', '0.00', '6', '5', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0');
INSERT INTO `guguo_bill_setting` VALUES ('3', '增值类', '0', '商城', '建设银行', '0.00', '6', '5', '4', '2', '1', '1', '1', '0', '1', '1', '0', '0');

-- ----------------------------
-- Table structure for guguo_business
-- ----------------------------
DROP TABLE IF EXISTS `guguo_business`;
CREATE TABLE `guguo_business` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '行业id',
  `business_name` varchar(32) NOT NULL COMMENT '行业名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_business
-- ----------------------------
INSERT INTO `guguo_business` VALUES ('1', 'IT行业');
INSERT INTO `guguo_business` VALUES ('2', '金融行业');
INSERT INTO `guguo_business` VALUES ('3', '制造业');

-- ----------------------------
-- Table structure for guguo_business_flow_item
-- ----------------------------
DROP TABLE IF EXISTS `guguo_business_flow_item`;
CREATE TABLE `guguo_business_flow_item` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务id',
  `item_name` varchar(32) NOT NULL COMMENT '业务名称',
  `have_verification` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否有审核',
  `verification_name` varchar(16) NOT NULL COMMENT '审核名称',
  `verification_remark` varchar(255) DEFAULT NULL COMMENT '审核备注',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型,1:可选择流程,0:固定流程',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_business_flow_item
-- ----------------------------
INSERT INTO `guguo_business_flow_item` VALUES ('1', '有意向', '0', '', null, '1', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('2', '预约拜访', '0', '', null, '1', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('3', '上门拜访', '0', '', '（App签到后自动更新）', '1', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('4', '申请成单', '1', '成单审核', '（申请成单流程在成交合同审核通过后）', '1', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('5', '赢单', '0', ' ', '', '0', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('6', '输单', '0', ' ', null, '0', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('7', '作废', '0', ' ', null, '0', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('8', '发票申请', '0', ' ', null, '0', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('9', '已退款', '0', ' ', null, '0', '1');
INSERT INTO `guguo_business_flow_item` VALUES ('10', '无意向', '0', ' ', null, '0', '1');

-- ----------------------------
-- Table structure for guguo_business_flow_item_link
-- ----------------------------
DROP TABLE IF EXISTS `guguo_business_flow_item_link`;
CREATE TABLE `guguo_business_flow_item_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务id',
  `setting_id` int(11) NOT NULL COMMENT '业务流设置id',
  `item_id` int(11) NOT NULL COMMENT '审核条目id',
  `order_num` tinyint(3) unsigned NOT NULL COMMENT '顺序',
  `handle_1` int(11) NOT NULL COMMENT '一审人，角色',
  `handle_2` int(11) DEFAULT '0' COMMENT '二审人，角色',
  `handle_3` int(11) DEFAULT '0' COMMENT '三审人，角色',
  `handle_4` int(11) DEFAULT '0' COMMENT '四审人，角色',
  `handle_5` int(11) DEFAULT '0' COMMENT '五审人，角色',
  `handle_6` int(11) DEFAULT '0' COMMENT '六审人，角色',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_business_flow_item_link
-- ----------------------------
INSERT INTO `guguo_business_flow_item_link` VALUES ('1', '1', '1', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('2', '1', '2', '2', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('3', '1', '3', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('4', '1', '4', '4', '6', '5', '4', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('9', '3', '1', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('10', '3', '2', '2', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('11', '3', '3', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('12', '3', '4', '4', '8', '7', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('13', '4', '1', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('14', '4', '2', '2', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('15', '4', '3', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('16', '4', '4', '4', '7', '6', '5', '4', '3', '2');
INSERT INTO `guguo_business_flow_item_link` VALUES ('120', '2', '1', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('121', '2', '4', '2', '6', '5', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('129', '7', '1', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('130', '7', '2', '2', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('131', '7', '3', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO `guguo_business_flow_item_link` VALUES ('132', '7', '4', '4', '8', '7', '6', '5', '4', '3');

-- ----------------------------
-- Table structure for guguo_business_flow_setting
-- ----------------------------
DROP TABLE IF EXISTS `guguo_business_flow_setting`;
CREATE TABLE `guguo_business_flow_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务id',
  `business_flow_name` varchar(32) NOT NULL COMMENT '业务名称',
  `set_to_role` varchar(255) NOT NULL COMMENT '拥有业务的角色',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_business_flow_setting
-- ----------------------------
INSERT INTO `guguo_business_flow_setting` VALUES ('1', '百度大搜', '3,4,5');
INSERT INTO `guguo_business_flow_setting` VALUES ('2', '百度推广', '1,6');
INSERT INTO `guguo_business_flow_setting` VALUES ('3', '建站', '2,3,4');
INSERT INTO `guguo_business_flow_setting` VALUES ('4', '微信', '1,2,3');
INSERT INTO `guguo_business_flow_setting` VALUES ('7', '实习项目', '1,2,3,8');

-- ----------------------------
-- Table structure for guguo_call_note
-- ----------------------------
DROP TABLE IF EXISTS `guguo_call_note`;
CREATE TABLE `guguo_call_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `call_id` int(11) NOT NULL COMMENT '通话记录id',
  `cut_name` varchar(64) DEFAULT NULL COMMENT '标签名称',
  `start_time` int(11) DEFAULT NULL COMMENT '开始时间',
  `create_time` int(11) DEFAULT NULL COMMENT '记录创建时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '0删除1正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_call_note
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_call_record
-- ----------------------------
DROP TABLE IF EXISTS `guguo_call_record`;
CREATE TABLE `guguo_call_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '员工id',
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `contactor_id` int(11) DEFAULT NULL COMMENT '联系人id',
  `is_customer` tinyint(1) NOT NULL COMMENT '1客户电话，0联系人',
  `main_phone` varchar(13) NOT NULL COMMENT '主通话号码',
  `sub_phone` varchar(13) DEFAULT NULL COMMENT '辅助通话号码',
  `call_type` tinyint(1) DEFAULT NULL COMMENT '通话类型，1普通，2三方，3协助',
  `begin_time` int(11) DEFAULT NULL COMMENT '开始通话时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束通话时间',
  `call_direction` tinyint(1) DEFAULT '1' COMMENT '通话方向，1拨出，2呼入',
  `connected` tinyint(1) unsigned DEFAULT '0' COMMENT '是否接通',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_call_record
-- ----------------------------
INSERT INTO `guguo_call_record` VALUES ('1', '3', '11', null, '1', '18769714760', null, '1', '1498547154', '1498548155', '1', null);
INSERT INTO `guguo_call_record` VALUES ('2', '5', '4', null, '1', '18769714760', null, '1', '1498549155', '1498557156', '1', null);
INSERT INTO `guguo_call_record` VALUES ('3', '3', '17', null, '1', '13311112222', null, '1', '1498567156', '1498577157', '1', '0');
INSERT INTO `guguo_call_record` VALUES ('4', '5', '21', null, '1', '13311113333', null, '1', '1498587157', '1498597158', '1', '0');
INSERT INTO `guguo_call_record` VALUES ('5', '5', '27', null, '1', '13322223333', null, '1', '1498597158', '1498697159', '1', '0');
INSERT INTO `guguo_call_record` VALUES ('6', '2', '3', null, '1', '13322221111', null, '1', '1504491820', '1504491830', '1', '0');
INSERT INTO `guguo_call_record` VALUES ('7', '2', '76', null, '1', '13322221111', null, '1', '1504492685', '1504492695', '1', '0');

-- ----------------------------
-- Table structure for guguo_cloud_file
-- ----------------------------
DROP TABLE IF EXISTS `guguo_cloud_file`;
CREATE TABLE `guguo_cloud_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(128) NOT NULL COMMENT '文件名称',
  `file_type` tinyint(1) NOT NULL COMMENT '1文本文件 2图片 3音视频 4 office文档',
  `file_md5` char(32) DEFAULT NULL,
  `file_sha1` char(40) DEFAULT NULL,
  `file_size` int(11) NOT NULL COMMENT '文件大小单位B',
  `file_path` varchar(128) DEFAULT NULL COMMENT '文件路径',
  `business_id` mediumint(9) DEFAULT NULL COMMENT '业务id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_cloud_file
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_contract
-- ----------------------------
DROP TABLE IF EXISTS `guguo_contract`;
CREATE TABLE `guguo_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applied_id` int(11) NOT NULL COMMENT '合同申请id',
  `contract_no` varchar(64) NOT NULL COMMENT '合同号',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) DEFAULT NULL COMMENT '生成时间',
  `group_field` varchar(128) DEFAULT '' COMMENT '分组字段',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '合同状态，1已通过，4待领取，5已领取，6已作废，7已收回,8已提醒,9已退款',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='合同号';

-- ----------------------------
-- Records of guguo_contract
-- ----------------------------
INSERT INTO `guguo_contract` VALUES ('1', '1', 'bdnm1', '1501729201', '1501729201', 'bdnm1', '5');
INSERT INTO `guguo_contract` VALUES ('2', '1', 'bdnm2', '1501729201', '1501729201', 'bdnm2', '5');
INSERT INTO `guguo_contract` VALUES ('3', '1', 'bdnm3', '1501729201', '1501729201', 'bdnm3', '4');
INSERT INTO `guguo_contract` VALUES ('4', '2', 'zxjz1', '1501729201', '1501729201', 'zxjz1', '4');
INSERT INTO `guguo_contract` VALUES ('5', '2', 'zxjz2', '1501729201', '1501729201', 'zxjz2', '4');
INSERT INTO `guguo_contract` VALUES ('7', '4', 'dbdt1000', '1501736030', '1501736030', 'dbdt1000', '5');
INSERT INTO `guguo_contract` VALUES ('8', '4', 'dbdt1001', '1501736030', '1501736030', 'dbdt1001', '5');
INSERT INTO `guguo_contract` VALUES ('9', '4', 'dbdt1002', '1501736030', '1501736030', 'dbdt1002', '5');
INSERT INTO `guguo_contract` VALUES ('10', '4', 'dbdt1003', '1501736030', '1501736030', 'dbdt1003', '5');
INSERT INTO `guguo_contract` VALUES ('14', '5', 'dbdt1004', '1501736311', '1501736311', 'dbdt1004', '5');
INSERT INTO `guguo_contract` VALUES ('15', '5', 'dbdt1005', '1501736311', '1501736311', 'dbdt1005', '5');
INSERT INTO `guguo_contract` VALUES ('17', '6', 'dbds10000', '1501745719', '1501745719', 'dbds10000', '7');
INSERT INTO `guguo_contract` VALUES ('18', '6', 'dbds10001', '1501745719', '1501745719', 'dbds10001', '7');
INSERT INTO `guguo_contract` VALUES ('20', '6', 'dbds10002', '1501745724', '1501745724', 'dbds10002', '5');
INSERT INTO `guguo_contract` VALUES ('21', '6', 'dbds10003', '1501745724', '1501745724', 'dbds10003', '5');
INSERT INTO `guguo_contract` VALUES ('23', '7', 'dbdt1006', '1501919936', '1501919936', 'dbdt1006', '5');
INSERT INTO `guguo_contract` VALUES ('24', '7', 'dbdt1007', '1501919936', '1501919936', 'dbdt1007', '5');
INSERT INTO `guguo_contract` VALUES ('25', '8', 'dbds10004', '1501922630', '1501922630', 'dbds10004', '5');
INSERT INTO `guguo_contract` VALUES ('26', '8', 'dbds10005', '1501922630', '1501922630', 'dbds10005', '5');
INSERT INTO `guguo_contract` VALUES ('27', '10', 'bdnm4', '1501999178', '1501999178', 'bdnm4', '5');
INSERT INTO `guguo_contract` VALUES ('28', '11', 'bdnm5', '1501999817', '1501999817', 'bdnm5', '5');
INSERT INTO `guguo_contract` VALUES ('29', '11', 'bdnm6', '1501999817', '1501999817', 'bdnm6', '5');
INSERT INTO `guguo_contract` VALUES ('30', '11', 'bdnm7', '1501999817', '1501999817', 'bdnm7', '5');
INSERT INTO `guguo_contract` VALUES ('31', '17', 'zxjz3', '1502066033', '1502066033', 'zxjz3', '4');
INSERT INTO `guguo_contract` VALUES ('32', '18', 'bdbk1', '1502067535', '1502067535', 'bdbk1', '5');
INSERT INTO `guguo_contract` VALUES ('33', '18', 'bdbk2', '1502067535', '1502067535', 'bdbk2', '4');
INSERT INTO `guguo_contract` VALUES ('35', '22', 'bdbk3', '1502152095', '1502152095', 'bdbk3', '4');
INSERT INTO `guguo_contract` VALUES ('36', '22', 'bdbk4', '1502152095', '1502152095', 'bdbk4', '4');
INSERT INTO `guguo_contract` VALUES ('37', '26', 'zxjz4', '1502323238', '1502323238', 'zxjz4', '4');
INSERT INTO `guguo_contract` VALUES ('38', '27', 'bdnm8', '1502323300', '1502323300', 'bdnm8', '4');
INSERT INTO `guguo_contract` VALUES ('39', '29', 'zxjz5', '1502328541', '1502328541', 'zxjz5', '5');
INSERT INTO `guguo_contract` VALUES ('40', '29', 'zxjz6', '1502328541', '1502328541', 'zxjz6', '5');
INSERT INTO `guguo_contract` VALUES ('41', '29', 'zxjz7', '1502328541', '1502328541', 'zxjz7', '5');
INSERT INTO `guguo_contract` VALUES ('42', '35', 'bdbk5', '1504839803', '1504839803', 'bdbk5', '5');
INSERT INTO `guguo_contract` VALUES ('43', '34', 'zxjz8', '1504839838', '1504839838', 'zxjz8', '5');
INSERT INTO `guguo_contract` VALUES ('44', '43', 'bdnm9', '1504854449', '1504854449', 'bdnm9', '5');
INSERT INTO `guguo_contract` VALUES ('45', '44', 'bdbk6', '1504855056', '1504855056', 'bdbk6', '5');
INSERT INTO `guguo_contract` VALUES ('46', '44', 'bdbk7', '1504855056', '1504855056', 'bdbk7', '5');
INSERT INTO `guguo_contract` VALUES ('48', '42', 'dbds10006', '1504855065', '1504855065', '', '4');
INSERT INTO `guguo_contract` VALUES ('49', '45', 'zxjz9', '1504855833', '1504855833', 'zxjz9', '5');
INSERT INTO `guguo_contract` VALUES ('50', '49', 'zxjz10', '1505178940', '1505178940', 'zxjz10', '5');
INSERT INTO `guguo_contract` VALUES ('51', '49', 'zxjz11', '1505178940', '1505178940', 'zxjz11', '5');
INSERT INTO `guguo_contract` VALUES ('53', '57', 'bdds10007', '1505357160', '1505357160', 'bdds10007', '4');
INSERT INTO `guguo_contract` VALUES ('54', '57', 'bdds10008', '1505357160', '1505357160', 'bdds10008', '4');
INSERT INTO `guguo_contract` VALUES ('56', '56', 'bdbk8', '1505359326', '1505359326', '', '4');
INSERT INTO `guguo_contract` VALUES ('57', '56', 'bdbk9', '1505359326', '1505359326', '', '4');
INSERT INTO `guguo_contract` VALUES ('59', '60', 'dbdt1008', '1505467577', '1505467577', 'dbdt1008', '5');
INSERT INTO `guguo_contract` VALUES ('60', '59', 'zxjz12', '1505467718', '1505467718', 'zxjz12', '5');
INSERT INTO `guguo_contract` VALUES ('61', '58', 'bdnm10', '1505467731', '1505467731', 'bdnm10', '5');
INSERT INTO `guguo_contract` VALUES ('62', '61', 'zxjz13', '1506560537', '1506560537', 'zxjz13', '5');
INSERT INTO `guguo_contract` VALUES ('63', '61', 'zxjz14', '1506560537', '1506560537', 'zxjz14', '5');
INSERT INTO `guguo_contract` VALUES ('64', '61', 'zxjz15', '1506560537', '1506560537', 'zxjz15', '5');
INSERT INTO `guguo_contract` VALUES ('65', '61', 'zxjz16', '1506560537', '1506560537', 'zxjz16', '5');
INSERT INTO `guguo_contract` VALUES ('66', '61', 'zxjz17', '1506560537', '1506560537', 'zxjz17', '5');

-- ----------------------------
-- Table structure for guguo_contract_applied
-- ----------------------------
DROP TABLE IF EXISTS `guguo_contract_applied`;
CREATE TABLE `guguo_contract_applied` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(128) NOT NULL COMMENT '申请合同人id',
  `contract_type` smallint(6) DEFAULT NULL COMMENT '合同类型id',
  `contract_num` varchar(32) NOT NULL COMMENT '合同数量',
  `contract_apply_1` varchar(255) NOT NULL COMMENT '一审人',
  `contract_apply_2` varchar(255) DEFAULT '0' COMMENT '二审人',
  `contract_apply_3` varchar(255) DEFAULT '0' COMMENT '三审人',
  `contract_apply_4` varchar(255) DEFAULT '0' COMMENT '四审人',
  `contract_apply_5` varchar(255) DEFAULT '0' COMMENT '五审人',
  `contract_apply_6` varchar(255) DEFAULT '0' COMMENT '六审人',
  `contract_apply_status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '当前审核步骤',
  `contract_apply_now` varchar(255) NOT NULL COMMENT '当前审核人',
  `remind_num` tinyint(4) unsigned DEFAULT '0' COMMENT '提醒次数',
  `remark` varchar(255) DEFAULT '',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) DEFAULT NULL COMMENT '申请合同时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '申请后的合同状态，0审核中，1已通过，2已驳回，3已撤回',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COMMENT='合同申请，销售机会成单申请';

-- ----------------------------
-- Records of guguo_contract_applied
-- ----------------------------
INSERT INTO `guguo_contract_applied` VALUES ('1', '3', '5', '3', '72', '0', '0', '0', '0', '0', '1', '72', '0', null, '1501658444', '1501658444', '1');
INSERT INTO `guguo_contract_applied` VALUES ('2', '8', '4', '2', '85', '0', '0', '0', '0', '0', '1', '72', '0', null, '1501658444', '1501658444', '1');
INSERT INTO `guguo_contract_applied` VALUES ('4', '3', '3', '4', '72', '90', '4', '0', '0', '0', '3', '72', '0', null, '1501735611', '1501735611', '1');
INSERT INTO `guguo_contract_applied` VALUES ('5', '3', '3', '2', '85', '90', '4', '0', '0', '0', '2', '72', '0', null, '1501736302', '1501736302', '1');
INSERT INTO `guguo_contract_applied` VALUES ('6', '3', '1', '2', '72', '90', '4', '5', '1', '0', '5', '72', '0', null, '1501742987', '1501742987', '1');
INSERT INTO `guguo_contract_applied` VALUES ('7', '3', '3', '2', '85', '0', '0', '0', '0', '0', '1', '72', '0', null, '1501809863', '1501809863', '1');
INSERT INTO `guguo_contract_applied` VALUES ('8', '3', '1', '2', '72', '90', '4', '1', '6', '0', '5', '72', '0', null, '1501922545', '1501922545', '1');
INSERT INTO `guguo_contract_applied` VALUES ('9', '3', '5', '1', '72', '0', '0', '0', '0', '0', '1', '72', '0', null, '1501928885', '1501928885', '3');
INSERT INTO `guguo_contract_applied` VALUES ('10', '3', '5', '1', '72', '90', '4', '0', '0', '0', '3', '72', '0', null, '1501929723', '1501929723', '1');
INSERT INTO `guguo_contract_applied` VALUES ('11', '3', '5', '3', '72', '90', '4', '0', '0', '0', '3', '72', '0', '', '1501999624', '1501999624', '1');
INSERT INTO `guguo_contract_applied` VALUES ('12', '3', '5', '2', '72', '90', '4', '0', '0', '0', '1', '72', '0', '', '1502021924', '1502021924', '3');
INSERT INTO `guguo_contract_applied` VALUES ('13', '3', '5', '2', '72', '90', '4', '0', '0', '0', '1', '72', '0', '', '1502021957', '1502021957', '2');
INSERT INTO `guguo_contract_applied` VALUES ('14', '3', '5', '2', '72', '90', '4', '0', '0', '0', '1', '72', '0', '', '1502022803', '1502022803', '0');
INSERT INTO `guguo_contract_applied` VALUES ('15', '3', '5', '2', '72', '90', '4', '0', '0', '0', '1', '72', '0', '', '1502065462', '1502065462', '0');
INSERT INTO `guguo_contract_applied` VALUES ('16', '8', '2', '2', '85', '10', '0', '0', '0', '0', '1', '72', '0', '', '1502065507', '1502065507', '2');
INSERT INTO `guguo_contract_applied` VALUES ('17', '8', '4', '1', '72', '5', '4', '0', '0', '0', '3', '72', '0', '', '1502065971', '1502065971', '1');
INSERT INTO `guguo_contract_applied` VALUES ('18', '8', '2', '2', '72', '90', '0', '0', '0', '0', '2', '72', '0', '', '1502067494', '1502067494', '1');
INSERT INTO `guguo_contract_applied` VALUES ('19', '8', '3', '2', '72', '90', '4', '8', '0', '0', '1', '72', '0', '', '1502151546', '1502151546', '3');
INSERT INTO `guguo_contract_applied` VALUES ('20', '8', '3', '1', '72', '90', '4', '8', '0', '0', '2', '72', '0', '', '1502151546', '1502151546', '2');
INSERT INTO `guguo_contract_applied` VALUES ('22', '8', '2', '2', '72', '90', '0', '0', '0', '0', '2', '72', '0', '', '1502152045', '1502152045', '1');
INSERT INTO `guguo_contract_applied` VALUES ('23', '8', '2', '1', '85', '85', '0', '0', '0', '0', '1', '72', '0', '', '1502152045', '1502152045', '3');
INSERT INTO `guguo_contract_applied` VALUES ('25', '3', '5', '2', '72,3', '90,3', '4,3', '0', '0', '0', '2', '72', '0', '散发的打赏;', '1502249364', '1502249364', '0');
INSERT INTO `guguo_contract_applied` VALUES ('26', '5', '4', '1', '85', '5', '4', '0', '0', '0', '3', '72', '0', '审核备注测试1;审核备注测试2;审核备注测试3;', '1502323021', '1502323021', '1');
INSERT INTO `guguo_contract_applied` VALUES ('27', '8', '5', '1', '72', '90', '4', '0', '0', '0', '3', '72', '0', '备注测试1;备注测试2;备注测试3 ;', '1502323220', '1502323220', '1');
INSERT INTO `guguo_contract_applied` VALUES ('28', '3', '5', '2', '72', '72', '72', '0', '0', '0', '1', '72', '0', '', '1502328363', '1502328363', '0');
INSERT INTO `guguo_contract_applied` VALUES ('29', '3', '4', '3', '72', '72', '72', '0', '0', '0', '3', '72', '0', '', '1502328363', '1502328363', '1');
INSERT INTO `guguo_contract_applied` VALUES ('31', '8', '5', '1', '72', '72', '72', '0', '0', '0', '1', '72', '0', '', '1502505041', '1502505041', '0');
INSERT INTO `guguo_contract_applied` VALUES ('32', '3', '5', '3', '72', '72', '72', '0', '0', '0', '1', '72', '0', '', '1502505202', '1502505202', '0');
INSERT INTO `guguo_contract_applied` VALUES ('33', '8', '5', '1', '72', '72', '72', '0', '0', '0', '1', '72', '0', '', '1502673230', '1502673230', '2');
INSERT INTO `guguo_contract_applied` VALUES ('34', '3', '4', '1', '72', '72', '72', '0', '0', '0', '3', '72', '0', '', '1504839534', '1504839534', '1');
INSERT INTO `guguo_contract_applied` VALUES ('35', '3', '2', '1', '72', '72', '0', '0', '0', '0', '2', '72', '0', '', '1504839534', '1504839534', '1');
INSERT INTO `guguo_contract_applied` VALUES ('37', '9', '5', '1', '请选择', '请选择', '请选择', '0', '0', '0', '1', '请选择', '0', '', '1504852670', '1504852670', '0');
INSERT INTO `guguo_contract_applied` VALUES ('38', '9', '3', '1', '请选择', '请选择', '请选择', '请选择', '0', '0', '1', '请选择', '0', '', '1504852670', '1504852670', '0');
INSERT INTO `guguo_contract_applied` VALUES ('40', '72', '3', '1', '72', '72', '请选择', '请选择', '0', '0', '1', '72', '0', '', '1504853356', '1504853356', '2');
INSERT INTO `guguo_contract_applied` VALUES ('41', '72', '3', '1', '72', '72', '72', '72', '0', '0', '3', '72', '0', '', '1504853443', '1504853443', '2');
INSERT INTO `guguo_contract_applied` VALUES ('42', '9', '1', '1', '72', '72', '72', '72', '请选择', '0', '5', '请选择', '0', '', '1504853620', '1504853620', '0');
INSERT INTO `guguo_contract_applied` VALUES ('43', '9', '5', '1', '72', '72', '72', '0', '0', '0', '3', '72', '0', '', '1504854245', '1504854245', '1');
INSERT INTO `guguo_contract_applied` VALUES ('44', '9', '2', '2', '72', '72', '0', '0', '0', '0', '2', '72', '0', '', '1504855040', '1504855040', '1');
INSERT INTO `guguo_contract_applied` VALUES ('45', '9', '4', '1', '72', '72', '72', '0', '0', '0', '3', '72', '0', '', '1504855811', '1504855811', '1');
INSERT INTO `guguo_contract_applied` VALUES ('46', '12', '5', '2', '12', '12', '12', '0', '0', '0', '2', '请选择', '0', '', '1505178315', '1505178315', '0');
INSERT INTO `guguo_contract_applied` VALUES ('47', '12', '5', '2', '72', '72', '72', '0', '0', '0', '2', '72', '0', '', '1505178331', '1505178331', '2');
INSERT INTO `guguo_contract_applied` VALUES ('48', '12', '4', '2', '12', '12', '12', '0', '0', '0', '1', '请选择', '0', '', '1505178896', '1505178896', '0');
INSERT INTO `guguo_contract_applied` VALUES ('49', '12', '4', '2', '72', '72', '72', '0', '0', '0', '3', '72', '0', '', '1505178920', '1505178920', '1');
INSERT INTO `guguo_contract_applied` VALUES ('50', '12', '3', '2', '72', '72', '72', '72', '0', '0', '1', '72', '0', '', '1505292805', '1505292805', '0');
INSERT INTO `guguo_contract_applied` VALUES ('51', '12', '5', '2', '85', '85', '72', '0', '0', '0', '1', '85', '0', '', '1505292831', '1505292831', '0');
INSERT INTO `guguo_contract_applied` VALUES ('52', '12', '1', '2', '72', '10', '72', '6', '12', '0', '1', '72', '0', '', '1505294512', '1505294512', '0');
INSERT INTO `guguo_contract_applied` VALUES ('53', '12', '5', '2', '72', '72', '72', '0', '0', '0', '1', '72', '0', '', '1505354226', '1505354226', '0');
INSERT INTO `guguo_contract_applied` VALUES ('54', '12', '4', '2', '72', '72', '72', '0', '0', '0', '1', '72', '0', '', '1505354239', '1505354239', '0');
INSERT INTO `guguo_contract_applied` VALUES ('55', '12', '3', '2', '72', '72', '72', '72', '0', '0', '1', '72', '0', '', '1505354251', '1505354251', '0');
INSERT INTO `guguo_contract_applied` VALUES ('56', '12', '2', '2', '72', '72', '0', '0', '0', '0', '2', '72', '0', '', '1505354263', '1505354263', '0');
INSERT INTO `guguo_contract_applied` VALUES ('57', '12', '1', '2', '72', '72', '72', '72', '72', '0', '5', '72', '0', '', '1505354289', '1505354289', '1');
INSERT INTO `guguo_contract_applied` VALUES ('58', '8', '5', '1', '72', '72', '72', '0', '0', '0', '3', '72', '0', '', '1505467410', '1505467410', '1');
INSERT INTO `guguo_contract_applied` VALUES ('59', '8', '4', '1', '72', '72', '72', '0', '0', '0', '3', '72', '0', '', '1505467515', '1505467515', '1');
INSERT INTO `guguo_contract_applied` VALUES ('60', '8', '3', '1', '72', '72', '72', '72', '0', '0', '4', '72', '0', '', '1505467515', '1505467515', '1');
INSERT INTO `guguo_contract_applied` VALUES ('61', '3', '4', '5', '72', '72', '72', '0', '0', '0', '3', '72', '0', '', '1506560557', '1506560557', '1');

-- ----------------------------
-- Table structure for guguo_contract_setting
-- ----------------------------
DROP TABLE IF EXISTS `guguo_contract_setting`;
CREATE TABLE `guguo_contract_setting` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `contract_name` varchar(32) NOT NULL COMMENT '合同名称',
  `contract_prefix` varchar(32) DEFAULT NULL COMMENT '合同编号前缀',
  `start_num` bigint(16) NOT NULL COMMENT '合同编号起始',
  `end_num` bigint(16) NOT NULL COMMENT '合同编号结束',
  `current_contract` varchar(11) DEFAULT NULL COMMENT '当前合同号',
  `max_apply` mediumint(11) DEFAULT NULL COMMENT '最大申请合同数',
  `apply_1` mediumint(11) NOT NULL COMMENT '一审人，角色',
  `apply_2` mediumint(11) DEFAULT NULL COMMENT '二审，角色',
  `apply_3` mediumint(11) DEFAULT NULL COMMENT '三审，角色',
  `apply_4` mediumint(11) DEFAULT NULL COMMENT '四审，角色',
  `apply_5` mediumint(11) DEFAULT NULL COMMENT '五审，角色',
  `apply_6` mediumint(11) DEFAULT NULL COMMENT '六审，角色',
  `create_contract_num_1` tinyint(1) DEFAULT '0' COMMENT '一审是否生成合同号，0否',
  `create_contract_num_2` tinyint(1) DEFAULT '0' COMMENT '二审是否生成合同号，0否',
  `create_contract_num_3` tinyint(1) DEFAULT '0' COMMENT '三审是否生成合同号，0否',
  `create_contract_num_4` tinyint(1) DEFAULT '0' COMMENT '四审是否生成合同号，0否',
  `create_contract_num_5` tinyint(1) DEFAULT '0' COMMENT '五审是否生成合同号，0否',
  `create_contract_num_6` tinyint(1) DEFAULT '0' COMMENT '六审是否生成合同号，0否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='用于系统设置--业务流---合同设置';

-- ----------------------------
-- Records of guguo_contract_setting
-- ----------------------------
INSERT INTO `guguo_contract_setting` VALUES ('1', '百度大搜类合同', 'bdds', '10000', '99999', '10009', '0', '6', '5', '4', '2', '1', '0', '0', '0', '1', '0', '0', '0');
INSERT INTO `guguo_contract_setting` VALUES ('2', '百度百科类合同', 'bdbk', '1', '55', '10', '0', '6', '5', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0');
INSERT INTO `guguo_contract_setting` VALUES ('3', '百度地图类合同', 'dbdt', '1000', '9999', '1009', '0', '6', '5', '4', '3', '0', '0', '0', '0', '1', '0', '0', '0');
INSERT INTO `guguo_contract_setting` VALUES ('4', '建站类合同', 'zxjz', '1', '100', '18', '0', '6', '5', '4', '0', '0', '0', '0', '1', '0', '0', '0', '0');
INSERT INTO `guguo_contract_setting` VALUES ('5', '糯米类合同', 'bdnm', '1', '999', '11', '6', '6', '5', '4', '0', '0', '0', '0', '1', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for guguo_corporation_share
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_share`;
CREATE TABLE `guguo_corporation_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '原始状态id，默认0，转发的id为来源id',
  `userid` int(11) NOT NULL COMMENT '用户id',
  `content_id` int(11) NOT NULL COMMENT '内容',
  `business_id` mediumint(9) DEFAULT NULL COMMENT '业务id',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `good_count` mediumint(9) DEFAULT '0' COMMENT '点赞数',
  `return_count` mediumint(9) DEFAULT '0' COMMENT '转发数',
  `rewards` decimal(13,2) DEFAULT '0.00' COMMENT '打赏数',
  `type` tinyint(1) DEFAULT '0' COMMENT '分享类型0，原创1，链接，2，分享',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation_share
-- ----------------------------
INSERT INTO `guguo_corporation_share` VALUES ('1', '0', '1', '1', null, '1498550339', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('2', '0', '2', '1', null, '1498550385', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('3', '0', '3', '1', null, '1498550866', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('4', '0', '4', '1', null, '1498550873', '0', '0', '80.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('5', '0', '5', '1', null, '1498550913', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('6', '0', '6', '1', null, '1498551045', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('7', '0', '7', '1', null, '1498551060', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('8', '0', '8', '1', null, '1498551083', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('9', '0', '9', '1', null, '1498552613', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('10', '0', '10', '1', null, '1498552792', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('13', '0', '11', '2', null, '1498554522', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('15', '0', '2', '2', null, '1498554714', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('16', '0', '3', '2', null, '1498555185', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('17', '0', '4', '2', null, '1498556135', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('18', '0', '5', '2', null, '1498556246', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('19', '0', '6', '2', null, '1498556534', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('20', '0', '7', '2', null, '1498556787', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('21', '0', '8', '3', null, '1498557067', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('22', '0', '9', '3', null, '1498557455', '1', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('23', '0', '1', '3', null, '1498558103', '1', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('24', '0', '10', '4', null, '1498558181', '0', '4', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('25', '0', '11', '5', null, '1498723443', '1', '4', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('26', '0', '8', '1', null, '1500024735', '1', '0', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('27', '0', '7', '6', null, '1500024749', '0', '3', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('28', '27', '2', '6', null, '1500025169', '1', '2', '65.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('29', '28', '3', '6', null, '1500025383', '1', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('30', '25', '4', '5', null, '1500359944', '1', '0', '100.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('31', '24', '6', '4', null, '1500360061', '1', '0', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('32', '24', '3', '4', null, '1500534666', '1', '1', '12.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('33', '32', '3', '4', null, '1500877949', '1', '0', '62.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('34', '24', '3', '4', null, '1501039942', '1', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('35', '34', '3', '4', null, '1501040166', '1', '1', '14.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('36', '27', '3', '6', null, '1501040176', '1', '1', '10.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('37', '28', '3', '6', null, '1501041390', '1', '0', '1005.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('38', '29', '3', '6', null, '1501041397', '1', '0', '880.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('39', '36', '3', '6', null, '1501041401', '1', '1', '13.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('40', '35', '3', '4', null, '1501117960', '1', '1', '10.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('41', '39', '3', '6', null, '1501117964', '0', '2', '34.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('42', '40', '3', '4', null, '1501117965', '0', '1', '15.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('43', '41', '3', '6', null, '1501117966', '1', '1', '15.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('44', '41', '3', '6', null, '1501117969', '1', '2', '17.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('45', '42', '3', '4', null, '1501117971', '1', '1', '560.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('46', '44', '3', '6', null, '1501208502', '0', '0', '11.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('47', '43', '3', '6', null, '1501208505', '1', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('48', '45', '3', '4', null, '1501469645', '0', '1', '10.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('49', '44', '3', '6', null, '1501491288', '0', '1', '20.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('50', '24', '3', '4', null, '1501554516', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('51', '25', '3', '5', null, '1501554530', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('52', '49', '3', '6', null, '1501554536', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('53', '50', '3', '4', null, '1501554543', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('54', '25', '3', '5', null, '1501554553', '0', '1', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('55', '25', '3', '5', null, '1501554658', '0', '1', '56.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('56', '47', '3', '6', null, '1501573988', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('57', '54', '3', '5', null, '1501574035', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('58', '48', '3', '4', null, '1501574351', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('59', '27', '3', '6', null, '1501574656', '0', '0', '13.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('60', '22', '3', '3', null, '1501576923', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('61', '23', '3', '3', null, '1501576940', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('63', '57', '3', '5', null, '1501577444', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('64', '62', '3', '5', null, '1501577444', '0', '4', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('65', '60', '3', '3', null, '1501577461', '0', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('66', '53', '3', '4', null, '1501579831', '1', '1', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('67', '65', '3', '3', null, '1501579837', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('68', '65', '3', '3', null, '1501581612', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('69', '64', '3', '5', null, '1501581618', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('70', '64', '3', '5', null, '1501581654', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('71', '64', '3', '5', null, '1501581658', '0', '3', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('72', '64', '3', '5', null, '1501581682', '0', '0', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('73', '62', '3', '5', null, '1501581701', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('74', '71', '3', '5', null, '1501581809', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('75', '71', '3', '5', null, '1501581813', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('76', '71', '3', '5', null, '1501581821', '1', '6', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('77', '76', '3', '5', null, '1501831909', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('78', '76', '3', '5', null, '1501831910', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('79', '76', '3', '5', null, '1501831911', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('80', '76', '3', '5', null, '1501831911', '0', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('81', '76', '3', '5', null, '1501831911', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('82', '76', '3', '5', null, '1501831911', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('83', '80', '3', '5', null, '1501835050', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('84', '80', '3', '5', null, '1501835053', '0', '3', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('85', '84', '3', '5', null, '1501836227', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('86', '84', '3', '5', null, '1501836228', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('87', '84', '3', '5', null, '1501836237', '1', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('88', '87', '3', '5', null, '1501837074', '0', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('89', '88', '3', '5', null, '1501837352', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('90', '86', '3', '5', null, '1501837358', '0', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('91', '90', '3', '5', null, '1501837530', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('92', '90', '3', '5', null, '1501837533', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('93', '88', '3', '5', null, '1501837536', '0', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('94', '87', '3', '5', null, '1501837539', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('95', '66', '3', '4', null, '1501837549', '0', '5', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('96', '95', '3', '4', null, '1501837851', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('97', '95', '3', '4', null, '1501837853', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('98', '95', '3', '4', null, '1501837854', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('99', '95', '3', '4', null, '1501837856', '0', '5', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('100', '95', '3', '4', null, '1501837857', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('101', '99', '3', '4', null, '1501837904', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('102', '99', '3', '4', null, '1501837905', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('103', '99', '3', '4', null, '1501837906', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('104', '99', '3', '4', null, '1501837907', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('105', '99', '3', '4', null, '1501837909', '0', '7', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('106', '105', '3', '4', null, '1501838034', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('107', '105', '3', '4', null, '1501838037', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('108', '105', '3', '4', null, '1501838050', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('109', '105', '3', '4', null, '1501838050', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('110', '105', '3', '4', null, '1501838051', '0', '4', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('111', '105', '3', '4', null, '1501838051', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('112', '110', '3', '4', null, '1501838154', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('113', '110', '3', '4', null, '1501838156', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('114', '110', '3', '4', null, '1501838157', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('115', '110', '3', '4', null, '1501838159', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('116', '109', '3', '4', null, '1501838162', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('117', '108', '3', '4', null, '1501838170', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('118', '107', '3', '4', null, '1501838173', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('119', '105', '3', '4', null, '1501838175', '0', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('120', '119', '3', '4', null, '1502250120', '0', '0', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('121', '0', '3', '7', null, '1502328931', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('122', '0', '3', '7', null, '1502329634', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('123', '0', '3', '7', null, '1502329688', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('124', '93', '3', '5', null, '1502329891', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('125', '93', '3', '5', null, '1502329893', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('126', '0', '3', '8', null, '1502329989', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('127', '123', '3', '7', null, '1502330026', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('128', '0', '3', '9', null, '1502330304', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('129', '0', '3', '10', null, '1502330386', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('130', '0', '3', '11', null, '1502330527', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('131', '0', '3', '12', null, '1502330563', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('132', '0', '3', '13', null, '1502330602', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('133', '0', '3', '14', null, '1502330624', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('134', '133', '3', '14', null, '1502331556', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('135', '0', '3', '15', null, '1502332093', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('136', '0', '3', '16', null, '1502332110', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('137', '0', '3', '17', null, '1502332297', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('138', '119', '3', '4', null, '1502351567', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('139', '0', '3', '18', null, '1502414420', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('140', '0', '3', '19', null, '1502415171', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('141', '0', '3', '20', null, '1502415296', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('142', '0', '3', '21', null, '1502415464', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('143', '0', '5', '22', null, '1502418006', '1', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('144', '143', '3', '22', null, '1502418173', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('145', '142', '3', '21', null, '1502418186', '0', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('146', '145', '3', '21', null, '1502420216', '1', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('147', '145', '3', '21', null, '1502423029', '3', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('148', '146', '3', '21', null, '1502431365', '2', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('149', '0', '6', '23', null, '1502442647', '3', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('150', '148', '3', '21', null, '1502443442', '1', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('151', '0', '5', '24', null, '1502520463', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('152', '150', '3', '21', null, '1502520836', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('153', '0', '5', '25', null, '1502528525', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('154', '138', '4', '4', null, '1502780676', '0', '1', '5.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('155', '154', '4', '4', null, '1502781264', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('156', '153', '4', '25', null, '1502786674', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('157', '0', '4', '9', null, '1502789308', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('158', '0', '4', '7', null, '1502789405', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('159', '0', '4', '7', null, '1502789847', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('160', '0', '4', '7', null, '1502790320', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('161', '0', '3', '27', null, '1502791069', '0', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('162', '0', '4', '28', null, '1502845418', '2', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('163', '161', '4', '27', null, '1502845669', '0', '1', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('164', '0', '5', '29', null, '1502846975', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('165', '161', '5', '27', null, '1502848218', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('166', '163', '4', '27', null, '1502938264', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('167', '0', '4', '30', null, '1503038915', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('168', '0', '4', '31', null, '1503039417', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('169', '0', '4', '32', null, '1503039433', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('182', '0', '4', '45', null, '1503047542', '2', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('186', '185', '4', '48', null, '1503391353', '2', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('188', '0', '4', '50', null, '1503569027', '1', '2', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('191', '0', '4', '53', null, '1503628424', '2', '0', '3.10', '0');
INSERT INTO `guguo_corporation_share` VALUES ('196', '0', '2', '58', null, '1503904199', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('197', '0', '2', '59', null, '1503905331', '0', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('198', '0', '2', '60', null, '1503905479', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('199', '0', '2', '61', null, '1503905657', '0', '0', '0.00', '1');
INSERT INTO `guguo_corporation_share` VALUES ('206', '0', '4', '68', null, '1503971238', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('211', '0', '4', '73', null, '1503975306', '1', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('241', '0', '5', '102', null, '1504161261', '3', '0', '1500.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('242', '0', '6', '103', null, '1504228356', '2', '0', '0.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('243', '0', '6', '104', null, '1504229073', '3', '0', '148.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('244', '0', '4', '105', null, '1504753740', '2', '0', '95.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('245', '0', '4', '106', null, '1504918704', '1', '0', '444.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('249', '0', '4', '110', null, '1505371676', '4', '0', '2302.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('250', '0', '4', '111', null, '1506498432', '1', '0', '1.00', '0');
INSERT INTO `guguo_corporation_share` VALUES ('251', '0', '4', '112', null, '1506498742', '0', '0', '0.00', '0');

-- ----------------------------
-- Table structure for guguo_corporation_share_comment
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_share_comment`;
CREATE TABLE `guguo_corporation_share_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `share_id` int(11) NOT NULL COMMENT '状态条目id',
  `replyer_id` int(11) NOT NULL COMMENT '评论者id',
  `reply_content` varchar(140) NOT NULL COMMENT '评论内容',
  `reviewer_id` int(11) DEFAULT '0' COMMENT '被评论者ID',
  `reply_commont_id` int(11) DEFAULT '0' COMMENT '被评论的评论ID',
  `commont_time` int(11) DEFAULT NULL COMMENT '评论时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=523 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of guguo_corporation_share_comment
-- ----------------------------
INSERT INTO `guguo_corporation_share_comment` VALUES ('1', '25', '3', 'sdfsdfsdf', '0', '0', '1501149806');
INSERT INTO `guguo_corporation_share_comment` VALUES ('2', '25', '2', 'safddsf', '3', '1', '1501149746');
INSERT INTO `guguo_corporation_share_comment` VALUES ('3', '25', '1', 'dgfdgfd', '2', '2', '1501149686');
INSERT INTO `guguo_corporation_share_comment` VALUES ('4', '25', '4', 'SDvfdsfsdf', '0', '0', '1501149626');
INSERT INTO `guguo_corporation_share_comment` VALUES ('5', '24', '1', 'vsdfasfdsdfdsf', '0', '0', '1501149566');
INSERT INTO `guguo_corporation_share_comment` VALUES ('6', '23', '2', 'xzcvxzvxzcvxzcv', '0', '0', '1501149506');
INSERT INTO `guguo_corporation_share_comment` VALUES ('7', '22', '3', 'xcvcvxzcxvxzv', '0', '0', '1501149446');
INSERT INTO `guguo_corporation_share_comment` VALUES ('8', '20', '4', 'xcvxvxzcvxc', '0', '0', '1501149386');
INSERT INTO `guguo_corporation_share_comment` VALUES ('9', '22', '4', 'ssdvzxcvcxv', '0', '0', '1501149326');
INSERT INTO `guguo_corporation_share_comment` VALUES ('10', '24', '2', 'sdfsdfsdfsasadf', '0', '0', '1501149266');
INSERT INTO `guguo_corporation_share_comment` VALUES ('11', '24', '1', 'aaaaaaaa', '2', '10', '1501149206');
INSERT INTO `guguo_corporation_share_comment` VALUES ('12', '25', '2', '哈哈哈哈', '0', '0', '1501149146');
INSERT INTO `guguo_corporation_share_comment` VALUES ('13', '25', '3', '哈哈哈哈', '0', '0', '1501149086');
INSERT INTO `guguo_corporation_share_comment` VALUES ('14', '25', '4', '哈哈哈哈', '0', '0', '1501149026');
INSERT INTO `guguo_corporation_share_comment` VALUES ('15', '25', '5', '哈哈哈哈', '0', '0', '1501148966');
INSERT INTO `guguo_corporation_share_comment` VALUES ('16', '25', '6', 'aaaaaaaa', '3', '15', '1501148906');
INSERT INTO `guguo_corporation_share_comment` VALUES ('17', '25', '7', '这是评论', '0', '0', '1501148846');
INSERT INTO `guguo_corporation_share_comment` VALUES ('18', '25', '8', '这是回复', '0', '0', '1501148786');
INSERT INTO `guguo_corporation_share_comment` VALUES ('19', '24', '9', '这是品论', '0', '0', '1501148726');
INSERT INTO `guguo_corporation_share_comment` VALUES ('20', '24', '10', '这是回复', '2', '10', '1501148666');
INSERT INTO `guguo_corporation_share_comment` VALUES ('21', '15', '11', '哈哈哈哈', '0', '0', '1501148606');
INSERT INTO `guguo_corporation_share_comment` VALUES ('22', '15', '5', '哈哈哈哈', '0', '0', '1501148546');
INSERT INTO `guguo_corporation_share_comment` VALUES ('23', '25', '6', '哈哈哈哈', '0', '0', '1501148486');
INSERT INTO `guguo_corporation_share_comment` VALUES ('24', '25', '4', '[傲慢][傲慢][傲慢]', '0', '0', '1501148426');
INSERT INTO `guguo_corporation_share_comment` VALUES ('25', '25', '3', '[发怒][发怒][发怒][发怒]', '0', '0', '1501148366');
INSERT INTO `guguo_corporation_share_comment` VALUES ('26', '25', '7', '哈哈哈哈哈笑', '0', '0', '1501148306');
INSERT INTO `guguo_corporation_share_comment` VALUES ('27', '25', '8', '哈哈哈哈', '0', '0', '1501148246');
INSERT INTO `guguo_corporation_share_comment` VALUES ('28', '24', '2', '哈哈哈哈', '3', '19', '1501148186');
INSERT INTO `guguo_corporation_share_comment` VALUES ('29', '23', '1', '哈哈哈哈', '0', '0', '1501148126');
INSERT INTO `guguo_corporation_share_comment` VALUES ('30', '23', '10', '哈哈哈哈', '0', '0', '1501148066');
INSERT INTO `guguo_corporation_share_comment` VALUES ('31', '23', '11', '哈哈哈哈', '0', '0', '1501148006');
INSERT INTO `guguo_corporation_share_comment` VALUES ('32', '23', '9', '哈哈哈哈', '0', '0', '1501147946');
INSERT INTO `guguo_corporation_share_comment` VALUES ('33', '25', '3', '哈哈哈哈', '0', '0', '1501147886');
INSERT INTO `guguo_corporation_share_comment` VALUES ('34', '25', '7', '她在我心里都住不,她只要自由', '0', '0', '1501147826');
INSERT INTO `guguo_corporation_share_comment` VALUES ('35', '25', '8', '哈哈哈哈', '3', '24', '1501147766');
INSERT INTO `guguo_corporation_share_comment` VALUES ('36', '29', '5', '哈哈哈哈', '0', '0', '1501147706');
INSERT INTO `guguo_corporation_share_comment` VALUES ('37', '27', '1', '哈哈哈哈', '0', '0', '1501147646');
INSERT INTO `guguo_corporation_share_comment` VALUES ('38', '25', '10', '人', '0', '0', '1501147586');
INSERT INTO `guguo_corporation_share_comment` VALUES ('39', '25', '4', '哈哈哈哈', '3', '33', '1501147526');
INSERT INTO `guguo_corporation_share_comment` VALUES ('40', '24', '3', '哈哈哈哈哈哈哈 ', '0', '0', '1501147466');
INSERT INTO `guguo_corporation_share_comment` VALUES ('41', '24', '3', '哈哈哈哈', '2', '28', '1501147406');
INSERT INTO `guguo_corporation_share_comment` VALUES ('42', '17', '3', '哈哈哈哈哈哈哈', '0', '0', '1501147346');
INSERT INTO `guguo_corporation_share_comment` VALUES ('43', '17', '3', '哈哈哈哈', '0', '0', '1501147286');
INSERT INTO `guguo_corporation_share_comment` VALUES ('44', '16', '3', '哈哈哈哈', '0', '0', '1501147226');
INSERT INTO `guguo_corporation_share_comment` VALUES ('45', '18', '3', '哈哈哈哈', '0', '0', '1501147166');
INSERT INTO `guguo_corporation_share_comment` VALUES ('46', '25', '3', '哈哈哈哈', '0', '0', '1501147106');
INSERT INTO `guguo_corporation_share_comment` VALUES ('47', '24', '3', '哈哈哈哈', '0', '0', '1501147046');
INSERT INTO `guguo_corporation_share_comment` VALUES ('48', '43', '3', '哈哈哈哈我人', '0', '0', '1501146986');
INSERT INTO `guguo_corporation_share_comment` VALUES ('49', '42', '3', '哈哈哈哈', '0', '0', '1501146926');
INSERT INTO `guguo_corporation_share_comment` VALUES ('50', '41', '3', '哈哈哈哈', '0', '0', '1501146866');
INSERT INTO `guguo_corporation_share_comment` VALUES ('51', '43', '3', '哈哈哈哈我', '0', '0', '1501147376');
INSERT INTO `guguo_corporation_share_comment` VALUES ('52', '43', '3', '哈哈哈哈', '0', '0', '1501147386');
INSERT INTO `guguo_corporation_share_comment` VALUES ('53', '42', '3', '哈哈哈哈', '0', '0', '1501147396');
INSERT INTO `guguo_corporation_share_comment` VALUES ('54', '25', '3', '哈哈哈哈', '0', '0', '1501147406');
INSERT INTO `guguo_corporation_share_comment` VALUES ('55', '24', '3', '哈哈哈哈', '0', '0', '1501147416');
INSERT INTO `guguo_corporation_share_comment` VALUES ('56', '40', '3', '哈哈哈哈', '0', '0', '1501147426');
INSERT INTO `guguo_corporation_share_comment` VALUES ('57', '40', '3', '哈哈哈哈', '0', '0', '1501147436');
INSERT INTO `guguo_corporation_share_comment` VALUES ('58', '43', '3', '哈哈哈哈', '3', '51', '1501147446');
INSERT INTO `guguo_corporation_share_comment` VALUES ('59', '45', '3', '哈哈哈哈', '0', '0', '1501147456');
INSERT INTO `guguo_corporation_share_comment` VALUES ('60', '45', '3', '哈哈哈哈', '0', '0', '1501147466');
INSERT INTO `guguo_corporation_share_comment` VALUES ('61', '45', '3', '哈哈哈哈', '0', '0', '1501147476');
INSERT INTO `guguo_corporation_share_comment` VALUES ('62', '32', '3', '哈哈哈哈', '0', '0', '1501147486');
INSERT INTO `guguo_corporation_share_comment` VALUES ('63', '23', '3', '哈哈哈哈哈', '0', '0', '1501147496');
INSERT INTO `guguo_corporation_share_comment` VALUES ('64', '21', '3', '[色]', '0', '0', '1501147506');
INSERT INTO `guguo_corporation_share_comment` VALUES ('65', '43', '3', '哈哈哈哈', '0', '0', '1501147516');
INSERT INTO `guguo_corporation_share_comment` VALUES ('66', '48', '3', '哈哈哈哈', '0', '0', '1501147526');
INSERT INTO `guguo_corporation_share_comment` VALUES ('67', '46', '3', '哈哈哈哈', '0', '0', '1501147536');
INSERT INTO `guguo_corporation_share_comment` VALUES ('68', '43', '3', '哈哈哈哈', '0', '0', '1501147546');
INSERT INTO `guguo_corporation_share_comment` VALUES ('69', '41', '3', '哈哈哈哈', '0', '0', '1501147556');
INSERT INTO `guguo_corporation_share_comment` VALUES ('70', '46', '3', '哈哈哈哈', '0', '0', '1501147566');
INSERT INTO `guguo_corporation_share_comment` VALUES ('71', '49', '3', '哈哈哈哈', '0', '0', '1501147576');
INSERT INTO `guguo_corporation_share_comment` VALUES ('72', '45', '3', '哈哈哈哈', '0', '0', '1501147586');
INSERT INTO `guguo_corporation_share_comment` VALUES ('73', '45', '3', '哈哈哈哈', '0', '0', '1501147596');
INSERT INTO `guguo_corporation_share_comment` VALUES ('74', '48', '3', '哈哈大笑', '0', '0', '1501147606');
INSERT INTO `guguo_corporation_share_comment` VALUES ('75', '48', '3', '哈哈哈哈', '0', '0', '1501147616');
INSERT INTO `guguo_corporation_share_comment` VALUES ('76', '50', '3', '哈哈哈哈', '0', '0', '1501147626');
INSERT INTO `guguo_corporation_share_comment` VALUES ('77', '49', '3', '哈哈哈哈', '0', '0', '1501147636');
INSERT INTO `guguo_corporation_share_comment` VALUES ('78', '49', '3', '你的时候', '3', '71', '1501147646');
INSERT INTO `guguo_corporation_share_comment` VALUES ('79', '53', '3', '哈哈哈哈好', '0', '0', '1501147656');
INSERT INTO `guguo_corporation_share_comment` VALUES ('80', '28', '3', '是钩碰', '0', '0', '1501147666');
INSERT INTO `guguo_corporation_share_comment` VALUES ('81', '58', '3', '哈哈哈哈', '0', '0', '1501147676');
INSERT INTO `guguo_corporation_share_comment` VALUES ('82', '43', '3', '哈哈哈哈', '0', '0', '1501147686');
INSERT INTO `guguo_corporation_share_comment` VALUES ('83', '76', '3', 'Aww thanks m', '0', '0', '1501147696');
INSERT INTO `guguo_corporation_share_comment` VALUES ('84', '138', '3', 'Uhh ', '0', '0', '1501147706');
INSERT INTO `guguo_corporation_share_comment` VALUES ('85', '138', '3', 'Akakakak', '0', '0', '1501147716');
INSERT INTO `guguo_corporation_share_comment` VALUES ('86', '138', '3', 'Ajkjankjfknmkfaf', '0', '0', '1501147726');
INSERT INTO `guguo_corporation_share_comment` VALUES ('87', '138', '3', '哈哈哈哈党和国家', '0', '0', '1501147736');
INSERT INTO `guguo_corporation_share_comment` VALUES ('88', '143', '3', '哈哈哈哈', '0', '0', '1501147746');
INSERT INTO `guguo_corporation_share_comment` VALUES ('89', '143', '3', '哈哈哈哈', '0', '0', '1501147756');
INSERT INTO `guguo_corporation_share_comment` VALUES ('90', '143', '3', 'SSS', '0', '0', '1501147766');
INSERT INTO `guguo_corporation_share_comment` VALUES ('91', '143', '3', '我是', '0', '0', '1501147776');
INSERT INTO `guguo_corporation_share_comment` VALUES ('92', '143', '3', 'Eee', '0', '0', '1501147786');
INSERT INTO `guguo_corporation_share_comment` VALUES ('93', '143', '3', 'Sssss', '3', '88', '1501147796');
INSERT INTO `guguo_corporation_share_comment` VALUES ('94', '143', '3', 'Wwww', '0', '0', '1501147806');
INSERT INTO `guguo_corporation_share_comment` VALUES ('95', '148', '3', '22222', '0', '0', '1501147816');
INSERT INTO `guguo_corporation_share_comment` VALUES ('96', '148', '3', 'Wwww', '0', '0', '1501147826');
INSERT INTO `guguo_corporation_share_comment` VALUES ('97', '148', '3', 'Wwwwnnn', '0', '0', '1501147836');
INSERT INTO `guguo_corporation_share_comment` VALUES ('98', '148', '5', 'Bbbbb', '3', '97', '1501147846');
INSERT INTO `guguo_corporation_share_comment` VALUES ('99', '148', '5', 'Bbbbbbkkkk', '5', '98', '1501147856');
INSERT INTO `guguo_corporation_share_comment` VALUES ('100', '148', '6', 'Eeeeee', '5', '99', '1501147866');
INSERT INTO `guguo_corporation_share_comment` VALUES ('101', '148', '6', 'Eeeee', '3', '97', '1501147876');
INSERT INTO `guguo_corporation_share_comment` VALUES ('102', '148', '3', 'Www', '0', '0', '1501147886');
INSERT INTO `guguo_corporation_share_comment` VALUES ('103', '148', '6', 'Eee', '5', '99', '1501147896');
INSERT INTO `guguo_corporation_share_comment` VALUES ('104', '148', '6', 'Uuuu', '6', '103', '1501147906');
INSERT INTO `guguo_corporation_share_comment` VALUES ('105', '148', '6', 'Tttttt', '3', '96', '1501147916');
INSERT INTO `guguo_corporation_share_comment` VALUES ('106', '148', '6', '4444444', '6', '105', '1501147926');
INSERT INTO `guguo_corporation_share_comment` VALUES ('107', '148', '6', 'Rrrrr', '3', '97', '1501147936');
INSERT INTO `guguo_corporation_share_comment` VALUES ('108', '148', '6', 'Rrrr', '6', '100', '1501147946');
INSERT INTO `guguo_corporation_share_comment` VALUES ('109', '148', '6', 'Greddd', '0', '0', '1501147956');
INSERT INTO `guguo_corporation_share_comment` VALUES ('110', '148', '6', 'Gggghhhf', '0', '0', '1501147966');
INSERT INTO `guguo_corporation_share_comment` VALUES ('111', '147', '3', 'Rrrrrr', '0', '0', '1501147976');
INSERT INTO `guguo_corporation_share_comment` VALUES ('112', '147', '3', 'Rrrr', '0', '0', '1501147986');
INSERT INTO `guguo_corporation_share_comment` VALUES ('113', '147', '6', 'Ttttt', '3', '111', '1501147996');
INSERT INTO `guguo_corporation_share_comment` VALUES ('114', '147', '6', 'Ddddd ', '3', '111', '1501148006');
INSERT INTO `guguo_corporation_share_comment` VALUES ('115', '148', '6', 'Aaaaaa', '0', '0', '1501148016');
INSERT INTO `guguo_corporation_share_comment` VALUES ('116', '146', '6', 'Aaaaa', '0', '0', '1501148026');
INSERT INTO `guguo_corporation_share_comment` VALUES ('117', '143', '6', 'Dddddd', '0', '0', '1501148036');
INSERT INTO `guguo_corporation_share_comment` VALUES ('118', '143', '6', 'Eee', '6', '117', '1501148046');
INSERT INTO `guguo_corporation_share_comment` VALUES ('119', '147', '6', 'Www', '6', '113', '1501148056');
INSERT INTO `guguo_corporation_share_comment` VALUES ('120', '138', '6', 'Www', '0', '0', '1501148066');
INSERT INTO `guguo_corporation_share_comment` VALUES ('121', '138', '6', 'Wwww', '0', '0', '1501148076');
INSERT INTO `guguo_corporation_share_comment` VALUES ('122', '136', '6', 'Www', '0', '0', '1501148086');
INSERT INTO `guguo_corporation_share_comment` VALUES ('123', '138', '6', 'Lllllll', '0', '0', '1501148096');
INSERT INTO `guguo_corporation_share_comment` VALUES ('124', '148', '6', 'Wwwww', '0', '0', '1501148106');
INSERT INTO `guguo_corporation_share_comment` VALUES ('125', '148', '6', 'Eeee', '6', '124', '1501148116');
INSERT INTO `guguo_corporation_share_comment` VALUES ('126', '145', '6', 'Wwwww', '0', '0', '1501148126');
INSERT INTO `guguo_corporation_share_comment` VALUES ('127', '144', '6', 'Jjjjjjj', '0', '0', '1501148136');
INSERT INTO `guguo_corporation_share_comment` VALUES ('128', '145', '6', 'Wwwww', '0', '0', '1501148146');
INSERT INTO `guguo_corporation_share_comment` VALUES ('129', '145', '6', 'Eee', '0', '0', '1501148156');
INSERT INTO `guguo_corporation_share_comment` VALUES ('130', '148', '3', '我', '0', '0', '1501148166');
INSERT INTO `guguo_corporation_share_comment` VALUES ('131', '138', '3', '我', '0', '0', '1501148176');
INSERT INTO `guguo_corporation_share_comment` VALUES ('132', '138', '3', '哈哈哈哈', '0', '0', '1501148186');
INSERT INTO `guguo_corporation_share_comment` VALUES ('133', '148', '3', '哈哈哈哈', '0', '0', '1501148196');
INSERT INTO `guguo_corporation_share_comment` VALUES ('134', '136', '3', '哈哈哈哈', '0', '0', '1501148206');
INSERT INTO `guguo_corporation_share_comment` VALUES ('135', '131', '3', '哈哈哈哈', '0', '0', '1501148216');
INSERT INTO `guguo_corporation_share_comment` VALUES ('136', '125', '3', '哈哈哈哈', '0', '0', '1501148226');
INSERT INTO `guguo_corporation_share_comment` VALUES ('137', '124', '3', '哈哈哈哈', '0', '0', '1501148236');
INSERT INTO `guguo_corporation_share_comment` VALUES ('138', '121', '3', '哈哈哈哈', '0', '0', '1501148246');
INSERT INTO `guguo_corporation_share_comment` VALUES ('139', '119', '3', '哈哈哈哈', '0', '0', '1501148256');
INSERT INTO `guguo_corporation_share_comment` VALUES ('140', '118', '3', '我', '0', '0', '1501148266');
INSERT INTO `guguo_corporation_share_comment` VALUES ('141', '149', '6', '跟哥哥哥哥哥哥', '0', '0', '1501148276');
INSERT INTO `guguo_corporation_share_comment` VALUES ('142', '149', '6', '在', '0', '0', '1501148286');
INSERT INTO `guguo_corporation_share_comment` VALUES ('143', '145', '6', '呃呃', '6', '128', '1501148296');
INSERT INTO `guguo_corporation_share_comment` VALUES ('144', '149', '3', '哈哈哈哈', '0', '0', '1501148306');
INSERT INTO `guguo_corporation_share_comment` VALUES ('145', '149', '3', '哈哈哈哈', '0', '0', '1501148316');
INSERT INTO `guguo_corporation_share_comment` VALUES ('146', '148', '3', '哈哈哈哈', '0', '0', '1501148326');
INSERT INTO `guguo_corporation_share_comment` VALUES ('147', '149', '6', '俄', '0', '0', '1501148336');
INSERT INTO `guguo_corporation_share_comment` VALUES ('148', '148', '6', '你', '0', '0', '1501148346');
INSERT INTO `guguo_corporation_share_comment` VALUES ('149', '148', '6', '在一起', '3', '133', '1501148356');
INSERT INTO `guguo_corporation_share_comment` VALUES ('150', '148', '6', '在于一', '0', '0', '1501148366');
INSERT INTO `guguo_corporation_share_comment` VALUES ('151', '150', '3', '哈哈哈哈', '0', '0', '1501148376');
INSERT INTO `guguo_corporation_share_comment` VALUES ('152', '150', '5', 'Diff', '0', '0', '1501148386');
INSERT INTO `guguo_corporation_share_comment` VALUES ('153', '150', '5', '滴滴答答滴滴答答滴滴答答滴滴答答滴滴答答滴滴答答滴滴答答滴滴答答滴滴答答滴滴答答的多', '3', '151', '1501148396');
INSERT INTO `guguo_corporation_share_comment` VALUES ('154', '144', '5', '让', '0', '0', '1501148406');
INSERT INTO `guguo_corporation_share_comment` VALUES ('155', '138', '4', '哈哈哈哈', '0', '0', '1501148416');
INSERT INTO `guguo_corporation_share_comment` VALUES ('156', '132', '5', '呃呃', '0', '0', '1501148426');
INSERT INTO `guguo_corporation_share_comment` VALUES ('157', '131', '5', '我', '0', '0', '1501148436');
INSERT INTO `guguo_corporation_share_comment` VALUES ('158', '130', '5', '我', '0', '0', '1501148446');
INSERT INTO `guguo_corporation_share_comment` VALUES ('159', '128', '4', '哈哈哈哈哈笑', '0', '0', '1501148456');
INSERT INTO `guguo_corporation_share_comment` VALUES ('160', '128', '4', '哈哈哈哈', '0', '0', '1501148466');
INSERT INTO `guguo_corporation_share_comment` VALUES ('161', '150', '5', '嗡嗡嗡w', '5', '153', '1501148476');
INSERT INTO `guguo_corporation_share_comment` VALUES ('162', '150', '5', '你们', '5', '153', '1501148486');
INSERT INTO `guguo_corporation_share_comment` VALUES ('163', '150', '5', '你', '5', '161', '1501148496');
INSERT INTO `guguo_corporation_share_comment` VALUES ('164', '149', '5', '我的心是一', '0', '0', '1501148506');
INSERT INTO `guguo_corporation_share_comment` VALUES ('165', '150', '5', '你们的', '5', '152', '1501148516');
INSERT INTO `guguo_corporation_share_comment` VALUES ('166', '150', '5', '我们', '3', '151', '1501148526');
INSERT INTO `guguo_corporation_share_comment` VALUES ('167', '149', '5', '人人人人', '5', '164', '1501148536');
INSERT INTO `guguo_corporation_share_comment` VALUES ('168', '150', '5', '你的心都有', '5', '153', '1501148546');
INSERT INTO `guguo_corporation_share_comment` VALUES ('169', '150', '5', '你的时候', '5', '153', '1501148556');
INSERT INTO `guguo_corporation_share_comment` VALUES ('170', '150', '5', '在这一天都没得做个', '5', '153', '1501148566');
INSERT INTO `guguo_corporation_share_comment` VALUES ('171', '150', '5', '在这一次我爱你们', '5', '153', '1501148576');
INSERT INTO `guguo_corporation_share_comment` VALUES ('172', '150', '5', '我的心都有一个人的心都有一个人的心都有一个人', '5', '162', '1501148586');
INSERT INTO `guguo_corporation_share_comment` VALUES ('173', '150', '5', '你的人是谁', '0', '0', '1501148596');
INSERT INTO `guguo_corporation_share_comment` VALUES ('174', '150', '5', '你', '5', '153', '1501148606');
INSERT INTO `guguo_corporation_share_comment` VALUES ('175', '150', '5', '你', '5', '153', '1501148616');
INSERT INTO `guguo_corporation_share_comment` VALUES ('176', '146', '5', '你', '0', '0', '1501148626');
INSERT INTO `guguo_corporation_share_comment` VALUES ('177', '146', '5', '在', '0', '0', '1501148636');
INSERT INTO `guguo_corporation_share_comment` VALUES ('178', '146', '5', '我', '0', '0', '1501148646');
INSERT INTO `guguo_corporation_share_comment` VALUES ('179', '146', '5', '我', '0', '0', '1501148656');
INSERT INTO `guguo_corporation_share_comment` VALUES ('180', '146', '5', '在', '6', '116', '1501148666');
INSERT INTO `guguo_corporation_share_comment` VALUES ('181', '148', '5', '你的人是', '6', '148', '1501148676');
INSERT INTO `guguo_corporation_share_comment` VALUES ('182', '150', '5', '你的人的', '5', '153', '1501148686');
INSERT INTO `guguo_corporation_share_comment` VALUES ('183', '149', '5', '你们的最新', '5', '167', '1501148696');
INSERT INTO `guguo_corporation_share_comment` VALUES ('184', '149', '5', '在你身边的人的时候', '0', '0', '1501148706');
INSERT INTO `guguo_corporation_share_comment` VALUES ('185', '147', '5', '你', '3', '112', '1502520648');
INSERT INTO `guguo_corporation_share_comment` VALUES ('186', '147', '5', '在', '0', '0', '1502520653');
INSERT INTO `guguo_corporation_share_comment` VALUES ('187', '150', '5', '在这一', '5', '175', '1502520665');
INSERT INTO `guguo_corporation_share_comment` VALUES ('188', '150', '5', '现场', '3', '151', '1502520689');
INSERT INTO `guguo_corporation_share_comment` VALUES ('189', '150', '5', '在一起时会', '5', '187', '1502520726');
INSERT INTO `guguo_corporation_share_comment` VALUES ('190', '151', '5', '这种', '0', '0', '1502520797');
INSERT INTO `guguo_corporation_share_comment` VALUES ('191', '151', '5', '在这方面的', '0', '0', '1502520806');
INSERT INTO `guguo_corporation_share_comment` VALUES ('192', '151', '5', '是否就要', '0', '0', '1502520813');
INSERT INTO `guguo_corporation_share_comment` VALUES ('193', '151', '5', '你们的最', '0', '0', '1502520829');
INSERT INTO `guguo_corporation_share_comment` VALUES ('194', '151', '5', '这种音乐风格的', '0', '0', '1502520845');
INSERT INTO `guguo_corporation_share_comment` VALUES ('195', '151', '5', '在你身边', '0', '0', '1502520854');
INSERT INTO `guguo_corporation_share_comment` VALUES ('196', '151', '5', 'ssss太', '0', '0', '1502520859');
INSERT INTO `guguo_corporation_share_comment` VALUES ('197', '151', '5', '你的时候你在', '0', '0', '1502520873');
INSERT INTO `guguo_corporation_share_comment` VALUES ('198', '151', '5', '在我', '0', '0', '1502520912');
INSERT INTO `guguo_corporation_share_comment` VALUES ('199', '148', '5', '是一', '5', '181', '1502520940');
INSERT INTO `guguo_corporation_share_comment` VALUES ('200', '151', '5', '在这方面我都会在一起了。我都不会更好的时候我都会在你身边的人的心是一个月了。我都', '5', '191', '1502520964');
INSERT INTO `guguo_corporation_share_comment` VALUES ('201', '151', '5', '这么着', '5', '200', '1502520992');
INSERT INTO `guguo_corporation_share_comment` VALUES ('202', '151', '5', '这么多天', '5', '200', '1502520998');
INSERT INTO `guguo_corporation_share_comment` VALUES ('203', '151', '5', '这个世界的人', '0', '0', '1502521004');
INSERT INTO `guguo_corporation_share_comment` VALUES ('204', '151', '5', '是', '0', '0', '1502521008');
INSERT INTO `guguo_corporation_share_comment` VALUES ('205', '151', '5', '在你身边', '5', '203', '1502521026');
INSERT INTO `guguo_corporation_share_comment` VALUES ('206', '151', '5', '你是一个', '5', '204', '1502521082');
INSERT INTO `guguo_corporation_share_comment` VALUES ('207', '151', '5', '俄武器', '5', '206', '1502521595');
INSERT INTO `guguo_corporation_share_comment` VALUES ('208', '150', '5', '你', '5', '188', '1502521962');
INSERT INTO `guguo_corporation_share_comment` VALUES ('209', '149', '5', '你', '6', '141', '1502522134');
INSERT INTO `guguo_corporation_share_comment` VALUES ('210', '147', '5', '你', '0', '0', '1502522142');
INSERT INTO `guguo_corporation_share_comment` VALUES ('211', '152', '5', '的办学', '0', '0', '1502522157');
INSERT INTO `guguo_corporation_share_comment` VALUES ('212', '150', '5', '你的', '0', '0', '1502524691');
INSERT INTO `guguo_corporation_share_comment` VALUES ('213', '150', '5', '你的', '0', '0', '1502524700');
INSERT INTO `guguo_corporation_share_comment` VALUES ('214', '151', '5', '[发怒]', '0', '0', '1502527370');
INSERT INTO `guguo_corporation_share_comment` VALUES ('215', '152', '5', '[猪头]', '0', '0', '1502527381');
INSERT INTO `guguo_corporation_share_comment` VALUES ('216', '152', '5', '[大哭]', '0', '0', '1502527416');
INSERT INTO `guguo_corporation_share_comment` VALUES ('217', '152', '5', 'www.baidu.com', '0', '0', '1502527528');
INSERT INTO `guguo_corporation_share_comment` VALUES ('218', '152', '5', 'https://www.baidu.com', '0', '0', '1502529324');
INSERT INTO `guguo_corporation_share_comment` VALUES ('219', '152', '5', 'http://123.com', '0', '0', '1502530477');
INSERT INTO `guguo_corporation_share_comment` VALUES ('220', '152', '5', '12234@163.com', '0', '0', '1502670144');
INSERT INTO `guguo_corporation_share_comment` VALUES ('221', '152', '5', '[足球]', '0', '0', '1502670176');
INSERT INTO `guguo_corporation_share_comment` VALUES ('222', '152', '5', '@ssd', '0', '0', '1502670458');
INSERT INTO `guguo_corporation_share_comment` VALUES ('223', '152', '5', '[衰]', '0', '0', '1502670574');
INSERT INTO `guguo_corporation_share_comment` VALUES ('224', '153', '5', 'Eww', '0', '0', '1502670685');
INSERT INTO `guguo_corporation_share_comment` VALUES ('225', '153', '5', '[发怒]', '0', '0', '1502674612');
INSERT INTO `guguo_corporation_share_comment` VALUES ('226', '152', '5', '[足球]在我', '0', '0', '1502690816');
INSERT INTO `guguo_corporation_share_comment` VALUES ('227', '153', '4', '哈哈哈哈哈', '5', '225', '1502785095');
INSERT INTO `guguo_corporation_share_comment` VALUES ('230', '155', '4', '哈哈哈哈', '0', '0', '1502786691');
INSERT INTO `guguo_corporation_share_comment` VALUES ('231', '152', '4', '我', '5', '218', '1502786790');
INSERT INTO `guguo_corporation_share_comment` VALUES ('232', '152', '4', '哈哈哈哈', '5', '222', '1502786799');
INSERT INTO `guguo_corporation_share_comment` VALUES ('233', '153', '4', '哈哈哈哈', '4', '227', '1502786810');
INSERT INTO `guguo_corporation_share_comment` VALUES ('234', '153', '4', '哈哈哈哈', '4', '233', '1502786830');
INSERT INTO `guguo_corporation_share_comment` VALUES ('236', '152', '4', '哈哈哈哈', '0', '0', '1502786887');
INSERT INTO `guguo_corporation_share_comment` VALUES ('237', '152', '4', '哈哈哈哈', '5', '221', '1502787022');
INSERT INTO `guguo_corporation_share_comment` VALUES ('238', '155', '4', '哈哈哈哈', '4', '230', '1502787857');
INSERT INTO `guguo_corporation_share_comment` VALUES ('239', '152', '4', '哈哈哈哈', '5', '226', '1502788123');
INSERT INTO `guguo_corporation_share_comment` VALUES ('240', '138', '4', '哈哈哈哈', '0', '0', '1502788134');
INSERT INTO `guguo_corporation_share_comment` VALUES ('241', '138', '4', '哈哈哈哈', '3', '132', '1502788145');
INSERT INTO `guguo_corporation_share_comment` VALUES ('242', '135', '4', '哈哈哈哈哈笑着面对人生个疯疯癫癫就快快进入大学伯明翰听到你居然才能克服 v 客厅的背景', '0', '0', '1502788176');
INSERT INTO `guguo_corporation_share_comment` VALUES ('244', '161', '5', '傻逼', '0', '0', '1502843762');
INSERT INTO `guguo_corporation_share_comment` VALUES ('245', '163', '5', '你', '0', '0', '1502845843');
INSERT INTO `guguo_corporation_share_comment` VALUES ('246', '162', '4', '穿越火线', '0', '0', '1502845870');
INSERT INTO `guguo_corporation_share_comment` VALUES ('248', '162', '5', '你', '4', '247', '1502848418');
INSERT INTO `guguo_corporation_share_comment` VALUES ('249', '162', '5', '在', '0', '0', '1502848445');
INSERT INTO `guguo_corporation_share_comment` VALUES ('250', '156', '5', '我', '4', '247', '1502848951');
INSERT INTO `guguo_corporation_share_comment` VALUES ('251', '160', '5', '是', '0', '0', '1502849316');
INSERT INTO `guguo_corporation_share_comment` VALUES ('252', '163', '5', '你', '0', '0', '1502849376');
INSERT INTO `guguo_corporation_share_comment` VALUES ('253', '162', '5', '我', '0', '0', '1502849381');
INSERT INTO `guguo_corporation_share_comment` VALUES ('254', '161', '5', '你', '5', '244', '1502849387');
INSERT INTO `guguo_corporation_share_comment` VALUES ('255', '159', '5', '在', '0', '0', '1502849392');
INSERT INTO `guguo_corporation_share_comment` VALUES ('256', '153', '5', '你', '5', '224', '1502849421');
INSERT INTO `guguo_corporation_share_comment` VALUES ('257', '163', '5', '我', '0', '0', '1502855361');
INSERT INTO `guguo_corporation_share_comment` VALUES ('258', '163', '5', '你', '5', '257', '1502863786');
INSERT INTO `guguo_corporation_share_comment` VALUES ('259', '153', '5', 'Rrrr', '4', '227', '1502931923');
INSERT INTO `guguo_corporation_share_comment` VALUES ('262', '151', '5', '你的', '5', '207', '1502937199');
INSERT INTO `guguo_corporation_share_comment` VALUES ('263', '150', '5', '你', '5', '189', '1502937208');
INSERT INTO `guguo_corporation_share_comment` VALUES ('264', '166', '5', 'www.baidu.com', '0', '0', '1502951270');
INSERT INTO `guguo_corporation_share_comment` VALUES ('265', '163', '5', '个', '5', '258', '1502954553');
INSERT INTO `guguo_corporation_share_comment` VALUES ('266', '166', '5', '我', '0', '0', '1502956147');
INSERT INTO `guguo_corporation_share_comment` VALUES ('267', '165', '5', '你', '4', '260', '1502956176');
INSERT INTO `guguo_corporation_share_comment` VALUES ('268', '153', '4', 'Drfr', '4', '227', '1502958077');
INSERT INTO `guguo_corporation_share_comment` VALUES ('269', '162', '5', '你的人都', '5', '248', '1502961542');
INSERT INTO `guguo_corporation_share_comment` VALUES ('270', '162', '5', 'Wwq is', '4', '246', '1503020570');
INSERT INTO `guguo_corporation_share_comment` VALUES ('271', '159', '5', '你', '0', '0', '1503020734');
INSERT INTO `guguo_corporation_share_comment` VALUES ('272', '165', '5', '你', '5', '267', '1503038987');
INSERT INTO `guguo_corporation_share_comment` VALUES ('273', '167', '5', '你', '0', '0', '1503039028');
INSERT INTO `guguo_corporation_share_comment` VALUES ('274', '162', '5', '在', '0', '0', '1503039035');
INSERT INTO `guguo_corporation_share_comment` VALUES ('275', '160', '5', '是', '0', '0', '1503039046');
INSERT INTO `guguo_corporation_share_comment` VALUES ('276', '167', '5', '这', '0', '0', '1503039052');
INSERT INTO `guguo_corporation_share_comment` VALUES ('308', '169', '4', '哈哈哈哈', '0', '0', '1503367680');
INSERT INTO `guguo_corporation_share_comment` VALUES ('312', '164', '4', '哈哈哈哈', '0', '0', '1503367711');
INSERT INTO `guguo_corporation_share_comment` VALUES ('458', '241', '2', '谁让你都删了的！', '0', '0', '1504225786');
INSERT INTO `guguo_corporation_share_comment` VALUES ('459', '241', '2', '我是erbi', '0', '0', '1504226323');
INSERT INTO `guguo_corporation_share_comment` VALUES ('460', '241', '2', '请不要登我的号！', '0', '0', '1504227665');
INSERT INTO `guguo_corporation_share_comment` VALUES ('461', '241', '6', '你好', '0', '0', '1504228322');
INSERT INTO `guguo_corporation_share_comment` VALUES ('463', '196', '2', '测试', '0', '0', '1504228315');
INSERT INTO `guguo_corporation_share_comment` VALUES ('464', '242', '2', '66666', '0', '0', '1504228359');
INSERT INTO `guguo_corporation_share_comment` VALUES ('465', '242', '4', '哈哈哈哈', '2', '464', '1504237990');
INSERT INTO `guguo_corporation_share_comment` VALUES ('466', '243', '4', '哈哈哈哈', '0', '0', '1504253630');
INSERT INTO `guguo_corporation_share_comment` VALUES ('467', '241', '4', '打他', '2', '460', '1504253798');
INSERT INTO `guguo_corporation_share_comment` VALUES ('468', '243', '5', 'We ', '0', '0', '1504753139');
INSERT INTO `guguo_corporation_share_comment` VALUES ('469', '182', '4', '哈哈哈哈', '0', '0', '1504834150');
INSERT INTO `guguo_corporation_share_comment` VALUES ('470', '206', '2', '链接', '0', '0', '1504924532');
INSERT INTO `guguo_corporation_share_comment` VALUES ('471', '245', '2', '模块机', '0', '0', '1504924547');
INSERT INTO `guguo_corporation_share_comment` VALUES ('472', '244', '2', '模块机', '0', '0', '1504924560');
INSERT INTO `guguo_corporation_share_comment` VALUES ('473', '243', '2', '哈哈哈哈', '0', '0', '1504925642');
INSERT INTO `guguo_corporation_share_comment` VALUES ('474', '243', '2', '图图图图图', '0', '0', '1504925825');
INSERT INTO `guguo_corporation_share_comment` VALUES ('475', '245', '5', '傻逼', '2', '471', '1505090564');
INSERT INTO `guguo_corporation_share_comment` VALUES ('476', '245', '8', '战斗nmh', '0', '0', '1505176564');
INSERT INTO `guguo_corporation_share_comment` VALUES ('477', '245', '5', '二逼', '8', '476', '1505263445');
INSERT INTO `guguo_corporation_share_comment` VALUES ('478', '245', '4', '傻叼', '8', '476', '1505269133');
INSERT INTO `guguo_corporation_share_comment` VALUES ('480', '120', '3', '发给对方付出', '0', '0', '1505291932');
INSERT INTO `guguo_corporation_share_comment` VALUES ('481', '120', '3', '方式', '0', '0', '1505291942');
INSERT INTO `guguo_corporation_share_comment` VALUES ('482', '245', '4', '[猪头][猪头][猪头][猪头]', '0', '0', '1505349722');
INSERT INTO `guguo_corporation_share_comment` VALUES ('483', '245', '4', '哈', '0', '0', '1505349852');
INSERT INTO `guguo_corporation_share_comment` VALUES ('484', '245', '4', '哈哈哈哈', '0', '0', '1505351531');
INSERT INTO `guguo_corporation_share_comment` VALUES ('486', '160', '4', 'h哈哈哈哈', '5', '275', '1505353255');
INSERT INTO `guguo_corporation_share_comment` VALUES ('487', '245', '4', '哈哈哈哈', '0', '0', '1505354409');
INSERT INTO `guguo_corporation_share_comment` VALUES ('488', '242', '4', '哈哈哈哈', '0', '0', '1505356564');
INSERT INTO `guguo_corporation_share_comment` VALUES ('489', '242', '4', '哈哈哈哈', '2', '464', '1505356572');
INSERT INTO `guguo_corporation_share_comment` VALUES ('490', '241', '4', '[发怒]😕😕😕😕', '0', '0', '1505356653');
INSERT INTO `guguo_corporation_share_comment` VALUES ('491', '241', '4', '😇😇😇😇😇😇', '4', '490', '1505356677');
INSERT INTO `guguo_corporation_share_comment` VALUES ('496', '243', '2', '43224', '0', '0', '1505370714');
INSERT INTO `guguo_corporation_share_comment` VALUES ('497', '243', '2', '0000', '0', '0', '1505370748');
INSERT INTO `guguo_corporation_share_comment` VALUES ('499', '249', '2', '评论', '0', '0', '1505371699');
INSERT INTO `guguo_corporation_share_comment` VALUES ('500', '249', '4', '哈哈哈哈哈笑着对', '0', '0', '1505372171');
INSERT INTO `guguo_corporation_share_comment` VALUES ('501', '249', '4', '哈哈哈哈', '2', '499', '1505372175');
INSERT INTO `guguo_corporation_share_comment` VALUES ('502', '249', '2', '回复你', '0', '0', '1505372264');
INSERT INTO `guguo_corporation_share_comment` VALUES ('503', '249', '4', '😳😳😳😳', '4', '500', '1505372282');
INSERT INTO `guguo_corporation_share_comment` VALUES ('504', '249', '4', '小黑龙大傻叉', '0', '0', '1505372410');
INSERT INTO `guguo_corporation_share_comment` VALUES ('505', '249', '2', 'alert(&#39;shishi&#39;);', '0', '0', '1505372433');
INSERT INTO `guguo_corporation_share_comment` VALUES ('506', '249', '4', '哈哈哈哈', '0', '0', '1505373024');
INSERT INTO `guguo_corporation_share_comment` VALUES ('507', '182', '4', 'emotion8[微笑]', '4', '469', '1505382204');
INSERT INTO `guguo_corporation_share_comment` VALUES ('508', '249', '4', 'emotion9', '0', '0', '1505439177');
INSERT INTO `guguo_corporation_share_comment` VALUES ('509', '249', '2', '能回复吗', '0', '0', '1505440441');
INSERT INTO `guguo_corporation_share_comment` VALUES ('510', '249', '12', '呀呀呀', '0', '0', '1505461246');
INSERT INTO `guguo_corporation_share_comment` VALUES ('511', '249', '12', '回复', '0', '0', '1505461266');
INSERT INTO `guguo_corporation_share_comment` VALUES ('514', '249', '4', '哈哈哈哈', '12', '511', '1506064072');
INSERT INTO `guguo_corporation_share_comment` VALUES ('515', '249', '12', '评论', '0', '0', '1506067786');
INSERT INTO `guguo_corporation_share_comment` VALUES ('516', '249', '3', '哈喽', '0', '0', '1506069862');
INSERT INTO `guguo_corporation_share_comment` VALUES ('517', '249', '3', '哈哈哈哈哈哈或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或哈哈哈哈哈哈或或或或或或或或或或或或或或或或或或 ', '0', '0', '1506069910');
INSERT INTO `guguo_corporation_share_comment` VALUES ('518', '249', '7', '哈哈', '0', '0', '1506332745');

-- ----------------------------
-- Table structure for guguo_corporation_share_content
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_share_content`;
CREATE TABLE `guguo_corporation_share_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(144) DEFAULT NULL COMMENT '内容id',
  `text` text COMMENT '正文链接',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation_share_content
-- ----------------------------
INSERT INTO `guguo_corporation_share_content` VALUES ('1', '测试动态发布', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('2', '测试分享消息息息息息息', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('3', '哈哈哈哈哈笑', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('4', '哈哈哈哈你是我的', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('6', '测试动态发布1', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('7', '哈哈哈哈', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('8', '哈哈', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('9', '我是', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('10', '我是史学鹏', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('11', '我是史学鹏吗', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('12', '我是。 学鹏', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('13', '史学鹏', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('14', '嗯嗯嗯嗯 ', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('15', '学鹏三国杀', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('16', '闪过啥', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('17', '你是谁', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('18', '你', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('19', 'Zhonghuarenmin', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('20', 'A', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('21', 'Q', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('22', '2222', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('23', '是生生世世生生世世生生世世生生世世生生世世生生世世生生世世生生世世生生世世人人人人人人人人人人人', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('24', '你的人', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('25', 'Qqqqqq', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('27', '测试动态发布', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('28', '哈哈哈哈', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('29', '在这一', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('30', '哈哈哈哈你们是因为自己没有做完作业的孩子', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('31', '在于', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('32', '航天飞机', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('45', '哈哈哈哈', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('53', '开会了', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('58', '试试', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('59', '测试ajax', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('60', '测试能否添加上text', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('61', '测试url', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('68', '哈哈哈哈', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('73', '哈哈哈哈', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('102', '你', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('103', '你好', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('104', '拖', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('105', 'U', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('106', '哈哈哈哈战斗之夜', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('110', '颜氏', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('111', '你是', null);
INSERT INTO `guguo_corporation_share_content` VALUES ('112', '你好', null);

-- ----------------------------
-- Table structure for guguo_corporation_share_like
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_share_like`;
CREATE TABLE `guguo_corporation_share_like` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `share_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `like_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `like_index` (`share_id`,`user_id`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=430 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation_share_like
-- ----------------------------
INSERT INTO `guguo_corporation_share_like` VALUES ('1', '23', '3', '1500598277');
INSERT INTO `guguo_corporation_share_like` VALUES ('6', '30', '3', '1500625093');
INSERT INTO `guguo_corporation_share_like` VALUES ('9', '26', '3', '1500628271');
INSERT INTO `guguo_corporation_share_like` VALUES ('10', '25', '3', '1500629591');
INSERT INTO `guguo_corporation_share_like` VALUES ('12', '29', '3', '1500947437');
INSERT INTO `guguo_corporation_share_like` VALUES ('17', '33', '3', '1500947850');
INSERT INTO `guguo_corporation_share_like` VALUES ('22', '28', '3', '1500947859');
INSERT INTO `guguo_corporation_share_like` VALUES ('32', '31', '3', '1501040692');
INSERT INTO `guguo_corporation_share_like` VALUES ('33', '35', '3', '1501041034');
INSERT INTO `guguo_corporation_share_like` VALUES ('34', '32', '3', '1501041385');
INSERT INTO `guguo_corporation_share_like` VALUES ('37', '36', '3', '1501054549');
INSERT INTO `guguo_corporation_share_like` VALUES ('38', '34', '3', '1501054551');
INSERT INTO `guguo_corporation_share_like` VALUES ('46', '22', '3', '1501203478');
INSERT INTO `guguo_corporation_share_like` VALUES ('52', '44', '3', '1501470396');
INSERT INTO `guguo_corporation_share_like` VALUES ('55', '40', '3', '1501470448');
INSERT INTO `guguo_corporation_share_like` VALUES ('56', '39', '3', '1501470451');
INSERT INTO `guguo_corporation_share_like` VALUES ('57', '38', '3', '1501470452');
INSERT INTO `guguo_corporation_share_like` VALUES ('58', '37', '3', '1501470454');
INSERT INTO `guguo_corporation_share_like` VALUES ('59', '43', '3', '1501488719');
INSERT INTO `guguo_corporation_share_like` VALUES ('63', '45', '3', '1501550115');
INSERT INTO `guguo_corporation_share_like` VALUES ('85', '47', '3', '1501575873');
INSERT INTO `guguo_corporation_share_like` VALUES ('89', '66', '3', '1501815749');
INSERT INTO `guguo_corporation_share_like` VALUES ('90', '76', '3', '1501831908');
INSERT INTO `guguo_corporation_share_like` VALUES ('95', '87', '3', '1501837540');
INSERT INTO `guguo_corporation_share_like` VALUES ('102', '132', '3', '1502331591');
INSERT INTO `guguo_corporation_share_like` VALUES ('105', '140', '3', '1502415240');
INSERT INTO `guguo_corporation_share_like` VALUES ('106', '139', '3', '1502415243');
INSERT INTO `guguo_corporation_share_like` VALUES ('107', '143', '3', '1502418132');
INSERT INTO `guguo_corporation_share_like` VALUES ('113', '146', '3', '1502434586');
INSERT INTO `guguo_corporation_share_like` VALUES ('119', '147', '3', '1502442148');
INSERT INTO `guguo_corporation_share_like` VALUES ('123', '148', '3', '1502443517');
INSERT INTO `guguo_corporation_share_like` VALUES ('145', '135', '3', '1502443585');
INSERT INTO `guguo_corporation_share_like` VALUES ('146', '148', '6', '1502444509');
INSERT INTO `guguo_corporation_share_like` VALUES ('147', '147', '6', '1502444518');
INSERT INTO `guguo_corporation_share_like` VALUES ('148', '149', '6', '1502496562');
INSERT INTO `guguo_corporation_share_like` VALUES ('149', '147', '5', '1502496637');
INSERT INTO `guguo_corporation_share_like` VALUES ('150', '150', '3', '1502496887');
INSERT INTO `guguo_corporation_share_like` VALUES ('151', '149', '3', '1502496889');
INSERT INTO `guguo_corporation_share_like` VALUES ('160', '149', '5', '1502520299');
INSERT INTO `guguo_corporation_share_like` VALUES ('162', '151', '5', '1502677802');
INSERT INTO `guguo_corporation_share_like` VALUES ('173', '155', '4', '1502781270');
INSERT INTO `guguo_corporation_share_like` VALUES ('174', '156', '4', '1502788100');
INSERT INTO `guguo_corporation_share_like` VALUES ('178', '162', '4', '1502845471');
INSERT INTO `guguo_corporation_share_like` VALUES ('182', '165', '4', '1502938447');
INSERT INTO `guguo_corporation_share_like` VALUES ('184', '162', '5', '1503018004');
INSERT INTO `guguo_corporation_share_like` VALUES ('208', '182', '5', '1503278745');
INSERT INTO `guguo_corporation_share_like` VALUES ('271', '186', '5', '1503459233');
INSERT INTO `guguo_corporation_share_like` VALUES ('284', '191', '5', '1503628642');
INSERT INTO `guguo_corporation_share_like` VALUES ('330', '186', '4', '1503989657');
INSERT INTO `guguo_corporation_share_like` VALUES ('341', '196', '2', '1503996614');
INSERT INTO `guguo_corporation_share_like` VALUES ('352', '241', '2', '1504227794');
INSERT INTO `guguo_corporation_share_like` VALUES ('353', '241', '6', '1504228318');
INSERT INTO `guguo_corporation_share_like` VALUES ('354', '182', '4', '1504229419');
INSERT INTO `guguo_corporation_share_like` VALUES ('355', '243', '5', '1504229443');
INSERT INTO `guguo_corporation_share_like` VALUES ('356', '242', '5', '1504229447');
INSERT INTO `guguo_corporation_share_like` VALUES ('364', '242', '4', '1504253761');
INSERT INTO `guguo_corporation_share_like` VALUES ('365', '206', '4', '1504753075');
INSERT INTO `guguo_corporation_share_like` VALUES ('369', '211', '4', '1504753718');
INSERT INTO `guguo_corporation_share_like` VALUES ('390', '244', '8', '1505178185');
INSERT INTO `guguo_corporation_share_like` VALUES ('397', '243', '3', '1505291871');
INSERT INTO `guguo_corporation_share_like` VALUES ('398', '241', '3', '1505291878');
INSERT INTO `guguo_corporation_share_like` VALUES ('402', '191', '4', '1505353204');
INSERT INTO `guguo_corporation_share_like` VALUES ('403', '188', '4', '1505353205');
INSERT INTO `guguo_corporation_share_like` VALUES ('407', '198', '4', '1505357811');
INSERT INTO `guguo_corporation_share_like` VALUES ('418', '245', '4', '1505370465');
INSERT INTO `guguo_corporation_share_like` VALUES ('419', '244', '4', '1505370469');
INSERT INTO `guguo_corporation_share_like` VALUES ('420', '243', '4', '1505370471');
INSERT INTO `guguo_corporation_share_like` VALUES ('425', '249', '8', '1505437275');
INSERT INTO `guguo_corporation_share_like` VALUES ('426', '249', '4', '1505956356');
INSERT INTO `guguo_corporation_share_like` VALUES ('427', '249', '5', '1506063799');
INSERT INTO `guguo_corporation_share_like` VALUES ('428', '249', '2', '1506127416');
INSERT INTO `guguo_corporation_share_like` VALUES ('429', '250', '4', '1506498557');

-- ----------------------------
-- Table structure for guguo_corporation_share_picture
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_share_picture`;
CREATE TABLE `guguo_corporation_share_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL COMMENT '状态条目id',
  `path` varchar(256) NOT NULL COMMENT '图片路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation_share_picture
-- ----------------------------
INSERT INTO `guguo_corporation_share_picture` VALUES ('10', '1', '/webroot/sdzhongxun/images/20170627/9f76ee16e3bfb5d465c88734439ae041.jpg');
INSERT INTO `guguo_corporation_share_picture` VALUES ('11', '1', '/webroot/sdzhongxun/images/20170627/decb9c19c979dc2c31174a7f68263ffb.png');
INSERT INTO `guguo_corporation_share_picture` VALUES ('16', '1', '/webroot/sdzhongxun/images/20170627/869ab9700b947d4db99ff5654c7a9e17.jpg');
INSERT INTO `guguo_corporation_share_picture` VALUES ('17', '1', '/webroot/sdzhongxun/images/20170627/ffa9138db20af3ed5db11cf92def798a.png');
INSERT INTO `guguo_corporation_share_picture` VALUES ('19', '1', '/webroot/sdzhongxun/images/20170627/337b2d161460e10980cf5ca2a15bf6da.jpg');
INSERT INTO `guguo_corporation_share_picture` VALUES ('20', '1', '/webroot/sdzhongxun/images/20170627/bd704b7d4ab812574faa1784ab137cd8.png');
INSERT INTO `guguo_corporation_share_picture` VALUES ('22', '1', '/webroot/sdzhongxun/images/20170627/eb9d852e7493b354b5325f542d5b0f2a.jpg');
INSERT INTO `guguo_corporation_share_picture` VALUES ('23', '2', '/webroot/sdzhongxun/images/20170627/a72c756ee61d0dcb2f556e063b1c7751.png');
INSERT INTO `guguo_corporation_share_picture` VALUES ('25', '2', '/webroot/sdzhongxun/images/20170627/9accf0242b955a1d2b7eb342c3ef0dc7.jpg');
INSERT INTO `guguo_corporation_share_picture` VALUES ('26', '2', '/webroot/sdzhongxun/images/20170627/7c83a5f88a9d41d7ca8f03acbdec20df.png');
INSERT INTO `guguo_corporation_share_picture` VALUES ('28', '2', '/webroot/sdzhongxun/images/20170627/98c7bb5a57bdc7318241280cbe324d4a.jpg');
INSERT INTO `guguo_corporation_share_picture` VALUES ('29', '2', '/webroot/sdzhongxun/images/20170627/35e482b666b44088d9fa3b70f5d08323.png');
INSERT INTO `guguo_corporation_share_picture` VALUES ('31', '2', '/webroot/sdzhongxun/images/20170627/c209343e4330a8865af2d944056b88b8.jpg');
INSERT INTO `guguo_corporation_share_picture` VALUES ('32', '2', '/webroot/sdzhongxun/images/20170627/9565ba94bd78b52fe56aeb62124e52d1.png');
INSERT INTO `guguo_corporation_share_picture` VALUES ('34', '3', '/webroot/sdzhongxun/images/20170627/434e067e2aa1ae647f8b1bc4f6903c26.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('35', '3', '/webroot/sdzhongxun/images/20170627/44119acb1c60fbcc1ad8380bd8e75ec7.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('36', '3', '/webroot/sdzhongxun/images/20170627/6a74ad5d6c742ec901c729adc4a7d5a9.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('37', '3', '/webroot/sdzhongxun/images/20170627/d97a818191d65aaded86cb5f9b8bed70.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('38', '3', '/webroot/sdzhongxun/images/20170627/8e1267c662c590ef2d81a460b4ef0007.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('41', '3', '/webroot/sdzhongxun/images/20170627/6b3d8c7f095049b5e864ccf73dde0310.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('42', '3', '/webroot/sdzhongxun/images/20170627/e18a1061f1c383d09a9f2c3b8572acd9.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('43', '3', '/webroot/sdzhongxun/images/20170627/c66d8fa30165f03abee60e0bbd7eca39.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('44', '4', '/webroot/sdzhongxun/images/20170627/f5aab027d1aaab01a77cc1b8f2086bf0.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('45', '4', '/webroot/sdzhongxun/images/20170627/e80ba3f9c4a72819f9e52433309b0ce1.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('46', '4', '/webroot/sdzhongxun/images/20170627/a613e761d7d3b9b55e1e22e9e843a25c.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('48', '4', '/webroot/sdzhongxun/images/20170627/d3348c8509a914d41c3db459f4357404.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('49', '4', '/webroot/sdzhongxun/images/20170627/a906592e70ce442fe5417bd9431451a7.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('50', '4', '/webroot/sdzhongxun/images/20170627/09852dde7085c910f99938590dcc6d7d.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('51', '4', '/webroot/sdzhongxun/images/20170627/75e420d9b27535570f9cc65c31b7f368.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('52', '4', '/webroot/sdzhongxun/images/20170627/dae4efd063a6922c67a804c8e47c5190.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('59', '10', '/webroot/sdzhongxun/images/20170810/6520dc1fee693c4d1472d0f8950723b0.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('60', '10', '/webroot/sdzhongxun/images/20170810/d8752436f5c8a9b143847453acd4e698.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('61', '10', '/webroot/sdzhongxun/images/20170810/450a0e0151afe4e95e425cf57d0d6e44.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('62', '10', '/webroot/sdzhongxun/images/20170810/f47e4590f1beb4c0cea39774d91acbf5.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('63', '10', '/webroot/sdzhongxun/images/20170810/5817dcfb6ab39274b8bd121a01cdbb2d.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('64', '10', '/webroot/sdzhongxun/images/20170810/d0a1ca54f174684ee0f0002dba1aa30f.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('65', '10', '/webroot/sdzhongxun/images/20170810/d2e654fb271f8702b30706be8af70cd0.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('69', '11', '/webroot/sdzhongxun/images/20170810/c25eaa6e48d03d1964e58c3f2751eafc.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('70', '11', '/webroot/sdzhongxun/images/20170810/bf6aaac273daf0e6364b16316a6b5636.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('71', '11', '/webroot/sdzhongxun/images/20170810/fbb8c853d3b4571746b77504da6ce43f.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('72', '9', '/webroot/sdzhongxun/images/20170815/0f30b09433d12eb7d709fd05a242273e.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('73', '9', '/webroot/sdzhongxun/images/20170815/7ea9edc24dd89f428536f8eadd7d5e1a.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('75', '9', '/webroot/sdzhongxun/images/20170815/1478bf3b9e6678b8488d3bba3cb751bd.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('76', '9', '/webroot/sdzhongxun/images/20170815/34e5d1dbce7ef1c00a8d38382be64513.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('78', '9', '/webroot/sdzhongxun/images/20170815/2ba33a7aca8afee281b3687ab6308a19.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('79', '9', '/webroot/sdzhongxun/images/20170815/e8e773046dc6f1509f6e066c717a5c16.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('80', '7', '/webroot/sdzhongxun/images/20170815/bc05643eb22348283355fca4b190ebcb.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('81', '7', '/webroot/sdzhongxun/images/20170815/167d09a8da4202d25af6ced75c9b1b9e.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('82', '7', '/webroot/sdzhongxun/images/20170815/e763eb47777ab5e8607db63dae81835b.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('83', '7', '/webroot/sdzhongxun/images/20170815/98c98b0a76c6fcc79d26f1aa251a1753.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('85', '7', '/webroot/sdzhongxun/images/20170815/84b09b4e1aa74b0f6924f57fe9424e79.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('86', '7', '/webroot/sdzhongxun/images/20170815/0ee256ea07444dfbde7ecb710da6d351.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('87', '7', '/webroot/sdzhongxun/images/20170815/ecf559144cb2ce7bd1fe253fa943cd3d.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('88', '7', '/webroot/sdzhongxun/images/20170815/9ffc1a7ea7c34647a7431da77df53b46.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('92', '28', '/webroot/sdzhongxun/images/20170816/395db0877634355a729a64627f6bc391.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('93', '28', '/webroot/sdzhongxun/images/20170816/baf8b68cdb04ab4be727303f5b70acd9.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('95', '30', '/webroot/sdzhongxun/images/20170818/a144e99b009fffcfe1fdb492b11372eb.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('96', '30', '/webroot/sdzhongxun/images/20170818/29e5143e517900ea4386abfca1406d90.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('97', '30', '/webroot/sdzhongxun/images/20170818/57f6a0afb99b5b42c624aba88921e56c.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('98', '30', '/webroot/sdzhongxun/images/20170818/28ae724eebe27c8db9276cdcb11572ff.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('102', '31', '/webroot/sdzhongxun/images/20170818/3563efea475354bd75666e52ead0c563.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('103', '31', '/webroot/sdzhongxun/images/20170818/895c625df59276a249f827f4416fef57.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('104', '31', '/webroot/sdzhongxun/images/20170818/b5792bd146ab622d93826b3411283ce0.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('105', '31', '/webroot/sdzhongxun/images/20170818/36995de96b44e5d9862482b4b93fe096.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('109', '32', '/webroot/sdzhongxun/images/20170818/53b8ebf6e0ee38ef943c6b71176301ab.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('110', '32', '/webroot/sdzhongxun/images/20170818/56e80c77c6310060965d29751d8b82b4.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('133', '45', '/webroot/sdzhongxun/images/20170818/6e0c3ed127ee0562ffc88e22c141f8f0.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('143', '53', '/webroot/sdzhongxun/images/20170825/11db68056e37c820155cec96bcb4a05c.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('144', '53', '/webroot/sdzhongxun/images/20170825/03fee8969ea4dd1a4f77189437b284d5.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('160', '68', '/webroot/sdzhongxun/images/20170829/43d7d70f36986caaaf8ed4558a3ad946.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('167', '73', '/webroot/sdzhongxun/images/20170829/aeea1f15b0839c2e62a3156af0de8db2.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('206', '102', '/webroot/sdzhongxun/images/20170831/610dd3c45a5313be4d33a3155cb4b863.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('207', '104', '/webroot/sdzhongxun/images/20170901/80d09c1b1c209b1e1e6bd805b46cb1f7.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('208', '105', '/webroot/sdzhongxun/images/20170907/020fb4d1c0db95abc4e6685c7a09d0fc.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('209', '106', '/webroot/sdzhongxun/images/20170909/bd58d78ee07f8ccfca12a6c63e4f0d0a.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('213', '110', '/webroot/sdzhongxun/images/20170914/d19b333f2366ac30fe183f0f569933d9.JPG');
INSERT INTO `guguo_corporation_share_picture` VALUES ('214', '111', '/webroot/sdzhongxun/images/20170927/0360dedb69c382cb6057da99f1bed895.JPG');

-- ----------------------------
-- Table structure for guguo_corporation_share_tape
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_share_tape`;
CREATE TABLE `guguo_corporation_share_tape` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL COMMENT '状态条目id',
  `path` varchar(256) NOT NULL COMMENT '录音路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation_share_tape
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_corporation_share_tip
-- ----------------------------
DROP TABLE IF EXISTS `guguo_corporation_share_tip`;
CREATE TABLE `guguo_corporation_share_tip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `share_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `money` decimal(13,2) unsigned NOT NULL,
  `tip_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tip_index` (`share_id`,`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_corporation_share_tip
-- ----------------------------
INSERT INTO `guguo_corporation_share_tip` VALUES ('14', '32', '3', '1.00', '1500605644');
INSERT INTO `guguo_corporation_share_tip` VALUES ('15', '33', '3', '5.00', '1500878635');
INSERT INTO `guguo_corporation_share_tip` VALUES ('16', '32', '3', '5.00', '1500883096');
INSERT INTO `guguo_corporation_share_tip` VALUES ('17', '30', '3', '5.00', '1500883206');
INSERT INTO `guguo_corporation_share_tip` VALUES ('18', '33', '3', '2.00', '1500947224');
INSERT INTO `guguo_corporation_share_tip` VALUES ('19', '26', '3', '5.00', '1500947872');
INSERT INTO `guguo_corporation_share_tip` VALUES ('20', '31', '3', '5.00', '1501040215');
INSERT INTO `guguo_corporation_share_tip` VALUES ('21', '27', '3', '5.00', '1501040961');
INSERT INTO `guguo_corporation_share_tip` VALUES ('24', '33', '3', '55.00', '1501041012');
INSERT INTO `guguo_corporation_share_tip` VALUES ('25', '32', '3', '5.00', '1501053971');
INSERT INTO `guguo_corporation_share_tip` VALUES ('26', '36', '3', '5.00', '1501054071');
INSERT INTO `guguo_corporation_share_tip` VALUES ('27', '36', '3', '5.00', '1501054125');
INSERT INTO `guguo_corporation_share_tip` VALUES ('28', '38', '3', '5.00', '1501054488');
INSERT INTO `guguo_corporation_share_tip` VALUES ('29', '38', '3', '8.00', '1501054501');
INSERT INTO `guguo_corporation_share_tip` VALUES ('30', '38', '3', '862.00', '1501054517');
INSERT INTO `guguo_corporation_share_tip` VALUES ('31', '35', '3', '5.00', '1501059202');
INSERT INTO `guguo_corporation_share_tip` VALUES ('32', '35', '3', '9.00', '1501059215');
INSERT INTO `guguo_corporation_share_tip` VALUES ('33', '39', '3', '1.00', '1501060745');
INSERT INTO `guguo_corporation_share_tip` VALUES ('34', '39', '3', '1.00', '1501060757');
INSERT INTO `guguo_corporation_share_tip` VALUES ('35', '39', '3', '1.00', '1501060805');
INSERT INTO `guguo_corporation_share_tip` VALUES ('36', '32', '3', '1.00', '1501061280');
INSERT INTO `guguo_corporation_share_tip` VALUES ('37', '37', '3', '1000.00', '1501117925');
INSERT INTO `guguo_corporation_share_tip` VALUES ('38', '41', '3', '5.00', '1501145287');
INSERT INTO `guguo_corporation_share_tip` VALUES ('39', '44', '3', '5.00', '1501145922');
INSERT INTO `guguo_corporation_share_tip` VALUES ('40', '41', '3', '5.00', '1501146137');
INSERT INTO `guguo_corporation_share_tip` VALUES ('41', '42', '3', '5.00', '1501146255');
INSERT INTO `guguo_corporation_share_tip` VALUES ('42', '42', '3', '5.00', '1501146337');
INSERT INTO `guguo_corporation_share_tip` VALUES ('43', '40', '3', '5.00', '1501146412');
INSERT INTO `guguo_corporation_share_tip` VALUES ('44', '39', '3', '5.00', '1501146574');
INSERT INTO `guguo_corporation_share_tip` VALUES ('45', '39', '3', '5.00', '1501146602');
INSERT INTO `guguo_corporation_share_tip` VALUES ('46', '44', '3', '2.00', '1501146628');
INSERT INTO `guguo_corporation_share_tip` VALUES ('47', '41', '3', '5.00', '1501146786');
INSERT INTO `guguo_corporation_share_tip` VALUES ('48', '41', '3', '2.00', '1501146819');
INSERT INTO `guguo_corporation_share_tip` VALUES ('49', '41', '3', '5.00', '1501146878');
INSERT INTO `guguo_corporation_share_tip` VALUES ('50', '38', '3', '5.00', '1501146974');
INSERT INTO `guguo_corporation_share_tip` VALUES ('51', '41', '3', '5.00', '1501147079');
INSERT INTO `guguo_corporation_share_tip` VALUES ('52', '41', '3', '2.00', '1501147109');
INSERT INTO `guguo_corporation_share_tip` VALUES ('53', '30', '3', '5.00', '1501147383');
INSERT INTO `guguo_corporation_share_tip` VALUES ('54', '30', '3', '10.00', '1501147409');
INSERT INTO `guguo_corporation_share_tip` VALUES ('55', '30', '3', '80.00', '1501147433');
INSERT INTO `guguo_corporation_share_tip` VALUES ('56', '4', '3', '80.00', '1501147495');
INSERT INTO `guguo_corporation_share_tip` VALUES ('57', '43', '3', '5.00', '1501148017');
INSERT INTO `guguo_corporation_share_tip` VALUES ('58', '43', '3', '5.00', '1501148076');
INSERT INTO `guguo_corporation_share_tip` VALUES ('59', '43', '3', '5.00', '1501148102');
INSERT INTO `guguo_corporation_share_tip` VALUES ('60', '44', '3', '5.00', '1501148429');
INSERT INTO `guguo_corporation_share_tip` VALUES ('61', '41', '3', '5.00', '1501148602');
INSERT INTO `guguo_corporation_share_tip` VALUES ('62', '37', '3', '5.00', '1501148649');
INSERT INTO `guguo_corporation_share_tip` VALUES ('63', '42', '3', '5.00', '1501149164');
INSERT INTO `guguo_corporation_share_tip` VALUES ('64', '40', '3', '5.00', '1501202514');
INSERT INTO `guguo_corporation_share_tip` VALUES ('65', '44', '3', '5.00', '1501203243');
INSERT INTO `guguo_corporation_share_tip` VALUES ('66', '28', '3', '65.00', '1501205358');
INSERT INTO `guguo_corporation_share_tip` VALUES ('67', '25', '3', '5.00', '1501469683');
INSERT INTO `guguo_corporation_share_tip` VALUES ('68', '45', '3', '555.00', '1501470371');
INSERT INTO `guguo_corporation_share_tip` VALUES ('69', '45', '3', '5.00', '1501491712');
INSERT INTO `guguo_corporation_share_tip` VALUES ('70', '49', '3', '5.00', '1501494193');
INSERT INTO `guguo_corporation_share_tip` VALUES ('71', '48', '3', '5.00', '1501550145');
INSERT INTO `guguo_corporation_share_tip` VALUES ('72', '48', '3', '5.00', '1501551728');
INSERT INTO `guguo_corporation_share_tip` VALUES ('73', '49', '3', '5.00', '1501551753');
INSERT INTO `guguo_corporation_share_tip` VALUES ('74', '49', '3', '5.00', '1501552030');
INSERT INTO `guguo_corporation_share_tip` VALUES ('75', '49', '3', '5.00', '1501554406');
INSERT INTO `guguo_corporation_share_tip` VALUES ('76', '46', '3', '5.00', '1501554494');
INSERT INTO `guguo_corporation_share_tip` VALUES ('77', '55', '3', '2.00', '1501555256');
INSERT INTO `guguo_corporation_share_tip` VALUES ('78', '55', '3', '25.00', '1501555282');
INSERT INTO `guguo_corporation_share_tip` VALUES ('79', '55', '3', '6.00', '1501555298');
INSERT INTO `guguo_corporation_share_tip` VALUES ('80', '55', '3', '8.00', '1501559864');
INSERT INTO `guguo_corporation_share_tip` VALUES ('81', '55', '3', '5.00', '1501559964');
INSERT INTO `guguo_corporation_share_tip` VALUES ('82', '54', '3', '5.00', '1501568405');
INSERT INTO `guguo_corporation_share_tip` VALUES ('83', '55', '3', '5.00', '1501568997');
INSERT INTO `guguo_corporation_share_tip` VALUES ('84', '55', '3', '5.00', '1501574166');
INSERT INTO `guguo_corporation_share_tip` VALUES ('85', '59', '3', '5.00', '1501577533');
INSERT INTO `guguo_corporation_share_tip` VALUES ('86', '59', '3', '8.00', '1501577556');
INSERT INTO `guguo_corporation_share_tip` VALUES ('87', '46', '3', '6.00', '1501637933');
INSERT INTO `guguo_corporation_share_tip` VALUES ('88', '72', '3', '5.00', '1501751035');
INSERT INTO `guguo_corporation_share_tip` VALUES ('89', '66', '3', '5.00', '1501815761');
INSERT INTO `guguo_corporation_share_tip` VALUES ('90', '120', '3', '5.00', '1502329226');
INSERT INTO `guguo_corporation_share_tip` VALUES ('92', '154', '3', '5.00', '1502781255');
INSERT INTO `guguo_corporation_share_tip` VALUES ('102', '191', '5', '1.00', '1503628677');
INSERT INTO `guguo_corporation_share_tip` VALUES ('103', '191', '5', '1.00', '1503628743');
INSERT INTO `guguo_corporation_share_tip` VALUES ('104', '191', '5', '1.00', '1503642001');
INSERT INTO `guguo_corporation_share_tip` VALUES ('105', '191', '5', '0.10', '1503650421');
INSERT INTO `guguo_corporation_share_tip` VALUES ('111', '243', '2', '54.00', '1504493007');
INSERT INTO `guguo_corporation_share_tip` VALUES ('112', '243', '2', '20.00', '1504570538');
INSERT INTO `guguo_corporation_share_tip` VALUES ('113', '245', '2', '90.00', '1504924415');
INSERT INTO `guguo_corporation_share_tip` VALUES ('114', '244', '2', '90.00', '1504924575');
INSERT INTO `guguo_corporation_share_tip` VALUES ('115', '245', '8', '100.00', '1505178530');
INSERT INTO `guguo_corporation_share_tip` VALUES ('116', '245', '8', '100.00', '1505178559');
INSERT INTO `guguo_corporation_share_tip` VALUES ('117', '245', '5', '147.00', '1505296242');
INSERT INTO `guguo_corporation_share_tip` VALUES ('118', '245', '5', '1.00', '1505296590');
INSERT INTO `guguo_corporation_share_tip` VALUES ('119', '245', '5', '1.00', '1505296930');
INSERT INTO `guguo_corporation_share_tip` VALUES ('120', '245', '4', '5.00', '1505359002');
INSERT INTO `guguo_corporation_share_tip` VALUES ('121', '244', '4', '5.00', '1505359206');
INSERT INTO `guguo_corporation_share_tip` VALUES ('122', '241', '4', '1500.00', '1505359346');
INSERT INTO `guguo_corporation_share_tip` VALUES ('125', '249', '2', '100.00', '1505371742');
INSERT INTO `guguo_corporation_share_tip` VALUES ('126', '249', '4', '1000.00', '1505371791');
INSERT INTO `guguo_corporation_share_tip` VALUES ('127', '249', '4', '1000.00', '1505372493');
INSERT INTO `guguo_corporation_share_tip` VALUES ('129', '243', '3', '74.00', '1506068677');
INSERT INTO `guguo_corporation_share_tip` VALUES ('130', '249', '4', '2.00', '1506152908');
INSERT INTO `guguo_corporation_share_tip` VALUES ('131', '249', '4', '200.00', '1506153676');
INSERT INTO `guguo_corporation_share_tip` VALUES ('133', '250', '4', '1.00', '1506498564');

-- ----------------------------
-- Table structure for guguo_customer
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer`;
CREATE TABLE `guguo_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(128) NOT NULL COMMENT '客户名称，即公司名称',
  `resource_from` tinyint(1) NOT NULL COMMENT '客户来源:1批量导入，2员工添加,3员工搜集',
  `telephone` varchar(13) NOT NULL COMMENT '客户联系方式',
  `add_man` int(11) DEFAULT NULL COMMENT '添加客户的员工',
  `add_batch` int(10) DEFAULT '0' COMMENT '导入批次，批量导入时填写',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `handle_man` int(11) DEFAULT '0' COMMENT '客户负责人，0表示无人或系统管理员负责',
  `take_type` int(11) DEFAULT NULL COMMENT '获取途径,1转介绍,2搜索,3购买',
  `take_time` int(11) DEFAULT NULL COMMENT '领取时间',
  `field1` mediumint(9) DEFAULT NULL COMMENT '客户行业',
  `field2` mediumint(9) DEFAULT NULL COMMENT '客户行业',
  `field` mediumint(9) DEFAULT NULL COMMENT '客户行业',
  `grade` varchar(16) DEFAULT NULL COMMENT '客户级别',
  `prov` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `dist` varchar(64) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL COMMENT '详细地址',
  `location` varchar(128) DEFAULT NULL COMMENT '详细定位',
  `lat` double(10,6) DEFAULT '0.000000' COMMENT '客户坐标位置,纬度',
  `lng` double(10,6) DEFAULT '0.000000' COMMENT '客户坐标位置,经度',
  `website` varchar(128) DEFAULT NULL COMMENT '客户公司网站，逗号分隔',
  `remark` varchar(256) DEFAULT NULL COMMENT '备注信息',
  `belongs_to` tinyint(4) DEFAULT NULL COMMENT '1客户管理，2公海池，3我的客户，4待处理',
  `is_public` tinyint(1) DEFAULT '1' COMMENT '是否公开，1是，0否',
  `public_to_employee` text COMMENT '对谁可见，员工id，逗号分隔',
  `public_to_department` text COMMENT '对部门可见，部门id，逗号分隔',
  `is_partner` tinyint(1) DEFAULT NULL COMMENT '老客户，合作客户，1是，0否',
  `last_edit_time` int(11) DEFAULT '0' COMMENT '最后编辑时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_customer
-- ----------------------------
INSERT INTO `guguo_customer` VALUES ('1', '山东李白有限公司', '1', '13312123232', '2', '0', '1495527856', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', '1', null, null, null, '60');
INSERT INTO `guguo_customer` VALUES ('2', '山东杜甫有限公司', '1', '13321125355', '2', '0', '1495527857', '1', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', '1', null, null, null, '120');
INSERT INTO `guguo_customer` VALUES ('3', '山东白居易有限公司', '1', '13321215658', '2', '0', '1495527858', '2', null, null, '0', '0', '0', '', '', '', '', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504494190');
INSERT INTO `guguo_customer` VALUES ('4', '山东王昌龄有限公司', '1', '13355788753', '2', '0', '1495527859', '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', '1', null, null, null, '240');
INSERT INTO `guguo_customer` VALUES ('11', '山东中迅网络传媒有限公司', '1', '010-58585519', '1', '2017050007', '1495527860', '3', '1', '1495527860', '1', '2', '3', 'A', '山东省', '潍坊市', '潍城区', '潍坊市潍城区和平路胜利西街', '金艺大厦7f', '0.000000', '0.000000', 'http://www.baidusd.com/,http://www.winbywin.com/', '', '3', '1', null, null, null, '1502327973');
INSERT INTO `guguo_customer` VALUES ('15', '山东光大电子机械设备自动化回收绿色环保有限公司', '1', '18866668888', '3', '0', '1498656606', '0', null, null, '3', '2', '2', 'B', '1', '1', '1', '胜利西街', '金艺大厦', '0.000000', '0.000000', 'http://www.baidu666.com/', '大海的发挥到符合法定和', '1', '1', null, null, null, '1498657506');
INSERT INTO `guguo_customer` VALUES ('16', '山东陆游有限公司', '0', '18888888888', '3', '0', '1498626513', '5', null, null, null, null, '0', 'A', '', '', '', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498627473');
INSERT INTO `guguo_customer` VALUES ('17', '山东陆游1有限公司', '1', '18888888888', '3', '0', '1498626523', '5', null, null, '0', '0', '0', 'A', '湖南省', '湘潭市', '雨湖区', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504678406');
INSERT INTO `guguo_customer` VALUES ('18', '山东陆游2有限公司', '0', '18888888888', '3', '0', '1498626529', '5', null, null, null, null, '0', 'A', '', '', '', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498627609');
INSERT INTO `guguo_customer` VALUES ('19', '山东陆游3有限公司', '0', '18888888888', '3', '0', '1498626536', '5', null, null, null, null, '0', 'A', '', '', '', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498627676');
INSERT INTO `guguo_customer` VALUES ('20', '山东陆游4有限公司', '0', '18888888888', '3', '0', '1498626558', '5', null, null, null, null, '0', 'A', '', '', '', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498627758');
INSERT INTO `guguo_customer` VALUES ('21', '山东陆游5有限公司', '1', '18888888888', '3', '0', '1498626564', '5', null, null, '0', '0', '0', 'A', '湖南省', '湘潭市', '雨湖区', '金艺大厦', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504658310');
INSERT INTO `guguo_customer` VALUES ('22', '山东陆游6有限公司', '0', '18888888888', '3', '0', '1498626570', '5', null, null, null, null, '0', 'A', '', '', '', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498627890');
INSERT INTO `guguo_customer` VALUES ('23', '山东陆游7有限公司', '0', '18888888888', '3', '0', '1498626575', '5', null, null, null, null, '0', 'A', '', '', '', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498627955');
INSERT INTO `guguo_customer` VALUES ('24', '山东李太白有限公司', '0', '18888888888', '3', '0', '1498630369', '5', null, null, null, null, '0', 'A', '', '', '', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498631809');
INSERT INTO `guguo_customer` VALUES ('25', '山东李灰有限公司', '0', '18888888888', '3', '0', '1498630504', '5', null, null, null, null, '0', 'A', '', '', '', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498632004');
INSERT INTO `guguo_customer` VALUES ('26', '山东说大哥散发的该有限公司', '0', '18888888888', '3', '0', '1498674183', '3', '3', null, '2', '2', '2', 'B', '山东省', '潍坊市', '潍城区', '撒发达否', '阿三大声说', '0.000000', '0.000000', '阿凡打赏否', '阿三发放的是否', '3', '1', null, null, null, '1504778357');
INSERT INTO `guguo_customer` VALUES ('27', '山东测试有限公司', '2', '15688888888', '3', '0', '1498705429', '5', '0', null, '0', '0', '0', 'C', '山东省', '青岛市', '崂山区', '改放到是法国范德萨', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504924430');
INSERT INTO `guguo_customer` VALUES ('28', '山东新增插件测试有限公司', '1', '15658585858', '3', '0', '1498897222', '3', null, null, '0', '0', '0', 'C', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498898902');
INSERT INTO `guguo_customer` VALUES ('29', '山东测试的订单有限公司', '1', '15565656565', '3', '0', '1498897370', '3', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498899110');
INSERT INTO `guguo_customer` VALUES ('30', '山东测试时有限公司', '1', '15656656656', '3', '0', '1498897600', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498899400');
INSERT INTO `guguo_customer` VALUES ('31', '山东测试时时有限公司', '1', '15656665666', '3', '0', '1498897658', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498899518');
INSERT INTO `guguo_customer` VALUES ('32', '山东测测是事实有限公司', '1', '15555655656', '3', '0', '1498897732', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498899652');
INSERT INTO `guguo_customer` VALUES ('33', '山东阿额哇额有限公司', '1', '15643244543', '3', '0', '1498898232', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498900212');
INSERT INTO `guguo_customer` VALUES ('34', '山东萨达是大势有限公司', '1', '15576546765', '3', '0', '1498898570', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498900610');
INSERT INTO `guguo_customer` VALUES ('35', '山东都是发生地方有限公司', '1', '15687656786', '3', '0', '1498898687', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498900787');
INSERT INTO `guguo_customer` VALUES ('36', '山东依然有热有限公司', '1', '15745667765', '3', '0', '1498898821', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498900981');
INSERT INTO `guguo_customer` VALUES ('37', '山东额撒人事有限公司', '1', '15698767865', '3', '0', '1498899000', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498901220');
INSERT INTO `guguo_customer` VALUES ('38', '山东散发的设置有限公司', '1', '13576543355', '3', '0', '1498899106', '3', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498901386');
INSERT INTO `guguo_customer` VALUES ('39', '山东阿斯顿否有限公司', '1', '13574324675', '3', '0', '1498899335', '3', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498901675');
INSERT INTO `guguo_customer` VALUES ('40', '山东完全额外请有限公司', '1', '13575435786', '3', '0', '1498899380', '3', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498901780');
INSERT INTO `guguo_customer` VALUES ('41', '山东士大夫散发的时有限公司', '1', '13583524618', '3', '0', '1498899539', '3', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498901999');
INSERT INTO `guguo_customer` VALUES ('42', '山东梵蒂冈范德萨有限公司', '1', '13525252525', '3', '0', '1498899632', '3', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498902152');
INSERT INTO `guguo_customer` VALUES ('43', '山东扼杀地方有限公司', '1', '15414141414', '3', '0', '1498899671', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498902251');
INSERT INTO `guguo_customer` VALUES ('44', '山东是否有限公司', '1', '15832323232', '3', '0', '1498899705', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498902345');
INSERT INTO `guguo_customer` VALUES ('45', '山东让阿二额有限公司', '1', '15843212342', '3', '0', '1498899785', '3', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498902485');
INSERT INTO `guguo_customer` VALUES ('46', '山东士大夫有限公司', '1', '15323421342', '3', '0', '1498899803', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498902563');
INSERT INTO `guguo_customer` VALUES ('47', '山东额阿文她让热有限公司', '1', '15323122313', '3', '0', '1498899831', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1498902651');
INSERT INTO `guguo_customer` VALUES ('48', '山东的撒分割有限公司', '1', '15654326543', '3', '0', '1498899858', '5', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498902738');
INSERT INTO `guguo_customer` VALUES ('49', '山东大幅上升有限公司', '1', '15387653456', '3', '0', '1498899916', '5', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498902856');
INSERT INTO `guguo_customer` VALUES ('50', '山东儿啊否有限公司', '1', '15698723143', '3', '0', '1498900027', '5', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498903027');
INSERT INTO `guguo_customer` VALUES ('51', '山东阿斯顿有限公司', '1', '15643263252', '3', '0', '1498900054', '5', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498903114');
INSERT INTO `guguo_customer` VALUES ('52', '山东前往额有限公司', '1', '15685324643', '3', '0', '1498900290', '5', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1498903410');
INSERT INTO `guguo_customer` VALUES ('53', '山东中迅网络传媒有限公司', '2', '18888888888', '5', '0', '1499842385', '5', null, null, '0', '0', '3', 'A', '1', '1', '1', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1499845565');
INSERT INTO `guguo_customer` VALUES ('54', '山东柔可有限公司', '0', '15764297307', '5', '0', '1499934136', '5', null, null, '0', '0', '0', null, null, null, null, null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1499937376');
INSERT INTO `guguo_customer` VALUES ('55', '山东未命名有限公司', '0', '', '5', '0', '1500020302', '5', null, null, '0', '0', '0', null, null, null, null, null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1500023602');
INSERT INTO `guguo_customer` VALUES ('56', '山东涨势有限公司', '1', '110', '5', '0', '1500023834', '5', null, null, '0', '0', '0', 'A', '浙江省', '湖州市', '南浔区', 'sssssssssss', null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1500027194');
INSERT INTO `guguo_customer` VALUES ('57', '山东飞鸽有限公司', '1', '15488853221', '6', '0', '1500025953', '6', null, null, '0', '0', '0', 'C', '湖南省', '湘潭市', '雨湖区', '', null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1500029373');
INSERT INTO `guguo_customer` VALUES ('58', '山东爱可斯三有限公司', '0', '344578', '6', '0', '1500026193', '6', null, null, '0', '0', '0', 'D', null, null, null, null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1500029673');
INSERT INTO `guguo_customer` VALUES ('59', '山东涨笑有限公司', '1', '15644325677', '5', '0', '1500256937', '5', null, null, '0', '0', '0', 'A', '山东省', '潍坊市', '潍城区', null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1500260477');
INSERT INTO `guguo_customer` VALUES ('60', 'eeee', '3', 'wwwwwww', '5', '0', '1500608126', '5', null, null, '0', '0', '0', 'C', null, null, null, null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1500611726');
INSERT INTO `guguo_customer` VALUES ('61', 'rrrrr', '1', 'ttttt', '5', '0', '1500608302', '5', null, null, '0', '0', '0', 'D', null, null, null, null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1500611962');
INSERT INTO `guguo_customer` VALUES ('62', 'zhang ', '0', 'gggggg', '5', '0', '1500608424', '5', null, null, '0', '0', '0', 'A', '湖南省', '湘潭市', '雨湖区', null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1500612144');
INSERT INTO `guguo_customer` VALUES ('63', 'dddd', '0', '224556', '5', '0', '1501119205', '5', null, null, '0', '0', '0', null, null, null, null, null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1501122985');
INSERT INTO `guguo_customer` VALUES ('64', 'wwww', '0', '111', '9', '0', '1501577353', '9', null, null, '0', '0', '0', null, null, null, null, null, null, '0.000000', '0.000000', null, null, '3', '1', null, null, null, '1501577353');
INSERT INTO `guguo_customer` VALUES ('65', '广告法丰富的', '1', '134567866', '4', '0', '1501728850', '4', null, null, '0', '0', '0', 'C', '', '', '', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1501728850');
INSERT INTO `guguo_customer` VALUES ('66', '潍坊猛男公司', '3', '13346287454', '8', '0', '1502323490', '0', null, null, '0', '0', '1', 'A', '1', '1', '1', 'here', null, '0.000000', '0.000000', 'http://www.mengnan.com', '备注', '2', '1', null, null, null, '1502323490');
INSERT INTO `guguo_customer` VALUES ('67', '美杜莎', '2', '18053646666', '8', '0', '1502324880', '0', null, null, '0', '0', '2', 'B', '1', '1', '1', '', null, '0.000000', '0.000000', 'http://www.med.com', '', '2', '1', null, null, null, '1502324880');
INSERT INTO `guguo_customer` VALUES ('68', '毛不易', '1', '15236468745', '8', '0', '1502325624', '0', null, null, '0', '0', '3', 'A', '1', '1', '1', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1502325624');
INSERT INTO `guguo_customer` VALUES ('69', '客户1', '1', '18678018888', '12', '0', '1504168088', '12', null, null, '0', '0', '1', 'A', '1', '1', '1', '金艺大厦', null, '0.000000', '0.000000', '', '备注', '3', '1', null, null, null, '1504168088');
INSERT INTO `guguo_customer` VALUES ('70', '入', '2', '我们组', '6', '0', '1504229286', '6', null, null, '0', '0', '0', '', '山东省', '潍坊市', '潍城区', '吩咐的时候回去的事情', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504229286');
INSERT INTO `guguo_customer` VALUES ('71', '我的', '1', '我们自己', '5', '0', '1504258354', '5', null, null, '0', '0', '0', 'A', '湖南省', '湘潭市', '雨湖区', '我们', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504258354');
INSERT INTO `guguo_customer` VALUES ('72', '中国石油', '1', '2345677', '5', '0', '1504489174', '5', null, null, '0', '0', '0', 'A', '湖南省', '湘潭市', '雨湖区', '我的', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504489174');
INSERT INTO `guguo_customer` VALUES ('73', '123', '0', '123556', '5', '0', '1504489732', '5', null, null, '0', '0', '0', '', '', '', '', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504489732');
INSERT INTO `guguo_customer` VALUES ('74', '中华', '1', '135677', '5', '0', '1504490104', '5', null, null, '0', '0', '0', 'A', '', '', '', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504665440');
INSERT INTO `guguo_customer` VALUES ('75', '来往', '0', '23467', '5', '0', '1504490464', '0', null, null, '0', '0', '0', '', '', '', '', null, null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504490464');
INSERT INTO `guguo_customer` VALUES ('76', '我们自己都', '2', '14567885435', '2', '0', '1504490532', '2', null, null, '0', '0', '0', 'C', '', '', '', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504507881');
INSERT INTO `guguo_customer` VALUES ('77', '你的心', '0', '14456', '5', '0', '1504491299', '5', null, null, '0', '0', '0', '', '', '', '', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504491299');
INSERT INTO `guguo_customer` VALUES ('78', '20170904001', '1', '18222287632', '8', '0', '1504493166', '0', null, null, '0', '0', '2', 'B', '1', '1', '1', '潍城区金艺大厦7层', null, '0.000000', '0.000000', 'http://www.baidu.com', '', '2', '1', null, null, null, '1504493166');
INSERT INTO `guguo_customer` VALUES ('79', 'ere', '1', '18678018888', '12', '0', '1504507756', '12', null, null, '1', '1', '1', 'A', '山东省', '潍坊市', '潍城区', '', '', '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504507870');
INSERT INTO `guguo_customer` VALUES ('80', '客户1', '1', '0536-65527000', '12', '0', '1504509156', '12', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504509156');
INSERT INTO `guguo_customer` VALUES ('81', 'kehu', '1', '18778018888', '12', '0', '1504572174', '12', null, null, '0', '0', '1', 'A', '1', '1', '1', '金艺大厦', null, '0.000000', '0.000000', 'http://www.zhongxun.com', '备注信息', '3', '1', null, null, null, '1504572174');
INSERT INTO `guguo_customer` VALUES ('82', '客户1', '1', '18678018888', '12', '0', '1504573509', '0', null, null, '0', '0', '2', 'A', '1', '1', '1', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504573509');
INSERT INTO `guguo_customer` VALUES ('83', '测试1', '1', '18678018888', '12', '0', '1504574667', '12', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504574667');
INSERT INTO `guguo_customer` VALUES ('84', '客户名称', '1', '18678018888', '12', '0', '1504576155', '12', null, null, '1', '1', '1', 'A', '山东省', '潍坊市', '潍城区', '', '', '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504577026');
INSERT INTO `guguo_customer` VALUES ('85', '百度', '2', '17117171717', '8', '0', '1504593200', '0', null, null, '0', '0', '0', 'A', '1', '1', '1', '', null, '0.000000', '0.000000', 'http://www.baidu.com', '无', '2', '1', null, null, null, '1504593200');
INSERT INTO `guguo_customer` VALUES ('86', '阿里', '3', '17171717171', '8', '0', '1504600863', '0', null, null, '0', '0', '2', 'A', '1', '1', '1', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504600863');
INSERT INTO `guguo_customer` VALUES ('87', '啊', '1', '13444442323', '8', '0', '1504601206', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504601206');
INSERT INTO `guguo_customer` VALUES ('88', '山东是大多数发达有限公司', '1', '15655665656', '3', '0', '1504601235', '3', null, null, '0', '0', '0', 'A', '0', '0', '0', '', '(120.127661,38.672909)', '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504601235');
INSERT INTO `guguo_customer` VALUES ('89', '111', '1', '13000000000', '8', '0', '1504601592', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504601592');
INSERT INTO `guguo_customer` VALUES ('90', '毛不易', '1', '13311115555', '8', '0', '1504659694', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504659694');
INSERT INTO `guguo_customer` VALUES ('91', '中华', '1', '14522547778', '5', '0', '1504665110', '5', null, null, '0', '0', '0', 'A', '', '', '', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504665415');
INSERT INTO `guguo_customer` VALUES ('92', '问问', '1', '13311115555', '8', '0', '1504686560', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504686560');
INSERT INTO `guguo_customer` VALUES ('93', '问问', '1', '13311115555', '8', '0', '1504687094', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504687094');
INSERT INTO `guguo_customer` VALUES ('94', '1', '1', '13322221111', '8', '0', '1504687590', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504687590');
INSERT INTO `guguo_customer` VALUES ('95', 'kuojk', '1', '13322221111', '8', '0', '1504687727', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504687727');
INSERT INTO `guguo_customer` VALUES ('96', '二', '1', '13287661257', '9', '0', '1504689488', '9', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504689488');
INSERT INTO `guguo_customer` VALUES ('97', '123', '1', '13346287454', '8', '0', '1504694095', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504694095');
INSERT INTO `guguo_customer` VALUES ('98', '毛不易', '1', '13346287454', '8', '0', '1504694290', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504694290');
INSERT INTO `guguo_customer` VALUES ('99', '王丹', '2', '18678018888', '12', '0', '1504750376', '12', null, null, '1', '1', '1', 'C', '山东省', '潍坊市', '潍城区', '金艺大厦', '', '0.000000', '0.000000', 'http://www.zhongxun.com', '备注信息', '3', '1', null, null, null, '1504750491');
INSERT INTO `guguo_customer` VALUES ('100', '额', '1', '13311115555', '8', '0', '1504765336', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504765336');
INSERT INTO `guguo_customer` VALUES ('101', '地址客户', '1', '18678018888', '12', '0', '1504771017', '12', null, null, '0', '0', '1', 'A', '浙江省', '杭州市', '拱墅区', '金艺大厦', null, '0.000000', '0.000000', 'http://www.zhongxun.com', '备注', '3', '1', null, null, null, '1504771017');
INSERT INTO `guguo_customer` VALUES ('102', '毛不易', '1', '13346287454', '8', '0', '1504776834', '0', null, null, '0', '0', '0', 'A', '0', '0', '0', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504776834');
INSERT INTO `guguo_customer` VALUES ('103', '山东士大夫到沙发有限公司', '1', '010-58585518', '3', '2017090005', '1504776844', '3', null, null, null, null, '0', null, null, null, null, '山东省潍坊市维城区和平路胜利西街金艺大厦', null, '1.000000', '1.000000', 'http://www.baidusd.com/', null, '3', '1', null, null, null, '0');
INSERT INTO `guguo_customer` VALUES ('104', '山东士大夫订单有限公司', '1', '010-58585518', '3', '2017090007', '1504831615', '3', null, null, null, null, '1', null, null, null, null, '山东省潍坊市维城区和平路胜利西街金艺大厦', null, '1.000000', '1.000000', 'http://www.baidusd.com/', null, '3', '1', null, null, null, '0');
INSERT INTO `guguo_customer` VALUES ('105', '山东新建测试有限公司', '0', '15634534534', '3', '0', '1504834029', '3', '1', null, '0', '0', '0', 'A', '0', '0', '0', '', '', '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504834029');
INSERT INTO `guguo_customer` VALUES ('106', 'dg ', '1', '1253655444', '5', '0', '1504834031', '5', '0', null, '0', '0', '0', 'A', '湖南省', '湘潭市', '雨湖区', 'ssf ', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504834031');
INSERT INTO `guguo_customer` VALUES ('107', '哈哈哈哈', '2', '13455554444', '4', '0', '1504834072', '4', '0', null, '0', '0', '0', 'D', '湖南省', '湘潭市', '雨湖区', null, null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504834072');
INSERT INTO `guguo_customer` VALUES ('108', '哈哈哈哈', '1', '147258369', '5', '0', '1504834781', '5', '0', null, '0', '0', '0', 'A', '湖南省', '湘潭市', '雨湖区', '哈哈哈哈', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504851924');
INSERT INTO `guguo_customer` VALUES ('109', '恩恩', '0', '13322226667', '5', '0', '1504835916', '5', '1', null, '0', '0', '0', 'A', '0', '0', '0', '', '', '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504835916');
INSERT INTO `guguo_customer` VALUES ('110', '恩恩2', '0', '13322226667', '5', '0', '1504836857', '5', '1', null, '0', '0', '0', 'A', '0', '0', '0', '', '', '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504836857');
INSERT INTO `guguo_customer` VALUES ('111', '我的科技', '0', '13311112222', '3', '0', '1504837865', '3', '2', null, '0', '0', '0', 'B', '1', '1', '1', '', '(119.152604,36.748317)', '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1504837865');
INSERT INTO `guguo_customer` VALUES ('112', '标签用户', '0', '18678018888', '12', '0', '1504855402', '12', '1', null, '0', '0', '0', 'A', '北京市', '北京市', '东城区', '石景山', null, '39.916697', '116.228534', '', '个人标签', '3', '1', null, null, null, '1504855402');
INSERT INTO `guguo_customer` VALUES ('113', '标签客户1', '0', '18678018888', '12', '0', '1504855674', '12', '1', null, '0', '0', '0', 'A', '上海市', '上海市', '黄浦区', '国际机场', null, '31.196769', '121.395278', '', '个人标签', '3', '1', null, null, null, '1504855674');
INSERT INTO `guguo_customer` VALUES ('114', 'www', '0', '13311115553', '8', '0', '1504860162', '0', '1', null, '0', '0', '0', 'A', '上海市', '上海市', '普陀区', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504860162');
INSERT INTO `guguo_customer` VALUES ('115', '2', '0', '13322221111', '8', '0', '1504862245', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '有意向有意向有意向有意向无意向无意向', '2', '1', null, null, null, '1504862245');
INSERT INTO `guguo_customer` VALUES ('116', '毛不易', '0', '13311115553', '8', '0', '1504864998', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504864998');
INSERT INTO `guguo_customer` VALUES ('117', '123', '0', '13311115555', '8', '0', '1504921793', '0', '1', null, '0', '0', '0', 'A', '山东省', '潍坊市', '奎文区', '金艺大厦', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504921793');
INSERT INTO `guguo_customer` VALUES ('118', '毛不易', '0', '13311115555', '8', '0', '1504923157', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504923157');
INSERT INTO `guguo_customer` VALUES ('119', '毛不易', '0', '13322221111', '8', '0', '1504923267', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504923267');
INSERT INTO `guguo_customer` VALUES ('120', '毛不易', '0', '13311115555', '8', '0', '1504923591', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504923591');
INSERT INTO `guguo_customer` VALUES ('121', '123', '0', '13322221111', '8', '0', '1504926115', '0', '2', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504926115');
INSERT INTO `guguo_customer` VALUES ('122', '123', '0', '13346287454', '8', '0', '1504926975', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1504926975');
INSERT INTO `guguo_customer` VALUES ('123', '无联系人客户', '0', '18778018888', '12', '0', '1505093707', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '有意向', '3', '1', null, null, null, '1505093707');
INSERT INTO `guguo_customer` VALUES ('124', '无联系人客户1', '0', '18778018888', '12', '0', '1505094155', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '无意向', '3', '1', null, null, null, '1505094167');
INSERT INTO `guguo_customer` VALUES ('125', '无联系人客户2', '0', '18778018888', '12', '0', '1505094225', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '无意向', '3', '1', null, null, null, '1505094225');
INSERT INTO `guguo_customer` VALUES ('126', '无联系人客户3', '0', '18778018888', '12', '0', '1505094413', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '无意向', '3', '1', null, null, null, '1505094419');
INSERT INTO `guguo_customer` VALUES ('127', '无联系人客户4', '0', '18778018888', '12', '0', '1505094709', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505094765');
INSERT INTO `guguo_customer` VALUES ('128', '客户金宝村', '0', '18778018888', '12', '0', '1505094932', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505094932');
INSERT INTO `guguo_customer` VALUES ('129', '无联系人客户5', '0', '18778018888', '12', '0', '1505095488', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505095488');
INSERT INTO `guguo_customer` VALUES ('130', '无联系人客户', '0', '18678018888', '12', '0', '1505095954', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505095954');
INSERT INTO `guguo_customer` VALUES ('131', '无联系人客户6', '0', '18678018888', '12', '0', '1505096950', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505096950');
INSERT INTO `guguo_customer` VALUES ('132', '123', '0', '13311115553', '8', '0', '1505096991', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1505097004');
INSERT INTO `guguo_customer` VALUES ('133', '无联系人客户7', '0', '18778018888', '12', '0', '1505097132', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505097132');
INSERT INTO `guguo_customer` VALUES ('134', '1234', '0', '13311115553', '8', '0', '1505097375', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1505097375');
INSERT INTO `guguo_customer` VALUES ('135', '无联系人客户8', '0', '18778018888', '12', '0', '1505097389', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505097389');
INSERT INTO `guguo_customer` VALUES ('136', '额恩恩', '0', '13311115555', '8', '0', '1505097399', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1505097399');
INSERT INTO `guguo_customer` VALUES ('137', '毛不易', '0', '13311115555', '8', '0', '1505097410', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1505097410');
INSERT INTO `guguo_customer` VALUES ('138', '无联系人客户9', '0', '18778018888', '12', '0', '1505097649', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505097649');
INSERT INTO `guguo_customer` VALUES ('139', '跳过联系人1', '0', '18678018888', '12', '0', '1505098094', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505098094');
INSERT INTO `guguo_customer` VALUES ('140', '跳过联系人2', '0', '18778018888', '12', '0', '1505098248', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505098248');
INSERT INTO `guguo_customer` VALUES ('141', '表单二上一步', '0', '18678018888', '12', '0', '1505098344', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '2', '1', null, null, null, '1505098349');
INSERT INTO `guguo_customer` VALUES ('142', '表单二上一步，下一步再重新保存', '0', '18678018888', '12', '0', '1505098398', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505098401');
INSERT INTO `guguo_customer` VALUES ('143', '表单一直接保存', '0', '18678018888', '12', '0', '1505098452', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505098452');
INSERT INTO `guguo_customer` VALUES ('144', '跳过表单二，表单三重新写入下一步，表单上三保存', '0', '18678018888', '12', '0', '1505098521', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505098521');
INSERT INTO `guguo_customer` VALUES ('145', '一步一步返回修改的客户1', '0', '18678018888', '12', '0', '1505098666', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505098677');
INSERT INTO `guguo_customer` VALUES ('146', '12', '0', '18678018888', '12', '0', '1505098911', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505098927');
INSERT INTO `guguo_customer` VALUES ('147', '2333', '0', '18778018888', '12', '0', '1505099009', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505099027');
INSERT INTO `guguo_customer` VALUES ('148', '客户1', '0', '18778018888', '12', '0', '1505099046', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505099046');
INSERT INTO `guguo_customer` VALUES ('149', '测试联系人的客户', '0', '18778018888', '12', '0', '1505100385', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505100436');
INSERT INTO `guguo_customer` VALUES ('150', '客户1', '0', '18678018888', '12', '0', '1505101601', '12', '1', null, '1', '1', '1', 'A', '山东省', '烟台市', '莱山区', '详细地址', '金艺大厦', '0.000000', '0.000000', '', '个人的标签', '3', '1', null, null, null, '1505444361');
INSERT INTO `guguo_customer` VALUES ('151', '额', '0', '13311115555', '8', '0', '1505102055', '0', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '有对对对意向有对对对意向有对对对意向有对对对意向无意向', '2', '1', null, null, null, '1505102055');
INSERT INTO `guguo_customer` VALUES ('152', '猛男协会', '0', '13311115555', '8', '0', '1505116272', '8', '3', null, '1', '1', '1', 'A', '山东省', '潍坊市', '潍城区', '金艺大厦7层', '', '0.000000', '0.000000', 'http://www.baidu.com', '有对对对意向无意向对对对3333对对对无意向有对对对意向', '3', '1', null, null, null, '1505440175');
INSERT INTO `guguo_customer` VALUES ('153', '服务器2', '0', '13311115555', '8', '0', '1505440314', '8', '2', null, '0', '0', '1', 'C', '山东省', '潍坊市', '潍城区', '', null, '36.723540', '119.044329', 'http://www.mengnan.com', '无意向无意向无意向无意向', '3', '1', null, null, null, '1505440952');
INSERT INTO `guguo_customer` VALUES ('154', '毛不易', '0', '13311115555', '8', '0', '1505441669', '8', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505441669');
INSERT INTO `guguo_customer` VALUES ('155', '毛不易', '0', '13311115553', '8', '0', '1505442174', '8', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505442174');
INSERT INTO `guguo_customer` VALUES ('156', '123', '0', '13322221111', '8', '0', '1505442384', '8', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505442384');
INSERT INTO `guguo_customer` VALUES ('157', '', '0', '13311115555', '8', '0', '1505442509', '8', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505442509');
INSERT INTO `guguo_customer` VALUES ('158', '阿三发达', '0', '15566667777', '3', '0', '1505442638', '3', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505442638');
INSERT INTO `guguo_customer` VALUES ('159', '客户客户客户', '0', '18678018888', '12', '0', '1505444985', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505444985');
INSERT INTO `guguo_customer` VALUES ('160', '客户客户酷划', '0', '18678018888', '12', '0', '1505445001', '12', '1', null, '1', '1', '1', 'A', '省份', '地级市', '市、县级市', '', '', '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505445704');
INSERT INTO `guguo_customer` VALUES ('161', '有联系人客户', '0', '18678018888', '12', '0', '1505445016', '12', '1', null, '1', '1', '1', 'A', '省份', '地级市', '市、县级市', '', '', '0.000000', '0.000000', '', '有对对对意向', '3', '1', null, null, null, '1505461531');
INSERT INTO `guguo_customer` VALUES ('162', '额', '0', '13322221111', '8', '0', '1505445532', '8', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505445574');
INSERT INTO `guguo_customer` VALUES ('163', '额', '0', '13311115553', '8', '0', '1505445638', '8', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1505445639');
INSERT INTO `guguo_customer` VALUES ('164', '新建客户数PK测试', '0', '13311112222', '3', '0', '1506154254', '3', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1506154254');
INSERT INTO `guguo_customer` VALUES ('165', '客户我是', '2', '13210763325', '4', '0', '1506154268', '4', '0', null, '0', '0', '0', 'A', '湖南省', '湘潭市', '雨湖区', '哈哈哈哈', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1506154268');
INSERT INTO `guguo_customer` VALUES ('166', '新建客户数PK测试2', '0', '13311112223', '3', '0', '1506154303', '3', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1506154303');
INSERT INTO `guguo_customer` VALUES ('167', '测试测试', '0', '15555555555', '5', '0', '1506154750', '5', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1506154750');
INSERT INTO `guguo_customer` VALUES ('168', '为了激励新增的客户', '0', '18678018888', '12', '0', '1506305813', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1506305813');
INSERT INTO `guguo_customer` VALUES ('169', '激励任务的新增客户1', '0', '18678018888', '12', '0', '1506305842', '12', '1', null, '0', '0', '0', 'A', '省份', '地级市', '市、县级市', '', null, '0.000000', '0.000000', '', '', '3', '1', null, null, null, '1506305842');

-- ----------------------------
-- Table structure for guguo_customer_contact
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer_contact`;
CREATE TABLE `guguo_customer_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `contact_name` varchar(64) NOT NULL COMMENT '联系人姓名',
  `sex` tinyint(1) unsigned DEFAULT '1',
  `phone_first` varchar(13) NOT NULL COMMENT '联系人首要电话',
  `phone_second` varchar(13) DEFAULT NULL COMMENT '联系人备用电话',
  `phone_third` varchar(13) DEFAULT NULL COMMENT '联系人备用电话',
  `email` varchar(128) DEFAULT NULL COMMENT '邮箱',
  `qqnum` varchar(12) DEFAULT NULL COMMENT 'qq号',
  `wechat` varchar(64) DEFAULT NULL COMMENT '微信',
  `structure` varchar(64) DEFAULT NULL COMMENT '所在部门',
  `occupation` varchar(64) DEFAULT NULL COMMENT '职位',
  `key_decide` tinyint(1) DEFAULT '0' COMMENT '是否关键决策人0否，1是',
  `deal_capability` tinyint(1) DEFAULT NULL COMMENT '决策能力',
  `introducer` varchar(16) DEFAULT '' COMMENT '客户介绍人，本公司',
  `close_degree` tinyint(1) DEFAULT '0' COMMENT '亲密度',
  `birthday` varchar(16) DEFAULT NULL COMMENT '生日，如19901122',
  `hobby` varchar(256) DEFAULT NULL COMMENT '爱好',
  `remark` varchar(256) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `create_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_customer_contact
-- ----------------------------
INSERT INTO `guguo_customer_contact` VALUES ('1', '11', '刘六溜', '1', '16666666666', '010-56565516', '', '', '', '', '', '', '0', '1', '0', '1', '', '', '', '1498682919', '3');
INSERT INTO `guguo_customer_contact` VALUES ('2', '25', '李白', '1', '18888888888', '', '', '', '', '', '', '', null, '1', '0', '0', '', '', '', '1498682919', '3');
INSERT INTO `guguo_customer_contact` VALUES ('3', '26', '发达', '1', '18888888888', '', '', 'sfds@asda.com', '123123123', '1231312', '时发生的', '到沙发大饭店时', '0', '2', '哈哈哈', '1', '1504713600', '撒发射的', '啊额撒他是个否', '1498682919', '3');
INSERT INTO `guguo_customer_contact` VALUES ('4', '26', '萨达', '1', '18877777777', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1498682919', '3');
INSERT INTO `guguo_customer_contact` VALUES ('5', '0', '阿达', '1', '188888888888', '', '', '', '', '', '士大夫', '都是发生大', null, '1', '0', '0', '', 'safSFDz ', '', '1498696029', '3');
INSERT INTO `guguo_customer_contact` VALUES ('6', '26', '阿达', '1', '158888888888', '', '', '', '', '', '', '', '1', '1', '0', '0', '', '', '', '1498696331', '3');
INSERT INTO `guguo_customer_contact` VALUES ('7', '27', '大声说发达', '1', '15588888888', '', '', '', '', '', '', '撒范德萨地方', null, '2', '0', '0', '', '', '', '1498705442', '3');
INSERT INTO `guguo_customer_contact` VALUES ('8', '27', '联系人', '1', '15766666666', '', '', '', '', '', '', '', null, '1', '0', '0', '', '阿斯顿打赏', '', '1498705608', '3');
INSERT INTO `guguo_customer_contact` VALUES ('9', '11', '上达到', '1', '15677777777', '', '', '', '', '', '', '', null, '2', '0', '0', '', '', '', '1498886600', '3');
INSERT INTO `guguo_customer_contact` VALUES ('10', '38', '前往额啊', '1', '', '', '', '', '', '', '', '', null, '2', '0', '0', '', '', '', '1498899160', '3');
INSERT INTO `guguo_customer_contact` VALUES ('11', '39', '阿达发热额', '1', '15746566787', '', '', '', '', '', '阿斯顿否', '', null, '1', '0', '0', '', '', '', '1498899356', '3');
INSERT INTO `guguo_customer_contact` VALUES ('12', '27', 'zhang', '1', '12345', null, null, null, null, null, null, null, null, null, null, null, null, null, null, '1502156032', '5');
INSERT INTO `guguo_customer_contact` VALUES ('13', '21', 'aaaaaa', '1', '155555555', null, null, null, '3455666', null, null, null, null, null, null, null, null, null, null, '1502157329', '5');
INSERT INTO `guguo_customer_contact` VALUES ('14', '27', 'Cheshire', '1', '1123667', null, null, null, null, null, null, null, null, null, null, null, null, null, null, '1502158570', '5');
INSERT INTO `guguo_customer_contact` VALUES ('15', '17', 'sssss', '1', '1223567833', null, null, null, null, null, null, null, null, null, null, null, null, null, null, '1502173183', '5');
INSERT INTO `guguo_customer_contact` VALUES ('16', '11', '测试测试', '1', '18769714761', '0536-88888889', '', '', '123456789', '', '', '', '0', '1', '0', '0', '', '', '', '1502328026', '3');
INSERT INTO `guguo_customer_contact` VALUES ('17', '35', '发的', '1', '发GV', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1503907706', '9');
INSERT INTO `guguo_customer_contact` VALUES ('18', '35', '二', '1', '发的', '', '32', '', '得出', '', '', '', '0', '1', '0', '0', '', '', '', '1503907733', '9');
INSERT INTO `guguo_customer_contact` VALUES ('19', '70', '拖', '1', '189161搂着', '', '', '', '', '', '', '', '0', '0', '0', '0', '0', '', '', '1504229355', '6');
INSERT INTO `guguo_customer_contact` VALUES ('20', '78', '韩信', '1', '18222287632', '18333387633', '18444487634', '18555587635@gmial.com', '18666687636', '187777876535', '财务部', '财务主管', '1', '6', '0', '1', '', '打篮球', '智障，谁设计的这些字段', '1504493394', '8');
INSERT INTO `guguo_customer_contact` VALUES ('21', '76', '你是', '1', '15688643332', '', '', '', '', '', '', '', '0', '0', '0', '0', '0', '', '', '1504495766', '2');
INSERT INTO `guguo_customer_contact` VALUES ('22', '76', '张三', '1', '110', '', '', '', '734134823', '', '', '领导', '0', '0', '0', '0', '0', '', '', '1504495904', '2');
INSERT INTO `guguo_customer_contact` VALUES ('23', '76', '我的', '1', '12356788', '', '', '', '', '', '', '', '0', '0', '0', '0', '0', '', '', '1504509133', '2');
INSERT INTO `guguo_customer_contact` VALUES ('24', '76', '我的心是', '1', '1234667', '', '', '', '', '', '', '', '0', '0', '0', '0', '0', '', '', '1504509146', '2');
INSERT INTO `guguo_customer_contact` VALUES ('25', '76', '王明', '1', '134678654', '', '', '', '', '', '', '', '0', '0', '0', '0', '0', '', '', '1504509788', '2');
INSERT INTO `guguo_customer_contact` VALUES ('26', '81', '王总1', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504572189', '12');
INSERT INTO `guguo_customer_contact` VALUES ('27', '82', '联系人', '1', '0536-66627000', '18678018888', '18678018888', '18678018888', '18678018888', '18678018888', '', '', '0', '1', '0', '0', '', '', '', '1504573571', '12');
INSERT INTO `guguo_customer_contact` VALUES ('28', '83', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504574674', '12');
INSERT INTO `guguo_customer_contact` VALUES ('29', '84', '', '1', '18678018888', '18678018888', '18678018888', '18678018888@qq.com', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504576227', '12');
INSERT INTO `guguo_customer_contact` VALUES ('30', '86', '马云云', '1', '15000000000', '', '', '', '959595959', '', '', '', '1', '2', '0', '0', '', '', '', '1504600913', '8');
INSERT INTO `guguo_customer_contact` VALUES ('31', '88', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504601238', '3');
INSERT INTO `guguo_customer_contact` VALUES ('32', '89', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504601595', '8');
INSERT INTO `guguo_customer_contact` VALUES ('33', '90', '猛男', '1', '13322225565', '', '', '', '959595959', '959595959', '厌烦', '发发', '0', '1', '0', '0', '', '懒', '无备注', '1504659900', '8');
INSERT INTO `guguo_customer_contact` VALUES ('34', '85', '啊', '1', '13333333333', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504660482', '8');
INSERT INTO `guguo_customer_contact` VALUES ('35', '65', 'the', '1', '135677644', '', '', '', '', '', '', '', '0', '0', '0', '0', '0', '', '', '1504669758', '4');
INSERT INTO `guguo_customer_contact` VALUES ('36', '92', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504686577', '8');
INSERT INTO `guguo_customer_contact` VALUES ('37', '93', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504687133', '8');
INSERT INTO `guguo_customer_contact` VALUES ('38', '94', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504687591', '8');
INSERT INTO `guguo_customer_contact` VALUES ('39', '95', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504687729', '8');
INSERT INTO `guguo_customer_contact` VALUES ('40', '97', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504694097', '8');
INSERT INTO `guguo_customer_contact` VALUES ('41', '98', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504694290', '8');
INSERT INTO `guguo_customer_contact` VALUES ('42', '99', '', '1', '', '', '', '', '', '', '', '', '0', '1', '0', '0', '', '', '', '1504750390', '12');
INSERT INTO `guguo_customer_contact` VALUES ('43', '102', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1504776911', '8');
INSERT INTO `guguo_customer_contact` VALUES ('44', '105', '联系联系', '1', '15656565656', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1504834046', '3');
INSERT INTO `guguo_customer_contact` VALUES ('45', '17', '我的', '1', '14567', '', '', '', '', '', '', '', '0', '0', '', '0', '0', '', '', '1504835455', '5');
INSERT INTO `guguo_customer_contact` VALUES ('46', '109', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1504835939', '5');
INSERT INTO `guguo_customer_contact` VALUES ('47', '110', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1504836859', '5');
INSERT INTO `guguo_customer_contact` VALUES ('48', '111', '操心细节', '1', '13222222222', '', '', '', '', '', '', '', '0', '2', '', '0', '', '', '', '1504837973', '3');
INSERT INTO `guguo_customer_contact` VALUES ('49', '112', '', '1', '', '', '', '', '', '', '', '', '1', '2', 'ee', '1', '1504454400', '爱好', '', '1504855467', '12');
INSERT INTO `guguo_customer_contact` VALUES ('50', '113', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '个人标签网站建设意向', '1504855835', '12');
INSERT INTO `guguo_customer_contact` VALUES ('51', '119', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1504923277', '8');
INSERT INTO `guguo_customer_contact` VALUES ('52', '120', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1504923598', '8');
INSERT INTO `guguo_customer_contact` VALUES ('53', '123', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505093731', '12');
INSERT INTO `guguo_customer_contact` VALUES ('54', '124', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505094159', '12');
INSERT INTO `guguo_customer_contact` VALUES ('55', '125', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505094257', '12');
INSERT INTO `guguo_customer_contact` VALUES ('56', '126', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505094416', '12');
INSERT INTO `guguo_customer_contact` VALUES ('57', '127', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505094713', '12');
INSERT INTO `guguo_customer_contact` VALUES ('58', '130', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505096063', '12');
INSERT INTO `guguo_customer_contact` VALUES ('59', '132', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505096992', '8');
INSERT INTO `guguo_customer_contact` VALUES ('60', '138', '问问', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505097666', '12');
INSERT INTO `guguo_customer_contact` VALUES ('61', '142', 'Erin', '1', '18678028888', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505098423', '12');
INSERT INTO `guguo_customer_contact` VALUES ('62', '144', 'Erin', '1', '05366550000', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505098553', '12');
INSERT INTO `guguo_customer_contact` VALUES ('63', '145', 'Erin1', '1', '05366550000', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505098691', '12');
INSERT INTO `guguo_customer_contact` VALUES ('64', '148', 'Erin', '1', '05366553000', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505099318', '12');
INSERT INTO `guguo_customer_contact` VALUES ('65', '149', 'Erin', '1', '05366550000', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505100475', '12');
INSERT INTO `guguo_customer_contact` VALUES ('66', '151', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '有对对对意向无意向对对对', '1505102061', '8');
INSERT INTO `guguo_customer_contact` VALUES ('67', '150', 'Erin', '1', '18678018888', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '无意向', '1505187186', '12');
INSERT INTO `guguo_customer_contact` VALUES ('68', '150', 'Erin1', '1', '18678018888', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505187248', '12');
INSERT INTO `guguo_customer_contact` VALUES ('69', '150', '', '1', '', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505197304', '12');
INSERT INTO `guguo_customer_contact` VALUES ('70', '161', '联系人', '1', '18678018888', '', '', '', '', '', '', '', '0', '1', '', '0', '', '', '', '1505445052', '12');

-- ----------------------------
-- Table structure for guguo_customer_import_fail
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer_import_fail`;
CREATE TABLE `guguo_customer_import_fail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch` int(11) NOT NULL COMMENT '导入批次',
  `customer_name` varchar(128) DEFAULT NULL COMMENT '公司名称',
  `telephone` varchar(13) DEFAULT NULL COMMENT '电话号码',
  `address` varchar(128) DEFAULT NULL COMMENT '地址',
  `location` varchar(64) DEFAULT NULL COMMENT '定位',
  `field` varchar(64) DEFAULT NULL COMMENT '行业',
  `website` varchar(128) DEFAULT NULL COMMENT '官网',
  `remark` varchar(255) DEFAULT NULL COMMENT '失败备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_customer_import_fail
-- ----------------------------
INSERT INTO `guguo_customer_import_fail` VALUES ('4', '2017050004', '山东中迅网络传媒有限公司', '010-58585518', '山东省潍坊市维城区和平路胜利西街金艺大厦', '119.11376,36.713159', '互联网', 'http://www.baidusd.com/', '手机号码格式不正确');
INSERT INTO `guguo_customer_import_fail` VALUES ('5', '2017090002', '山东中迅网络传媒有限公司', '010-58585518', '山东省潍坊市维城区和平路胜利西街金艺大厦', '119.11376,36.713159', '互联网', 'http://www.baidusd.com/', 'SQLSTATE[HY000]: General error: 1366 Incorrect integer value: \'互联网\' for column \'field\' at row 1');
INSERT INTO `guguo_customer_import_fail` VALUES ('6', '2017090003', '山东中迅网络传媒有限公司', '010-58585518', '山东省潍坊市维城区和平路胜利西街金艺大厦', '119.11376,36.713159', '互联网', 'http://www.baidusd.com/', 'SQLSTATE[HY000]: General error: 1366 Incorrect integer value: \'互联网\' for column \'field\' at row 1');
INSERT INTO `guguo_customer_import_fail` VALUES ('7', '2017090004', '山东中迅网络传媒有限公司', '010-58585518', '山东省潍坊市维城区和平路胜利西街金艺大厦', '119.11376,36.713159', 'IT行业', 'http://www.baidusd.com/', 'SQLSTATE[HY000]: General error: 1366 Incorrect integer value: \'IT行业\' for column \'field\' at row 1');

-- ----------------------------
-- Table structure for guguo_customer_import_record
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer_import_record`;
CREATE TABLE `guguo_customer_import_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) DEFAULT NULL COMMENT '导入时间',
  `operator` int(11) DEFAULT NULL COMMENT '操作者',
  `import_result` tinyint(1) DEFAULT NULL COMMENT '导入结果，0全部失败，1部分失败，2全部成功',
  `import_to` tinyint(4) DEFAULT NULL COMMENT '导入位置,1客户管理，2公海池，3我的客户，4待处理',
  `success_num` int(11) DEFAULT NULL COMMENT '导入成功的数量',
  `fail_num` int(11) DEFAULT NULL COMMENT '导入失败的数量',
  `batch` int(10) DEFAULT NULL COMMENT '导入批次，格式：201704280001',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_customer_import_record
-- ----------------------------
INSERT INTO `guguo_customer_import_record` VALUES ('5', '1495527149', '1', '0', '2', '0', '1', '2017050004');
INSERT INTO `guguo_customer_import_record` VALUES ('12', '1495527860', '1', '2', '3', '1', '0', '2017050007');
INSERT INTO `guguo_customer_import_record` VALUES ('13', '1504777443', '3', '0', null, '0', '0', '2017090001');
INSERT INTO `guguo_customer_import_record` VALUES ('14', '1504777530', '3', '0', '3', '0', '1', '2017090002');
INSERT INTO `guguo_customer_import_record` VALUES ('15', '1504830922', '3', '0', '3', '0', '1', '2017090003');
INSERT INTO `guguo_customer_import_record` VALUES ('16', '1504831010', '3', '0', '3', '0', '1', '2017090004');
INSERT INTO `guguo_customer_import_record` VALUES ('17', '1504831260', '3', '2', '3', '1', '0', '2017090005');
INSERT INTO `guguo_customer_import_record` VALUES ('18', '1504831595', '3', '0', null, '0', '0', '2017090006');
INSERT INTO `guguo_customer_import_record` VALUES ('19', '1504831615', '3', '2', '3', '1', '0', '2017090007');

-- ----------------------------
-- Table structure for guguo_customer_negotiate
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer_negotiate`;
CREATE TABLE `guguo_customer_negotiate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `tend_to` tinyint(1) DEFAULT '0' COMMENT '0无意向1有意向',
  `phone_correct` tinyint(1) DEFAULT '1' COMMENT '0号码有误，1号码正确',
  `profile_correct` tinyint(1) DEFAULT NULL COMMENT '个人资料，0有误，1正确',
  `call_through` tinyint(1) DEFAULT '1' COMMENT '0未接通，1可拨通',
  `is_wait` tinyint(1) DEFAULT '0' COMMENT '0非待定，1待定',
  `wait_alarm_time` int(10) unsigned DEFAULT NULL COMMENT '待沟通提醒时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_customer_negotiate
-- ----------------------------
INSERT INTO `guguo_customer_negotiate` VALUES ('1', '11', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('2', '15', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('3', '16', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('4', '17', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('5', '18', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('6', '19', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('7', '20', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('8', '21', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('9', '22', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('10', '23', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('11', '24', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('12', '25', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('13', '27', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('14', '28', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('15', '29', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('16', '30', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('17', '31', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('18', '32', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('19', '33', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('20', '34', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('21', '35', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('22', '36', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('23', '37', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('24', '38', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('25', '39', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('26', '40', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('27', '41', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('28', '42', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('29', '43', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('30', '44', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('31', '45', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('32', '46', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('33', '47', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('34', '48', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('35', '49', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('36', '50', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('37', '51', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('38', '52', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('39', '53', '0', '1', '1', '1', '1', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('40', '54', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('41', '55', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('42', '56', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('43', '57', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('44', '58', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('45', '59', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('46', '60', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('47', '61', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('48', '62', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('49', '63', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('50', '64', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('51', '65', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('52', '66', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('53', '67', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('54', '68', '0', '1', '1', '1', '1', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('55', '69', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('56', '70', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('57', '71', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('58', '72', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('59', '73', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('60', '74', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('61', '75', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('62', '76', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('63', '77', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('64', '78', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('65', '79', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('66', '80', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('67', '81', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('68', '82', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('69', '83', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('70', '84', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('71', '85', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('72', '86', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('73', '87', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('74', '88', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('75', '89', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('76', '90', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('77', '91', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('78', '92', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('79', '93', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('80', '94', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('81', '95', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('82', '96', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('83', '97', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('84', '98', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('85', '99', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('86', '100', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('87', '101', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('88', '102', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('89', '105', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('90', '106', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('91', '107', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('92', '108', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('93', '109', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('94', '110', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('95', '111', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('96', '112', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('97', '113', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('98', '114', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('99', '115', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('100', '116', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('101', '117', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('102', '118', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('103', '119', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('104', '120', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('105', '121', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('106', '122', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('107', '123', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('108', '124', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('109', '125', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('110', '126', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('111', '127', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('112', '128', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('113', '129', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('114', '130', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('115', '131', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('116', '132', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('117', '133', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('118', '134', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('119', '135', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('120', '136', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('121', '137', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('122', '138', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('123', '139', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('124', '140', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('125', '141', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('126', '142', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('127', '143', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('128', '144', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('129', '145', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('130', '146', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('131', '147', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('132', '148', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('133', '149', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('134', '150', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('135', '151', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('136', '152', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('137', '153', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('138', '154', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('139', '155', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('140', '156', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('141', '157', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('142', '158', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('143', '159', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('144', '160', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('145', '161', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('146', '162', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('147', '163', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('148', '164', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('149', '165', '0', '1', null, '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('150', '166', '1', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('151', '167', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('152', '168', '0', '1', '1', '1', '0', null);
INSERT INTO `guguo_customer_negotiate` VALUES ('153', '169', '0', '1', '1', '1', '0', null);

-- ----------------------------
-- Table structure for guguo_customer_product_type
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer_product_type`;
CREATE TABLE `guguo_customer_product_type` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `product_type` varchar(64) NOT NULL COMMENT '产品类型',
  `bill_id` int(11) NOT NULL COMMENT '发票id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_customer_product_type
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_customer_search
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer_search`;
CREATE TABLE `guguo_customer_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search_from` tinyint(4) unsigned DEFAULT NULL COMMENT '搜索来源(百度、搜狗、360)',
  `customer_name` varchar(128) NOT NULL COMMENT '客户名称(公司名称)',
  `contact_name` varchar(64) DEFAULT NULL COMMENT '联系人姓名',
  `phone` varchar(13) NOT NULL COMMENT '联系人电话',
  `industry` varchar(64) DEFAULT NULL COMMENT '行业',
  `com_adds` varchar(256) DEFAULT NULL COMMENT '公司地址',
  `website` varchar(256) DEFAULT NULL COMMENT '公司官网',
  `create_user` int(10) NOT NULL COMMENT '创建用户',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '搜索来源',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_customer_search
-- ----------------------------
INSERT INTO `guguo_customer_search` VALUES ('1', '1', '测试搜索客户0', '客户0', '18888888888', 'IT0', '地址0', '0', '3', '1493264325', '1');
INSERT INTO `guguo_customer_search` VALUES ('2', '1', '测试搜索客户1', '客户1', '18888888889', 'IT1', '地址1', '1', '3', '1493265188', '1');
INSERT INTO `guguo_customer_search` VALUES ('3', '1', '测试搜索客户2', '客户2', '18888888899', 'IT2', '地址2', '2', '3', '1493265193', '1');
INSERT INTO `guguo_customer_search` VALUES ('4', '1', '山东中迅网络传媒有限公司', '刘六溜', '16666666666', 'IT3', null, null, '3', '1', '1');

-- ----------------------------
-- Table structure for guguo_customer_setting
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer_setting`;
CREATE TABLE `guguo_customer_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(64) DEFAULT NULL,
  `protect_customer_day` smallint(5) unsigned DEFAULT '0' COMMENT '客户保护天数',
  `take_times_employee` smallint(5) unsigned DEFAULT '0' COMMENT '同一客户领取次数，员工',
  `take_times_structure` smallint(5) unsigned DEFAULT '0' COMMENT '同一客户领取次数，部门',
  `to_halt_day` smallint(6) DEFAULT NULL COMMENT '划归停滞客户的天数',
  `effective_call` smallint(6) DEFAULT NULL COMMENT '有效通话时间，单位秒',
  `protect_customer_num` smallint(6) DEFAULT NULL COMMENT '保护客户个数',
  `public_sea_seen` tinyint(1) DEFAULT '1' COMMENT '公海池客户名称可见与否，1是，0否',
  `set_to_structure` varchar(128) DEFAULT NULL COMMENT '设置给的部门id，逗号分隔',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_customer_setting
-- ----------------------------
INSERT INTO `guguo_customer_setting` VALUES ('1', '客户设置1', '1', '0', '0', '1', '1', '2', '1', '1,2,3');
INSERT INTO `guguo_customer_setting` VALUES ('2', '客户设置2', '2', '3', '4', '1', '2', '3', '1', '2,3,4');
INSERT INTO `guguo_customer_setting` VALUES ('3', '客户设置3', '12', '12', '12', '12', '12', '12', '0', '2');
INSERT INTO `guguo_customer_setting` VALUES ('4', '客户设置4', '12', '12', '12', '12', '12', '20', '1', '1,2,3,4');
INSERT INTO `guguo_customer_setting` VALUES ('5', '客户设置5', '12', '12', '12', '12', '12', '12', '1', '3,4,5');
INSERT INTO `guguo_customer_setting` VALUES ('6', '客户设置6', '3', '10', '12', '3', '100', '12', '1', '7');
INSERT INTO `guguo_customer_setting` VALUES ('7', '客户设置测试', '1', '2', '3', '4', '5', '6', '0', '1,2,3');

-- ----------------------------
-- Table structure for guguo_customer_trace
-- ----------------------------
DROP TABLE IF EXISTS `guguo_customer_trace`;
CREATE TABLE `guguo_customer_trace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '添加类型 0:系统,1:人工',
  `operator_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '操作类型 0:编辑信息,1:电话',
  `operator_id` int(11) NOT NULL COMMENT '操作员工id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `db_table_name` varchar(64) NOT NULL COMMENT '表名',
  `db_field_name` varchar(64) NOT NULL COMMENT '字段名',
  `old_value` varchar(255) NOT NULL COMMENT '旧值',
  `new_value` varchar(255) NOT NULL COMMENT '新值',
  `value_type` varchar(32) DEFAULT NULL COMMENT '值类型',
  `option_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '操作状态',
  `option_name` varchar(64) NOT NULL COMMENT '操作名',
  `sub_name` varchar(64) NOT NULL COMMENT '子项目名',
  `item_name` varchar(64) NOT NULL COMMENT '操作项名',
  `from_name` varchar(64) NOT NULL COMMENT '来源名',
  `link_name` varchar(64) NOT NULL COMMENT '连接名',
  `to_name` varchar(64) NOT NULL COMMENT '目标名',
  `status_name` varchar(64) DEFAULT NULL COMMENT '状态名',
  `remark` varchar(765) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=492 DEFAULT CHARSET=utf8 COMMENT='记录对单个客户的更改';

-- ----------------------------
-- Records of guguo_customer_trace
-- ----------------------------
INSERT INTO `guguo_customer_trace` VALUES ('1', '0', '0', '2', '1494816850', '1', 'customer', 'handle_man', '', '', null, '0', '转移', '', '客户', '', '到', '上级领导', '', '转移客户到上级领导');
INSERT INTO `guguo_customer_trace` VALUES ('2', '0', '0', '2', '1494819575', '1', 'customer', 'handle_man', '', '', null, '1', '转移', '', '客户', '', '', '', '成功', '转移客户到上级领导成功');
INSERT INTO `guguo_customer_trace` VALUES ('3', '0', '0', '3', '1495527860', '11', 'customer', 'handle_man', '', '', null, '1', '领取', '', '客户', '', '', '', '', '领取客户');
INSERT INTO `guguo_customer_trace` VALUES ('4', '0', '0', '3', '1501565784', '11', 'customer', 'grade', 'B', 'A', '', '0', '更改了', '', '客户级别', 'B', '更改为', 'A', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('10', '0', '0', '3', '1501567474', '11', 'customer', 'prov', '1', '山东省', '', '0', '更改了', '', '省份', '1', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('11', '0', '0', '3', '1501567474', '11', 'customer', 'city', '1', '潍坊市', '', '0', '更改了', '', '城市', '1', '更改为', '潍坊市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('12', '0', '0', '3', '1501567474', '11', 'customer', 'dist', '1', '潍城区', '', '0', '更改了', '', '区县', '1', '更改为', '潍城区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('20', '0', '0', '3', '1501568847', '11', 'customer', 'field1', '2', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '金融行业', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('21', '0', '0', '3', '1501579907', '11', 'customer', 'telephone', '010-58585518', '010-58585519', '', '0', '更改了', '', '联系电话', '010-58585518', '更改为', '010-58585519', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('22', '0', '0', '3', '1501640947', '11', 'customer', 'grade', 'A', 'B', '', '0', '更改了', '', '客户级别', 'A', '更改为', 'B', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('23', '0', '0', '3', '1501641032', '11', 'customer', 'grade', 'B', 'A', '', '0', '更改了', '', '客户级别', 'B', '更改为', 'A', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('27', '0', '0', '3', '1501641026', '11', 'customer', 'address', '潍坊市潍城区和平路胜利西街', '潍坊市潍城区和平路胜利西街金艺大厦', '', '0', '更改了', '', '详细地址', '潍坊市潍城区和平路胜利西街', '更改为', '潍坊市潍城区和平路胜利西街金艺大厦', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('28', '0', '0', '3', '1501641026', '11', 'customer', 'location', '金艺大厦', '', '', '0', '更改了', '', '详细定位', '金艺大厦', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('30', '0', '0', '3', '1501641037', '11', 'customer', 'address', '潍坊市潍城区和平路胜利西街金艺大厦', '潍坊市潍城区和平路胜利西街', '', '0', '更改了', '', '详细地址', '潍坊市潍城区和平路胜利西街金艺大厦', '更改为', '潍坊市潍城区和平路胜利西街', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('31', '0', '0', '3', '1501641037', '11', 'customer', 'location', '', '金艺大厦', '', '0', '更改了', '', '详细定位', '', '更改为', '金艺大厦', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('33', '0', '0', '3', '1501641136', '11', 'customer', 'address', '潍坊市潍城区和平路胜利西街', '潍坊市潍城区和平路胜利西街金艺大厦', '', '0', '更改了', '', '详细地址', '潍坊市潍城区和平路胜利西街', '更改为', '潍坊市潍城区和平路胜利西街金艺大厦', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('34', '0', '0', '3', '1501641136', '11', 'customer', 'location', '金艺大厦', '', '', '0', '更改了', '', '详细定位', '金艺大厦', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('40', '0', '0', '3', '1501641142', '11', 'customer', 'address', '潍坊市潍城区和平路胜利西街金艺大厦', '潍坊市潍城区和平路胜利西街', '', '0', '更改了', '', '详细地址', '潍坊市潍城区和平路胜利西街金艺大厦', '更改为', '潍坊市潍城区和平路胜利西街', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('41', '0', '0', '3', '1501641142', '11', 'customer', 'location', '', '金艺大厦', '', '0', '更改了', '', '详细定位', '', '更改为', '金艺大厦', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('47', '0', '0', '5', '1501641319', '27', 'customer', 'prov', '湖北省', '湖南省', '', '0', '更改了', '', '省份', '湖北省', '更改为', '湖南省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('48', '0', '0', '5', '1501641319', '27', 'customer', 'city', '武汉市', '湘潭市', '', '0', '更改了', '', '城市', '武汉市', '更改为', '湘潭市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('49', '0', '0', '5', '1501641319', '27', 'customer', 'dist', '江岸区', '岳塘区', '', '0', '更改了', '', '区县', '江岸区', '更改为', '岳塘区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('50', '0', '0', '5', '1501641319', '27', 'customer', 'lat', '0.000000', '', '', '0', '更改了', '', '坐标纬度', '0.000000', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('51', '0', '0', '5', '1501641319', '27', 'customer', 'lng', '0.000000', '', '', '0', '更改了', '', '坐标经度', '0.000000', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('54', '0', '0', '5', '1501641573', '27', 'customer', 'city', '湘潭市', '常德市', '', '0', '更改了', '', '城市', '湘潭市', '更改为', '常德市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('55', '0', '0', '5', '1501641573', '27', 'customer', 'dist', '岳塘区', '武陵区', '', '0', '更改了', '', '区县', '岳塘区', '更改为', '武陵区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('61', '0', '0', '3', '1501641783', '27', 'customer', 'resource_from', '', '1', '', '0', '更改了', '', '客户来源', '', '更改为', '1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('62', '0', '0', '3', '1501641783', '27', 'customer', 'field1', '', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('63', '0', '0', '3', '1501641783', '27', 'customer', 'field2', '', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('64', '0', '0', '3', '1501641783', '27', 'customer', 'field', '', '1', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('65', '0', '0', '3', '1501641783', '27', 'customer', 'prov', '湖南省', '山东省', '', '0', '更改了', '', '省份', '湖南省', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('66', '0', '0', '3', '1501641783', '27', 'customer', 'city', '常德市', '潍坊市', '', '0', '更改了', '', '城市', '常德市', '更改为', '潍坊市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('67', '0', '0', '3', '1501641783', '27', 'customer', 'dist', '武陵区', '潍城区', '', '0', '更改了', '', '区县', '武陵区', '更改为', '潍城区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('76', '0', '0', '5', '1501641881', '27', 'customer', 'resource_from', '1', '', '', '0', '更改了', '', '客户来源', '1', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('77', '0', '0', '5', '1501641881', '27', 'customer', 'field1', '1', '', 'getBusinessName', '0', '更改了', '', '客户行业[1]', 'IT行业', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('78', '0', '0', '5', '1501641881', '27', 'customer', 'field2', '1', '', 'getBusinessName', '0', '更改了', '', '客户行业[2]', 'IT行业', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('79', '0', '0', '5', '1501641881', '27', 'customer', 'field', '1', '', 'getBusinessName', '0', '更改了', '', '客户行业[3]', 'IT行业', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('80', '0', '0', '5', '1501641881', '27', 'customer', 'prov', '山东省', '', '', '0', '更改了', '', '省份', '山东省', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('81', '0', '0', '5', '1501641881', '27', 'customer', 'city', '潍坊市', '', '', '0', '更改了', '', '城市', '潍坊市', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('82', '0', '0', '5', '1501641881', '27', 'customer', 'dist', '潍城区', '', '', '0', '更改了', '', '区县', '潍城区', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('91', '0', '0', '3', '1501641885', '27', 'customer', 'resource_from', '', '1', '', '0', '更改了', '', '客户来源', '', '更改为', '1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('92', '0', '0', '3', '1501641885', '27', 'customer', 'field1', '', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('93', '0', '0', '3', '1501641885', '27', 'customer', 'field2', '', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('94', '0', '0', '3', '1501641885', '27', 'customer', 'field', '', '1', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('95', '0', '0', '3', '1501641885', '27', 'customer', 'prov', '', '山东省', '', '0', '更改了', '', '省份', '', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('96', '0', '0', '3', '1501641885', '27', 'customer', 'city', '', '潍坊市', '', '0', '更改了', '', '城市', '', '更改为', '潍坊市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('97', '0', '0', '3', '1501641885', '27', 'customer', 'dist', '', '潍城区', '', '0', '更改了', '', '区县', '', '更改为', '潍城区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('98', '0', '0', '5', '1501642115', '27', 'customer', 'customer_name', '山东测试测试有限公司', '山东测试有限公司', '', '0', '更改了', '', '客户名称', '山东测试测试有限公司', '更改为', '山东测试有限公司', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('99', '0', '0', '5', '1501642115', '27', 'customer', 'field1', '1', '', 'getBusinessName', '0', '更改了', '', '客户行业[1]', 'IT行业', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('100', '0', '0', '5', '1501642115', '27', 'customer', 'field2', '1', '', 'getBusinessName', '0', '更改了', '', '客户行业[2]', 'IT行业', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('101', '0', '0', '5', '1501642115', '27', 'customer', 'field', '1', '', 'getBusinessName', '0', '更改了', '', '客户行业[3]', 'IT行业', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('102', '0', '0', '5', '1501642115', '27', 'customer', 'grade', 'A', 'B', '', '0', '更改了', '', '客户级别', 'A', '更改为', 'B', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('103', '0', '0', '5', '1501642115', '27', 'customer', 'prov', '山东省', '湖南省', '', '0', '更改了', '', '省份', '山东省', '更改为', '湖南省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('104', '0', '0', '5', '1501642115', '27', 'customer', 'city', '潍坊市', '湘潭市', '', '0', '更改了', '', '城市', '潍坊市', '更改为', '湘潭市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('105', '0', '0', '5', '1501642115', '27', 'customer', 'dist', '潍城区', '湘乡', '', '0', '更改了', '', '区县', '潍城区', '更改为', '湘乡', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('113', '0', '0', '5', '1501644512', '27', 'customer', 'prov', '湖南省', '', '', '0', '更改了', '', '省份', '湖南省', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('114', '0', '0', '5', '1501644512', '27', 'customer', 'city', '湘潭市', '', '', '0', '更改了', '', '城市', '湘潭市', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('115', '0', '0', '5', '1501644512', '27', 'customer', 'dist', '湘乡', '', '', '0', '更改了', '', '区县', '湘乡', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('116', '0', '0', '5', '1501644582', '27', 'customer', 'grade', 'B', 'A', '', '0', '更改了', '', '客户级别', 'B', '更改为', 'A', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('117', '0', '0', '5', '1501644582', '27', 'customer', 'prov', '', '湖北省', '', '0', '更改了', '', '省份', '', '更改为', '湖北省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('118', '0', '0', '5', '1501644582', '27', 'customer', 'city', '', '武汉市', '', '0', '更改了', '', '城市', '', '更改为', '武汉市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('119', '0', '0', '5', '1501644582', '27', 'customer', 'dist', '', '汉南区', '', '0', '更改了', '', '区县', '', '更改为', '汉南区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('123', '0', '0', '5', '1501644606', '27', 'customer', 'prov', '湖北省', '', '', '0', '更改了', '', '省份', '湖北省', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('124', '0', '0', '5', '1501644606', '27', 'customer', 'city', '武汉市', '', '', '0', '更改了', '', '城市', '武汉市', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('125', '0', '0', '5', '1501644606', '27', 'customer', 'dist', '汉南区', '', '', '0', '更改了', '', '区县', '汉南区', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('126', '0', '0', '5', '1501644640', '27', 'customer', 'grade', 'A', 'C', '', '0', '更改了', '', '客户级别', 'A', '更改为', 'C', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('127', '0', '0', '5', '1501644640', '27', 'customer', 'prov', '', '云南省', '', '0', '更改了', '', '省份', '', '更改为', '云南省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('128', '0', '0', '5', '1501644640', '27', 'customer', 'city', '', '临沧市', '', '0', '更改了', '', '城市', '', '更改为', '临沧市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('129', '0', '0', '5', '1501644640', '27', 'customer', 'dist', '', '云县', '', '0', '更改了', '', '区县', '', '更改为', '云县', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('133', '0', '0', '5', '1501644662', '27', 'customer', 'prov', '云南省', '', '', '0', '更改了', '', '省份', '云南省', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('134', '0', '0', '5', '1501644662', '27', 'customer', 'city', '临沧市', '', '', '0', '更改了', '', '城市', '临沧市', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('135', '0', '0', '5', '1501644662', '27', 'customer', 'dist', '云县', '', '', '0', '更改了', '', '区县', '云县', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('136', '0', '0', '5', '1501654600', '17', 'customer', 'field1', '', '0', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('137', '0', '0', '5', '1501654600', '17', 'customer', 'field2', '', '0', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('138', '0', '0', '5', '1501654600', '17', 'customer', 'prov', '', '湖南省', '', '0', '更改了', '', '省份', '', '更改为', '湖南省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('139', '0', '0', '5', '1501654600', '17', 'customer', 'city', '', '湘潭市', '', '0', '更改了', '', '城市', '', '更改为', '湘潭市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('140', '0', '0', '5', '1501654600', '17', 'customer', 'dist', '', '雨湖区', '', '0', '更改了', '', '区县', '', '更改为', '雨湖区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('143', '0', '0', '5', '1501726478', '21', 'customer', 'prov', '', '湖南省', '', '0', '更改了', '', '省份', '', '更改为', '湖南省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('144', '0', '0', '5', '1501726478', '21', 'customer', 'city', '', '湘潭市', '', '0', '更改了', '', '城市', '', '更改为', '湘潭市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('145', '0', '0', '5', '1501726478', '21', 'customer', 'dist', '', '雨湖区', '', '0', '更改了', '', '区县', '', '更改为', '雨湖区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('146', '0', '0', '5', '1501750663', '27', 'customer', 'prov', '', '山东省', '', '0', '更改了', '', '省份', '', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('147', '0', '0', '5', '1501750663', '27', 'customer', 'city', '', '青岛市', '', '0', '更改了', '', '城市', '', '更改为', '青岛市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('148', '0', '0', '5', '1501750663', '27', 'customer', 'dist', '', '崂山区', '', '0', '更改了', '', '区县', '', '更改为', '崂山区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('155', '0', '0', '3', '1502176458', '11', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '变更', '', '阶段', '有意向', '改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('156', '0', '0', '3', '1502176940', '11', 'sale_chance', 'sale_name', '销售机会追踪', '销售机会追踪1', '', '0', '变更', '', '销售机会名称', '销售机会追踪', '改为', '销售机会追踪1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('157', '0', '0', '3', '1502176940', '11', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '变更', '', '阶段', '有意向', '改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('158', '0', '0', '3', '1502176940', '11', 'sale_chance', 'guess_money', '123.00', '123.01', '', '0', '变更', '', '预期金额', '123.00', '改为', '123.01', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('159', '0', '0', '3', '1502176940', '11', 'sale_chance', 'prepay_time', '1502121600', '1502208000', '', '0', '变更', '', '预计成单日期', '1502121600', '改为', '1502208000', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('168', '0', '0', '3', '1502177548', '11', 'customer', 'comm_status', '1', '6', 'getCommStatusName', '0', '更改了', '', '沟通状态', '无意向', '更改为', '有意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('169', '0', '0', '3', '1502178500', '11', 'customer', 'grade', 'A', 'B', '', '0', '更改了', '', '客户级别', 'A', '更改为', 'B', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('170', '0', '0', '3', '1502178500', '11', 'customer', 'comm_status', '6', '5', 'getCommStatusName', '0', '更改了', '', '沟通状态', '有意向', '更改为', '待定', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('172', '0', '0', '3', '1502178508', '11', 'customer', 'grade', 'B', 'A', '', '0', '更改了', '', '客户级别', 'B', '更改为', 'A', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('173', '0', '0', '3', '1502178508', '11', 'customer', 'comm_status', '5', '6', 'getCommStatusName', '0', '更改了', '', '沟通状态', '待定', '更改为', '有意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('175', '0', '0', '3', '1502178693', '11', 'sale_chance', 'sale_name', '销售机会追踪1', '销售机会追踪测试', '', '0', '更改了', '', '销售机会名称', '销售机会追踪1', '更改为', '销售机会追踪测试', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('176', '0', '0', '3', '1502178693', '11', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('177', '0', '0', '3', '1502178693', '11', 'sale_chance', 'guess_money', '123.01', '123.00', '', '0', '更改了', '', '预期金额', '123.01', '更改为', '123.00', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('178', '0', '0', '3', '1502178693', '11', 'sale_chance', 'prepay_time', '1502208000', '1502121600', '', '0', '更改了', '', '预计成单日期', '1502208000', '更改为', '1502121600', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('182', '0', '0', '8', '1502245064', '1', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('183', '0', '0', '3', '1502247603', '11', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('184', '0', '0', '3', '1502323752', '11', 'sale_chance', 'id', '0', '24', '', '0', '添加了', '', '新商机', '', '', '测试', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('185', '0', '0', '8', '1502323820', '66', 'sale_chance', 'id', '0', '25', '', '0', '添加了', '', '新商机', '', '', '销售机会Ａ', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('186', '0', '0', '8', '1502324114', '66', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '销售机会Ａ', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('187', '0', '0', '8', '1502324297', '66', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '销售机会Ａ', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('188', '0', '0', '8', '1502325665', '68', 'sale_chance', 'id', '0', '26', '', '0', '添加了', '', '新商机', '', '', '巨星', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('189', '0', '0', '8', '1502325743', '68', 'sale_chance', 'sale_status', '1', '4', 'getSaleStatusName', '0', '更改了', '巨星', '阶段', '有意向', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('190', '0', '0', '3', '1502327973', '11', 'customer', 'location', '金艺大厦', '金艺大厦7f', '', '0', '更改了', '', '详细定位', '金艺大厦', '更改为', '金艺大厦7f', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('191', '0', '0', '3', '1502328026', '11', 'customer_contact', 'id', '0', '16', '', '0', '添加了', '', '新联系人', '', '', '测试测试', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('192', '0', '0', '3', '1502328123', '11', 'customer_contact', 'phone_first', '18769714760', '18769714761', '', '0', '更改了', '测试测试', '首要电话', '18769714760', '更改为', '18769714761', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('193', '0', '0', '3', '1502328123', '11', 'customer_contact', 'phone_second', '0536-88888888', '0536-88888889', '', '0', '更改了', '测试测试', '备用电话', '0536-88888888', '更改为', '0536-88888889', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('194', '0', '0', '3', '1502328123', '11', 'customer_contact', 'qqnum', '', '123456789', '', '0', '更改了', '测试测试', 'QQ', '', '更改为', '123456789', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('195', '0', '0', '3', '1502328764', '11', 'sale_chance', 'id', '0', '27', '', '0', '添加了', '', '新商机', '', '', '建站销售机会演示', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('196', '0', '0', '8', '1502434790', '1', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '嗷嗷啊', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('197', '0', '0', '8', '1502434919', '1', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '嗷嗷啊', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('198', '0', '0', '8', '1502673317', '68', 'sale_chance', 'id', '0', '28', '', '0', '添加了', '', '新商机', '', '', '如来神掌', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('199', '0', '0', '8', '1502673371', '68', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '如来神掌', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('200', '0', '0', '8', '1502673468', '68', 'sale_chance', 'visit_time', '0', '', 'time_format', '0', '更改了', '如来神掌', '拜访时间', '', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('201', '0', '0', '8', '1502766722', '66', 'sale_chance', 'id', '0', '29', '', '0', '添加了', '', '新商机', '', '', '如来神掌', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('202', '0', '0', '8', '1502766728', '66', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '如来神掌', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('203', '0', '0', '8', '1503045757', '68', 'sale_chance', 'id', '0', '30', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('204', '0', '0', '9', '1503907706', '35', 'customer_contact', 'id', '0', '17', '', '0', '添加了', '', '新联系人', '', '', '发的', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('205', '0', '0', '9', '1503907733', '35', 'customer_contact', 'id', '0', '18', '', '0', '添加了', '', '新联系人', '', '', '二', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('206', '0', '0', '6', '1504229355', '70', 'customer_contact', 'id', '0', '19', '', '0', '添加了', '', '新联系人', '', '', '拖', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('207', '0', '0', '5', '1504258142', '21', 'customer', 'address', '', '你的', '', '0', '更改了', '', '详细地址', '', '更改为', '你的', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('208', '0', '0', '8', '1504493394', '78', 'customer_contact', 'id', '0', '20', '', '0', '添加了', '', '新联系人', '', '', '韩信', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('209', '0', '0', '2', '1504494190', '3', 'customer', 'telephone', '13321215656', '13321215658', '', '0', '更改了', '', '联系电话', '13321215656', '更改为', '13321215658', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('210', '0', '0', '2', '1504494190', '3', 'customer', 'field1', '', '0', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('211', '0', '0', '2', '1504494190', '3', 'customer', 'field2', '', '0', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('212', '0', '0', '2', '1504494190', '3', 'customer', 'field', '', '0', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('216', '0', '0', '2', '1504494900', '76', 'customer', 'resource_from', '0', '2', '', '0', '更改了', '', '客户来源', '0', '更改为', '2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('217', '0', '0', '2', '1504494924', '76', 'customer', 'resource_from', '2', '1', '', '0', '更改了', '', '客户来源', '2', '更改为', '1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('218', '0', '0', '2', '1504495065', '76', 'customer', 'resource_from', '1', '2', '', '0', '更改了', '', '客户来源', '1', '更改为', '2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('219', '0', '0', '2', '1504495766', '76', 'customer_contact', 'id', '0', '21', '', '0', '添加了', '', '新联系人', '', '', '你是', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('220', '0', '0', '2', '1504495904', '76', 'customer_contact', 'id', '0', '22', '', '0', '添加了', '', '新联系人', '', '', '张三', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('221', '0', '0', '8', '1504507010', '78', 'sale_chance', 'id', '0', '31', '', '0', '添加了', '', '新商机', '', '', '铜川巨量', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('222', '0', '0', '2', '1504507033', '76', 'customer', 'grade', '', 'A', '', '0', '更改了', '', '客户级别', '', '更改为', 'A', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('223', '0', '0', '2', '1504507353', '76', 'customer', 'resource_from', '2', '1', '', '0', '更改了', '', '客户来源', '2', '更改为', '1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('224', '0', '0', '12', '1504507781', '79', 'customer', 'telephone', '15169696124', '0536-6552700', '', '0', '更改了', '', '联系电话', '15169696124', '更改为', '0536-6552700', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('225', '0', '0', '12', '1504507781', '79', 'customer', 'field1', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('226', '0', '0', '12', '1504507781', '79', 'customer', 'field2', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('227', '0', '0', '12', '1504507781', '79', 'customer', 'field', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('228', '0', '0', '12', '1504507781', '79', 'customer', 'prov', '0', '山东省', '', '0', '更改了', '', '省份', '0', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('229', '0', '0', '12', '1504507781', '79', 'customer', 'city', '0', '潍坊市', '', '0', '更改了', '', '城市', '0', '更改为', '潍坊市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('230', '0', '0', '12', '1504507781', '79', 'customer', 'dist', '0', '潍城区', '', '0', '更改了', '', '区县', '0', '更改为', '潍城区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('231', '0', '0', '12', '1504507784', '79', 'customer', 'telephone', '15169696124', '0536-6552700', '', '0', '更改了', '', '联系电话', '15169696124', '更改为', '0536-6552700', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('232', '0', '0', '12', '1504507784', '79', 'customer', 'field1', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('233', '0', '0', '12', '1504507784', '79', 'customer', 'field2', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('234', '0', '0', '12', '1504507784', '79', 'customer', 'field', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('235', '0', '0', '12', '1504507784', '79', 'customer', 'prov', '0', '山东省', '', '0', '更改了', '', '省份', '0', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('236', '0', '0', '12', '1504507784', '79', 'customer', 'city', '0', '潍坊市', '', '0', '更改了', '', '城市', '0', '更改为', '潍坊市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('237', '0', '0', '12', '1504507784', '79', 'customer', 'dist', '0', '潍城区', '', '0', '更改了', '', '区县', '0', '更改为', '潍城区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('238', '0', '0', '12', '1504507870', '79', 'customer', 'telephone', '0536-6552700', '18678018888', '', '0', '更改了', '', '联系电话', '0536-6552700', '更改为', '18678018888', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('239', '0', '0', '2', '1504507881', '76', 'customer', 'resource_from', '1', '2', '', '0', '更改了', '', '客户来源', '1', '更改为', '2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('240', '0', '0', '2', '1504507881', '76', 'customer', 'grade', 'A', 'C', '', '0', '更改了', '', '客户级别', 'A', '更改为', 'C', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('242', '0', '0', '2', '1504509133', '76', 'customer_contact', 'id', '0', '23', '', '0', '添加了', '', '新联系人', '', '', '我的', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('243', '0', '0', '2', '1504509146', '76', 'customer_contact', 'id', '0', '24', '', '0', '添加了', '', '新联系人', '', '', '我的心是', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('244', '0', '0', '2', '1504509788', '76', 'customer_contact', 'id', '0', '25', '', '0', '添加了', '', '新联系人', '', '', '王明', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('245', '0', '0', '12', '1504572189', '81', 'customer_contact', 'id', '0', '26', '', '0', '添加了', '', '新联系人', '', '', '王总1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('246', '0', '0', '12', '1504572198', '81', 'sale_chance', 'id', '0', '32', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('247', '0', '0', '12', '1504573571', '82', 'customer_contact', 'id', '0', '27', '', '0', '添加了', '', '新联系人', '', '', '联系人', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('248', '0', '0', '12', '1504573578', '82', 'sale_chance', 'id', '0', '33', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('249', '0', '0', '12', '1504574675', '83', 'customer_contact', 'id', '0', '28', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('250', '0', '0', '12', '1504574687', '83', 'sale_chance', 'id', '0', '34', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('251', '0', '0', '12', '1504576227', '84', 'customer_contact', 'id', '0', '29', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('252', '0', '0', '12', '1504576268', '84', 'sale_chance', 'id', '0', '35', '', '0', '添加了', '', '新商机', '', '', 'xiaoshou', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('253', '0', '0', '12', '1504577026', '84', 'customer', 'field1', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('254', '0', '0', '12', '1504577026', '84', 'customer', 'field2', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('255', '0', '0', '12', '1504577026', '84', 'customer', 'field', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('256', '0', '0', '12', '1504577026', '84', 'customer', 'prov', '0', '山东省', '', '0', '更改了', '', '省份', '0', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('257', '0', '0', '12', '1504577026', '84', 'customer', 'city', '0', '潍坊市', '', '0', '更改了', '', '城市', '0', '更改为', '潍坊市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('258', '0', '0', '12', '1504577026', '84', 'customer', 'dist', '0', '潍城区', '', '0', '更改了', '', '区县', '0', '更改为', '潍城区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('260', '0', '0', '4', '1504582187', '65', 'sale_chance', 'id', '0', '36', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('261', '0', '0', '8', '1504593767', '85', 'sale_chance', 'id', '0', '37', '', '0', '添加了', '', '新商机', '', '', '销售机会1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('262', '0', '0', '8', '1504593832', '85', 'sale_chance', 'id', '0', '38', '', '0', '添加了', '', '新商机', '', '', '销售机会2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('263', '0', '0', '8', '1504600913', '86', 'customer_contact', 'id', '0', '30', '', '0', '添加了', '', '新联系人', '', '', '马云云', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('264', '0', '0', '8', '1504601022', '86', 'sale_chance', 'id', '0', '39', '', '0', '添加了', '', '新商机', '', '', '机会销售', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('265', '0', '0', '8', '1504601181', '86', 'sale_chance', 'id', '0', '40', '', '0', '添加了', '', '新商机', '', '', '机会2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('266', '0', '0', '8', '1504601220', '87', 'sale_chance', 'id', '0', '41', '', '0', '添加了', '', '新商机', '', '', '啊', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('267', '0', '0', '3', '1504601238', '88', 'customer_contact', 'id', '0', '31', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('268', '0', '0', '3', '1504601251', '88', 'sale_chance', 'id', '0', '42', '', '0', '添加了', '', '新商机', '', '', '萨达萨斯的', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('269', '0', '0', '8', '1504601595', '89', 'customer_contact', 'id', '0', '32', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('270', '0', '0', '5', '1504658310', '21', 'customer', 'address', '你的', '金艺大厦', '', '0', '更改了', '', '详细地址', '你的', '更改为', '金艺大厦', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('271', '0', '0', '8', '1504659900', '90', 'customer_contact', 'id', '0', '33', '', '0', '添加了', '', '新联系人', '', '', '猛男', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('272', '0', '0', '8', '1504659938', '90', 'sale_chance', 'id', '0', '43', '', '0', '添加了', '', '新商机', '', '', '唧唧复唧唧', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('273', '0', '0', '8', '1504660482', '85', 'customer_contact', 'id', '0', '34', '', '0', '添加了', '', '新联系人', '', '', '啊', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('274', '0', '0', '4', '1504663288', '65', 'sale_chance', 'id', '0', '44', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('275', '0', '0', '5', '1504665415', '91', 'customer', 'resource_from', '0', '1', '', '0', '更改了', '', '客户来源', '0', '更改为', '1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('276', '0', '0', '5', '1504665440', '74', 'customer', 'resource_from', '0', '1', '', '0', '更改了', '', '客户来源', '0', '更改为', '1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('277', '0', '0', '5', '1504665440', '74', 'customer', 'grade', '', 'A', '', '0', '更改了', '', '客户级别', '', '更改为', 'A', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('278', '0', '0', '4', '1504669758', '65', 'customer_contact', 'id', '0', '35', '', '0', '添加了', '', '新联系人', '', '', 'the', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('279', '0', '0', '5', '1504678406', '17', 'customer', 'resource_from', '0', '1', '', '0', '更改了', '', '客户来源', '0', '更改为', '1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('280', '0', '0', '4', '1504680267', '65', 'sale_chance', 'id', '0', '46', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('281', '0', '0', '4', '1504680721', '65', 'sale_chance', 'id', '0', '47', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('282', '0', '0', '4', '1504680763', '65', 'sale_chance', 'id', '0', '48', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('283', '0', '0', '8', '1504686577', '92', 'customer_contact', 'id', '0', '36', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('284', '0', '0', '8', '1504687133', '93', 'customer_contact', 'id', '0', '37', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('285', '0', '0', '8', '1504687513', '93', 'sale_chance', 'id', '0', '50', '', '0', '添加了', '', '新商机', '', '', '1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('286', '0', '0', '8', '1504687533', '93', 'sale_chance', 'id', '0', '51', '', '0', '添加了', '', '新商机', '', '', '2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('287', '0', '0', '8', '1504687559', '93', 'sale_chance', 'id', '0', '52', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('288', '0', '0', '8', '1504687591', '94', 'customer_contact', 'id', '0', '38', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('289', '0', '0', '8', '1504687714', '94', 'sale_chance', 'id', '0', '53', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('290', '0', '0', '8', '1504687729', '95', 'customer_contact', 'id', '0', '39', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('291', '0', '0', '8', '1504687769', '95', 'sale_chance', 'id', '0', '54', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('292', '0', '0', '8', '1504687853', '95', 'sale_chance', 'id', '0', '55', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('293', '0', '0', '8', '1504687861', '95', 'sale_chance', 'id', '0', '56', '', '0', '添加了', '', '新商机', '', '', 'aaa', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('294', '0', '0', '8', '1504694097', '97', 'customer_contact', 'id', '0', '40', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('295', '0', '0', '8', '1504694290', '98', 'customer_contact', 'id', '0', '41', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('296', '0', '0', '8', '1504694310', '98', 'sale_chance', 'id', '0', '57', '', '0', '添加了', '', '新商机', '', '', '333', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('297', '0', '0', '12', '1504750390', '99', 'customer_contact', 'id', '0', '42', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('298', '0', '0', '12', '1504750440', '99', 'sale_chance', 'id', '0', '58', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('299', '0', '0', '12', '1504750468', '99', 'customer', 'field1', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('300', '0', '0', '12', '1504750468', '99', 'customer', 'field2', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('301', '0', '0', '12', '1504750468', '99', 'customer', 'prov', '1', '山东省', '', '0', '更改了', '', '省份', '1', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('302', '0', '0', '12', '1504750468', '99', 'customer', 'city', '1', '潍坊市', '', '0', '更改了', '', '城市', '1', '更改为', '潍坊市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('303', '0', '0', '12', '1504750468', '99', 'customer', 'dist', '1', '潍城区', '', '0', '更改了', '', '区县', '1', '更改为', '潍城区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('306', '0', '0', '12', '1504750491', '99', 'customer', 'grade', 'B', 'C', '', '0', '更改了', '', '客户级别', 'B', '更改为', 'C', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('307', '0', '0', '3', '1504767602', '26', 'customer_contact', 'key_decide', '', '0', 'getYesNoName', '0', '更改了', '发达', '是否是关键决策人', '否', '更改为', '否', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('308', '0', '0', '3', '1504767602', '26', 'customer_contact', 'introducer', '0', '3421', '', '0', '更改了', '发达', '客户介绍人', '0', '更改为', '3421', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('310', '0', '0', '5', '1504768102', '77', 'sale_chance', 'id', '0', '59', '', '0', '添加了', '', '新商机', '', '', 'hhhhhh', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('311', '0', '0', '5', '1504768111', '77', 'sale_chance', 'id', '0', '60', '', '0', '添加了', '', '新商机', '', '', 'hhhhhh', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('312', '0', '0', '5', '1504768271', '74', 'sale_chance', 'id', '0', '61', '', '0', '添加了', '', '新商机', '', '', 'hhhhgfdd', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('313', '0', '0', '5', '1504768324', '72', 'sale_chance', 'id', '0', '62', '', '0', '添加了', '', '新商机', '', '', '测试商机', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('314', '0', '0', '5', '1504768876', '16', 'sale_chance', 'id', '0', '63', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('315', '0', '0', '5', '1504769346', '77', 'sale_chance', 'id', '0', '67', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('316', '0', '0', '3', '1504769511', '26', 'sale_chance', 'id', '0', '68', '', '0', '添加了', '', '新商机', '', '', '是否大', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('317', '0', '0', '3', '1504769540', '26', 'sale_chance', 'id', '0', '69', '', '0', '添加了', '', '新商机', '', '', 'dfsafd', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('318', '0', '0', '5', '1504769686', '24', 'sale_chance', 'id', '0', '70', '', '0', '添加了', '', '新商机', '', '', '俄罗斯', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('319', '0', '0', '3', '1504769692', '26', 'sale_chance', 'id', '0', '71', '', '0', '添加了', '', '新商机', '', '', 'dfdsfdsf', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('320', '0', '0', '5', '1504769744', '91', 'sale_chance', 'id', '0', '72', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('321', '0', '0', '3', '1504770034', '26', 'sale_chance', 'id', '0', '74', '', '0', '添加了', '', '新商机', '', '', 'zzzzzz', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('322', '0', '0', '5', '1504770077', '72', 'sale_chance', 'id', '0', '75', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('323', '0', '0', '3', '1504772913', '26', 'customer_contact', 'introducer', '3421', '哈哈哈', '', '0', '更改了', '发达', '客户介绍人', '3421', '更改为', '哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('324', '0', '0', '3', '1504773240', '26', 'customer_contact', 'birthday', '', '1504713600', 'day_format', '0', '更改了', '发达', '生日', '', '更改为', '2017-09-07', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('325', '0', '0', '8', '1504776911', '102', 'customer_contact', 'id', '0', '43', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('326', '0', '0', '3', '1504778045', '26', 'customer', 'resource_from', '1', '0', '', '0', '更改了', '', '客户来源', '1', '更改为', '0', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('327', '0', '0', '3', '1504778045', '26', 'customer', 'prov', '1', '山东省', '', '0', '更改了', '', '省份', '1', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('328', '0', '0', '3', '1504778045', '26', 'customer', 'city', '1', '潍坊市', '', '0', '更改了', '', '城市', '1', '更改为', '潍坊市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('329', '0', '0', '3', '1504778045', '26', 'customer', 'dist', '1', '潍城区', '', '0', '更改了', '', '区县', '1', '更改为', '潍城区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('330', '0', '0', '3', '1504778045', '26', 'customer', 'comm_status', '0', '1', 'getCommStatusName', '0', '更改了', '', '沟通状态', '无', '更改为', '无意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('333', '0', '0', '3', '1504778170', '26', 'customer', 'comm_status', '0', '1', 'getCommStatusName', '0', '更改了', '', '沟通状态', '无', '更改为', '无意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('334', '0', '0', '3', '1504778357', '26', 'customer', 'comm_status', '0', '1', 'getCommStatusName', '0', '更改了', '', '沟通状态', '无', '更改为', '无意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('335', '0', '0', '4', '1504832209', '65', 'sale_chance', 'id', '0', '82', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('336', '0', '0', '5', '1504832852', '77', 'sale_chance', 'id', '0', '83', '', '0', '添加了', '', '新商机', '', '', 'dg ', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('337', '0', '0', '3', '1504834046', '105', 'customer_contact', 'id', '0', '44', '', '0', '添加了', '', '新联系人', '', '', '联系联系', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('338', '0', '0', '3', '1504834170', '105', 'sale_chance', 'id', '0', '84', '', '0', '添加了', '', '新商机', '', '', '哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('339', '0', '0', '5', '1504834960', '108', 'sale_chance', 'id', '0', '85', '', '0', '添加了', '', '新商机', '', '', '我的', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('340', '0', '0', '5', '1504835455', '17', 'customer_contact', 'id', '0', '45', '', '0', '添加了', '', '新联系人', '', '', '我的', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('341', '0', '0', '5', '1504835939', '109', 'customer_contact', 'id', '0', '46', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('342', '0', '0', '5', '1504836028', '109', 'sale_chance', 'id', '0', '86', '', '0', '添加了', '', '新商机', '', '', '恩恩', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('343', '0', '0', '5', '1504836859', '110', 'customer_contact', 'id', '0', '47', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('344', '0', '0', '3', '1504837973', '111', 'customer_contact', 'id', '0', '48', '', '0', '添加了', '', '新联系人', '', '', '操心细节', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('345', '0', '0', '3', '1504838020', '111', 'sale_chance', 'id', '0', '87', '', '0', '添加了', '', '新商机', '', '', '销售计划', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('346', '0', '0', '3', '1504838638', '111', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '销售计划', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('347', '0', '0', '3', '1504838756', '111', 'sale_chance', 'id', '0', '88', '', '0', '添加了', '', '新商机', '', '', '我的', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('348', '0', '0', '3', '1504840471', '111', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '销售计划', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('349', '0', '0', '3', '1504841038', '111', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '我的', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('350', '0', '0', '5', '1504851661', '108', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '我的', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('351', '0', '0', '9', '1504852536', '64', 'sale_chance', 'id', '0', '89', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('352', '0', '0', '9', '1504853184', '64', 'sale_chance', 'id', '0', '90', '', '0', '添加了', '', '新商机', '', '', '销售1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('353', '0', '0', '9', '1504853252', '64', 'sale_chance', 'id', '0', '91', '', '0', '添加了', '', '新商机', '', '', '销售2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('354', '0', '0', '9', '1504853751', '64', 'sale_chance', 'id', '0', '92', '', '0', '添加了', '', '新商机', '', '', '销售3', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('355', '0', '0', '9', '1504853862', '64', 'sale_chance', 'id', '0', '93', '', '0', '添加了', '', '新商机', '', '', '销售5', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('356', '0', '0', '9', '1504854057', '64', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '销售5', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('357', '0', '0', '4', '1504854335', '107', 'sale_chance', 'id', '0', '95', '', '0', '添加了', '', '新商机', '', '', '哈哈哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('358', '0', '0', '9', '1504854551', '64', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '销售5', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('359', '0', '0', '9', '1504854985', '64', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '销售3', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('360', '0', '0', '12', '1504855467', '112', 'customer_contact', 'id', '0', '49', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('361', '0', '0', '9', '1504855605', '64', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '销售3', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('362', '0', '0', '9', '1504855742', '64', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '销售2', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('363', '0', '0', '12', '1504855835', '113', 'customer_contact', 'id', '0', '50', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('364', '0', '0', '12', '1504855869', '113', 'sale_chance', 'id', '0', '96', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('365', '0', '0', '3', '1504857410', '111', 'sale_chance', 'id', '0', '97', '', '0', '添加了', '', '新商机', '', '', '阿斯顿', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('366', '0', '0', '3', '1504857417', '111', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '阿斯顿', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('367', '0', '0', '8', '1504864684', '93', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '1', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('368', '0', '0', '8', '1504923277', '119', 'customer_contact', 'id', '0', '51', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('369', '0', '0', '8', '1504923286', '119', 'sale_chance', 'id', '0', '98', '', '0', '添加了', '', '新商机', '', '', '如来神掌', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('370', '0', '0', '8', '1504923598', '120', 'customer_contact', 'id', '0', '52', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('371', '0', '0', '8', '1504923604', '120', 'sale_chance', 'id', '0', '99', '', '0', '添加了', '', '新商机', '', '', '如来神掌', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('372', '0', '0', '5', '1504924430', '27', 'customer', 'resource_from', '1', '2', '', '0', '更改了', '', '客户来源', '1', '更改为', '2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('373', '0', '0', '5', '1504924430', '27', 'customer', 'comm_status', '1', '0', 'getCommStatusName', '0', '更改了', '', '沟通状态', '无意向', '更改为', '无', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('375', '0', '0', '12', '1505093731', '123', 'customer_contact', 'id', '0', '53', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('376', '0', '0', '12', '1505094159', '124', 'customer_contact', 'id', '0', '54', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('377', '0', '0', '12', '1505094257', '125', 'customer_contact', 'id', '0', '55', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('378', '0', '0', '12', '1505094416', '126', 'customer_contact', 'id', '0', '56', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('379', '0', '0', '12', '1505094659', '126', 'sale_chance', 'id', '0', '100', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('380', '0', '0', '12', '1505094713', '127', 'customer_contact', 'id', '0', '57', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('381', '0', '0', '12', '1505094716', '127', 'sale_chance', 'id', '0', '101', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('382', '0', '0', '12', '1505094729', '127', 'sale_chance', 'prepay_time', '0', '', '', '0', '更改了', '', '预计成单日期', '0', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('383', '0', '0', '12', '1505094753', '127', 'sale_chance', 'prepay_time', '0', '', '', '0', '更改了', '', '预计成单日期', '0', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('384', '0', '0', '12', '1505096063', '130', 'customer_contact', 'id', '0', '58', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('385', '0', '0', '8', '1505096992', '132', 'customer_contact', 'id', '0', '59', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('386', '0', '0', '12', '1505097666', '138', 'customer_contact', 'id', '0', '60', '', '0', '添加了', '', '新联系人', '', '', '问问', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('387', '0', '0', '12', '1505098268', '140', 'sale_chance', 'id', '0', '102', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('388', '0', '0', '12', '1505098423', '142', 'customer_contact', 'id', '0', '61', '', '0', '添加了', '', '新联系人', '', '', 'Erin', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('389', '0', '0', '12', '1505098553', '144', 'customer_contact', 'id', '0', '62', '', '0', '添加了', '', '新联系人', '', '', 'Erin', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('390', '0', '0', '12', '1505098567', '144', 'sale_chance', 'id', '0', '103', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('391', '0', '0', '12', '1505098677', '145', 'customer', 'customer_name', '一步一步返回修改的客户', '一步一步返回修改的客户1', '', '0', '更改了', '', '客户名称', '一步一步返回修改的客户', '更改为', '一步一步返回修改的客户1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('392', '0', '0', '12', '1505098691', '145', 'customer_contact', 'id', '0', '63', '', '0', '添加了', '', '新联系人', '', '', 'Erin', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('393', '0', '0', '12', '1505098696', '145', 'customer_contact', 'contact_name', 'Erin', 'Erin1', '', '0', '更改了', 'Erin', '联系人姓名', 'Erin', '更改为', 'Erin1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('394', '0', '0', '12', '1505098710', '145', 'sale_chance', 'id', '0', '104', '', '0', '添加了', '', '新商机', '', '', '销售机会哈哈', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('395', '0', '0', '12', '1505098927', '146', 'customer', 'customer_name', '1', '12', '', '0', '更改了', '', '客户名称', '1', '更改为', '12', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('396', '0', '0', '12', '1505098984', '146', 'sale_chance', 'id', '0', '105', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('397', '0', '0', '12', '1505099024', '147', 'customer', 'customer_name', '2', '2333', '', '0', '更改了', '', '客户名称', '2', '更改为', '2333', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('398', '0', '0', '12', '1505099319', '148', 'customer_contact', 'id', '0', '64', '', '0', '添加了', '', '新联系人', '', '', 'Erin', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('399', '0', '0', '12', '1505100475', '149', 'customer_contact', 'id', '0', '65', '', '0', '添加了', '', '新联系人', '', '', 'Erin', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('400', '0', '0', '12', '1505101637', '150', 'sale_chance', 'id', '0', '106', '', '0', '添加了', '', '新商机', '', '', '销售机会', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('401', '0', '0', '8', '1505102061', '151', 'customer_contact', 'id', '0', '66', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('402', '0', '0', '8', '1505102069', '151', 'sale_chance', 'id', '0', '107', '', '0', '添加了', '', '新商机', '', '', '如来神掌', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('403', '0', '0', '12', '1505102246', '150', 'customer', 'field1', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('404', '0', '0', '12', '1505102246', '150', 'customer', 'field2', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('405', '0', '0', '12', '1505102246', '150', 'customer', 'field', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('406', '0', '0', '12', '1505112176', '150', 'sale_chance', 'id', '0', '108', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('407', '0', '0', '12', '1505112196', '150', 'sale_chance', 'sale_name', '', '销售机会2', '', '0', '更改了', '', '销售机会名称', '', '更改为', '销售机会2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('408', '0', '0', '12', '1505119838', '150', 'sale_chance', 'id', '0', '109', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('409', '0', '0', '12', '1505119863', '150', 'sale_chance', 'sale_name', '', '销售机会2', '', '0', '更改了', '', '销售机会名称', '', '更改为', '销售机会2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('410', '0', '0', '12', '1505120868', '150', 'sale_chance', 'id', '0', '110', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('411', '0', '0', '12', '1505120885', '150', 'sale_chance', 'sale_name', '', '销售机会4', '', '0', '更改了', '', '销售机会名称', '', '更改为', '销售机会4', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('412', '0', '0', '12', '1505120900', '150', 'sale_chance', 'sale_name', '销售机会2', '销售机会1', '', '0', '更改了', '销售机会2', '销售机会名称', '销售机会2', '更改为', '销售机会1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('413', '0', '0', '12', '1505177982', '150', 'sale_chance', 'id', '0', '111', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('414', '0', '0', '12', '1505178000', '150', 'sale_chance', 'id', '0', '112', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('415', '0', '0', '12', '1505179495', '150', 'customer', 'remark', '无意向', '个人的标签', '', '0', '更改了', '', '备注', '无意向', '更改为', '个人的标签', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('416', '0', '0', '12', '1505179646', '150', 'customer', 'prov', '省份', '山东省', '', '0', '更改了', '', '省份', '省份', '更改为', '山东省', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('417', '0', '0', '12', '1505179646', '150', 'customer', 'city', '地级市', '烟台市', '', '0', '更改了', '', '城市', '地级市', '更改为', '烟台市', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('418', '0', '0', '12', '1505179646', '150', 'customer', 'dist', '市、县级市', '莱山区', '', '0', '更改了', '', '区县', '市、县级市', '更改为', '莱山区', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('419', '0', '0', '12', '1505179646', '150', 'customer', 'address', '', '详细地址', '', '0', '更改了', '', '详细地址', '', '更改为', '详细地址', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('420', '0', '0', '12', '1505179646', '150', 'customer', 'location', '', '金艺大厦', '', '0', '更改了', '', '详细定位', '', '更改为', '金艺大厦', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('421', '0', '0', '12', '1505179646', '150', 'customer', 'comm_status', '1', '6', 'getCommStatusName', '0', '更改了', '', '沟通状态', '无意向', '更改为', '有意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('423', '0', '0', '12', '1505179697', '150', 'customer', 'remark', '个人的标签', '', '', '0', '更改了', '', '备注', '个人的标签', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('424', '0', '0', '12', '1505180327', '150', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '销售机会4', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('425', '0', '0', '12', '1505187186', '150', 'customer_contact', 'id', '0', '67', '', '0', '添加了', '', '新联系人', '', '', 'Erin', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('426', '0', '0', '12', '1505187248', '150', 'customer_contact', 'id', '0', '68', '', '0', '添加了', '', '新联系人', '', '', 'Erin1', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('427', '0', '0', '12', '1505197304', '150', 'customer_contact', 'id', '0', '69', '', '0', '添加了', '', '新联系人', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('428', '0', '0', '12', '1505199259', '150', 'sale_chance', 'id', '0', '113', '', '0', '添加了', '', '新商机', '', '', '销售机会666', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('429', '0', '0', '12', '1505199670', '150', 'customer_contact', 'remark', '', '无意向', '', '0', '更改了', 'Erin', '备注', '', '更改为', '无意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('430', '0', '0', '12', '1505199699', '150', 'customer', 'remark', '', '个人的标签', '', '0', '更改了', '', '备注', '', '更改为', '个人的标签', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('431', '0', '0', '12', '1505203309', '150', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '销售机会4', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('432', '0', '0', '12', '1505203309', '150', 'sale_chance', 'guess_money', '0.00', '1000.00', '', '0', '更改了', '销售机会4', '预期金额', '0.00', '更改为', '1000.00', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('434', '0', '0', '12', '1505351437', '150', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '销售机会2', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('435', '0', '0', '12', '1505351437', '150', 'sale_chance', 'guess_money', '0.00', '100.00', '', '0', '更改了', '销售机会2', '预期金额', '0.00', '更改为', '100.00', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('437', '0', '0', '12', '1505351695', '150', 'sale_chance', 'id', '0', '114', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('438', '0', '0', '12', '1505351723', '150', 'sale_chance', 'sale_name', '', '机会9', '', '0', '更改了', '', '销售机会名称', '', '更改为', '机会9', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('439', '0', '0', '12', '1505351723', '150', 'sale_chance', 'guess_money', '0.00', '200.00', '', '0', '更改了', '', '预期金额', '0.00', '更改为', '200.00', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('441', '0', '0', '12', '1505352509', '150', 'sale_chance', 'guess_money', '0.00', '99.00', '', '0', '更改了', '销售机会666', '预期金额', '0.00', '更改为', '99.00', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('442', '0', '0', '8', '1505440175', '152', 'customer', 'field1', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('443', '0', '0', '8', '1505440175', '152', 'customer', 'field2', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('444', '0', '0', '8', '1505440175', '152', 'customer', 'lat', '36.713209', '0.000000', '', '0', '更改了', '', '坐标纬度', '36.713209', '更改为', '0.000000', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('445', '0', '0', '8', '1505440175', '152', 'customer', 'lng', '119.113787', '0.000000', '', '0', '更改了', '', '坐标经度', '119.113787', '更改为', '0.000000', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('446', '0', '0', '8', '1505440175', '152', 'customer', 'remark', '有对对对意向', '有对对对意向无意向对对对3333对对对无意向有对对对意向', '', '0', '更改了', '', '备注', '有对对对意向', '更改为', '有对对对意向无意向对对对3333对对对无意向有对对对意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('449', '0', '0', '8', '1505440952', '153', 'customer', 'customer_name', '服务器', '服务器2', '', '0', '更改了', '', '客户名称', '服务器', '更改为', '服务器2', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('450', '0', '0', '12', '1505445052', '161', 'customer_contact', 'id', '0', '70', '', '0', '添加了', '', '新联系人', '', '', '联系人', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('451', '0', '0', '12', '1505445061', '161', 'customer', 'field1', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('452', '0', '0', '12', '1505445061', '161', 'customer', 'field2', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('453', '0', '0', '12', '1505445061', '161', 'customer', 'field', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('454', '0', '0', '12', '1505445068', '160', 'customer', 'field1', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[1]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('455', '0', '0', '12', '1505445068', '160', 'customer', 'field2', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[2]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('456', '0', '0', '12', '1505445068', '160', 'customer', 'field', '0', '1', 'getBusinessName', '0', '更改了', '', '客户行业[3]', '0', '更改为', 'IT行业', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('457', '0', '0', '8', '1505446245', '163', 'sale_chance', 'id', '0', '115', '', '0', '添加了', '', '新商机', '', '', '如来神掌', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('458', '0', '0', '8', '1505460054', '163', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '如来神掌', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('459', '0', '0', '8', '1505460054', '163', 'sale_chance', 'prepay_time', '0', '', '', '0', '更改了', '如来神掌', '预计成单日期', '0', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('461', '0', '0', '12', '1505461341', '161', 'customer', 'remark', '', '有对对对意向', '', '0', '更改了', '', '备注', '', '更改为', '有对对对意向', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('462', '0', '0', '8', '1505465516', '163', 'sale_chance', 'visit_place', '', '金艺大厦7层', '', '0', '更改了', '如来神掌', '拜访地点', '', '更改为', '金艺大厦7层', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('463', '0', '0', '8', '1505465516', '163', 'sale_chance', 'prepay_time', '0', '', '', '0', '更改了', '如来神掌', '预计成单日期', '0', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('464', '0', '0', '8', '1505466933', '163', 'sale_chance', 'visit_place', '金艺大厦7层', '', '', '0', '更改了', '如来神掌', '拜访地点', '金艺大厦7层', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('465', '0', '0', '8', '1505466933', '163', 'sale_chance', 'guess_money', '0.00', '1000.00', '', '0', '更改了', '如来神掌', '预期金额', '0.00', '更改为', '1000.00', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('466', '0', '0', '8', '1505466933', '163', 'sale_chance', 'prepay_time', '0', '', '', '0', '更改了', '如来神掌', '预计成单日期', '0', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('468', '0', '0', '8', '1506071252', '163', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '如来神掌', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('469', '0', '0', '8', '1506071252', '163', 'sale_chance', 'prepay_time', '0', '', '', '0', '更改了', '如来神掌', '预计成单日期', '0', '更改为', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('471', '0', '0', '8', '1506072962', '162', 'sale_chance', 'id', '0', '116', '', '0', '添加了', '', '新商机', '', '', '', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('472', '0', '0', '8', '1506072980', '162', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('473', '0', '0', '8', '1506305046', '163', 'sale_chance', 'id', '0', '117', '', '0', '添加了', '', '新商机', '', '', '李伯伯', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('474', '0', '0', '8', '1506305065', '163', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '李伯伯', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('475', '0', '0', '3', '1506305546', '11', 'sale_chance', 'sale_status', '1', '2', 'getSaleStatusName', '0', '更改了', '建站销售机会演示', '阶段', '有意向', '更改为', '预约拜访', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('476', '0', '0', '3', '1506308012', '11', 'sale_chance', 'visit_place', '', '金艺大厦', '', '0', '更改了', '建站销售机会演示', '拜访地点', '', '更改为', '金艺大厦', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('477', '0', '0', '3', '1506308139', '11', 'sale_chance', 'location', '', '36.7075,119.1324', '', '0', '更改了', '建站销售机会演示', '拜访位置坐标', '', '更改为', '36.7075,119.1324', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('478', '0', '0', '3', '1506308204', '11', 'sale_chance', 'location', '36.7075,119.1324', '36.713173,119.113675', '', '0', '更改了', '建站销售机会演示', '拜访位置坐标', '36.7075,119.1324', '更改为', '36.713173,119.113675', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('479', '0', '0', '3', '1506496801', '11', 'sale_chance', 'need_money', '123.00', '123', '', '0', '更改了', '销售机会跟踪测试', '应支付金额', '123.00', '更改为', '123', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('480', '0', '0', '3', '1506496801', '11', 'sale_chance', 'payed_money', '123.00', '123', '', '0', '更改了', '销售机会跟踪测试', '已支付金额', '123.00', '更改为', '123', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('481', '0', '0', '3', '1506496801', '11', 'sale_chance', 'final_money', '123.00', '123', '', '0', '更改了', '销售机会跟踪测试', '成单金额', '123.00', '更改为', '123', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('482', '0', '0', '3', '1506497009', '11', 'sale_chance', 'need_money', '123.00', '123', '', '0', '更改了', '销售机会跟踪测试', '应支付金额', '123.00', '更改为', '123', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('483', '0', '0', '3', '1506497009', '11', 'sale_chance', 'payed_money', '123.00', '123', '', '0', '更改了', '销售机会跟踪测试', '已支付金额', '123.00', '更改为', '123', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('484', '0', '0', '3', '1506497009', '11', 'sale_chance', 'final_money', '123.00', '123', '', '0', '更改了', '销售机会跟踪测试', '成单金额', '123.00', '更改为', '123', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('485', '0', '0', '3', '1506561682', '11', 'sale_chance', 'sale_status', '3', '4', 'getSaleStatusName', '0', '更改了', '建站销售机会演示', '阶段', '已拜访', '更改为', '成单申请', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('486', '0', '0', '3', '1506561682', '11', 'sale_chance', 'need_money', '0.00', '3210', '', '0', '更改了', '建站销售机会演示', '应支付金额', '0.00', '更改为', '3210', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('487', '0', '0', '3', '1506561682', '11', 'sale_chance', 'payed_money', '', '3210', '', '0', '更改了', '建站销售机会演示', '已支付金额', '', '更改为', '3210', '', '');
INSERT INTO `guguo_customer_trace` VALUES ('488', '0', '0', '3', '1506561682', '11', 'sale_chance', 'final_money', '', '3210', '', '0', '更改了', '建站销售机会演示', '成单金额', '', '更改为', '3210', '', '');

-- ----------------------------
-- Table structure for guguo_email_record
-- ----------------------------
DROP TABLE IF EXISTS `guguo_email_record`;
CREATE TABLE `guguo_email_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_userid` int(11) NOT NULL COMMENT '员工id',
  `to_userid` int(11) DEFAULT NULL COMMENT '收件方id',
  `sender_addr` varchar(128) NOT NULL COMMENT '发件方邮件地址',
  `dest_addr` varchar(128) NOT NULL COMMENT '收件方邮件地址，多项逗号分隔',
  `title` varchar(128) NOT NULL COMMENT '标题',
  `send_time` int(11) NOT NULL COMMENT '发送时间',
  `content` text COMMENT '内容',
  `attachment_name` varchar(128) DEFAULT NULL COMMENT '附件名称',
  `attachment_path` varchar(256) DEFAULT NULL COMMENT '附件路径',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0草稿箱，1发件箱，-1回收站，-2彻底删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_email_record
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_employee
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee`;
CREATE TABLE `guguo_employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corpid` varchar(64) DEFAULT NULL COMMENT '所属公司id',
  `telephone` varchar(16) NOT NULL COMMENT '用户电话号码唯一',
  `nickname` varchar(128) DEFAULT NULL COMMENT '环信账号昵称',
  `username` varchar(128) DEFAULT NULL COMMENT '环信账户用户名',
  `password` varchar(32) DEFAULT NULL,
  `userpic` varchar(256) DEFAULT NULL,
  `truename` varchar(128) DEFAULT NULL COMMENT '员工真实姓名',
  `stage_name` varchar(128) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL COMMENT '性别0女1男',
  `age` tinyint(3) unsigned DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `qqnum` varchar(13) DEFAULT NULL,
  `wechat` varchar(64) DEFAULT NULL,
  `worknum` varchar(64) DEFAULT NULL COMMENT '工号',
  `create_time` int(11) DEFAULT NULL,
  `lastlogintime` int(11) DEFAULT NULL,
  `haveim` tinyint(1) DEFAULT '0' COMMENT '是否已注册环信账号0未注册1已注册',
  `lastloginip` varchar(128) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '-1离职1正常',
  `system_token` varchar(32) DEFAULT NULL COMMENT 'app登陆token',
  `left_money` int(11) unsigned DEFAULT '0' COMMENT '剩余金额，单位分',
  `frozen_money` int(11) unsigned DEFAULT '0' COMMENT '冻结金额',
  `corp_left_money` int(11) unsigned DEFAULT '0' COMMENT '授权公司余额',
  `corp_frozen_money` int(11) unsigned DEFAULT '0' COMMENT '冻结公司额度',
  `pay_password` varchar(32) DEFAULT NULL COMMENT '支付密码',
  `alipay_account` varchar(128) DEFAULT NULL COMMENT '支付宝账号',
  `is_leader` tinyint(1) DEFAULT '0' COMMENT '0非领导1领导',
  `wired_phone` varchar(12) DEFAULT NULL COMMENT '座机号',
  `part_phone` varchar(8) DEFAULT NULL COMMENT '分机号',
  `on_duty` tinyint(1) DEFAULT '1' COMMENT '员工状态，1在职，2休假',
  PRIMARY KEY (`id`),
  UNIQUE KEY `telephone_index` (`telephone`) USING BTREE,
  UNIQUE KEY `stage_name_index` (`stage_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_employee
-- ----------------------------
INSERT INTO `guguo_employee` VALUES ('0', null, '0', null, null, '0', '/static/images/logo.png', '管理员', null, null, null, null, null, null, null, null, null, '0', null, '1', null, '0', '0', '0', '0', null, null, '0', null, null, '1');
INSERT INTO `guguo_employee` VALUES ('1', '1', '13322223333', '', '', '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/2017-03-25/149041358160251.jpeg', '李洪金', null, '1', '20', 'jack@qq.com', '12345678', 'noasdg', 'NO000001', '1484209455', '1506579874', '1', '192.168.102.70', '1', 'de180130ffe3f765450c2bfc2d7ec2ef', '54308', '0', '100000', '701759', 'd41d8cd98f00b204e9800998ecf8427e', 'bqirro0741@sandbox.com', '0', null, null, '1');
INSERT INTO `guguo_employee` VALUES ('2', '1', '13322221111', null, null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170901/150422636226670.jpeg', '吴振国', null, '1', '123', null, null, null, 'NO000002', '1484209455', '1506556412', '1', '127.0.0.1', '1', 'd886fc5ac090706b0e3e41e17689c79e', '864866', '0', '100000', '701756', '5e8667a439c68f5145dd2fcbecf02209', 'bqirro0741@sandbox.com', '0', null, null, '1');
INSERT INTO `guguo_employee` VALUES ('3', '1', '13311112222', null, null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170720/3.jpg', '曹鑫鑫', null, '1', null, null, null, null, 'NO000003', '1484209455', '1506557632', '1', '127.0.0.1', '1', '51d4ac4ca3d8d3eec921df97fde3c603', '2005814', '8524466', '84400', '706544', 'e10adc3949ba59abbe56e057f20f883e', '', '0', null, null, '1');
INSERT INTO `guguo_employee` VALUES ('4', '1', '13322225555', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170927/150649895819341.jpeg', '孙大鹏', null, '1', null, '', '', '', 'NO000004', '1484209455', '1506502715', '1', '192.168.102.70', '1', 'ce41c5bc8dfa7eedc0c966f7d92204d0', '9925', '294360', '95200', '705760', 'e10adc3949ba59abbe56e057f20f883e', '', '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('5', '1', '13322226667', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170928/150656271737168.jpeg', '张晓阳', null, '1', null, '', '', '', 'NO000005', '1484209455', '1506567440', '1', '192.168.102.70', '1', '8c0e3e864d36a30dbe525463dd18e02f', '290499', '0', '100000', null, '4607e782c4d86fd5364d7e4508bb10d9', '', '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('6', '1', '13311111111', null, null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170720/4.jpg', '肖文昌', null, '1', null, null, null, null, 'NO000006', '1484209455', '1504228284', '0', '192.168.100.14', '1', 'c384ce20dc659bdcbb1303937223a7b5', '168037', '0', '100000', null, null, null, '0', null, null, '1');
INSERT INTO `guguo_employee` VALUES ('7', '1', '13311113333', null, null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170720/5.jpg', '赵婧彤', null, '0', null, null, null, null, 'NO000007', '1484209455', '1506566553', '0', '127.0.0.1', '1', '1bb5a80cab1144a8625603e34d1a85af', '156551', '0', '100000', null, 'e10adc3949ba59abbe56e057f20f883e', null, '0', null, null, '1');
INSERT INTO `guguo_employee` VALUES ('8', '1', '13311115555', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/datacount/img/1.jpg', '史学鹏', null, '1', null, '', '', '', 'NO000008', '1484209455', '1506475012', '0', '127.0.0.1', '1', '51591d9a3d136f73ba0f2ac5dff14a79', '168494', '0', '100000', null, 'e10adc3949ba59abbe56e057f20f883e', null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('9', '1', '13311116666', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170720/7.jpg', '潘艳梅', null, '1', null, '912581111@qq.com', '', '', 'NO000009', '1484209455', '1506134659', '0', '192.168.102.70', '1', 'ca94f8ba7655a9fc9be6e817d2fa5411', '188183', '0', '100000', null, 'e10adc3949ba59abbe56e057f20f883e', null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('10', '1', '13311118888', null, null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170720/1.jpg', '张雨', null, '0', null, null, null, null, 'NO000010', '1484209455', '1506131299', '0', '192.168.102.51', '1', '95b51bbab1744fcf59e5022a2ffa0ca2', '184374', '0', '100000', null, '4607e782c4d86fd5364d7e4508bb10d9', null, '0', null, null, '1');
INSERT INTO `guguo_employee` VALUES ('11', '1', '13311119999', null, null, '5e8667a439c68f5145dd2fcbecf02209', '/webroot/sdzhongxun/images/20170720/9.jpg', '董倩', null, '0', null, null, null, null, 'NO000011', '1484209455', '1506579929', '0', '192.168.102.70', '1', 'd2b1053cfc310b22998feda9a7cab3d6', '186926', '0', '100000', null, 'e10adc3949ba59abbe56e057f20f883e', null, '0', null, null, '1');
INSERT INTO `guguo_employee` VALUES ('12', '1', '13322227777', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_woman.jpg', '王聪聪', null, '0', null, '', '', '', 'NO000012', '1503996620', '1506559347', '0', '127.0.0.1', '1', 'e10adc3949ba59abbe56e057f20f883e', '197520', '3000', '95000', '706730', 'e10adc3949ba59abbe56e057f20f883e', null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('72', null, '18618888888', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '员工甲', null, '1', null, 'sad@sad.com', '', '', 'no12121', '1484209455', '1506559844', '1', '192.168.102.63', '1', null, '0', '0', '100000', null, null, null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('85', 'sdzhongxun', '15858585518', null, '15858585518', '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '李华', null, '1', null, null, '5858518', 'NB74110', 'NB74110', '1484209455', '1502328509', '1', null, '1', null, '0', '0', '100000', null, null, null, '1', '010-58585518', '518', '1');
INSERT INTO `guguo_employee` VALUES ('90', null, '15655558888', '甲', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_woman.jpg', '员工乙', null, '0', null, '', '', '', 'no12315', '1500088065', '1506559834', '1', '192.168.102.63', '1', null, '0', '0', '100000', null, null, null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('97', null, '15556565667', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '员工丁', null, '1', null, '', '', '', '21', '1506329158', null, '1', null, '1', null, '0', '0', '0', '0', null, null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('98', null, '15591919191', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '员工丙', null, '1', null, '', '', '', 'no123123', '1506329212', null, '1', null, '1', null, '0', '0', '0', '0', null, null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('99', null, '15612344321', '员工子', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '员工子', null, '1', null, '', '', '', '789', '1506407703', null, '1', null, '1', null, '0', '0', '0', '0', null, null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('103', null, '18756788765', '员工丑', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '员工丑', null, '1', null, '', '', '', '456', '1506408330', null, '1', null, '1', null, '0', '0', '0', '0', null, null, '0', '', '', '1');
INSERT INTO `guguo_employee` VALUES ('104', null, '15809877890', '员工寅', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '员工寅', null, '1', null, '', '', '', '343', '1506408452', null, '1', null, '1', null, '0', '0', '0', '0', null, null, '0', '', '', '1');

-- ----------------------------
-- Table structure for guguo_employee_delete
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_delete`;
CREATE TABLE `guguo_employee_delete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corpid` varchar(64) DEFAULT NULL COMMENT '所属公司id',
  `telephone` varchar(16) DEFAULT NULL COMMENT '用户电话号码唯一',
  `nickname` varchar(128) DEFAULT NULL COMMENT '环信账号昵称',
  `username` varchar(128) DEFAULT NULL COMMENT '环信账户用户名',
  `password` varchar(32) DEFAULT NULL,
  `userpic` varchar(256) DEFAULT NULL,
  `truename` varchar(128) DEFAULT NULL COMMENT '员工真实姓名',
  `gender` tinyint(1) DEFAULT NULL COMMENT '性别0女1男',
  `age` tinyint(3) unsigned DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `qqnum` varchar(13) DEFAULT NULL,
  `wechat` varchar(64) DEFAULT NULL,
  `worknum` varchar(64) DEFAULT NULL COMMENT '工号',
  `create_time` int(11) DEFAULT NULL,
  `lastlogintime` int(11) DEFAULT NULL,
  `role` mediumint(9) DEFAULT NULL COMMENT '角色权限',
  `haveim` tinyint(1) DEFAULT '0' COMMENT '是否已注册环信账号0未注册1已注册',
  `lastloginip` varchar(128) DEFAULT NULL,
  `system_token` varchar(32) DEFAULT NULL COMMENT 'app登陆token',
  `left_money` int(11) unsigned DEFAULT '0' COMMENT '剩余金额，单位分',
  `pay_password` varchar(32) DEFAULT NULL COMMENT '支付密码',
  `alipay_account` varchar(128) DEFAULT NULL COMMENT '支付宝账号',
  `is_leader` tinyint(1) DEFAULT '0' COMMENT '0非领导1领导',
  `wired_phone` varchar(12) DEFAULT NULL COMMENT '座机号',
  `part_phone` varchar(8) DEFAULT NULL COMMENT '分机号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_employee_delete
-- ----------------------------
INSERT INTO `guguo_employee_delete` VALUES ('70', null, '15715530636', '', null, null, null, '后黑后', '1', null, 'asd@asd.com', '', '', 'NO999999', null, null, '6', '1', null, null, '0', null, null, '0', '', '');
INSERT INTO `guguo_employee_delete` VALUES ('74', null, '15623523425', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_woman.jpg', '员工乙', '0', null, 'sadsdf@asd.com', '', '', 'no12331', null, null, '6', '1', null, null, '0', null, null, '0', '', '');
INSERT INTO `guguo_employee_delete` VALUES ('84', 'sdzhongxun', '15858585518', '', '15858585518', '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '李华', '1', null, '', '5858518', 'NB74110', 'NB74110', '1484209455', null, '6', '1', null, null, '0', null, null, '1', '010-58585518', '518');
INSERT INTO `guguo_employee_delete` VALUES ('91', null, '15858585858', '', null, '5e8667a439c68f5145dd2fcbecf02209', '/static/images/default_head_man.jpg', '员工丙', '1', null, '', '', '', 'no2345', '1500088583', null, null, '1', null, null, '0', null, null, '0', '', '');

-- ----------------------------
-- Table structure for guguo_employee_import_fail
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_import_fail`;
CREATE TABLE `guguo_employee_import_fail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch` int(11) NOT NULL COMMENT '导入批次',
  `username` varchar(64) DEFAULT NULL COMMENT '员工姓名',
  `telephone` varchar(13) DEFAULT NULL COMMENT '手机号',
  `wired_phone` varchar(12) DEFAULT NULL COMMENT '座机',
  `part_phone` varchar(8) DEFAULT NULL COMMENT '分机',
  `sex` varchar(4) DEFAULT NULL COMMENT '性别',
  `worknum` varchar(64) DEFAULT NULL COMMENT '工号',
  `is_leader` varchar(4) DEFAULT NULL COMMENT '是否领导',
  `struct` varchar(128) DEFAULT NULL,
  `role` varchar(64) DEFAULT NULL COMMENT '角色',
  `qqnum` varchar(13) DEFAULT NULL COMMENT 'qq号',
  `wechat` varchar(32) DEFAULT NULL COMMENT '微信号',
  `remark` varchar(64) DEFAULT NULL COMMENT '失败备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='本表字段值值值对应excel模板，非employee表';

-- ----------------------------
-- Records of guguo_employee_import_fail
-- ----------------------------
INSERT INTO `guguo_employee_import_fail` VALUES ('18', '2017050001', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '导入帐号时发生错误!');
INSERT INTO `guguo_employee_import_fail` VALUES ('19', '2017050006', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('20', '2017070001', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('21', '2017070002', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('22', '2017070003', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('23', '2017070004', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('24', '2017070005', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('25', '2017070006', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('26', '2017070007', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('27', '2017070008', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('28', '2017070009', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', 'email不能为空');
INSERT INTO `guguo_employee_import_fail` VALUES ('29', '2017070011', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', 'Undefined index: truename');
INSERT INTO `guguo_employee_import_fail` VALUES ('30', '2017070012', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', 'Undefined index: truename');
INSERT INTO `guguo_employee_import_fail` VALUES ('31', '2017070014', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', 'Undefined index: status');
INSERT INTO `guguo_employee_import_fail` VALUES ('32', '2017070015', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', null, '1', '5858518', 'NB74110', '注册环信时发生错误!');
INSERT INTO `guguo_employee_import_fail` VALUES ('33', '2017070018', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', '研发部', '职员', '5858518', 'NB74110', '未找到名称为 职员 的职位!');
INSERT INTO `guguo_employee_import_fail` VALUES ('34', '2017070021', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', '研发部,产品组', '职员', '5858518', 'NB74110', 'Undefined index: 研发部,产品组');
INSERT INTO `guguo_employee_import_fail` VALUES ('35', '2017090001', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', '研发部,产品组', '职员', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('36', '2017090002', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', '研发部,产品组', '职员', '5858518', 'NB74110', '手机号已存在!');
INSERT INTO `guguo_employee_import_fail` VALUES ('37', '2017090003', '李华', '15858585518', '010-58585518', '518', '男', 'NB74110', '是', '研发部,产品组', '职员', '5858518', 'NB74110', '手机号已存在!');

-- ----------------------------
-- Table structure for guguo_employee_import_record
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_import_record`;
CREATE TABLE `guguo_employee_import_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL COMMENT '导入时间',
  `operator` int(11) NOT NULL COMMENT '操作者',
  `import_result` tinyint(1) NOT NULL COMMENT '导入结果，0全部失败，1部分失败，2全部成功',
  `success_num` int(11) NOT NULL COMMENT '导入成功的数量',
  `fail_num` int(11) NOT NULL COMMENT '导入失败的数量',
  `batch` int(10) NOT NULL COMMENT '导入批次，格式：201704280001',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_employee_import_record
-- ----------------------------
INSERT INTO `guguo_employee_import_record` VALUES ('48', '1495524022', '1', '0', '0', '1', '2017050001');
INSERT INTO `guguo_employee_import_record` VALUES ('52', '1495524711', '1', '2', '1', '0', '2017050003');
INSERT INTO `guguo_employee_import_record` VALUES ('56', '1496200608', '3', '0', '0', '1', '2017050006');
INSERT INTO `guguo_employee_import_record` VALUES ('57', '1499133011', '3', '0', '0', '1', '2017070001');
INSERT INTO `guguo_employee_import_record` VALUES ('58', '1499133730', '3', '0', '0', '1', '2017070002');
INSERT INTO `guguo_employee_import_record` VALUES ('59', '1499135608', '3', '0', '0', '1', '2017070003');
INSERT INTO `guguo_employee_import_record` VALUES ('60', '1499140528', '3', '0', '0', '1', '2017070004');
INSERT INTO `guguo_employee_import_record` VALUES ('61', '1499143704', '3', '0', '0', '1', '2017070005');
INSERT INTO `guguo_employee_import_record` VALUES ('62', '1499218566', '3', '0', '0', '1', '2017070006');
INSERT INTO `guguo_employee_import_record` VALUES ('63', '1499843527', '3', '0', '0', '1', '2017070007');
INSERT INTO `guguo_employee_import_record` VALUES ('64', '1499921917', '5', '0', '0', '1', '2017070008');
INSERT INTO `guguo_employee_import_record` VALUES ('65', '1499927812', '3', '0', '0', '1', '2017070009');
INSERT INTO `guguo_employee_import_record` VALUES ('66', '1499928053', '3', '0', '0', '0', '2017070010');
INSERT INTO `guguo_employee_import_record` VALUES ('67', '1499928410', '3', '0', '0', '1', '2017070011');
INSERT INTO `guguo_employee_import_record` VALUES ('68', '1499928823', '3', '0', '0', '1', '2017070012');
INSERT INTO `guguo_employee_import_record` VALUES ('69', '1499928905', '3', '2', '1', '0', '2017070013');
INSERT INTO `guguo_employee_import_record` VALUES ('70', '1499929097', '3', '0', '0', '1', '2017070014');
INSERT INTO `guguo_employee_import_record` VALUES ('71', '1499929766', '3', '0', '0', '1', '2017070015');
INSERT INTO `guguo_employee_import_record` VALUES ('72', '1499929804', '3', '2', '1', '0', '2017070016');
INSERT INTO `guguo_employee_import_record` VALUES ('73', '1499931213', '3', '0', '0', '0', '2017070017');
INSERT INTO `guguo_employee_import_record` VALUES ('74', '1499931373', '3', '0', '0', '1', '2017070018');
INSERT INTO `guguo_employee_import_record` VALUES ('75', '1499931462', '3', '0', '0', '0', '2017070019');
INSERT INTO `guguo_employee_import_record` VALUES ('76', '1499931488', '3', '0', '0', '0', '2017070020');
INSERT INTO `guguo_employee_import_record` VALUES ('77', '1499931544', '3', '0', '0', '1', '2017070021');
INSERT INTO `guguo_employee_import_record` VALUES ('78', '1499931635', '3', '2', '1', '0', '2017070022');
INSERT INTO `guguo_employee_import_record` VALUES ('79', '1499931792', '3', '2', '1', '0', '2017070023');
INSERT INTO `guguo_employee_import_record` VALUES ('80', '1500086195', '3', '2', '1', '0', '2017070024');
INSERT INTO `guguo_employee_import_record` VALUES ('81', '1504774681', '3', '0', '0', '1', '2017090001');
INSERT INTO `guguo_employee_import_record` VALUES ('82', '1504775352', '3', '0', '0', '1', '2017090002');
INSERT INTO `guguo_employee_import_record` VALUES ('83', '1504830788', '3', '0', '0', '1', '2017090003');

-- ----------------------------
-- Table structure for guguo_employee_notice
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_notice`;
CREATE TABLE `guguo_employee_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '员工id',
  `customer_id` int(11) DEFAULT NULL COMMENT '客户id',
  `notice_content` varchar(256) DEFAULT NULL COMMENT '提醒内容',
  `notice_title` varchar(32) DEFAULT NULL COMMENT '提醒名称',
  `notice_time` int(11) DEFAULT NULL COMMENT '提醒时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_employee_notice
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_employee_score
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_score`;
CREATE TABLE `guguo_employee_score` (
  `id` int(11) NOT NULL COMMENT '用户id',
  `score` int(11) DEFAULT '0' COMMENT '积分',
  `title` varchar(64) DEFAULT NULL COMMENT '称谓，称号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_employee_score
-- ----------------------------
INSERT INTO `guguo_employee_score` VALUES ('1', '90', '黑暗之门');
INSERT INTO `guguo_employee_score` VALUES ('2', '70', '上古之战');
INSERT INTO `guguo_employee_score` VALUES ('3', '100', '氏族之王');
INSERT INTO `guguo_employee_score` VALUES ('4', '88', '最后守护');
INSERT INTO `guguo_employee_score` VALUES ('5', '96', '巨龙时代');

-- ----------------------------
-- Table structure for guguo_employee_task
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_task`;
CREATE TABLE `guguo_employee_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_name` varchar(64) NOT NULL COMMENT '任务名称',
  `task_start_time` int(10) unsigned NOT NULL COMMENT '开始时间',
  `task_end_time` int(10) unsigned NOT NULL COMMENT '结束时间',
  `task_take_start_time` int(10) unsigned NOT NULL COMMENT '加入开始时间',
  `task_take_end_time` int(10) unsigned NOT NULL COMMENT '加入截止时间',
  `task_type` tinyint(4) NOT NULL COMMENT '任务类型,1:激励任务,2:PK任务,3:悬赏任务,4:日常任务',
  `task_method` tinyint(4) NOT NULL COMMENT '结算方式,1:达标次序,2:达标结果,3:周期排名,4:PK,5:悬赏',
  `content` varchar(255) DEFAULT NULL COMMENT '任务描述',
  `public_to_take` varchar(255) NOT NULL COMMENT '面向群体',
  `public_to_view` varchar(255) NOT NULL COMMENT '可见范围',
  `like_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `tip_count` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '打赏总额',
  `reward_count` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '奖励总额',
  `reward_max_num` int(10) unsigned NOT NULL,
  `create_employee` int(10) unsigned NOT NULL COMMENT '创建员工',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态,0:作废,1:未生效,2:进行中,3:结算中,4:发放中,5:发放完成',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_employee_task
-- ----------------------------
INSERT INTO `guguo_employee_task` VALUES ('1', '1234', '1', '1502801394', '1', '1602801394', '1', '2', null, '1.2,3,12', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '3', '0.00', '1.00', '0', '1', '0', '5');
INSERT INTO `guguo_employee_task` VALUES ('2', '432', '1', '1502801394', '1', '1602801394', '1', '3', '321', '1,2,3,12', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '4', '0.00', '4.00', '0', '2', '213', '5');
INSERT INTO `guguo_employee_task` VALUES ('6', '激励测试任务1', '1', '1502801394', '1', '1602801394', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '100.00', '0', '3', '1502782642', '5');
INSERT INTO `guguo_employee_task` VALUES ('7', '激励测试任务2', '1', '1502801394', '1', '1602801394', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,', '0', '0.00', '100.00', '0', '3', '1502784225', '5');
INSERT INTO `guguo_employee_task` VALUES ('8', '激励测试任务3', '1', '1502801394', '1', '1602801394', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '0', '0.00', '100.00', '0', '3', '1502784255', '5');
INSERT INTO `guguo_employee_task` VALUES ('9', '激励测试任务4', '1', '1502801394', '1', '1602801394', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '0.00', '100.00', '0', '3', '1502872547', '5');
INSERT INTO `guguo_employee_task` VALUES ('10', '激励测试任务0825', '1', '1502801394', '1', '1602801394', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '2', '0.00', '100.00', '0', '3', '1503627344', '5');
INSERT INTO `guguo_employee_task` VALUES ('11', 'PK测试任务5', '1', '1502801394', '1', '1602801394', '2', '4', 'PK测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '0', '0.00', '100.00', '0', '3', '1503629012', '5');
INSERT INTO `guguo_employee_task` VALUES ('15', '激励测试任务6', '1', '1502801394', '0', '0', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '0', '0.00', '4.00', '0', '3', '1504507127', '5');
INSERT INTO `guguo_employee_task` VALUES ('16', '激励测试任务7', '1', '1502801394', '0', '0', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '0.00', '4.00', '0', '3', '1504507151', '5');
INSERT INTO `guguo_employee_task` VALUES ('17', '激励测试任务8', '1', '1502801394', '0', '0', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '2', '0.00', '4.00', '0', '3', '1504507897', '5');
INSERT INTO `guguo_employee_task` VALUES ('18', '激励测试任务9', '1483200000', '1505540800', '0', '0', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '200.00', '4.00', '0', '3', '1504508189', '5');
INSERT INTO `guguo_employee_task` VALUES ('19', '激励测试任务', '1483200000', '1505750400', '0', '0', '1', '1', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '0.00', '4.00', '3', '3', '1505786625', '5');
INSERT INTO `guguo_employee_task` VALUES ('22', '测试PK任务', '1483200000', '1505750400', '1483200000', '1505750400', '2', '4', '测试PK任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '0.00', '2.00', '1', '3', '1505787088', '5');
INSERT INTO `guguo_employee_task` VALUES ('24', '激励测试任务', '1483200000', '1504540800', '0', '0', '1', '2', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '10.00', '1', '3', '1505787258', '5');
INSERT INTO `guguo_employee_task` VALUES ('25', '激励测试任务', '1483200000', '1504540800', '0', '0', '1', '3', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '10.00', '1', '3', '1505787710', '5');
INSERT INTO `guguo_employee_task` VALUES ('26', '激励测试任务', '1483200000', '1504540800', '0', '0', '1', '3', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '100.00', '10', '3', '1505789565', '5');
INSERT INTO `guguo_employee_task` VALUES ('27', '激励测试任务', '1483200000', '1504540800', '0', '0', '1', '3', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '100.00', '10', '3', '1505789850', '5');
INSERT INTO `guguo_employee_task` VALUES ('28', '激励测试任务', '1483200000', '1504540800', '0', '0', '1', '3', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '100.00', '10', '3', '1505800868', '5');
INSERT INTO `guguo_employee_task` VALUES ('29', '激励测试任务', '1483200000', '1504540800', '0', '0', '1', '3', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '100.00', '10', '3', '1505801451', '5');
INSERT INTO `guguo_employee_task` VALUES ('30', '激励测试任务', '1483200000', '1504540800', '0', '0', '1', '3', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '100.00', '10', '3', '1505801561', '5');
INSERT INTO `guguo_employee_task` VALUES ('31', '激励测试任务', '1483200000', '1504540800', '0', '0', '1', '3', '激励测试任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '20.00', '100.00', '10', '3', '1505802856', '5');
INSERT INTO `guguo_employee_task` VALUES ('32', '测试PK任务', '1483200000', '1505750400', '1483200000', '1505750400', '2', '4', '测试PK任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '10.00', '2.00', '1', '3', '1505803285', '5');
INSERT INTO `guguo_employee_task` VALUES ('33', '测试PK任务', '1483200000', '1505750400', '1483200000', '1505750400', '2', '4', '测试PK任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '10.00', '2.00', '1', '3', '1505804299', '5');
INSERT INTO `guguo_employee_task` VALUES ('34', '测试悬赏任务', '1483200000', '1505750400', '0', '0', '3', '5', '测试悬赏任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '10.00', '4.00', '2', '3', '1505805106', '5');
INSERT INTO `guguo_employee_task` VALUES ('36', '测试悬赏任务', '1483200000', '1505750400', '0', '0', '3', '5', '测试悬赏任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '10.00', '4.00', '2', '3', '1505808662', '5');
INSERT INTO `guguo_employee_task` VALUES ('37', '测试悬赏任务', '1483200000', '1505750400', '0', '0', '3', '5', '测试悬赏任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90,12', '1', '10.00', '4.00', '2', '3', '1505810114', '5');
INSERT INTO `guguo_employee_task` VALUES ('38', '测试悬赏任务', '1483200000', '1505750400', '0', '0', '3', '5', '测试悬赏任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '4.00', '2', '3', '1505953221', '3');
INSERT INTO `guguo_employee_task` VALUES ('39', '测试悬赏任务', '1483200000', '1505836799', '0', '0', '3', '5', '测试悬赏任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '4.00', '2', '3', '1505957776', '3');
INSERT INTO `guguo_employee_task` VALUES ('40', '测试悬赏任务', '1483200000', '1505836799', '0', '0', '3', '5', '测试悬赏任务的说明', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1', '0.00', '4.00', '2', '3', '1505958054', '3');
INSERT INTO `guguo_employee_task` VALUES ('41', '哈哈哈哈', '1505923200', '1506009599', '1505923200', '1506009599', '1', '3', '哈哈哈哈', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1', '0.00', '800.00', '8', '4', '1505959383', '5');
INSERT INTO `guguo_employee_task` VALUES ('47', '哈哈哈哈', '1505923200', '1506009599', '1505923200', '1506009599', '1', '3', '测试一下', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1', '0.00', '800.00', '8', '4', '1505964252', '5');
INSERT INTO `guguo_employee_task` VALUES ('48', '哈哈哈哈', '1505923200', '1506009599', '1505923200', '1506009599', '1', '3', '我们自己也许你一世', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1', '0.00', '8.00', '8', '4', '1505965119', '5');
INSERT INTO `guguo_employee_task` VALUES ('49', '气味呃', '1506044872', '1506044932', '0', '0', '1', '1', '额外请', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1', '0.00', '6.00', '2', '3', '1506044895', '5');
INSERT INTO `guguo_employee_task` VALUES ('50', '的撒', '1506045297', '1506045357', '0', '0', '1', '2', '阿斯顿', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1', '0.00', '2.00', '1', '3', '1506045325', '5');
INSERT INTO `guguo_employee_task` VALUES ('51', '额外请我去呃', '1506047619', '1506268800', '1506047679', '1506047739', '2', '4', '高仿噶', '4,5,8,9,12,72,2,3,6,7,10,11', '4,5,8,9,12,72,2,3,6,7,10,11', '21', '133.00', '6.00', '2', '3', '1506047673', '5');
INSERT INTO `guguo_employee_task` VALUES ('53', 'PK任务测试', '1506063420', '1508655420', '1506063420', '1506754620', '2', '4', '任务说明', '4,5,12', '4,5,12', '0', '12.00', '30.00', '3', '12', '1506063485', '2');
INSERT INTO `guguo_employee_task` VALUES ('54', '新任务', '1506009600', '1506095999', '1506009600', '1506095999', '2', '4', '曹鑫鑫想测试', '3', '3', '2', '0.00', '8.00', '8', '4', '1506067861', '5');
INSERT INTO `guguo_employee_task` VALUES ('55', '哈哈哈哈', '1506009600', '1506095999', '1506009600', '1506095999', '3', '5', '哈哈哈哈', '3', '3', '0', '0.00', '40.00', '8', '4', '1506071892', '3');
INSERT INTO `guguo_employee_task` VALUES ('56', '阿奇为', '1485148151', '1506143477', '0', '0', '1', '1', '', '4,5,8,9,12,72,1,2,3,4,5,6,7,8,9,10,11,72,85,90', '4,5,8,9,12,72,1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '1.00', '6.00', '2', '3', '1506143375', '5');
INSERT INTO `guguo_employee_task` VALUES ('57', '太热温热为', '1485152025', '1506147230', '0', '0', '1', '2', '', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '1,2,3,4,5,6,7,8,9,10,11,72,85,90', '0', '0.00', '10.00', '1', '3', '1506147236', '5');
INSERT INTO `guguo_employee_task` VALUES ('58', '哈哈哈哈', '1506096000', '1506182399', '1506096000', '1506182399', '2', '4', '测试', '3', '3', '0', '0.00', '40.00', '8', '4', '1506150226', '5');
INSERT INTO `guguo_employee_task` VALUES ('59', '我的', '1506096000', '1506182399', '1506096000', '1506182399', '1', '3', '你是我的', '1,1,5,9', '1,1,5,9', '0', '0.00', '40.00', '8', '4', '1506150318', '5');
INSERT INTO `guguo_employee_task` VALUES ('60', '测试激励任务', '1504250640', '1506152100', '0', '0', '1', '1', '', '4,5,8,9', '4,5,8,9', '0', '0.00', '10.00', '4', '3', '1506151610', '5');
INSERT INTO `guguo_employee_task` VALUES ('63', '哈哈哈哈水了！', '1506096000', '1506182399', '1506096000', '1506182399', '2', '4', '哈哈哈哈哈笑得', '3,1,4', '3,1,4', '0', '0.00', '440.00', '8', '4', '1506151707', '5');
INSERT INTO `guguo_employee_task` VALUES ('64', '测试PK', '1506151950', '1506151953', '1506151951', '1506151952', '2', '4', '啊舒服舒服', '4,5,8', '4,5,8', '0', '0.00', '6.00', '2', '3', '1506151934', '5');
INSERT INTO `guguo_employee_task` VALUES ('65', '测试激励任务0', '1504337700', '1506238500', '0', '0', '1', '1', '', '3,4,5,6,7', '3,4,5,6,7', '0', '0.00', '10.00', '4', '3', '1506152168', '5');
INSERT INTO `guguo_employee_task` VALUES ('66', '测试激励任务1', '1504424100', '1506238500', '0', '0', '1', '2', '测试激励任务1说明', '3,4,5,6,7,8,9', '3,4,5,6,7,8,9', '0', '0.00', '10.00', '1', '3', '1506152287', '5');
INSERT INTO `guguo_employee_task` VALUES ('67', '测试激励任务2', '1504424100', '1506238500', '0', '0', '1', '3', '', '3,4,5,6,7,8,9,10', '3,4,5,6,7,8,9,10', '1', '1.00', '6.00', '4', '3', '1506152460', '5');
INSERT INTO `guguo_employee_task` VALUES ('68', '测试PK', '1504424523', '1506238997', '1504424578', '1506238993', '2', '4', '', '3,4,5,6,7,8,9', '3,4,5,6,7,8,9', '0', '0.00', '6.00', '2', '3', '1506152735', '5');
INSERT INTO `guguo_employee_task` VALUES ('69', '悬赏测试', '1504338406', '1506239215', '1504424800', '1506239213', '3', '5', '', '3,4,5,6', '3,4,5,6', '0', '0.00', '2.00', '2', '3', '1506152852', '3');
INSERT INTO `guguo_employee_task` VALUES ('70', 'pk任务', '1505923200', '1506182399', '1505923200', '1506182399', '2', '4', '哈哈哈哈', '3,4', '3,4', '0', '0.00', '448.00', '8', '4', '1506153299', '5');
INSERT INTO `guguo_employee_task` VALUES ('71', '再来一个', '1506096000', '1506182399', '1506096000', '1506182399', '2', '4', '哈哈哈哈', '3,1,1,5,4,9,8', '3,1,1,5,4,9,8', '3', '0.00', '704.00', '8', '4', '1506153476', '5');
INSERT INTO `guguo_employee_task` VALUES ('72', '测试客户数', '1505290122', '1506155324', '1505290123', '1506155323', '2', '4', '', '3,4,5,6,7', '3,4,5,6,7', '0', '2000.00', '20.00', '2', '3', '1506154194', '5');
INSERT INTO `guguo_employee_task` VALUES ('73', '123', '1506160597', '1506160602', '1506160599', '1506160600', '2', '4', '', '4,5', '4,5', '0', '0.00', '6.00', '2', '3', '1506160713', '5');
INSERT INTO `guguo_employee_task` VALUES ('74', '哈哈哈哈今天测试', '1506182400', '1506355199', '1506182400', '1506355199', '2', '4', '我要去看电影', '3,4,8', '3,4,8', '0', '59.00', '464.00', '8', '4', '1506303317', '5');
INSERT INTO `guguo_employee_task` VALUES ('75', '激励任务_测试新增客户数', '1504232061', '1506306600', '0', '0', '1', '2', '任务说明', '4,5,8,9,12,72', '4,5,8,9,12,72', '1', '1.00', '10.00', '1', '12', '1506305763', '5');
INSERT INTO `guguo_employee_task` VALUES ('76', '激励任务_测试有效通话数', '1504232477', '1506306150', '0', '0', '1', '2', '说明', '4,5,8,9,12,72', '4,5,8,9,12,72', '1', '0.00', '10.00', '1', '12', '1506306117', '5');
INSERT INTO `guguo_employee_task` VALUES ('77', '激励任务2—测试', '1504237600', '1506311326', '0', '0', '1', '2', '说明', '4,5,8,9,12,72', '4,5,8,9,12,72', '0', '0.00', '10.00', '1', '12', '1506311258', '5');
INSERT INTO `guguo_employee_task` VALUES ('78', '激励任务2—新增客户测试', '1504238036', '1506311700', '0', '0', '1', '2', '说明', '4,5,8,9,12,72', '4,5,8,9,12,72', '2', '0.00', '10.00', '1', '12', '1506311657', '5');
INSERT INTO `guguo_employee_task` VALUES ('79', '激励任务测试是否可以终止', '1506473611', '1507424021', '0', '0', '1', '2', '任务说明', '4,5,8,9,12', '4,5,8,9,12', '0', '0.00', '10.00', '1', '12', '1506473649', '0');
INSERT INTO `guguo_employee_task` VALUES ('80', '测试PK123123', '1504775258', '1506503866', '1504775260', '1506503862', '2', '4', '', '4,5,8,9,12', '4,5,8,9,12', '0', '0.00', '400.00', '4', '3', '1506503246', '5');
INSERT INTO `guguo_employee_task` VALUES ('81', '测试测试', '1506563630', '1506563872', '0', '0', '1', '2', '', '8,9,12,9,12,8,9,1,2,3,4,5,6,7,8,9,8,9', '8,9,12,9,12,8,9,1,2,3,4,5,6,7,8,9,8,9', '0', '0.00', '2.00', '1', '3', '1506563660', '5');
INSERT INTO `guguo_employee_task` VALUES ('82', '测试PK', '1504230968', '1506563953', '1504230970', '1506563951', '2', '4', '', '1,2,3,4,5,6,7,8,9,11,85', '1,2,3,4,5,6,7,8,9,11,85', '0', '0.00', '6.00', '2', '3', '1506563800', '5');

-- ----------------------------
-- Table structure for guguo_employee_task_comment
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_task_comment`;
CREATE TABLE `guguo_employee_task_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL COMMENT '任务id',
  `replyer_id` int(11) NOT NULL COMMENT '评论者id',
  `reply_content` varchar(140) NOT NULL COMMENT '评论内容',
  `reviewer_id` int(11) DEFAULT '0' COMMENT '被评论者ID',
  `reply_comment_id` int(11) DEFAULT '0' COMMENT '被评论的评论ID',
  `comment_time` int(11) DEFAULT NULL COMMENT '评论时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COMMENT='任务评论表';

-- ----------------------------
-- Records of guguo_employee_task_comment
-- ----------------------------
INSERT INTO `guguo_employee_task_comment` VALUES ('1', '1', '2', 'qwee', '0', '0', null);
INSERT INTO `guguo_employee_task_comment` VALUES ('2', '2', '2', 'qwer', '0', '0', '1502509198');
INSERT INTO `guguo_employee_task_comment` VALUES ('3', '2', '2', 'qwer', '2', '2', '1502509248');
INSERT INTO `guguo_employee_task_comment` VALUES ('4', '1', '2', '111111', '0', '0', '1502760283');
INSERT INTO `guguo_employee_task_comment` VALUES ('5', '1', '2', 'adfa', '0', '0', '1503907378');
INSERT INTO `guguo_employee_task_comment` VALUES ('6', '1', '2', 'adfa', '0', '0', '1503907566');
INSERT INTO `guguo_employee_task_comment` VALUES ('7', '1', '2', 'adfa', '0', '0', '1503907898');
INSERT INTO `guguo_employee_task_comment` VALUES ('8', '1', '4', '哈哈哈哈', '0', '0', '1503969173');
INSERT INTO `guguo_employee_task_comment` VALUES ('9', '1', '4', '哈哈哈哈党', '0', '0', '1503969218');
INSERT INTO `guguo_employee_task_comment` VALUES ('10', '1', '4', '哈哈哈哈', '0', '0', '1503993815');
INSERT INTO `guguo_employee_task_comment` VALUES ('11', '1', '4', '哈哈哈哈', '0', '0', '1504054197');
INSERT INTO `guguo_employee_task_comment` VALUES ('12', '1', '4', ' 哈哈哈哈', '0', '0', '1504077027');
INSERT INTO `guguo_employee_task_comment` VALUES ('13', '1', '4', '哈哈哈哈', '0', '0', '1504077048');
INSERT INTO `guguo_employee_task_comment` VALUES ('14', '1', '4', '哈哈哈哈', '0', '0', '1504077277');
INSERT INTO `guguo_employee_task_comment` VALUES ('15', '1', '4', '哈哈哈哈', '0', '0', '1504077556');
INSERT INTO `guguo_employee_task_comment` VALUES ('16', '1', '4', '哈哈哈哈', '0', '0', '1504080698');
INSERT INTO `guguo_employee_task_comment` VALUES ('17', '1', '4', '哈哈哈哈', '0', '0', '1504083195');
INSERT INTO `guguo_employee_task_comment` VALUES ('18', '1', '4', '哈哈哈哈', '0', '0', '1504083848');
INSERT INTO `guguo_employee_task_comment` VALUES ('19', '1', '4', '哈哈哈哈', '0', '0', '1504085062');
INSERT INTO `guguo_employee_task_comment` VALUES ('20', '1', '4', ' 哈哈哈哈', '0', '0', '1504085701');
INSERT INTO `guguo_employee_task_comment` VALUES ('21', '1', '4', '哈哈哈哈你好啊', '0', '0', '1504086952');
INSERT INTO `guguo_employee_task_comment` VALUES ('22', '1', '4', '哈哈哈哈', '0', '0', '1504087044');
INSERT INTO `guguo_employee_task_comment` VALUES ('23', '1', '4', '你就可以养家糊口', '0', '0', '1504087049');
INSERT INTO `guguo_employee_task_comment` VALUES ('24', '1', '4', '在一起就好啦？哈哈', '0', '0', '1504087055');
INSERT INTO `guguo_employee_task_comment` VALUES ('25', '1', '4', '哈哈哈哈', '0', '0', '1504087401');
INSERT INTO `guguo_employee_task_comment` VALUES ('26', '1', '4', '哈哈哈哈', '0', '0', '1504140348');
INSERT INTO `guguo_employee_task_comment` VALUES ('27', '1', '4', '在一起', '0', '0', '1504140359');
INSERT INTO `guguo_employee_task_comment` VALUES ('28', '1', '4', '这个世界', '0', '0', '1504140366');
INSERT INTO `guguo_employee_task_comment` VALUES ('29', '1', '4', '哈哈哈哈', '0', '0', '1504140597');
INSERT INTO `guguo_employee_task_comment` VALUES ('30', '1', '4', '哈哈哈哈', '0', '0', '1504141254');
INSERT INTO `guguo_employee_task_comment` VALUES ('31', '1', '4', '哈哈哈哈', '0', '0', '1504141369');
INSERT INTO `guguo_employee_task_comment` VALUES ('32', '1', '4', '在一起时', '0', '0', '1504141374');
INSERT INTO `guguo_employee_task_comment` VALUES ('33', '1', '4', '在于我无关……哈哈', '0', '0', '1504141381');
INSERT INTO `guguo_employee_task_comment` VALUES ('34', '1', '4', '哈哈哈哈水', '0', '0', '1504144222');
INSERT INTO `guguo_employee_task_comment` VALUES ('35', '1', '4', '在线运营模式立即赢得', '0', '0', '1504144227');
INSERT INTO `guguo_employee_task_comment` VALUES ('36', '1', '4', '哈哈哈哈', '0', '0', '1504226373');
INSERT INTO `guguo_employee_task_comment` VALUES ('37', '1', '4', '哈哈哈哈', '0', '0', '1504227044');
INSERT INTO `guguo_employee_task_comment` VALUES ('38', '1', '4', '在家呢', '0', '0', '1504227049');
INSERT INTO `guguo_employee_task_comment` VALUES ('39', '1', '4', '你就', '0', '0', '1504227053');
INSERT INTO `guguo_employee_task_comment` VALUES ('40', '1', '4', '哈哈哈哈', '0', '0', '1504227241');
INSERT INTO `guguo_employee_task_comment` VALUES ('41', '1', '4', '哈哈哈哈', '2', '1', '1504237840');
INSERT INTO `guguo_employee_task_comment` VALUES ('42', '1', '4', '哈哈哈哈', '2', '1', '1504237852');
INSERT INTO `guguo_employee_task_comment` VALUES ('43', '1', '4', '哈哈哈哈', '2', '1', '1504238069');
INSERT INTO `guguo_employee_task_comment` VALUES ('44', '1', '4', '哈哈哈哈', '2', '1', '1504238078');
INSERT INTO `guguo_employee_task_comment` VALUES ('45', '1', '4', '哈哈哈哈', '2', '1', '1504238160');
INSERT INTO `guguo_employee_task_comment` VALUES ('46', '1', '4', '哈哈哈哈', '2', '1', '1504238177');
INSERT INTO `guguo_employee_task_comment` VALUES ('47', '1', '4', '哈哈哈哈', '2', '1', '1504238204');
INSERT INTO `guguo_employee_task_comment` VALUES ('48', '1', '4', '哈哈哈哈', '2', '1', '1504247162');
INSERT INTO `guguo_employee_task_comment` VALUES ('49', '1', '4', '哈哈哈哈', '0', '0', '1504247171');
INSERT INTO `guguo_employee_task_comment` VALUES ('50', '1', '4', '哈哈哈哈', '0', '0', '1504247186');
INSERT INTO `guguo_employee_task_comment` VALUES ('51', '1', '4', '  哈哈哈哈', '2', '1', '1504248605');
INSERT INTO `guguo_employee_task_comment` VALUES ('52', '1', '4', '你也快来', '2', '1', '1504248610');
INSERT INTO `guguo_employee_task_comment` VALUES ('53', '1', '4', '哈哈哈哈', '0', '0', '1504248613');
INSERT INTO `guguo_employee_task_comment` VALUES ('54', '1', '4', '发', '2', '1', '1504248641');
INSERT INTO `guguo_employee_task_comment` VALUES ('55', '1', '4', '哈哈哈哈', '0', '0', '1504249301');
INSERT INTO `guguo_employee_task_comment` VALUES ('56', '1', '4', '哈哈哈哈', '2', '1', '1504250030');
INSERT INTO `guguo_employee_task_comment` VALUES ('57', '1', '4', '哈哈哈哈', '2', '1', '1504250455');
INSERT INTO `guguo_employee_task_comment` VALUES ('58', '1', '4', '哈哈哈哈', '2', '1', '1504250841');
INSERT INTO `guguo_employee_task_comment` VALUES ('59', '1', '4', '哈哈哈哈', '2', '1', '1504251327');
INSERT INTO `guguo_employee_task_comment` VALUES ('60', '1', '4', '哈哈哈哈', '2', '1', '1504252021');
INSERT INTO `guguo_employee_task_comment` VALUES ('61', '1', '4', '哈哈哈哈', '0', '0', '1504252039');
INSERT INTO `guguo_employee_task_comment` VALUES ('62', '1', '4', '哈哈哈哈', '2', '1', '1504922980');
INSERT INTO `guguo_employee_task_comment` VALUES ('63', '1', '4', '哈哈哈哈', '0', '0', '1504922998');
INSERT INTO `guguo_employee_task_comment` VALUES ('64', '1', '4', '哈哈哈哈', '2', '1', '1504924598');
INSERT INTO `guguo_employee_task_comment` VALUES ('65', '1', '4', '在一起吧', '0', '0', '1504924606');
INSERT INTO `guguo_employee_task_comment` VALUES ('66', '1', '4', '哈哈哈哈哈笑的那么', '0', '0', '1504924656');
INSERT INTO `guguo_employee_task_comment` VALUES ('67', '1', '4', '哈哈哈哈', '0', '0', '1504924663');
INSERT INTO `guguo_employee_task_comment` VALUES ('68', '1', '4', '😂😂😂', '2', '1', '1504927832');
INSERT INTO `guguo_employee_task_comment` VALUES ('69', '1', '4', '哈哈哈哈', '2', '1', '1504927836');
INSERT INTO `guguo_employee_task_comment` VALUES ('70', '1', '4', '哈哈哈哈', '2', '1', '1505119256');
INSERT INTO `guguo_employee_task_comment` VALUES ('71', '1', '4', '哈哈哈哈', '0', '0', '1505119262');
INSERT INTO `guguo_employee_task_comment` VALUES ('72', '1', '4', '😂😂😂😂😂', '2', '1', '1505186297');
INSERT INTO `guguo_employee_task_comment` VALUES ('73', '1', '4', '搞了些什么', '0', '0', '1505186312');
INSERT INTO `guguo_employee_task_comment` VALUES ('74', '1', '4', '哈哈哈哈', '2', '1', '1505208995');
INSERT INTO `guguo_employee_task_comment` VALUES ('75', '1', '4', '在', '2', '1', '1505209002');
INSERT INTO `guguo_employee_task_comment` VALUES ('76', '1', '4', 'W', '2', '1', '1505462490');
INSERT INTO `guguo_employee_task_comment` VALUES ('77', '1', '4', '哈哈哈哈', '2', '1', '1505462642');
INSERT INTO `guguo_employee_task_comment` VALUES ('78', '1', '4', '哈哈哈哈', '2', '1', '1505462750');
INSERT INTO `guguo_employee_task_comment` VALUES ('79', '1', '4', ' 哈哈', '2', '1', '1505717509');
INSERT INTO `guguo_employee_task_comment` VALUES ('80', '1', '4', '哈哈哈哈', '2', '1', '1505785099');
INSERT INTO `guguo_employee_task_comment` VALUES ('81', '1', '4', '哈哈哈哈', '0', '0', '1505785119');
INSERT INTO `guguo_employee_task_comment` VALUES ('82', '1', '4', '哈哈哈哈', '2', '1', '1505803598');
INSERT INTO `guguo_employee_task_comment` VALUES ('83', '33', '4', '😒😒😒', '2', '1', '1505803609');
INSERT INTO `guguo_employee_task_comment` VALUES ('84', '33', '4', '枪王排位', '2', '1', '1505803631');
INSERT INTO `guguo_employee_task_comment` VALUES ('85', '1', '4', ' 哈哈哈哈', '2', '1', '1506061468');
INSERT INTO `guguo_employee_task_comment` VALUES ('86', '1', '4', '哈哈哈哈', '2', '1', '1506062674');
INSERT INTO `guguo_employee_task_comment` VALUES ('87', '1', '4', '哈哈哈哈', '2', '1', '1506062688');
INSERT INTO `guguo_employee_task_comment` VALUES ('88', '51', '4', '哈哈哈哈', '2', '1', '1506062912');
INSERT INTO `guguo_employee_task_comment` VALUES ('89', '53', '5', 'Www', '2', '1', '1506067042');
INSERT INTO `guguo_employee_task_comment` VALUES ('90', '10', '12', '打赏的多', '0', '0', '1506072474');
INSERT INTO `guguo_employee_task_comment` VALUES ('91', '16', '12', '阿达复方丹参', '0', '0', '1506072612');
INSERT INTO `guguo_employee_task_comment` VALUES ('92', '16', '12', '的士速递九分裤', '0', '0', '1506072629');
INSERT INTO `guguo_employee_task_comment` VALUES ('93', '51', '12', '啊啊撒', '0', '0', '1506072985');
INSERT INTO `guguo_employee_task_comment` VALUES ('94', '36', '12', '悬赏的评论', '0', '0', '1506073073');
INSERT INTO `guguo_employee_task_comment` VALUES ('95', '53', '12', 'SaaS', '0', '0', '1506073738');
INSERT INTO `guguo_employee_task_comment` VALUES ('96', '37', '12', '阿萨撒所大多', '0', '0', '1506073749');
INSERT INTO `guguo_employee_task_comment` VALUES ('97', '19', '12', '俺是撒奥所', '0', '0', '1506073761');
INSERT INTO `guguo_employee_task_comment` VALUES ('98', '37', '12', 'fdghhfh', '0', '0', '1506127284');
INSERT INTO `guguo_employee_task_comment` VALUES ('99', '51', '2', 'dsafdasdf', '0', '0', '1506127808');
INSERT INTO `guguo_employee_task_comment` VALUES ('100', '51', '2', 'mishishui', '0', '0', '1506127886');
INSERT INTO `guguo_employee_task_comment` VALUES ('101', '55', '3', 'haha ', '0', '0', '1506128806');
INSERT INTO `guguo_employee_task_comment` VALUES ('102', '54', '3', 'haode', '0', '0', '1506130071');
INSERT INTO `guguo_employee_task_comment` VALUES ('103', '51', '2', '哈哈', '0', '0', '1506135030');
INSERT INTO `guguo_employee_task_comment` VALUES ('104', '51', '2', '爱的发的', '0', '0', '1506136096');
INSERT INTO `guguo_employee_task_comment` VALUES ('105', '51', '2', '回复测试', '12', '93', '1506137035');
INSERT INTO `guguo_employee_task_comment` VALUES ('106', '51', '2', '回复测试发的顺丰 ', '2', '104', '1506137127');
INSERT INTO `guguo_employee_task_comment` VALUES ('107', '51', '2', '哈哈', '4', '88', '1506137799');
INSERT INTO `guguo_employee_task_comment` VALUES ('108', '51', '7', 'hhaa', '2', '107', '1506138921');
INSERT INTO `guguo_employee_task_comment` VALUES ('109', '51', '7', 'haha', '2', '107', '1506138951');
INSERT INTO `guguo_employee_task_comment` VALUES ('110', '51', '7', '好的', '12', '93', '1506138996');
INSERT INTO `guguo_employee_task_comment` VALUES ('111', '48', '3', '阿萨德', '0', '0', '1506147225');
INSERT INTO `guguo_employee_task_comment` VALUES ('112', '48', '3', '阿福', '3', '111', '1506147232');
INSERT INTO `guguo_employee_task_comment` VALUES ('113', '65', '3', '撒大声地', '0', '0', '1506153069');
INSERT INTO `guguo_employee_task_comment` VALUES ('114', '65', '3', '按时', '3', '113', '1506153074');
INSERT INTO `guguo_employee_task_comment` VALUES ('115', '67', '7', '哈哈', '0', '0', '1506322539');
INSERT INTO `guguo_employee_task_comment` VALUES ('116', '67', '7', '你好', '7', '115', '1506322548');
INSERT INTO `guguo_employee_task_comment` VALUES ('117', '67', '7', 'haaaaaaaaaaaaaaaaaaaaaaaaaa啊哈哈哈哈或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或或哈啊啊啊啊阿啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊哈啊啊啊啊阿啊啊啊啊啊啊啊\n', '0', '0', '1506329479');
INSERT INTO `guguo_employee_task_comment` VALUES ('118', '67', '7', 'as', '0', '0', '1506331970');
INSERT INTO `guguo_employee_task_comment` VALUES ('119', '67', '7', 'sf', '0', '0', '1506331984');
INSERT INTO `guguo_employee_task_comment` VALUES ('120', '67', '7', 'haode ', '7', '118', '1506332088');
INSERT INTO `guguo_employee_task_comment` VALUES ('121', '53', '12', 'PK啊PK', '0', '0', '1506389964');
INSERT INTO `guguo_employee_task_comment` VALUES ('122', '67', '7', 'haha ', '7', '120', '1506390000');
INSERT INTO `guguo_employee_task_comment` VALUES ('123', '67', '7', '么么哒', '0', '0', '1506390333');
INSERT INTO `guguo_employee_task_comment` VALUES ('124', '53', '12', '测试回复', '5', '89', '1506390638');
INSERT INTO `guguo_employee_task_comment` VALUES ('125', '67', '7', '么么哒', '7', '123', '1506391072');
INSERT INTO `guguo_employee_task_comment` VALUES ('126', '65', '7', '22', '0', '0', '1506393811');
INSERT INTO `guguo_employee_task_comment` VALUES ('127', '65', '7', '哈哈', '3', '113', '1506393826');

-- ----------------------------
-- Table structure for guguo_employee_task_guess
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_task_guess`;
CREATE TABLE `guguo_employee_task_guess` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL COMMENT '任务id',
  `guess_take_employee` int(11) NOT NULL COMMENT '被猜参与员工ID',
  `guess_employee` int(11) NOT NULL COMMENT '猜输赢员工ID',
  `guess_money` int(11) NOT NULL COMMENT '参与金额',
  `guess_time` int(11) NOT NULL COMMENT '参与时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='任务猜输赢';

-- ----------------------------
-- Records of guguo_employee_task_guess
-- ----------------------------
INSERT INTO `guguo_employee_task_guess` VALUES ('1', '15', '3', '1', '100', '1505181215');
INSERT INTO `guguo_employee_task_guess` VALUES ('9', '15', '3', '2', '100', '1505187346');
INSERT INTO `guguo_employee_task_guess` VALUES ('10', '22', '4', '1', '100', '1505187431');
INSERT INTO `guguo_employee_task_guess` VALUES ('11', '22', '4', '2', '100', '1505187504');
INSERT INTO `guguo_employee_task_guess` VALUES ('12', '22', '4', '3', '100', '1505187543');
INSERT INTO `guguo_employee_task_guess` VALUES ('13', '22', '4', '4', '100', '1505187690');
INSERT INTO `guguo_employee_task_guess` VALUES ('14', '22', '3', '5', '100', '1505187690');
INSERT INTO `guguo_employee_task_guess` VALUES ('15', '22', '4', '2', '100', '1505198148');
INSERT INTO `guguo_employee_task_guess` VALUES ('16', '22', '4', '2', '50', '1505266582');
INSERT INTO `guguo_employee_task_guess` VALUES ('17', '22', '4', '2', '50', '1505266871');
INSERT INTO `guguo_employee_task_guess` VALUES ('18', '22', '4', '2', '50', '1505266901');
INSERT INTO `guguo_employee_task_guess` VALUES ('19', '32', '3', '5', '10', '1505803626');
INSERT INTO `guguo_employee_task_guess` VALUES ('20', '33', '3', '5', '10', '1505804382');
INSERT INTO `guguo_employee_task_guess` VALUES ('21', '70', '4', '3', '2', '1506153342');
INSERT INTO `guguo_employee_task_guess` VALUES ('22', '72', '4', '7', '300', '1506154915');

-- ----------------------------
-- Table structure for guguo_employee_task_like
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_task_like`;
CREATE TABLE `guguo_employee_task_like` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL COMMENT '喜欢任务id',
  `user_id` int(11) NOT NULL COMMENT '喜欢人id',
  `like_time` int(11) unsigned NOT NULL COMMENT '喜欢时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_employee_task_like
-- ----------------------------
INSERT INTO `guguo_employee_task_like` VALUES ('1', '10', '12', '111');
INSERT INTO `guguo_employee_task_like` VALUES ('2', '9', '12', '111');
INSERT INTO `guguo_employee_task_like` VALUES ('3', '10', '1', '111');
INSERT INTO `guguo_employee_task_like` VALUES ('6', '37', '12', '1506044810');
INSERT INTO `guguo_employee_task_like` VALUES ('7', '36', '12', '1506044812');
INSERT INTO `guguo_employee_task_like` VALUES ('8', '34', '12', '1506044815');
INSERT INTO `guguo_employee_task_like` VALUES ('9', '33', '12', '1506044816');
INSERT INTO `guguo_employee_task_like` VALUES ('11', '32', '12', '1506044823');
INSERT INTO `guguo_employee_task_like` VALUES ('13', '19', '12', '1506044828');
INSERT INTO `guguo_employee_task_like` VALUES ('15', '17', '12', '1506044836');
INSERT INTO `guguo_employee_task_like` VALUES ('16', '16', '12', '1506044838');
INSERT INTO `guguo_employee_task_like` VALUES ('18', '50', '3', '1506045371');
INSERT INTO `guguo_employee_task_like` VALUES ('19', '49', '3', '1506045372');
INSERT INTO `guguo_employee_task_like` VALUES ('21', '47', '3', '1506045375');
INSERT INTO `guguo_employee_task_like` VALUES ('22', '41', '3', '1506045377');
INSERT INTO `guguo_employee_task_like` VALUES ('25', '17', '3', '1506045394');
INSERT INTO `guguo_employee_task_like` VALUES ('26', '48', '3', '1506045407');
INSERT INTO `guguo_employee_task_like` VALUES ('34', '40', '3', '1506061646');
INSERT INTO `guguo_employee_task_like` VALUES ('234', '1', '4', '1506153807');
INSERT INTO `guguo_employee_task_like` VALUES ('236', '71', '4', '1506153927');
INSERT INTO `guguo_employee_task_like` VALUES ('239', '71', '3', '1506154169');
INSERT INTO `guguo_employee_task_like` VALUES ('240', '71', '5', '1506155056');
INSERT INTO `guguo_employee_task_like` VALUES ('243', '76', '12', '1506306144');
INSERT INTO `guguo_employee_task_like` VALUES ('248', '67', '7', '1506321261');
INSERT INTO `guguo_employee_task_like` VALUES ('250', '75', '12', '1506387440');
INSERT INTO `guguo_employee_task_like` VALUES ('252', '22', '12', '1506415201');
INSERT INTO `guguo_employee_task_like` VALUES ('254', '78', '12', '1506418598');
INSERT INTO `guguo_employee_task_like` VALUES ('255', '78', '8', '1506475986');

-- ----------------------------
-- Table structure for guguo_employee_task_reward
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_task_reward`;
CREATE TABLE `guguo_employee_task_reward` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL COMMENT '任务id',
  `reward_type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '奖励类型,1:均分,2:定额',
  `reward_method` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '奖励方式,1:完成后发放,2:确认后发放,3:接任务立即发放,4:达到条件后发放',
  `reward_amount` int(10) unsigned NOT NULL COMMENT '奖励额度',
  `reward_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '奖励数量,0为不限制',
  `reward_start` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '奖励起始名次',
  `reward_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '奖励结束名次',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COMMENT='任务奖励';

-- ----------------------------
-- Records of guguo_employee_task_reward
-- ----------------------------
INSERT INTO `guguo_employee_task_reward` VALUES ('1', '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('2', '2', '1', '1', '2', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('5', '6', '1', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('6', '7', '1', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('7', '8', '1', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('8', '9', '1', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('9', '10', '1', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('10', '11', '1', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('14', '15', '2', '1', '2', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('15', '15', '2', '1', '1', '2', '2', '3');
INSERT INTO `guguo_employee_task_reward` VALUES ('17', '16', '2', '1', '2', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('18', '16', '2', '1', '1', '2', '2', '3');
INSERT INTO `guguo_employee_task_reward` VALUES ('20', '17', '2', '1', '2', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('21', '17', '2', '1', '1', '2', '2', '3');
INSERT INTO `guguo_employee_task_reward` VALUES ('23', '18', '2', '1', '2', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('24', '18', '2', '1', '1', '2', '2', '3');
INSERT INTO `guguo_employee_task_reward` VALUES ('26', '19', '2', '4', '2', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('27', '19', '2', '4', '1', '2', '2', '3');
INSERT INTO `guguo_employee_task_reward` VALUES ('31', '22', '2', '1', '2', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('33', '24', '1', '1', '10', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('34', '25', '2', '1', '10', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('35', '26', '2', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('36', '27', '2', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('37', '28', '2', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('38', '29', '2', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('39', '30', '2', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('40', '31', '2', '1', '10', '10', '1', '10');
INSERT INTO `guguo_employee_task_reward` VALUES ('41', '32', '2', '1', '2', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('42', '33', '2', '1', '2', '1', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('43', '34', '2', '1', '2', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('45', '36', '2', '1', '2', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('46', '37', '2', '1', '2', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('47', '38', '2', '1', '2', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('48', '39', '2', '1', '2', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('49', '40', '2', '1', '2', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('50', '41', '2', '1', '100', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('56', '47', '2', '1', '100', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('57', '48', '2', '1', '1', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('58', '49', '2', '4', '3', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('59', '50', '1', '1', '2', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('60', '51', '2', '1', '3', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('62', '53', '2', '1', '10', '3', '1', '3');
INSERT INTO `guguo_employee_task_reward` VALUES ('63', '54', '2', '1', '1', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('64', '55', '2', '1', '5', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('65', '56', '2', '4', '3', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('66', '57', '1', '1', '10', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('67', '58', '2', '1', '5', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('68', '59', '2', '1', '5', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('69', '60', '2', '4', '2', '2', '3', '4');
INSERT INTO `guguo_employee_task_reward` VALUES ('70', '60', '2', '4', '3', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('74', '63', '2', '1', '55', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('75', '64', '2', '1', '3', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('76', '65', '2', '4', '2', '2', '3', '4');
INSERT INTO `guguo_employee_task_reward` VALUES ('77', '65', '2', '4', '3', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('79', '66', '1', '1', '10', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('80', '67', '2', '1', '1', '2', '3', '4');
INSERT INTO `guguo_employee_task_reward` VALUES ('81', '67', '2', '1', '2', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('83', '68', '2', '1', '3', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('84', '69', '2', '1', '1', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('85', '70', '2', '1', '56', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('86', '71', '2', '1', '88', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('87', '72', '2', '1', '10', '3', '1', '3');
INSERT INTO `guguo_employee_task_reward` VALUES ('88', '73', '2', '1', '3', '2', '1', '2');
INSERT INTO `guguo_employee_task_reward` VALUES ('89', '74', '2', '1', '58', '8', '1', '8');
INSERT INTO `guguo_employee_task_reward` VALUES ('90', '75', '1', '1', '10', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('91', '76', '1', '1', '10', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('92', '77', '1', '1', '10', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('93', '78', '1', '1', '10', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('94', '79', '1', '1', '10', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('95', '80', '2', '1', '100', '4', '1', '4');
INSERT INTO `guguo_employee_task_reward` VALUES ('96', '81', '1', '1', '2', '0', '1', '1');
INSERT INTO `guguo_employee_task_reward` VALUES ('97', '82', '2', '1', '3', '2', '1', '2');

-- ----------------------------
-- Table structure for guguo_employee_task_take
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_task_take`;
CREATE TABLE `guguo_employee_task_take` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL COMMENT '任务id',
  `take_employee` int(11) NOT NULL COMMENT '参与员工ID',
  `take_time` int(11) NOT NULL COMMENT '参与时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=536 DEFAULT CHARSET=utf8 COMMENT='任务目标';

-- ----------------------------
-- Records of guguo_employee_task_take
-- ----------------------------
INSERT INTO `guguo_employee_task_take` VALUES ('1', '1', '2', '12345');
INSERT INTO `guguo_employee_task_take` VALUES ('2', '1', '1', '2123');
INSERT INTO `guguo_employee_task_take` VALUES ('4', '2', '1', '1602801394');
INSERT INTO `guguo_employee_task_take` VALUES ('5', '2', '2', '1602801394');
INSERT INTO `guguo_employee_task_take` VALUES ('6', '11', '1', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('7', '11', '2', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('8', '11', '3', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('9', '11', '4', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('10', '11', '5', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('11', '11', '6', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('12', '11', '7', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('13', '11', '8', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('14', '11', '9', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('15', '11', '10', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('16', '11', '11', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('17', '11', '72', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('18', '11', '85', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('19', '11', '90', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('20', '10', '1', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('21', '10', '2', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('22', '10', '3', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('23', '10', '4', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('24', '10', '5', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('25', '10', '6', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('26', '10', '7', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('27', '10', '8', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('28', '10', '9', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('29', '10', '10', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('30', '10', '11', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('31', '10', '72', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('32', '10', '85', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('33', '10', '90', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('34', '9', '1', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('35', '9', '2', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('36', '9', '3', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('37', '9', '4', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('38', '9', '5', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('39', '9', '6', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('40', '9', '7', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('41', '9', '8', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('42', '9', '9', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('43', '9', '10', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('44', '9', '11', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('45', '9', '72', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('46', '9', '85', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('47', '9', '90', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('48', '8', '1', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('49', '8', '2', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('50', '8', '3', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('51', '8', '4', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('52', '8', '5', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('53', '8', '6', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('54', '8', '7', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('55', '8', '8', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('56', '8', '9', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('57', '8', '10', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('58', '8', '11', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('59', '8', '72', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('60', '8', '85', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('61', '8', '90', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('62', '7', '1', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('63', '7', '2', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('64', '7', '3', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('65', '7', '4', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('66', '7', '5', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('67', '7', '6', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('68', '7', '7', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('69', '7', '8', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('70', '7', '9', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('71', '7', '10', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('72', '7', '11', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('73', '7', '72', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('74', '7', '85', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('75', '7', '90', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('76', '6', '1', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('77', '6', '2', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('78', '6', '3', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('79', '6', '4', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('80', '6', '5', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('81', '6', '6', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('82', '6', '7', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('83', '6', '8', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('84', '6', '9', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('85', '6', '10', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('86', '6', '11', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('87', '6', '72', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('88', '6', '85', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('89', '6', '90', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('90', '2', '3', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('91', '2', '4', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('92', '1', '5', '1504231117');
INSERT INTO `guguo_employee_task_take` VALUES ('133', '1', '3', '1');
INSERT INTO `guguo_employee_task_take` VALUES ('134', '2', '5', '1');
INSERT INTO `guguo_employee_task_take` VALUES ('150', '15', '3', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('151', '15', '1', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('152', '15', '2', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('153', '15', '3', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('154', '15', '4', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('155', '15', '5', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('156', '15', '6', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('157', '15', '7', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('158', '15', '8', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('159', '15', '9', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('160', '15', '10', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('161', '15', '11', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('162', '15', '72', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('163', '15', '85', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('164', '15', '90', '1504507127');
INSERT INTO `guguo_employee_task_take` VALUES ('165', '16', '3', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('166', '16', '1', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('167', '16', '2', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('168', '16', '3', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('169', '16', '4', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('170', '16', '5', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('171', '16', '6', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('172', '16', '7', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('173', '16', '8', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('174', '16', '9', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('175', '16', '10', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('176', '16', '11', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('177', '16', '72', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('178', '16', '85', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('179', '16', '90', '1504507151');
INSERT INTO `guguo_employee_task_take` VALUES ('180', '17', '3', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('181', '17', '1', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('182', '17', '2', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('183', '17', '3', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('184', '17', '4', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('185', '17', '5', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('186', '17', '6', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('187', '17', '7', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('188', '17', '8', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('189', '17', '9', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('190', '17', '10', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('191', '17', '11', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('192', '17', '72', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('193', '17', '85', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('194', '17', '90', '1504507897');
INSERT INTO `guguo_employee_task_take` VALUES ('195', '18', '3', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('196', '18', '1', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('197', '18', '2', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('198', '18', '3', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('199', '18', '4', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('200', '18', '5', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('201', '18', '6', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('202', '18', '7', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('203', '18', '8', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('204', '18', '9', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('205', '18', '10', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('206', '18', '11', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('207', '18', '72', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('208', '18', '85', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('209', '18', '90', '1504508189');
INSERT INTO `guguo_employee_task_take` VALUES ('210', '19', '3', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('211', '19', '1', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('212', '19', '2', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('213', '19', '3', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('214', '19', '4', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('215', '19', '5', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('216', '19', '6', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('217', '19', '7', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('218', '19', '8', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('219', '19', '9', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('220', '19', '10', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('221', '19', '11', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('222', '19', '72', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('223', '19', '85', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('224', '19', '90', '1505786625');
INSERT INTO `guguo_employee_task_take` VALUES ('227', '22', '3', '1505787088');
INSERT INTO `guguo_employee_task_take` VALUES ('243', '24', '3', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('244', '24', '1', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('245', '24', '2', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('246', '24', '3', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('247', '24', '4', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('248', '24', '5', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('249', '24', '6', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('250', '24', '7', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('251', '24', '8', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('252', '24', '9', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('253', '24', '10', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('254', '24', '11', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('255', '24', '72', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('256', '24', '85', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('257', '24', '90', '1505787258');
INSERT INTO `guguo_employee_task_take` VALUES ('258', '25', '3', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('259', '25', '1', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('260', '25', '2', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('261', '25', '3', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('262', '25', '4', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('263', '25', '5', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('264', '25', '6', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('265', '25', '7', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('266', '25', '8', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('267', '25', '9', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('268', '25', '10', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('269', '25', '11', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('270', '25', '72', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('271', '25', '85', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('272', '25', '90', '1505787710');
INSERT INTO `guguo_employee_task_take` VALUES ('273', '26', '3', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('274', '26', '1', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('275', '26', '2', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('276', '26', '3', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('277', '26', '4', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('278', '26', '5', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('279', '26', '6', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('280', '26', '7', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('281', '26', '8', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('282', '26', '9', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('283', '26', '10', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('284', '26', '11', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('285', '26', '72', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('286', '26', '85', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('287', '26', '90', '1505789565');
INSERT INTO `guguo_employee_task_take` VALUES ('288', '27', '3', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('289', '27', '1', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('290', '27', '2', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('291', '27', '3', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('292', '27', '4', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('293', '27', '5', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('294', '27', '6', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('295', '27', '7', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('296', '27', '8', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('297', '27', '9', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('298', '27', '10', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('299', '27', '11', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('300', '27', '72', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('301', '27', '85', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('302', '27', '90', '1505789850');
INSERT INTO `guguo_employee_task_take` VALUES ('303', '28', '3', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('304', '28', '1', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('305', '28', '2', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('306', '28', '3', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('307', '28', '4', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('308', '28', '5', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('309', '28', '6', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('310', '28', '7', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('311', '28', '8', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('312', '28', '9', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('313', '28', '10', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('314', '28', '11', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('315', '28', '72', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('316', '28', '85', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('317', '28', '90', '1505800868');
INSERT INTO `guguo_employee_task_take` VALUES ('318', '29', '3', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('319', '29', '1', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('320', '29', '2', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('321', '29', '3', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('322', '29', '4', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('323', '29', '5', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('324', '29', '6', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('325', '29', '7', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('326', '29', '8', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('327', '29', '9', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('328', '29', '10', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('329', '29', '11', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('330', '29', '72', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('331', '29', '85', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('332', '29', '90', '1505801451');
INSERT INTO `guguo_employee_task_take` VALUES ('333', '30', '3', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('334', '30', '1', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('335', '30', '2', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('336', '30', '3', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('337', '30', '4', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('338', '30', '5', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('339', '30', '6', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('340', '30', '7', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('341', '30', '8', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('342', '30', '9', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('343', '30', '10', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('344', '30', '11', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('345', '30', '72', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('346', '30', '85', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('347', '30', '90', '1505801561');
INSERT INTO `guguo_employee_task_take` VALUES ('348', '31', '3', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('349', '31', '1', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('350', '31', '2', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('351', '31', '3', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('352', '31', '4', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('353', '31', '5', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('354', '31', '6', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('355', '31', '7', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('356', '31', '8', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('357', '31', '9', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('358', '31', '10', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('359', '31', '11', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('360', '31', '72', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('361', '31', '85', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('362', '31', '90', '1505802856');
INSERT INTO `guguo_employee_task_take` VALUES ('363', '32', '3', '1505803285');
INSERT INTO `guguo_employee_task_take` VALUES ('364', '33', '3', '1505804299');
INSERT INTO `guguo_employee_task_take` VALUES ('366', '34', '5', '1505805787');
INSERT INTO `guguo_employee_task_take` VALUES ('367', '36', '5', '1505808770');
INSERT INTO `guguo_employee_task_take` VALUES ('368', '37', '5', '1505810213');
INSERT INTO `guguo_employee_task_take` VALUES ('369', '37', '2', '1505810218');
INSERT INTO `guguo_employee_task_take` VALUES ('370', '47', '3', '1505964252');
INSERT INTO `guguo_employee_task_take` VALUES ('371', '48', '3', '1505965119');
INSERT INTO `guguo_employee_task_take` VALUES ('372', '50', '1101', '1506045325');
INSERT INTO `guguo_employee_task_take` VALUES ('373', '50', '1102', '1506045325');
INSERT INTO `guguo_employee_task_take` VALUES ('375', '51', '3', '1506047673');
INSERT INTO `guguo_employee_task_take` VALUES ('376', '2', '12', '1506060510');
INSERT INTO `guguo_employee_task_take` VALUES ('377', '10', '12', '1506060558');
INSERT INTO `guguo_employee_task_take` VALUES ('378', '9', '12', '1506060625');
INSERT INTO `guguo_employee_task_take` VALUES ('379', '53', '12', '1506063485');
INSERT INTO `guguo_employee_task_take` VALUES ('382', '54', '4', '1506067861');
INSERT INTO `guguo_employee_task_take` VALUES ('383', '56', '4', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('384', '56', '5', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('385', '56', '8', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('386', '56', '9', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('387', '56', '12', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('388', '56', '72', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('389', '56', '1', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('390', '56', '2', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('391', '56', '3', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('392', '56', '6', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('393', '56', '7', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('394', '56', '10', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('395', '56', '11', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('396', '56', '85', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('397', '56', '90', '1506143375');
INSERT INTO `guguo_employee_task_take` VALUES ('398', '57', '1', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('399', '57', '2', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('400', '57', '3', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('401', '57', '4', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('402', '57', '5', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('403', '57', '6', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('404', '57', '7', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('405', '57', '8', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('406', '57', '9', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('407', '57', '10', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('408', '57', '11', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('409', '57', '72', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('410', '57', '85', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('411', '57', '90', '1506147236');
INSERT INTO `guguo_employee_task_take` VALUES ('413', '58', '4', '1506150226');
INSERT INTO `guguo_employee_task_take` VALUES ('414', '59', '1', '1506150318');
INSERT INTO `guguo_employee_task_take` VALUES ('415', '59', '5', '1506150318');
INSERT INTO `guguo_employee_task_take` VALUES ('416', '59', '9', '1506150318');
INSERT INTO `guguo_employee_task_take` VALUES ('417', '60', '4', '1506151610');
INSERT INTO `guguo_employee_task_take` VALUES ('418', '60', '5', '1506151610');
INSERT INTO `guguo_employee_task_take` VALUES ('419', '60', '8', '1506151610');
INSERT INTO `guguo_employee_task_take` VALUES ('420', '60', '9', '1506151610');
INSERT INTO `guguo_employee_task_take` VALUES ('430', '63', '4', '1506151707');
INSERT INTO `guguo_employee_task_take` VALUES ('431', '64', '3', '1506151934');
INSERT INTO `guguo_employee_task_take` VALUES ('432', '65', '3', '1506152168');
INSERT INTO `guguo_employee_task_take` VALUES ('433', '65', '4', '1506152168');
INSERT INTO `guguo_employee_task_take` VALUES ('434', '65', '5', '1506152168');
INSERT INTO `guguo_employee_task_take` VALUES ('435', '65', '6', '1506152168');
INSERT INTO `guguo_employee_task_take` VALUES ('436', '65', '7', '1506152168');
INSERT INTO `guguo_employee_task_take` VALUES ('439', '66', '3', '1506152287');
INSERT INTO `guguo_employee_task_take` VALUES ('440', '66', '4', '1506152287');
INSERT INTO `guguo_employee_task_take` VALUES ('441', '66', '5', '1506152287');
INSERT INTO `guguo_employee_task_take` VALUES ('442', '66', '6', '1506152287');
INSERT INTO `guguo_employee_task_take` VALUES ('443', '66', '7', '1506152287');
INSERT INTO `guguo_employee_task_take` VALUES ('444', '66', '8', '1506152287');
INSERT INTO `guguo_employee_task_take` VALUES ('445', '66', '9', '1506152287');
INSERT INTO `guguo_employee_task_take` VALUES ('446', '67', '3', '1506152460');
INSERT INTO `guguo_employee_task_take` VALUES ('447', '67', '4', '1506152460');
INSERT INTO `guguo_employee_task_take` VALUES ('448', '67', '5', '1506152460');
INSERT INTO `guguo_employee_task_take` VALUES ('449', '67', '6', '1506152460');
INSERT INTO `guguo_employee_task_take` VALUES ('450', '67', '7', '1506152460');
INSERT INTO `guguo_employee_task_take` VALUES ('451', '67', '8', '1506152460');
INSERT INTO `guguo_employee_task_take` VALUES ('452', '67', '9', '1506152460');
INSERT INTO `guguo_employee_task_take` VALUES ('453', '67', '10', '1506152460');
INSERT INTO `guguo_employee_task_take` VALUES ('461', '68', '3', '1506152735');
INSERT INTO `guguo_employee_task_take` VALUES ('462', '70', '4', '1506153299');
INSERT INTO `guguo_employee_task_take` VALUES ('463', '71', '4', '1506153476');
INSERT INTO `guguo_employee_task_take` VALUES ('464', '71', '3', '1506153619');
INSERT INTO `guguo_employee_task_take` VALUES ('465', '72', '3', '1506154194');
INSERT INTO `guguo_employee_task_take` VALUES ('466', '72', '4', '1506154417');
INSERT INTO `guguo_employee_task_take` VALUES ('467', '72', '5', '1506154684');
INSERT INTO `guguo_employee_task_take` VALUES ('468', '73', '3', '1506160713');
INSERT INTO `guguo_employee_task_take` VALUES ('469', '74', '4', '1506303317');
INSERT INTO `guguo_employee_task_take` VALUES ('470', '75', '4', '1506305763');
INSERT INTO `guguo_employee_task_take` VALUES ('471', '75', '5', '1506305763');
INSERT INTO `guguo_employee_task_take` VALUES ('472', '75', '8', '1506305763');
INSERT INTO `guguo_employee_task_take` VALUES ('473', '75', '9', '1506305763');
INSERT INTO `guguo_employee_task_take` VALUES ('474', '75', '12', '1506305763');
INSERT INTO `guguo_employee_task_take` VALUES ('475', '75', '72', '1506305763');
INSERT INTO `guguo_employee_task_take` VALUES ('477', '76', '4', '1506306117');
INSERT INTO `guguo_employee_task_take` VALUES ('478', '76', '5', '1506306117');
INSERT INTO `guguo_employee_task_take` VALUES ('479', '76', '8', '1506306117');
INSERT INTO `guguo_employee_task_take` VALUES ('480', '76', '9', '1506306117');
INSERT INTO `guguo_employee_task_take` VALUES ('481', '76', '12', '1506306117');
INSERT INTO `guguo_employee_task_take` VALUES ('482', '76', '72', '1506306117');
INSERT INTO `guguo_employee_task_take` VALUES ('484', '77', '4', '1506311258');
INSERT INTO `guguo_employee_task_take` VALUES ('485', '77', '5', '1506311258');
INSERT INTO `guguo_employee_task_take` VALUES ('486', '77', '8', '1506311258');
INSERT INTO `guguo_employee_task_take` VALUES ('487', '77', '9', '1506311258');
INSERT INTO `guguo_employee_task_take` VALUES ('488', '77', '12', '1506311258');
INSERT INTO `guguo_employee_task_take` VALUES ('489', '77', '72', '1506311258');
INSERT INTO `guguo_employee_task_take` VALUES ('491', '78', '4', '1506311657');
INSERT INTO `guguo_employee_task_take` VALUES ('492', '78', '5', '1506311657');
INSERT INTO `guguo_employee_task_take` VALUES ('493', '78', '8', '1506311657');
INSERT INTO `guguo_employee_task_take` VALUES ('494', '78', '9', '1506311657');
INSERT INTO `guguo_employee_task_take` VALUES ('495', '78', '12', '1506311657');
INSERT INTO `guguo_employee_task_take` VALUES ('496', '78', '72', '1506311657');
INSERT INTO `guguo_employee_task_take` VALUES ('498', '79', '4', '1506473649');
INSERT INTO `guguo_employee_task_take` VALUES ('499', '79', '5', '1506473649');
INSERT INTO `guguo_employee_task_take` VALUES ('500', '79', '8', '1506473649');
INSERT INTO `guguo_employee_task_take` VALUES ('501', '79', '9', '1506473649');
INSERT INTO `guguo_employee_task_take` VALUES ('502', '79', '12', '1506473649');
INSERT INTO `guguo_employee_task_take` VALUES ('505', '80', '3', '1506503246');
INSERT INTO `guguo_employee_task_take` VALUES ('506', '81', '8', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('507', '81', '9', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('508', '81', '12', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('509', '81', '1', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('510', '81', '2', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('511', '81', '3', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('512', '81', '4', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('513', '81', '5', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('514', '81', '6', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('515', '81', '7', '1506563660');
INSERT INTO `guguo_employee_task_take` VALUES ('521', '82', '3', '1506563800');

-- ----------------------------
-- Table structure for guguo_employee_task_target
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_task_target`;
CREATE TABLE `guguo_employee_task_target` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL COMMENT '任务id',
  `target_type` tinyint(4) NOT NULL COMMENT '目标类型,1:通话数,2:商机数,3:成交额,4:成单数,5:拜访数,6:新增客户数,7:悬赏拜访对象',
  `target_num` int(10) unsigned NOT NULL COMMENT '目标量',
  `target_customer` int(11) NOT NULL DEFAULT '0' COMMENT '目标客户',
  `target_description` varchar(255) NOT NULL DEFAULT '' COMMENT '任务描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COMMENT='任务目标';

-- ----------------------------
-- Records of guguo_employee_task_target
-- ----------------------------
INSERT INTO `guguo_employee_task_target` VALUES ('1', '1', '1', '2', '1', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('2', '2', '1', '3', '1', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('6', '6', '3', '500', '2', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('7', '7', '4', '10', '2', '1');
INSERT INTO `guguo_employee_task_target` VALUES ('8', '8', '5', '5', '3', '1');
INSERT INTO `guguo_employee_task_target` VALUES ('9', '9', '6', '3', '3', '1');
INSERT INTO `guguo_employee_task_target` VALUES ('10', '10', '2', '13', '4', '1');
INSERT INTO `guguo_employee_task_target` VALUES ('11', '11', '2', '1', '4', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('16', '15', '2', '10', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('17', '16', '2', '10', '0', '12,2');
INSERT INTO `guguo_employee_task_target` VALUES ('18', '17', '2', '10', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('19', '18', '2', '10', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('20', '19', '2', '10', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('23', '22', '2', '1', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('25', '24', '2', '10', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('26', '25', '2', '10', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('27', '26', '2', '1', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('28', '27', '2', '1', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('29', '28', '2', '1', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('30', '29', '2', '1', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('31', '30', '2', '1', '0', '12');
INSERT INTO `guguo_employee_task_target` VALUES ('32', '31', '2', '1', '0', '2,12');
INSERT INTO `guguo_employee_task_target` VALUES ('33', '32', '2', '1', '0', '3');
INSERT INTO `guguo_employee_task_target` VALUES ('34', '33', '2', '1', '0', '5');
INSERT INTO `guguo_employee_task_target` VALUES ('35', '34', '7', '1', '11', '4');
INSERT INTO `guguo_employee_task_target` VALUES ('37', '36', '7', '1', '11', '5');
INSERT INTO `guguo_employee_task_target` VALUES ('38', '37', '7', '1', '11', '6');
INSERT INTO `guguo_employee_task_target` VALUES ('39', '38', '7', '1', '11', '0');
INSERT INTO `guguo_employee_task_target` VALUES ('40', '39', '7', '1', '11', '0');
INSERT INTO `guguo_employee_task_target` VALUES ('41', '40', '7', '1', '11', '0');
INSERT INTO `guguo_employee_task_target` VALUES ('42', '41', '1', '0', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('48', '47', '1', '0', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('49', '48', '1', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('50', '49', '1', '4', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('51', '50', '1', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('52', '51', '1', '0', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('54', '53', '1', '0', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('55', '54', '2', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('56', '55', '4', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('57', '56', '1', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('58', '57', '1', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('59', '58', '2', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('60', '59', '1', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('61', '60', '1', '2', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('64', '63', '1', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('65', '64', '1', '0', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('66', '65', '1', '2', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('67', '66', '1', '3', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('68', '67', '1', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('69', '68', '1', '0', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('70', '69', '1', '0', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('71', '70', '1', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('72', '71', '1', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('73', '72', '6', '0', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('74', '73', '1', '0', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('75', '74', '2', '100', '0', '孙大鹏');
INSERT INTO `guguo_employee_task_target` VALUES ('76', '75', '6', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('77', '76', '1', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('78', '77', '6', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('79', '78', '6', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('80', '79', '1', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('81', '80', '1', '0', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('82', '81', '1', '1', '0', '');
INSERT INTO `guguo_employee_task_target` VALUES ('83', '82', '1', '0', '0', '');

-- ----------------------------
-- Table structure for guguo_employee_task_tip
-- ----------------------------
DROP TABLE IF EXISTS `guguo_employee_task_tip`;
CREATE TABLE `guguo_employee_task_tip` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL COMMENT '任务id',
  `tip_employee` int(11) NOT NULL COMMENT '打赏员工ID',
  `tip_money` decimal(13,2) unsigned NOT NULL COMMENT '打赏钱数',
  `tip_time` int(10) unsigned NOT NULL COMMENT '打赏时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='任务打赏';

-- ----------------------------
-- Records of guguo_employee_task_tip
-- ----------------------------
INSERT INTO `guguo_employee_task_tip` VALUES ('1', '2', '1', '100.00', '12344');
INSERT INTO `guguo_employee_task_tip` VALUES ('2', '2', '2', '111.00', '123');
INSERT INTO `guguo_employee_task_tip` VALUES ('3', '18', '2', '100.00', '1505113659');
INSERT INTO `guguo_employee_task_tip` VALUES ('4', '18', '2', '100.00', '1505113799');
INSERT INTO `guguo_employee_task_tip` VALUES ('5', '2', '3', '10.00', '1505802872');
INSERT INTO `guguo_employee_task_tip` VALUES ('6', '31', '3', '10.00', '1505803325');
INSERT INTO `guguo_employee_task_tip` VALUES ('7', '32', '3', '10.00', '1505803339');
INSERT INTO `guguo_employee_task_tip` VALUES ('8', '33', '3', '10.00', '1505804363');
INSERT INTO `guguo_employee_task_tip` VALUES ('9', '2', '3', '10.00', '1505805131');
INSERT INTO `guguo_employee_task_tip` VALUES ('10', '36', '3', '10.00', '1505808739');
INSERT INTO `guguo_employee_task_tip` VALUES ('11', '37', '3', '10.00', '1505810137');
INSERT INTO `guguo_employee_task_tip` VALUES ('12', '51', '3', '123.00', '1506063597');
INSERT INTO `guguo_employee_task_tip` VALUES ('13', '51', '3', '1.00', '1506063763');
INSERT INTO `guguo_employee_task_tip` VALUES ('14', '53', '4', '2.00', '1506064184');
INSERT INTO `guguo_employee_task_tip` VALUES ('15', '53', '4', '5.00', '1506066345');
INSERT INTO `guguo_employee_task_tip` VALUES ('16', '51', '3', '2.00', '1506066674');
INSERT INTO `guguo_employee_task_tip` VALUES ('17', '51', '3', '3.00', '1506067448');
INSERT INTO `guguo_employee_task_tip` VALUES ('18', '51', '3', '4.00', '1506067688');
INSERT INTO `guguo_employee_task_tip` VALUES ('19', '56', '3', '1.00', '1506143451');
INSERT INTO `guguo_employee_task_tip` VALUES ('20', '53', '4', '5.00', '1506150507');
INSERT INTO `guguo_employee_task_tip` VALUES ('21', '67', '3', '1.00', '1506153202');
INSERT INTO `guguo_employee_task_tip` VALUES ('22', '72', '4', '2000.00', '1506154997');
INSERT INTO `guguo_employee_task_tip` VALUES ('23', '75', '12', '1.00', '1506305883');
INSERT INTO `guguo_employee_task_tip` VALUES ('24', '74', '4', '6.00', '1506307492');
INSERT INTO `guguo_employee_task_tip` VALUES ('25', '74', '4', '6.00', '1506307518');
INSERT INTO `guguo_employee_task_tip` VALUES ('26', '74', '4', '6.00', '1506307557');
INSERT INTO `guguo_employee_task_tip` VALUES ('27', '74', '4', '5.00', '1506310027');
INSERT INTO `guguo_employee_task_tip` VALUES ('28', '74', '4', '5.00', '1506310315');
INSERT INTO `guguo_employee_task_tip` VALUES ('29', '74', '4', '6.00', '1506310358');
INSERT INTO `guguo_employee_task_tip` VALUES ('30', '74', '4', '5.00', '1506310669');
INSERT INTO `guguo_employee_task_tip` VALUES ('31', '74', '4', '5.00', '1506311452');
INSERT INTO `guguo_employee_task_tip` VALUES ('32', '74', '4', '5.00', '1506311461');
INSERT INTO `guguo_employee_task_tip` VALUES ('33', '74', '4', '5.00', '1506311502');
INSERT INTO `guguo_employee_task_tip` VALUES ('34', '74', '4', '5.00', '1506311826');

-- ----------------------------
-- Table structure for guguo_import_file
-- ----------------------------
DROP TABLE IF EXISTS `guguo_import_file`;
CREATE TABLE `guguo_import_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `type` tinyint(4) unsigned NOT NULL COMMENT '导入类型(1:员工;2:客户;)',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` char(64) NOT NULL DEFAULT '' COMMENT '保存名称',
  `savepath` char(255) NOT NULL DEFAULT '' COMMENT '文件保存路径',
  `ext` char(5) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `location` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件保存位置',
  `create_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_md5` (`md5`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='文件表';

-- ----------------------------
-- Records of guguo_import_file
-- ----------------------------
INSERT INTO `guguo_import_file` VALUES ('18', '1', 'employee.xlsx', '9b256206a40af820926b9e39782ea553.xlsx', '/home/work/workspace/zhuoying/public/uploads/20170520', 'xlsx', 'application/octet-stream', '5342', '33d2a60ae35070f86f5ccf175305bfab', '9d07a36b8107b99dea96933cf8eef67f555dad9b', '0', '1495268759');
INSERT INTO `guguo_import_file` VALUES ('19', '1', 'Employee.xlsx', 'b6972593a978eaf2e9a266f67e68bf52.xlsx', '/home/work/ziliao/workspace/zhuoying/public/webroot/sdzhongxun/import_file/20170713', 'xlsx', 'application/octet-stream', '5251', '6a2e10cb299013722c637d769333ed9c', '3c8809d4f6e3953151bb2824e639b972f45458a4', '0', '1499931107');
INSERT INTO `guguo_import_file` VALUES ('20', '2', 'Customer.xlsx', '8e630e36ff9b7fccdcb6353d2ba1b63e.xlsx', '/home/work/ziliao/workspace/zhuoying/public/webroot/sdzhongxun/import_file/20170908', 'xlsx', 'application/octet-stream', '5632', '2a762b50311ad65bde8b3e572258bbe1', '6b2e5039191910115de73618e34e7716b64f6fd8', '0', '1504830922');
INSERT INTO `guguo_import_file` VALUES ('21', '1', 'Employee.xlsx', '4d059d908a54ee1e00e9c0ff7b3f0755.xlsx', '/home/work/ziliao/workspace/zhuoying/public/webroot/sdzhongxun/import_file/20170713', 'xlsx', 'application/octet-stream', '5340', '2474c2b0daf6a7f8432e6419fe0e88bc', 'dbc26b44165a6becda0567d76c6e370e51c22be6', '0', '1499931487');
INSERT INTO `guguo_import_file` VALUES ('22', '1', 'Employee.xlsx', '6449b667c44232a122ff9d78c02771b4.xlsx', '/home/work/ziliao/workspace/zhuoying/public/webroot/sdzhongxun/import_file/20170908', 'xlsx', 'application/octet-stream', '5367', '80dd33e4c58a23c8915284050624f194', 'fd82a69bb87503d02b6b193c68cd16d7f8256fda', '0', '1504830787');
INSERT INTO `guguo_import_file` VALUES ('23', '2', 'customer.xls', '3ae04a17a9d4d59ca16736ab99cbc308.xls', '/home/work/ziliao/workspace/zhuoying/public/webroot/sdzhongxun/import_file/20170907', 'xls', 'application/vnd.ms-office', '7680', 'fcb67d00c8dade5d7b3c2a7ca250dbfb', '813fce10443d5dfef8a3b6fda138f850cc3c4860', '0', '1504777420');
INSERT INTO `guguo_import_file` VALUES ('24', '2', 'Customer.xlsx', 'd0c96bc1d3c3ce5de76fc6b9a937b12a.xlsx', '/home/work/ziliao/workspace/zhuoying/public/webroot/sdzhongxun/import_file/20170908', 'xlsx', 'application/octet-stream', '5649', 'eb2d9b0609b77a140b108ae987ab5366', 'fcd85f549e1f7ef9ea1504729183a3ec4f4df843', '0', '1504831614');

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
-- Table structure for guguo_my_live_show
-- ----------------------------
DROP TABLE IF EXISTS `guguo_my_live_show`;
CREATE TABLE `guguo_my_live_show` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `show_belongs` tinyint(1) DEFAULT NULL COMMENT '1企业直播2中迅直播',
  `show_id` int(11) DEFAULT NULL COMMENT '视频id',
  `watch_percent` tinyint(3) DEFAULT NULL COMMENT '观看百分比1-100',
  `last_watch_time` int(11) DEFAULT NULL COMMENT '上次观看时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_my_live_show
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_param_remark
-- ----------------------------
DROP TABLE IF EXISTS `guguo_param_remark`;
CREATE TABLE `guguo_param_remark` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自动增量',
  `title` varchar(100) NOT NULL COMMENT '备注名称',
  `add_man` int(10) DEFAULT NULL COMMENT '添加人id,空时为系统备注变量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COMMENT='备注参数表';

-- ----------------------------
-- Records of guguo_param_remark
-- ----------------------------
INSERT INTO `guguo_param_remark` VALUES ('1', '有对对对意向', '0');
INSERT INTO `guguo_param_remark` VALUES ('2', '无意向', '0');
INSERT INTO `guguo_param_remark` VALUES ('4', '个人的标签', '12');
INSERT INTO `guguo_param_remark` VALUES ('8', '存起来', '12');
INSERT INTO `guguo_param_remark` VALUES ('15', '123', '3');
INSERT INTO `guguo_param_remark` VALUES ('16', '对对对', '8');
INSERT INTO `guguo_param_remark` VALUES ('17', '33', '8');

-- ----------------------------
-- Table structure for guguo_picture
-- ----------------------------
DROP TABLE IF EXISTS `guguo_picture`;
CREATE TABLE `guguo_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `category_id` int(255) DEFAULT '0',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `block` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否屏蔽',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `system` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_picture
-- ----------------------------
INSERT INTO `guguo_picture` VALUES ('101', '20170626/7cf1e6ec2c1a11b89cbdd165f692187d.png', '0', '340fac80d7a90dc867935de88afcd411', '7aa0eee8797bd031b1a2ade7224f9f573c832f56', '1498448224', '0', '1', '0');
INSERT INTO `guguo_picture` VALUES ('102', '20170626/d03e81b47e6419039deb914e0b289d16.jpg', '0', 'a79de6e8bed9eb895b47c61f8b853377', '046ce3f0022693adaf0f2ac596d5910b1d05ca6d', '1498448224', '0', '1', '0');
INSERT INTO `guguo_picture` VALUES ('103', '20170626/c4aa679572fc085dec1dcddb055b60b1.jpg', '0', '135f6e5d6c371c7784615598534c3529', '7e385dfef27f0ff8f587988232c1ce029fbb9173', '1498448224', '0', '1', '0');

-- ----------------------------
-- Table structure for guguo_picture_category
-- ----------------------------
DROP TABLE IF EXISTS `guguo_picture_category`;
CREATE TABLE `guguo_picture_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `system` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_picture_category
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_red_envelope
-- ----------------------------
DROP TABLE IF EXISTS `guguo_red_envelope`;
CREATE TABLE `guguo_red_envelope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `redid` varchar(32) NOT NULL COMMENT '红包id',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '红包类型:1运气红包,2普通红包,3任务红包',
  `task_id` int(10) unsigned DEFAULT '0' COMMENT '任务id,任务红包所属任务',
  `fromuser` int(11) DEFAULT NULL COMMENT '谁发出的 用户id,0为系统红包',
  `money` decimal(13,2) unsigned DEFAULT NULL COMMENT '红包金额单位元',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `took_time` int(11) DEFAULT NULL COMMENT '领取时间',
  `is_token` tinyint(1) DEFAULT '0' COMMENT '是否领取0未领取１已领取 2到期未领取返还',
  `took_user` int(11) unsigned DEFAULT '0' COMMENT '领取人',
  `total_money` decimal(13,2) DEFAULT NULL COMMENT '该红包id的红包总金额，单位元',
  `sendback_time` int(11) DEFAULT NULL COMMENT '红包超时返还时间',
  `took_telephone` varchar(16) DEFAULT NULL COMMENT '领取红包人电话',
  `remark` varchar(32) DEFAULT NULL COMMENT '红包说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1429 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_red_envelope
-- ----------------------------
INSERT INTO `guguo_red_envelope` VALUES ('166', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '6.52', '1489450580', '1489457088', '1', '2', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('167', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '5.10', '1489450580', '1489625463', '1', '2', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('168', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '4.78', '1489450580', null, '2', '0', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('169', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '0.43', '1489450580', null, '2', '0', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('170', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '1.23', '1489450580', null, '2', '0', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('171', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '4.25', '1489450580', null, '2', '0', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('172', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '3.93', '1489450580', null, '2', '0', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('173', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '5.56', '1489450580', null, '2', '0', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('174', '5ad78ce5ec2b1f8f58d8efad3bafb966', '0', null, '2', '1.79', '1489450580', null, '2', '0', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('175', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '1.49', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('176', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '31.08', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('177', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '3.20', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('178', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '2.97', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('179', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '2.33', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('180', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '2.39', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('181', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '7.33', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('182', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '2.44', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('183', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '5.08', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('184', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '3.33', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('185', 'f8f58d8efad3bafb9665ad78ce5ec2b1', '0', null, '2', '4.77', '1489450580', null, '2', '0', '100.00', '1489712479', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('197', '442ab7a8b22c2bce02726808e90fcfbe', '0', null, '2', '20.00', '1489470854', null, '2', '0', '20.00', '1489717885', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('198', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '0.77', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('199', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '4.10', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('200', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '5.39', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('201', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '2.60', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('202', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '4.57', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('203', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '18.41', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('204', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '2.68', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('205', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '7.53', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('206', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '8.43', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('207', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '5.44', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('208', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '1.34', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('209', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '4.58', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('210', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '5.02', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('211', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '4.65', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('212', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '4.93', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('213', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '3.11', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('214', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '1.93', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('215', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '3.78', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('216', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '0.01', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('217', '739da40e7543a36121a9809fa4972ba8', '0', null, '2', '10.73', '1489717606', null, '2', '0', '100.00', '1489997257', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('218', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '1.95', '1490062122', '1490062302', '1', '2', '100.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('219', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '3.80', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('220', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '13.94', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('221', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '1.05', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('222', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '5.43', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('223', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '4.81', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('224', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '5.57', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('225', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '13.62', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('226', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '0.81', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('227', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '3.78', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('228', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '3.60', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('229', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '0.01', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('230', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '10.42', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('231', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '8.97', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('232', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '4.74', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('233', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '0.33', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('234', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '5.57', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('235', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '1.81', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('236', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '5.02', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('237', 'd04789de5a98e312abdbb3af65a8dcdb', '0', null, '2', '4.77', '1490062122', null, '2', '0', '100.00', '1490172515', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('252', 'dcbe215fda272c0f9b729698ed78ccda', '0', null, '2', '0.37', '1490076007', null, '2', '0', '12.00', '1490174165', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('253', 'dcbe215fda272c0f9b729698ed78ccda', '0', null, '2', '1.18', '1490076007', null, '2', '0', '12.00', '1490174165', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('254', 'dcbe215fda272c0f9b729698ed78ccda', '0', null, '2', '10.45', '1490076007', null, '2', '0', '12.00', '1490174165', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('255', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.37', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('256', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.45', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('257', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.60', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('258', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.30', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('259', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.09', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('260', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.21', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('261', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '1.24', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('262', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.45', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('263', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.51', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('264', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.05', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('265', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.65', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('266', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.04', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('267', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.55', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('268', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.52', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('269', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.16', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('270', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.73', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('271', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.61', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('272', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '2.09', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('273', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.29', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('274', '7183768935f692d65a7cbd25397f3f69', '0', null, '2', '0.09', '1490584666', null, '2', '0', '10.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('286', '05c99400472df3e8682825e88c0872ca', '0', null, '2', '0.02', '1490584689', null, '2', '0', '0.02', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('287', '135848eecba98bc439c08c1a9dfe81b1', '0', null, '4', '0.02', '1490584849', null, '2', '0', '0.02', '1490684561', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('288', '9a12783cb5decfdd03a2645f3c4e3989', '0', null, '4', '0.02', '1490584988', null, '2', '0', '0.02', '1490684561', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('289', '05c5cd14c1faa479232fdb430929ab98', '0', null, '4', '0.02', '1490585111', null, '2', '0', '0.02', '1490684561', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('290', '8f65301e7f61d9ba98e75b85b9bd3ced', '0', null, '4', '0.02', '1490585404', null, '2', '0', '0.02', '1490684561', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('291', '9a564961823361d0979934e8f0076935', '0', null, '4', '0.20', '1490585671', null, '2', '0', '0.20', '1490684561', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('292', '6bd30e30644f64c10c88ae3cd9791dfd', '0', null, '4', '0.02', '1490585840', null, '2', '0', '0.02', '1490684561', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('293', '77541853996dbcea3f4b4e3e4869c187', '0', null, '4', '3.70', '1490662516', null, '2', '0', '3.70', '1490845254', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('294', '6e4f3d6a70ed7c55027f413d07af62f8', '0', null, '4', '2.00', '1490684655', null, '2', '0', '2.00', '1490845254', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('295', 'ed25adba623d4d9cd3bf3bb41abf8736', '0', null, '5', '3.00', '1490685800', null, '2', '0', '3.00', '1490833452', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('296', 'cdd6623176916559998e39277659cffb', '0', null, '5', '2.00', '1490686752', null, '2', '0', '2.00', '1490833452', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('297', '95462ae8a3355b21e8b0afbd513999a7', '0', null, '4', '2.30', '1490845543', null, '2', '0', '4.00', '1491439587', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('298', '95462ae8a3355b21e8b0afbd513999a7', '0', null, '4', '1.70', '1490845543', null, '2', '0', '4.00', '1491439587', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('300', '18c2e18c85afbac7109752963dee80f4', '0', null, '4', '1.48', '1490853290', '1490860553', '1', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('301', '18c2e18c85afbac7109752963dee80f4', '0', null, '4', '0.52', '1490853290', null, '2', '0', '2.00', '1491439587', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('303', '50ecf37359e85d75b5dc983ce5721844', '0', null, '4', '0.23', '1490853589', '1490860661', '1', '4', '3.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('304', '50ecf37359e85d75b5dc983ce5721844', '0', null, '4', '2.77', '1490853589', null, '2', '0', '3.00', '1491439587', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('306', 'e9ed58f7b358ac035360dd4f36537d72', '0', null, '4', '0.23', '1490853939', '1490860570', '1', '4', '0.40', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('307', 'e9ed58f7b358ac035360dd4f36537d72', '0', null, '4', '0.17', '1490853939', null, '2', '0', '0.40', '1491439587', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('309', 'ecf451512843cf43c28d4457c9d21093', '0', null, '2', '6.00', '1490855639', null, '2', '0', '30.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('310', 'ecf451512843cf43c28d4457c9d21093', '0', null, '2', '6.00', '1490855639', null, '2', '0', '30.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('311', 'ecf451512843cf43c28d4457c9d21093', '0', null, '2', '6.00', '1490855639', null, '2', '0', '30.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('312', 'ecf451512843cf43c28d4457c9d21093', '0', null, '2', '6.00', '1490855639', null, '2', '0', '30.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('313', 'ecf451512843cf43c28d4457c9d21093', '0', null, '2', '6.00', '1490855639', null, '2', '0', '30.00', '1491443574', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('314', 'ebc03ebb968f7733f4c6c594524e7f1b', '0', null, '5', '4.20', '1491464496', '1491464721', '1', '5', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('315', 'ebc03ebb968f7733f4c6c594524e7f1b', '0', null, '5', '0.31', '1491464496', null, '2', '0', '5.00', '1491550975', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('316', 'ebc03ebb968f7733f4c6c594524e7f1b', '0', null, '5', '0.49', '1491464496', null, '2', '0', '5.00', '1491550975', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('317', '260b729ee11f94fc6011228b2bd21cb5', '0', null, '5', '0.69', '1491466204', '1491468932', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('318', '260b729ee11f94fc6011228b2bd21cb5', '0', null, '5', '1.31', '1491466204', null, '2', '0', '2.00', '1491552690', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('320', '09cccea9eb6f3e2a6188b004871f1d29', '0', null, '5', '0.49', '1491466219', '1491466426', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('321', '09cccea9eb6f3e2a6188b004871f1d29', '0', null, '5', '1.51', '1491466219', null, '2', '0', '2.00', '1491552690', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('323', '013b1391b25c903df35c7c9729971fbe', '0', null, '5', '0.50', '1491467948', '1491467999', '1', '5', '0.50', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('324', 'a54a1ca002edf2b89804b19f2ab3dae3', '0', null, '5', '1.40', '1491469080', '1491469086', '1', '5', '1.40', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('325', '5ea61be7599fe190a9a209f3623725c7', '0', null, '5', '0.76', '1491549987', '1491550002', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('326', '5ea61be7599fe190a9a209f3623725c7', '0', null, '5', '1.24', '1491549987', '1491553076', '1', '2', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('328', '18aa3432c71e730ec0fc55c04691524f', '0', null, '5', '1.00', '1491550043', '1491550046', '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('329', '017dafe9ad9891cecf84f2f5ad775277', '0', null, '5', '1.00', '1491550256', '1491550260', '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('330', '6cc1082558705f3dc1cfa942a8ba483f', '0', null, '5', '1.11', '1491550712', '1491550715', '1', '5', '1.11', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('331', 'd7492618fbb18edf5241d170750399f9', '0', null, '5', '1.00', '1491550956', '1491550959', '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('332', '5d1058530021b34dc7eeb787e279b3fe', '0', null, '5', '2.00', '1491551749', '1491551752', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('333', '9ddda8aca8a543516abb009c6e36a2df', '0', null, '5', '1.00', '1491552986', null, '2', '0', '1.00', '1491803902', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('334', 'dd19c363c91bf091e887ffc0e0e50699', '0', null, '2', '1.98', '1491553222', '1491553233', '1', '2', '30.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('335', 'dd19c363c91bf091e887ffc0e0e50699', '0', null, '2', '1.64', '1491553222', null, '2', '0', '30.00', '1491873508', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('336', 'dd19c363c91bf091e887ffc0e0e50699', '0', null, '2', '8.46', '1491553222', null, '2', '0', '30.00', '1491873508', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('337', 'dd19c363c91bf091e887ffc0e0e50699', '0', null, '2', '4.79', '1491553222', null, '2', '0', '30.00', '1491873508', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('338', 'dd19c363c91bf091e887ffc0e0e50699', '0', null, '2', '8.63', '1491553222', null, '2', '0', '30.00', '1491873508', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('339', 'dd19c363c91bf091e887ffc0e0e50699', '0', null, '2', '4.50', '1491553222', null, '2', '0', '30.00', '1491873508', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('340', '1713e59306b68fc5599688bc61da66af', '0', null, '5', '1.00', '1491610826', '1491610830', '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('341', 'cb7b491e0f97709e6671fae6beb9d50c', '0', null, '5', '0.66', '1491611253', '1491611256', '1', '5', '0.66', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('342', 'd40d709008ee6a22e243f3e8bef2cf14', '0', null, '5', '2.00', '1491611347', '1491611356', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('343', '54a5cfce2186156169711baa0d042a28', '0', null, '5', '0.47', '1491611448', '1491611452', '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('344', '54a5cfce2186156169711baa0d042a28', '0', null, '5', '0.53', '1491611448', null, '2', '0', '1.00', '1491803902', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('346', '7f89589b52095eb1628b0bbea5fe7be0', '0', null, '5', '1.11', '1491612393', '1491612396', '1', '5', '1.11', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('347', 'a363bed7a5f5e6e80209ba2d260d0b89', '0', null, '5', '0.11', '1491612519', '1491612533', '1', '5', '0.11', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('348', '57d4be36a7b144598ae18094220fb480', '0', null, '4', '1.00', '1491621785', '1491621790', '1', '4', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('349', 'c98924a4c0b69de8d142a76b7fbe7460', '0', null, '4', '1.66', '1491787593', '1491790913', '1', '4', '1.66', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('350', 'ba615fa22331b9762c60b8245fb8f09b', '0', null, '4', '1.66', '1491787594', '1491790931', '1', '4', '1.66', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('351', '33847e4c69efdcdf9ea9a390d9be24e5', '0', null, '4', '1.66', '1491787594', '1491790968', '1', '4', '1.66', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('352', '67e87eaaef96b530cc8a41a45e0949f7', '0', null, '4', '1.00', '1491791184', '1491795264', '1', '4', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('353', '2a9980f99138e2103ddf698b0d4b0931', '0', null, '4', '0.81', '1491795407', '1491795427', '1', '4', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('354', '2a9980f99138e2103ddf698b0d4b0931', '0', null, '4', '0.19', '1491795407', '1491803908', '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('356', 'c3b0766ef9366d29d3aca5ebebe84ec8', '0', null, '4', '1.11', '1491795652', '1491795656', '1', '4', '1.11', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('357', '4f139db5a2ad16e0d9f9c7f340720e21', '0', null, '5', '1.13', '1491804661', '1491804688', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('358', '4f139db5a2ad16e0d9f9c7f340720e21', '0', null, '5', '0.87', '1491804661', '1491804690', '1', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('360', '10101417da24c53feaeab339f6366fbb', '0', null, '5', '0.71', '1491804662', '1491804714', '1', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('361', '10101417da24c53feaeab339f6366fbb', '0', null, '5', '1.29', '1491804662', '1491804730', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('363', 'cd33a40a4b107e358853e5e2f4ab4745', '0', null, '5', '1.32', '1491804752', '1491804757', '1', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('364', 'cd33a40a4b107e358853e5e2f4ab4745', '0', null, '5', '0.68', '1491804752', '1491873446', '1', '3', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('366', '9b583fd16a9ea46f856c0b78b693f644', '0', null, '5', '0.25', '1491804793', '1491804803', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('367', '9b583fd16a9ea46f856c0b78b693f644', '0', null, '5', '1.75', '1491804793', '1491804803', '1', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('369', '8d041403568e11070e1b27fa58a8dc83', '0', null, '5', '0.65', '1491804833', '1491804839', '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('370', '8d041403568e11070e1b27fa58a8dc83', '0', null, '5', '0.35', '1491804833', '1491804839', '1', '4', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('372', '7c138efb0f1a608f078779edfed89b14', '0', null, '4', '1.29', '1491804865', '1491804873', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('373', '7c138efb0f1a608f078779edfed89b14', '0', null, '4', '0.71', '1491804865', '1491804873', '1', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('375', 'f475ca47305e382460bca6a557e1195e', '0', null, '5', '8.64', '1491805412', '1491805428', '1', '4', '8.88', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('376', 'f475ca47305e382460bca6a557e1195e', '0', null, '5', '0.24', '1491805412', '1491805446', '1', '5', '8.88', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('378', 'fadd94fef348f708c463e9f5c33d70dd', '0', null, '5', '0.56', '1491805469', '1491805476', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('379', 'fadd94fef348f708c463e9f5c33d70dd', '0', null, '5', '1.44', '1491805469', '1491805497', '1', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('381', '15d2bb29d2118a69f6cf9ab1ce4c8d17', '0', null, '5', '0.55', '1491805615', '1491805621', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('382', '15d2bb29d2118a69f6cf9ab1ce4c8d17', '0', null, '5', '1.45', '1491805615', '1491805633', '1', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('384', '462742d702cfb4732ed6d4876f7d2c78', '0', null, '5', '1.16', '1491805820', '1491805823', '1', '5', '3.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('385', '462742d702cfb4732ed6d4876f7d2c78', '0', null, '5', '1.84', '1491805820', '1491805850', '1', '4', '3.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('387', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '1.56', '1491808778', '1491814842', '1', '2', '6.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('388', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '0.30', '1491808778', '1491872656', '1', '5', '6.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('389', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '0.39', '1491808778', '1491872833', '1', '5', '6.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('390', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '0.32', '1491808778', '1491872851', '1', '2', '6.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('391', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '0.67', '1491808778', '1491872854', '1', '5', '6.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('392', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '0.20', '1491808778', '1491872857', '1', '2', '6.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('393', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '0.11', '1491808778', null, '2', '0', '6.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('394', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '0.61', '1491808778', null, '2', '0', '6.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('395', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '0.61', '1491808778', null, '2', '0', '6.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('396', '073fb2d1baaa766ab8b77cbf3e4884ef', '0', null, '2', '1.23', '1491808778', null, '2', '0', '6.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('402', '07b443941acee2d8b1fff0b532744725', '0', null, '2', '1.92', '1491811135', null, '2', '0', '6.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('403', '07b443941acee2d8b1fff0b532744725', '0', null, '2', '3.51', '1491811135', null, '2', '0', '6.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('404', '07b443941acee2d8b1fff0b532744725', '0', null, '2', '0.57', '1491811135', null, '2', '0', '6.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('405', 'a285c4ec7d1e027416857ae456a1da95', '0', null, '2', '0.45', '1491872957', '1491872967', '1', '2', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('406', 'a285c4ec7d1e027416857ae456a1da95', '0', null, '2', '0.78', '1491872957', '1491872973', '1', '5', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('407', 'a285c4ec7d1e027416857ae456a1da95', '0', null, '2', '1.82', '1491872957', '1491873366', '1', '4', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('408', 'a285c4ec7d1e027416857ae456a1da95', '0', null, '2', '1.56', '1491872957', '1491873417', '1', '3', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('409', 'a285c4ec7d1e027416857ae456a1da95', '0', null, '2', '0.39', '1491872957', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('412', 'd7635125f04186e55265c7f7310aa540', '0', null, '4', '243.03', '1491874162', '1491874172', '1', '5', '400.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('413', 'd7635125f04186e55265c7f7310aa540', '0', null, '4', '156.97', '1491874162', '1491874172', '1', '4', '400.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('415', '805c81e5a526b799d41d36e1b2c25000', '0', null, '5', '109.61', '1491874227', '1491874318', '1', '4', '300.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('416', '805c81e5a526b799d41d36e1b2c25000', '0', null, '5', '107.93', '1491874227', '1491874319', '1', '5', '300.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('417', '805c81e5a526b799d41d36e1b2c25000', '0', null, '5', '82.46', '1491874227', '1491874325', '1', '2', '300.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('418', '2848e54fa0735890885dd9478585e766', '0', null, '5', '38.70', '1491874400', '1491874572', '1', '2', '300.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('419', '2848e54fa0735890885dd9478585e766', '0', null, '5', '98.35', '1491874400', '1491874572', '1', '5', '300.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('420', '2848e54fa0735890885dd9478585e766', '0', null, '5', '162.95', '1491874400', '1491874777', '1', '4', '300.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('421', '7617e18eb918482decb64e1c82b2f15f', '0', null, '5', '2.97', '1491874984', '1491880600', '1', '2', '88.88', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('422', '7617e18eb918482decb64e1c82b2f15f', '0', null, '5', '59.54', '1491874984', '1491880733', '1', '2', '88.88', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('423', '7617e18eb918482decb64e1c82b2f15f', '0', null, '5', '26.37', '1491874984', null, '2', '0', '88.88', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('424', 'd5fd7e2ba46f551358cf82518f6f02b0', '0', null, '4', '0.02', '1491875053', '1491875057', '1', '4', '0.02', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('425', '8c36387b61c5b090a11ab3e2f16ede07', '0', null, '2', '0.01', '1491875297', '1491875305', '1', '2', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('426', '8c36387b61c5b090a11ab3e2f16ede07', '0', null, '2', '0.07', '1491875297', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('427', '8c36387b61c5b090a11ab3e2f16ede07', '0', null, '2', '4.53', '1491875297', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('428', '8c36387b61c5b090a11ab3e2f16ede07', '0', null, '2', '0.10', '1491875297', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('429', '8c36387b61c5b090a11ab3e2f16ede07', '0', null, '2', '0.29', '1491875297', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('432', 'a7314807d75f9a1b681d7177a0fbca3a', '0', null, '5', '3.04', '1491876431', '1491876469', '1', '1', '8.88', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('433', 'a7314807d75f9a1b681d7177a0fbca3a', '0', null, '5', '1.46', '1491876431', '1491876469', '1', '5', '8.88', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('434', 'a7314807d75f9a1b681d7177a0fbca3a', '0', null, '5', '2.37', '1491876431', '1491881144', '1', '2', '8.88', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('435', 'a7314807d75f9a1b681d7177a0fbca3a', '0', null, '5', '2.01', '1491876431', null, '2', '0', '8.88', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('439', '5962b26bd01dd33c64f837d201413858', '0', null, '5', '1.39', '1491876598', '1491877162', '1', '5', '7.77', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('440', '5962b26bd01dd33c64f837d201413858', '0', null, '5', '5.33', '1491876598', null, '2', '0', '7.77', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('441', '5962b26bd01dd33c64f837d201413858', '0', null, '5', '1.05', '1491876598', null, '2', '0', '7.77', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('442', 'd664d1a679ff5465781bb92acfffeac1', '0', null, '5', '1.15', '1491876600', '1491876701', '1', '4', '7.77', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('443', 'd664d1a679ff5465781bb92acfffeac1', '0', null, '5', '5.95', '1491876600', '1491876701', '1', '5', '7.77', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('444', 'd664d1a679ff5465781bb92acfffeac1', '0', null, '5', '0.67', '1491876600', '1491880764', '1', '2', '7.77', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('445', '28c503ef00b600ad47b6455652c9f281', '0', null, '5', '2.00', '1491877334', '1491880814', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('446', '1ba59f899a454a70c52622afdb634030', '0', null, '5', '2.00', '1491880839', '1491880843', '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('447', 'a34dec22f0894e8e7314b4c8ce06fdbd', '0', null, '5', '1.20', '1491881047', '1491881050', '1', '5', '1.20', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('448', 'd367ce77e93dab1ff25f3b4f65f81a98', '0', null, '5', '10.43', '1491881595', null, '2', '0', '44.44', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('449', 'd367ce77e93dab1ff25f3b4f65f81a98', '0', null, '5', '0.45', '1491881595', null, '2', '0', '44.44', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('450', 'd367ce77e93dab1ff25f3b4f65f81a98', '0', null, '5', '25.08', '1491881595', null, '2', '0', '44.44', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('451', 'd367ce77e93dab1ff25f3b4f65f81a98', '0', null, '5', '8.48', '1491881595', null, '2', '0', '44.44', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('455', 'd103a9d1b72c13e469e02fa7121f49c1', '0', null, '5', '0.41', '1491881807', '1491881906', '1', '3', '4.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('456', 'd103a9d1b72c13e469e02fa7121f49c1', '0', null, '5', '1.23', '1491881807', '1491881907', '1', '4', '4.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('457', 'd103a9d1b72c13e469e02fa7121f49c1', '0', null, '5', '0.31', '1491881807', '1491881924', '1', '5', '4.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('458', 'd103a9d1b72c13e469e02fa7121f49c1', '0', null, '5', '2.05', '1491881807', '1491881934', '1', '2', '4.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('462', '6c519e8995f8183515ec190508d8a1e5', '0', null, '5', '2.83', '1491881975', '1491881996', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('463', '6c519e8995f8183515ec190508d8a1e5', '0', null, '5', '1.25', '1491881975', '1491882016', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('464', '6c519e8995f8183515ec190508d8a1e5', '0', null, '5', '0.45', '1491881975', '1491882046', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('465', '6c519e8995f8183515ec190508d8a1e5', '0', null, '5', '0.47', '1491881975', null, '2', '0', '5.00', '1491969241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('469', 'cbe1745b61e09492c02eb3f94b699a32', '0', null, '2', '0.05', '1491882322', '1491882483', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('470', 'cbe1745b61e09492c02eb3f94b699a32', '0', null, '2', '0.41', '1491882322', '1491882500', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('471', 'cbe1745b61e09492c02eb3f94b699a32', '0', null, '2', '4.15', '1491882322', '1491882501', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('472', 'cbe1745b61e09492c02eb3f94b699a32', '0', null, '2', '0.14', '1491882322', '1491889877', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('473', 'cbe1745b61e09492c02eb3f94b699a32', '0', null, '2', '0.25', '1491882322', '1491889926', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('474', 'd90ed818193c7217ef92c341cea56da5', '0', null, '2', '1.47', '1491889945', '1491890061', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('475', 'd90ed818193c7217ef92c341cea56da5', '0', null, '2', '1.41', '1491889945', '1491890197', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('476', 'd90ed818193c7217ef92c341cea56da5', '0', null, '2', '0.88', '1491889945', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('477', 'd90ed818193c7217ef92c341cea56da5', '0', null, '2', '0.96', '1491889945', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('478', 'd90ed818193c7217ef92c341cea56da5', '0', null, '2', '0.28', '1491889945', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('481', '17f31aea6fd0bc297589e8ce6d7a5184', '0', null, '2', '1.56', '1491893007', '1491893483', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('482', '17f31aea6fd0bc297589e8ce6d7a5184', '0', null, '2', '0.68', '1491893007', '1491893439', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('483', '17f31aea6fd0bc297589e8ce6d7a5184', '0', null, '2', '0.75', '1491893007', '1491893763', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('484', '17f31aea6fd0bc297589e8ce6d7a5184', '0', null, '2', '1.57', '1491893007', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('485', '17f31aea6fd0bc297589e8ce6d7a5184', '0', null, '2', '0.44', '1491893007', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('488', '90af1ea53697c7eac38e8a75f649886b', '0', null, '2', '2.55', '1491893812', '1491893853', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('489', '90af1ea53697c7eac38e8a75f649886b', '0', null, '2', '0.32', '1491893812', '1491893853', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('490', '90af1ea53697c7eac38e8a75f649886b', '0', null, '2', '1.20', '1491893812', '1491894341', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('491', '90af1ea53697c7eac38e8a75f649886b', '0', null, '2', '0.45', '1491893812', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('492', '90af1ea53697c7eac38e8a75f649886b', '0', null, '2', '0.48', '1491893812', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('495', '4958c3502453946581fda0d1b008703f', '0', null, '2', '0.77', '1491894460', '1491897224', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('496', '4958c3502453946581fda0d1b008703f', '0', null, '2', '2.17', '1491894460', '1491897385', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('497', '4958c3502453946581fda0d1b008703f', '0', null, '2', '0.64', '1491894460', '1491897558', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('498', '4958c3502453946581fda0d1b008703f', '0', null, '2', '0.34', '1491894460', '1491897676', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('499', '4958c3502453946581fda0d1b008703f', '0', null, '2', '1.08', '1491894460', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('502', 'ddd1073b869262838853992b5d306253', '0', null, '2', '0.10', '1491897706', '1491897714', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('503', 'ddd1073b869262838853992b5d306253', '0', null, '2', '0.11', '1491897706', '1491897759', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('504', 'ddd1073b869262838853992b5d306253', '0', null, '2', '1.15', '1491897706', '1491897810', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('505', 'ddd1073b869262838853992b5d306253', '0', null, '2', '2.52', '1491897706', '1491897876', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('506', 'ddd1073b869262838853992b5d306253', '0', null, '2', '1.12', '1491897706', '1491897967', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('509', '64868c929d9f53d06e9d4be7b4263ef6', '0', null, '2', '0.22', '1491898016', '1491898023', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('510', '64868c929d9f53d06e9d4be7b4263ef6', '0', null, '2', '0.21', '1491898016', '1491898023', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('511', '64868c929d9f53d06e9d4be7b4263ef6', '0', null, '2', '1.89', '1491898016', '1491898023', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('512', '64868c929d9f53d06e9d4be7b4263ef6', '0', null, '2', '1.30', '1491898016', '1491898023', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('513', '64868c929d9f53d06e9d4be7b4263ef6', '0', null, '2', '1.38', '1491898016', '1491898023', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('516', '1c88c25ee367ad6d98a6d25eb3bbeb15', '0', null, '2', '1.22', '1491899402', '1491899411', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('517', '1c88c25ee367ad6d98a6d25eb3bbeb15', '0', null, '2', '1.30', '1491899402', '1491899412', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('518', '1c88c25ee367ad6d98a6d25eb3bbeb15', '0', null, '2', '0.90', '1491899402', '1491899412', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('519', '1c88c25ee367ad6d98a6d25eb3bbeb15', '0', null, '2', '0.91', '1491899402', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('520', '1c88c25ee367ad6d98a6d25eb3bbeb15', '0', null, '2', '0.67', '1491899402', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('523', '086c5ea0bb0c8a6bcbf48d78e558681e', '0', null, '2', '1.11', '1491899485', '1491899493', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('524', '086c5ea0bb0c8a6bcbf48d78e558681e', '0', null, '2', '0.21', '1491899485', '1491899493', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('525', '086c5ea0bb0c8a6bcbf48d78e558681e', '0', null, '2', '3.15', '1491899485', '1491899494', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('526', '086c5ea0bb0c8a6bcbf48d78e558681e', '0', null, '2', '0.45', '1491899485', '1491899494', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('527', '086c5ea0bb0c8a6bcbf48d78e558681e', '0', null, '2', '0.08', '1491899485', '1491901085', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('530', '2fd8fd449524e6a6d681cbd1267120a0', '0', null, '2', '0.30', '1491901135', '1491901144', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('531', '2fd8fd449524e6a6d681cbd1267120a0', '0', null, '2', '1.06', '1491901135', '1491901145', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('532', '2fd8fd449524e6a6d681cbd1267120a0', '0', null, '2', '0.30', '1491901135', '1491901145', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('533', '2fd8fd449524e6a6d681cbd1267120a0', '0', null, '2', '0.64', '1491901135', '1491901145', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('534', '2fd8fd449524e6a6d681cbd1267120a0', '0', null, '2', '2.70', '1491901135', '1491901199', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('537', '20794abdd1f3d744f9616a428583a641', '0', null, '2', '0.44', '1491901215', '1491901222', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('538', '20794abdd1f3d744f9616a428583a641', '0', null, '2', '1.31', '1491901215', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('539', '20794abdd1f3d744f9616a428583a641', '0', null, '2', '1.55', '1491901215', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('540', '20794abdd1f3d744f9616a428583a641', '0', null, '2', '1.01', '1491901215', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('541', '20794abdd1f3d744f9616a428583a641', '0', null, '2', '0.69', '1491901215', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('544', 'c9799336fa8817ca101abfe6c3cdbb0f', '0', null, '2', '2.57', '1491901464', '1491901472', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('545', 'c9799336fa8817ca101abfe6c3cdbb0f', '0', null, '2', '1.18', '1491901464', '1491901472', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('546', 'c9799336fa8817ca101abfe6c3cdbb0f', '0', null, '2', '0.73', '1491901464', '1491901472', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('547', 'c9799336fa8817ca101abfe6c3cdbb0f', '0', null, '2', '0.39', '1491901464', '1491901633', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('548', 'c9799336fa8817ca101abfe6c3cdbb0f', '0', null, '2', '0.13', '1491901464', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('551', 'f07be964f3292e1b27b80e7c7b931f06', '0', null, '2', '2.00', '1491901736', '1491901817', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('552', 'f07be964f3292e1b27b80e7c7b931f06', '0', null, '2', '0.40', '1491901736', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('553', 'f07be964f3292e1b27b80e7c7b931f06', '0', null, '2', '0.41', '1491901736', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('554', 'f07be964f3292e1b27b80e7c7b931f06', '0', null, '2', '0.29', '1491901736', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('555', 'f07be964f3292e1b27b80e7c7b931f06', '0', null, '2', '1.90', '1491901736', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('558', 'eeb75f766db8a60ceb9a3c2adfa6634a', '0', null, '2', '1.87', '1491902102', '1491902109', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('559', 'eeb75f766db8a60ceb9a3c2adfa6634a', '0', null, '2', '1.11', '1491902102', '1491902168', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('560', 'eeb75f766db8a60ceb9a3c2adfa6634a', '0', null, '2', '0.63', '1491902102', '1491902168', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('561', 'eeb75f766db8a60ceb9a3c2adfa6634a', '0', null, '2', '0.21', '1491902102', '1491902169', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('562', 'eeb75f766db8a60ceb9a3c2adfa6634a', '0', null, '2', '1.18', '1491902102', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('565', '788d0d6e2045aa47d13f09c0d1bdc3b9', '0', null, '2', '1.45', '1491902234', '1491902240', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('566', '788d0d6e2045aa47d13f09c0d1bdc3b9', '0', null, '2', '0.62', '1491902234', '1491902240', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('567', '788d0d6e2045aa47d13f09c0d1bdc3b9', '0', null, '2', '0.32', '1491902234', '1491902240', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('568', '788d0d6e2045aa47d13f09c0d1bdc3b9', '0', null, '2', '2.41', '1491902234', '1491902240', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('569', '788d0d6e2045aa47d13f09c0d1bdc3b9', '0', null, '2', '0.20', '1491902234', '1491902240', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('572', '80880c22b526c70f6ba3f22e70662d51', '0', null, '2', '1.24', '1491902665', '1491902671', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('573', '80880c22b526c70f6ba3f22e70662d51', '0', null, '2', '0.15', '1491902665', '1491902672', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('574', '80880c22b526c70f6ba3f22e70662d51', '0', null, '2', '0.15', '1491902665', '1491902672', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('575', '80880c22b526c70f6ba3f22e70662d51', '0', null, '2', '3.33', '1491902665', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('576', '80880c22b526c70f6ba3f22e70662d51', '0', null, '2', '0.13', '1491902665', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('579', '885f66e5521fbbb2dfbed5f3065854a0', '0', null, '2', '0.65', '1491902895', '1491902901', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('580', '885f66e5521fbbb2dfbed5f3065854a0', '0', null, '2', '1.15', '1491902895', '1491902902', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('581', '885f66e5521fbbb2dfbed5f3065854a0', '0', null, '2', '0.45', '1491902895', '1491902902', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('582', '885f66e5521fbbb2dfbed5f3065854a0', '0', null, '2', '2.05', '1491902895', '1491902902', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('583', '885f66e5521fbbb2dfbed5f3065854a0', '0', null, '2', '0.70', '1491902895', '1491902902', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('586', '8ce33936c7945d8b67ade6bb745ef7b5', '0', null, '2', '1.25', '1491903097', '1491903103', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('587', '8ce33936c7945d8b67ade6bb745ef7b5', '0', null, '2', '0.85', '1491903097', '1491903103', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('588', '8ce33936c7945d8b67ade6bb745ef7b5', '0', null, '2', '0.38', '1491903097', '1491903103', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('589', '8ce33936c7945d8b67ade6bb745ef7b5', '0', null, '2', '1.29', '1491903097', '1491903104', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('590', '8ce33936c7945d8b67ade6bb745ef7b5', '0', null, '2', '1.23', '1491903097', '1491903104', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('591', '7862e05c7452cf3b31b400d5a198ca4c', '0', null, '2', '1.86', '1491956468', '1491957810', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('592', '7862e05c7452cf3b31b400d5a198ca4c', '0', null, '2', '0.84', '1491956468', '1491958329', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('593', '7862e05c7452cf3b31b400d5a198ca4c', '0', null, '2', '0.30', '1491956468', '1491958329', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('594', '7862e05c7452cf3b31b400d5a198ca4c', '0', null, '2', '0.90', '1491956468', '1491958329', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('595', '7862e05c7452cf3b31b400d5a198ca4c', '0', null, '2', '1.10', '1491956468', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('598', 'bda757dc5ac03b04bd7c1b56e9d8652c', '0', null, '5', '1.58', '1491958326', '1491958331', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('599', 'bda757dc5ac03b04bd7c1b56e9d8652c', '0', null, '5', '0.42', '1491958326', '1491959657', '1', '4', '2.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('601', '4826bb566ae35ccd9387c5564d85ae4a', '0', null, '2', '0.71', '1491958360', '1491958366', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('602', '4826bb566ae35ccd9387c5564d85ae4a', '0', null, '2', '0.98', '1491958360', '1491958366', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('603', '4826bb566ae35ccd9387c5564d85ae4a', '0', null, '2', '2.16', '1491958360', '1491958366', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('604', '4826bb566ae35ccd9387c5564d85ae4a', '0', null, '2', '0.38', '1491958360', '1491958366', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('605', '4826bb566ae35ccd9387c5564d85ae4a', '0', null, '2', '0.77', '1491958360', '1491958366', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('608', 'ebb2dfc5863b2f803df78074a1c28bef', '0', null, '2', '2.32', '1491958487', '1491958790', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('609', 'ebb2dfc5863b2f803df78074a1c28bef', '0', null, '2', '0.87', '1491958487', '1491958790', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('610', 'ebb2dfc5863b2f803df78074a1c28bef', '0', null, '2', '0.94', '1491958487', '1491958791', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('611', 'ebb2dfc5863b2f803df78074a1c28bef', '0', null, '2', '0.75', '1491958487', '1491958792', '1', '3', '5.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('612', 'ebb2dfc5863b2f803df78074a1c28bef', '0', null, '2', '0.12', '1491958487', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('615', 'e6a588c50c159590c256cc7dba3cef25', '0', null, '2', '1.06', '1491958820', '1491958871', '1', '4', '5.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('616', 'e6a588c50c159590c256cc7dba3cef25', '0', null, '2', '1.99', '1491958820', '1491958912', '1', '2', '5.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('617', 'e6a588c50c159590c256cc7dba3cef25', '0', null, '2', '0.23', '1491958820', '1491958913', '1', '1', '5.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('618', 'e6a588c50c159590c256cc7dba3cef25', '0', null, '2', '1.38', '1491958820', '1491958913', '1', '5', '5.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('619', 'e6a588c50c159590c256cc7dba3cef25', '0', null, '2', '0.34', '1491958820', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('622', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '0.46', '1491962013', '1491962150', '1', '5', '11.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('623', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '0.46', '1491962013', '1491962249', '1', '2', '11.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('624', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '1.10', '1491962013', '1491962249', '1', '4', '11.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('625', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '0.69', '1491962013', '1491962250', '1', '1', '11.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('626', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '1.33', '1491962013', '1491962299', '1', '3', '11.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('627', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '0.69', '1491962013', '1491962993', '1', '11', '11.00', null, '13311119999', null);
INSERT INTO `guguo_red_envelope` VALUES ('628', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '1.47', '1491962013', null, '2', '0', '11.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('629', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '0.62', '1491962013', null, '2', '0', '11.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('630', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '2.42', '1491962013', null, '2', '0', '11.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('631', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '0.54', '1491962013', null, '2', '0', '11.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('632', '8088dadd7a9606ea3085ed05f7780ca6', '0', null, '2', '1.22', '1491962013', null, '2', '0', '11.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('637', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '1.64', '1491962854', '1491962959', '1', '1', '11.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('638', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '1.51', '1491962854', '1491962959', '1', '9', '11.00', null, '13311116666', null);
INSERT INTO `guguo_red_envelope` VALUES ('639', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '0.49', '1491962854', '1491962959', '1', '4', '11.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('640', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '0.38', '1491962854', '1491962960', '1', '7', '11.00', null, '13311113333', null);
INSERT INTO `guguo_red_envelope` VALUES ('641', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '0.20', '1491962854', '1491962960', '1', '2', '11.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('642', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '0.22', '1491962854', '1491962960', '1', '5', '11.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('643', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '2.88', '1491962854', '1491962997', '1', '8', '11.00', null, '13311115555', null);
INSERT INTO `guguo_red_envelope` VALUES ('644', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '0.72', '1491962854', '1491963009', '1', '6', '11.00', null, '13311111111', null);
INSERT INTO `guguo_red_envelope` VALUES ('645', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '1.35', '1491962854', '1491963032', '1', '3', '11.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('646', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '1.54', '1491962854', null, '2', '0', '11.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('647', 'c300b162fab97cbc762c70f36bcbd675', '0', null, '2', '0.07', '1491962854', null, '2', '0', '11.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('652', 'e5a3c763b0fc74403d461c3ca395ed1e', '0', null, '5', '1.13', '1491963094', '1491968957', '1', '5', '3.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('653', 'e5a3c763b0fc74403d461c3ca395ed1e', '0', null, '5', '0.76', '1491963094', null, '2', '0', '3.00', '1492070458', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('654', 'e5a3c763b0fc74403d461c3ca395ed1e', '0', null, '5', '1.11', '1491963094', null, '2', '0', '3.00', '1492070458', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('655', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '0.09', '1491963195', '1491963241', '1', '1', '11.00', null, '13322223333', null);
INSERT INTO `guguo_red_envelope` VALUES ('656', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '2.41', '1491963195', '1491963242', '1', '3', '11.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('657', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '0.46', '1491963195', '1491963242', '1', '2', '11.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('658', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '0.20', '1491963195', '1491963243', '1', '7', '11.00', null, '13311113333', null);
INSERT INTO `guguo_red_envelope` VALUES ('659', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '1.57', '1491963195', '1491963243', '1', '4', '11.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('660', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '1.13', '1491963195', '1491963243', '1', '8', '11.00', null, '13311115555', null);
INSERT INTO `guguo_red_envelope` VALUES ('661', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '0.72', '1491963195', '1491963243', '1', '6', '11.00', null, '13311111111', null);
INSERT INTO `guguo_red_envelope` VALUES ('662', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '0.72', '1491963195', '1491963243', '1', '5', '11.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('663', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '1.64', '1491963195', '1491963243', '1', '11', '11.00', null, '13311119999', null);
INSERT INTO `guguo_red_envelope` VALUES ('664', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '0.99', '1491963195', '1491963243', '1', '9', '11.00', null, '13311116666', null);
INSERT INTO `guguo_red_envelope` VALUES ('665', 'e3f2391f78aa24e14da8238c9853a964', '0', null, '2', '1.07', '1491963195', '1491963243', '1', '10', '11.00', null, '13311118888', null);
INSERT INTO `guguo_red_envelope` VALUES ('666', '4d87640339340c7b2eda0f4286c06ed9', '0', null, '5', '0.91', '1492480867', '1492480877', '1', '5', '1.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('667', '4d87640339340c7b2eda0f4286c06ed9', '0', null, '5', '0.09', '1492480867', '1492504882', '1', '2', '1.00', null, '13322221111', null);
INSERT INTO `guguo_red_envelope` VALUES ('668', '06d586ec8817dc09eae465fcf6d94bef', '0', null, '5', '1.00', '1498531369', null, '2', '0', '1.00', '1498634619', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('669', '16453721fcfcd5038f1e3eaaf1b1d15a', '0', null, '5', '7.00', '1498531615', null, '2', '0', '7.00', '1498634619', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('670', '49e28367f3fbf77a824462572ccd5118', '0', null, '5', '2.00', '1498531800', null, '2', '0', '2.00', '1498634619', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('671', '86263cfc284197f3146998e0bc21840c', '0', null, '4', '1.00', '1498531842', null, '2', '0', '1.00', '1498705215', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('672', 'a58269f5faaa4edbe3a6dce3bfc2714b', '0', null, '5', '1.00', '1498531946', null, '2', '0', '1.00', '1498634619', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('673', 'e557f4f1923599009b1d1035d0ce4e8c', '0', null, '6', '2.30', '1498533196', null, '2', '0', '2.30', '1500253726', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('674', 'f9bc4fdad676d71becaa4745706f4fa4', '0', null, '6', '3.22', '1498533296', null, '2', '0', '3.22', '1500253726', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('675', 'aa1d01f7fccbf178637a2dd831876339', '0', null, '6', '2.90', '1498533710', null, '2', '0', '2.90', '1498715662', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('676', 'f8b4e3366d522ceacb51f4d2c70e68a9', '0', null, '5', '0.45', '1498533932', null, '2', '0', '0.45', '1498634619', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('677', '190178265845e723e46f155b4cfc4035', '0', null, '5', '3.33', '1498546078', null, '2', '0', '3.33', '1498634619', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('678', '4cf555ee4c5bf90a3f317b60a6394d9b', '0', null, '5', '1.00', '1498550604', null, '2', '0', '1.00', '1498637152', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('679', '01071f206b1b76bb1102bfc01b6fa42b', '0', null, '5', '4.00', '1498550621', null, '2', '0', '4.00', '1498637152', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('680', 'efe966943bac0aa2e884298a71fd5a5d', '0', null, '5', '5.00', '1498550631', null, '2', '0', '5.00', '1498637152', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('681', 'cc8613e04b7b31ff6b6540b6e551ae81', '0', null, '5', '6.00', '1498550640', null, '2', '0', '6.00', '1498637152', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('682', '3da78c5ef0d1ec94a0c933ea86be0280', '0', null, '5', '1.00', '1498633474', null, '2', '0', '1.00', '1498720477', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('683', 'b7301ee449b8c2fc979ffd2816599e45', '0', null, '5', '0.28', '1498638700', null, '2', '0', '1.00', '1498728300', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('684', 'b7301ee449b8c2fc979ffd2816599e45', '0', null, '5', '0.72', '1498638700', null, '2', '0', '1.00', '1498728300', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('686', '422fde98c3d5e81431f3cda89908f2fa', '0', null, '5', '0.81', '1498639631', null, '2', '0', '1.00', '1498728300', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('687', '422fde98c3d5e81431f3cda89908f2fa', '0', null, '5', '0.19', '1498639631', null, '2', '0', '1.00', '1498728300', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('689', '4366372067ab192b30c21346b0f8599a', '0', null, '5', '38.00', '1498641996', null, '2', '0', '38.00', '1498788378', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('690', 'eba567a5555f097e276bd635fef6e612', '0', null, '5', '2.00', '1498700946', null, '2', '0', '2.00', '1498788378', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('691', '97c88ef6f0db84bb5454a385b15b0cbe', '0', null, '5', '5.00', '1498701636', null, '2', '0', '5.00', '1498788378', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('692', '59e3c692c775f2680c8c3fbb74948542', '0', null, '5', '3.33', '1498725667', null, '2', '0', '3.33', '1498871844', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('693', '7c1f162ca0e7d254797beb2e7e815c0e', '0', null, '3', '4.44', '1498725736', null, '2', '0', '4.44', '1500448127', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('694', 'afac1a180c9264ee9c97b5f742ba3788', '0', null, '3', '0.12', '1500514151', null, '1', '3', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('695', 'afac1a180c9264ee9c97b5f742ba3788', '0', null, '3', '0.18', '1500514151', null, '2', '0', '1.00', '1501209858', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('696', 'afac1a180c9264ee9c97b5f742ba3788', '0', null, '3', '0.39', '1500514151', null, '2', '0', '1.00', '1501209858', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('697', 'afac1a180c9264ee9c97b5f742ba3788', '0', null, '3', '0.21', '1500514151', null, '2', '0', '1.00', '1501209858', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('698', 'afac1a180c9264ee9c97b5f742ba3788', '0', null, '3', '0.10', '1500514151', null, '2', '0', '1.00', '1501209858', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('701', '4667a259b4a35f6bb8abe351859fcf23', '0', null, '3', '0.24', '1500518695', null, '1', '3', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('702', '4667a259b4a35f6bb8abe351859fcf23', '0', null, '3', '0.16', '1500518695', null, '2', '0', '1.00', '1501138932', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('703', '4667a259b4a35f6bb8abe351859fcf23', '0', null, '3', '0.07', '1500518695', null, '2', '0', '1.00', '1501138932', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('704', '4667a259b4a35f6bb8abe351859fcf23', '0', null, '3', '0.40', '1500518695', null, '2', '0', '1.00', '1501138932', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('705', '4667a259b4a35f6bb8abe351859fcf23', '0', null, '3', '0.13', '1500518695', null, '2', '0', '1.00', '1501138932', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('708', '35cf358e2884e0bced92a6a847f16867', '0', null, '5', '1.00', '1500519427', null, '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('709', 'ea342d6082ccb010438e642659d7a977', '0', null, '6', '344.00', '1500519557', null, '1', '5', '344.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('710', 'fbb56c867171576a8a48ffc830e6d7a5', '0', null, '6', '1.00', '1500519747', null, '1', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('711', '5c92fa9d6130b5a87a2b7792acad9ec1', '0', null, '6', '2.00', '1500519762', null, '1', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('712', '4d1ff8fa16794abde48e30aab90cd6d0', '0', null, '6', '3.00', '1500519778', null, '1', '5', '3.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('713', '5a2b661ed0beecb6e70c8342ff2fefc7', '0', null, '5', '1.03', '1500521118', null, '1', '6', '3.55', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('714', '5a2b661ed0beecb6e70c8342ff2fefc7', '0', null, '5', '0.95', '1500521118', null, '1', '5', '3.55', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('715', '5a2b661ed0beecb6e70c8342ff2fefc7', '0', null, '5', '1.57', '1500521118', null, '2', '0', '3.55', '1500609674', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('716', '590dc59012faeef7344a8d4d5271f677', '0', null, '5', '3.49', '1500521136', null, '1', '5', '4.66', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('717', '590dc59012faeef7344a8d4d5271f677', '0', null, '5', '0.20', '1500521136', null, '1', '6', '4.66', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('718', '590dc59012faeef7344a8d4d5271f677', '0', null, '5', '0.97', '1500521136', null, '2', '0', '4.66', '1500609674', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('719', 'f2a6f8f3448cf4328593e0cf509b71da', '0', null, '3', '1.00', '1500599275', null, '1', '3', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('720', '50610cd2ab897a451096596cda5cdee3', '0', null, '3', '1.00', '1500599442', null, '1', '3', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('721', 'c7e550323f73f1fcfea50df58563fe94', '0', null, '7', '1.00', '1501032455', null, '2', '0', '1.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('722', 'e1df28cc4670da0677bf8fddcae4c793', '0', null, '7', '2.00', '1501032510', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('723', '9f5d814dc8987aa982ad84d2192fc8e7', '0', null, '7', '3.00', '1501032748', null, '2', '0', '3.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('724', '2a1d52d5632ee1f2eba49b834970c7d3', '0', null, '10', '2.00', '1501033349', null, '2', '0', '2.00', '1501125473', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('725', '94031d5b322d90d58d6d5b4033190ad0', '0', null, '10', '15.20', '1501051227', null, '2', '0', '15.20', '1501138371', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('726', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '1.78', '1501053447', null, '1', '10', '14.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('727', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '0.03', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('728', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '1.41', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('729', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '2.39', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('730', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '0.63', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('731', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '3.40', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('732', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '2.41', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('733', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '0.15', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('734', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '1.02', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('735', '67b7f9de5e4f1ac88c9228ff28fcdf9a', '0', null, '10', '0.78', '1501053447', null, '2', '0', '14.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('741', '1dff30a95443a3281291ec444c42ec1d', '0', null, '10', '0.13', '1501053507', null, '1', '10', '20.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('742', '1dff30a95443a3281291ec444c42ec1d', '0', null, '10', '19.87', '1501053507', null, '2', '0', '20.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('744', '36939065da60d8e1a6b97cfd0c9ebff6', '0', null, '10', '9.00', '1501057869', null, '1', '10', '18.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('745', '36939065da60d8e1a6b97cfd0c9ebff6', '0', null, '10', '9.00', '1501057869', null, '2', '0', '18.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('747', '2ae203e58d0cd554241c40b53fa37bfa', '0', null, '10', '0.48', '1501061676', null, '1', '10', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('748', '2ae203e58d0cd554241c40b53fa37bfa', '0', null, '10', '0.97', '1501061676', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('749', '2ae203e58d0cd554241c40b53fa37bfa', '0', null, '10', '0.55', '1501061676', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('750', '18be064e71becd7357ca8f294e87c6a6', '0', null, '10', '2.35', '1501114253', '1501147158', '1', '10', '5.00', null, '13311118888', null);
INSERT INTO `guguo_red_envelope` VALUES ('751', '18be064e71becd7357ca8f294e87c6a6', '0', null, '10', '2.65', '1501114253', null, '2', '0', '5.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('753', '36390ffd22dd2c058d9e2af48393db25', '0', null, '10', '2.00', '1501114345', null, '2', '0', '4.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('754', '36390ffd22dd2c058d9e2af48393db25', '0', null, '10', '2.00', '1501114345', null, '2', '0', '4.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('756', '9600552780ae6e9af9f6fc42c2355c8f', '0', null, '10', '1.76', '1501114594', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('757', '9600552780ae6e9af9f6fc42c2355c8f', '0', null, '10', '0.24', '1501114594', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('759', 'fdd2594219d6874eeb884ea0f017ee60', '0', null, '10', '0.18', '1501114893', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('760', 'fdd2594219d6874eeb884ea0f017ee60', '0', null, '10', '1.82', '1501114893', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('762', '31576aba523ad3ec7a9f7662ae6908a0', '0', null, '10', '0.67', '1501114916', null, '2', '0', '1.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('763', '31576aba523ad3ec7a9f7662ae6908a0', '0', null, '10', '0.33', '1501114916', null, '2', '0', '1.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('765', 'efc412a8e7247cfd96f4e482df616207', '0', null, '10', '1.00', '1501114990', null, '2', '0', '1.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('766', '397d5b94aa61b2393447ed92a9cbbceb', '0', null, '10', '0.56', '1501115227', null, '2', '0', '1.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('767', '397d5b94aa61b2393447ed92a9cbbceb', '0', null, '10', '0.44', '1501115227', null, '2', '0', '1.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('769', '70320b65f2f783ac9464a1194093bdc6', '0', null, '10', '1.08', '1501115268', '1501147526', '1', '10', '2.00', null, '13311118888', null);
INSERT INTO `guguo_red_envelope` VALUES ('770', '70320b65f2f783ac9464a1194093bdc6', '0', null, '10', '0.92', '1501115268', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('772', 'd8571824a180f992824ddf88ae06e496', '0', null, '10', '1.69', '1501115737', '1501147116', '1', '10', '2.00', null, '13311118888', null);
INSERT INTO `guguo_red_envelope` VALUES ('773', 'd8571824a180f992824ddf88ae06e496', '0', null, '10', '0.31', '1501115737', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('775', 'cffd1f1eac0d79c87345f310d3e22612', '0', null, '10', '1.00', '1501115792', null, '1', '10', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('776', '3e3ea05695de894f3b68435f9ddb1b86', '0', null, '10', '1.00', '1501116274', null, '2', '0', '1.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('777', '48f38e1c29194dc2840c18d0cfb379a1', '0', null, '10', '1.82', '1501118194', '1501139824', '1', '10', '2.00', null, '13311118888', null);
INSERT INTO `guguo_red_envelope` VALUES ('778', '48f38e1c29194dc2840c18d0cfb379a1', '0', null, '10', '0.18', '1501118194', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('780', '31b3a0c2709258ea9b95b3789f861bc5', '0', null, '10', '0.99', '1501138642', '1501139814', '1', '10', '1.00', null, '13311118888', null);
INSERT INTO `guguo_red_envelope` VALUES ('781', '31b3a0c2709258ea9b95b3789f861bc5', '0', null, '10', '0.01', '1501138642', '1501148487', '1', '5', '1.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('783', '70d7ec597053def64bd40842f2c6abfd', '0', null, '10', '0.09', '1501138959', '1501139635', '1', '3', '1.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('784', '70d7ec597053def64bd40842f2c6abfd', '0', null, '10', '0.91', '1501138959', '1501139807', '1', '10', '1.00', null, '13311118888', null);
INSERT INTO `guguo_red_envelope` VALUES ('786', 'f4508458a03dcd6527feebe8c071540d', '0', null, '10', '0.24', '1501147978', '1501147994', '1', '10', '2.00', null, '13311118888', null);
INSERT INTO `guguo_red_envelope` VALUES ('787', 'f4508458a03dcd6527feebe8c071540d', '0', null, '10', '1.76', '1501147978', '1501148452', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('789', 'c28acbfd0d46aaf6d73a8d2fa955d079', '0', null, '5', '2.00', '1501202405', null, '2', '0', '2.00', '1501289245', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('790', '8a99a3b91847b27a4ec1a0a757da51fa', '0', null, '5', '333.00', '1501210846', null, '2', '0', '333.00', '1501298735', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('791', '522d95326aac14886e76e147822375b0', '0', null, '5', '20.00', '1501229303', null, '2', '0', '20.00', '1501468330', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('792', '97985ba052fcaece0bfb2266929fb5b3', '0', null, '5', '2.00', '1501288579', '1501288589', '1', '5', '6.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('793', '97985ba052fcaece0bfb2266929fb5b3', '0', null, '5', '2.00', '1501288579', null, '2', '0', '6.00', '1501468330', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('794', '97985ba052fcaece0bfb2266929fb5b3', '0', null, '5', '2.00', '1501288579', null, '2', '0', '6.00', '1501468330', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('795', 'b48e27d278272b08df052373dcef50fd', '0', null, '5', '2.00', '1501298728', null, '2', '0', '2.00', '1501468330', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('796', '92e645954f1de2034f2cb4090750e35f', '0', null, '5', '5.00', '1501469404', null, '2', '0', '5.00', '1501576524', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('797', 'fa4f60459591359375634ed2cb3cc5cf', '0', null, '5', '2.00', '1501471575', '1501471581', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('798', 'a44b462306abff8995a88dd5df80dddc', '0', null, '5', '2.00', '1501548637', null, '2', '0', '2.00', '1501640617', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('799', '9ac3e5487c2fe30f903ea4000ec1857e', '0', null, '5', '2.00', '1501576620', null, '2', '0', '2.00', '1501665736', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('800', 'fdd5cba8dea43a27769bac17dfb5e566', '0', null, '5', '0.21', '1501576699', '1501576706', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('801', 'fdd5cba8dea43a27769bac17dfb5e566', '0', null, '5', '0.93', '1501576699', null, '2', '0', '2.00', '1501665736', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('802', 'fdd5cba8dea43a27769bac17dfb5e566', '0', null, '5', '0.86', '1501576699', null, '2', '0', '2.00', '1501665736', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('803', '7e541eeb08d85fdd5e7f363a117a5298', '0', null, '9', '2.00', '1501577312', null, '2', '0', '2.00', '1503546297', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('804', 'def8d267ff18f190c5f3316733cd8dd2', '0', null, '5', '2.00', '1502325566', null, '2', '0', '2.00', '1502412656', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('805', '42d13a57e5982e38aaa8a41da6bdb7c7', '0', null, '3', '1.00', '1503556784', null, '2', '0', '1.00', '1503557401', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('806', '0ff6c06544f75a4e20c0f81f3ae7ca5f', '0', null, '3', '1.00', '1503556910', null, '2', '0', '1.00', '1503557521', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('807', 'a8837e0f0b8426195724e00762a5ada5', '0', null, '3', '1.00', '1503557039', null, '2', '0', '1.00', '1503557641', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('808', 'e1c493b5d5523874c8688cb1dc3d8407', '0', null, '5', '100.00', '1503558970', null, '2', '0', '100.00', '1503559622', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('809', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', '1503624803', '1', '5', '18.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('810', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', null, '2', '0', '18.00', '1503625441', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('811', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', null, '2', '0', '18.00', '1503625441', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('812', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', null, '2', '0', '18.00', '1503625441', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('813', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', null, '2', '0', '18.00', '1503625441', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('814', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', null, '2', '0', '18.00', '1503625441', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('815', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', null, '2', '0', '18.00', '1503625441', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('816', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', null, '2', '0', '18.00', '1503625441', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('817', '304f12bce169439939e53b0902d4eba3', '0', null, '5', '2.00', '1503624797', null, '2', '0', '18.00', '1503625441', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('824', 'f1b773a8685310c84fe852dd5bdec0e6', '0', null, '5', '20.00', '1503705343', null, '2', '0', '20.00', '1503705962', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('825', '71ebd926b55de1359157db996c0d2f6f', '0', null, '5', '100.00', '1504137842', null, '2', '0', '100.00', '1504138501', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('826', '2b28c46963dc69fda435201827346851', '0', null, '5', '100.00', '1504139981', '1504140293', '1', '4', '100.00', null, '13322225555', null);
INSERT INTO `guguo_red_envelope` VALUES ('827', '313191578be1de978b09ae0957cc04d6', '0', null, '5', '2.00', '1504228616', '1504228626', '1', '6', '2.00', null, '13311111111', null);
INSERT INTO `guguo_red_envelope` VALUES ('828', '76aad358ef5a8ffbab9edbaae4c21d88', '0', null, '5', '2.00', '1504228656', '1504228675', '1', '6', '2.00', null, '13311111111', null);
INSERT INTO `guguo_red_envelope` VALUES ('829', '7492bbbfea10c33168d6de93853fc38e', '0', null, '5', '2.00', '1504228711', null, '2', '0', '2.00', '1504229341', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('830', '1ae3304b6dd6811acd4753410f8f82db', '0', null, '5', '2.00', '1504228767', null, '2', '0', '2.00', '1504229401', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('831', 'f36f20b6a420a9e45b4fffce539cd08a', '0', null, '5', '1.99', '1504247268', '1504247273', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('832', 'f36f20b6a420a9e45b4fffce539cd08a', '0', null, '5', '0.01', '1504247268', null, '2', '0', '2.00', '1504247881', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('834', '51ac4d5103d055071c08e912f7616621', '0', null, '5', '0.88', '1504247311', '1504247656', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('835', '51ac4d5103d055071c08e912f7616621', '0', null, '5', '1.12', '1504247311', null, '2', '0', '2.00', '1504247941', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('837', 'b89868c7b6702043da4446858c5fbf2e', '0', null, '5', '2.00', '1504247746', null, '2', '0', '2.00', '1504248361', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('838', 'cc0e9eb36081ee936b79cc2e113bee1a', '0', null, '5', '2.00', '1504247769', '1504247778', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('839', '9b2cb72bad6494c47a05f1d359864703', '0', null, '5', '2.00', '1504247877', null, '2', '0', '2.00', '1504248481', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('840', '0b23df7d757938a18ee4295d0f3ae8e1', '0', null, '5', '2.00', '1504248813', '1504248819', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('841', 'fc239063f0a00142d27d7f0ee7841ef8', '0', null, '5', '2.00', '1504249024', '1504249029', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('842', '4250e83f815cc4e1ace973b41fdce953', '0', null, '5', '2.00', '1504249220', '1504249226', '1', '5', '2.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('843', '7a546b8a43ac267d69bae24fccb6801e', '0', null, '5', '2.00', '1504249322', null, '2', '0', '2.00', '1504249981', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('844', 'a0f6fe7cd664e2c0f3dfc6860b2c940e', '0', null, '5', '1.00', '1504579223', null, '2', '0', '1.00', '1504579862', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('845', '301e9dc7e161f87d1e359076118614e6', '0', null, '5', '1.00', '1504579229', null, '2', '0', '1.00', '1504579862', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('846', '0afa85a5721c0c2b01315d837902a9d7', '0', null, '5', '1.00', '1504579257', null, '2', '0', '1.00', '1504579862', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('847', '9d3e445bc36b5475126c600418c17ae1', '0', null, '5', '1.00', '1504586592', null, '2', '0', '1.00', '1504587241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('848', '8da435dcf0b1c6d00431e0a14609a05f', '0', null, '5', '1.00', '1504586598', null, '2', '0', '1.00', '1504587241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('849', 'd67d72b987f59e093b942442dab3f1f7', '0', null, '5', '2.00', '1504592499', null, '2', '0', '2.00', '1504593121', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('850', '513176b36aa1b46bdb2798640e51d374', '0', null, '5', '1.00', '1504592561', null, '2', '0', '1.00', '1504593181', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('851', 'c56d1c9ca673f8021d3d759f66ba0141', '0', null, '5', '1.00', '1504592611', null, '2', '0', '1.00', '1504593241', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('852', 'c5918abdd2b342192188f6417e221b59', '0', null, '3', '1.00', '1504592855', null, '2', '0', '1.00', '1504593481', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('853', '2b3d80dd8eb629d2b24ed42b12c00716', '0', null, '3', '1.00', '1504593121', null, '2', '0', '1.00', '1504593781', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('854', '734ea7f06e5f66cc22c780aa253b52e2', '0', null, '5', '2.00', '1504593444', null, '2', '0', '2.00', '1504594081', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('855', '25e7e4b78f543bffdf00052d8a3f0c72', '0', null, '5', '1.00', '1504593471', '1504593477', '1', '5', '1.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('856', '4d3d13f7449944a999b74f974b9cefce', '0', null, '5', '1.00', '1504594128', null, '2', '0', '1.00', '1504594741', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('857', 'c6a77a738038ceaf5ac140557d15b997', '0', null, '5', '1.00', '1504594862', null, '2', '0', '1.00', '1504595521', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('858', '53939814cacb04e10ef9e00ced456301', '0', null, '5', '1.00', '1504596270', null, '2', '0', '1.00', '1504596901', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('859', '976eb08f75fc5cf1e58caa97dca878da', '0', null, '5', '1.00', '1504596893', null, '2', '0', '1.00', '1504597501', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('860', 'b6df11f9e7cda60b559cfc48bedc0b51', '0', null, '5', '1.00', '1504597550', '1504597560', '1', '5', '1.00', null, '13322226666', null);
INSERT INTO `guguo_red_envelope` VALUES ('861', 'efadcf6a08ba1eabaceb3663f500db08', '3', '18', '3', '1.00', '1505115702', null, '0', '3', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('862', '86b65bef6714b74866c721cf0cf5243d', '3', '6', '0', '10.00', '1505273153', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('863', 'ad74dba590df96d18f4c541a21c2f060', '3', '6', '0', '10.00', '1505273153', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('864', '8b9c33518d41a6f8c5a9dee607385b72', '3', '6', '0', '10.00', '1505273153', null, '0', '9', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('865', '91cf816bf4f6ac09e31f50e4ab3e1572', '3', '8', '0', '10.00', '1505273260', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('866', '671222df65ba1cc8c9e5c29d8bb3b834', '3', '8', '0', '10.00', '1505273260', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('868', 'ec072df14da0337189e5948e11808352', '3', '9', '0', '10.00', '1505273260', null, '0', '2', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('869', '84ccbb3b21c0848c69263e415e2d5399', '3', '9', '0', '10.00', '1505273260', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('870', '1e84c713a41f73409fa13b30994ce174', '3', '9', '0', '10.00', '1505273260', null, '0', '5', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('871', 'e9a905dc8d58de70c4a08ebc9db1db22', '3', '9', '0', '10.00', '1505273260', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('872', '3ba96ace7cd4a2d6ac060ffa8f8e4ad5', '3', '9', '0', '10.00', '1505273260', null, '0', '6', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('875', '8a3d99150ae0152c07db5c518d075a88', '3', '10', '0', '10.00', '1505273260', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('876', '0691550db4f9818bc37747d26fe93275', '3', '10', '0', '10.00', '1505273260', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('878', 'bc9ecd7477bef74422e42e78df0f07d3', '3', '15', '0', '2.00', '1505273260', null, '0', '3', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('879', '9fd1b081a035db6fba5f5164c78a125f', '3', '15', '0', '1.00', '1505273260', null, '0', '8', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('880', '70ccc19603a029c0ba0d64519e9c8b4f', '3', '15', '0', '1.00', '1505273260', null, '0', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('881', 'fb903e99f756c6897d3b39a0110a1db5', '3', '16', '0', '2.00', '1505273260', null, '0', '3', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('882', 'e36ed1a1d424c3cce21a71b923251513', '3', '16', '0', '1.00', '1505273260', null, '0', '8', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('883', '5b52140afd225b0169700a2f9294bbc7', '3', '16', '0', '1.00', '1505273260', null, '0', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('884', 'b503325fcecb2a2fb5e37c16fa118b66', '3', '17', '0', '2.00', '1505273282', null, '0', '3', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('885', '78101e45bdaf9d2d367217656cb320b2', '3', '17', '0', '1.00', '1505273282', null, '0', '8', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('886', '11ffc878d13982e3b09ce7413748c49d', '3', '17', '0', '1.00', '1505273282', null, '0', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('887', '4361333f595618dae623570cd1911628', '3', '18', '0', '1.00', '1505273447', null, '0', '8', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('888', 'ab0f91c1208ce8fb5e617a74472e4e7a', '3', '18', '0', '1.00', '1505273447', null, '0', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('890', 'bce29adf719c01cf9c6542be1901a6cf', '0', '0', '4', '100.00', '1505443963', null, '2', '0', '100.00', '1505444581', null, null);
INSERT INTO `guguo_red_envelope` VALUES ('898', 'a9ae661e0ff0616cb7eed0af0f18eb0e', '3', '19', '0', '2.00', '1505791982', null, '0', '3', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('899', 'ce95ac339e60c49ddff671d75f4ba2d3', '3', '19', '0', '1.00', '1505791982', null, '0', '8', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('900', 'ae2d31d7e5a5176a0be5e41173d57354', '3', '19', '0', '1.00', '1505791982', null, '0', '5', '1.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('901', '6b3e051a15335dc41d6b2264969890e1', '3', '19', '0', '0.00', '1505791982', null, '0', '3', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('902', '46165cc7f19a722adf77274be5a44776', '3', '19', '0', '0.00', '1505791982', null, '0', '8', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('903', '70ea96f6d31723f2cf1d237ac1233b13', '3', '19', '0', '0.00', '1505791982', null, '0', '5', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('905', 'c0f311a7ec267e2b6cbdfedf9075a2b1', '3', '22', '0', '2.00', '1505791982', '1506136937', '1', '3', '2.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('906', 'df55cac06751c0d2bd4009c0f21555b8', '3', '22', '0', '1.00', '1505791982', '1506143161', '1', '3', '0.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('908', 'bacf34cd0bfc301a5485f716caabe9bb', '3', '24', '0', '5.00', '1505791982', null, '0', '3', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('909', '093d79207c36cc116c048d553939fdd9', '3', '24', '0', '5.00', '1505791982', null, '0', '8', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('910', '9f23fbda8f8c32595b4d0602a3292858', '3', '24', '0', '0.00', '1505791982', null, '0', '3', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('911', 'e2315d5074842c1851f0cea8e199c008', '3', '24', '0', '0.00', '1505791982', null, '0', '8', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('915', 'aa2c06cb6a9f7232f1c2900812b2f7d6', '3', '25', '0', '10.00', '1505791982', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('916', '4067af44c14ece9efe21c9c1b968e8e5', '3', '25', '0', '0.00', '1505791982', null, '0', '3', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('918', '50b50a058e6c8ed5355925b775ab5656', '3', '26', '0', '10.00', '1505791982', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('919', 'ca4f6a6cef14fb3a2e44811cc1e2c639', '3', '26', '0', '10.00', '1505791982', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('920', '99daa8a2e3eb25c2eea487e521e03179', '3', '26', '0', '0.00', '1505791982', null, '0', '3', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('921', 'e2b6e0ad9c9058407b0a769dc741f2ea', '3', '26', '0', '0.00', '1505791982', null, '0', '8', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('925', 'ff31227fb6a194af82c4e95b74a560d3', '3', '27', '0', '10.00', '1505791982', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('926', 'a38fa8d14844286a810c813982f99602', '3', '27', '0', '10.00', '1505791982', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('927', 'af4d400849747ca555ad4b6a92a294bc', '3', '27', '0', '0.00', '1505791982', null, '0', '3', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('928', '47770bfbb8fe57caecdf378920aa06d1', '3', '27', '0', '0.00', '1505791982', null, '0', '8', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('932', '47324ebfd664bb11fed50eaff9bf2a65', '3', '28', '0', '10.00', '1505800871', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('933', 'dfc253ef4ef69ccddeb39a0d8499e6cc', '3', '28', '0', '10.00', '1505800871', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('934', '74c09e17b9c72f258b61ad22069ea0f6', '3', '28', '0', '0.00', '1505800871', null, '0', '3', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('935', '708c9e0dc959c98e8bb4f36b38574881', '3', '28', '0', '0.00', '1505800871', null, '0', '8', '0.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('942', '7b0a24fa52b246ea69b833d602e5ff4a', '3', '29', '0', '10.00', '1505801510', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('943', '1a695ed97f2b79d1a024fdf609007dce', '3', '29', '0', '10.00', '1505801510', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('945', 'b3e1753fd86740d36a6ce0e23dba2441', '3', '30', '0', '10.00', '1505801563', null, '0', '3', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('946', 'a9c022ca39e109e2025dd07ce222a8da', '3', '30', '0', '10.00', '1505801563', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('948', '6d0dfd9c4326d89f7b2eb60f6efd14f7', '3', '31', '0', '10.00', '1505803221', '1506557727', '1', '3', '10.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('949', 'c79e69ecd6394e497fc89dd9b9fde7b9', '3', '31', '0', '10.00', '1505803221', null, '0', '8', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('950', '63d7e763798447f53f2041c85dc9b9f4', '3', '31', '0', '5.00', '1505803221', null, '0', '3', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('951', '884644e0a42ee5decc828ada27a3872c', '3', '31', '0', '5.00', '1505803221', null, '0', '8', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('955', 'a64137b36fb11325371f79b8d18a4bf3', '3', '32', '0', '2.00', '1505803636', '1506136799', '1', '3', '2.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('956', 'c348fc01fcabb7d4754db945d8e99e41', '3', '32', '0', '10.00', '1505803636', '1506136843', '1', '3', '10.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('957', '613a1ce230bb59f685297673794fb939', '3', '32', '0', '10.00', '1505803636', null, '0', '5', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('958', '2553d8dea0caacb1efe5acc76343f21a', '3', '33', '0', '2.00', '1505804584', '1506136617', '1', '3', '2.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('959', '359ab00515acd6ea79d871d1dbbf7a99', '3', '33', '0', '10.00', '1505804584', '1506136794', '1', '3', '10.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('960', '98046474e488c84775a34feef513a227', '3', '33', '0', '10.00', '1505804584', null, '0', '5', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('961', '14a36293e708eb280f821b291048e270', '3', '34', '0', '2.00', '1505808237', null, '0', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('962', '384add9513c0415b088de3f4dee50967', '3', '34', '0', '10.00', '1505808237', '1506133467', '1', '3', '10.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('964', 'f9dc8fff9b6e5fefc7fbb6ff3601c6fb', '3', '36', '0', '2.00', '1505808779', null, '0', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('965', '4dc4e8b7fe32ac10a510b0075192de91', '3', '36', '0', '10.00', '1505808779', null, '0', '5', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('967', 'b14acdbd0133d2df5cd6f1f17f2731f1', '3', '37', '0', '2.00', '1505810275', null, '0', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('968', 'dee7f61978c6fed4041d832d6d4f3c21', '3', '37', '0', '2.00', '1505810275', null, '0', '2', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('969', '83b2d74ae5f8845137762668701de5b8', '3', '37', '0', '5.00', '1505810275', null, '0', '5', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('970', 'e95fb0eab81af9a92afeb252e72d1ecc', '3', '37', '0', '5.00', '1505810275', null, '0', '2', '5.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('974', 'e861195db8b769f6b381cdd12b193012', '0', '0', '5', '200.00', '1506132894', '1506132993', '1', '3', '200.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('1379', '0ada39cf72f7f86188115261320f7e38', '3', '1', '0', '0.50', '1506147421', null, '0', '5', '0.50', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1380', '4b269ed1ec81d3b826a2195937529c33', '3', '2', '0', '2.00', '1506147421', null, '0', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1381', '9fd17639d6c7398cfdd1ed6a8a668ce5', '3', '11', '0', '10.00', '1506147421', '1506302653', '1', '3', '10.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('1382', '516fa6f016d507075194628880897dfe', '3', '11', '0', '10.00', '1506147421', '1506302848', '1', '8', '10.00', null, '13311115555', null);
INSERT INTO `guguo_red_envelope` VALUES ('1383', '386991e703ab399b6bf889069451c7d9', '3', '56', '0', '3.00', '1506158684', '1506484541', '1', '3', '3.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('1384', 'f2ff087dc98b9d337ddf3ff06eb04048', '3', '56', '0', '3.00', '1506158684', null, '0', '5', '3.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1385', '37e5b1d053367ce88298f0954cc8ab6e', '3', '56', '0', '0.50', '1506158684', null, '0', '3', '0.50', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1386', '75409e2e4d000ffe5de7575b567e689a', '3', '56', '0', '0.50', '1506158684', null, '0', '5', '0.50', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1390', 'b36375f42b01758bcbe75f435382d64b', '3', '57', '0', '10.00', '1506158684', null, '0', '5', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1391', '22f8f36bd3646c3dc1edcfbfb7776296', '3', '57', '0', '3.33', '1506158684', '1506472662', '1', '3', '3.33', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('1392', 'f70f84bcdf3964e45f50b90afd3b45cf', '3', '57', '0', '3.33', '1506158684', null, '0', '2', '3.33', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1393', 'b9fe786483074a2d30b522b6f177ba87', '3', '72', '0', '10.00', '1506158853', '1506159074', '1', '3', '10.00', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('1394', '50b5b8383c752ceb20d0fb06e102b3ac', '3', '72', '0', '10.00', '1506158853', null, '0', '5', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1395', 'd51174ab2dd5d58172bb06f3dd91b975', '3', '72', '0', '10.00', '1506158853', null, '0', '4', '10.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1396', '15587185a03acdac9577ef22bf06ad09', '3', '72', '0', '666.68', '1506158853', '1506301404', '1', '3', '666.68', null, '13311112222', null);
INSERT INTO `guguo_red_envelope` VALUES ('1397', '8be6f4cbd633b1d08655b71f91336bba', '3', '72', '0', '666.66', '1506158853', null, '0', '5', '666.66', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1398', '033c34d2dba512c4926cb17efbebc5d7', '3', '72', '0', '666.66', '1506158853', null, '0', '4', '666.66', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1400', '50c73971227b93ce4cebb735db3a0a41', '3', '75', '0', '2.00', '1506306601', '1506310490', '1', '12', '2.00', null, '13322227777', null);
INSERT INTO `guguo_red_envelope` VALUES ('1401', 'e7d9b8fd9ce0e93960cdb3fea8c7c352', '3', '75', '0', '2.00', '1506306601', null, '0', '8', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1402', 'c179569b96466f5ae18695f09fc90dfb', '3', '75', '0', '2.00', '1506306601', null, '0', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1403', '0351638e7cab9cd0f38adcf322eeb7ce', '3', '75', '0', '2.00', '1506306601', null, '0', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1404', '6abb52957decfedcff2156427381510f', '3', '75', '0', '2.00', '1506306601', null, '0', '9', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1405', 'e00b9708e43ce072e7bc764f5194b622', '3', '75', '0', '0.20', '1506306601', '1506311114', '1', '12', '0.20', null, '13322227777', null);
INSERT INTO `guguo_red_envelope` VALUES ('1406', 'afeabbacf06b74765f3d46439e9d2969', '3', '75', '0', '0.20', '1506306601', null, '0', '8', '0.20', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1407', '843eb98ccc7a43b5b08db47f3f4c35a6', '3', '75', '0', '0.20', '1506306601', null, '0', '5', '0.20', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1408', 'c4b236d50c17d90aa13a8e6e74fbca28', '3', '75', '0', '0.20', '1506306601', null, '0', '4', '0.20', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1409', '788a9de8da9bbd3639375454a5d85a33', '3', '75', '0', '0.20', '1506306601', null, '0', '9', '0.20', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1415', '44d5b6e09c5533ccacc30546bf7a4539', '3', '77', '0', '2.00', '1506311341', '1506311395', '1', '12', '2.00', null, '13322227777', null);
INSERT INTO `guguo_red_envelope` VALUES ('1416', '04809f0aa039b6649a6c12a1c0b1d156', '3', '77', '0', '2.00', '1506311341', '1506476020', '1', '8', '2.00', null, '13311115555', null);
INSERT INTO `guguo_red_envelope` VALUES ('1417', '82ee5654072497bc5cb0a8d01dd9fb31', '3', '77', '0', '2.00', '1506311341', null, '0', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1418', '9cf64b4494f45199951845210e6ce0a9', '3', '77', '0', '2.00', '1506311341', null, '0', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1419', '1d611744abcef40b994d9faf92e6e6b4', '3', '77', '0', '2.00', '1506311341', null, '0', '9', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1422', '2e9284739465855a60d77d8ccc1c2d0e', '3', '78', '0', '2.00', '1506311701', '1506311757', '1', '12', '2.00', null, '13322227777', null);
INSERT INTO `guguo_red_envelope` VALUES ('1423', '3ecee4d858341530551e7c0c2bee2184', '3', '78', '0', '2.00', '1506311701', '1506475979', '1', '8', '2.00', null, '13311115555', null);
INSERT INTO `guguo_red_envelope` VALUES ('1424', 'c2b964bda032e7a4585c78d99197b1b3', '3', '78', '0', '2.00', '1506311701', null, '0', '5', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1425', '3cc8f39ce9429f15b49c1c24cfced053', '3', '78', '0', '2.00', '1506311701', null, '0', '4', '2.00', null, null, null);
INSERT INTO `guguo_red_envelope` VALUES ('1426', 'f8d2d6fa7712ed0f2bda5ff009aa42aa', '3', '78', '0', '2.00', '1506311701', null, '0', '9', '2.00', null, null, null);

-- ----------------------------
-- Table structure for guguo_role
-- ----------------------------
DROP TABLE IF EXISTS `guguo_role`;
CREATE TABLE `guguo_role` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `role_name` varchar(32) NOT NULL COMMENT '角色名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_role
-- ----------------------------
INSERT INTO `guguo_role` VALUES ('1', '董事长');
INSERT INTO `guguo_role` VALUES ('2', '总裁');
INSERT INTO `guguo_role` VALUES ('3', '大区经理');
INSERT INTO `guguo_role` VALUES ('4', '分公司总经理');
INSERT INTO `guguo_role` VALUES ('5', '部门经理');
INSERT INTO `guguo_role` VALUES ('6', '职员');
INSERT INTO `guguo_role` VALUES ('7', '临时工');
INSERT INTO `guguo_role` VALUES ('8', '实习生');

-- ----------------------------
-- Table structure for guguo_role_business
-- ----------------------------
DROP TABLE IF EXISTS `guguo_role_business`;
CREATE TABLE `guguo_role_business` (
  `role_id` mediumint(9) NOT NULL COMMENT '角色id',
  `business_id` tinyint(2) NOT NULL COMMENT '业务id',
  KEY `role_business` (`role_id`,`business_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_role_business
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_role_employee
-- ----------------------------
DROP TABLE IF EXISTS `guguo_role_employee`;
CREATE TABLE `guguo_role_employee` (
  `user_id` int(11) NOT NULL COMMENT '员工id',
  `role_id` mediumint(9) NOT NULL COMMENT '角色id',
  UNIQUE KEY `role_employee` (`user_id`,`role_id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_role_employee
-- ----------------------------
INSERT INTO `guguo_role_employee` VALUES ('1', '1');
INSERT INTO `guguo_role_employee` VALUES ('1', '2');
INSERT INTO `guguo_role_employee` VALUES ('2', '1');
INSERT INTO `guguo_role_employee` VALUES ('2', '2');
INSERT INTO `guguo_role_employee` VALUES ('3', '1');
INSERT INTO `guguo_role_employee` VALUES ('3', '2');
INSERT INTO `guguo_role_employee` VALUES ('3', '3');
INSERT INTO `guguo_role_employee` VALUES ('4', '1');
INSERT INTO `guguo_role_employee` VALUES ('4', '8');
INSERT INTO `guguo_role_employee` VALUES ('5', '1');
INSERT INTO `guguo_role_employee` VALUES ('6', '1');
INSERT INTO `guguo_role_employee` VALUES ('6', '2');
INSERT INTO `guguo_role_employee` VALUES ('7', '1');
INSERT INTO `guguo_role_employee` VALUES ('7', '2');
INSERT INTO `guguo_role_employee` VALUES ('8', '1');
INSERT INTO `guguo_role_employee` VALUES ('8', '8');
INSERT INTO `guguo_role_employee` VALUES ('9', '1');
INSERT INTO `guguo_role_employee` VALUES ('9', '2');
INSERT INTO `guguo_role_employee` VALUES ('10', '1');
INSERT INTO `guguo_role_employee` VALUES ('10', '2');
INSERT INTO `guguo_role_employee` VALUES ('10', '5');
INSERT INTO `guguo_role_employee` VALUES ('11', '1');
INSERT INTO `guguo_role_employee` VALUES ('11', '2');
INSERT INTO `guguo_role_employee` VALUES ('12', '1');
INSERT INTO `guguo_role_employee` VALUES ('72', '8');
INSERT INTO `guguo_role_employee` VALUES ('85', '1');
INSERT INTO `guguo_role_employee` VALUES ('85', '2');
INSERT INTO `guguo_role_employee` VALUES ('85', '5');
INSERT INTO `guguo_role_employee` VALUES ('85', '6');
INSERT INTO `guguo_role_employee` VALUES ('90', '4');
INSERT INTO `guguo_role_employee` VALUES ('90', '8');
INSERT INTO `guguo_role_employee` VALUES ('97', '1');
INSERT INTO `guguo_role_employee` VALUES ('97', '4');
INSERT INTO `guguo_role_employee` VALUES ('97', '7');
INSERT INTO `guguo_role_employee` VALUES ('98', '7');
INSERT INTO `guguo_role_employee` VALUES ('99', '1');
INSERT INTO `guguo_role_employee` VALUES ('99', '4');
INSERT INTO `guguo_role_employee` VALUES ('99', '7');
INSERT INTO `guguo_role_employee` VALUES ('103', '1');
INSERT INTO `guguo_role_employee` VALUES ('103', '4');
INSERT INTO `guguo_role_employee` VALUES ('103', '7');
INSERT INTO `guguo_role_employee` VALUES ('104', '1');

-- ----------------------------
-- Table structure for guguo_role_rule
-- ----------------------------
DROP TABLE IF EXISTS `guguo_role_rule`;
CREATE TABLE `guguo_role_rule` (
  `role_id` mediumint(9) NOT NULL COMMENT '角色id',
  `rule_id` mediumint(9) NOT NULL COMMENT '权限id',
  KEY `role_rule` (`role_id`,`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_role_rule
-- ----------------------------
INSERT INTO `guguo_role_rule` VALUES ('1', '1');
INSERT INTO `guguo_role_rule` VALUES ('1', '2');
INSERT INTO `guguo_role_rule` VALUES ('1', '3');
INSERT INTO `guguo_role_rule` VALUES ('1', '4');

-- ----------------------------
-- Table structure for guguo_rule
-- ----------------------------
DROP TABLE IF EXISTS `guguo_rule`;
CREATE TABLE `guguo_rule` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(32) NOT NULL COMMENT '规则英文标识',
  `status` tinyint(1) DEFAULT '1' COMMENT '1有效0失效',
  `rule_title` varchar(64) DEFAULT NULL COMMENT '规则中文名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_rule
-- ----------------------------
INSERT INTO `guguo_rule` VALUES ('1', 'rule1', '1', '规则1');
INSERT INTO `guguo_rule` VALUES ('2', 'rule2', '1', '规则2');
INSERT INTO `guguo_rule` VALUES ('3', 'rule3', '1', '规则3');
INSERT INTO `guguo_rule` VALUES ('4', 'rule4', '1', '规则4');

-- ----------------------------
-- Table structure for guguo_sale_chance
-- ----------------------------
DROP TABLE IF EXISTS `guguo_sale_chance`;
CREATE TABLE `guguo_sale_chance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) unsigned NOT NULL COMMENT '客户id',
  `employee_id` int(11) unsigned NOT NULL COMMENT '创建销售机会的员工id',
  `associator_id` varchar(16) DEFAULT '' COMMENT '协助处理人id',
  `business_id` mediumint(9) unsigned NOT NULL COMMENT '业务id',
  `sale_name` varchar(64) DEFAULT NULL COMMENT '销售机会名称',
  `sale_status` tinyint(4) unsigned DEFAULT '0' COMMENT '销售机会状态，0无意向, 1有意向，2预约拜访，3已拜访，4成单申请，5赢单，6输单，7作废，8发票申请,9已退款',
  `guess_money` decimal(13,2) DEFAULT NULL COMMENT '预估成单金额，单位元',
  `prepay_time` int(11) DEFAULT NULL COMMENT '预计成单时间',
  `need_money` decimal(13,2) NOT NULL COMMENT '应支付成单金额，单位元',
  `payed_money` decimal(13,2) DEFAULT NULL COMMENT '已支付金额，单位元',
  `final_money` decimal(13,2) DEFAULT NULL COMMENT '成单金额，单位元',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_sale_chance
-- ----------------------------
INSERT INTO `guguo_sale_chance` VALUES ('1', '11', '3', '', '1', '网站建设1', '4', '123.00', '1533312000', '123.00', '123.00', '123.00', '1501464306', '1502248789', '糯米123糯米123糯米123');
INSERT INTO `guguo_sale_chance` VALUES ('2', '11', '3', '', '1', '网站建设2', '5', '123.00', '0', '123.00', '100.45', '123.00', '1501557107', '1502004707', '备注备');
INSERT INTO `guguo_sale_chance` VALUES ('3', '25', '3', '', '1', '网站建设3', '0', '1.00', '1501313436', '0.00', null, null, '1501557107', '1501557107', null);
INSERT INTO `guguo_sale_chance` VALUES ('4', '26', '3', '', '1', '网站建设4', '0', '2.00', '1501313436', '0.00', null, null, '1501557107', '1501557107', null);
INSERT INTO `guguo_sale_chance` VALUES ('5', '26', '3', '', '1', '网站建设5', '0', '123.00', '1501313436', '0.00', null, null, '1501557107', '1501557107', null);
INSERT INTO `guguo_sale_chance` VALUES ('6', '27', '3', '', '1', '网站建设6', '0', '1.00', '1501313436', '0.00', null, null, '1501557107', '1501557107', null);
INSERT INTO `guguo_sale_chance` VALUES ('7', '38', '3', '', '1', '网站建设7', '0', '2.00', '1501313436', '0.00', null, null, '1501557107', '1501557107', null);
INSERT INTO `guguo_sale_chance` VALUES ('8', '39', '3', '', '1', '阿三打赏', '0', '3.00', '1501313436', '0.00', null, null, '1501557107', '1501557107', null);
INSERT INTO `guguo_sale_chance` VALUES ('9', '11', '3', '', '1', '完全呃曹', '5', '123.00', '0', '123.00', '100.45', '123.00', '1501580694', '1501580694', null);
INSERT INTO `guguo_sale_chance` VALUES ('10', '11', '3', '', '7', '销售机会测试', '5', '54321.00', '0', '54322.00', '50000.98', '54322.00', '1501918643', '1501920865', null);
INSERT INTO `guguo_sale_chance` VALUES ('11', '11', '3', '', '7', '销售机会测试2', '5', '4321.00', '1501776000', '4000.00', '4000.00', '4000.00', '1501922786', '1501923900', '备注备注备注');
INSERT INTO `guguo_sale_chance` VALUES ('12', '11', '3', '', '7', '销售机会测试3', '4', '51423.00', '1501862400', '51423.00', '51423.00', '51423.00', '1501923052', '1502023236', '备注备注');
INSERT INTO `guguo_sale_chance` VALUES ('13', '1', '8', '', '4', '李伯伯', '5', '10000.00', '1502208000', '10.00', '10.00', '10.00', '1502066293', '1502066819', '李白白当上了红军');
INSERT INTO `guguo_sale_chance` VALUES ('14', '1', '8', '', '1', '红军', '5', '1000.00', '1502035200', '1.00', '1.00', '1.00', '1502066614', '1502068731', '李伯伯参军');
INSERT INTO `guguo_sale_chance` VALUES ('15', '1', '8', '', '1', '如来神掌', '7', '888.00', '1502035200', '0.00', null, null, '1502067636', '1502067679', 'Chinese Kongfu');
INSERT INTO `guguo_sale_chance` VALUES ('16', '1', '8', '', '3', '降龙十八掌', '5', '8888.00', '1502035200', '1.00', '1.00', '1.00', '1502067762', '1502152210', '潜龙勿用');
INSERT INTO `guguo_sale_chance` VALUES ('17', '1', '8', '', '4', '金钟罩', '5', '1.00', '1502035200', '1.00', '1.00', '1.00', '1502068236', '1502068398', 'lastone');
INSERT INTO `guguo_sale_chance` VALUES ('18', '1', '8', '', '7', '水天需', '1', '10000.00', '1502121600', '0.00', null, null, '1502152467', '1502152840', '游子吟');
INSERT INTO `guguo_sale_chance` VALUES ('19', '1', '8', '', '4', '不速之客', '5', '111.00', '1502121600', '1.00', '1.00', '1.00', '1502152584', '1502245064', '111');
INSERT INTO `guguo_sale_chance` VALUES ('20', '1', '8', '', '7', '嗷嗷啊', '4', '13141.00', '1502121600', '1.00', '1.00', '1.00', '1502152786', '1502434919', '');
INSERT INTO `guguo_sale_chance` VALUES ('21', '11', '3', '', '7', '销售机会跟踪测试', '5', '123.00', '1502121600', '123.00', '123.00', '123.00', '1502172185', '1506497009', '');
INSERT INTO `guguo_sale_chance` VALUES ('24', '11', '3', '', '7', '测试', '1', '123.00', '1502294400', '0.00', null, null, '1502323752', '1502323752', '的撒');
INSERT INTO `guguo_sale_chance` VALUES ('25', '66', '8', '', '4', '销售机会Ａ', '4', '1.00', '1502294400', '10000.00', '5000.00', '10000.00', '1502323820', '1502324297', 'cxx');
INSERT INTO `guguo_sale_chance` VALUES ('26', '68', '8', '', '2', '巨星', '5', '5000.00', '1502294400', '5000.00', '5000.00', '5000.00', '1502325665', '1502325743', '');
INSERT INTO `guguo_sale_chance` VALUES ('27', '11', '3', '', '3', '建站销售机会演示', '3', '3210.00', '1502294400', '3210.00', '3210.00', '3210.00', '1502328764', '1506561682', '');
INSERT INTO `guguo_sale_chance` VALUES ('28', '68', '8', '', '7', '如来神掌', '3', '322.00', '1502640000', '0.00', null, null, '1502673317', '1502673468', '');
INSERT INTO `guguo_sale_chance` VALUES ('29', '66', '8', '', '7', '如来神掌', '3', '10000.00', '1502726400', '0.00', null, null, '1502766722', '1502766728', '');
INSERT INTO `guguo_sale_chance` VALUES ('30', '68', '8', '', '7', '销售机会', '1', '2.00', '1502985600', '0.00', null, null, '1503045757', '1503045757', '');
INSERT INTO `guguo_sale_chance` VALUES ('31', '78', '8', '', '4', '铜川巨量', '1', '30000.00', '1504454400', '0.00', null, null, '1504507010', '1504507010', '有意向');
INSERT INTO `guguo_sale_chance` VALUES ('32', '81', '12', '', '1', '销售机会', '1', '1.00', '0', '0.00', null, null, '1504572198', '1504572198', '');
INSERT INTO `guguo_sale_chance` VALUES ('33', '82', '12', '', '1', '销售机会', '1', '2.00', '0', '0.00', null, null, '1504573578', '1504573578', '');
INSERT INTO `guguo_sale_chance` VALUES ('34', '83', '12', '', '1', '销售机会', '1', '3.00', '0', '0.00', null, null, '1504574687', '1504574687', '');
INSERT INTO `guguo_sale_chance` VALUES ('35', '84', '12', '', '1', 'xiaoshou', '1', '1.00', '0', '0.00', null, null, '1504576268', '1504576268', '');
INSERT INTO `guguo_sale_chance` VALUES ('36', '65', '4', '1', '1', '销售机会', '1', '2.00', '1504540800', '0.00', null, null, '1504582187', '1504582187', '备注');
INSERT INTO `guguo_sale_chance` VALUES ('37', '85', '8', '', '2', '销售机会1', '1', '100.00', '1505145600', '0.00', null, null, '1504593767', '1504593767', '有意向');
INSERT INTO `guguo_sale_chance` VALUES ('38', '85', '8', '', '7', '销售机会2', '1', '200.00', '1504540800', '0.00', null, null, '1504593832', '1504593832', '有意向2');
INSERT INTO `guguo_sale_chance` VALUES ('39', '86', '8', '', '1', '机会销售', '1', '2000.00', '0', '0.00', null, null, '1504601022', '1504601022', '');
INSERT INTO `guguo_sale_chance` VALUES ('40', '86', '8', '', '3', '机会2', '1', '200.00', '1504540800', '0.00', null, null, '1504601181', '1504601181', '');
INSERT INTO `guguo_sale_chance` VALUES ('41', '87', '8', '', '7', '啊', '1', '199.00', '1504540800', '0.00', null, null, '1504601220', '1504601220', '');
INSERT INTO `guguo_sale_chance` VALUES ('42', '88', '3', '', '1', '萨达萨斯的', '1', '12332.00', '0', '0.00', null, null, '1504601251', '1504601251', '');
INSERT INTO `guguo_sale_chance` VALUES ('43', '90', '8', '', '1', '唧唧复唧唧', '1', '200.00', '1505318400', '0.00', null, null, '1504659938', '1504659938', '无');
INSERT INTO `guguo_sale_chance` VALUES ('44', '65', '4', '', '2', '销售机会', '1', '1.00', '1504627200', '0.00', null, null, '1504663288', '1504663288', '备注');
INSERT INTO `guguo_sale_chance` VALUES ('46', '65', '4', '', '4', '哈哈哈哈', '1', '8888.00', '1504627200', '0.00', null, null, '1504680267', '1504680267', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('47', '65', '4', '', '7', '哈哈哈哈', '1', '2.00', '1504627200', '0.00', null, null, '1504680721', '1504680721', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('48', '65', '4', '', '7', '哈哈哈哈', '1', '8999.00', '1504627200', '0.00', null, null, '1504680763', '1504680763', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('50', '93', '8', '', '7', '1', '3', '10000.00', '1504627200', '0.00', null, null, '1504687513', '1504864684', '');
INSERT INTO `guguo_sale_chance` VALUES ('51', '93', '8', '', '7', '2', '1', '3.00', '1504627200', '0.00', null, null, '1504687533', '1504687533', '');
INSERT INTO `guguo_sale_chance` VALUES ('52', '93', '8', '', '7', '销售机会', '1', '1.00', '1504627200', '0.00', null, null, '1504687559', '1504687559', '');
INSERT INTO `guguo_sale_chance` VALUES ('53', '94', '8', '', '7', '销售机会', '1', '2.00', '1504627200', '0.00', null, null, '1504687714', '1504687714', '');
INSERT INTO `guguo_sale_chance` VALUES ('54', '95', '8', '', '7', '销售机会', '1', '3.00', '1504627200', '0.00', null, null, '1504687769', '1504687769', '');
INSERT INTO `guguo_sale_chance` VALUES ('55', '95', '8', '', '4', '销售机会', '1', '1.00', '1504627200', '0.00', null, null, '1504687853', '1504687853', '');
INSERT INTO `guguo_sale_chance` VALUES ('56', '95', '8', '', '7', 'aaa', '1', '2.00', '1504627200', '0.00', null, null, '1504687861', '1504687861', '');
INSERT INTO `guguo_sale_chance` VALUES ('57', '98', '8', '', '1', '333', '1', '3.00', '1505923200', '0.00', null, null, '1504694310', '1504694310', '');
INSERT INTO `guguo_sale_chance` VALUES ('58', '99', '12', '', '1', '销售机会', '1', '1.00', '1508342400', '0.00', null, null, '1504750440', '1504750440', '');
INSERT INTO `guguo_sale_chance` VALUES ('59', '77', '5', '', '4', 'hhhhhh', '1', '800.00', '0', '0.00', null, null, '1504768102', '1504768102', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('60', '77', '5', '', '4', 'hhhhhh', '1', '800.00', '0', '0.00', null, null, '1504768111', '1504768111', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('61', '74', '5', '', '4', 'hhhhgfdd', '1', '2.00', '0', '0.00', null, null, '1504768271', '1504768271', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('62', '72', '5', '', '4', '测试商机', '1', '3.00', '1504713600', '0.00', null, null, '1504768324', '1504768324', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('63', '16', '5', '', '0', '哈哈哈哈', '1', '1.00', '0', '0.00', null, null, '1504768876', '1504768876', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('67', '77', '5', '', '4', '哈哈哈哈', '1', '1000.00', '1504713600', '0.00', null, null, '1504769346', '1504769346', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('68', '26', '3', '18769714760', '7', '是否大', '1', '12342.00', '1504713600', '0.00', null, null, '1504769511', '1504769511', '');
INSERT INTO `guguo_sale_chance` VALUES ('69', '26', '3', '18769714760', '7', 'dfsafd', '1', '123.00', '1504713600', '0.00', null, null, '1504769540', '1504769540', '');
INSERT INTO `guguo_sale_chance` VALUES ('70', '24', '5', '0', '3', '俄罗斯', '1', '2.00', '0', '0.00', null, null, '1504769686', '1504769686', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('71', '26', '3', '18769714760', '7', 'dfdsfdsf', '1', '321.00', '1504713600', '0.00', null, null, '1504769692', '1504769692', '');
INSERT INTO `guguo_sale_chance` VALUES ('72', '91', '5', '0', '1', '哈哈哈哈', '1', '200.00', '1504713600', '0.00', null, null, '1504769744', '1504769744', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('74', '26', '3', '12312312312', '7', 'zzzzzz', '1', '123.00', '1504713600', '0.00', null, null, '1504770034', '1504770034', '');
INSERT INTO `guguo_sale_chance` VALUES ('75', '72', '5', '0', '0', '哈哈哈哈', '1', '3.00', '0', '0.00', null, null, '1504770077', '1504770077', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('82', '65', '4', '0', '4', '哈哈哈哈', '1', '800.00', '1504800000', '0.00', null, null, '1504832209', '1504832209', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('83', '77', '5', '0', '7', 'dg ', '1', '28.00', '1504800000', '0.00', null, null, '1504832852', '1504832852', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('84', '105', '3', '1333333', '7', '哈哈', '1', '123.00', '1504800000', '0.00', null, null, '1504834170', '1504834170', '');
INSERT INTO `guguo_sale_chance` VALUES ('85', '108', '5', '13311112222', '3', '我的', '3', '200.00', '1504800000', '0.00', null, null, '1504834960', '1504851661', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('86', '109', '5', '18888888888', '4', '恩恩', '1', '18888.00', '1506700800', '0.00', null, null, '1504836028', '1504836028', '');
INSERT INTO `guguo_sale_chance` VALUES ('87', '111', '3', '', '4', '销售计划', '5', '30000.00', '1505404800', '30000.00', '30000.00', '30000.00', '1504838020', '1504840471', '');
INSERT INTO `guguo_sale_chance` VALUES ('88', '111', '3', '', '4', '我的', '3', '333.00', '1504800000', '0.00', null, null, '1504838756', '1504841038', '');
INSERT INTO `guguo_sale_chance` VALUES ('89', '64', '9', '', '7', '销售机会', '1', '0.00', '1504800000', '0.00', null, null, '1504852536', '1504852536', '');
INSERT INTO `guguo_sale_chance` VALUES ('90', '64', '9', '孙', '7', '销售1', '1', '2000.00', '1504800000', '0.00', null, null, '1504853184', '1504853184', '');
INSERT INTO `guguo_sale_chance` VALUES ('91', '64', '9', '', '3', '销售2', '3', '1232.00', '1504800000', '0.00', null, null, '1504853252', '1504855742', '');
INSERT INTO `guguo_sale_chance` VALUES ('92', '64', '9', '', '1', '销售3', '5', '453.00', '1504800000', '453.00', '453.00', '453.00', '1504853751', '1504855605', '');
INSERT INTO `guguo_sale_chance` VALUES ('93', '64', '9', '', '7', '销售5', '5', '5645.00', '1504800000', '5645.00', '5645.00', '5645.00', '1504853862', '1504854551', '');
INSERT INTO `guguo_sale_chance` VALUES ('95', '107', '4', '', '7', '哈哈哈哈', '1', '1.00', '0', '0.00', null, null, '1504854335', '1504854335', '哈哈');
INSERT INTO `guguo_sale_chance` VALUES ('96', '113', '12', '', '4', '销售机会', '1', '2.00', '1509379200', '0.00', null, null, '1504855869', '1504855869', '个人标签网站建设意向');
INSERT INTO `guguo_sale_chance` VALUES ('97', '111', '3', '', '7', '阿斯顿', '3', '123.00', '1504800000', '0.00', null, null, '1504857410', '1504857417', '');
INSERT INTO `guguo_sale_chance` VALUES ('98', '119', '8', '', '4', '如来神掌', '1', '3.00', '0', '0.00', null, null, '1504923286', '1504923286', '');
INSERT INTO `guguo_sale_chance` VALUES ('99', '120', '8', '', '7', '如来神掌', '1', '1.00', '0', '0.00', null, null, '1504923604', '1504923604', '');
INSERT INTO `guguo_sale_chance` VALUES ('100', '126', '12', '', '7', '销售机会', '1', '2.00', '0', '0.00', null, null, '1505094659', '1505094659', '');
INSERT INTO `guguo_sale_chance` VALUES ('101', '127', '12', '', '7', '销售机会', '1', '3.00', '0', '0.00', null, null, '1505094716', '1505094753', '');
INSERT INTO `guguo_sale_chance` VALUES ('102', '140', '12', '', '7', '销售机会', '1', '1.00', '0', '0.00', null, null, '1505098268', '1505098268', '');
INSERT INTO `guguo_sale_chance` VALUES ('103', '144', '12', '', '4', '销售机会', '1', '2.00', '0', '0.00', null, null, '1505098567', '1505098567', '');
INSERT INTO `guguo_sale_chance` VALUES ('104', '145', '12', '', '2', '销售机会哈哈', '1', '3.00', '0', '0.00', null, null, '1505098710', '1505098710', '');
INSERT INTO `guguo_sale_chance` VALUES ('105', '146', '12', '', '4', '销售机会', '1', '1.00', '0', '0.00', null, null, '1505098984', '1505098984', '');
INSERT INTO `guguo_sale_chance` VALUES ('106', '150', '12', '', '4', '销售机会', '7', '2.00', '0', '0.00', null, null, '1505101637', '1505101637', '有对对对意向无意向');
INSERT INTO `guguo_sale_chance` VALUES ('107', '151', '8', '', '7', '如来神掌', '1', '3.00', '0', '0.00', null, null, '1505102069', '1505102069', '无意向对对对');
INSERT INTO `guguo_sale_chance` VALUES ('108', '150', '12', '', '7', '销售机会1', '1', '1.00', '1505059200', '0.00', null, null, '1505112176', '1505120900', '');
INSERT INTO `guguo_sale_chance` VALUES ('109', '150', '12', '', '7', '销售机会2', '3', '100.00', '1505059200', '0.00', null, null, '1505119838', '1505351437', '有对对对意向');
INSERT INTO `guguo_sale_chance` VALUES ('110', '150', '12', '', '7', '销售机会4', '5', '1000.00', '1505059200', '1000.00', '1000.00', '1000.00', '1505120868', '1505203309', '个人的标签');
INSERT INTO `guguo_sale_chance` VALUES ('111', '150', '12', '', '7', '销售机会', '7', '2.00', '1505145600', '0.00', null, null, '1505177982', '1505177982', '');
INSERT INTO `guguo_sale_chance` VALUES ('112', '150', '12', '', '7', '销售机会', '7', '3.00', '1505145600', '0.00', null, null, '1505178000', '1505178000', '');
INSERT INTO `guguo_sale_chance` VALUES ('113', '150', '12', '', '7', '销售机会666', '1', '99.00', '1505145600', '0.00', null, null, '1505199259', '1505352509', '有对对对意向');
INSERT INTO `guguo_sale_chance` VALUES ('114', '150', '12', '', '7', '机会9', '1', '200.00', '1505318400', '0.00', null, null, '1505351695', '1505444334', '');
INSERT INTO `guguo_sale_chance` VALUES ('115', '163', '8', '', '7', '如来神掌', '5', '1000.00', '0', '1000.00', '1000.00', '1000.00', '1505446245', '1506071252', '33对对对');
INSERT INTO `guguo_sale_chance` VALUES ('116', '162', '8', '', '7', '销售机会', '3', '1.00', '1506009600', '0.00', null, null, '1506072962', '1506072980', '');
INSERT INTO `guguo_sale_chance` VALUES ('117', '163', '8', '', '7', '李伯伯', '3', '888.00', '1506268800', '0.00', null, null, '1506305046', '1506305065', '');
INSERT INTO `guguo_sale_chance` VALUES ('118', '11', '3', '', '7', '啊哈哈', '1', '10.00', '1506355200', '0.00', null, null, '1506386324', '1506386324', '');

-- ----------------------------
-- Table structure for guguo_sale_chance_visit
-- ----------------------------
DROP TABLE IF EXISTS `guguo_sale_chance_visit`;
CREATE TABLE `guguo_sale_chance_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL COMMENT '销售机会id',
  `visit_time` int(11) DEFAULT NULL COMMENT '拜访时间',
  `create_time` int(11) DEFAULT NULL COMMENT '记录创建时间',
  `visit_place` varchar(128) DEFAULT NULL COMMENT '拜访地点',
  `location` varchar(64) DEFAULT NULL COMMENT '拜访位置坐标',
  `sign_in_location` varchar(64) DEFAULT NULL COMMENT '签到时位置坐标',
  `partner_notice` tinyint(1) DEFAULT '1' COMMENT '结伴提醒，1是，0否',
  `add_note` tinyint(1) DEFAULT NULL COMMENT '添加到备忘录',
  `visit_ok` tinyint(1) DEFAULT NULL COMMENT '拜访成功，1是，0否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_sale_chance_visit
-- ----------------------------
INSERT INTO `guguo_sale_chance_visit` VALUES ('1', '1', '1501689600', '1501461684', '阿斯顿', '', '36.000000,119.000000', '1', '1', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('2', '2', '1501516700', '1501464170', '萨达费收费', '', null, '1', '1', '0');
INSERT INTO `guguo_sale_chance_visit` VALUES ('3', '9', '1501516800', '1501579178', '213曹', '', null, '1', '1', '0');
INSERT INTO `guguo_sale_chance_visit` VALUES ('4', '10', '1501862400', '1501918668', '拜访地址测试', '', '36.000000,119.000000', '1', '1', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('5', '11', '1501862400', '1501923097', '拜访地点 拜访地点 ', '', '36.000000,119.000000', '1', '1', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('6', '12', '1501862400', '1501923114', '拜访地点 拜访地点 拜访地点 ', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('7', '13', '1502035200', '1502066386', '这里这里', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('8', '15', '1502035200', '1502067679', '少林寺', '', null, '0', '0', '0');
INSERT INTO `guguo_sale_chance_visit` VALUES ('9', '16', '1502035200', '1502068105', '现龙在田', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('10', '17', '1502035200', '1502068304', '紫禁之巅', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('11', '14', '1502035200', '1502068700', '1', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('12', '19', '1502121600', '1502152606', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('13', '21', '1502121600', '1502172204', '者的身份散发的改', '', '36.000000,119.000000', '1', '1', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('14', '25', '1502294400', '1502324114', 'xxc', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('15', '20', '0', '1502434790', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('16', '28', '0', '1502673371', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('17', '29', '0', '1502766728', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('18', '87', '1504838580', '1504838638', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('19', '88', '1504840980', '1504841038', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('20', '85', '1504851600', '1504851661', '', '', '0.000000,0.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('21', '93', '1504854000', '1504854057', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('22', '92', '1504854960', '1504854985', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('23', '91', '1565249220', '1504855742', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('24', '97', '1504857360', '1504857417', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('25', '50', '1504864620', '1504864684', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('26', '110', '1505180280', '1505180327', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('27', '109', '1505351400', '1505351437', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('28', '115', '1505459760', '1505460054', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('29', '116', '1506072960', '1506072980', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('30', '117', '1506305040', '1506305065', '', '', '36.000000,119.000000', '0', '0', '1');
INSERT INTO `guguo_sale_chance_visit` VALUES ('31', '27', '1506305520', '1506305547', '金艺大厦', '36.713173,119.113675', '36.000000,119.000000', '0', '0', '1');

-- ----------------------------
-- Table structure for guguo_sale_order_bill
-- ----------------------------
DROP TABLE IF EXISTS `guguo_sale_order_bill`;
CREATE TABLE `guguo_sale_order_bill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `sale_id` int(11) DEFAULT NULL COMMENT '销售机会id',
  `operator` int(11) DEFAULT NULL COMMENT '申请人,员工id',
  `bill_type` int(11) NOT NULL COMMENT '发票类型',
  `order_id` int(11) NOT NULL COMMENT '成单合同申请id',
  `contract_id` int(11) NOT NULL COMMENT '合同id',
  `contract_no` varchar(64) NOT NULL COMMENT '合同编号',
  `customer_name` varchar(64) NOT NULL COMMENT '客户名称',
  `bill_no` varchar(64) DEFAULT NULL COMMENT '发票号',
  `tax_num` varchar(32) DEFAULT NULL COMMENT '公司发票税号',
  `bill_money` decimal(13,2) NOT NULL COMMENT '发票金额,单位元',
  `pay_type` varchar(32) NOT NULL COMMENT '付款方式,0现金,1,2,3银行编号',
  `handle_1` varchar(255) NOT NULL COMMENT '一审人',
  `handle_2` varchar(255) DEFAULT '0' COMMENT '二审人',
  `handle_3` varchar(255) DEFAULT '0' COMMENT '三审人',
  `handle_4` varchar(255) DEFAULT '0' COMMENT '四审人',
  `handle_5` varchar(255) DEFAULT '0' COMMENT '五审人',
  `handle_6` varchar(255) DEFAULT '0' COMMENT '六审人',
  `handle_status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '当前审核步骤',
  `handle_now` varchar(255) NOT NULL COMMENT '当前审核人',
  `remark` varchar(255) DEFAULT '',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `check_money_time` int(11) DEFAULT NULL COMMENT '认款日期',
  `status` tinyint(1) NOT NULL COMMENT '发票审核状态,0审核中，1已通过，2已驳回，3已撤回,4待领取，5已领取，6已作废，7已收回,8已提醒,9已退款',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_sale_order_bill
-- ----------------------------
INSERT INTO `guguo_sale_order_bill` VALUES ('5', '11', '9', '3', '3', '2', '20', 'dbds10002', '山东中迅网络传媒有限公司', '321123321117', '1234567890', '321.00', '建设银行', '72', '90', '4', '1', '6', '9', '1', '72', '', '1501901926', '1501901926', '1501901926', '3');
INSERT INTO `guguo_sale_order_bill` VALUES ('6', '11', '10', '3', '2', '4', '23', 'dbdt1006', '山东中迅网络传媒有限公司', '321123321118', '9876543210', '50000.00', '工商银行', '72', '90', '0', '0', '0', '0', '2', '72', '', '1501921002', '1501921002', null, '4');
INSERT INTO `guguo_sale_order_bill` VALUES ('7', '11', '11', '3', '2', '5', '25', 'dbds10004', '山东中迅网络传媒有限公司', '321123321119', '987654321', '6000.00', '工商银行', '72', '90', '0', '0', '0', '0', '2', '72', '', '1501924700', '1501924700', null, '4');
INSERT INTO `guguo_sale_order_bill` VALUES ('8', '11', '9', '3', '2', '2', '1', 'bdnm1', '山东中迅网络传媒有限公司', '321123321120', '432123456', '6000.00', '工商银行', '72', '90', '0', '0', '0', '0', '2', '72', '', '1501930438', '1501930438', null, '4');
INSERT INTO `guguo_sale_order_bill` VALUES ('9', '11', '2', '3', '3', '1', '29', 'bdnm6', '山东中迅网络传媒有限公司', '321123321121', '', '100.45', '建设银行', '72', '90', '4', '1', '6', '9', '1', '72', '', '1502005325', '1502005325', null, '3');
INSERT INTO `guguo_sale_order_bill` VALUES ('10', '11', '2', '3', '1', '1', '29', 'bdnm6', '山东中迅网络传媒有限公司', '321123321122', '1234567', '100.45', '阿斯顿', '72', '90', '4', '0', '0', '0', '1', '72', '', '1502010978', '1502010978', null, '2');
INSERT INTO `guguo_sale_order_bill` VALUES ('11', '11', '2', '3', '1', '1', '29', 'bdnm6', '山东中迅网络传媒有限公司', '321123321123', '100.45', '100.45', '的撒', '72', '90', '4', '0', '0', '0', '3', '72', '', '1502063414', '1502063414', null, '4');
INSERT INTO `guguo_sale_order_bill` VALUES ('12', '1', '13', '8', '1', '7', '31', 'zxjz3', '山东李白有限公司', '12138', '12138', '10.00', '现金', '72', '90', '4', '0', '0', '0', '3', '72', '', '1502067325', '1502067325', null, '4');
INSERT INTO `guguo_sale_order_bill` VALUES ('13', '1', '17', '8', '2', '8', '33', 'bdbk2', '山东李白有限公司', 'bdbk2', '', '0.00', '现金', '72', '90', '0', '0', '0', '0', '2', '72', '', '1502068922', '1502068922', null, '5');
INSERT INTO `guguo_sale_order_bill` VALUES ('14', '1', '14', '8', '1', '9', '32', 'bdbk1', '山东李白有限公司', '12138', '12138', '1.00', '阿斯顿', '72', '90', '4', '0', '0', '0', '3', '72', '', '1502072642', '1502072642', null, '5');
INSERT INTO `guguo_sale_order_bill` VALUES ('15', '1', '16', '8', '2', '10', '35', 'bdbk3', '山东李白有限公司', '12138', '', '888.00', '现金', '72', '90', '0', '0', '0', '0', '2', '72', '', '1502152352', '1502152352', null, '4');
INSERT INTO `guguo_sale_order_bill` VALUES ('16', '1', '19', '8', '1', '11', '36', 'bdbk4', '山东李白有限公司', '12345', '9876543210', '123.00', '的撒', '72,3', '90,3', '4,3', '0', '0', '0', '2', '72', '123;', '1502251017', '1502251017', null, '3');
INSERT INTO `guguo_sale_order_bill` VALUES ('17', '68', '26', '8', '1', '14', '5', 'zxjz2', '毛不易', '12138', '12138', '3446.00', '现金', '72', '72', '72', '0', '0', '0', '3', '72', '', '1502325825', '1502325825', null, '5');
INSERT INTO `guguo_sale_order_bill` VALUES ('18', '111', '87', '3', '2', '16', '42', 'bdbk5', '我的科技', '12138', '', '30000.00', '现金', '72', '72', '0', '0', '0', '0', '2', '72', '', '1504841244', '1504841244', null, '5');
INSERT INTO `guguo_sale_order_bill` VALUES ('20', '150', '110', '12', '3', '19', '51', 'zxjz11', '客户1', 'vvvv', '', '1000.00', '现金', '72', '72', '72', '72', '72', '72', '6', '72', '', '1505204127', '1505204127', null, '4');
INSERT INTO `guguo_sale_order_bill` VALUES ('21', '11', '21', '3', '2', '28', '10', 'dbdt1003', '山东中迅网络传媒有限公司', 'fp123124', '', '23.00', '工商银行', '72', '72', '0', '0', '0', '0', '2', '72', '', '1506559853', '1506559853', null, '4');
INSERT INTO `guguo_sale_order_bill` VALUES ('22', '11', '21', '3', '2', '28', '14', 'dbdt1004', '山东中迅网络传媒有限公司', 'fp123123', '', '100.00', '现金', '72', '72', '0', '0', '0', '0', '2', '72', '', '1506560472', '1506560472', null, '4');

-- ----------------------------
-- Table structure for guguo_sale_order_bill_item
-- ----------------------------
DROP TABLE IF EXISTS `guguo_sale_order_bill_item`;
CREATE TABLE `guguo_sale_order_bill_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_id` int(11) NOT NULL COMMENT '发票申请id',
  `product_type` varchar(32) DEFAULT NULL COMMENT '产品类型',
  `product_type_money` decimal(13,2) NOT NULL COMMENT '金额,单位元',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_sale_order_bill_item
-- ----------------------------
INSERT INTO `guguo_sale_order_bill_item` VALUES ('1', '5', '商城', '321.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('2', '6', '大搜', '50000.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('3', '7', '大搜', '6000.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('4', '8', '大搜', '6000.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('5', '9', '商城', '100.45');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('6', '10', 'pc', '5.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('7', '10', 'mobi', '8.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('8', '10', 'wx', '87.45');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('9', '11', 'pc', '10.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('10', '11', 'mobi', '20.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('11', '11', 'wx', '70.45');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('12', '12', 'pc', '1.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('13', '12', 'mobi', '5.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('14', '12', 'wx', '4.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('15', '13', '大搜', '0.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('16', '14', 'mobi', '1.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('17', '15', '大搜', '888.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('18', '16', 'pc', '123.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('19', '17', 'pc', '12.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('20', '17', 'mobi', '3434.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('21', '17', 'wx', '0.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('22', '18', '大搜', '30000.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('23', '20', '商城', '1000.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('24', '21', '大搜', '23.00');
INSERT INTO `guguo_sale_order_bill_item` VALUES ('25', '22', '大搜', '100.00');

-- ----------------------------
-- Table structure for guguo_sale_order_contract
-- ----------------------------
DROP TABLE IF EXISTS `guguo_sale_order_contract`;
CREATE TABLE `guguo_sale_order_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL COMMENT '销售机会id',
  `order_num` tinyint(4) NOT NULL COMMENT '合同数量',
  `prod_desc` varchar(255) DEFAULT NULL COMMENT '产品说明',
  `handle_1` varchar(255) NOT NULL COMMENT '一审核人，员工id',
  `handle_2` varchar(255) DEFAULT '0' COMMENT '二审核人，员工id',
  `handle_3` varchar(255) DEFAULT '0' COMMENT '三审核人，员工id',
  `handle_4` varchar(255) DEFAULT '0' COMMENT '四审人',
  `handle_5` varchar(255) DEFAULT '0' COMMENT '五审人',
  `handle_6` varchar(255) DEFAULT '0' COMMENT '六审人',
  `handle_status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '当前审核步骤',
  `handle_now` varchar(255) NOT NULL COMMENT '当前审核人',
  `remark` varchar(255) DEFAULT '',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态,0审核中,1通过,2驳回,3撤回',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='销售机会成单申请，提交后需要审核';

-- ----------------------------
-- Records of guguo_sale_order_contract
-- ----------------------------
INSERT INTO `guguo_sale_order_contract` VALUES ('1', '2', '1', '是大多数否', '72', '90', '4', '0', '0', '0', '3', '72', '', '1502004707', '1502004707', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('2', '9', '2', '是大多数', '72', '5', '4', '0', '0', '0', '1', '72', '', '1501580694', '1501580694', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('4', '10', '2', '', '72', '90', '85', '5', '4', '3', '1', '72', '', '1501920865', '1501920865', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('5', '11', '2', '产品详情 产品详情 ', '72', '90', '85', '5', '4', '3', '6', '72', '', '1501923900', '1501923900', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('6', '12', '2', '产品详情 产品详情 产品详情 ', '72', '90', '85', '5', '4', '3', '1', '72', '', '1502023236', '1502023236', '0');
INSERT INTO `guguo_sale_order_contract` VALUES ('7', '13', '1', '蓝翔', '72', '72', '5', '4', '3', '1', '6', '72', '', '1502066819', '1502066819', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('8', '17', '2', '', '72', '72', '5', '4', '3', '1', '6', '72', '', '1502068398', '1502068398', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('9', '14', '2', '', '72', '5', '4', '0', '0', '0', '3', '72', '', '1502068731', '1502068731', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('10', '16', '2', '', '72', '72', '0', '0', '0', '0', '2', '72', '', '1502152210', '1502152210', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('11', '19', '2', '', '72', '72', '5', '4', '3', '1', '6', '72', '', '1502245064', '1502245064', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('12', '1', '2', '阿三打洞撒阿达是', '72,3', '90,3', '4,3', '', '', '', '3', '4,3', '123;', '1502248790', '1502248790', '0');
INSERT INTO `guguo_sale_order_contract` VALUES ('13', '25', '1', '详情', '72', '72', '10', '72', '3', '1', '3', '10', '', '1502324297', '1502324297', '0');
INSERT INTO `guguo_sale_order_contract` VALUES ('14', '26', '1', '', '72', '72', '', '', '', '', '2', '72', '', '1502325743', '1502325743', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('15', '20', '2', '', '4', '72', '72', '10', '72', '3', '1', '4', '', '1502434919', '1502434919', '0');
INSERT INTO `guguo_sale_order_contract` VALUES ('16', '87', '2', '', '72', '72', '72', '72', '72', '72', '6', '72', '', '1504840471', '1504840471', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('17', '93', '2', 'eedd', '72', '72', '72', '72', '72', '72', '6', '72', '', '1504854551', '1504854551', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('18', '92', '2', '的', '72', '72', '72', '', '', '', '3', '72', '', '1504855605', '1504855605', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('19', '110', '1', '详情', '72', '72', '72', '72', '72', '72', '6', '72', '', '1505203309', '1505203309', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('20', '115', '2', '', '72', '72', '72', '72', '72', '72', '6', '72', '', '1506071252', '1506071252', '1');
INSERT INTO `guguo_sale_order_contract` VALUES ('28', '21', '2', '', '72', '72', '72', '72', '72', '72', '6', '72', '', '1506497009', '1506497009', '1');

-- ----------------------------
-- Table structure for guguo_sale_order_contract_item
-- ----------------------------
DROP TABLE IF EXISTS `guguo_sale_order_contract_item`;
CREATE TABLE `guguo_sale_order_contract_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL COMMENT '销售机会id',
  `sale_order_id` int(11) unsigned NOT NULL COMMENT '销售机会成单申请id',
  `contract_id` int(10) unsigned NOT NULL COMMENT '合同id',
  `contract_money` decimal(13,2) NOT NULL COMMENT '合同金额',
  `pay_money` decimal(13,2) NOT NULL COMMENT '打款金额，单位元',
  `pay_type` tinyint(1) NOT NULL COMMENT '付款方式1现金，2转账',
  `pay_bank` varchar(64) NOT NULL COMMENT '打款银行',
  `pay_name` varchar(128) NOT NULL COMMENT '打款名称',
  `due_time` int(10) unsigned NOT NULL COMMENT '预计合同到期时间',
  `need_bill` tinyint(1) NOT NULL DEFAULT '1' COMMENT '需要发票1是，0否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='销售机会成单申请合同';

-- ----------------------------
-- Records of guguo_sale_order_contract_item
-- ----------------------------
INSERT INTO `guguo_sale_order_contract_item` VALUES ('1', '2', '1', '29', '123.00', '100.45', '1', '', '撒发达', '1501603200', '1');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('2', '9', '2', '1', '123.00', '100.45', '2', '', '萨达搜索的', '1501516800', '1');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('3', '10', '4', '23', '54322.00', '50000.98', '2', '', '打款名称 打款名称 ', '1533398400', '1');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('4', '11', '5', '25', '4000.00', '4000.00', '2', '', '打款名称打款名称', '1501862400', '1');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('5', '12', '6', '28', '51423.00', '51423.00', '2', '', '打款名称打款名称打款名称', '1501862400', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('6', '13', '7', '31', '10.00', '10.00', '1', '', '打款名称', '1502035200', '1');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('7', '17', '8', '33', '1.00', '1.00', '2', '', 'biubiubiu', '1502035200', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('8', '14', '9', '32', '1.00', '1.00', '2', '', '', '1502035200', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('9', '16', '10', '35', '1.00', '1.00', '2', '', '', '1502121600', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('10', '19', '11', '36', '23.00', '23.00', '1', '', '山东中迅网络传媒有限公司', '2017', '1');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('11', '1', '12', '30', '123.00', '123.00', '2', '', '糯米123', '1502208000', '1');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('12', '25', '13', '4', '10000.00', '5000.00', '1', '', '打你妹', '1502294400', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('13', '26', '14', '5', '5000.00', '5000.00', '1', '', 'cxx', '1502294400', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('14', '20', '15', '38', '100.00', '100.00', '1', '', '山东中迅网络传媒有限公司', '2017', '1');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('15', '87', '16', '42', '30000.00', '30000.00', '2', '', '我的科技', '1504800000', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('16', '93', '17', '44', '5645.00', '5645.00', '2', '', 'wwww', '1504800000', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('17', '92', '18', '48', '453.00', '453.00', '2', '', 'wwww', '1504800000', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('18', '110', '19', '51', '1000.00', '1000.00', '1', '', '客户1', '1505145600', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('19', '115', '20', '59', '1000.00', '1000.00', '2', '', '额', '1506009600', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('20', '21', '28', '10', '23.00', '23.00', '1', '', '山东中迅网络传媒有限公司', '1506441600', '0');
INSERT INTO `guguo_sale_order_contract_item` VALUES ('21', '21', '28', '14', '100.00', '100.00', '1', '', '山东中迅网络传媒有限公司', '1506441600', '1');

-- ----------------------------
-- Table structure for guguo_structure
-- ----------------------------
DROP TABLE IF EXISTS `guguo_structure`;
CREATE TABLE `guguo_structure` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `struct_pid` mediumint(9) DEFAULT '-1' COMMENT '上级体系id，-1为默认部门，0为顶层',
  `struct_name` varchar(128) DEFAULT NULL COMMENT '体系名称',
  `struct_en` varchar(128) DEFAULT NULL COMMENT '体系英文名',
  `struct_intro` text COMMENT '体系介绍',
  `struct_leader` varchar(64) DEFAULT NULL COMMENT '体系领导',
  `groupid` varchar(155) DEFAULT NULL COMMENT '群组id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_structure
-- ----------------------------
INSERT INTO `guguo_structure` VALUES ('1', '0', '总公司', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('2', '1', '华北大区', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('3', '2', '潍坊中迅', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('4', '3', '销售部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('5', '4', '销售一部', null, null, null, '27603478052865');
INSERT INTO `guguo_structure` VALUES ('6', '4', '销售二部', null, null, null, '28215424909313');
INSERT INTO `guguo_structure` VALUES ('7', '4', '销售三部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('8', '2', '济宁中迅', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('9', '2', '泰安中迅', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('10', '2', '菏泽中迅', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('11', '2', '莱芜中迅', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('12', '3', '技术部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('13', '3', '研发部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('14', '8', '销售部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('15', '9', '销售部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('16', '10', '销售部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('18', '12', '美工分部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('19', '12', '文书分部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('20', '1', '其他大区', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('21', '20', '其他分公司', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('22', '21', '其他部门', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('23', '21', '其他有关部门', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('24', '12', '审核分部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('25', '13', 'UI组', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('26', '13', '产品组', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('27', '1', '海外大区', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('28', '27', '北美分公司', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('29', '28', '山景城分部', null, null, null, null);
INSERT INTO `guguo_structure` VALUES ('30', '10', '事业部', null, null, null, null);

-- ----------------------------
-- Table structure for guguo_structure_employee
-- ----------------------------
DROP TABLE IF EXISTS `guguo_structure_employee`;
CREATE TABLE `guguo_structure_employee` (
  `user_id` int(11) NOT NULL COMMENT '员工id',
  `struct_id` mediumint(9) NOT NULL COMMENT '部门id，-1为默认部门',
  UNIQUE KEY `userid,struct_id` (`user_id`,`struct_id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_structure_employee
-- ----------------------------
INSERT INTO `guguo_structure_employee` VALUES ('1', '6');
INSERT INTO `guguo_structure_employee` VALUES ('1', '12');
INSERT INTO `guguo_structure_employee` VALUES ('1', '13');
INSERT INTO `guguo_structure_employee` VALUES ('1', '29');
INSERT INTO `guguo_structure_employee` VALUES ('2', '3');
INSERT INTO `guguo_structure_employee` VALUES ('2', '4');
INSERT INTO `guguo_structure_employee` VALUES ('2', '12');
INSERT INTO `guguo_structure_employee` VALUES ('2', '13');
INSERT INTO `guguo_structure_employee` VALUES ('2', '29');
INSERT INTO `guguo_structure_employee` VALUES ('3', '3');
INSERT INTO `guguo_structure_employee` VALUES ('3', '5');
INSERT INTO `guguo_structure_employee` VALUES ('3', '12');
INSERT INTO `guguo_structure_employee` VALUES ('3', '13');
INSERT INTO `guguo_structure_employee` VALUES ('3', '29');
INSERT INTO `guguo_structure_employee` VALUES ('4', '12');
INSERT INTO `guguo_structure_employee` VALUES ('4', '13');
INSERT INTO `guguo_structure_employee` VALUES ('4', '29');
INSERT INTO `guguo_structure_employee` VALUES ('5', '5');
INSERT INTO `guguo_structure_employee` VALUES ('5', '12');
INSERT INTO `guguo_structure_employee` VALUES ('5', '13');
INSERT INTO `guguo_structure_employee` VALUES ('5', '29');
INSERT INTO `guguo_structure_employee` VALUES ('6', '3');
INSERT INTO `guguo_structure_employee` VALUES ('6', '5');
INSERT INTO `guguo_structure_employee` VALUES ('6', '12');
INSERT INTO `guguo_structure_employee` VALUES ('6', '13');
INSERT INTO `guguo_structure_employee` VALUES ('6', '29');
INSERT INTO `guguo_structure_employee` VALUES ('7', '3');
INSERT INTO `guguo_structure_employee` VALUES ('7', '5');
INSERT INTO `guguo_structure_employee` VALUES ('7', '12');
INSERT INTO `guguo_structure_employee` VALUES ('7', '13');
INSERT INTO `guguo_structure_employee` VALUES ('7', '29');
INSERT INTO `guguo_structure_employee` VALUES ('8', '1');
INSERT INTO `guguo_structure_employee` VALUES ('8', '12');
INSERT INTO `guguo_structure_employee` VALUES ('8', '13');
INSERT INTO `guguo_structure_employee` VALUES ('8', '29');
INSERT INTO `guguo_structure_employee` VALUES ('9', '1');
INSERT INTO `guguo_structure_employee` VALUES ('9', '5');
INSERT INTO `guguo_structure_employee` VALUES ('9', '12');
INSERT INTO `guguo_structure_employee` VALUES ('9', '13');
INSERT INTO `guguo_structure_employee` VALUES ('9', '29');
INSERT INTO `guguo_structure_employee` VALUES ('10', '6');
INSERT INTO `guguo_structure_employee` VALUES ('11', '3');
INSERT INTO `guguo_structure_employee` VALUES ('11', '6');
INSERT INTO `guguo_structure_employee` VALUES ('11', '12');
INSERT INTO `guguo_structure_employee` VALUES ('11', '13');
INSERT INTO `guguo_structure_employee` VALUES ('11', '29');
INSERT INTO `guguo_structure_employee` VALUES ('12', '1');
INSERT INTO `guguo_structure_employee` VALUES ('12', '6');
INSERT INTO `guguo_structure_employee` VALUES ('50', '3');
INSERT INTO `guguo_structure_employee` VALUES ('51', '1');
INSERT INTO `guguo_structure_employee` VALUES ('57', '6');
INSERT INTO `guguo_structure_employee` VALUES ('58', '6');
INSERT INTO `guguo_structure_employee` VALUES ('59', '7');
INSERT INTO `guguo_structure_employee` VALUES ('60', '7');
INSERT INTO `guguo_structure_employee` VALUES ('61', '1');
INSERT INTO `guguo_structure_employee` VALUES ('62', '1');
INSERT INTO `guguo_structure_employee` VALUES ('63', '1');
INSERT INTO `guguo_structure_employee` VALUES ('66', '1');
INSERT INTO `guguo_structure_employee` VALUES ('67', '1');
INSERT INTO `guguo_structure_employee` VALUES ('68', '1');
INSERT INTO `guguo_structure_employee` VALUES ('71', '5');
INSERT INTO `guguo_structure_employee` VALUES ('72', '7');
INSERT INTO `guguo_structure_employee` VALUES ('73', '1');
INSERT INTO `guguo_structure_employee` VALUES ('85', '6');
INSERT INTO `guguo_structure_employee` VALUES ('85', '12');
INSERT INTO `guguo_structure_employee` VALUES ('85', '13');
INSERT INTO `guguo_structure_employee` VALUES ('85', '26');
INSERT INTO `guguo_structure_employee` VALUES ('85', '29');
INSERT INTO `guguo_structure_employee` VALUES ('90', '6');
INSERT INTO `guguo_structure_employee` VALUES ('90', '7');
INSERT INTO `guguo_structure_employee` VALUES ('97', '6');
INSERT INTO `guguo_structure_employee` VALUES ('97', '7');
INSERT INTO `guguo_structure_employee` VALUES ('98', '6');
INSERT INTO `guguo_structure_employee` VALUES ('99', '6');
INSERT INTO `guguo_structure_employee` VALUES ('99', '7');
INSERT INTO `guguo_structure_employee` VALUES ('103', '5');
INSERT INTO `guguo_structure_employee` VALUES ('104', '5');

-- ----------------------------
-- Table structure for guguo_take_cash
-- ----------------------------
DROP TABLE IF EXISTS `guguo_take_cash`;
CREATE TABLE `guguo_take_cash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL COMMENT '用户id',
  `money_type` tinyint(4) unsigned DEFAULT '1' COMMENT '金额类型,1:个人,2:公司',
  `take_money` int(11) DEFAULT NULL COMMENT '变动的金额：单位分, 存入正值，取出负值',
  `status` tinyint(1) DEFAULT NULL COMMENT '1取出   2存入',
  `alipay_account` varchar(128) DEFAULT NULL COMMENT '支付宝账号，提现使用',
  `took_time` int(11) DEFAULT NULL COMMENT '取钱时间',
  `order_number` varchar(40) DEFAULT NULL COMMENT '订单号',
  `to_userid` int(11) DEFAULT NULL COMMENT '内部用户转账时，收款人id',
  `remark` varchar(128) DEFAULT NULL COMMENT '备注',
  `from_userid` int(11) DEFAULT NULL COMMENT '收到转账时，来源用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5089 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_take_cash
-- ----------------------------
INSERT INTO `guguo_take_cash` VALUES ('2', '2', '1', '100', '1', 'bqirro0741@sandbox.com', '1489819774', 'guguo_tran_money201703181449321489819772', null, '用户充值', null);
INSERT INTO `guguo_take_cash` VALUES ('3', '2', '1', '100', '1', 'bqirro0741@sandbox.com', '1489825470', 'guguo_tran_money201703181624271489825467', null, '用户充值', null);
INSERT INTO `guguo_take_cash` VALUES ('4', '2', '1', '-100', '1', null, '1489995148', null, null, '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('5', '4', '1', '100', '2', null, '1489995148', null, null, '收到转账', null);
INSERT INTO `guguo_take_cash` VALUES ('6', '2', '1', '-100', '1', null, '1489995697', null, '4', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('7', '4', '1', '100', '2', null, '1489995697', null, null, '收到转账', '2');
INSERT INTO `guguo_take_cash` VALUES ('8', '2', '1', '100', '1', 'bqirro0741@sandbox.com', '1489996237', 'guguo_tran_money201703201550331489996233', null, '用户充值', null);
INSERT INTO `guguo_take_cash` VALUES ('11', '2', '1', '100', '1', 'bqirro0741@sandbox.com', '1490058732', 'guguo_tran_money201703210912101490058730', null, '用户充值', null);
INSERT INTO `guguo_take_cash` VALUES ('12', '2', '1', '-100', '1', 'bqirro0741@sandbox.com', '1490059506', 'guguo_tran_money201703210925061490059506', null, '用户提现，金额为100分', null);
INSERT INTO `guguo_take_cash` VALUES ('18', '2', '1', '-100', '1', 'bqirro0741@sandbox.com', '1490061272', 'guguo_tran_money201703210954321490061272', null, '用户提现，金额为100分', null);
INSERT INTO `guguo_take_cash` VALUES ('19', '2', '1', '-100', '1', 'bqirro0741@sandbox.com', '1490061314', 'guguo_tran_money201703210955141490061314', null, '用户提现，金额为100分', null);
INSERT INTO `guguo_take_cash` VALUES ('20', '2', '1', '-100', '1', 'bqirro0741@sandbox.com', '1490061342', 'guguo_tran_money201703210955391490061339', null, '用户提现，金额为100分', null);
INSERT INTO `guguo_take_cash` VALUES ('21', '2', '1', '-200', '1', null, '1490061646', null, '4', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('22', '4', '1', '200', '2', null, '1490061646', null, null, '收到转账', '2');
INSERT INTO `guguo_take_cash` VALUES ('23', '2', '1', '-10000', '1', null, '1490062122', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('24', '2', '1', '195', '2', null, '1490062302', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('25', '2', '1', '-1200', '1', null, '1490076007', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('27', '2', '1', '20000', '2', null, '1490079787', null, null, '用户充值', null);
INSERT INTO `guguo_take_cash` VALUES ('30', '2', '1', '20000', '2', null, '1490147694', null, null, '用户充值', null);
INSERT INTO `guguo_take_cash` VALUES ('31', '2', '1', '9804', '2', null, '1490172515', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('32', '2', '1', '1200', '2', null, '1490174165', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('33', '2', '1', '-200', '1', null, '1490270026', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('34', '3', '1', '200', '2', null, '1490270026', null, null, '收到转账', '2');
INSERT INTO `guguo_take_cash` VALUES ('35', '2', '1', '-200', '1', null, '1490315387', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('36', '3', '1', '200', '2', null, '1490315387', null, null, '收到转账', '2');
INSERT INTO `guguo_take_cash` VALUES ('37', '2', '1', '-200', '1', null, '1490315597', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('38', '5', '1', '200', '2', null, '1490315597', null, null, '收到转账', '2');
INSERT INTO `guguo_take_cash` VALUES ('39', '3', '1', '-200', '1', null, '1490315906', null, '4', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('40', '4', '1', '200', '2', null, '1490315906', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('49', '2', '1', '20000', '2', null, '1490320511', null, null, '用户充值', null);
INSERT INTO `guguo_take_cash` VALUES ('50', '3', '1', '-200', '1', null, '1490322979', null, '4', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('51', '4', '1', '200', '2', null, '1490322979', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('52', '5', '1', '-120', '1', null, '1490326043', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('53', '3', '1', '120', '2', null, '1490326043', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('54', '5', '1', '-33', '1', null, '1490327374', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('55', '3', '1', '33', '2', null, '1490327374', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('56', '5', '1', '-15', '1', null, '1490335080', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('57', '3', '1', '15', '2', null, '1490335080', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('58', '5', '1', '-1', '1', null, '1490335533', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('59', '3', '1', '1', '2', null, '1490335533', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('60', '5', '1', '-1', '1', null, '1490335557', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('61', '3', '1', '1', '2', null, '1490335557', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('62', '5', '1', '-1', '1', null, '1490335627', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('63', '3', '1', '1', '2', null, '1490335627', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('64', '5', '1', '-1', '1', null, '1490335978', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('65', '3', '1', '1', '2', null, '1490335978', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('66', '5', '1', '-1', '1', null, '1490336075', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('67', '3', '1', '1', '2', null, '1490336075', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('68', '5', '1', '-1', '1', null, '1490336254', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('69', '3', '1', '1', '2', null, '1490336254', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('70', '5', '1', '-1', '1', null, '1490336494', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('71', '3', '1', '1', '2', null, '1490336494', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('72', '5', '1', '-5', '1', null, '1490336626', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('73', '3', '1', '5', '2', null, '1490336626', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('74', '5', '1', '-2', '1', null, '1490336683', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('75', '3', '1', '2', '2', null, '1490336683', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('76', '5', '1', '-10', '1', null, '1490336958', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('77', '3', '1', '10', '2', null, '1490336958', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('78', '3', '1', '-500', '1', null, '1490346659', null, '1', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('79', '1', '1', '500', '2', null, '1490346659', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('80', '3', '1', '-500', '1', null, '1490347061', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('81', '5', '1', '500', '2', null, '1490347061', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('82', '3', '1', '-200', '1', null, '1490575343', null, '2', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('83', '2', '1', '200', '2', null, '1490575343', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('84', '2', '1', '-1000', '1', null, '1490584666', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('85', '2', '1', '-2', '1', null, '1490584689', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('86', '4', '1', '-2', '1', null, '1490584849', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('87', '4', '1', '-2', '1', null, '1490584988', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('88', '4', '1', '-2', '1', null, '1490585111', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('89', '4', '1', '-2', '1', null, '1490585404', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('90', '4', '1', '-20', '1', null, '1490585671', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('91', '4', '1', '-2', '1', null, '1490585840', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('92', '4', '1', '-370', '1', null, '1490662516', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('93', '4', '1', '30', '2', null, '1490684561', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('94', '4', '1', '-200', '1', null, '1490684655', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('95', '5', '1', '-300', '1', null, '1490685800', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('96', '5', '1', '-200', '1', null, '1490686752', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('97', '5', '1', '500', '2', null, '1490833452', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('98', '4', '1', '570', '2', null, '1490845254', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('99', '4', '1', '-400', '1', null, '1490845543', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('100', '4', '1', '-200', '1', null, '1490853290', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('101', '4', '1', '-300', '1', null, '1490853589', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('102', '4', '1', '-40', '1', null, '1490853939', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('103', '2', '1', '-3000', '1', null, '1490855639', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('104', '4', '1', '148', '2', null, '1490860553', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('105', '4', '1', '23', '2', null, '1490860570', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('106', '4', '1', '23', '2', null, '1490860661', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('107', '4', '1', '745', '2', null, '1491439587', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('108', '2', '1', '4001', '2', null, '1491443574', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('109', '5', '1', '-500', '1', null, '1491464496', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('110', '5', '1', '420', '2', null, '1491464721', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('111', '5', '1', '-200', '1', null, '1491466204', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('112', '5', '1', '-200', '1', null, '1491466219', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('113', '5', '1', '49', '2', null, '1491466426', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('114', '5', '1', '-50', '1', null, '1491467948', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('115', '5', '1', '50', '2', null, '1491467999', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('116', '5', '1', '69', '2', null, '1491468932', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('117', '5', '1', '-140', '1', null, '1491469080', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('118', '5', '1', '140', '2', null, '1491469086', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('119', '5', '1', '-200', '1', null, '1491549987', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('120', '5', '1', '76', '2', null, '1491550002', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('121', '5', '1', '-100', '1', null, '1491550043', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('122', '5', '1', '100', '2', null, '1491550046', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('123', '5', '1', '-100', '1', null, '1491550256', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('124', '5', '1', '100', '2', null, '1491550260', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('125', '5', '1', '-111', '1', null, '1491550712', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('126', '5', '1', '111', '2', null, '1491550715', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('127', '5', '1', '-100', '1', null, '1491550956', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('128', '5', '1', '100', '2', null, '1491550959', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('129', '5', '1', '80', '2', null, '1491550975', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('130', '5', '1', '-200', '1', null, '1491551749', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('131', '5', '1', '200', '2', null, '1491551752', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('132', '5', '1', '282', '2', null, '1491552690', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('133', '5', '1', '-100', '1', null, '1491552986', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('134', '2', '1', '124', '2', null, '1491553076', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('135', '2', '1', '-3000', '1', null, '1491553222', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('136', '2', '1', '198', '2', null, '1491553233', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('137', '5', '1', '-100', '1', null, '1491610826', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('138', '5', '1', '100', '2', null, '1491610830', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('139', '5', '1', '-66', '1', null, '1491611253', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('140', '5', '1', '66', '2', null, '1491611256', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('141', '5', '1', '-200', '1', null, '1491611347', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('142', '5', '1', '200', '2', null, '1491611356', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('143', '5', '1', '-100', '1', null, '1491611448', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('144', '5', '1', '47', '2', null, '1491611452', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('145', '5', '1', '-111', '1', null, '1491612393', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('146', '5', '1', '111', '2', null, '1491612396', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('147', '5', '1', '-11', '1', null, '1491612519', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('148', '5', '1', '11', '2', null, '1491612533', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('149', '4', '1', '-100', '1', null, '1491621785', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('150', '4', '1', '100', '2', null, '1491621790', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('151', '4', '1', '-166', '1', null, '1491787593', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('152', '4', '1', '-166', '1', null, '1491787594', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('153', '4', '1', '-166', '1', null, '1491787594', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('154', '4', '1', '166', '2', null, '1491790913', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('155', '4', '1', '166', '2', null, '1491790931', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('156', '4', '1', '166', '2', null, '1491790968', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('157', '4', '1', '-100', '1', null, '1491791184', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('158', '4', '1', '100', '2', null, '1491795264', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('159', '4', '1', '-100', '1', null, '1491795407', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('160', '4', '1', '81', '2', null, '1491795427', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('161', '4', '1', '-111', '1', null, '1491795652', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('162', '4', '1', '111', '2', null, '1491795656', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('163', '5', '1', '153', '2', null, '1491803902', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('164', '5', '1', '19', '2', null, '1491803908', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('165', '5', '1', '-200', '1', null, '1491804661', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('166', '5', '1', '-200', '1', null, '1491804662', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('167', '5', '1', '113', '2', null, '1491804688', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('168', '4', '1', '87', '2', null, '1491804690', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('169', '5', '1', '71', '2', null, '1491804714', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('170', '4', '1', '71', '2', null, '1491804714', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('171', '5', '1', '129', '2', null, '1491804730', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('172', '5', '1', '-200', '1', null, '1491804752', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('173', '5', '1', '132', '2', null, '1491804757', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('174', '4', '1', '132', '2', null, '1491804757', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('175', '5', '1', '-200', '1', null, '1491804793', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('176', '5', '1', '25', '2', null, '1491804803', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('177', '4', '1', '175', '2', null, '1491804803', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('178', '5', '1', '-100', '1', null, '1491804833', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('179', '5', '1', '65', '2', null, '1491804839', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('180', '4', '1', '35', '2', null, '1491804839', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('181', '4', '1', '-200', '1', null, '1491804865', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('182', '5', '1', '129', '2', null, '1491804873', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('183', '4', '1', '71', '2', null, '1491804873', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('184', '5', '1', '-888', '1', null, '1491805412', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('185', '5', '1', '864', '2', null, '1491805428', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('186', '4', '1', '864', '2', null, '1491805428', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('187', '5', '1', '24', '2', null, '1491805446', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('188', '5', '1', '-200', '1', null, '1491805469', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('189', '4', '1', '56', '2', null, '1491805476', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('190', '5', '1', '56', '2', null, '1491805476', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('191', '4', '1', '144', '2', null, '1491805497', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('192', '5', '1', '-200', '1', null, '1491805615', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('193', '4', '1', '55', '2', null, '1491805620', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('194', '5', '1', '55', '2', null, '1491805621', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('195', '4', '1', '145', '2', null, '1491805633', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('196', '5', '1', '-300', '1', null, '1491805820', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('197', '5', '1', '116', '2', null, '1491805823', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('198', '4', '1', '184', '2', null, '1491805850', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('199', '2', '1', '-600', '1', null, '1491808778', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('200', '2', '1', '-600', '1', null, '1491811135', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('201', '2', '1', '156', '2', null, '1491814842', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('205', '5', '1', '30', '2', null, '1491872656', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('206', '5', '1', '39', '2', null, '1491872833', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('207', '2', '1', '32', '2', null, '1491872851', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('208', '5', '1', '67', '2', null, '1491872854', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('209', '2', '1', '20', '2', null, '1491872857', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('210', '2', '1', '-500', '1', null, '1491872957', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('211', '2', '1', '45', '2', null, '1491872967', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('212', '5', '1', '78', '2', null, '1491872973', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('213', '4', '1', '182', '2', null, '1491873366', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('214', '3', '1', '156', '2', null, '1491873417', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('215', '3', '1', '68', '2', null, '1491873446', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('216', '3', '1', '2802', '2', null, '1491873508', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('217', '4', '1', '-40000', '1', null, '1491874162', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('218', '5', '1', '24303', '2', null, '1491874172', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('219', '4', '1', '15697', '2', null, '1491874172', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('220', '5', '1', '-30000', '1', null, '1491874227', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('221', '4', '1', '10961', '2', null, '1491874318', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('222', '5', '1', '10793', '2', null, '1491874319', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('223', '2', '1', '8246', '2', null, '1491874325', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('224', '5', '1', '-30000', '1', null, '1491874400', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('225', '2', '1', '3870', '2', null, '1491874572', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('226', '4', '1', '9835', '2', null, '1491874572', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('227', '5', '1', '9835', '2', null, '1491874572', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('228', '4', '1', '16295', '2', null, '1491874777', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('229', '5', '1', '-8888', '1', null, '1491874984', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('230', '4', '1', '-2', '1', null, '1491875053', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('231', '4', '1', '2', '2', null, '1491875057', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('232', '2', '1', '-500', '1', null, '1491875297', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('233', '2', '1', '1', '2', null, '1491875305', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('234', '5', '1', '-888', '1', null, '1491876431', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('235', '4', '1', '304', '2', null, '1491876469', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('236', '1', '1', '304', '2', null, '1491876469', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('237', '5', '1', '146', '2', null, '1491876469', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('238', '5', '1', '-777', '1', null, '1491876598', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('239', '5', '1', '-777', '1', null, '1491876600', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('240', '1', '1', '115', '2', null, '1491876701', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('241', '5', '1', '595', '2', null, '1491876701', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('242', '4', '1', '115', '2', null, '1491876701', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('243', '5', '1', '139', '2', null, '1491877162', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('244', '5', '1', '-200', '1', null, '1491877334', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('245', '2', '1', '297', '2', null, '1491880600', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('246', '2', '1', '5954', '2', null, '1491880733', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('247', '2', '1', '67', '2', null, '1491880764', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('248', '5', '1', '200', '2', null, '1491880814', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('249', '5', '1', '-200', '1', null, '1491880839', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('250', '5', '1', '200', '2', null, '1491880843', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('251', '5', '1', '-120', '1', null, '1491881047', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('252', '5', '1', '120', '2', null, '1491881050', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('253', '2', '1', '237', '2', null, '1491881144', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('254', '5', '1', '-4444', '1', null, '1491881595', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('255', '5', '1', '-400', '1', null, '1491881807', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('256', '3', '1', '41', '2', null, '1491881906', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('257', '5', '1', '123', '2', null, '1491881906', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('258', '4', '1', '123', '2', null, '1491881907', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('259', '5', '1', '31', '2', null, '1491881924', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('260', '2', '1', '205', '2', null, '1491881934', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('261', '5', '1', '-500', '1', null, '1491881975', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('262', '2', '1', '283', '2', null, '1491881995', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('263', '4', '1', '283', '2', null, '1491881995', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('264', '3', '1', '283', '2', null, '1491881996', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('265', '5', '1', '283', '2', null, '1491881996', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('266', '4', '1', '125', '2', null, '1491882016', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('267', '3', '1', '45', '2', null, '1491882046', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('268', '2', '1', '-500', '1', null, '1491882322', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('269', '3', '1', '5', '2', null, '1491882483', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('270', '2', '1', '41', '2', null, '1491882500', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('271', '4', '1', '415', '2', null, '1491882501', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('272', '1', '1', '14', '2', null, '1491889877', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('273', '5', '1', '25', '2', null, '1491889926', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('274', '2', '1', '-500', '1', null, '1491889945', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('275', '4', '1', '147', '2', null, '1491890061', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('276', '1', '1', '141', '2', null, '1491890197', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('277', '2', '1', '-500', '1', null, '1491893007', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('278', '3', '1', '68', '2', null, '1491893438', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('279', '1', '1', '68', '2', null, '1491893438', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('280', '5', '1', '68', '2', null, '1491893438', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('281', '4', '1', '68', '2', null, '1491893439', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('282', '3', '1', '156', '2', null, '1491893483', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('283', '2', '1', '156', '2', null, '1491893483', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('284', '1', '1', '156', '2', null, '1491893483', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('285', '5', '1', '156', '2', null, '1491893483', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('286', '1', '1', '75', '2', null, '1491893762', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('287', '2', '1', '75', '2', null, '1491893763', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('288', '3', '1', '75', '2', null, '1491893763', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('289', '2', '1', '-500', '1', null, '1491893812', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('290', '1', '1', '255', '2', null, '1491893852', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('291', '4', '1', '255', '2', null, '1491893853', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('292', '2', '1', '32', '2', null, '1491893853', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('293', '5', '1', '32', '2', null, '1491893853', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('294', '3', '1', '32', '2', null, '1491893853', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('295', '2', '1', '120', '2', null, '1491894340', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('296', '1', '1', '120', '2', null, '1491894340', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('297', '5', '1', '120', '2', null, '1491894341', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('298', '2', '1', '-500', '1', null, '1491894460', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('299', '1', '1', '77', '2', null, '1491897224', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('300', '2', '1', '77', '2', null, '1491897224', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('301', '3', '1', '77', '2', null, '1491897224', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('302', '4', '1', '77', '2', null, '1491897224', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('303', '5', '1', '77', '2', null, '1491897224', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('304', '4', '1', '217', '2', null, '1491897302', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('305', '2', '1', '217', '2', null, '1491897302', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('306', '5', '1', '217', '2', null, '1491897302', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('307', '3', '1', '217', '2', null, '1491897302', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('308', '5', '1', '217', '2', null, '1491897385', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('309', '4', '1', '217', '2', null, '1491897385', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('310', '1', '1', '217', '2', null, '1491897385', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('311', '3', '1', '217', '2', null, '1491897385', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('312', '4', '1', '64', '2', null, '1491897558', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('313', '1', '1', '64', '2', null, '1491897558', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('314', '2', '1', '64', '2', null, '1491897558', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('315', '1', '1', '34', '2', null, '1491897675', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('316', '4', '1', '34', '2', null, '1491897676', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('317', '2', '1', '-500', '1', null, '1491897706', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('318', '2', '1', '10', '2', null, '1491897713', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('319', '1', '1', '10', '2', null, '1491897713', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('320', '3', '1', '10', '2', null, '1491897713', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('321', '5', '1', '10', '2', null, '1491897713', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('322', '4', '1', '10', '2', null, '1491897714', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('323', '2', '1', '11', '2', null, '1491897758', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('324', '5', '1', '11', '2', null, '1491897758', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('325', '1', '1', '11', '2', null, '1491897759', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('326', '3', '1', '11', '2', null, '1491897759', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('327', '1', '1', '115', '2', null, '1491897810', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('328', '2', '1', '115', '2', null, '1491897810', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('329', '5', '1', '115', '2', null, '1491897810', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('330', '2', '1', '252', '2', null, '1491897875', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('331', '1', '1', '252', '2', null, '1491897876', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('332', '2', '1', '112', '2', null, '1491897967', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('333', '2', '1', '-500', '1', null, '1491898016', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('334', '1', '1', '22', '2', null, '1491898023', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('335', '2', '1', '21', '2', null, '1491898023', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('336', '4', '1', '189', '2', null, '1491898023', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('337', '3', '1', '130', '2', null, '1491898023', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('338', '5', '1', '138', '2', null, '1491898023', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('339', '2', '1', '-500', '1', null, '1491899402', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('340', '1', '1', '122', '2', null, '1491899411', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('341', '2', '1', '122', '2', null, '1491899411', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('342', '3', '1', '122', '2', null, '1491899411', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('343', '4', '1', '130', '2', null, '1491899412', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('344', '5', '1', '90', '2', null, '1491899412', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('345', '2', '1', '-500', '1', null, '1491899485', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('346', '2', '1', '111', '2', null, '1491899493', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('347', '1', '1', '21', '2', null, '1491899493', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('348', '3', '1', '21', '2', null, '1491899493', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('349', '4', '1', '315', '2', null, '1491899494', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('350', '5', '1', '45', '2', null, '1491899494', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('351', '1', '1', '8', '2', null, '1491901085', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('352', '2', '1', '-500', '1', null, '1491901135', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('353', '2', '1', '30', '2', null, '1491901144', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('354', '3', '1', '30', '2', null, '1491901144', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('355', '1', '1', '106', '2', null, '1491901145', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('356', '4', '1', '30', '2', null, '1491901145', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('357', '5', '1', '64', '2', null, '1491901145', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('358', '3', '1', '270', '2', null, '1491901199', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('359', '2', '1', '-500', '1', null, '1491901215', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('360', '1', '1', '44', '2', null, '1491901222', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('361', '2', '1', '44', '2', null, '1491901222', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('362', '3', '1', '44', '2', null, '1491901222', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('363', '4', '1', '44', '2', null, '1491901222', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('364', '5', '1', '44', '2', null, '1491901222', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('365', '2', '1', '-500', '1', null, '1491901464', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('366', '1', '1', '257', '2', null, '1491901472', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('367', '2', '1', '118', '2', null, '1491901472', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('368', '4', '1', '118', '2', null, '1491901472', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('369', '5', '1', '118', '2', null, '1491901472', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('370', '3', '1', '73', '2', null, '1491901472', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('371', '2', '1', '39', '2', null, '1491901633', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('372', '4', '1', '39', '2', null, '1491901633', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('373', '2', '1', '-500', '1', null, '1491901736', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('374', '4', '1', '200', '2', null, '1491901816', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('375', '3', '1', '200', '2', null, '1491901817', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('376', '1', '1', '200', '2', null, '1491901816', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('377', '2', '1', '200', '2', null, '1491901817', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('378', '5', '1', '200', '2', null, '1491901817', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('379', '2', '1', '-500', '1', null, '1491902102', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('380', '1', '1', '187', '2', null, '1491902109', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('381', '2', '1', '187', '2', null, '1491902109', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('382', '3', '1', '187', '2', null, '1491902109', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('383', '4', '1', '187', '2', null, '1491902109', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('384', '5', '1', '187', '2', null, '1491902109', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('385', '1', '1', '111', '2', null, '1491902168', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('386', '2', '1', '63', '2', null, '1491902168', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('387', '3', '1', '21', '2', null, '1491902168', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('388', '4', '1', '21', '2', null, '1491902169', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('389', '2', '1', '-500', '1', null, '1491902234', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('390', '1', '1', '145', '2', null, '1491902240', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('391', '4', '1', '62', '2', null, '1491902240', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('392', '5', '1', '32', '2', null, '1491902240', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('393', '2', '1', '241', '2', null, '1491902240', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('394', '3', '1', '20', '2', null, '1491902240', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('395', '2', '1', '-500', '1', null, '1491902665', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('396', '1', '1', '124', '2', null, '1491902671', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('397', '2', '1', '15', '2', null, '1491902672', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('398', '3', '1', '15', '2', null, '1491902672', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('399', '5', '1', '15', '2', null, '1491902672', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('400', '4', '1', '15', '2', null, '1491902672', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('401', '2', '1', '-500', '1', null, '1491902895', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('402', '2', '1', '65', '2', null, '1491902901', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('403', '1', '1', '115', '2', null, '1491902902', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('404', '5', '1', '45', '2', null, '1491902902', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('405', '4', '1', '205', '2', null, '1491902902', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('406', '3', '1', '70', '2', null, '1491902902', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('407', '2', '1', '-500', '1', null, '1491903097', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('408', '1', '1', '125', '2', null, '1491903103', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('409', '2', '1', '85', '2', null, '1491903103', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('410', '3', '1', '38', '2', null, '1491903103', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('411', '4', '1', '129', '2', null, '1491903104', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('412', '5', '1', '123', '2', null, '1491903104', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('413', '2', '1', '-500', '1', null, '1491956468', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('414', '1', '1', '186', '2', null, '1491957810', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('415', '2', '1', '186', '2', null, '1491957810', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('416', '3', '1', '186', '2', null, '1491957810', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('417', '4', '1', '186', '2', null, '1491957810', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('418', '5', '1', '186', '2', null, '1491957810', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('419', '5', '1', '-200', '1', null, '1491958326', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('420', '1', '1', '84', '2', null, '1491958329', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('421', '2', '1', '30', '2', null, '1491958329', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('422', '4', '1', '90', '2', null, '1491958329', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('423', '5', '1', '90', '2', null, '1491958329', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('424', '5', '1', '158', '2', null, '1491958331', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('425', '2', '1', '-500', '1', null, '1491958360', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('426', '1', '1', '71', '2', null, '1491958366', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('427', '5', '1', '98', '2', null, '1491958366', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('428', '3', '1', '216', '2', null, '1491958366', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('429', '2', '1', '38', '2', null, '1491958366', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('430', '4', '1', '77', '2', null, '1491958366', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('431', '2', '1', '-500', '1', null, '1491958487', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('432', '4', '1', '232', '2', null, '1491958790', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('433', '1', '1', '87', '2', null, '1491958790', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('434', '2', '1', '94', '2', null, '1491958791', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('435', '3', '1', '75', '2', null, '1491958792', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('436', '2', '1', '-500', '1', null, '1491958820', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('437', '4', '1', '106', '2', null, '1491958871', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('438', '2', '1', '199', '2', null, '1491958912', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('439', '1', '1', '23', '2', null, '1491958913', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('440', '5', '1', '138', '2', null, '1491958913', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('441', '4', '1', '42', '2', null, '1491959657', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('442', '2', '1', '-1100', '1', null, '1491962013', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('443', '5', '1', '46', '2', null, '1491962150', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('444', '2', '1', '46', '2', null, '1491962249', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('445', '4', '1', '110', '2', null, '1491962249', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('446', '1', '1', '69', '2', null, '1491962250', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('447', '3', '1', '133', '2', null, '1491962299', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('448', '2', '1', '-1100', '1', null, '1491962854', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('449', '1', '1', '164', '2', null, '1491962959', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('450', '9', '1', '151', '2', null, '1491962959', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('451', '4', '1', '49', '2', null, '1491962959', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('452', '7', '1', '38', '2', null, '1491962960', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('453', '2', '1', '20', '2', null, '1491962960', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('454', '5', '1', '22', '2', null, '1491962960', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('455', '11', '1', '69', '2', null, '1491962993', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('456', '8', '1', '288', '2', null, '1491962997', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('457', '6', '1', '72', '2', null, '1491963009', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('458', '3', '1', '135', '2', null, '1491963032', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('459', '5', '1', '-300', '1', null, '1491963094', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('460', '2', '1', '-1100', '1', null, '1491963195', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('461', '1', '1', '9', '2', null, '1491963241', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('462', '3', '1', '241', '2', null, '1491963242', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('463', '2', '1', '46', '2', null, '1491963242', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('464', '7', '1', '20', '2', null, '1491963243', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('465', '4', '1', '157', '2', null, '1491963243', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('466', '8', '1', '113', '2', null, '1491963243', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('467', '6', '1', '72', '2', null, '1491963243', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('468', '5', '1', '72', '2', null, '1491963243', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('469', '11', '1', '164', '2', null, '1491963243', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('470', '9', '1', '99', '2', null, '1491963243', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('471', '10', '1', '107', '2', null, '1491963243', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('472', '5', '1', '113', '2', null, '1491968957', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('473', '5', '1', '7967', '2', null, '1491969241', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('474', '5', '1', '187', '2', null, '1492070458', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('475', '5', '1', '-100', '1', null, '1492480867', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('476', '5', '1', '91', '2', null, '1492480877', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('477', '2', '1', '9', '2', null, '1492504882', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('478', '3', '1', '-1', '1', null, '1498528799', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('479', '5', '1', '1', '2', null, '1498528799', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('480', '3', '1', '-1', '1', null, '1498529567', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('481', '5', '1', '1', '2', null, '1498529567', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('482', '3', '1', '-1', '1', null, '1498529640', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('483', '5', '1', '1', '2', null, '1498529640', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('484', '3', '1', '-1', '1', null, '1498529828', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('485', '5', '1', '1', '2', null, '1498529828', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('486', '3', '1', '-1', '1', null, '1498530301', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('487', '5', '1', '1', '2', null, '1498530301', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('488', '3', '1', '-1', '1', null, '1498530328', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('489', '5', '1', '1', '2', null, '1498530328', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('490', '5', '1', '-100', '1', null, '1498530912', null, '4', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('491', '4', '1', '100', '2', null, '1498530912', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('492', '5', '1', '-100', '1', null, '1498531369', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('493', '5', '1', '-700', '1', null, '1498531615', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('494', '5', '1', '-200', '1', null, '1498531800', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('495', '4', '1', '-100', '1', null, '1498531842', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('496', '5', '1', '-100', '1', null, '1498531946', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('497', '6', '1', '-230', '1', null, '1498533196', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('498', '6', '1', '-322', '1', null, '1498533296', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('499', '6', '1', '-290', '1', null, '1498533710', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('500', '5', '1', '-45', '1', null, '1498533932', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('501', '5', '1', '-333', '1', null, '1498546078', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('502', '5', '1', '-100', '1', null, '1498550604', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('503', '5', '1', '-400', '1', null, '1498550621', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('504', '5', '1', '-500', '1', null, '1498550631', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('505', '5', '1', '-600', '1', null, '1498550640', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('506', '5', '1', '-100', '1', null, '1498633474', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('507', '5', '1', '1478', '2', null, '1498634619', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('508', '5', '1', '1600', '2', null, '1498637152', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('509', '5', '1', '-100', '1', null, '1498638700', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('510', '5', '1', '-100', '1', null, '1498639631', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('511', '5', '1', '-3800', '1', null, '1498641996', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('512', '5', '1', '-200', '1', null, '1498700946', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('513', '5', '1', '-500', '1', null, '1498701636', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('514', '4', '1', '100', '2', null, '1498705215', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('515', '3', '1', '290', '2', null, '1498715662', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('516', '5', '1', '100', '2', null, '1498720477', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('517', '5', '1', '-333', '1', null, '1498725667', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('518', '3', '1', '-444', '1', null, '1498725736', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('519', '3', '1', '-200', '1', null, '1498725995', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('520', '5', '1', '200', '2', null, '1498725995', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('521', '5', '1', '200', '2', null, '1498728300', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('522', '5', '1', '4500', '2', null, '1498788378', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('523', '5', '1', '333', '2', null, '1498871844', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('524', '6', '1', '552', '2', null, '1500253726', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('525', '3', '1', '444', '2', null, '1500448127', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('526', '3', '1', '-100', '1', null, '1500514151', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('527', '3', '1', '-100', '1', null, '1500518695', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('528', '5', '1', '-100', '1', null, '1500519427', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('529', '6', '1', '-34400', '1', null, '1500519557', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('530', '6', '1', '-100', '1', null, '1500519747', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('531', '6', '1', '-200', '1', null, '1500519762', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('532', '6', '1', '-300', '1', null, '1500519778', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('533', '5', '1', '-355', '1', null, '1500521118', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('534', '5', '1', '-466', '1', null, '1500521136', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('535', '5', '1', '-100', '1', null, '1500538507', null, '6', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('536', '6', '1', '100', '2', null, '1500538507', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('537', '3', '1', '-100', '1', null, '1500599275', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('538', '3', '1', '100', '1', null, '1500599313', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('539', '3', '1', '-100', '1', null, '1500599442', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('540', '3', '1', '100', '1', null, '1500599459', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('541', '3', '1', '-100', '1', null, '1500605643', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('542', '3', '1', '100', '1', null, '1500605643', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('543', '5', '1', '254', '2', null, '1500609674', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('544', '5', '1', '-200', '1', null, '1500625182', null, '6', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('545', '6', '1', '200', '2', null, '1500625182', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('546', '5', '1', '-200', '1', null, '1500865398', null, '6', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('547', '6', '1', '200', '2', null, '1500865398', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('548', '3', '1', '-500', '1', null, '1500878635', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('549', '3', '1', '500', '1', null, '1500878635', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('550', '3', '1', '-500', '1', null, '1500883096', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('551', '3', '1', '500', '1', null, '1500883096', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('552', '3', '1', '-500', '1', null, '1500883206', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('553', '4', '1', '500', '1', null, '1500883206', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('554', '3', '1', '-200', '1', null, '1500947224', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('555', '3', '1', '200', '1', null, '1500947224', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('556', '3', '1', '-500', '1', null, '1500947872', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('557', '8', '1', '500', '1', null, '1500947872', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('558', '7', '1', '-100', '1', null, '1501029814', null, '6', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('559', '6', '1', '100', '2', null, '1501029814', null, null, '收到转账', '7');
INSERT INTO `guguo_take_cash` VALUES ('560', '7', '1', '-100', '1', null, '1501029980', null, '6', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('561', '6', '1', '100', '2', null, '1501029980', null, null, '收到转账', '7');
INSERT INTO `guguo_take_cash` VALUES ('562', '7', '1', '-100', '1', null, '1501031120', null, '6', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('563', '6', '1', '100', '2', null, '1501031120', null, null, '收到转账', '7');
INSERT INTO `guguo_take_cash` VALUES ('564', '7', '1', '-100', '1', null, '1501032325', null, '6', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('565', '6', '1', '100', '2', null, '1501032325', null, null, '收到转账', '7');
INSERT INTO `guguo_take_cash` VALUES ('566', '7', '1', '-100', '1', null, '1501032386', null, '6', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('567', '6', '1', '100', '2', null, '1501032386', null, null, '收到转账', '7');
INSERT INTO `guguo_take_cash` VALUES ('568', '7', '1', '-100', '1', null, '1501032455', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('569', '7', '1', '-200', '1', null, '1501032510', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('570', '7', '1', '-300', '1', null, '1501032748', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('571', '10', '1', '-200', '1', null, '1501033349', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('572', '10', '1', '-200', '1', null, '1501036239', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('573', '9', '1', '200', '2', null, '1501036239', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('574', '10', '1', '-200', '1', null, '1501036347', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('575', '9', '1', '200', '2', null, '1501036347', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('576', '10', '1', '-800', '1', null, '1501037821', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('577', '9', '1', '800', '2', null, '1501037821', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('578', '10', '1', '-200', '1', null, '1501038009', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('579', '9', '1', '200', '2', null, '1501038009', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('580', '10', '1', '-10', '1', null, '1501038421', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('581', '9', '1', '10', '2', null, '1501038421', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('582', '3', '1', '-500', '1', null, '1501040215', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('583', '6', '1', '500', '1', null, '1501040215', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('584', '10', '1', '-20', '1', null, '1501040959', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('585', '9', '1', '20', '2', null, '1501040959', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('586', '3', '1', '-500', '1', null, '1501040961', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('587', '7', '1', '500', '1', null, '1501040961', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('588', '3', '1', '-5500', '1', null, '1501041012', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('589', '3', '1', '5500', '1', null, '1501041012', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('590', '10', '1', '-10', '1', null, '1501041665', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('591', '9', '1', '10', '2', null, '1501041665', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('592', '10', '1', '-1520', '1', null, '1501051227', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('593', '10', '1', '-1400', '1', null, '1501053447', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('594', '10', '1', '178', '1', null, '1501053452', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('595', '10', '1', '-2000', '1', null, '1501053507', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('596', '10', '1', '13', '1', null, '1501053511', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('597', '3', '1', '-500', '1', null, '1501053971', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('598', '3', '1', '500', '1', null, '1501053971', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('599', '3', '1', '-500', '1', null, '1501054071', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('600', '3', '1', '500', '1', null, '1501054071', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('601', '3', '1', '-500', '1', null, '1501054125', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('602', '3', '1', '500', '1', null, '1501054125', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('603', '3', '1', '-500', '1', null, '1501054488', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('604', '3', '1', '500', '1', null, '1501054488', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('605', '3', '1', '-800', '1', null, '1501054501', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('606', '3', '1', '800', '1', null, '1501054501', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('607', '3', '1', '-86200', '1', null, '1501054517', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('608', '3', '1', '86200', '1', null, '1501054517', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('609', '10', '1', '-1800', '1', null, '1501057869', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('610', '10', '1', '900', '1', null, '1501058130', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('611', '3', '1', '-500', '1', null, '1501059202', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('612', '3', '1', '500', '1', null, '1501059202', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('613', '3', '1', '-900', '1', null, '1501059215', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('614', '3', '1', '900', '1', null, '1501059215', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('615', '3', '1', '-100', '1', null, '1501060745', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('616', '3', '1', '100', '1', null, '1501060745', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('617', '3', '1', '-100', '1', null, '1501060757', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('618', '3', '1', '100', '1', null, '1501060757', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('619', '3', '1', '-100', '1', null, '1501060805', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('620', '3', '1', '100', '1', null, '1501060805', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('621', '3', '1', '-100', '1', null, '1501061280', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('622', '3', '1', '100', '1', null, '1501061280', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('623', '10', '1', '-200', '1', null, '1501061676', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('624', '10', '1', '48', '1', null, '1501061680', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('625', '10', '1', '-500', '1', null, '1501114253', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('626', '10', '1', '-400', '1', null, '1501114345', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('627', '10', '1', '-200', '1', null, '1501114594', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('628', '10', '1', '-200', '1', null, '1501114893', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('629', '10', '1', '-100', '1', null, '1501114916', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('630', '10', '1', '-100', '1', null, '1501114990', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('631', '10', '1', '-100', '1', null, '1501115039', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('632', '9', '1', '100', '2', null, '1501115039', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('633', '10', '1', '-100', '1', null, '1501115227', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('634', '10', '1', '-200', '1', null, '1501115268', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('635', '10', '1', '-200', '1', null, '1501115737', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('636', '10', '1', '-100', '1', null, '1501115792', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('637', '10', '1', '-100', '1', null, '1501116261', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('638', '9', '1', '100', '2', null, '1501116261', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('639', '10', '1', '-100', '1', null, '1501116274', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('640', '10', '1', '100', '1', null, '1501116718', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('641', '10', '1', '-100', '1', null, '1501117331', null, '9', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('642', '9', '1', '100', '2', null, '1501117331', null, null, '收到转账', '10');
INSERT INTO `guguo_take_cash` VALUES ('643', '3', '1', '-100000', '1', null, '1501117925', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('644', '3', '1', '100000', '1', null, '1501117925', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('645', '10', '1', '-200', '1', null, '1501118194', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('646', '10', '1', '200', '2', null, '1501125473', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('647', '10', '1', '1520', '2', null, '1501138371', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('648', '10', '1', '-100', '1', null, '1501138642', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('649', '10', '1', '-100', '1', null, '1501138959', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('650', '3', '1', '76', '2', null, '1501138932', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('651', '3', '1', '9', '1', null, '1501139635', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('652', '10', '1', '91', '1', null, '1501139807', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('653', '10', '1', '99', '1', null, '1501139814', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('654', '10', '1', '182', '1', null, '1501139824', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('655', '3', '1', '-500', '1', null, '1501145287', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('656', '3', '1', '500', '1', null, '1501145287', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('657', '3', '1', '-500', '1', null, '1501145922', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('658', '3', '1', '500', '1', null, '1501145922', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('659', '3', '1', '-500', '1', null, '1501146137', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('660', '3', '1', '500', '1', null, '1501146137', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('661', '3', '1', '-500', '1', null, '1501146255', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('662', '3', '1', '500', '1', null, '1501146255', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('663', '3', '1', '-500', '1', null, '1501146337', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('664', '3', '1', '500', '1', null, '1501146337', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('665', '3', '1', '-500', '1', null, '1501146412', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('666', '3', '1', '500', '1', null, '1501146412', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('667', '3', '1', '-500', '1', null, '1501146574', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('668', '3', '1', '500', '1', null, '1501146574', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('669', '3', '1', '-500', '1', null, '1501146602', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('670', '3', '1', '500', '1', null, '1501146602', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('671', '3', '1', '-200', '1', null, '1501146628', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('672', '3', '1', '200', '1', null, '1501146628', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('673', '3', '1', '-500', '1', null, '1501146786', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('674', '3', '1', '500', '1', null, '1501146786', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('675', '3', '1', '-200', '1', null, '1501146819', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('676', '3', '1', '200', '1', null, '1501146819', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('677', '3', '1', '-500', '1', null, '1501146878', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('678', '3', '1', '500', '1', null, '1501146878', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('679', '3', '1', '-500', '1', null, '1501146974', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('680', '3', '1', '500', '1', null, '1501146974', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('681', '3', '1', '-500', '1', null, '1501147079', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('682', '3', '1', '500', '1', null, '1501147079', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('683', '3', '1', '-200', '1', null, '1501147109', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('684', '3', '1', '200', '1', null, '1501147109', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('685', '10', '1', '169', '1', null, '1501147116', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('686', '10', '1', '235', '1', null, '1501147158', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('687', '3', '1', '-500', '1', null, '1501147383', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('688', '4', '1', '500', '1', null, '1501147383', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('689', '3', '1', '-1000', '1', null, '1501147409', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('690', '4', '1', '1000', '1', null, '1501147409', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('691', '3', '1', '-8000', '1', null, '1501147433', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('692', '4', '1', '8000', '1', null, '1501147433', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('693', '3', '1', '-8000', '1', null, '1501147495', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('694', '4', '1', '8000', '1', null, '1501147495', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('695', '10', '1', '108', '1', null, '1501147526', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('696', '10', '1', '-200', '1', null, '1501147978', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('697', '10', '1', '24', '1', null, '1501147994', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('698', '3', '1', '-500', '1', null, '1501148017', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('699', '3', '1', '500', '1', null, '1501148017', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('700', '3', '1', '-500', '1', null, '1501148076', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('701', '3', '1', '500', '1', null, '1501148076', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('702', '3', '1', '-500', '1', null, '1501148102', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('703', '3', '1', '500', '1', null, '1501148102', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('704', '3', '1', '-500', '1', null, '1501148429', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('705', '3', '1', '500', '1', null, '1501148429', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('706', '5', '1', '176', '1', null, '1501148452', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('707', '5', '1', '1', '1', null, '1501148487', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('708', '3', '1', '-500', '1', null, '1501148602', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('709', '3', '1', '500', '1', null, '1501148602', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('710', '3', '1', '-500', '1', null, '1501148649', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('711', '3', '1', '500', '1', null, '1501148649', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('712', '3', '1', '-500', '1', null, '1501149164', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('713', '3', '1', '500', '1', null, '1501149164', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('714', '5', '1', '-200', '1', null, '1501202405', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('715', '3', '1', '-500', '1', null, '1501202514', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('716', '3', '1', '500', '1', null, '1501202514', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('717', '3', '1', '-500', '1', null, '1501203243', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('718', '3', '1', '500', '1', null, '1501203243', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('719', '3', '1', '-6500', '1', null, '1501205358', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('720', '2', '1', '6500', '1', null, '1501205358', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('721', '3', '1', '88', '2', null, '1501209858', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('722', '5', '1', '-33300', '1', null, '1501210846', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('723', '5', '1', '-2000', '1', null, '1501229303', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('724', '5', '1', '-600', '1', null, '1501288579', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('725', '5', '1', '200', '1', null, '1501288589', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('726', '5', '1', '200', '2', null, '1501289245', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('727', '5', '1', '-200', '1', null, '1501298728', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('728', '5', '1', '33300', '2', null, '1501298735', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('729', '5', '1', '2600', '2', null, '1501468330', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('730', '5', '1', '-500', '1', null, '1501469404', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('731', '3', '1', '-500', '1', null, '1501469683', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('732', '11', '1', '500', '1', null, '1501469683', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('733', '3', '1', '-55500', '1', null, '1501470371', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('734', '3', '1', '55500', '1', null, '1501470371', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('735', '5', '1', '-200', '1', null, '1501471575', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('736', '5', '1', '200', '1', null, '1501471581', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('737', '3', '1', '-500', '1', null, '1501491712', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('738', '3', '1', '500', '1', null, '1501491712', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('739', '3', '1', '-500', '1', null, '1501494193', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('740', '3', '1', '500', '1', null, '1501494193', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('741', '5', '1', '-200', '1', null, '1501548637', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('742', '3', '1', '-500', '1', null, '1501550145', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('743', '3', '1', '500', '1', null, '1501550145', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('744', '3', '1', '-500', '1', null, '1501551728', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('745', '3', '1', '500', '1', null, '1501551728', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('746', '3', '1', '-500', '1', null, '1501551753', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('747', '3', '1', '500', '1', null, '1501551753', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('748', '3', '1', '-500', '1', null, '1501552030', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('749', '3', '1', '500', '1', null, '1501552030', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('750', '3', '1', '-500', '1', null, '1501554406', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('751', '3', '1', '500', '1', null, '1501554406', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('752', '3', '1', '-500', '1', null, '1501554494', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('753', '3', '1', '500', '1', null, '1501554494', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('754', '3', '1', '-200', '1', null, '1501555256', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('755', '3', '1', '200', '1', null, '1501555256', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('756', '3', '1', '-2500', '1', null, '1501555282', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('757', '3', '1', '2500', '1', null, '1501555282', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('758', '3', '1', '-600', '1', null, '1501555298', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('759', '3', '1', '600', '1', null, '1501555298', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('760', '3', '1', '-800', '1', null, '1501559864', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('761', '3', '1', '800', '1', null, '1501559864', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('762', '3', '1', '-500', '1', null, '1501559964', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('763', '3', '1', '500', '1', null, '1501559964', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('764', '3', '1', '-500', '1', null, '1501568405', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('765', '3', '1', '500', '1', null, '1501568405', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('766', '3', '1', '-500', '1', null, '1501568997', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('767', '3', '1', '500', '1', null, '1501568997', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('768', '3', '1', '-500', '1', null, '1501574166', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('769', '3', '1', '500', '1', null, '1501574166', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('770', '5', '1', '500', '2', null, '1501576524', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('771', '5', '1', '-200', '1', null, '1501576620', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('772', '5', '1', '-200', '1', null, '1501576699', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('773', '5', '1', '21', '1', null, '1501576707', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('774', '9', '1', '-200', '1', null, '1501577312', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('775', '3', '1', '-500', '1', null, '1501577533', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('776', '3', '1', '500', '1', null, '1501577533', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('777', '3', '1', '-800', '1', null, '1501577556', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('778', '3', '1', '800', '1', null, '1501577556', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('779', '3', '1', '-600', '1', null, '1501637933', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('780', '3', '1', '600', '1', null, '1501637933', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('781', '5', '1', '200', '2', null, '1501640617', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('782', '5', '1', '379', '2', null, '1501665736', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('783', '3', '1', '-500', '1', null, '1501751035', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('784', '3', '1', '500', '1', null, '1501751035', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('785', '3', '1', '-500', '1', null, '1501815761', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('786', '3', '1', '500', '1', null, '1501815761', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('787', '5', '1', '-200', '1', null, '1502325566', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('788', '3', '1', '-500', '1', null, '1502329225', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('789', '3', '1', '500', '1', null, '1502329225', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('790', '5', '1', '200', '2', null, '1502412656', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('791', '3', '1', '-500', '1', null, '1502781255', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('792', '4', '1', '500', '1', null, '1502781255', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('793', '3', '2', '-1000', '1', null, '1502782642', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('794', '3', '2', '-1000', '1', null, '1502784225', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('795', '3', '2', '-1000', '1', null, '1502784255', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('796', '3', '2', '-1000', '1', null, '1502872547', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('797', '3', '1', '-100', '1', null, '1503302190', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('798', '4', '1', '100', '1', null, '1503302190', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('799', '3', '1', '-100', '1', null, '1503302204', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('800', '4', '1', '100', '1', null, '1503302204', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('801', '3', '1', '-100', '1', null, '1503363452', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('802', '4', '1', '100', '1', null, '1503363452', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('803', '5', '1', '-100', '1', null, '1503363896', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('804', '4', '1', '100', '1', null, '1503363896', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('805', '5', '1', '-200', '1', null, '1503363917', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('806', '4', '1', '200', '1', null, '1503363917', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('807', '5', '1', '-3100', '1', null, '1503364559', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('808', '4', '1', '3100', '1', null, '1503364559', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('809', '5', '1', '-100', '1', null, '1503364589', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('810', '4', '1', '100', '1', null, '1503364589', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('811', '2', '1', '11', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('812', '2', '1', '61', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('813', '2', '1', '61', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('814', '2', '1', '123', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('815', '2', '1', '192', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('816', '2', '1', '351', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('817', '2', '1', '56', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('818', '2', '1', '39', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('819', '2', '1', '7', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('820', '2', '1', '453', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('821', '2', '1', '10', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('822', '2', '1', '28', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('823', '2', '1', '88', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('824', '2', '1', '96', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('825', '2', '1', '28', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('826', '2', '1', '157', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('827', '2', '1', '44', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('828', '2', '1', '45', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('829', '2', '1', '48', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('830', '2', '1', '108', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('831', '2', '1', '91', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('832', '2', '1', '67', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('833', '2', '1', '131', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('834', '2', '1', '155', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('835', '2', '1', '101', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('836', '2', '1', '69', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('837', '2', '1', '13', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('838', '2', '1', '40', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('839', '2', '1', '41', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('840', '2', '1', '28', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('841', '2', '1', '190', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('842', '2', '1', '118', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('843', '2', '1', '333', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('844', '2', '1', '13', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('845', '2', '1', '110', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('846', '2', '1', '12', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('847', '2', '1', '34', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('848', '2', '1', '147', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('849', '2', '1', '62', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('850', '2', '1', '242', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('851', '2', '1', '54', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('852', '2', '1', '122', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('853', '2', '1', '154', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('854', '2', '1', '7', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('855', '7', '1', '100', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('856', '7', '1', '200', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('857', '7', '1', '300', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('858', '10', '1', '3', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('859', '10', '1', '141', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('860', '10', '1', '239', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('861', '10', '1', '63', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('862', '10', '1', '340', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('863', '10', '1', '241', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('864', '10', '1', '15', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('865', '10', '1', '102', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('866', '10', '1', '78', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('867', '10', '1', '1987', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('868', '10', '1', '900', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('869', '10', '1', '97', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('870', '10', '1', '55', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('871', '10', '1', '265', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('872', '10', '1', '200', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('873', '10', '1', '200', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('874', '10', '1', '176', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('875', '10', '1', '24', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('876', '10', '1', '18', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('877', '10', '1', '182', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('878', '10', '1', '67', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('879', '10', '1', '33', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('880', '10', '1', '100', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('881', '10', '1', '56', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('882', '10', '1', '44', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('883', '10', '1', '92', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('884', '10', '1', '31', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('885', '10', '1', '100', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('886', '10', '1', '18', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('887', '9', '1', '200', '2', null, '1503546297', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('888', '3', '1', '-100', '1', null, '1503556784', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('889', '3', '1', '-100', '1', null, '1503556910', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('890', '3', '1', '-100', '1', null, '1503557039', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('891', '3', '1', '100', '2', null, '1503557401', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('892', '3', '1', '100', '2', null, '1503557521', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('893', '3', '1', '100', '2', null, '1503557641', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('894', '5', '1', '-10000', '1', null, '1503558970', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('895', '5', '1', '10000', '2', null, '1503559622', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('896', '5', '1', '-1800', '1', null, '1503624797', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('897', '5', '1', '200', '1', null, '1503624803', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('898', '5', '1', '200', '2', null, '1503625441', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('899', '5', '1', '200', '2', null, '1503625441', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('900', '5', '1', '200', '2', null, '1503625441', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('901', '5', '1', '200', '2', null, '1503625441', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('902', '5', '1', '200', '2', null, '1503625441', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('903', '5', '1', '200', '2', null, '1503625441', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('904', '5', '1', '200', '2', null, '1503625441', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('905', '5', '1', '200', '2', null, '1503625441', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('906', '3', '2', '-1000', '1', null, '1503627344', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('907', '5', '1', '-100', '1', null, '1503628351', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('908', '5', '1', '100', '1', null, '1503628351', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('909', '5', '1', '-100', '1', null, '1503628671', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('910', '5', '1', '100', '1', null, '1503628671', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('911', '5', '1', '-100', '1', null, '1503628677', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('912', '4', '1', '100', '1', null, '1503628677', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('913', '5', '1', '-100', '1', null, '1503628743', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('914', '4', '1', '100', '1', null, '1503628743', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('915', '3', '2', '-1000', '1', null, '1503629012', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('916', '5', '1', '-200', '1', null, '1503629980', null, '8', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('917', '8', '1', '200', '2', null, '1503629980', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('918', '5', '1', '-100', '1', null, '1503642001', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('919', '4', '1', '100', '1', null, '1503642001', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('920', '5', '1', '-10', '1', null, '1503650421', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('921', '4', '1', '10', '1', null, '1503650421', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('922', '5', '1', '-2000', '1', null, '1503650659', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('923', '5', '1', '2000', '1', null, '1503650659', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('924', '5', '1', '-100', '1', null, '1503650676', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('925', '5', '1', '100', '1', null, '1503650676', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('926', '5', '1', '-908', '1', null, '1503651709', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('927', '5', '1', '908', '1', null, '1503651709', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('928', '5', '1', '-2000', '1', null, '1503705343', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('929', '5', '1', '2000', '2', null, '1503705962', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('930', '5', '1', '-200', '1', null, '1503997463', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('931', '5', '1', '200', '1', null, '1503997463', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('932', '5', '1', '-10000', '1', null, '1504137842', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('933', '5', '1', '10000', '2', null, '1504138501', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('934', '5', '1', '-100', '1', null, '1504139374', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('935', '4', '1', '100', '1', null, '1504139374', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('936', '5', '1', '-10000', '1', null, '1504139981', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('937', '4', '1', '10000', '1', null, '1504140293', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('938', '5', '1', '-200', '1', null, '1504228616', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('939', '6', '1', '200', '1', null, '1504228626', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('940', '5', '1', '-200', '1', null, '1504228656', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('941', '6', '1', '200', '1', null, '1504228675', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('942', '5', '1', '-200', '1', null, '1504228711', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('943', '5', '1', '-200', '1', null, '1504228767', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('944', '5', '1', '200', '2', null, '1504229341', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('945', '5', '1', '200', '2', null, '1504229401', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('946', '5', '1', '-200', '1', null, '1504247268', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('947', '5', '1', '199', '1', null, '1504247273', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('948', '5', '1', '-200', '1', null, '1504247311', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('949', '5', '1', '88', '1', null, '1504247656', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('950', '5', '1', '-200', '1', null, '1504247746', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('951', '5', '1', '-200', '1', null, '1504247769', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('952', '5', '1', '200', '1', null, '1504247778', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('953', '5', '1', '-200', '1', null, '1504247877', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('954', '5', '1', '1', '2', null, '1504247881', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('955', '5', '1', '112', '2', null, '1504247941', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('956', '5', '1', '200', '2', null, '1504248361', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('957', '5', '1', '200', '2', null, '1504248481', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('958', '5', '1', '-200', '1', null, '1504248813', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('959', '5', '1', '200', '1', null, '1504248819', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('960', '5', '1', '-200', '1', null, '1504249024', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('961', '5', '1', '200', '1', null, '1504249029', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('962', '5', '1', '-200', '1', null, '1504249220', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('963', '5', '1', '200', '1', null, '1504249226', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('964', '5', '1', '-200', '1', null, '1504249322', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('965', '5', '1', '200', '2', null, '1504249981', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('966', '2', '1', '-5400', '1', null, '1504493007', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('967', '6', '1', '5400', '1', null, '1504493007', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('968', '3', '2', '-400', '1', null, '1504507127', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('969', '3', '2', '-400', '1', null, '1504507151', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('970', '3', '2', '-400', '1', null, '1504507897', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('971', '3', '2', '-400', '1', null, '1504508189', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('972', '2', '1', '-2000', '1', null, '1504570538', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('973', '6', '1', '2000', '1', null, '1504570538', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('974', '5', '1', '-100', '1', null, '1504579223', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('975', '5', '1', '-100', '1', null, '1504579229', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('976', '5', '1', '-100', '1', null, '1504579257', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('977', '5', '1', '100', '2', null, '1504579862', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('978', '5', '1', '100', '2', null, '1504579862', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('979', '5', '1', '100', '2', null, '1504579862', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('980', '5', '1', '-100', '1', null, '1504586592', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('981', '5', '1', '-100', '1', null, '1504586598', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('982', '5', '1', '100', '2', null, '1504587241', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('983', '5', '1', '100', '2', null, '1504587241', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('984', '5', '1', '-200', '1', null, '1504592499', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('985', '5', '1', '-100', '1', null, '1504592561', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('986', '5', '1', '-100', '1', null, '1504592611', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('987', '3', '1', '-100', '1', null, '1504592855', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('988', '5', '1', '200', '2', null, '1504593121', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('989', '3', '1', '-100', '1', null, '1504593121', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('990', '5', '1', '100', '2', null, '1504593181', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('991', '3', '1', '-1', '1', null, '1504593221', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('992', '5', '1', '1', '2', null, '1504593221', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('993', '5', '1', '100', '2', null, '1504593241', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('994', '5', '1', '-200', '1', null, '1504593444', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('995', '5', '1', '-100', '1', null, '1504593471', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('996', '5', '1', '100', '1', null, '1504593477', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('997', '3', '1', '100', '2', null, '1504593481', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('998', '3', '1', '-1', '1', null, '1504593591', null, '5', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('999', '5', '1', '1', '2', null, '1504593591', null, null, '收到转账', '3');
INSERT INTO `guguo_take_cash` VALUES ('1000', '3', '1', '100', '2', null, '1504593781', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('1001', '5', '1', '200', '2', null, '1504594081', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('1002', '5', '1', '-100', '1', null, '1504594128', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1003', '5', '1', '100', '2', null, '1504594741', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('1004', '5', '1', '-100', '1', null, '1504594862', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1005', '5', '1', '-100', '1', null, '1504595065', null, '4', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('1006', '4', '1', '100', '2', null, '1504595065', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('1007', '5', '1', '100', '2', null, '1504595521', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('1008', '5', '1', '-100', '1', null, '1504596270', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1009', '5', '1', '-100', '1', null, '1504596893', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1010', '5', '1', '100', '2', null, '1504596901', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('1011', '5', '1', '100', '2', null, '1504597501', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('1012', '5', '1', '-100', '1', null, '1504597550', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1013', '5', '1', '100', '1', null, '1504597560', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1014', '2', '1', '-9000', '1', null, '1504924415', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1015', '4', '1', '9000', '1', null, '1504924415', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1016', '2', '1', '-9000', '1', null, '1504924575', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1017', '4', '1', '9000', '1', null, '1504924575', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1018', '2', '1', '-10000', '1', null, '1505113659', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1019', '2', '1', '-10000', '1', null, '1505113799', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1020', '3', '1', '-100', '1', null, '1505115702', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1021', '8', '1', '-200', '1', null, '1505178511', null, '4', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('1022', '4', '1', '200', '2', null, '1505178511', null, null, '收到转账', '8');
INSERT INTO `guguo_take_cash` VALUES ('1023', '8', '1', '-10000', '1', null, '1505178530', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1024', '4', '1', '10000', '1', null, '1505178530', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1025', '8', '1', '-10000', '1', null, '1505178559', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1026', '4', '1', '10000', '1', null, '1505178559', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1027', '2', '1', '-10000', '1', null, '1505181215', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1028', '2', '1', '-10000', '1', null, '1505181247', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1029', '2', '1', '-10000', '1', null, '1505181351', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1030', '2', '1', '-10000', '1', null, '1505185686', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1031', '2', '1', '-10000', '1', null, '1505185967', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1032', '2', '1', '-10000', '1', null, '1505186052', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1033', '2', '1', '-10000', '1', null, '1505186091', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1034', '2', '1', '-10000', '1', null, '1505186366', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1035', '2', '1', '-10000', '1', null, '1505187346', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1036', '2', '1', '-10000', '1', null, '1505187431', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1037', '2', '1', '-10000', '1', null, '1505187504', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1038', '2', '1', '-10000', '1', null, '1505187543', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1039', '2', '1', '-10000', '1', null, '1505187690', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1040', '2', '1', '-10000', '1', null, '1505198148', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1041', '2', '1', '-5000', '1', null, '1505266582', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1042', '2', '1', '-5000', '1', null, '1505266871', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1043', '2', '1', '-5000', '1', null, '1505266901', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1044', '3', '2', '-30', '1', null, '1505273153', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1045', '3', '2', '-20', '1', null, '1505273260', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1046', '3', '2', '-50', '1', null, '1505273260', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1047', '3', '2', '-20', '1', null, '1505273260', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1048', '3', '2', '-4', '1', null, '1505273260', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1049', '3', '2', '-2', '1', null, '1505273447', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1050', '5', '2', '-14700', '1', null, '1505296242', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1051', '4', '1', '14700', '1', null, '1505296242', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1052', '5', '2', '-100', '1', null, '1505296590', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1053', '4', '1', '100', '1', null, '1505296590', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1054', '5', '2', '-100', '1', null, '1505296930', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1055', '4', '1', '100', '1', null, '1505296930', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1056', '4', '1', '-500', '1', null, '1505359002', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1057', '4', '1', '500', '1', null, '1505359002', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1058', '4', '1', '-500', '1', null, '1505359206', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1059', '4', '1', '500', '1', null, '1505359206', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1060', '4', '1', '-150000', '1', null, '1505359346', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1061', '5', '1', '150000', '1', null, '1505359346', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1062', '5', '1', '-100', '1', null, '1505359668', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1063', '4', '1', '100', '1', null, '1505359668', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1064', '2', '1', '-10000', '1', null, '1505361512', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1065', '4', '1', '10000', '1', null, '1505361512', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1066', '2', '1', '-10000', '1', null, '1505371742', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1067', '4', '1', '10000', '1', null, '1505371742', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1068', '4', '1', '-100000', '1', null, '1505371791', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1069', '4', '1', '100000', '1', null, '1505371791', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1070', '4', '1', '-100000', '1', null, '1505372493', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1071', '4', '1', '100000', '1', null, '1505372493', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1072', '4', '1', '-500', '1', null, '1505382853', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1073', '4', '1', '500', '1', null, '1505382853', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1074', '4', '1', '-10000', '1', null, '1505443963', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1075', '4', '1', '10000', '2', null, '1505444581', null, null, '红包到期返还', null);
INSERT INTO `guguo_take_cash` VALUES ('1076', '3', '2', '-400', '1', null, '1505786625', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1079', '3', '1', '-200', '1', null, '1505787088', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1081', '3', '2', '-1000', '1', null, '1505787258', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1082', '3', '2', '-1000', '1', null, '1505787710', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1083', '3', '2', '-10000', '1', null, '1505789565', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1084', '3', '2', '-10000', '1', null, '1505789850', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1085', '3', '1', '-2', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1086', '3', '1', '-1', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1087', '3', '1', '-1', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1088', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1089', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1090', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1092', '3', '1', '-2', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1093', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1095', '3', '1', '-5', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1096', '3', '1', '-5', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1097', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1098', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1102', '3', '1', '-10', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1103', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1105', '3', '1', '-10', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1106', '3', '1', '-10', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1107', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1108', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1109', '3', '1', '-80', '1', null, '1505791982', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('1110', '3', '1', '80', '1', null, '1505791982', null, null, '任务发放结余解冻', null);
INSERT INTO `guguo_take_cash` VALUES ('1112', '3', '1', '-10', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1113', '3', '1', '-10', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1114', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1115', '3', '1', '0', '1', null, '1505791982', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1116', '3', '1', '-80', '1', null, '1505791982', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('1117', '3', '1', '80', '1', null, '1505791982', null, null, '任务发放结余解冻', null);
INSERT INTO `guguo_take_cash` VALUES ('1119', '3', '2', '-10000', '1', null, '1505800868', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1120', '3', '1', '-10', '1', null, '1505800871', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1121', '3', '1', '-10', '1', null, '1505800871', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1122', '3', '1', '0', '1', null, '1505800871', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1123', '3', '1', '0', '1', null, '1505800871', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1124', '3', '1', '-80', '1', null, '1505800871', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('1125', '3', '1', '80', '1', null, '1505800871', null, null, '任务发放结余解冻', null);
INSERT INTO `guguo_take_cash` VALUES ('1127', '3', '2', '-10000', '1', null, '1505801451', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1128', '3', '1', '-10', '1', null, '1505801510', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1129', '3', '1', '-10', '1', null, '1505801510', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1130', '3', '1', '-80', '1', null, '1505801510', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('1131', '3', '1', '80', '1', null, '1505801510', null, null, '任务发放结余解冻', null);
INSERT INTO `guguo_take_cash` VALUES ('1135', '3', '2', '-10000', '1', null, '1505801561', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1136', '3', '1', '-10', '1', null, '1505801563', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1137', '3', '1', '-10', '1', null, '1505801563', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1138', '3', '1', '-80', '1', null, '1505801563', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('1139', '3', '1', '80', '1', null, '1505801563', null, null, '任务发放结余解冻', null);
INSERT INTO `guguo_take_cash` VALUES ('1143', '3', '2', '-10000', '1', null, '1505802856', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1144', '3', '1', '-1000', '1', null, '1505802872', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1145', '3', '2', '-10', '1', null, '1505803221', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1146', '3', '2', '-10', '1', null, '1505803221', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1147', '3', '2', '-5', '1', null, '1505803221', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1148', '3', '2', '-5', '1', null, '1505803221', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1149', '3', '2', '-80', '1', null, '1505803221', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('1150', '3', '2', '80', '1', null, '1505803221', null, null, '任务发放结余解冻', null);
INSERT INTO `guguo_take_cash` VALUES ('1152', '3', '1', '-200', '1', null, '1505803285', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1153', '3', '1', '-1000', '1', null, '1505803325', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1154', '3', '1', '-1000', '1', null, '1505803339', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1155', '5', '1', '-1000', '1', null, '1505803626', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1156', '3', '1', '-2', '1', null, '1505803636', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1157', '3', '1', '-10', '1', null, '1505803636', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1158', '3', '1', '-10', '1', null, '1505803636', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1159', '3', '1', '-200', '1', null, '1505804299', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1160', '3', '1', '-1000', '1', null, '1505804363', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1161', '5', '1', '-1000', '1', null, '1505804382', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1162', '3', '1', '-2', '1', null, '1505804584', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1163', '3', '1', '-10', '1', null, '1505804584', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1164', '3', '1', '-10', '1', null, '1505804584', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1165', '3', '1', '-400', '1', null, '1505805106', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1166', '3', '1', '-1000', '1', null, '1505805131', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1167', '3', '1', '-2', '1', null, '1505808237', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1168', '3', '1', '-10', '1', null, '1505808237', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1169', '3', '1', '2', '1', null, '1505808237', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('1170', '3', '1', '-400', '1', null, '1505808662', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1171', '3', '1', '-1000', '1', null, '1505808739', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1172', '3', '1', '-2', '1', null, '1505808779', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1173', '3', '1', '-10', '1', null, '1505808779', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1174', '3', '1', '2', '1', null, '1505808779', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('1175', '3', '1', '-400', '1', null, '1505810114', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1176', '3', '1', '-1000', '1', null, '1505810137', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1177', '3', '1', '-2', '1', null, '1505810275', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1178', '3', '1', '-2', '1', null, '1505810275', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1179', '3', '1', '-5', '1', null, '1505810275', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1180', '3', '1', '-5', '1', null, '1505810275', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('1184', '3', '1', '-400', '1', null, '1505953221', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1185', '3', '1', '-400', '1', null, '1505957776', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1186', '3', '1', '-400', '1', null, '1505958054', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1187', '4', '1', '-80000', '1', null, '1505959383', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1188', '4', '2', '-80000', '1', null, '1505964252', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1189', '4', '2', '-800', '1', null, '1505965119', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1190', '3', '1', '-600', '1', null, '1506044895', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1191', '3', '2', '-200', '1', null, '1506045325', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1192', '3', '1', '-600', '1', null, '1506047673', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1193', '12', '1', '-3000', '1', null, '1506063485', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1194', '3', '1', '-12300', '1', null, '1506063597', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1195', '3', '1', '-100', '1', null, '1506063763', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1196', '4', '1', '-200', '1', null, '1506064184', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1197', '4', '1', '-500', '1', null, '1506066345', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1198', '3', '1', '-200', '1', null, '1506066674', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1199', '3', '1', '-300', '1', null, '1506067447', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1200', '3', '1', '-400', '1', null, '1506067688', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1201', '4', '1', '-800', '1', null, '1506067861', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1202', '3', '1', '-7400', '1', null, '1506068677', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('1203', '6', '1', '7400', '1', null, '1506068677', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('1204', '4', '1', '-4000', '1', null, '1506071892', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('1753', '5', '1', '-20000', '1', null, '1506132894', null, null, '创建红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1758', '5', '1', '-100', '1', null, '1506132919', null, '3', '从余额转出', null);
INSERT INTO `guguo_take_cash` VALUES ('1759', '3', '1', '100', '2', null, '1506132919', null, null, '收到转账', '5');
INSERT INTO `guguo_take_cash` VALUES ('1760', '3', '1', '20000', '1', null, '1506132993', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('1793', '3', '1', '1000', '1', null, '1506133467', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('2006', '3', '1', '200', '1', null, '1506136617', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('2019', '3', '1', '1000', '1', null, '1506136794', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('2020', '3', '1', '200', '1', null, '1506136799', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('2025', '3', '1', '1000', '1', null, '1506136843', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('2030', '3', '1', '200', '1', null, '1506136937', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('2447', '3', '1', '100', '1', null, '1506143161', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('2460', '3', '2', '-600', '1', null, '1506143375', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('2469', '3', '1', '-100', '1', null, '1506143451', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('3108', '3', '2', '-1000', '1', null, '1506147236', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('3127', '1', '2', '-1', '1', null, '1506147421', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('3128', '1', '2', '1', '1', null, '1506147421', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('3130', '2', '2', '-2', '1', null, '1506147421', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('3131', '2', '2', '2', '1', null, '1506147421', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('3133', '3', '1', '-10', '1', null, '1506147421', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('3134', '3', '1', '-10', '1', null, '1506147421', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('3135', '3', '1', '80', '1', null, '1506147421', null, null, '任务发放结余', null);
INSERT INTO `guguo_take_cash` VALUES ('3850', '4', '1', '-4000', '1', null, '1506150226', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('3879', '4', '2', '-4000', '1', null, '1506150318', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('3922', '4', '1', '-500', '1', null, '1506150507', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4175', '3', '2', '-1000', '1', null, '1506151610', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4206', '4', '1', '-44000', '1', null, '1506151707', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4263', '3', '1', '-600', '1', null, '1506151934', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4320', '3', '2', '-1000', '1', null, '1506152168', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4349', '3', '2', '-1000', '1', null, '1506152287', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4378', '3', '2', '-600', '1', null, '1506152460', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4449', '3', '1', '-600', '1', null, '1506152735', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4478', '3', '1', '-200', '1', null, '1506152852', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4493', '4', '1', '-200', '1', null, '1506152908', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('4494', '4', '1', '200', '1', null, '1506152908', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('4565', '3', '1', '-100', '1', null, '1506153202', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4580', '4', '1', '-44800', '1', null, '1506153299', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4595', '3', '1', '-200', '1', null, '1506153342', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4624', '4', '1', '-70400', '1', null, '1506153476', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4667', '3', '1', '-8800', '1', null, '1506153619', null, null, '参与任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4682', '4', '1', '-20000', '1', null, '1506153676', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('4683', '4', '1', '20000', '1', null, '1506153676', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('4796', '3', '1', '-2000', '1', null, '1506154194', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4853', '4', '1', '-1000', '1', null, '1506154417', null, null, '参与任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4924', '5', '1', '-1000', '1', null, '1506154684', null, null, '参与任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4967', '7', '1', '-30000', '1', null, '1506154915', null, null, '猜输赢任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4996', '4', '1', '-200000', '1', null, '1506154997', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('4997', '3', '1', '4', '1', null, '1506158684', null, null, '任务失败退回', null);
INSERT INTO `guguo_take_cash` VALUES ('4998', '3', '1', '4', '1', null, '1506158684', null, null, '任务失败退回', null);
INSERT INTO `guguo_take_cash` VALUES ('4999', '3', '1', '4', '1', null, '1506158684', null, null, '任务失败退回', null);
INSERT INTO `guguo_take_cash` VALUES ('5000', '4', '1', '40', '1', null, '1506158684', null, null, '任务失败退回', null);
INSERT INTO `guguo_take_cash` VALUES ('5001', '3', '2', '-3', '1', null, '1506158684', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5002', '3', '2', '-3', '1', null, '1506158684', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5003', '3', '2', '-1', '1', null, '1506158684', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5004', '3', '2', '-1', '1', null, '1506158684', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5008', '3', '2', '-10', '1', null, '1506158684', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5009', '3', '2', '-3', '1', null, '1506158684', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5010', '3', '2', '-3', '1', null, '1506158684', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5011', '7', '1', '300', '1', null, '1506158853', null, null, '猜输赢任务失败退回', null);
INSERT INTO `guguo_take_cash` VALUES ('5012', '3', '1', '-10', '1', null, '1506158853', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5013', '3', '1', '-10', '1', null, '1506158853', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5014', '3', '1', '-10', '1', null, '1506158853', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5015', '3', '1', '-667', '1', null, '1506158853', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5016', '3', '1', '-667', '1', null, '1506158853', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5017', '3', '1', '-667', '1', null, '1506158853', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5018', '3', '1', '1000', '1', null, '1506159074', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5019', '3', '1', '-600', '1', null, '1506160713', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5020', '3', '1', '2', '1', null, '1506239221', null, null, '任务失败退回', null);
INSERT INTO `guguo_take_cash` VALUES ('5021', '3', '1', '66668', '1', null, '1506301404', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5022', '3', '1', '1000', '1', null, '1506302653', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5023', '8', '1', '1000', '1', null, '1506302848', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5024', '4', '1', '-46400', '1', null, '1506303317', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5025', '12', '2', '-1000', '1', null, '1506305763', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5026', '12', '1', '-100', '1', null, '1506305883', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5027', '12', '2', '-1000', '1', null, '1506306117', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5028', '12', '2', '-2', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5029', '12', '2', '-2', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5030', '12', '2', '-2', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5031', '12', '2', '-2', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5032', '12', '2', '-2', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5033', '12', '2', '0', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5034', '12', '2', '0', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5035', '12', '2', '0', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5036', '12', '2', '0', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5037', '12', '2', '0', '1', null, '1506306601', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5043', '4', '1', '-600', '1', null, '1506307492', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5044', '4', '1', '-600', '1', null, '1506307518', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5045', '4', '1', '-600', '1', null, '1506307557', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5046', '4', '1', '-500', '1', null, '1506310027', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5047', '4', '1', '-500', '1', null, '1506310315', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5048', '4', '1', '-600', '1', null, '1506310358', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5049', '12', '1', '200', '1', null, '1506310490', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5050', '4', '1', '-500', '1', null, '1506310669', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5051', '12', '1', '20', '1', null, '1506311114', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5052', '12', '2', '-1000', '1', null, '1506311258', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5053', '12', '2', '-2', '1', null, '1506311341', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5054', '12', '2', '-2', '1', null, '1506311341', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5055', '12', '2', '-2', '1', null, '1506311341', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5056', '12', '2', '-2', '1', null, '1506311341', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5057', '12', '2', '-2', '1', null, '1506311341', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5060', '12', '1', '200', '1', null, '1506311395', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5061', '4', '1', '-500', '1', null, '1506311452', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5062', '4', '1', '-500', '1', null, '1506311461', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5063', '4', '1', '-500', '1', null, '1506311502', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5064', '12', '2', '-1000', '1', null, '1506311657', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5065', '12', '2', '-2', '1', null, '1506311701', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5066', '12', '2', '-2', '1', null, '1506311701', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5067', '12', '2', '-2', '1', null, '1506311701', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5068', '12', '2', '-2', '1', null, '1506311701', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5069', '12', '2', '-2', '1', null, '1506311701', null, null, '任务奖励发放', null);
INSERT INTO `guguo_take_cash` VALUES ('5072', '12', '1', '200', '1', null, '1506311757', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5073', '4', '1', '-500', '1', null, '1506311826', null, null, '打赏任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5074', '3', '1', '333', '1', null, '1506472662', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5075', '12', '2', '-1000', '1', null, '1506473649', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5076', '5', '1', '-200', '1', null, '1506474816', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('5077', '4', '1', '200', '1', null, '1506474816', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('5078', '8', '1', '200', '1', null, '1506475979', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5079', '8', '1', '200', '1', null, '1506476020', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5080', '3', '1', '300', '1', null, '1506484541', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5081', '4', '1', '-100', '1', null, '1506498564', null, null, '打赏用户', null);
INSERT INTO `guguo_take_cash` VALUES ('5082', '4', '1', '100', '1', null, '1506498564', null, null, '收到打赏', null);
INSERT INTO `guguo_take_cash` VALUES ('5083', '3', '1', '-40000', '1', null, '1506503246', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5084', '3', '1', '1000', '1', null, '1506557727', null, null, '领取红包', null);
INSERT INTO `guguo_take_cash` VALUES ('5085', '3', '2', '-200', '1', null, '1506563660', null, null, '发起任务', null);
INSERT INTO `guguo_take_cash` VALUES ('5086', '3', '1', '-600', '1', null, '1506563800', null, null, '发起任务', null);

-- ----------------------------
-- Table structure for guguo_talk_article
-- ----------------------------
DROP TABLE IF EXISTS `guguo_talk_article`;
CREATE TABLE `guguo_talk_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_name` varchar(255) NOT NULL COMMENT '话术名称',
  `article_type` int(11) NOT NULL DEFAULT '1' COMMENT '话术类型1，原创 2，引用',
  `article_class` int(11) NOT NULL COMMENT '文章分类',
  `article_content` varchar(255) NOT NULL COMMENT '简介',
  `article_url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `article_text` text COMMENT '正文',
  `article_is_top` int(11) DEFAULT '0' COMMENT '是否置顶 0 不置顶 1置顶',
  `article_start_top_time` int(11) DEFAULT NULL COMMENT '置顶开始时间',
  `article_end_top_time` int(11) DEFAULT NULL COMMENT '置顶结束时间',
  `article_start_show_time` int(11) DEFAULT NULL COMMENT '显示开始时间',
  `article_end_show_time` int(11) DEFAULT NULL COMMENT '显示结束时间',
  `article_release_type` int(11) NOT NULL DEFAULT '0' COMMENT '发布类型 0立即发布 1定时发布',
  `article_release_time` int(11) DEFAULT NULL COMMENT '发布时间',
  `article_creat_time` int(11) NOT NULL COMMENT '创建时间',
  `article_edit_time` int(11) NOT NULL COMMENT '修改时间',
  `add_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_talk_article
-- ----------------------------
INSERT INTO `guguo_talk_article` VALUES ('3', '231', '1', '1', '123', '', '32131', '0', '0', '0', '0', '0', '0', '0', '1503371277', '1503371277', '2');
INSERT INTO `guguo_talk_article` VALUES ('4', '123', '2', '1', '123123', '123123', '', '0', '0', '0', '0', '0', '0', '0', '1503371581', '1503371581', '2');
INSERT INTO `guguo_talk_article` VALUES ('6', '引用测试', '2', '3', '引用测试', 'www.baidu.com', '', '0', '0', '0', '0', '0', '0', '0', '1503383612', '1503383612', '2');
INSERT INTO `guguo_talk_article` VALUES ('7', '测试能否一次提交成功', '1', '4', '123', '', '0987654', '0', '0', '0', '0', '0', '0', '0', '1503386414', '1503386414', '2');
INSERT INTO `guguo_talk_article` VALUES ('8', '测试置顶时间', '1', '5', '测试置顶时间', '', '测试置顶时间', '1', '1403375120', '1404152720', '0', '0', '0', '0', '1503394783', '1503394783', '2');
INSERT INTO `guguo_talk_article` VALUES ('9', '时间', '1', '2', '1321', '', '43242', '1', '1503502320', '1504185780', '0', '0', '0', '0', '1503535671', '1503535671', '2');
INSERT INTO `guguo_talk_article` VALUES ('10', 'url', '2', '4', 'www.baidu.com', 'www.baidu.com', '', '0', '0', '0', '0', '0', '0', '0', '1503535936', '1503535936', '2');
INSERT INTO `guguo_talk_article` VALUES ('11', '拜访话术', '1', '2', '拜访话术', '', '<span style=\"color:#E56600;font-family:SimHei;font-size:24px;\"><strong><em><u>正确态度</u></em></strong></span>', '0', '0', '0', '0', '0', '0', '0', '1503540034', '1503540034', '2');
INSERT INTO `guguo_talk_article` VALUES ('12', '测试显示', '1', '4', '123', '', '2323', '0', '0', '0', '0', '0', '0', '0', '1503627732', '1503627732', '2');
INSERT INTO `guguo_talk_article` VALUES ('13', '测试显示', '1', '4', '123', '', '2323', '0', '0', '0', '0', '0', '0', '0', '1503627732', '1503627732', '2');
INSERT INTO `guguo_talk_article` VALUES ('14', '百度', '2', '1', 'baidu', 'www.baidu.com', null, '0', '0', '0', '0', '0', '0', '0', '1503627936', '1503627936', '2');
INSERT INTO `guguo_talk_article` VALUES ('15', '1234', '1', '1', '1234', '', '<span style=\"font-family:NSimSun;font-size:24px;color:#006600;\"><strong><em><u>1234123123jknp123lm12;31h 2bkjlk1;23 hijok;l\'9ijmuok,pl.[;</u></em></strong></span>', '0', '0', '0', '0', '0', '0', '0', '1503628158', '1503628158', '2');
INSERT INTO `guguo_talk_article` VALUES ('16', '建个标题长一点', '1', '1', '建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点', '', '<span style=\"color:#99BB00;\"><strong><em>建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点建个标题长一点</em></strong></span>', '0', '0', '0', '0', '0', '0', '0', '1503630981', '1503630981', '2');
INSERT INTO `guguo_talk_article` VALUES ('17', 'asdf', '1', '1', 'adf', '', 'dafdadf', '0', '0', '0', '0', '0', '0', '0', '1503883186', '1503883186', '2');
INSERT INTO `guguo_talk_article` VALUES ('19', '中国', '1', '2', '大师傅空间十分空间十分', '', '递四方速递范德萨发生发送发送范德萨发生法师法师法师法师的范德萨发斯蒂芬斯蒂芬', '1', '1505169900', '1506744720', '0', '0', '0', '0', '1505289594', '1505289594', '2');
INSERT INTO `guguo_talk_article` VALUES ('20', '猛男十八刘', '1', '4', '猛男协会长', '', '<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"1339\" style=\"width:1006pt;\" class=\"ke-zeroborder\">\r\n	<tbody>\r\n		<tr>\r\n			<td height=\"18\" width=\"358\">\r\n				路径\r\n			</td>\r\n			<td width=\"238\">\r\n				修改内容\r\n			</td>\r\n			<td width=\"238\">\r\n			</td>\r\n			<td width=\"238\">\r\n			</td>\r\n			<td width=\"120\">\r\n			</td>\r\n			<td width=\"147\">\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td height=\"18\">\r\n				public\\crm\\css\\index.css\r\n			</td>\r\n			<td>\r\n				三级nav添加宽度，避免折行\r\n			</td>\r\n			<td>\r\n				改变新建客户时，三步骤状态颜色\r\n			</td>\r\n			<td>\r\n				调整新建客户底下保存按钮的位置\r\n			</td>\r\n			<td>\r\n				调整地图位置\r\n			</td>\r\n			<td>\r\n				改变详细地址的样式\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td height=\"18\">\r\n				app\\crm\\view\\customer\\my_customer.html\r\n			</td>\r\n			<td>\r\n				商机改为机会\r\n			</td>\r\n			<td>\r\n				沟通状态改为沟通结果\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td height=\"18\">\r\n				app\\crm\\view\\customer\\add_page.html\r\n			</td>\r\n			<td>\r\n				隐藏联系人输入框，调整顺序\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td height=\"18\">\r\n				public\\crm\\js\\customer_add.js\r\n			</td>\r\n			<td>\r\n				改变新建客户时，三步骤状态颜色\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td height=\"18\">\r\n				public\\static\\js\\list_manage.1.1.js\r\n			</td>\r\n			<td>\r\n				隐藏输出在控制台的代码\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n			<td>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>', '0', '0', '0', '0', '0', '0', '0', '1505292393', '1505292393', '2');
INSERT INTO `guguo_talk_article` VALUES ('21', '销掌门PC端UI设计需求', '1', '1', '空', '', '<p class=\"MsoListParagraph\" style=\"margin-left:18.0pt;text-indent:-18.0pt;\">\r\n	1. 概要<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-indent:14.0pt;\">\r\n	共有两个程序销掌门<span>PC</span>端和安装程序需要设计<span>UI</span>。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" style=\"margin-left:14.0pt;text-indent:-14.0pt;\">\r\n	&nbsp; PC端分<span>IM</span>（即时消息）和软电话两大块，之前是分开设计的，现在需要整合到一起，相关界面需要重新设计。<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:-18pt;\">\r\n	2. PC端界面<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:32pt;text-indent:-18pt;\">\r\n	1) 登录界面<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	内容：<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	账号、密码提示标签<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	账号、密码输入框<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	最大化、最小化按钮<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	登录、取消按钮<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	2)个人信息展示，单聊、群聊、聊天室展示界面<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:15.75pt;\">\r\n	<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	个人信息包括：头像、名称、职位（示例界面没显示）<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	单聊（<span>single</span>）<span>,</span>群聊（<span>group</span>）<span>,</span>聊天室选择功能<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	成员显示列表显示<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	3）个人信息修改界面<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	示例界面：无<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	功能：支持修改头像、名称和职位<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	4）单人聊天界面<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:15.75pt;\">\r\n	<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 语音、表情、图片、发送按钮，关闭按钮<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 聊天输入框<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 聊天记录显示框<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	5）表情选择界面<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:15.75pt;\">\r\n	<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	5）群聊界面<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:15.75pt;\">\r\n	<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 语音、表情、图片、发送按钮<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 聊天输入框<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 聊天记录显示框<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 成员列表显示框<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 6）群聊<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp; 示例界面：无，可参考群聊界面<span>5</span>）。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:14pt;\">\r\n	7）软电话界面<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:15.75pt;\">\r\n	<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	内容：<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	拨号盘、呼叫记录、联系人、配置菜单。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	拨号盘数字<span> 1</span>、<span>2</span>、<span>3…</span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	播放、录音声量控制<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	号码输入框<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	呼叫按钮<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	录音开关按钮<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	呼叫状态提示<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:21pt;\">\r\n	8）语音播放管理界面<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"text-indent:15.75pt;\">\r\n	<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	内容：<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	播放、暂停按钮<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	文件打开按钮<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	文件列表框<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	文件播放进度控制条<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	文件长度及当前播放时长显示<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:-18pt;\">\r\n	3. 安装程序界面<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	下面只列出了必须的界面，其它界面如知识产权声明界面、公司用户信息界面可根据需要添加<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	1）欢迎界面<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	具体显示内容可修改<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	2）安装路径选择界面<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	内容：<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	安装路径输入框及提示文本<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	选择按钮<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	上一步、下一步、取消按钮<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	3）进度界面<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	内容：<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	进度提示文本<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	进度条（图中没有显示）<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	4）成功界面<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	5）失败界面<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	示例界面：无<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	&nbsp;\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:-18pt;\">\r\n	4. 程序图标<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	销掌门<span>PC</span>端桌面快捷方式、托盘（系统右下脚）图标<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	安装程序图标、卸载程序图标<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:-18pt;\">\r\n	5. 说明<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	以上界面仅供参考，可根据设计具体修改，但列出的功能必须满足，其余可根据情况添加。界面的大小不固定，可根据情况修改，只要排版布局合理即可。<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:-18pt;\">\r\n	6. 设计要点<span></span>\r\n</p>\r\n<p class=\"MsoListParagraph\" align=\"left\" style=\"margin-left:18pt;text-indent:0cm;\">\r\n	图中元素可分为标签、输入框、按钮、列表、进度条<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp;&nbsp; 标签：字体大小、字体颜色，不需要动态效果。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp;&nbsp; 输入框：有两种状态，获得焦点和失去焦点，需要加以区分。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\" style=\"margin-left:56pt;text-indent:-56pt;\">\r\n	&nbsp;&nbsp; 按钮：分为两种，一种是普通按钮（按下后立刻弹起），一种是切换按钮（按下后不弹起，需要再按一次才弹起）。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	普通按钮有三种状态，正常态、鼠标悬停态、按下状态，需要区分。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	切换按钮有两种状态，弹起态和按下态。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	列表：每个列表项有三种状态，正常态、鼠标滑过状态、双击状态。<span></span>\r\n</p>\r\n<p class=\"MsoNormal\" align=\"left\">\r\n	&nbsp;\r\n</p>', '0', '0', '0', '0', '0', '0', '0', '1505292516', '1505292516', '2');
INSERT INTO `guguo_talk_article` VALUES ('22', '1111', '1', '1', '看看就好几个回复', '', '<span style=\"font-family:&quot;font-size:18px;color:#4C33E5;\"><strong><em>纠纷预防</em></strong></span>', '0', '0', '0', '0', '0', '0', '0', '1505293475', '1505293475', '2');
INSERT INTO `guguo_talk_article` VALUES ('24', '测试图片', '1', '1', '俺的沙发的发放大的发生发大发发斯蒂芬', '', '<img src=\"/knowledgebase/js/attached/image/20170914/20170914091100_90754.jpg\" alt=\"\" />', '0', '0', '0', '0', '0', '0', '0', '1505351928', '1505351928', '2');
INSERT INTO `guguo_talk_article` VALUES ('25', '测试图片能不能行', '1', '1', '爱的发地方撒旦法是否大沙发沙发斯蒂芬撒', '', '<img src=\"/knowledgebase/js/attached/image/20170914/20170914092428_89866.jpg\" alt=\"\" />', '0', '0', '0', '0', '0', '0', '0', '1505352270', '1505352270', '2');
INSERT INTO `guguo_talk_article` VALUES ('27', '测试引用', '2', '1', '测试引用测试引用测试引用测试引用', 'www.baidu.com', null, '0', '0', '0', '0', '0', '0', '0', '1505373396', '1505373396', '2');

-- ----------------------------
-- Table structure for guguo_talk_article_type
-- ----------------------------
DROP TABLE IF EXISTS `guguo_talk_article_type`;
CREATE TABLE `guguo_talk_article_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL COMMENT '分类名称',
  `creat_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_talk_article_type
-- ----------------------------
INSERT INTO `guguo_talk_article_type` VALUES ('1', 'fsf', '1234');
INSERT INTO `guguo_talk_article_type` VALUES ('2', '测试', '1503382530');
INSERT INTO `guguo_talk_article_type` VALUES ('3', '测试1', '1503383281');
INSERT INTO `guguo_talk_article_type` VALUES ('4', '测试2', '1503383301');
INSERT INTO `guguo_talk_article_type` VALUES ('5', '测试3', '1503383361');
INSERT INTO `guguo_talk_article_type` VALUES ('6', '新的分类', '1505372729');
INSERT INTO `guguo_talk_article_type` VALUES ('7', 'zz', '1506479454');

-- ----------------------------
-- Table structure for guguo_talking_method
-- ----------------------------
DROP TABLE IF EXISTS `guguo_talking_method`;
CREATE TABLE `guguo_talking_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL COMMENT '话术标题',
  `content` text COMMENT '话术内容',
  `business_id` mediumint(9) DEFAULT NULL COMMENT '业务id',
  `is_top` tinyint(1) DEFAULT '0' COMMENT '0普通1置顶',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_talking_method
-- ----------------------------

-- ----------------------------
-- Table structure for guguo_umessage
-- ----------------------------
DROP TABLE IF EXISTS `guguo_umessage`;
CREATE TABLE `guguo_umessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL COMMENT '用户id',
  `type` smallint(4) DEFAULT NULL COMMENT '1修改密码记录  2红包领取记录　3转账记录　4提现记录　5充值记录，6员工管理',
  `remark` varchar(256) DEFAULT NULL COMMENT '备注信息',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=718 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_umessage
-- ----------------------------
INSERT INTO `guguo_umessage` VALUES ('1', '2', '1', 'reset pay password', '1489221176');
INSERT INTO `guguo_umessage` VALUES ('2', '2', '2', '用户领取红包,金额510分', '1489625463');
INSERT INTO `guguo_umessage` VALUES ('3', '2', '2', '收到返还的超时红包，id为168,169,170,171,172,173,174返还金额2197分', '1489633123');
INSERT INTO `guguo_umessage` VALUES ('4', '2', '2', '收到返还的超时红包，id为175,176,177,178,179,180,181,182,183,184,185,197返还金额8641分', '1489712479');
INSERT INTO `guguo_umessage` VALUES ('5', '2', '2', '用户创建红包,总金额10000分，共20个', '1489717606');
INSERT INTO `guguo_umessage` VALUES ('6', '2', '2', '收到返还的超时红包，id为197返还金额2000分', '1489717885');
INSERT INTO `guguo_umessage` VALUES ('8', '2', '4', '用户提现，金额为100分', '1489819774');
INSERT INTO `guguo_umessage` VALUES ('9', '2', '4', '用户提现，金额为100分', '1489825470');
INSERT INTO `guguo_umessage` VALUES ('10', '2', '3', '用户app转账成功，转至用户id4,转账金额100分', '1489995148');
INSERT INTO `guguo_umessage` VALUES ('11', '2', '3', '用户app转账成功，转至用户id4,转账金额100分', '1489995697');
INSERT INTO `guguo_umessage` VALUES ('12', '2', '4', '用户提现，金额为100分', '1489996237');
INSERT INTO `guguo_umessage` VALUES ('13', '2', '2', '收到返还的超时红包，id为198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217返还金额10000分', '1489997257');
INSERT INTO `guguo_umessage` VALUES ('16', '2', '4', '用户提现，金额为100分', '1490058733');
INSERT INTO `guguo_umessage` VALUES ('17', '2', '4', '用户提现，金额为100分', '1490059506');
INSERT INTO `guguo_umessage` VALUES ('18', '2', '4', '用户提现，金额为100分', '1490061035');
INSERT INTO `guguo_umessage` VALUES ('19', '2', '4', '用户提现，金额为100分', '1490061273');
INSERT INTO `guguo_umessage` VALUES ('20', '2', '4', '用户提现，金额为100分', '1490061314');
INSERT INTO `guguo_umessage` VALUES ('21', '2', '4', '用户提现，金额为100分', '1490061342');
INSERT INTO `guguo_umessage` VALUES ('22', '2', '3', '用户app转账成功，转至用户id4,转账金额200分', '1490061646');
INSERT INTO `guguo_umessage` VALUES ('23', '2', '2', '用户创建红包成功,总金额10000分，共20个', '1490062122');
INSERT INTO `guguo_umessage` VALUES ('24', '2', '2', '用户领取红包,金额195分', '1490062302');
INSERT INTO `guguo_umessage` VALUES ('25', '2', '2', '用户创建红包成功,总金额1200分，共3个', '1490076007');
INSERT INTO `guguo_umessage` VALUES ('26', '2', '5', '用户充值成功,总金额20000分', '1490079787');
INSERT INTO `guguo_umessage` VALUES ('27', '2', '5', '生成用户订单，订单号guguo_app_pay1490085658396691', '1490085658');
INSERT INTO `guguo_umessage` VALUES ('28', '2', '5', '生成用户订单，订单号guguo_app_pay2017032116512214900862821510', '1490086282');
INSERT INTO `guguo_umessage` VALUES ('29', '2', '5', '用户充值成功,兑换系统货币失败，总金额20000分', '1490147603');
INSERT INTO `guguo_umessage` VALUES ('30', '2', '5', '用户充值成功,兑换系统货币失败，总金额20000分', '1490147650');
INSERT INTO `guguo_umessage` VALUES ('31', '2', '5', '用户充值成功,总金额20000分', '1490147694');
INSERT INTO `guguo_umessage` VALUES ('32', '2', '2', '收到返还的超时红包，id为219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237返还金额9804分', '1490172515');
INSERT INTO `guguo_umessage` VALUES ('33', '2', '2', '收到返还的超时红包，id为252,253,254返还金额1200分', '1490174165');
INSERT INTO `guguo_umessage` VALUES ('34', '2', '3', '用户app转账成功，转至用户id3,转账金额200分', '1490270029');
INSERT INTO `guguo_umessage` VALUES ('35', '2', '3', '用户app转账成功，转至用户id3,转账金额200分', '1490315387');
INSERT INTO `guguo_umessage` VALUES ('36', '2', '3', '用户app转账成功，转至用户id5,转账金额200分', '1490315597');
INSERT INTO `guguo_umessage` VALUES ('37', '3', '3', '用户app转账成功，转至用户id4,转账金额200分', '1490315906');
INSERT INTO `guguo_umessage` VALUES ('38', '2', '5', '用户充值成功,总金额20000分', '1490320511');
INSERT INTO `guguo_umessage` VALUES ('39', '3', '3', '用户app转账成功，转至用户id4,转账金额200分', '1490322979');
INSERT INTO `guguo_umessage` VALUES ('40', '5', '3', '用户app转账成功，转至用户id3,转账金额120分', '1490326043');
INSERT INTO `guguo_umessage` VALUES ('41', '5', '3', '用户app转账成功，转至用户id3,转账金额33分', '1490327374');
INSERT INTO `guguo_umessage` VALUES ('42', '5', '3', '用户app转账成功，转至用户id3,转账金额15分', '1490335080');
INSERT INTO `guguo_umessage` VALUES ('43', '5', '3', '用户app转账成功，转至用户id3,转账金额1分', '1490335533');
INSERT INTO `guguo_umessage` VALUES ('44', '5', '3', '用户app转账成功，转至用户id3,转账金额1分', '1490335557');
INSERT INTO `guguo_umessage` VALUES ('45', '5', '3', '用户app转账成功，转至用户id3,转账金额1分', '1490335627');
INSERT INTO `guguo_umessage` VALUES ('46', '5', '3', '用户app转账成功，转至用户id3,转账金额1分', '1490335978');
INSERT INTO `guguo_umessage` VALUES ('47', '5', '3', '用户app转账成功，转至用户id3,转账金额1分', '1490336075');
INSERT INTO `guguo_umessage` VALUES ('48', '5', '3', '用户app转账成功，转至用户id3,转账金额1分', '1490336254');
INSERT INTO `guguo_umessage` VALUES ('49', '5', '3', '用户app转账成功，转至用户id3,转账金额1分', '1490336494');
INSERT INTO `guguo_umessage` VALUES ('50', '5', '3', '用户app转账成功，转至用户id3,转账金额5分', '1490336627');
INSERT INTO `guguo_umessage` VALUES ('51', '5', '3', '用户app转账成功，转至用户id3,转账金额2分', '1490336683');
INSERT INTO `guguo_umessage` VALUES ('52', '5', '3', '用户app转账成功，转至用户id3,转账金额10分', '1490336958');
INSERT INTO `guguo_umessage` VALUES ('53', '3', '3', '用户app转账成功，转至用户id1,转账金额500分', '1490346659');
INSERT INTO `guguo_umessage` VALUES ('54', '3', '3', '用户app转账成功，转至用户id5,转账金额500分', '1490347061');
INSERT INTO `guguo_umessage` VALUES ('55', '3', '3', '用户app转账成功，转至用户id2,转账金额200分', '1490575343');
INSERT INTO `guguo_umessage` VALUES ('56', '2', '2', '用户创建红包成功,总金额1000分，共20个', '1490584666');
INSERT INTO `guguo_umessage` VALUES ('57', '2', '2', '用户创建红包成功,总金额2分，共1个', '1490584689');
INSERT INTO `guguo_umessage` VALUES ('58', '4', '2', '用户创建红包成功,总金额2分，共1个', '1490584849');
INSERT INTO `guguo_umessage` VALUES ('59', '4', '2', '用户创建红包成功,总金额2分，共1个', '1490584989');
INSERT INTO `guguo_umessage` VALUES ('60', '4', '2', '用户创建红包成功,总金额2分，共1个', '1490585112');
INSERT INTO `guguo_umessage` VALUES ('61', '4', '2', '用户创建红包成功,总金额2分，共1个', '1490585404');
INSERT INTO `guguo_umessage` VALUES ('62', '4', '2', '用户创建红包成功,总金额20分，共1个', '1490585671');
INSERT INTO `guguo_umessage` VALUES ('63', '4', '2', '用户创建红包成功,总金额2分，共1个', '1490585840');
INSERT INTO `guguo_umessage` VALUES ('64', '4', '2', '用户创建红包成功,总金额370分，共1个', '1490662516');
INSERT INTO `guguo_umessage` VALUES ('65', '4', '2', '收到返还的超时红包，id为287,288,289,290,291,292返还金额30分', '1490684561');
INSERT INTO `guguo_umessage` VALUES ('66', '4', '2', '用户创建红包成功,总金额200分，共1个', '1490684655');
INSERT INTO `guguo_umessage` VALUES ('67', '5', '2', '用户创建红包成功,总金额300分，共1个', '1490685800');
INSERT INTO `guguo_umessage` VALUES ('68', '5', '2', '用户创建红包成功,总金额200分，共1个', '1490686752');
INSERT INTO `guguo_umessage` VALUES ('69', '5', '2', '收到返还的超时红包，id为295,296返还金额500分', '1490833452');
INSERT INTO `guguo_umessage` VALUES ('70', '4', '2', '收到返还的超时红包，id为293,294返还金额570分', '1490845254');
INSERT INTO `guguo_umessage` VALUES ('71', '4', '2', '用户创建红包成功,总金额400分，共2个', '1490845543');
INSERT INTO `guguo_umessage` VALUES ('72', '4', '2', '用户创建红包成功,总金额200分，共2个', '1490853290');
INSERT INTO `guguo_umessage` VALUES ('73', '4', '2', '用户创建红包成功,总金额300分，共2个', '1490853589');
INSERT INTO `guguo_umessage` VALUES ('74', '4', '2', '用户创建红包成功,总金额40分，共2个', '1490853939');
INSERT INTO `guguo_umessage` VALUES ('75', '2', '2', '用户创建红包成功,总金额3000分，共5个', '1490855639');
INSERT INTO `guguo_umessage` VALUES ('76', '4', '2', '用户领取红包,金额148分', '1490860553');
INSERT INTO `guguo_umessage` VALUES ('77', '4', '2', '用户领取红包,金额23分', '1490860570');
INSERT INTO `guguo_umessage` VALUES ('78', '4', '2', '用户领取红包,金额23分', '1490860661');
INSERT INTO `guguo_umessage` VALUES ('79', '4', '2', '收到返还的超时红包，id为297,298,301,304,307返还金额745分', '1491439587');
INSERT INTO `guguo_umessage` VALUES ('80', '2', '2', '收到返还的超时红包，id为255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,286,309,310,311,312,313返还金额4001分', '1491443574');
INSERT INTO `guguo_umessage` VALUES ('81', '5', '2', '用户创建红包成功,总金额500分，共3个', '1491464496');
INSERT INTO `guguo_umessage` VALUES ('82', '5', '2', '用户领取红包,金额420分', '1491464721');
INSERT INTO `guguo_umessage` VALUES ('83', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491466204');
INSERT INTO `guguo_umessage` VALUES ('84', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491466220');
INSERT INTO `guguo_umessage` VALUES ('85', '5', '2', '用户领取红包,金额49分', '1491466426');
INSERT INTO `guguo_umessage` VALUES ('86', '5', '2', '用户创建红包成功,总金额50分，共1个', '1491467949');
INSERT INTO `guguo_umessage` VALUES ('87', '5', '2', '用户领取红包,金额50分', '1491468000');
INSERT INTO `guguo_umessage` VALUES ('88', '5', '2', '用户领取红包,金额69分', '1491468933');
INSERT INTO `guguo_umessage` VALUES ('89', '5', '2', '用户创建红包成功,总金额140分，共1个', '1491469081');
INSERT INTO `guguo_umessage` VALUES ('90', '5', '2', '用户领取红包,金额140分', '1491469086');
INSERT INTO `guguo_umessage` VALUES ('91', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491549987');
INSERT INTO `guguo_umessage` VALUES ('92', '5', '2', '用户领取红包,金额76分', '1491550002');
INSERT INTO `guguo_umessage` VALUES ('93', '5', '2', '用户创建红包成功,总金额100分，共1个', '1491550043');
INSERT INTO `guguo_umessage` VALUES ('94', '5', '2', '用户领取红包,金额100分', '1491550046');
INSERT INTO `guguo_umessage` VALUES ('95', '5', '2', '用户创建红包成功,总金额100分，共1个', '1491550256');
INSERT INTO `guguo_umessage` VALUES ('96', '5', '2', '用户领取红包,金额100分', '1491550260');
INSERT INTO `guguo_umessage` VALUES ('97', '5', '2', '用户创建红包成功,总金额111分，共1个', '1491550712');
INSERT INTO `guguo_umessage` VALUES ('98', '5', '2', '用户领取红包,金额111分', '1491550715');
INSERT INTO `guguo_umessage` VALUES ('99', '5', '2', '用户创建红包成功,总金额100分，共1个', '1491550956');
INSERT INTO `guguo_umessage` VALUES ('100', '5', '2', '用户领取红包,金额100分', '1491550960');
INSERT INTO `guguo_umessage` VALUES ('101', '5', '2', '收到返还的超时红包，id为315,316返还金额80分', '1491550975');
INSERT INTO `guguo_umessage` VALUES ('102', '5', '2', '用户创建红包成功,总金额200分，共1个', '1491551749');
INSERT INTO `guguo_umessage` VALUES ('103', '5', '2', '用户领取红包,金额200分', '1491551752');
INSERT INTO `guguo_umessage` VALUES ('104', '5', '2', '收到返还的超时红包，id为318,321返还金额282分', '1491552690');
INSERT INTO `guguo_umessage` VALUES ('105', '5', '2', '用户创建红包成功,总金额100分，共1个', '1491552986');
INSERT INTO `guguo_umessage` VALUES ('106', '2', '2', '用户领取红包,金额124分', '1491553076');
INSERT INTO `guguo_umessage` VALUES ('107', '2', '2', '用户创建红包成功,总金额3000分，共6个', '1491553222');
INSERT INTO `guguo_umessage` VALUES ('108', '2', '2', '用户领取红包,金额198分', '1491553233');
INSERT INTO `guguo_umessage` VALUES ('109', '5', '2', '用户创建红包成功,总金额100分，共1个', '1491610826');
INSERT INTO `guguo_umessage` VALUES ('110', '5', '2', '用户领取红包,金额100分', '1491610830');
INSERT INTO `guguo_umessage` VALUES ('111', '5', '2', '用户创建红包成功,总金额66分，共1个', '1491611253');
INSERT INTO `guguo_umessage` VALUES ('112', '5', '2', '用户领取红包,金额66分', '1491611256');
INSERT INTO `guguo_umessage` VALUES ('113', '5', '2', '用户创建红包成功,总金额200分，共1个', '1491611347');
INSERT INTO `guguo_umessage` VALUES ('114', '5', '2', '用户领取红包,金额200分', '1491611356');
INSERT INTO `guguo_umessage` VALUES ('115', '5', '2', '用户创建红包成功,总金额100分，共2个', '1491611449');
INSERT INTO `guguo_umessage` VALUES ('116', '5', '2', '用户领取红包,金额47分', '1491611452');
INSERT INTO `guguo_umessage` VALUES ('117', '5', '2', '用户创建红包成功,总金额111分，共1个', '1491612394');
INSERT INTO `guguo_umessage` VALUES ('118', '5', '2', '用户领取红包,金额111分', '1491612397');
INSERT INTO `guguo_umessage` VALUES ('119', '5', '2', '用户创建红包成功,总金额11分，共1个', '1491612519');
INSERT INTO `guguo_umessage` VALUES ('120', '5', '2', '用户领取红包,金额11分', '1491612533');
INSERT INTO `guguo_umessage` VALUES ('121', '4', '2', '用户创建红包成功,总金额100分，共1个', '1491621785');
INSERT INTO `guguo_umessage` VALUES ('122', '4', '2', '用户领取红包,金额100分', '1491621790');
INSERT INTO `guguo_umessage` VALUES ('123', '4', '2', '用户创建红包成功,总金额166分，共1个', '1491787593');
INSERT INTO `guguo_umessage` VALUES ('124', '4', '2', '用户创建红包成功,总金额166分，共1个', '1491787594');
INSERT INTO `guguo_umessage` VALUES ('125', '4', '2', '用户创建红包成功,总金额166分，共1个', '1491787594');
INSERT INTO `guguo_umessage` VALUES ('126', '4', '2', '用户领取红包,金额166分', '1491790913');
INSERT INTO `guguo_umessage` VALUES ('127', '4', '2', '用户领取红包,金额166分', '1491790931');
INSERT INTO `guguo_umessage` VALUES ('128', '4', '2', '用户领取红包,金额166分', '1491790968');
INSERT INTO `guguo_umessage` VALUES ('129', '4', '2', '用户创建红包成功,总金额100分，共1个', '1491791185');
INSERT INTO `guguo_umessage` VALUES ('130', '4', '2', '用户领取红包,金额100分', '1491795264');
INSERT INTO `guguo_umessage` VALUES ('131', '4', '2', '用户创建红包成功,总金额100分，共2个', '1491795407');
INSERT INTO `guguo_umessage` VALUES ('132', '4', '2', '用户领取红包,金额81分', '1491795427');
INSERT INTO `guguo_umessage` VALUES ('133', '4', '2', '用户创建红包成功,总金额111分，共1个', '1491795652');
INSERT INTO `guguo_umessage` VALUES ('134', '4', '2', '用户领取红包,金额111分', '1491795656');
INSERT INTO `guguo_umessage` VALUES ('135', '5', '2', '收到返还的超时红包，id为333,344返还金额153分', '1491803902');
INSERT INTO `guguo_umessage` VALUES ('136', '5', '2', '用户领取红包,金额19分', '1491803908');
INSERT INTO `guguo_umessage` VALUES ('137', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491804661');
INSERT INTO `guguo_umessage` VALUES ('138', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491804662');
INSERT INTO `guguo_umessage` VALUES ('139', '5', '2', '用户领取红包,金额113分', '1491804688');
INSERT INTO `guguo_umessage` VALUES ('140', '4', '2', '用户领取红包,金额87分', '1491804690');
INSERT INTO `guguo_umessage` VALUES ('141', '5', '2', '用户领取红包,金额71分', '1491804714');
INSERT INTO `guguo_umessage` VALUES ('142', '4', '2', '用户领取红包,金额71分', '1491804714');
INSERT INTO `guguo_umessage` VALUES ('143', '5', '2', '用户领取红包,金额129分', '1491804730');
INSERT INTO `guguo_umessage` VALUES ('144', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491804752');
INSERT INTO `guguo_umessage` VALUES ('145', '5', '2', '用户领取红包,金额132分', '1491804757');
INSERT INTO `guguo_umessage` VALUES ('146', '4', '2', '用户领取红包,金额132分', '1491804757');
INSERT INTO `guguo_umessage` VALUES ('147', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491804793');
INSERT INTO `guguo_umessage` VALUES ('148', '5', '2', '用户领取红包,金额25分', '1491804803');
INSERT INTO `guguo_umessage` VALUES ('149', '4', '2', '用户领取红包,金额175分', '1491804804');
INSERT INTO `guguo_umessage` VALUES ('150', '5', '2', '用户创建红包成功,总金额100分，共2个', '1491804833');
INSERT INTO `guguo_umessage` VALUES ('151', '5', '2', '用户领取红包,金额65分', '1491804839');
INSERT INTO `guguo_umessage` VALUES ('152', '4', '2', '用户领取红包,金额35分', '1491804839');
INSERT INTO `guguo_umessage` VALUES ('153', '4', '2', '用户创建红包成功,总金额200分，共2个', '1491804865');
INSERT INTO `guguo_umessage` VALUES ('154', '5', '2', '用户领取红包,金额129分', '1491804873');
INSERT INTO `guguo_umessage` VALUES ('155', '4', '2', '用户领取红包,金额71分', '1491804873');
INSERT INTO `guguo_umessage` VALUES ('156', '5', '2', '用户创建红包成功,总金额888分，共2个', '1491805412');
INSERT INTO `guguo_umessage` VALUES ('157', '5', '2', '用户领取红包,金额864分', '1491805428');
INSERT INTO `guguo_umessage` VALUES ('158', '4', '2', '用户领取红包,金额864分', '1491805428');
INSERT INTO `guguo_umessage` VALUES ('159', '5', '2', '用户领取红包,金额24分', '1491805446');
INSERT INTO `guguo_umessage` VALUES ('160', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491805469');
INSERT INTO `guguo_umessage` VALUES ('161', '4', '2', '用户领取红包,金额56分', '1491805477');
INSERT INTO `guguo_umessage` VALUES ('162', '5', '2', '用户领取红包,金额56分', '1491805477');
INSERT INTO `guguo_umessage` VALUES ('163', '4', '2', '用户领取红包,金额144分', '1491805497');
INSERT INTO `guguo_umessage` VALUES ('164', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491805615');
INSERT INTO `guguo_umessage` VALUES ('165', '4', '2', '用户领取红包,金额55分', '1491805621');
INSERT INTO `guguo_umessage` VALUES ('166', '5', '2', '用户领取红包,金额55分', '1491805621');
INSERT INTO `guguo_umessage` VALUES ('167', '4', '2', '用户领取红包,金额145分', '1491805633');
INSERT INTO `guguo_umessage` VALUES ('168', '5', '2', '用户创建红包成功,总金额300分，共2个', '1491805820');
INSERT INTO `guguo_umessage` VALUES ('169', '5', '2', '用户领取红包,金额116分', '1491805823');
INSERT INTO `guguo_umessage` VALUES ('170', '4', '2', '用户领取红包,金额184分', '1491805850');
INSERT INTO `guguo_umessage` VALUES ('171', '2', '2', '用户创建红包成功,总金额600分，共10个', '1491808778');
INSERT INTO `guguo_umessage` VALUES ('172', '2', '2', '用户创建红包成功,总金额600分，共3个', '1491811135');
INSERT INTO `guguo_umessage` VALUES ('173', '2', '2', '用户领取红包,金额156分', '1491814842');
INSERT INTO `guguo_umessage` VALUES ('174', '5', '2', '用户领取红包,金额0.30分', '1491872656');
INSERT INTO `guguo_umessage` VALUES ('175', '5', '2', '用户领取红包,金额0.39分', '1491872834');
INSERT INTO `guguo_umessage` VALUES ('176', '2', '2', '用户领取红包,金额0.32分', '1491872851');
INSERT INTO `guguo_umessage` VALUES ('177', '5', '2', '用户领取红包,金额0.67分', '1491872855');
INSERT INTO `guguo_umessage` VALUES ('178', '2', '2', '用户领取红包,金额0.20分', '1491872857');
INSERT INTO `guguo_umessage` VALUES ('179', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491872957');
INSERT INTO `guguo_umessage` VALUES ('180', '2', '2', '用户领取红包,金额0.45分', '1491872967');
INSERT INTO `guguo_umessage` VALUES ('181', '5', '2', '用户领取红包,金额0.78分', '1491872973');
INSERT INTO `guguo_umessage` VALUES ('182', '4', '2', '用户领取红包成功,金额1.82分', '1491873366');
INSERT INTO `guguo_umessage` VALUES ('183', '3', '2', '用户领取红包成功,金额1.56分', '1491873417');
INSERT INTO `guguo_umessage` VALUES ('184', '3', '2', '用户领取红包成功,金额0.68分', '1491873446');
INSERT INTO `guguo_umessage` VALUES ('185', '2', '2', '收到返还的超时红包，id为335,336,337,338,339返还金额2802分', '1491873508');
INSERT INTO `guguo_umessage` VALUES ('186', '4', '2', '用户创建红包成功,总金额40000分，共2个', '1491874162');
INSERT INTO `guguo_umessage` VALUES ('187', '5', '2', '用户领取红包成功,金额243.03分', '1491874172');
INSERT INTO `guguo_umessage` VALUES ('188', '4', '2', '用户领取红包成功,金额156.97分', '1491874173');
INSERT INTO `guguo_umessage` VALUES ('189', '5', '2', '用户创建红包成功,总金额30000分，共3个', '1491874227');
INSERT INTO `guguo_umessage` VALUES ('190', '4', '2', '用户领取红包成功,金额109.61分', '1491874318');
INSERT INTO `guguo_umessage` VALUES ('191', '5', '2', '用户领取红包成功,金额107.93分', '1491874320');
INSERT INTO `guguo_umessage` VALUES ('192', '2', '2', '用户领取红包成功,金额82.46分', '1491874325');
INSERT INTO `guguo_umessage` VALUES ('193', '5', '2', '用户创建红包成功,总金额30000分，共3个', '1491874400');
INSERT INTO `guguo_umessage` VALUES ('194', '2', '2', '用户领取红包成功,金额38.70分', '1491874572');
INSERT INTO `guguo_umessage` VALUES ('195', '4', '2', '用户领取红包成功,金额98.35分', '1491874572');
INSERT INTO `guguo_umessage` VALUES ('196', '5', '2', '用户领取红包成功,金额98.35分', '1491874572');
INSERT INTO `guguo_umessage` VALUES ('197', '4', '2', '用户领取红包成功,金额162.95分', '1491874777');
INSERT INTO `guguo_umessage` VALUES ('198', '5', '2', '用户创建红包成功,总金额8888分，共3个', '1491874984');
INSERT INTO `guguo_umessage` VALUES ('199', '4', '2', '用户创建红包成功,总金额2分，共1个', '1491875053');
INSERT INTO `guguo_umessage` VALUES ('200', '4', '2', '用户领取红包成功,金额0.02分', '1491875057');
INSERT INTO `guguo_umessage` VALUES ('201', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491875297');
INSERT INTO `guguo_umessage` VALUES ('202', '2', '2', '用户领取红包成功,金额0.01分', '1491875306');
INSERT INTO `guguo_umessage` VALUES ('203', '5', '2', '用户创建红包成功,总金额888分，共4个', '1491876431');
INSERT INTO `guguo_umessage` VALUES ('204', '4', '2', '用户领取红包成功,金额3.04分', '1491876469');
INSERT INTO `guguo_umessage` VALUES ('205', '1', '2', '用户领取红包成功,金额3.04分', '1491876469');
INSERT INTO `guguo_umessage` VALUES ('206', '5', '2', '用户领取红包成功,金额1.46分', '1491876469');
INSERT INTO `guguo_umessage` VALUES ('207', '5', '2', '用户创建红包成功,总金额777分，共3个', '1491876598');
INSERT INTO `guguo_umessage` VALUES ('208', '5', '2', '用户创建红包成功,总金额777分，共3个', '1491876600');
INSERT INTO `guguo_umessage` VALUES ('209', '1', '2', '用户领取红包成功,金额1.15分', '1491876701');
INSERT INTO `guguo_umessage` VALUES ('210', '5', '2', '用户领取红包成功,金额5.95分', '1491876702');
INSERT INTO `guguo_umessage` VALUES ('211', '4', '2', '用户领取红包成功,金额1.15分', '1491876702');
INSERT INTO `guguo_umessage` VALUES ('212', '5', '2', '用户领取红包成功,金额1.39分', '1491877162');
INSERT INTO `guguo_umessage` VALUES ('213', '5', '2', '用户创建红包成功,总金额200分，共1个', '1491877334');
INSERT INTO `guguo_umessage` VALUES ('214', '2', '2', '用户领取红包成功,金额2.97分', '1491880600');
INSERT INTO `guguo_umessage` VALUES ('215', '2', '2', '用户领取红包成功,金额59.54分', '1491880733');
INSERT INTO `guguo_umessage` VALUES ('216', '2', '2', '用户领取红包成功,金额0.67分', '1491880764');
INSERT INTO `guguo_umessage` VALUES ('217', '5', '2', '用户领取红包成功,金额2.00分', '1491880814');
INSERT INTO `guguo_umessage` VALUES ('218', '5', '2', '用户创建红包成功,总金额200分，共1个', '1491880839');
INSERT INTO `guguo_umessage` VALUES ('219', '5', '2', '用户领取红包成功,金额2.00分', '1491880843');
INSERT INTO `guguo_umessage` VALUES ('220', '5', '2', '用户创建红包成功,总金额120分，共1个', '1491881047');
INSERT INTO `guguo_umessage` VALUES ('221', '5', '2', '用户领取红包成功,金额1.20分', '1491881050');
INSERT INTO `guguo_umessage` VALUES ('222', '2', '2', '用户领取红包成功,金额2.37分', '1491881144');
INSERT INTO `guguo_umessage` VALUES ('223', '5', '2', '用户创建红包成功,总金额4444分，共4个', '1491881595');
INSERT INTO `guguo_umessage` VALUES ('224', '5', '2', '用户创建红包成功,总金额400分，共4个', '1491881807');
INSERT INTO `guguo_umessage` VALUES ('225', '3', '2', '用户领取红包成功,金额0.41分', '1491881906');
INSERT INTO `guguo_umessage` VALUES ('226', '5', '2', '用户领取红包成功,金额1.23分', '1491881907');
INSERT INTO `guguo_umessage` VALUES ('227', '4', '2', '用户领取红包成功,金额1.23分', '1491881907');
INSERT INTO `guguo_umessage` VALUES ('228', '5', '2', '用户领取红包成功,金额0.31分', '1491881924');
INSERT INTO `guguo_umessage` VALUES ('229', '2', '2', '用户领取红包成功,金额2.05分', '1491881934');
INSERT INTO `guguo_umessage` VALUES ('230', '5', '2', '用户创建红包成功,总金额500分，共4个', '1491881975');
INSERT INTO `guguo_umessage` VALUES ('231', '2', '2', '用户领取红包成功,金额2.83分', '1491881995');
INSERT INTO `guguo_umessage` VALUES ('232', '4', '2', '用户领取红包成功,金额2.83分', '1491881996');
INSERT INTO `guguo_umessage` VALUES ('233', '3', '2', '用户领取红包成功,金额2.83分', '1491881996');
INSERT INTO `guguo_umessage` VALUES ('234', '5', '2', '用户领取红包成功,金额2.83分', '1491881996');
INSERT INTO `guguo_umessage` VALUES ('235', '4', '2', '用户领取红包成功,金额1.25分', '1491882016');
INSERT INTO `guguo_umessage` VALUES ('236', '3', '2', '用户领取红包成功,金额0.45分', '1491882046');
INSERT INTO `guguo_umessage` VALUES ('237', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491882322');
INSERT INTO `guguo_umessage` VALUES ('238', '3', '2', '用户领取红包成功,金额0.05分', '1491882484');
INSERT INTO `guguo_umessage` VALUES ('239', '2', '2', '用户领取红包成功,金额0.41分', '1491882500');
INSERT INTO `guguo_umessage` VALUES ('240', '4', '2', '用户领取红包成功,金额4.15分', '1491882502');
INSERT INTO `guguo_umessage` VALUES ('241', '1', '2', '用户领取红包成功,金额0.14分', '1491889877');
INSERT INTO `guguo_umessage` VALUES ('242', '5', '2', '用户领取红包成功,金额0.25分', '1491889926');
INSERT INTO `guguo_umessage` VALUES ('243', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491889946');
INSERT INTO `guguo_umessage` VALUES ('244', '4', '2', '用户领取红包成功,金额1.47分', '1491890061');
INSERT INTO `guguo_umessage` VALUES ('245', '1', '2', '用户领取红包成功,金额1.41分', '1491890197');
INSERT INTO `guguo_umessage` VALUES ('246', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491893007');
INSERT INTO `guguo_umessage` VALUES ('247', '3', '2', '用户领取红包成功,金额0.68分', '1491893439');
INSERT INTO `guguo_umessage` VALUES ('248', '1', '2', '用户领取红包成功,金额0.68分', '1491893439');
INSERT INTO `guguo_umessage` VALUES ('249', '5', '2', '用户领取红包成功,金额0.68分', '1491893439');
INSERT INTO `guguo_umessage` VALUES ('250', '4', '2', '用户领取红包成功,金额0.68分', '1491893439');
INSERT INTO `guguo_umessage` VALUES ('251', '3', '2', '用户领取红包成功,金额1.56分', '1491893483');
INSERT INTO `guguo_umessage` VALUES ('252', '2', '2', '用户领取红包成功,金额1.56分', '1491893483');
INSERT INTO `guguo_umessage` VALUES ('253', '1', '2', '用户领取红包成功,金额1.56分', '1491893483');
INSERT INTO `guguo_umessage` VALUES ('254', '5', '2', '用户领取红包成功,金额1.56分', '1491893483');
INSERT INTO `guguo_umessage` VALUES ('255', '1', '2', '用户领取红包成功,金额0.75分', '1491893763');
INSERT INTO `guguo_umessage` VALUES ('256', '2', '2', '用户领取红包成功,金额0.75分', '1491893763');
INSERT INTO `guguo_umessage` VALUES ('257', '3', '2', '用户领取红包成功,金额0.75分', '1491893763');
INSERT INTO `guguo_umessage` VALUES ('258', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491893812');
INSERT INTO `guguo_umessage` VALUES ('259', '1', '2', '用户领取红包成功,金额2.55分', '1491893853');
INSERT INTO `guguo_umessage` VALUES ('260', '4', '2', '用户领取红包成功,金额2.55分', '1491893853');
INSERT INTO `guguo_umessage` VALUES ('261', '2', '2', '用户领取红包成功,金额0.32分', '1491893853');
INSERT INTO `guguo_umessage` VALUES ('262', '5', '2', '用户领取红包成功,金额0.32分', '1491893853');
INSERT INTO `guguo_umessage` VALUES ('263', '3', '2', '用户领取红包成功,金额0.32分', '1491893853');
INSERT INTO `guguo_umessage` VALUES ('264', '2', '2', '用户领取红包成功,金额1.20分', '1491894341');
INSERT INTO `guguo_umessage` VALUES ('265', '1', '2', '用户领取红包成功,金额1.20分', '1491894341');
INSERT INTO `guguo_umessage` VALUES ('266', '5', '2', '用户领取红包成功,金额1.20分', '1491894341');
INSERT INTO `guguo_umessage` VALUES ('267', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491894460');
INSERT INTO `guguo_umessage` VALUES ('268', '1', '2', '用户领取红包成功,金额0.77分', '1491897273');
INSERT INTO `guguo_umessage` VALUES ('269', '2', '2', '用户领取红包成功,金额0.77分', '1491897311');
INSERT INTO `guguo_umessage` VALUES ('270', '3', '2', '用户领取红包成功,金额0.77分', '1491897396');
INSERT INTO `guguo_umessage` VALUES ('271', '4', '2', '用户领取红包成功,金额0.77分', '1491897422');
INSERT INTO `guguo_umessage` VALUES ('272', '5', '2', '用户领取红包成功,金额0.77分', '1491897423');
INSERT INTO `guguo_umessage` VALUES ('273', '4', '2', '用户领取红包成功,金额2.17分', '1491897423');
INSERT INTO `guguo_umessage` VALUES ('274', '2', '2', '用户领取红包成功,金额2.17分', '1491897424');
INSERT INTO `guguo_umessage` VALUES ('275', '5', '2', '用户领取红包成功,金额2.17分', '1491897425');
INSERT INTO `guguo_umessage` VALUES ('276', '3', '2', '用户领取红包成功,金额2.17分', '1491897426');
INSERT INTO `guguo_umessage` VALUES ('277', '5', '2', '用户领取红包成功,金额2.17分', '1491897427');
INSERT INTO `guguo_umessage` VALUES ('278', '4', '2', '用户领取红包成功,金额2.17分', '1491897428');
INSERT INTO `guguo_umessage` VALUES ('279', '1', '2', '用户领取红包成功,金额2.17分', '1491897429');
INSERT INTO `guguo_umessage` VALUES ('280', '3', '2', '用户领取红包成功,金额2.17分', '1491897430');
INSERT INTO `guguo_umessage` VALUES ('281', '4', '2', '用户领取红包成功,金额0.64分', '1491897560');
INSERT INTO `guguo_umessage` VALUES ('282', '1', '2', '用户领取红包成功,金额0.64分', '1491897561');
INSERT INTO `guguo_umessage` VALUES ('283', '2', '2', '用户领取红包成功,金额0.64分', '1491897562');
INSERT INTO `guguo_umessage` VALUES ('284', '1', '2', '用户领取红包成功,金额0.34分', '1491897677');
INSERT INTO `guguo_umessage` VALUES ('285', '4', '2', '用户领取红包成功,金额0.34分', '1491897678');
INSERT INTO `guguo_umessage` VALUES ('286', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491897706');
INSERT INTO `guguo_umessage` VALUES ('287', '2', '2', '用户领取红包成功,金额0.10分', '1491897715');
INSERT INTO `guguo_umessage` VALUES ('288', '1', '2', '用户领取红包成功,金额0.10分', '1491897716');
INSERT INTO `guguo_umessage` VALUES ('289', '3', '2', '用户领取红包成功,金额0.10分', '1491897717');
INSERT INTO `guguo_umessage` VALUES ('290', '5', '2', '用户领取红包成功,金额0.10分', '1491897718');
INSERT INTO `guguo_umessage` VALUES ('291', '4', '2', '用户领取红包成功,金额0.10分', '1491897719');
INSERT INTO `guguo_umessage` VALUES ('292', '2', '2', '用户领取红包成功,金额0.11分', '1491897764');
INSERT INTO `guguo_umessage` VALUES ('293', '5', '2', '用户领取红包成功,金额0.11分', '1491897765');
INSERT INTO `guguo_umessage` VALUES ('294', '1', '2', '用户领取红包成功,金额0.11分', '1491897766');
INSERT INTO `guguo_umessage` VALUES ('295', '3', '2', '用户领取红包成功,金额0.11分', '1491897767');
INSERT INTO `guguo_umessage` VALUES ('296', '1', '2', '用户领取红包成功,金额1.15分', '1491897816');
INSERT INTO `guguo_umessage` VALUES ('297', '2', '2', '用户领取红包成功,金额1.15分', '1491897817');
INSERT INTO `guguo_umessage` VALUES ('298', '5', '2', '用户领取红包成功,金额1.15分', '1491897818');
INSERT INTO `guguo_umessage` VALUES ('299', '2', '2', '用户领取红包成功,金额2.52分', '1491897880');
INSERT INTO `guguo_umessage` VALUES ('300', '1', '2', '用户领取红包成功,金额2.52分', '1491897881');
INSERT INTO `guguo_umessage` VALUES ('301', '2', '2', '用户领取红包成功,金额1.12分', '1491897972');
INSERT INTO `guguo_umessage` VALUES ('302', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491898016');
INSERT INTO `guguo_umessage` VALUES ('303', '1', '2', '用户领取红包成功,金额0.22分', '1491898028');
INSERT INTO `guguo_umessage` VALUES ('304', '2', '2', '用户领取红包成功,金额0.21分', '1491898029');
INSERT INTO `guguo_umessage` VALUES ('305', '4', '2', '用户领取红包成功,金额1.89分', '1491898030');
INSERT INTO `guguo_umessage` VALUES ('306', '3', '2', '用户领取红包成功,金额1.30分', '1491898031');
INSERT INTO `guguo_umessage` VALUES ('307', '5', '2', '用户领取红包成功,金额1.38分', '1491898032');
INSERT INTO `guguo_umessage` VALUES ('308', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491899402');
INSERT INTO `guguo_umessage` VALUES ('309', '1', '2', '用户领取红包成功,金额1.22分', '1491899416');
INSERT INTO `guguo_umessage` VALUES ('310', '2', '2', '用户领取红包成功,金额1.22分', '1491899417');
INSERT INTO `guguo_umessage` VALUES ('311', '3', '2', '用户领取红包成功,金额1.22分', '1491899418');
INSERT INTO `guguo_umessage` VALUES ('312', '4', '2', '用户领取红包成功,金额1.30分', '1491899419');
INSERT INTO `guguo_umessage` VALUES ('313', '5', '2', '用户领取红包成功,金额0.90分', '1491899420');
INSERT INTO `guguo_umessage` VALUES ('314', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491899486');
INSERT INTO `guguo_umessage` VALUES ('315', '2', '2', '用户领取红包成功,金额1.11分', '1491899498');
INSERT INTO `guguo_umessage` VALUES ('316', '1', '2', '用户领取红包成功,金额0.21分', '1491899499');
INSERT INTO `guguo_umessage` VALUES ('317', '3', '2', '用户领取红包成功,金额0.21分', '1491899500');
INSERT INTO `guguo_umessage` VALUES ('318', '4', '2', '用户领取红包成功,金额3.15分', '1491899501');
INSERT INTO `guguo_umessage` VALUES ('319', '5', '2', '用户领取红包成功,金额0.45分', '1491899502');
INSERT INTO `guguo_umessage` VALUES ('320', '1', '2', '用户领取红包成功,金额0.08分', '1491901090');
INSERT INTO `guguo_umessage` VALUES ('321', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491901135');
INSERT INTO `guguo_umessage` VALUES ('322', '2', '2', '用户领取红包成功,金额0.30分', '1491901147');
INSERT INTO `guguo_umessage` VALUES ('323', '3', '2', '用户领取红包成功,金额0.30分', '1491901148');
INSERT INTO `guguo_umessage` VALUES ('324', '1', '2', '用户领取红包成功,金额1.06分', '1491901149');
INSERT INTO `guguo_umessage` VALUES ('325', '4', '2', '用户领取红包成功,金额0.30分', '1491901150');
INSERT INTO `guguo_umessage` VALUES ('326', '5', '2', '用户领取红包成功,金额0.64分', '1491901151');
INSERT INTO `guguo_umessage` VALUES ('327', '3', '2', '用户领取红包成功,金额2.70分', '1491901204');
INSERT INTO `guguo_umessage` VALUES ('328', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491901215');
INSERT INTO `guguo_umessage` VALUES ('329', '1', '2', '用户领取红包成功,金额0.44分', '1491901225');
INSERT INTO `guguo_umessage` VALUES ('330', '2', '2', '用户领取红包成功,金额0.44分', '1491901225');
INSERT INTO `guguo_umessage` VALUES ('331', '3', '2', '用户领取红包成功,金额0.44分', '1491901225');
INSERT INTO `guguo_umessage` VALUES ('332', '4', '2', '用户领取红包成功,金额0.44分', '1491901226');
INSERT INTO `guguo_umessage` VALUES ('333', '5', '2', '用户领取红包成功,金额0.44分', '1491901226');
INSERT INTO `guguo_umessage` VALUES ('334', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491901464');
INSERT INTO `guguo_umessage` VALUES ('335', '1', '2', '用户领取红包成功,金额2.57分', '1491901476');
INSERT INTO `guguo_umessage` VALUES ('336', '2', '2', '用户领取红包成功,金额1.18分', '1491901476');
INSERT INTO `guguo_umessage` VALUES ('337', '4', '2', '用户领取红包成功,金额1.18分', '1491901476');
INSERT INTO `guguo_umessage` VALUES ('338', '5', '2', '用户领取红包成功,金额1.18分', '1491901476');
INSERT INTO `guguo_umessage` VALUES ('339', '3', '2', '用户领取红包成功,金额0.73分', '1491901476');
INSERT INTO `guguo_umessage` VALUES ('340', '2', '2', '用户领取红包成功,金额0.39分', '1491901636');
INSERT INTO `guguo_umessage` VALUES ('341', '4', '2', '用户领取红包成功,金额0.39分', '1491901636');
INSERT INTO `guguo_umessage` VALUES ('342', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491901736');
INSERT INTO `guguo_umessage` VALUES ('343', '4', '2', '用户领取红包成功,金额2.00分', '1491901820');
INSERT INTO `guguo_umessage` VALUES ('344', '3', '2', '用户领取红包成功,金额2.00分', '1491901820');
INSERT INTO `guguo_umessage` VALUES ('345', '1', '2', '用户领取红包成功,金额2.00分', '1491901820');
INSERT INTO `guguo_umessage` VALUES ('346', '2', '2', '用户领取红包成功,金额2.00分', '1491901820');
INSERT INTO `guguo_umessage` VALUES ('347', '5', '2', '用户领取红包成功,金额2.00分', '1491901820');
INSERT INTO `guguo_umessage` VALUES ('348', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491902102');
INSERT INTO `guguo_umessage` VALUES ('349', '1', '2', '用户领取红包成功,金额1.87分', '1491902115');
INSERT INTO `guguo_umessage` VALUES ('350', '2', '2', '用户领取红包成功,金额1.87分', '1491902115');
INSERT INTO `guguo_umessage` VALUES ('351', '3', '2', '用户领取红包成功,金额1.87分', '1491902116');
INSERT INTO `guguo_umessage` VALUES ('352', '4', '2', '用户领取红包成功,金额1.87分', '1491902116');
INSERT INTO `guguo_umessage` VALUES ('353', '5', '2', '用户领取红包成功,金额1.87分', '1491902116');
INSERT INTO `guguo_umessage` VALUES ('354', '1', '2', '用户领取红包成功,金额1.11分', '1491902174');
INSERT INTO `guguo_umessage` VALUES ('355', '2', '2', '用户领取红包成功,金额0.63分', '1491902175');
INSERT INTO `guguo_umessage` VALUES ('356', '3', '2', '用户领取红包成功,金额0.21分', '1491902175');
INSERT INTO `guguo_umessage` VALUES ('357', '4', '2', '用户领取红包成功,金额0.21分', '1491902175');
INSERT INTO `guguo_umessage` VALUES ('358', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491902234');
INSERT INTO `guguo_umessage` VALUES ('359', '1', '2', '用户领取红包成功,金额1.45分', '1491902246');
INSERT INTO `guguo_umessage` VALUES ('360', '4', '2', '用户领取红包成功,金额0.62分', '1491902247');
INSERT INTO `guguo_umessage` VALUES ('361', '5', '2', '用户领取红包成功,金额0.32分', '1491902248');
INSERT INTO `guguo_umessage` VALUES ('362', '2', '2', '用户领取红包成功,金额2.41分', '1491902249');
INSERT INTO `guguo_umessage` VALUES ('363', '3', '2', '用户领取红包成功,金额0.20分', '1491902250');
INSERT INTO `guguo_umessage` VALUES ('364', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491902665');
INSERT INTO `guguo_umessage` VALUES ('365', '1', '2', '用户领取红包成功,金额1.24分', '1491902677');
INSERT INTO `guguo_umessage` VALUES ('366', '2', '2', '用户领取红包成功,金额0.15分', '1491902678');
INSERT INTO `guguo_umessage` VALUES ('367', '3', '2', '用户领取红包成功,金额0.15分', '1491902679');
INSERT INTO `guguo_umessage` VALUES ('368', '5', '2', '用户领取红包成功,金额0.15分', '1491902680');
INSERT INTO `guguo_umessage` VALUES ('369', '4', '2', '用户领取红包成功,金额0.15分', '1491902681');
INSERT INTO `guguo_umessage` VALUES ('370', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491902895');
INSERT INTO `guguo_umessage` VALUES ('371', '2', '2', '用户领取红包成功,金额0.65分', '1491902907');
INSERT INTO `guguo_umessage` VALUES ('372', '1', '2', '用户领取红包成功,金额1.15分', '1491902908');
INSERT INTO `guguo_umessage` VALUES ('373', '5', '2', '用户领取红包成功,金额0.45分', '1491902909');
INSERT INTO `guguo_umessage` VALUES ('374', '4', '2', '用户领取红包成功,金额2.05分', '1491902910');
INSERT INTO `guguo_umessage` VALUES ('375', '3', '2', '用户领取红包成功,金额0.70分', '1491902911');
INSERT INTO `guguo_umessage` VALUES ('376', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491903097');
INSERT INTO `guguo_umessage` VALUES ('377', '1', '2', '用户领取红包成功,金额1.25分', '1491903108');
INSERT INTO `guguo_umessage` VALUES ('378', '2', '2', '用户领取红包成功,金额0.85分', '1491903109');
INSERT INTO `guguo_umessage` VALUES ('379', '3', '2', '用户领取红包成功,金额0.38分', '1491903110');
INSERT INTO `guguo_umessage` VALUES ('380', '4', '2', '用户领取红包成功,金额1.29分', '1491903111');
INSERT INTO `guguo_umessage` VALUES ('381', '5', '2', '用户领取红包成功,金额1.23分', '1491903112');
INSERT INTO `guguo_umessage` VALUES ('382', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491956469');
INSERT INTO `guguo_umessage` VALUES ('383', '1', '2', '用户领取红包成功,金额1.86分', '1491957953');
INSERT INTO `guguo_umessage` VALUES ('384', '2', '2', '用户领取红包成功,金额1.86分', '1491957954');
INSERT INTO `guguo_umessage` VALUES ('385', '3', '2', '用户领取红包成功,金额1.86分', '1491957954');
INSERT INTO `guguo_umessage` VALUES ('386', '4', '2', '用户领取红包成功,金额1.86分', '1491957955');
INSERT INTO `guguo_umessage` VALUES ('387', '5', '2', '用户领取红包成功,金额1.86分', '1491957956');
INSERT INTO `guguo_umessage` VALUES ('388', '5', '2', '用户创建红包成功,总金额200分，共2个', '1491958326');
INSERT INTO `guguo_umessage` VALUES ('389', '1', '2', '用户领取红包成功,金额0.84分', '1491958334');
INSERT INTO `guguo_umessage` VALUES ('390', '2', '2', '用户领取红包成功,金额0.30分', '1491958335');
INSERT INTO `guguo_umessage` VALUES ('391', '4', '2', '用户领取红包成功,金额0.90分', '1491958336');
INSERT INTO `guguo_umessage` VALUES ('392', '5', '2', '用户领取红包成功,金额0.90分', '1491958337');
INSERT INTO `guguo_umessage` VALUES ('393', '5', '2', '用户领取红包成功,金额1.58分', '1491958338');
INSERT INTO `guguo_umessage` VALUES ('394', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491958360');
INSERT INTO `guguo_umessage` VALUES ('395', '1', '2', '用户领取红包成功,金额0.71分', '1491958371');
INSERT INTO `guguo_umessage` VALUES ('396', '5', '2', '用户领取红包成功,金额0.98分', '1491958372');
INSERT INTO `guguo_umessage` VALUES ('397', '3', '2', '用户领取红包成功,金额2.16分', '1491958373');
INSERT INTO `guguo_umessage` VALUES ('398', '2', '2', '用户领取红包成功,金额0.38分', '1491958373');
INSERT INTO `guguo_umessage` VALUES ('399', '4', '2', '用户领取红包成功,金额0.77分', '1491958374');
INSERT INTO `guguo_umessage` VALUES ('400', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491958487');
INSERT INTO `guguo_umessage` VALUES ('401', '4', '2', '用户领取红包成功,金额2.32分', '1491958795');
INSERT INTO `guguo_umessage` VALUES ('402', '1', '2', '用户领取红包成功,金额0.87分', '1491958796');
INSERT INTO `guguo_umessage` VALUES ('403', '2', '2', '用户领取红包成功,金额0.94分', '1491958797');
INSERT INTO `guguo_umessage` VALUES ('404', '3', '2', '用户领取红包成功,金额0.75分', '1491958797');
INSERT INTO `guguo_umessage` VALUES ('405', '2', '2', '用户创建红包成功,总金额500分，共5个', '1491958820');
INSERT INTO `guguo_umessage` VALUES ('406', '4', '2', '用户领取红包成功,金额1.06分', '1491958877');
INSERT INTO `guguo_umessage` VALUES ('407', '2', '2', '用户领取红包成功,金额1.99分', '1491958921');
INSERT INTO `guguo_umessage` VALUES ('408', '1', '2', '用户领取红包成功,金额0.23分', '1491958921');
INSERT INTO `guguo_umessage` VALUES ('409', '5', '2', '用户领取红包成功,金额1.38分', '1491958922');
INSERT INTO `guguo_umessage` VALUES ('410', '4', '2', '用户领取红包成功,金额0.42分', '1491959663');
INSERT INTO `guguo_umessage` VALUES ('411', '2', '2', '用户创建红包成功,总金额1100分，共11个', '1491962013');
INSERT INTO `guguo_umessage` VALUES ('412', '5', '2', '用户领取红包成功,金额0.46分', '1491962157');
INSERT INTO `guguo_umessage` VALUES ('413', '2', '2', '用户领取红包成功,金额0.46分', '1491962257');
INSERT INTO `guguo_umessage` VALUES ('414', '4', '2', '用户领取红包成功,金额1.10分', '1491962258');
INSERT INTO `guguo_umessage` VALUES ('415', '1', '2', '用户领取红包成功,金额0.69分', '1491962259');
INSERT INTO `guguo_umessage` VALUES ('416', '3', '2', '用户领取红包成功,金额1.33分', '1491962306');
INSERT INTO `guguo_umessage` VALUES ('417', '2', '2', '用户创建红包成功,总金额1100分，共11个', '1491962854');
INSERT INTO `guguo_umessage` VALUES ('418', '1', '2', '用户领取红包成功,金额1.64分', '1491962965');
INSERT INTO `guguo_umessage` VALUES ('419', '9', '2', '用户领取红包成功,金额1.51分', '1491962966');
INSERT INTO `guguo_umessage` VALUES ('420', '4', '2', '用户领取红包成功,金额0.49分', '1491962966');
INSERT INTO `guguo_umessage` VALUES ('421', '7', '2', '用户领取红包成功,金额0.38分', '1491962967');
INSERT INTO `guguo_umessage` VALUES ('422', '2', '2', '用户领取红包成功,金额0.20分', '1491962968');
INSERT INTO `guguo_umessage` VALUES ('423', '5', '2', '用户领取红包成功,金额0.22分', '1491962969');
INSERT INTO `guguo_umessage` VALUES ('424', '11', '2', '用户领取红包成功,金额0.69分', '1491962998');
INSERT INTO `guguo_umessage` VALUES ('425', '8', '2', '用户领取红包成功,金额2.88分', '1491963003');
INSERT INTO `guguo_umessage` VALUES ('426', '6', '2', '用户领取红包成功,金额0.72分', '1491963014');
INSERT INTO `guguo_umessage` VALUES ('427', '3', '2', '用户领取红包成功,金额1.35分', '1491963040');
INSERT INTO `guguo_umessage` VALUES ('428', '5', '2', '用户创建红包成功,总金额300分，共3个', '1491963094');
INSERT INTO `guguo_umessage` VALUES ('429', '2', '2', '用户创建红包成功,总金额1100分，共11个', '1491963195');
INSERT INTO `guguo_umessage` VALUES ('430', '1', '2', '用户领取红包成功,金额0.09分', '1491963246');
INSERT INTO `guguo_umessage` VALUES ('431', '3', '2', '用户领取红包成功,金额2.41分', '1491963247');
INSERT INTO `guguo_umessage` VALUES ('432', '2', '2', '用户领取红包成功,金额0.46分', '1491963248');
INSERT INTO `guguo_umessage` VALUES ('433', '7', '2', '用户领取红包成功,金额0.20分', '1491963249');
INSERT INTO `guguo_umessage` VALUES ('434', '4', '2', '用户领取红包成功,金额1.57分', '1491963250');
INSERT INTO `guguo_umessage` VALUES ('435', '8', '2', '用户领取红包成功,金额1.13分', '1491963251');
INSERT INTO `guguo_umessage` VALUES ('436', '6', '2', '用户领取红包成功,金额0.72分', '1491963251');
INSERT INTO `guguo_umessage` VALUES ('437', '5', '2', '用户领取红包成功,金额0.72分', '1491963253');
INSERT INTO `guguo_umessage` VALUES ('438', '11', '2', '用户领取红包成功,金额1.64分', '1491963253');
INSERT INTO `guguo_umessage` VALUES ('439', '9', '2', '用户领取红包成功,金额0.99分', '1491963255');
INSERT INTO `guguo_umessage` VALUES ('440', '10', '2', '用户领取红包成功,金额1.07分', '1491963255');
INSERT INTO `guguo_umessage` VALUES ('441', '5', '2', '用户领取红包成功,金额1.13分', '1491968961');
INSERT INTO `guguo_umessage` VALUES ('442', '5', '2', '用户创建红包成功,总金额100分，共2个', '1492480869');
INSERT INTO `guguo_umessage` VALUES ('443', '5', '2', '用户领取红包成功,金额0.91分', '1492481019');
INSERT INTO `guguo_umessage` VALUES ('444', '2', '2', '用户领取红包成功,金额0.09分', '1492504888');
INSERT INTO `guguo_umessage` VALUES ('445', '7', '6', '删除员工李洪金,王启文,曹鑫鑫,孙大鹏成功', '1495100022');
INSERT INTO `guguo_umessage` VALUES ('446', '3', '3', '用户app转账成功，转至用户id5,转账金额1分', '1498528799');
INSERT INTO `guguo_umessage` VALUES ('447', '3', '3', '用户app转账成功，转至用户id5,转账金额1分', '1498529567');
INSERT INTO `guguo_umessage` VALUES ('448', '3', '3', '用户app转账成功，转至用户id5,转账金额1分', '1498529640');
INSERT INTO `guguo_umessage` VALUES ('449', '3', '3', '用户app转账成功，转至用户id5,转账金额1分', '1498529828');
INSERT INTO `guguo_umessage` VALUES ('450', '3', '3', '用户app转账成功，转至用户id5,转账金额1分', '1498530301');
INSERT INTO `guguo_umessage` VALUES ('451', '3', '3', '用户app转账成功，转至用户id5,转账金额1分', '1498530328');
INSERT INTO `guguo_umessage` VALUES ('452', '5', '3', '用户app转账成功，转至用户id4,转账金额100分', '1498530912');
INSERT INTO `guguo_umessage` VALUES ('453', '5', '2', '用户创建红包成功,总金额100分，共1个', '1498531369');
INSERT INTO `guguo_umessage` VALUES ('454', '5', '2', '用户创建红包成功,总金额700分，共1个', '1498531615');
INSERT INTO `guguo_umessage` VALUES ('455', '5', '2', '用户创建红包成功,总金额200分，共1个', '1498531800');
INSERT INTO `guguo_umessage` VALUES ('456', '4', '2', '用户创建红包成功,总金额100分，共1个', '1498531842');
INSERT INTO `guguo_umessage` VALUES ('457', '5', '2', '用户创建红包成功,总金额100分，共1个', '1498531946');
INSERT INTO `guguo_umessage` VALUES ('458', '6', '2', '用户创建红包成功,总金额230分，共1个', '1498533196');
INSERT INTO `guguo_umessage` VALUES ('459', '6', '2', '用户创建红包成功,总金额322分，共1个', '1498533296');
INSERT INTO `guguo_umessage` VALUES ('460', '6', '2', '用户创建红包成功,总金额290分，共1个', '1498533710');
INSERT INTO `guguo_umessage` VALUES ('461', '5', '2', '用户创建红包成功,总金额45分，共1个', '1498533932');
INSERT INTO `guguo_umessage` VALUES ('462', '5', '2', '用户创建红包成功,总金额333分，共1个', '1498546078');
INSERT INTO `guguo_umessage` VALUES ('463', '5', '2', '用户创建红包成功,总金额100分，共1个', '1498550604');
INSERT INTO `guguo_umessage` VALUES ('464', '5', '2', '用户创建红包成功,总金额400分，共1个', '1498550621');
INSERT INTO `guguo_umessage` VALUES ('465', '5', '2', '用户创建红包成功,总金额500分，共1个', '1498550631');
INSERT INTO `guguo_umessage` VALUES ('466', '5', '2', '用户创建红包成功,总金额600分，共1个', '1498550640');
INSERT INTO `guguo_umessage` VALUES ('467', '5', '2', '用户创建红包成功,总金额100分，共1个', '1498633474');
INSERT INTO `guguo_umessage` VALUES ('468', '5', '2', '用户创建红包成功,总金额100分，共2个', '1498638700');
INSERT INTO `guguo_umessage` VALUES ('469', '5', '2', '用户创建红包成功,总金额100分，共2个', '1498639631');
INSERT INTO `guguo_umessage` VALUES ('470', '5', '2', '用户创建红包成功,总金额3800分，共1个', '1498641996');
INSERT INTO `guguo_umessage` VALUES ('471', '5', '2', '用户创建红包成功,总金额200分，共1个', '1498700946');
INSERT INTO `guguo_umessage` VALUES ('472', '5', '2', '用户创建红包成功,总金额500分，共1个', '1498701636');
INSERT INTO `guguo_umessage` VALUES ('473', '6', '2', '收到返还的超时红包，id为675返还金额290分', '1498715662');
INSERT INTO `guguo_umessage` VALUES ('474', '5', '2', '用户创建红包成功,总金额333分，共1个', '1498725667');
INSERT INTO `guguo_umessage` VALUES ('475', '3', '2', '用户创建红包成功,总金额444分，共1个', '1498725736');
INSERT INTO `guguo_umessage` VALUES ('476', '3', '3', '用户app转账成功，转至用户id5,转账金额200分', '1498725995');
INSERT INTO `guguo_umessage` VALUES ('477', '5', '6', '删除员工后黑后成功', '1499844585');
INSERT INTO `guguo_umessage` VALUES ('478', '3', '6', '删除员工员工乙成功', '1499935703');
INSERT INTO `guguo_umessage` VALUES ('479', '3', '6', '删除员工李华成功', '1500086176');
INSERT INTO `guguo_umessage` VALUES ('480', '3', '6', '删除员工员工丙成功', '1500088598');
INSERT INTO `guguo_umessage` VALUES ('481', '3', '2', '用户创建红包成功,总金额100分，共5个', '1500514151');
INSERT INTO `guguo_umessage` VALUES ('482', '3', '2', '用户创建红包成功,总金额100分，共5个', '1500518695');
INSERT INTO `guguo_umessage` VALUES ('483', '5', '2', '用户创建红包成功,总金额100分，共1个', '1500519427');
INSERT INTO `guguo_umessage` VALUES ('484', '6', '2', '用户创建红包成功,总金额34400分，共1个', '1500519557');
INSERT INTO `guguo_umessage` VALUES ('485', '6', '2', '用户创建红包成功,总金额100分，共1个', '1500519747');
INSERT INTO `guguo_umessage` VALUES ('486', '6', '2', '用户创建红包成功,总金额200分，共1个', '1500519762');
INSERT INTO `guguo_umessage` VALUES ('487', '6', '2', '用户创建红包成功,总金额300分，共1个', '1500519778');
INSERT INTO `guguo_umessage` VALUES ('488', '5', '2', '用户创建红包成功,总金额355分，共3个', '1500521118');
INSERT INTO `guguo_umessage` VALUES ('489', '5', '2', '用户创建红包成功,总金额466分，共3个', '1500521136');
INSERT INTO `guguo_umessage` VALUES ('490', '5', '3', '用户app转账成功，转至用户id6,转账金额100分', '1500538507');
INSERT INTO `guguo_umessage` VALUES ('491', '3', '2', '用户创建红包成功,总金额100分，共1个', '1500599275');
INSERT INTO `guguo_umessage` VALUES ('492', '3', '2', '用户创建红包成功,总金额100分，共1个', '1500599442');
INSERT INTO `guguo_umessage` VALUES ('493', '5', '3', '用户app转账成功，转至用户id6,转账金额200分', '1500625182');
INSERT INTO `guguo_umessage` VALUES ('494', '5', '3', '用户app转账成功，转至用户id6,转账金额200分', '1500865398');
INSERT INTO `guguo_umessage` VALUES ('495', '7', '3', '用户app转账成功，转至用户id6,转账金额100分', '1501029814');
INSERT INTO `guguo_umessage` VALUES ('496', '7', '3', '用户app转账成功，转至用户id6,转账金额100分', '1501029980');
INSERT INTO `guguo_umessage` VALUES ('497', '7', '3', '用户app转账成功，转至用户id6,转账金额100分', '1501031120');
INSERT INTO `guguo_umessage` VALUES ('498', '7', '3', '用户app转账成功，转至用户id6,转账金额100分', '1501032325');
INSERT INTO `guguo_umessage` VALUES ('499', '7', '3', '用户app转账成功，转至用户id6,转账金额100分', '1501032386');
INSERT INTO `guguo_umessage` VALUES ('500', '7', '2', '用户创建红包成功,总金额100分，共1个', '1501032455');
INSERT INTO `guguo_umessage` VALUES ('501', '7', '2', '用户创建红包成功,总金额200分，共1个', '1501032510');
INSERT INTO `guguo_umessage` VALUES ('502', '7', '2', '用户创建红包成功,总金额300分，共1个', '1501032748');
INSERT INTO `guguo_umessage` VALUES ('503', '10', '2', '用户创建红包成功,总金额200分，共1个', '1501033349');
INSERT INTO `guguo_umessage` VALUES ('504', '10', '3', '用户app转账成功，转至用户id9,转账金额200分', '1501036239');
INSERT INTO `guguo_umessage` VALUES ('505', '10', '3', '用户app转账成功，转至用户id9,转账金额200分', '1501036347');
INSERT INTO `guguo_umessage` VALUES ('506', '10', '3', '用户app转账成功，转至用户id9,转账金额800分', '1501037821');
INSERT INTO `guguo_umessage` VALUES ('507', '10', '3', '用户app转账成功，转至用户id9,转账金额200分', '1501038009');
INSERT INTO `guguo_umessage` VALUES ('508', '10', '3', '用户app转账成功，转至用户id9,转账金额10分', '1501038421');
INSERT INTO `guguo_umessage` VALUES ('509', '10', '3', '用户app转账成功，转至用户id9,转账金额20分', '1501040959');
INSERT INTO `guguo_umessage` VALUES ('510', '10', '3', '用户app转账成功，转至用户id9,转账金额10分', '1501041665');
INSERT INTO `guguo_umessage` VALUES ('511', '10', '2', '用户创建红包成功,总金额1520分，共1个', '1501051227');
INSERT INTO `guguo_umessage` VALUES ('512', '10', '2', '用户创建红包成功,总金额1400分，共10个', '1501053447');
INSERT INTO `guguo_umessage` VALUES ('513', '10', '2', '用户创建红包成功,总金额2000分，共2个', '1501053507');
INSERT INTO `guguo_umessage` VALUES ('514', '10', '2', '用户创建红包成功,总金额1800分，共2个', '1501057869');
INSERT INTO `guguo_umessage` VALUES ('515', '10', '2', '用户创建红包成功,总金额200分，共3个', '1501061676');
INSERT INTO `guguo_umessage` VALUES ('516', '10', '2', '用户创建红包成功,总金额500分，共2个', '1501114253');
INSERT INTO `guguo_umessage` VALUES ('517', '10', '2', '用户创建红包成功,总金额400分，共2个', '1501114345');
INSERT INTO `guguo_umessage` VALUES ('518', '10', '2', '用户创建红包成功,总金额200分，共2个', '1501114594');
INSERT INTO `guguo_umessage` VALUES ('519', '10', '2', '用户创建红包成功,总金额200分，共2个', '1501114893');
INSERT INTO `guguo_umessage` VALUES ('520', '10', '2', '用户创建红包成功,总金额100分，共2个', '1501114916');
INSERT INTO `guguo_umessage` VALUES ('521', '10', '2', '用户创建红包成功,总金额100分，共1个', '1501114990');
INSERT INTO `guguo_umessage` VALUES ('522', '10', '3', '用户app转账成功，转至用户id9,转账金额100分', '1501115039');
INSERT INTO `guguo_umessage` VALUES ('523', '10', '2', '用户创建红包成功,总金额100分，共2个', '1501115227');
INSERT INTO `guguo_umessage` VALUES ('524', '10', '2', '用户创建红包成功,总金额200分，共2个', '1501115268');
INSERT INTO `guguo_umessage` VALUES ('525', '10', '2', '用户创建红包成功,总金额200分，共2个', '1501115737');
INSERT INTO `guguo_umessage` VALUES ('526', '10', '2', '用户创建红包成功,总金额100分，共1个', '1501115792');
INSERT INTO `guguo_umessage` VALUES ('527', '10', '3', '用户app转账成功，转至用户id9,转账金额100分', '1501116261');
INSERT INTO `guguo_umessage` VALUES ('528', '10', '2', '用户创建红包成功,总金额100分，共1个', '1501116274');
INSERT INTO `guguo_umessage` VALUES ('529', '10', '3', '用户app转账成功，转至用户id9,转账金额100分', '1501117331');
INSERT INTO `guguo_umessage` VALUES ('530', '10', '2', '用户创建红包成功,总金额200分，共2个', '1501118194');
INSERT INTO `guguo_umessage` VALUES ('531', '3', '1', '用户修改支付密码', '1501121756');
INSERT INTO `guguo_umessage` VALUES ('532', '10', '2', '用户创建红包成功,总金额100分，共2个', '1501138642');
INSERT INTO `guguo_umessage` VALUES ('533', '10', '2', '用户创建红包成功,总金额100分，共2个', '1501138959');
INSERT INTO `guguo_umessage` VALUES ('534', '3', '2', '收到返还的超时红包，id为702,703,704,705返还金额76分', '1501138932');
INSERT INTO `guguo_umessage` VALUES ('535', '10', '2', '用户创建红包成功,总金额200分，共2个', '1501147978');
INSERT INTO `guguo_umessage` VALUES ('536', '5', '2', '用户创建红包成功,总金额200分，共1个', '1501202405');
INSERT INTO `guguo_umessage` VALUES ('537', '5', '2', '用户创建红包成功,总金额33300分，共1个', '1501210846');
INSERT INTO `guguo_umessage` VALUES ('538', '5', '2', '用户创建红包成功,总金额2000分，共1个', '1501229303');
INSERT INTO `guguo_umessage` VALUES ('539', '5', '2', '用户创建红包成功,总金额600分，共3个', '1501288579');
INSERT INTO `guguo_umessage` VALUES ('540', '5', '2', '用户创建红包成功,总金额200分，共1个', '1501298728');
INSERT INTO `guguo_umessage` VALUES ('541', '5', '2', '用户创建红包成功,总金额500分，共1个', '1501469404');
INSERT INTO `guguo_umessage` VALUES ('542', '5', '2', '用户创建红包成功,总金额200分，共1个', '1501471575');
INSERT INTO `guguo_umessage` VALUES ('543', '5', '1', '用户修改支付密码', '1501492467');
INSERT INTO `guguo_umessage` VALUES ('544', '5', '1', '用户修改支付密码', '1501546924');
INSERT INTO `guguo_umessage` VALUES ('545', '5', '1', '用户修改支付密码', '1501546986');
INSERT INTO `guguo_umessage` VALUES ('546', '5', '1', '用户修改支付密码', '1501547073');
INSERT INTO `guguo_umessage` VALUES ('547', '5', '1', '用户修改支付密码', '1501547126');
INSERT INTO `guguo_umessage` VALUES ('548', '5', '1', '用户修改支付密码', '1501547182');
INSERT INTO `guguo_umessage` VALUES ('549', '5', '1', '用户修改支付密码', '1501548621');
INSERT INTO `guguo_umessage` VALUES ('550', '5', '2', '用户创建红包成功,总金额200分，共1个', '1501548637');
INSERT INTO `guguo_umessage` VALUES ('551', '5', '1', '用户修改支付密码', '1501576588');
INSERT INTO `guguo_umessage` VALUES ('552', '5', '2', '用户创建红包成功,总金额200分，共1个', '1501576620');
INSERT INTO `guguo_umessage` VALUES ('553', '5', '2', '用户创建红包成功,总金额200分，共3个', '1501576699');
INSERT INTO `guguo_umessage` VALUES ('554', '9', '2', '用户创建红包成功,总金额200分，共1个', '1501577312');
INSERT INTO `guguo_umessage` VALUES ('555', '5', '2', '用户创建红包成功,总金额200分，共1个', '1502325566');
INSERT INTO `guguo_umessage` VALUES ('556', '2', '2', '收到返还的超时红包，id为393返还金额11分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('557', '2', '2', '收到返还的超时红包，id为394返还金额61分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('558', '2', '2', '收到返还的超时红包，id为395返还金额61分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('559', '2', '2', '收到返还的超时红包，id为396返还金额123分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('560', '2', '2', '收到返还的超时红包，id为402返还金额192分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('561', '2', '2', '收到返还的超时红包，id为403返还金额351分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('562', '2', '2', '收到返还的超时红包，id为404返还金额56分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('563', '2', '2', '收到返还的超时红包，id为409返还金额39分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('564', '2', '2', '收到返还的超时红包，id为426返还金额7分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('565', '2', '2', '收到返还的超时红包，id为427返还金额453分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('566', '2', '2', '收到返还的超时红包，id为428返还金额10分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('567', '2', '2', '收到返还的超时红包，id为429返还金额28分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('568', '2', '2', '收到返还的超时红包，id为476返还金额88分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('569', '2', '2', '收到返还的超时红包，id为477返还金额96分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('570', '2', '2', '收到返还的超时红包，id为478返还金额28分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('571', '2', '2', '收到返还的超时红包，id为484返还金额157分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('572', '2', '2', '收到返还的超时红包，id为485返还金额44分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('573', '2', '2', '收到返还的超时红包，id为491返还金额45分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('574', '2', '2', '收到返还的超时红包，id为492返还金额48分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('575', '2', '2', '收到返还的超时红包，id为499返还金额108分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('576', '2', '2', '收到返还的超时红包，id为519返还金额91分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('577', '2', '2', '收到返还的超时红包，id为520返还金额67分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('578', '2', '2', '收到返还的超时红包，id为538返还金额131分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('579', '2', '2', '收到返还的超时红包，id为539返还金额155分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('580', '2', '2', '收到返还的超时红包，id为540返还金额101分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('581', '2', '2', '收到返还的超时红包，id为541返还金额69分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('582', '2', '2', '收到返还的超时红包，id为548返还金额13分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('583', '2', '2', '收到返还的超时红包，id为552返还金额40分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('584', '2', '2', '收到返还的超时红包，id为553返还金额41分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('585', '2', '2', '收到返还的超时红包，id为554返还金额28分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('586', '2', '2', '收到返还的超时红包，id为555返还金额190分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('587', '2', '2', '收到返还的超时红包，id为562返还金额118分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('588', '2', '2', '收到返还的超时红包，id为575返还金额333分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('589', '2', '2', '收到返还的超时红包，id为576返还金额13分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('590', '2', '2', '收到返还的超时红包，id为595返还金额110分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('591', '2', '2', '收到返还的超时红包，id为612返还金额12分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('592', '2', '2', '收到返还的超时红包，id为619返还金额34分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('593', '2', '2', '收到返还的超时红包，id为628返还金额147分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('594', '2', '2', '收到返还的超时红包，id为629返还金额62分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('595', '2', '2', '收到返还的超时红包，id为630返还金额242分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('596', '2', '2', '收到返还的超时红包，id为631返还金额54分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('597', '2', '2', '收到返还的超时红包，id为632返还金额122分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('598', '2', '2', '收到返还的超时红包，id为646返还金额154分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('599', '2', '2', '收到返还的超时红包，id为647返还金额7分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('600', '7', '2', '收到返还的超时红包，id为721返还金额100分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('601', '7', '2', '收到返还的超时红包，id为722返还金额200分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('602', '7', '2', '收到返还的超时红包，id为723返还金额300分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('603', '10', '2', '收到返还的超时红包，id为727返还金额3分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('604', '10', '2', '收到返还的超时红包，id为728返还金额141分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('605', '10', '2', '收到返还的超时红包，id为729返还金额239分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('606', '10', '2', '收到返还的超时红包，id为730返还金额63分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('607', '10', '2', '收到返还的超时红包，id为731返还金额340分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('608', '10', '2', '收到返还的超时红包，id为732返还金额241分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('609', '10', '2', '收到返还的超时红包，id为733返还金额15分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('610', '10', '2', '收到返还的超时红包，id为734返还金额102分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('611', '10', '2', '收到返还的超时红包，id为735返还金额78分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('612', '10', '2', '收到返还的超时红包，id为742返还金额1987分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('613', '10', '2', '收到返还的超时红包，id为745返还金额900分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('614', '10', '2', '收到返还的超时红包，id为748返还金额97分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('615', '10', '2', '收到返还的超时红包，id为749返还金额55分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('616', '10', '2', '收到返还的超时红包，id为751返还金额265分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('617', '10', '2', '收到返还的超时红包，id为753返还金额200分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('618', '10', '2', '收到返还的超时红包，id为754返还金额200分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('619', '10', '2', '收到返还的超时红包，id为756返还金额176分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('620', '10', '2', '收到返还的超时红包，id为757返还金额24分', '1503546298');
INSERT INTO `guguo_umessage` VALUES ('621', '10', '2', '收到返还的超时红包，id为759返还金额18分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('622', '10', '2', '收到返还的超时红包，id为760返还金额182分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('623', '10', '2', '收到返还的超时红包，id为762返还金额67分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('624', '10', '2', '收到返还的超时红包，id为763返还金额33分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('625', '10', '2', '收到返还的超时红包，id为765返还金额100分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('626', '10', '2', '收到返还的超时红包，id为766返还金额56分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('627', '10', '2', '收到返还的超时红包，id为767返还金额44分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('628', '10', '2', '收到返还的超时红包，id为770返还金额92分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('629', '10', '2', '收到返还的超时红包，id为773返还金额31分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('630', '10', '2', '收到返还的超时红包，id为776返还金额100分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('631', '10', '2', '收到返还的超时红包，id为778返还金额18分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('632', '9', '2', '收到返还的超时红包，id为803返还金额200分', '1503546299');
INSERT INTO `guguo_umessage` VALUES ('633', '3', '2', '用户创建红包成功,总金额100分，共1个', '1503556784');
INSERT INTO `guguo_umessage` VALUES ('634', '3', '2', '用户创建红包成功,总金额100分，共1个', '1503556910');
INSERT INTO `guguo_umessage` VALUES ('635', '3', '2', '用户创建红包成功,总金额100分，共1个', '1503557039');
INSERT INTO `guguo_umessage` VALUES ('636', '3', '2', '收到返还的超时红包，id为805返还金额100分', '1503557401');
INSERT INTO `guguo_umessage` VALUES ('637', '3', '2', '收到返还的超时红包，id为806返还金额100分', '1503557521');
INSERT INTO `guguo_umessage` VALUES ('638', '3', '2', '收到返还的超时红包，id为807返还金额100分', '1503557641');
INSERT INTO `guguo_umessage` VALUES ('639', '5', '2', '用户创建红包成功,总金额10000分，共1个', '1503558970');
INSERT INTO `guguo_umessage` VALUES ('640', '5', '2', '收到返还的超时红包，id为808返还金额10000分', '1503559622');
INSERT INTO `guguo_umessage` VALUES ('641', '5', '2', '用户创建红包成功,总金额1800分，共9个', '1503624797');
INSERT INTO `guguo_umessage` VALUES ('642', '5', '2', '收到返还的超时红包，id为810返还金额200分', '1503625441');
INSERT INTO `guguo_umessage` VALUES ('643', '5', '2', '收到返还的超时红包，id为811返还金额200分', '1503625441');
INSERT INTO `guguo_umessage` VALUES ('644', '5', '2', '收到返还的超时红包，id为812返还金额200分', '1503625441');
INSERT INTO `guguo_umessage` VALUES ('645', '5', '2', '收到返还的超时红包，id为813返还金额200分', '1503625441');
INSERT INTO `guguo_umessage` VALUES ('646', '5', '2', '收到返还的超时红包，id为814返还金额200分', '1503625441');
INSERT INTO `guguo_umessage` VALUES ('647', '5', '2', '收到返还的超时红包，id为815返还金额200分', '1503625441');
INSERT INTO `guguo_umessage` VALUES ('648', '5', '2', '收到返还的超时红包，id为816返还金额200分', '1503625441');
INSERT INTO `guguo_umessage` VALUES ('649', '5', '2', '收到返还的超时红包，id为817返还金额200分', '1503625441');
INSERT INTO `guguo_umessage` VALUES ('650', '5', '3', '用户app转账成功，转至用户id8,转账金额200分', '1503629980');
INSERT INTO `guguo_umessage` VALUES ('651', '5', '2', '用户创建红包成功,总金额2000分，共1个', '1503705343');
INSERT INTO `guguo_umessage` VALUES ('652', '5', '2', '收到返还的超时红包，id为824返还金额2000分', '1503705962');
INSERT INTO `guguo_umessage` VALUES ('653', '5', '2', '用户创建红包成功,总金额10000分，共1个', '1504137842');
INSERT INTO `guguo_umessage` VALUES ('654', '5', '2', '收到返还的超时红包，id为825返还金额10000分', '1504138501');
INSERT INTO `guguo_umessage` VALUES ('655', '5', '2', '用户创建红包成功,总金额10000分，共1个', '1504139981');
INSERT INTO `guguo_umessage` VALUES ('656', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504228617');
INSERT INTO `guguo_umessage` VALUES ('657', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504228656');
INSERT INTO `guguo_umessage` VALUES ('658', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504228711');
INSERT INTO `guguo_umessage` VALUES ('659', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504228767');
INSERT INTO `guguo_umessage` VALUES ('660', '5', '2', '收到返还的超时红包，id为829返还金额200分', '1504229341');
INSERT INTO `guguo_umessage` VALUES ('661', '5', '2', '收到返还的超时红包，id为830返还金额200分', '1504229401');
INSERT INTO `guguo_umessage` VALUES ('662', '5', '2', '用户创建红包成功,总金额200分，共2个', '1504247268');
INSERT INTO `guguo_umessage` VALUES ('663', '5', '2', '用户创建红包成功,总金额200分，共2个', '1504247311');
INSERT INTO `guguo_umessage` VALUES ('664', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504247746');
INSERT INTO `guguo_umessage` VALUES ('665', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504247769');
INSERT INTO `guguo_umessage` VALUES ('666', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504247877');
INSERT INTO `guguo_umessage` VALUES ('667', '5', '2', '收到返还的超时红包，id为832返还金额1分', '1504247881');
INSERT INTO `guguo_umessage` VALUES ('668', '5', '2', '收到返还的超时红包，id为835返还金额112分', '1504247941');
INSERT INTO `guguo_umessage` VALUES ('669', '5', '2', '收到返还的超时红包，id为837返还金额200分', '1504248361');
INSERT INTO `guguo_umessage` VALUES ('670', '5', '2', '收到返还的超时红包，id为839返还金额200分', '1504248481');
INSERT INTO `guguo_umessage` VALUES ('671', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504248813');
INSERT INTO `guguo_umessage` VALUES ('672', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504249024');
INSERT INTO `guguo_umessage` VALUES ('673', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504249220');
INSERT INTO `guguo_umessage` VALUES ('674', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504249322');
INSERT INTO `guguo_umessage` VALUES ('675', '5', '2', '收到返还的超时红包，id为843返还金额200分', '1504249981');
INSERT INTO `guguo_umessage` VALUES ('676', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504579223');
INSERT INTO `guguo_umessage` VALUES ('677', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504579229');
INSERT INTO `guguo_umessage` VALUES ('678', '5', '1', '用户修改支付密码', '1504579246');
INSERT INTO `guguo_umessage` VALUES ('679', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504579257');
INSERT INTO `guguo_umessage` VALUES ('680', '5', '2', '收到返还的超时红包，id为844返还金额100分', '1504579862');
INSERT INTO `guguo_umessage` VALUES ('681', '5', '2', '收到返还的超时红包，id为845返还金额100分', '1504579862');
INSERT INTO `guguo_umessage` VALUES ('682', '5', '2', '收到返还的超时红包，id为846返还金额100分', '1504579862');
INSERT INTO `guguo_umessage` VALUES ('683', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504586592');
INSERT INTO `guguo_umessage` VALUES ('684', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504586598');
INSERT INTO `guguo_umessage` VALUES ('685', '5', '2', '收到返还的超时红包，id为847返还金额100分', '1504587241');
INSERT INTO `guguo_umessage` VALUES ('686', '5', '2', '收到返还的超时红包，id为848返还金额100分', '1504587241');
INSERT INTO `guguo_umessage` VALUES ('687', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504592499');
INSERT INTO `guguo_umessage` VALUES ('688', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504592561');
INSERT INTO `guguo_umessage` VALUES ('689', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504592611');
INSERT INTO `guguo_umessage` VALUES ('690', '3', '2', '用户创建红包成功,总金额100分，共1个', '1504592855');
INSERT INTO `guguo_umessage` VALUES ('691', '5', '2', '收到返还的超时红包，id为849返还金额200分', '1504593121');
INSERT INTO `guguo_umessage` VALUES ('692', '3', '2', '用户创建红包成功,总金额100分，共1个', '1504593121');
INSERT INTO `guguo_umessage` VALUES ('693', '5', '2', '收到返还的超时红包，id为850返还金额100分', '1504593181');
INSERT INTO `guguo_umessage` VALUES ('694', '3', '3', '用户app转账成功，转至用户id5,转账金额1分', '1504593221');
INSERT INTO `guguo_umessage` VALUES ('695', '5', '2', '收到返还的超时红包，id为851返还金额100分', '1504593241');
INSERT INTO `guguo_umessage` VALUES ('696', '5', '2', '用户创建红包成功,总金额200分，共1个', '1504593444');
INSERT INTO `guguo_umessage` VALUES ('697', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504593471');
INSERT INTO `guguo_umessage` VALUES ('698', '3', '2', '收到返还的超时红包，id为852返还金额100分', '1504593481');
INSERT INTO `guguo_umessage` VALUES ('699', '3', '3', '用户app转账成功，转至用户id5,转账金额1分', '1504593591');
INSERT INTO `guguo_umessage` VALUES ('700', '3', '2', '收到返还的超时红包，id为853返还金额100分', '1504593781');
INSERT INTO `guguo_umessage` VALUES ('701', '5', '2', '收到返还的超时红包，id为854返还金额200分', '1504594081');
INSERT INTO `guguo_umessage` VALUES ('702', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504594128');
INSERT INTO `guguo_umessage` VALUES ('703', '5', '2', '收到返还的超时红包，id为856返还金额100分', '1504594741');
INSERT INTO `guguo_umessage` VALUES ('704', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504594862');
INSERT INTO `guguo_umessage` VALUES ('705', '5', '3', '用户app转账成功，转至用户id4,转账金额100分', '1504595065');
INSERT INTO `guguo_umessage` VALUES ('706', '5', '2', '收到返还的超时红包，id为857返还金额100分', '1504595521');
INSERT INTO `guguo_umessage` VALUES ('707', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504596270');
INSERT INTO `guguo_umessage` VALUES ('708', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504596893');
INSERT INTO `guguo_umessage` VALUES ('709', '5', '2', '收到返还的超时红包，id为858返还金额100分', '1504596902');
INSERT INTO `guguo_umessage` VALUES ('710', '5', '2', '收到返还的超时红包，id为859返还金额100分', '1504597501');
INSERT INTO `guguo_umessage` VALUES ('711', '5', '2', '用户创建红包成功,总金额100分，共1个', '1504597550');
INSERT INTO `guguo_umessage` VALUES ('712', '3', '2', '用户创建红包成功,总金额100分，共1个', '1505115703');
INSERT INTO `guguo_umessage` VALUES ('713', '8', '3', '用户app转账成功，转至用户id4,转账金额200分', '1505178511');
INSERT INTO `guguo_umessage` VALUES ('714', '4', '2', '用户创建红包成功,总金额10000分，共1个', '1505443963');
INSERT INTO `guguo_umessage` VALUES ('715', '4', '2', '收到返还的超时红包，id为890返还金额10000分', '1505444581');
INSERT INTO `guguo_umessage` VALUES ('716', '5', '2', '用户创建红包成功,总金额20000分，共1个', '1506132894');
INSERT INTO `guguo_umessage` VALUES ('717', '5', '3', '用户app转账成功，转至用户id3,转账金额100分', '1506132919');

-- ----------------------------
-- Table structure for guguo_verification_log
-- ----------------------------
DROP TABLE IF EXISTS `guguo_verification_log`;
CREATE TABLE `guguo_verification_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型,1:合同审核,2:成单审核,3:发票审核',
  `target_id` int(10) unsigned NOT NULL COMMENT '对象id',
  `create_user` int(10) unsigned NOT NULL COMMENT '操作用户',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status_previous` tinyint(4) NOT NULL COMMENT '前一状态',
  `status_now` tinyint(4) NOT NULL COMMENT '提交状态',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `cause` varchar(255) DEFAULT NULL COMMENT '原因',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guguo_verification_log
-- ----------------------------
INSERT INTO `guguo_verification_log` VALUES ('1', '1', '10', '3', '1501999159', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('2', '1', '10', '3', '1501999178', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('3', '1', '10', '3', '1501999582', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('4', '1', '11', '3', '1501999764', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('5', '1', '11', '3', '1501999817', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('6', '1', '11', '3', '1501999842', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('7', '3', '8', '3', '1502001292', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('8', '3', '8', '3', '1502001525', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('9', '3', '8', '3', '1502001616', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('10', '3', '8', '3', '1502001640', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('11', '2', '1', '3', '1502004690', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('12', '2', '1', '3', '1502004713', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('13', '2', '1', '3', '1502004744', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('14', '2', '1', '3', '1502004776', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('15', '1', '13', '3', '1502021968', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('16', '2', '6', '3', '1502023133', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('17', '3', '10', '3', '1502023150', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('18', '3', '11', '3', '1502064120', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('19', '3', '11', '3', '1502064134', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('20', '3', '11', '3', '1502064139', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('21', '1', '17', '8', '1502066003', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('22', '1', '17', '8', '1502066033', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('23', '1', '17', '3', '1502066079', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('24', '2', '7', '8', '1502066885', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('25', '2', '7', '8', '1502066899', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('26', '2', '7', '8', '1502066903', '3', '4', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('27', '2', '7', '8', '1502066907', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('28', '2', '7', '8', '1502066912', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('29', '2', '7', '8', '1502066916', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('30', '3', '12', '8', '1502067389', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('31', '3', '12', '8', '1502067397', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('32', '3', '12', '8', '1502067400', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('33', '1', '18', '8', '1502067535', '1', '2', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('34', '1', '18', '8', '1502067539', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('35', '2', '8', '8', '1502068625', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('36', '2', '8', '8', '1502068629', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('37', '2', '8', '8', '1502068633', '3', '4', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('38', '2', '8', '8', '1502068635', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('39', '2', '8', '8', '1502068638', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('40', '2', '8', '8', '1502068641', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('41', '3', '13', '8', '1502068987', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('42', '3', '13', '8', '1502068993', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('43', '2', '9', '8', '1502069349', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('44', '2', '9', '8', '1502069353', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('45', '2', '9', '8', '1502069356', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('46', '3', '14', '8', '1502072812', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('47', '3', '14', '8', '1502072816', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('48', '3', '14', '8', '1502072819', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('49', '1', '18', '3', '1502076727', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('50', '1', '17', '3', '1502076788', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('51', '1', '10', '3', '1502076804', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('52', '1', '32', '3', '1502077016', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('53', '1', '17', '3', '1502077028', '0', '2', '收回合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('54', '1', '18', '3', '1502077055', '0', '2', '收回合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('55', '1', '14', '3', '1502077318', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('56', '3', '14', '3', '1502077392', '0', '2', '已领取发票', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('57', '3', '13', '3', '1502077400', '0', '2', '已领取发票', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('58', '1', '22', '8', '1502152095', '1', '2', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('59', '1', '22', '8', '1502152105', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('60', '1', '16', '8', '1502152113', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('61', '1', '20', '8', '1502152138', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('62', '1', '20', '8', '1502152148', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('63', '2', '10', '8', '1502152318', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('64', '2', '10', '8', '1502152322', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('65', '3', '15', '8', '1502152402', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('66', '3', '15', '8', '1502152405', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('67', '2', '11', '72', '1502250020', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('68', '2', '11', '72', '1502250034', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('69', '2', '11', '72', '1502250088', '3', '4', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('70', '2', '11', '72', '1502250102', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('71', '2', '11', '72', '1502250116', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('72', '2', '11', '72', '1502250247', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('73', '3', '16', '3', '1502251172', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('74', '3', '16', '3', '1502253077', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('75', '3', '16', '3', '1502253088', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('76', '3', '16', '3', '1502253230', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('77', '2', '12', '3', '1502253415', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('78', '3', '16', '3', '1502253480', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('79', '3', '16', '3', '1502253753', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('80', '3', '16', '3', '1502253957', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('81', '2', '12', '3', '1502254001', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('82', '1', '25', '3', '1502254169', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('83', '1', '26', '72', '1502323214', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('84', '1', '26', '72', '1502323238', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('85', '1', '26', '72', '1502323251', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('86', '1', '27', '72', '1502323286', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('87', '1', '27', '72', '1502323300', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('88', '1', '27', '72', '1502323313', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('89', '2', '14', '72', '1502325773', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('90', '2', '14', '72', '1502325780', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('91', '3', '17', '72', '1502325873', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('92', '3', '17', '72', '1502325875', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('93', '3', '17', '72', '1502325877', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('94', '3', '17', '72', '1502325916', '0', '2', '已领取发票', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('95', '1', '29', '72', '1502328532', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('96', '1', '29', '72', '1502328541', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('97', '1', '29', '72', '1502328552', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('98', '1', '35', '72', '1504839803', '1', '2', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('99', '1', '35', '72', '1504839817', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('100', '1', '34', '72', '1504839830', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('101', '1', '34', '72', '1504839838', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('102', '1', '34', '72', '1504839888', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('103', '1', '42', '72', '1504840264', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('104', '2', '16', '72', '1504840550', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('105', '2', '16', '72', '1504840776', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('106', '2', '16', '72', '1504840780', '3', '4', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('107', '2', '16', '72', '1504840784', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('108', '2', '16', '72', '1504840788', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('109', '2', '16', '72', '1504840797', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('110', '3', '18', '72', '1504841302', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('111', '3', '18', '72', '1504841306', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('112', '3', '18', '72', '1504841318', '0', '2', '已领取发票', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('113', '1', '42', '72', '1504854175', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('114', '1', '43', '72', '1504854308', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('115', '1', '43', '72', '1504854449', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('116', '1', '43', '72', '1504854459', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('117', '1', '42', '72', '1504854468', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('118', '1', '44', '72', '1504854493', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('119', '2', '17', '72', '1504854624', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('120', '2', '17', '72', '1504854628', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('121', '2', '17', '72', '1504854632', '3', '4', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('122', '2', '17', '72', '1504854635', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('123', '2', '17', '72', '1504854639', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('124', '2', '17', '72', '1504854643', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('125', '2', '13', '72', '1504854646', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('126', '2', '13', '72', '1504854650', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('127', '1', '44', '72', '1504855056', '1', '2', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('128', '1', '44', '72', '1504855061', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('129', '1', '42', '72', '1504855065', '3', '4', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('130', '1', '42', '72', '1504855071', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('131', '1', '41', '72', '1504855076', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('132', '1', '41', '72', '1504855080', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('133', '1', '45', '72', '1504855146', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('134', '1', '46', '72', '1504855165', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('135', '2', '18', '72', '1504855629', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('136', '2', '18', '72', '1504855633', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('137', '2', '18', '72', '1504855636', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('138', '1', '45', '72', '1504855829', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('139', '1', '45', '72', '1504855833', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('140', '1', '45', '72', '1504855836', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('141', '1', '49', '72', '1504857401', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('142', '1', '41', '72', '1504857475', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('143', '1', '40', '72', '1504857559', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('144', '1', '33', '72', '1504864414', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('145', '2', '12', '72', '1505177501', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('146', '1', '47', '72', '1505178776', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('147', '1', '46', '72', '1505178788', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('148', '1', '47', '72', '1505178864', '0', '2', '审核被驳回', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('149', '1', '49', '72', '1505178934', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('150', '1', '49', '72', '1505178940', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('151', '1', '49', '72', '1505178947', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('152', '2', '19', '72', '1505203339', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('153', '2', '19', '72', '1505203344', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('154', '2', '19', '72', '1505203349', '3', '4', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('155', '2', '19', '72', '1505203354', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('156', '2', '19', '72', '1505203358', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('157', '2', '19', '72', '1505203363', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('158', '3', '20', '72', '1505204203', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('159', '3', '20', '72', '1505204207', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('160', '3', '20', '72', '1505204219', '3', '4', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('161', '3', '20', '72', '1505204224', '4', '5', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('162', '3', '20', '72', '1505204227', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('163', '3', '20', '72', '1505204230', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('164', '1', '57', '72', '1505355204', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('165', '1', '57', '72', '1505357083', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('166', '1', '57', '72', '1505357160', '3', '4', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('167', '1', '57', '72', '1505358043', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('168', '1', '57', '72', '1505358141', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('169', '1', '56', '72', '1505359326', '1', '2', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('170', '1', '60', '72', '1505467536', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('171', '1', '60', '72', '1505467540', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('172', '1', '60', '72', '1505467577', '3', '4', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('173', '1', '60', '72', '1505467581', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('174', '1', '59', '72', '1505467585', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('175', '1', '59', '72', '1505467718', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('176', '1', '59', '72', '1505467723', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('177', '1', '58', '72', '1505467727', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('178', '1', '58', '72', '1505467731', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('179', '1', '58', '72', '1505467735', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('180', '1', '59', '72', '1505468042', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('181', '1', '60', '72', '1505468046', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('182', '1', '61', '72', '1505468049', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('183', '2', '20', '72', '1506071256', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('184', '2', '20', '72', '1506071260', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('185', '2', '20', '72', '1506071265', '3', '4', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('186', '2', '20', '72', '1506071267', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('187', '2', '20', '72', '1506071270', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('188', '2', '20', '72', '1506071273', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('189', '2', '28', '72', '1506497036', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('190', '2', '28', '72', '1506497040', '2', '3', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('191', '2', '28', '72', '1506497045', '3', '4', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('192', '2', '28', '72', '1506497048', '4', '5', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('193', '2', '28', '72', '1506497051', '5', '6', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('194', '2', '28', '72', '1506497053', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('195', '3', '22', '72', '1506560459', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('196', '3', '22', '72', '1506560463', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('197', '3', '21', '72', '1506560472', '1', '2', '填写发票号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('198', '3', '21', '72', '1506560473', '0', '4', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('199', '1', '61', '72', '1506560529', '1', '2', '审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('200', '1', '61', '72', '1506560537', '2', '3', '生成合同号!审核通过,转到下一审核人', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('201', '1', '61', '72', '1506560541', '0', '1', '审核最终通过!', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('202', '1', '62', '72', '1506561315', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('203', '1', '63', '72', '1506561320', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('204', '1', '64', '72', '1506561323', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('205', '1', '65', '72', '1506561327', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('206', '1', '66', '72', '1506561332', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('207', '1', '43', '72', '1506561337', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('208', '1', '39', '72', '1506561340', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('209', '1', '40', '72', '1506561343', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('210', '1', '41', '72', '1506561347', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('211', '1', '28', '72', '1506561355', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('212', '1', '29', '72', '1506561359', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('213', '1', '30', '72', '1506561361', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('214', '1', '27', '72', '1506561364', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('215', '1', '25', '72', '1506561367', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('216', '1', '26', '72', '1506561370', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('217', '1', '23', '72', '1506561373', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('218', '1', '24', '72', '1506561376', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('219', '1', '20', '72', '1506561379', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('220', '1', '21', '72', '1506561381', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('221', '1', '15', '72', '1506561384', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('222', '1', '7', '72', '1506561387', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('223', '1', '8', '72', '1506561390', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('224', '1', '9', '72', '1506561392', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('225', '1', '1', '72', '1506561400', '0', '2', '已领取合同', null, '1');
INSERT INTO `guguo_verification_log` VALUES ('226', '1', '2', '72', '1506561404', '0', '2', '已领取合同', null, '1');

-- ----------------------------
-- View structure for guguo_view_employee_task
-- ----------------------------
DROP VIEW IF EXISTS `guguo_view_employee_task`;
CREATE ALGORITHM=UNDEFINED DEFINER=`xzm`@`%` SQL SECURITY DEFINER VIEW `guguo_view_employee_task` AS select `et`.`id` AS `id`,`et`.`task_name` AS `task_name`,`et`.`task_start_time` AS `task_start_time`,`et`.`task_end_time` AS `task_end_time`,`et`.`task_take_start_time` AS `task_take_start_time`,`et`.`task_take_end_time` AS `task_take_end_time`,`et`.`task_type` AS `task_type`,`et`.`task_method` AS `task_method`,`et`.`content` AS `content`,`et`.`public_to_take` AS `public_to_take`,`et`.`public_to_view` AS `public_to_view`,`et`.`like_count` AS `like_count`,`et`.`tip_count` AS `tip_count`,`et`.`reward_count` AS `reward_count`,`et`.`reward_max_num` AS `reward_max_num`,`et`.`create_employee` AS `create_employee`,`et`.`create_time` AS `create_time`,`et`.`status` AS `status`,`e`.`telephone` AS `telephone`,`e`.`truename` AS `truename`,group_concat(`ee`.`truename` separator ',') AS `public_to_truename`,`etr`.`reward_amount` AS `reward_amount`,`etr`.`reward_num` AS `reward_num`,`etr`.`reward_type` AS `reward_type`,`etr`.`reward_method` AS `reward_method`,`etr`.`reward_start` AS `reward_start`,`etr`.`reward_end` AS `reward_end`,`etr`.`ranking` AS `ranking`,`etr`.`re_amount` AS `re_amount`,`ett`.`target_type` AS `target_type`,`ett`.`target_customer` AS `target_customer`,`ett`.`target_description` AS `target_description`,`ett`.`target_num` AS `target_num`,if(isnull(`etc`.`comment_count`),0,`etc`.`comment_count`) AS `comment_count`,if(isnull(`ettt`.`partin_count`),0,`ettt`.`partin_count`) AS `partin_count`,`ettt`.`take_employees` AS `take_employees`,`gc`.`customer_name` AS `customer_name`,`vett`.`tip_employees` AS `tip_employees`,`vett`.`tip_moneys` AS `tip_moneys` from ((((((((`guguo_employee_task` `et` left join `guguo_employee` `e` on((`e`.`id` = `et`.`create_employee`))) left join `guguo_employee` `ee` on(find_in_set(`ee`.`id`,`et`.`public_to_take`))) left join `guguo_view_employee_task_reward` `etr` on((`etr`.`task_id` = `et`.`id`))) left join `guguo_employee_task_target` `ett` on((`ett`.`task_id` = `et`.`id`))) left join `guguo_customer` `gc` on((`ett`.`target_customer` = `gc`.`id`))) left join `guguo_view_employee_task_comment` `etc` on((`etc`.`task_id` = `et`.`id`))) left join `guguo_view_employee_task_take` `ettt` on((`ettt`.`task_id` = `et`.`id`))) left join `guguo_view_employee_task_tip` `vett` on((`vett`.`task_id` = `et`.`id`))) group by `et`.`id` order by `et`.`create_time` desc ;

-- ----------------------------
-- View structure for guguo_view_employee_task_comment
-- ----------------------------
DROP VIEW IF EXISTS `guguo_view_employee_task_comment`;
CREATE ALGORITHM=UNDEFINED DEFINER=`xzm`@`%` SQL SECURITY DEFINER VIEW `guguo_view_employee_task_comment` AS select `c`.`id` AS `id`,`c`.`task_id` AS `task_id`,if(isnull(count(`c`.`id`)),0,count(`c`.`id`)) AS `comment_count` from `guguo_employee_task_comment` `c` group by `c`.`task_id` ;

-- ----------------------------
-- View structure for guguo_view_employee_task_reward
-- ----------------------------
DROP VIEW IF EXISTS `guguo_view_employee_task_reward`;
CREATE ALGORITHM=UNDEFINED DEFINER=`xzm`@`%` SQL SECURITY DEFINER VIEW `guguo_view_employee_task_reward` AS select `r`.`id` AS `id`,`r`.`task_id` AS `task_id`,`r`.`reward_type` AS `reward_type`,`r`.`reward_method` AS `reward_method`,`r`.`reward_amount` AS `reward_amount`,`r`.`reward_num` AS `reward_num`,`r`.`reward_start` AS `reward_start`,`r`.`reward_end` AS `reward_end`,group_concat(if((`r`.`reward_start` = `r`.`reward_end`),concat('第',`r`.`reward_start`,'名 ',`r`.`reward_amount`,'元/名'),concat(concat_ws('-',`r`.`reward_start`,`r`.`reward_end`),'名 ',`r`.`reward_amount`,'元/名')) order by `r`.`task_id` DESC,`r`.`reward_amount` DESC separator ',') AS `ranking`,group_concat(`r`.`reward_amount` order by `r`.`task_id` DESC,`r`.`reward_amount` ASC separator '~') AS `re_amount` from `guguo_employee_task_reward` `r` group by `r`.`task_id` ;

-- ----------------------------
-- View structure for guguo_view_employee_task_take
-- ----------------------------
DROP VIEW IF EXISTS `guguo_view_employee_task_take`;
CREATE ALGORITHM=UNDEFINED DEFINER=`xzm`@`%` SQL SECURITY DEFINER VIEW `guguo_view_employee_task_take` AS select `t`.`id` AS `id`,`t`.`task_id` AS `task_id`,group_concat(`t`.`take_employee` separator ',') AS `take_employees`,if(isnull(count(`t`.`id`)),0,count(`t`.`id`)) AS `partin_count` from `guguo_employee_task_take` `t` group by `t`.`task_id` ;

-- ----------------------------
-- View structure for guguo_view_employee_task_tip
-- ----------------------------
DROP VIEW IF EXISTS `guguo_view_employee_task_tip`;
CREATE ALGORITHM=UNDEFINED DEFINER=`xzm`@`%` SQL SECURITY DEFINER VIEW `guguo_view_employee_task_tip` AS select `t`.`id` AS `id`,`t`.`task_id` AS `task_id`,group_concat(distinct `t`.`tip_employee` separator ',') AS `tip_employees`,sum(`t`.`tip_money`) AS `tip_moneys` from `guguo_employee_task_tip` `t` group by `t`.`task_id` ;
SET FOREIGN_KEY_CHECKS=1;
