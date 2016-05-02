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
    private $linkTypes = [
        1 => '校内院系链接模块友情链接',
        2 => '管理机构链接模块友情链接',
        3 => '校外相关链接模块友情链接',
        4 => '院长书记信箱模块友情链接',
    ];

    public function __construct()
    {
        parent::__construct();
        $moduleActive = 'module';
        $this->assign(compact(['moduleActive']));
    }

    /**
     * show the link module index page
     */
    public function index() {
        $pageName = '友情链接管理';
        for ($i = 1; $i<5; ++$i) {
            $links[] = M('sitefriendlink')->where("linktype = $i")->select();
        }
        $this->assign(compact(['pageName', 'links']));
        $this->display('list');
    }

    /**
     * show and create new link
     */
    public function add() {
        $pageName = '新加友情链接';
        $type = 'add';
        $linkModule = $this->linkTypes;
        $data['title'] = I('title');
        if ($data['title'] != null) {
            $data['url'] = I('url');
            $data['linktype'] = I('link-type');
            $data['type'] = I('type');
            try {
                M('sitefriendlink')->data($data)->add();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                exit(-1);
            }
            $this->success("友情链接添加成功!", '/admin/friendlink');
            exit(-1);
        }
        $this->assign(compact(['pageName', 'type', 'linkModule']));
        $this->display('add');
    }

    /**
     * modify link
     */
    public function edit() {
        $pageName = '修改友情链接';
        $linkModule = $this->linkTypes;
        $id = I('id');
        $aim = I('aim');
        if($id != null) {
            $link = M('sitefriendlink')->find($id);
            if ($link != null) {
                $type = 'edit';
                $this->assign(compact(['pageName', 'link', 'linkModule', 'type']));
                $this->display('add');
                exit(0);
            }
        } elseif ($aim != null) {
            $data['title'] = I('title');
            $data['url'] = I('url');
            $data['linktype'] = I('link-type') < 5? I('link-type'): 1;
            $data['type'] = I('type') < 2 ? I('type') : 0;
            try {
                M('sitefriendlink')->where("id = $aim")->data($data)->save();
            } catch (\Exception $e) {
                $this->error("没有找到该连接数据!");
                exit(-1);
            }
            $this->success("修改链接成功!", '/admin/friendlink');
            exit(0);
        }
        $this->error("没有找到该连接数据!");
    }

    /**
     * delete a exist link
     */
    public function delete() {
        $id = I('id');
        try {
            M('sitefriendlink')->where("id = $id")->delete();
        } catch (\Exception $e) {
            $json['status'] = 1001;
            $json['data'] = $e->getMessage();
            echo json_encode($json);
            exit(-1);
        }
        $json['status'] = 1000;
        $json['data'] = "删除成功!";
        echo json_encode($json);
    }
}