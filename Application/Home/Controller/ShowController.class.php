<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 5/1/16
 * Time: 17:51
 */

namespace Home\Controller;


class showController
{
    public function index() {
        return $this->display('/single');
    }
}