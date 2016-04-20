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
    public function index() {
        $pageName = '管理员列表';
        $admins = M('systemuser')->select();
        $this->assign(compact(['pageName', 'admins']));
        $this->display('User/sysUser');
    }

    public function modify() {
        $pageName = '修改管理员';
        $id = I()[0];

        if($_POST!=null) {
//            dump($_POST);
            $input['id'] = I('before');
            $input['username'] = I('name');
            $input['password'] =  I('password') == null ? null: md5(I('password'));
//            todo 需要更多的人员
            $input['department'] = I('manager-class')==0? '超级管理员': '管理员';
            $input['userlevel'] = I('manager-class');
            $flag = M('systemuser')->data($input)->save();
            if($flag) $this->success('修改成功!', '/admin/sysUser/modify/'.$input['id']);
        } else{
            $admin = M('systemuser')->select($id)[0];
            $this->assign(compact(['pageName', 'admin']));
            $this->display('User/addOrModify');
        }
    }

    public function add() {
        $flag = I('crsf');

        if($flag == md5(1)) { //1是新增
            $input['username'] = I('name');
            $input['password'] = md5(I('password'));
            $input['department'] = I('manager-class')==0? '超级管理员': '管理员';
            $input['userlevel'] = I('manager-class');

            if(!trim($input['username'])) {
                exit('<script>alert("请输入用户名!")</script>');
            }

            if(!trim($input['password'])) {
                exit('<script>alert("请输入密码!")</script>');
            }

            if(!trim($input['username'])) {

            }

            M('systemuser')->add($input);
            dump($input);
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