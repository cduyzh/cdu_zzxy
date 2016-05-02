<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 18:42
 */

namespace Admin\Controller;


use Think\Controller;

class SysUserController extends SuperController
{

    public function __construct()
    {
        parent::__construct();
        $moduleActive = 'system';
        $this->assign(compact(['moduleActive']));
    }

    public function index() {
        $pageName = '管理员列表';
        $admins = M('systemuser')->select();
        $this->assign(compact(['pageName', 'admins']));
        $this->display('User/sysUser');
    }

    /**
     * modify a system manager
     */
    public function modify() {
        $pageName = '修改管理员';
        $id = I()[0];

        if($_POST!=null) {
            $input['id'] = I('before');
            if ($id != $_SESSION['user']['id']) {
                $system_setting = implode(',', I("system-setting"));
                $website_setting = implode(',' ,I('website-setting'));
                $input['actions'] = $system_setting. ',' . $website_setting;
            }
            $input['username'] = I('name');
            if (I('password') != null) {
                $input['password'] =  md5(I('password'));
            }
//            todo 需要更多的人员
            $input['department'] = I('manager-class')==0? '超级管理员': '管理员';
            $input['userlevel'] = I('manager-class');
            $flag = M('systemuser')->where("id = $input[id]")->data($input)->save();
            if($flag) $this->success('修改成功!', '/admin/sysUser/modify/'.$input['id']);
        } else{
            $admin = M('systemuser')->find($id);
            $auth = $_SESSION['user']['actions'];
            $this->assign(compact(['pageName', 'admin', 'auth']));
            $this->display('User/addOrModify');
        }
    }

    public function add() {
        $flag = I('crsf');
        if($flag == md5(1)) { //1是新增
            $system_setting = implode(',', I("system-setting"));
            $website_setting = implode(',' ,I('website-setting'));
            $input['actions'] = $system_setting. ',' . $website_setting;
            $input['username'] = I('name');
            $input['password'] = md5(I('password'));
            $input['department'] = I('manager-class')==0? '超级管理员': '管理员';
            $input['userlevel'] = I('manager-class');

            if(!trim($input['username'])) {
                $this->hrefBack('请输入用户名!');
            }

            if(!trim($input['password'])) {
                $this->hrefBack('请输入密码!');
            }

            if(M('systemuser')->where("username = '$input[username]'")->select() != null) {
                $this->hrefBack('账号已存在!');
            }
            try {
                M('systemuser')->add($input);
            } catch (\Exception $e) {
                $this->hrefBack();
            }
            $this->success("添加管理账号成功!", '/admin/sysUser');
        } else {
            $pageName = '添加管理员';
            $flag = md5(1);
            $this->assign(compact(['pageName', 'flag']));
            $this->display('User/addOrModify');
        }
    }

    public function delete() {
        $id = I()[0];
//        todo 判断权限
        $_flag = M('systemuser')->delete($id);
        if($_flag) {
            $json['status'] = 1000;
            $json['data'] = '删除成功!';
        } else {
            $json['status'] = 1001;
            $json['data'] = '删除失败!';
        }
        echo json_encode($json);
    }
}