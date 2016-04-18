<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 18:22
 */

namespace Admin\Controller;


use Think\Controller;

class SystemController  extends Controller
{
    public function index() {
        $this->display('System/main');
    }

    public function home() {
        $this->display('User/sysUser');
    }
}