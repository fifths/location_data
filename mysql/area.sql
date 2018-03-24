DROP TABLE IF EXISTS `areas`;
CREATE TABLE `areas` (
 `areaId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
 `parentId` int(11) NOT NULL COMMENT '父ID',
 `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '地区编码',
 `areaName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '地区名称',
 `areaType` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:省 2:市 3:县区',
 PRIMARY KEY (`areaId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci