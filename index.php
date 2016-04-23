<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','./Application/');

// 添加模块, 绑定 Home 模块
define('BUILD_MODEL_LIST','Home,Admin');

//定义全局文件变量
define('CSS_PATH','/Public/css');
define('JS_PATH','/Public/js');
define('IMG_PATH','/Public/imgs');
define('NOT_FOUND', '/Application/Home/View/notfound');

define('ADMIN_CSS_PATH','/Public/admin/css');
define('ADMIN_JS_PATH','/Public/admin/js');
define('ADMIN_IMG_PATH','/Public/admin/image');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单