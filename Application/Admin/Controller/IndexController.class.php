<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends SuperController {

    public function __construct()
    {
        parent::__construct();
        $module = 'system';
        $this->assign(compact(['module']));
    }

    public function index(){
        $this->display('/main');
    }
}