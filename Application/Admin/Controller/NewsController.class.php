<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/18/16
 * Time: 10:55
 */

namespace Admin\Controller;


use Think\Controller;

class NewsController extends SuperController
{
    public function __construct()
    {
        parent::__construct();
        $moduleActive = 'news';
        $this->assign(compact(['moduleActive']));
    }

    public function index() {
        $id = I('id');
        if($id != null) {
            $pageName = '新闻列表';
            $thisModule = M('sitemodule')->find($id);
            $articles = M('sitearticle')->where("moduleid = $id")
                ->order('addtime desc, listnum desc')
                ->getField('id,title,addtime,hit,listnum');
            $this->assign(compact(['pageName', 'articles', 'thisModule']));
            $this->display('list');
        } else {
            $this->hrefBack("参数错误!");
        }
    }

    public function edit() {
        $id = I('id');
        if ($id != null) {
            $pageName = '编辑新闻';
            $article = M('sitearticle')->find($id);
            if ($article == null) {
                $this->error('没有查找到新闻!');
                exit(-1);
            }
            $this->assign(compact(['pageName', 'article', 'id']));
            $this->display('add');
        } else {
            $pageName = '添加新闻';
            $id = I('mid');
            $this->assign(compact(['pageName', 'id']));
            $this->display('add');
        }
    }

    public function create() {
        $article = M('sitearticle');

        if (I('id') != null) {
            $data['id'] = I('id');
            $before = $article->find($data['id']);

            $data['title'] = I('title');
            $data['sortcontent'] = I('sort-content');
            $data['listnum'] = I('list-num');
            $data['url'] = $before['url'] == null ? '/Public/news/'.date('YmdHis').'/' : $before['url'];
            $data['content'] = $this->saveImage(I('content'), $data['url']);
            try {
                $article->where("id = $data[id]")->data($data)->save();
            } catch (\Exception $e) {
                $this->hrefBack("数据存储失败!请重新保存!");
                exit(-1);
            }
            $this->success("新闻修改成功!");
        } else {   //create a news
            $data['moduleid'] = I('mid');
            $data['title'] = I('title');
            $data['sortcontent'] = I('sort-content');
            $data['addtime'] = strtotime('now');
            $data['listnum'] = I('list-num');
            $data['url'] = '/Public/news/'.date('YmdHis').'/';     //访问文件地址;
            $data['content'] = $this->saveImage(I('content'), $data['url']);
            try {
                $article->where("id = $data[id]")->data($data)->save();
            } catch (\Exception $e) {
                $this->error("数据存储失败!请重新保存!");
                exit(-1);
            }
            $this->success("新闻修改成功!");
        }
    }

    /**
     * @param $content
     * @param $fileBase
     * @return mixed
     */
    public function saveImage($content, $fileBase) {
        if (trim($fileBase) == null) {
            $this->hrefBack('文件夹不存在!');
        } elseif(!is_dir($_SERVER['DOCUMENT_ROOT'] . $fileBase)) {
            if(!mkdir($_SERVER['DOCUMENT_ROOT'] . $fileBase)) { //创建文件夹
                $this->error("创建文件错误!");
                exit(-1);
            }
        } else {
            $handler = opendir($_SERVER['DOCUMENT_ROOT'] . $fileBase);
            while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
                if ($filename != "." && $filename != "..") {
                    $delFile[] = $filename;
                }
            }
            closedir($handler);
            foreach ($delFile as $file) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $fileBase . $file);
            }
        }

        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');  //转以后 content
        preg_match_all("/<img[^>]*\s*src=['\"]([^'\"]+)['|\"]/", $content, $imgs);  //获取所有的 src 链接

        for ($i=1; $i < count($imgs); ++$i) {
            foreach ($imgs[$i] as $key=>$img) {   //$img = dataType + Base64
                if(preg_match('/^data:image\/(\w+);base64,/', $img, $result)){
                    $imgBase = str_replace($result[0], '', $img);   //Base64
                    $new_file = $fileBase . strtotime('now') . $key . '.' . $result[1];    //存放文件地址,访问地址
                    if (file_put_contents($_SERVER['DOCUMENT_ROOT'] . $new_file, base64_decode($imgBase))){ //创建图片
                        $content = str_replace($img, $new_file, $content);
                    } else {
                        echo "<br>没有文件保存!!$_SERVER[DOCUMENT_ROOT]$new_file<br>";
                    }
                }
            }
        }
        return $content;
    }
}