<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 5/1/16
 * Time: 17:52
 */

namespace Home\Controller;


class ListController
{
    public function index() {
        return $this->display('/second');
    }
}