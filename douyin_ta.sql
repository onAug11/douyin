/*
 Navicat Premium Data Transfer

 Source Server         : api
 Source Server Type    : MySQL
 Source Server Version : 50733
 Source Host           : 81.70.16.87:3306
 Source Schema         : api

 Target Server Type    : MySQL
 Target Server Version : 50733
 File Encoding         : 65001

 Date: 27/02/2021 12:52:47
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for douyin_ta
-- ----------------------------
DROP TABLE IF EXISTS `douyin_ta`;
CREATE TABLE `douyin_ta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL COMMENT '被关注的人',
  `url` varchar(255) NOT NULL COMMENT '抖音主页',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `signature` varchar(255) DEFAULT NULL COMMENT '签名',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `following_count` int(11) NOT NULL DEFAULT '0' COMMENT '关注数',
  `follower_count` int(11) NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `total_favorited` int(11) NOT NULL DEFAULT '0' COMMENT '被点赞数',
  `aweme_count` int(11) NOT NULL DEFAULT '0' COMMENT '作品数',
  `favoriting_count` int(11) NOT NULL DEFAULT '0' COMMENT '喜欢作品数',
  `addTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `updateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
