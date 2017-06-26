/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : xsjb2b

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-10-14 18:17:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `oc_x_bd_visit_status`
-- ----------------------------
DROP TABLE IF EXISTS `oc_x_bd_visit_status`;
CREATE TABLE `oc_x_bd_visit_status` (
  `visit_status_id` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`visit_status_id`),
  UNIQUE KEY `visit_status_id` (`visit_status_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of oc_x_bd_visit_status
-- ----------------------------
INSERT INTO `oc_x_bd_visit_status` VALUES ('1', '没找到kp');
INSERT INTO `oc_x_bd_visit_status` VALUES ('2', '找到kp，直接拒绝');
INSERT INTO `oc_x_bd_visit_status` VALUES ('3', '找到kp，未注册，异议明确');
INSERT INTO `oc_x_bd_visit_status` VALUES ('4', '找到kp，已注册');
INSERT INTO `oc_x_bd_visit_status` VALUES ('5', '找到kp，已下首单');
