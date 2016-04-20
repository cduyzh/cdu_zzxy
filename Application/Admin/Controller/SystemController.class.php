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
    /**
     * show
     */
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

    /**
     * update
     */
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

    /**
     * show
     */
    public function home() {
        $pageName = '首页调用设置';
        $modules = M('settings')->where('variable like \'siteindex%\'')
            ->order('variable asc')->select();
        foreach($modules as $key=>$module) {
            $modules[$key]['value'] = explode(',', $module['value']);
        }
        $options = M('sitemodule')->where('fid != 0')->select();
        $this->assign(compact(['pageName', 'modules', 'options']));
        $this->display('System/home');
    }

    /**
     * set home module show
     */
    public function homeSet() {
        if($_POST != null) {
            for($i=0; $i<7; $i++) {
                $flag = 0;
                $flag = M('settings')->where("variable = '". I('index'.$i) ."'") ->count();
                $flag += M('sitemodule')->where('id ='. I('siteindex'.$i))->count();
                if($flag < 2) {
                    $this->error("不能有数据为空!第".($i+1)."行数据填写错误!");
                }
                if(!is_numeric(I('num'.$i)) || I('num'.$i)<1) {
                    $this->error("第".($i+1)."行调用条数填写错误!必须是大于0的整数");
                }

                if(!is_numeric(I('words'.$i)) || I('words'.$i)<1) {
                    $this->error("第".($i+1)."行标题字数填写错误!必须是大于0的整数");
                }
                try {
                    date(I('date'.$i));
                } catch(\Exception $e) {
                    $this->error("第".($i+1)."行时间格式填写错误!请修改!");
                }
                $datas[$i]['variable'] = I('index'.$i);
                $datas[$i]['value'] = I('siteindex'.$i) . ',' .
                                    I('num'.$i) . ',' .
                                    I('words'.$i) . ',' .
                                    I('date'.$i);
            }
        }
        foreach($datas as $data) {
            try {
                M('settings')->where('variable=\''.$data['variable'].'\'')->data($data)->save();
            } catch(\PDOException $e) {
                $this->error("服务器错误!请刷新重试!");
            }
        }
        $this->success(" 更新数据成功!");
    }
}