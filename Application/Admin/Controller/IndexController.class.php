<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends SuperController {

    public function __construct()
    {
        parent::__construct();
        $moduleActive = 'system';
        $this->assign(compact(['moduleActive']));
    }

    public function index(){
        $this->display('/main');
    }
}