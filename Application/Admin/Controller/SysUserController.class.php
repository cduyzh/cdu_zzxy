<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 18:42
 */

namespace Admin\Controller;


use Think\Controller;

class SysuserController extends SuperController
{

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->isAllow('system_user,')) {
            $this->hrefBack('你没有该权限!');
        }
        $userType = C('userType');
        $moduleActive = 'system';
        $mainmodule = M('sitemodule')->where('fid =0 and m_display = 0 and listnum > 0')
            ->order('listnum desc')->select();
        $this->assign(compact(['moduleActive' , 'userType' , 'mainmodule']));
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
        $User = M('systemuser');
        $id = I('id');
        $input['id'] = I('before');

        $admin = $User->find($id);
        if ($admin == null) {
            $admin = $User->find($input['id']);
            if ($admin == null) {
                $this->error('没有查找到该管理员!');
            }
        }

        if($_POST!=null) {  // modify
            if ($input['id'] != $_SESSION['user']['id'] &&
                ($admin['userlevel'] == 2 || 0 == $_SESSION['user']['userlevel'])) {
                $system_setting = implode(',', I("system-setting"));
                $website_setting = implode(',' ,I('website-setting'));
                $module_setting = implode(',' ,I('module-setting'));
                $input['actions'] = $system_setting. ',' . $website_setting . ',' . $module_setting;
                $input['userlevel'] = I('manager-class');
            }
            $input['username'] = I('name');
            if (I('password') != null) {
                $input['password'] =  md5(I('password'));
            }
//            todo 需要更多的人员
            $input['department'] = I('department');
            $flag = $User->where("id = $input[id]")->data($input)->save();
            if($flag !== false) $this->success('修改成功!', '/admin/sysuser/modify?id='.$input['id']);
            else $this->error("修改失败!");
        } else{  // get info
            $url = 'modify';
            $auth = $admin['actions'];
            $this->assign(compact(['pageName', 'admin', 'url', 'auth']));
            $this->display('User/addOrModify');
        }
    }

    /**
     * new a user
     */
    public function add() {
        $flag = I('crsf');
        if($flag == md5(1)) { //1是新增
            $input['userlevel'] = I('manager-class');
            if($input['userlevel'] == 1) {
                $input['actions'] = M('systemuser')->where('userlevel = 0')->limit(1)->select()[0]['actions'];
            } else {
                $system_setting = implode(',', I("system-setting"));
                $website_setting = implode(',' ,I('website-setting'));
                $module_setting = implode(',' ,I('module-setting'));
                $input['actions'] = $system_setting. ',' . $website_setting . ',' . $module_setting;
            }
            $input['username'] = I('name');
            $input['password'] = md5(I('password'));
            $input['department'] = I('department');

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
            $this->success("添加管理账号成功!", '/admin/sysuser');
        } else {    //显示页面
            $pageName = '添加管理员';
            $flag = md5(1);
            $url = 'add';
            $this->assign(compact(['pageName', 'flag', 'url']));
            $this->display('User/addOrModify');
        }
    }

    public function delete() {
        $id = I()[0];
//        todo 判断权限

        $MUser = M('systemuser');
        $user = $MUser->find($id);
        if ($user['userlevel'] < 2 && $_SESSION['user']['userlevel'] !=0) {
            $json['status'] = 1002;
            $json['data'] = '超级管理员不可以删除!';
            echo json_encode($json);
            exit(-1);
        }

        if ($user['id'] == $_SESSION['user']['id']) {
            $json['status'] = 1002;
            $json['data'] = '不可以删除自己的账号!';
            echo json_encode($json);
            exit(-1);
        }

        $_flag = $MUser->delete($id);
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