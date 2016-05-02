<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/20/16
 * Time: 18:55
 */

namespace Admin\Controller;


use Think\Controller;

class SuperController extends Controller
{
    protected $sysAuth = [
        'syste_set' => [
            'name'=>'系统参数设置',
            'url' =>'/admin/system',
        ],
        'syste_skin' => [
            'name'=>'首页调用设置',
            'url' =>'/admin/system/home',
        ],
        'syste_user' => [
            'name'=>'管理员账号管理',
            'url' =>'/admin/sysuser',
        ],
        'database' => [
            'name'=>'数据库优化',
            'url' =>'/admin/database',
        ],
        'database_backup' => [
            'name'=>'数据库备份',
            'url' =>'/admin/database/backup',
        ],
        'database_query' => [
            'name'=>'运行SQL语句',
            'url' =>'/admin/database/run',
        ],
        'database_replace' => [
            'name'=>'数据库处理',
            'url' =>'/admin/database/run',
        ],
    ];

    protected $siteSet = [
        'odule' => [
            'name'=>'网站栏目管理',
            'url' =>'/admin/module',
        ],
        'sitebook' => [
            'name'=>'网站留言管理',
            'url' =>'/admin/database/run',
        ],
        'friendlink' => [
            'name'=>'网站友情链接管理',
            'url' =>'/admin/friendlink',
        ],
    ];

//    protected  $stes = 'm1m,m5m,m9m,m15m,m18m,m23m,m28m,m32m,m33m,m43m,m35m,m61m';

    public function __construct()
    {
        parent::__construct();
        if($_SESSION['user'] == null) {
            redirect('/admin/login');
            exit(0);
        }
        $sysAuth = $this->sysAuth;
        $siteSet = $this->siteSet;
        $actions = $_SESSION['user']['actions'];
        $sitemodules = M('sitemodule')->where('fid = 0')->getField('id, modulename, moduletype, listnum, m_display');
        foreach ($sitemodules as $key=>$module) {
            $sitemodules[$key]['cmodule'] = M('sitemodule')->where("fid = $module[id]")->getField('id, fid, moduletype, modulename, listnum');
        }
        $this->assign(compact(['sitemodules', 'sysAuth', 'siteSet', 'actions']));
    }

    public function hrefBack($message = '请重新试试!') {
        $script = "<script>window.history.back();alert('$message');</script>";
        exit($script);
    }
}