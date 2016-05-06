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

    public function __construct()
    {
        parent::__construct();
        if($_SESSION['user'] == null) {
            redirect('/admin/login');
            exit(0);
        }
        $sysAuth = $this->sysAuth;
        $siteSet = $this->siteSet;
        $Mod = M('sitemodule');
        $actions = $_SESSION['user']['actions'];

        foreach ($actions as $action) {
            if (is_numeric($action)) {
                $userModule[] = $action;
            }
        }
        $userModule = '(' . implode(',', $userModule) . ')';


        if ($_SESSION['user']['userlevel'] < 2) { //        超级管理员可以查看所有模块
            $where = 'fid = 0';
        } else {
            $where = "m_display = 0 and id in $userModule";
        }

//        获取可以查看的主模块
        try {
            $sitemodules = $Mod->where($where)->order('m_display asc ,listnum desc')
                ->getField('id, modulename, moduletype, listnum, m_display');
        } catch (\Exception $e) {
            $sitemodules = null;
        }

        if ($_SESSION['user']['userlevel'] < 2) { //        超级管理员可以查看所有模块
            $where = '';
        } else {                                  //        普通管理员只可以查看显示的模块
            $where = " and m_display = 0";
        }

        foreach ($sitemodules as $key=>$module) {
            $sitemodules[$key]['cmodule'] = $Mod->where("fid = $module[id]" . $where)
                ->getField('id, fid, moduletype, modulename, listnum');
        }
//        exit();
        $this->assign(compact(['sitemodules', 'sysAuth', 'siteSet', 'actions']));
    }

    public function hrefBack($message = '请重新试试!') {
        $script = "<script>window.history.back();alert('$message');</script>";
        exit($script);
    }

    /**
     * @param null $string:  the permission should be check
     * @return bool
     */
    public function isAllow($string = null) {
        $user = M('systemuser')->find($_SESSION['user']['id'])['actions'];
        if ($user['userlevel'] < 2) return true;
        if ($string == null) return false;
        if (is_numeric($string)) $string = 'm'.$string.'m';
        return preg_match("/$string/", $user['actions']);
    }
}