<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 23:49
 */

namespace Admin\Controller;

class FriendLinkController extends SuperController
{
    public function index() {
        $this->display('list');
    }

    public function add() {
        $this->display('add');
    }
}