<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 18:42
 */

namespace Admin\Controller;


use Think\Controller;

class SysUserController extends Controller
{
    public function index() {
        $this->display('User/sysUser');
    }

    public function modify() {
        $this->display('User/addOrModify');
    }

    public function add() {
        $this->display('User/addOrModify');
    }
}