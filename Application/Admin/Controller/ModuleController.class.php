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
        $moduleActive = 'module';
        $this->assign(compact(['moduleActive']));
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
        $this->assign('url', 'create');
        switch ($type) {
            case 'child':
                $pageName = '添加子模块';
                $pid = I('pid');
                $fmodule = M('sitemodule')->where("id = $pid and fid = 0")->getField('0, id, modulename', 1)[0];
                if($fmodule == null) {
                    $this->error('数据错误!请回到栏目页面重新操作!');
                    exit(-1);
                }
                $this->assign(compact(['pageName', 'fmodule']));
                $this->display('add');
                break;
            case 'parent':
                $pageName = '添加模块';
                $this->assign(compact(['pageName', 'type']));
                $this->display('add');
                break;
            default:
                $this->display('Errors/500');
                break;
        }
    }

    public function edit() {
        $type = I('type');
        $Mod = M('sitemodule');
        $this->assign('url', 'modify');

        switch ($type) {
            case 'child':
                $pageName = '修改子模块';
                $id = I('id');
                $thisModule = $Mod->find($id);
                $fmodule = $Mod->find($thisModule['fid']);
                $this->assign(compact('pageName', 'thisModule', 'fmodule'));
                $this->display('add');
                break;
            case 'parent':
                $pageName = '修改模块';
                $id = I('id');
                $thisModule = $Mod->find($id);
                $this->assign(compact('pageName', 'thisModule', 'type'));
                $this->display('add');
                break;
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
     * modify a module
     */
    public function modify() {
        $data['id'] = I('id');
        $data['modulename'] = I('module-name');
        $data['moduletype'] = I('module-type');
        $data['listnum'] = I('list-num');
        $data['moption'] = I('moption');
        $data['url'] = I('url');
        $data['m_display'] = I('m-display');
        dump($data);
        try {
            M('sitemodule')->where("id = $data[id]")->save($data);
        } catch (Exception $e) {
            $e->getMessage();
            exit(-1);
        }
//        todo 需要检验是否修改成功!
//        $message = "<script>alert('模块已修改!');location.href(".$_SERVER['HTTP_REFERER'].");</script>";
//        exit($message);
//        $this->success('成功创建模块!');
    }

    /**
     * 删除模块
     */
    public function delete() {
        $type = I('type');
        $id = I('id');
        if($type === 'child') {
            M('sitemodule')->where("id = $id and fid != 0")->delete();
            M('sitearticle')->where("moduleid = $id")->delete();
        } elseif ($type === 'parent') {
            $sql = "DELETE FROM `hj_sitearticle` where moduleid in (
	                  SELECT id FROM `hj_sitemodule` where `fid`= $id or `id`= $id)";
            M('sitemodule')->where("id = $id or fid = $id")->delete();
            M()->execute($sql);
        } else {
            $json['status'] = 1003;
            $json['info'] = '服务器错误!请刷新重试!';
            echo json_encode($json);
            exit(-1);
        }
        $json['status'] = 1000;
        $json['info'] = '模块已删除!';
        echo json_encode($json);
    }
}