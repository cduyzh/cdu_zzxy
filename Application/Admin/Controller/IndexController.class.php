<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends SuperController {
    public function index(){
        $this->display('/main');
    }
}