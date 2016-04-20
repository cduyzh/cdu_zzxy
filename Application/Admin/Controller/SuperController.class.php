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
    public function __construct()
    {
        parent::__construct();
        if($_SESSION['user'] == null) {
            $this->error('请重新登录!', '/admin/login');
        }
    }
}