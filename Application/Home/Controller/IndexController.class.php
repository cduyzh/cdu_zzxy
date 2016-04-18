<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        return $this->display('/index');
    }

    public function show() {
        return $this->display('/second');
    }

    public function single() {
        return $this->display('/single');
    }
}