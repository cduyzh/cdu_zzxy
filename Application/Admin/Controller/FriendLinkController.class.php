<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 23:49
 */

namespace Admin\Controller;


use Think\Controller;

class FriendLinkController extends Controller
{
    public function index() {
        $this->display('set');
    }
}