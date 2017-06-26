/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : xsjb2b

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-10-14 18:17:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `oc_x_bd_visit_result`
-- ----------------------------
DROP TABLE IF EXISTS `oc_x_bd_visit_result`;
CREATE TABLE `oc_x_bd_visit_result` (
  `visit_achievement_id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `visit_result_id` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`visit_achievement_id`),
  KEY `visit_result_id` (`visit_achievement_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of oc_x_bd_visit_result
-- ----------------------------
INSERT INTO `oc_x_bd_visit_result` VALUES ('1', '0', '商家未下单');
INSERT INTO `oc_x_bd_visit_result` VALUES ('2', '1', '商家下单');
