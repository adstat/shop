## Alex 20150526 初始化线上Git
>> 项目路径 [XSJ Server]/opt/xsj/src/git/admin.git

## Alex 20150527 项目概要：所有用户客户端统一下单接口
>> API项目配置文件为config.php，对比上一次拷贝的版本，新增了缓存(Redis)的配置
>> 使用PHP-XMLRPC接口，调用路径.../xmlrpc/v1/index.php
>> module下存放主要模块的方法
>> system主要是系统组件，数据库，日志，缓存等
>> xmlrpc是接口文件，xmlrpc/v1目录下, index.php为入口，/compat, xmlrpc.php,xmlrpc_wrappers.php,xmlrpcs.php为接口库
>> logs主要日志，目前记录了SQL错误和订单数据提交日志(在module/order.php的addOrder方法中)

## Alex 20150527 合并前台促销页面
>> 合并了函数注册文件和module/product.php中的函数
## make a git test
