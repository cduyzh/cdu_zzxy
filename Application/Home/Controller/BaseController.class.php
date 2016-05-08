<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 5/3/16
 * Time: 17:03
 */

namespace Home\Controller;


use Think\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $Mod = M('sitemodule');
        $fmodules = $Mod->where('fid = 0 and m_display = 0')->order('listnum desc')->select();
        foreach ($fmodules as $key=>$item) {
            $fmodules[$key]['cmodule'] = $Mod->where("fid = $item[id] and m_display = 0")->order('listnum desc')->select();
        }
//        友情链接
        $link[0] = M('sitefriendlink')->where("linktype = 1")->select();
        $link[1] = M('sitefriendlink')->where("linktype = 2")->select();
        $link[2] = M('sitefriendlink')->where("linktype = 3")->select();
        $link[3] = M('sitefriendlink')->where("linktype = 4")->select();
//        网页参数
        $settings = M('settings')->getField('variable, value');//->select();
        $this->assign(compact(['fmodules', 'link', 'settings']));
    }

    public function hrefBack($message = '请重新试试!') {
        $script = "<script charset='utf-8'>window.history.back();alert('$message');</script>";
        exit($script);
    }
}