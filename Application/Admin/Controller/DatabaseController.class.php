<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 20:06
 */

namespace Admin\Controller;


class DatabaseController extends SuperController
{
    public function index() {
        $this->display('optDatabase');
    }

    public function backup() {
        $this->display('backup');
    }

    public function run() {
        $this->display('run');
    }
}