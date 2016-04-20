<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 18:22
 */

namespace Admin\Controller;


use Think\Controller;

class SystemController  extends SuperController
{
    public function index() {
        $pageName = '系统参数设置';
        $setting['sitename'] = M('settings')->where('variable = \'sitename\'')->select()[0];
        $setting['sitephone'] = M('settings')->where('variable = \'sitephone\'')->select()[0];
        $setting['siteaddress'] = M('settings')->where('variable = \'siteaddress\'')->select()[0];
        $setting['siteemail'] = M('settings')->where('variable = \'siteemail\'')->select()[0];
        $setting['sitestatus'] = M('settings')->where('variable = \'sitestatus\'')->select()[0];
        $setting['siteclosereason'] = M('settings')->where('variable = \'siteclosereason\'')->select()[0];
        $setting['siteuserip'] = M('settings')->where('variable = \'siteuserip\'')->select()[0];
        $setting['siteadminip'] = M('settings')->where('variable = \'siteadminip\'')->select()[0];
        $setting['sitekeywords'] = M('settings')->where('variable = \'sitekeywords\'')->select()[0];
        $setting['sitedescription'] = M('settings')->where('variable = \'sitedescription\'')->select()[0];
        $this->assign(compact(['pageName', 'setting']));
        $this->display('System/main');
    }

    public function update() {
        $num = 0;
        foreach(I() as $key=>$item) {
            $datas[$num]['variable'] = $key;
            $datas[$num]['value'] = $item;
            $num++;
        }
        try {
            $M = M('settings');
            foreach($datas as $data) {
                $M->where("variable ='$data[variable]'")->save($data);
            }
        } catch(Exception $e) {
            $this->error('修改失败!请重试!');
        }
        $this->success('修改成功!');
    }

    public function home() {
        $pageName = '首页调用设置';
        $this->assign(compact(['pageName']));
        $this->display('System/home');
    }
}