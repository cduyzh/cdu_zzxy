<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/18/16
 * Time: 10:55
 */

namespace Admin\Controller;


use Think\Controller;

class NewsController extends SuperController
{
    public function index() {
        $this->display('add');
    }

    public function all() {
        $this->display('list');
    }
}