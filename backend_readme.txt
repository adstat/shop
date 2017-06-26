## Alex 20150526 初始化线上Git
>> 项目路径 [XSJ Server]/opt/xsj/src/git/www.git

## Alex 20150527 项目概要：后台管理界面，直接操作数据库数据
>> 后台配置文件为admin/config.php
>> admin目录为后台运行目录，运行入口为admin/index.php
>> catalog目录是原OpenCartv2的前台文件，后台管理项目中没有实际使用
>> image为主要为产品图片，产品图片路径存在oc_product的image中，添加了oss字段用来判别图片是否上传了阿里云OSS，如果是，客户端（weixin）将更改图片路径。
>> system为OpenCartv2原系统运行库，后台部分功能需要调用

## Alex 20150527 合并后台促销页面管理界面的问题
>> 已添加的商品无法显示，再次添加将覆盖原来设置
## make a test
