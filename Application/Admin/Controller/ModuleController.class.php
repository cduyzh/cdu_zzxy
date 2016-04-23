<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 23:29
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Exception;

class ModuleController extends SuperController
{

    public function __construct()
    {
        parent::__construct();
        $module = 'module';
        $this->assign(compact(['module']));
    }

    /**
     * show the module page
     */
    public function index() {
        $pageName = '网站栏目管理';
        $modules = M('sitemodule')->where('fid = 0')->getField('id, modulename, moduletype, listnum, m_display');
        foreach ($modules as $key=>$module) {
            $modules[$key]['cmodule'] = M('sitemodule')->where("fid = $module[id]")->getField('id, fid, moduletype, modulename, listnum');
        }
        $this->assign(compact(['pageName', 'modules']));
        $this->display('set');
    }

    /**
     * show the add page
     */
    public function add() {
        $type = I('type');
        if($type === 'child') {
            $pageName = '添加子模块';
            $pid = I('pid');
            $fmodule = M('sitemodule')->where("id = $pid and fid = 0")->getField('0, id, modulename', 1)[0];
            if($fmodule == null) {
                $this->error('数据错误!请回到栏目页面重新操作!');
                exit(-1);
            }
            $this->assign(compact(['pageName', 'fmodule']));
            $this->display('add');
            return;
        } elseif ($type === 'parent') {
            $pageName = '添加模块';
            $this->assign(compact(['pageName', 'type']));
            $this->display('add');
            return;
        } else {
            $this->display('Errors/500');
        }
    }

    /**
     * create a module
     */
    public function create() {
        $data['fid'] = I('fid');
        $data['modulename'] = I('module-name');
        $data['moduletype'] = I('module-type');
        $data['listnum'] = I('list-num');
        $data['moption'] = I('moption');
        $data['url'] = I('url');
        $data['m_display'] = I('m-display');
        try {
            M('sitemodule')->data($data)->add();
        } catch (Exception $e) {
            $e->getMessage();
            exit(-1);
        }
        $this->success('成功创建模块!', '/admin/module');
    }

    /**
     *
     */
    public function delete() {
        $type = I('type');
        $id = I('id');
        if($type === 'child') {
            M('sitemodule')->where("id = $id and fid != 0")->delete();
        } elseif ($type === 'parent') {
            M('sitemodule')->where("id = $id or fid = $id")->delete();
        } else {
            $this->error('服务器错误!请刷新重试!');
        }
        $this->success('成功删除模块!', '/admin/module');
    }
}