<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/18/16
 * Time: 10:55
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Exception;

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
                ->order('isstickies asc, addtime desc, listnum desc')
                ->getField('id,title,addtime,hit,listnum,isstickies');
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
            if (!$this->isAllow($before['moduleid'])) {
                $this->hrefBack('你没有权限修改该模块下的新闻!');
            }

            $data['title'] = I('title');
            $data['sortcontent'] = I('sort-content');
            $data['listnum'] = I('list-num');
            $data['slider'] = I('slider');
            $data['content'] = html_entity_decode(I('content'), ENT_QUOTES, 'UTF-8');
            try {
                $article->where("id = $data[id]")->data($data)->save();
            } catch (\Exception $e) {
                $this->hrefBack("数据存储失败!请重新保存!");
                exit(-1);
            }
            $this->success("新闻修改成功!");
        } else {   //create a news
            if (!$this->isAllow(I('mid'))) {
                $this->hrefBack('你没有权限在该模块下发布新闻!');
            }
            $data['moduleid'] = I('mid');
            $data['title'] = I('title');
            $data['sortcontent'] = I('sort-content');
            $data['addtime'] = strtotime('now');
            $data['listnum'] = I('list-num');
            $data['slider'] = I('slider');
            $data['department'] = $_SESSION['user']['department'];
            $data['content'] = html_entity_decode(I('content'), ENT_QUOTES, 'UTF-8');
            try {
                $article->add($data);
            } catch (\Exception $e) {
                $this->error("数据存储失败!请重新保存!");
                exit(-1);
            }
            $this->success("成功添加新闻!", '/admin/news?id=' . $data['moduleid']);
        }
    }

    public function delete() {
        $id = I('id');
        $Art = M('sitearticle');
        $article = $Art->find($id);

        if ($article != null) {
            if (!$this->isAllow($article['moduleid'])) {
                $json['status'] = 1003;
                $json['data'] = '你无权操作该新闻!';
                echo json_encode($json);
                exit(-2);
            }

            try {
                $Art->delete($id);
            } catch (\Exception $e) {
                $json['status'] = 1001;
                $json['data'] = $e->getMessage();
                echo json_encode($json);
                exit(-1);
            }
            $this->deleteFiles($article['content']);
//            删除新闻的文件
            $json['status'] = 1000;
            $json['data'] = '新闻已删除!';
            echo json_encode($json);
            exit(0);
        } else {
            $json['status'] = 1002;
            $json['data'] = '该新闻不存在!';
            echo json_encode($json);
            exit(-1);
        }
    }

    /**
     * @param null $string : article's module_id
     * @return bool
     */
    public function isAllow($string = null)
    {
        try {
            $module = M('sitemodule')->find($string);
            $mid = $module['fid'] = 0 ? $string : $module['fid'];
        } catch (Exception $e) {
            return false;
        }
        return parent::isAllow($mid);
    }

    /**
     * top article
     */
    public function top() {
        $Art = M('sitearticle');
        $id = I('id');
        $article = $Art->find($id);

        if ($article != null) {
            if($this->isAllow($article['moduleid'])) {
                $Art->where("id = $id")->setField("isstickies", 0); //0表示最高的置顶
                $Art->where("isstickies < 9")->setInc("isstickies", 1);
                $this->success("新闻已置顶!");
            } else {
                $this->hrefBack("没有操作该模块新闻的权限!");
            }
        } else {
            $this->error("操作失败!");
        }
    }

    public function down() {
        $Art = M('sitearticle');
        $id = I('id');
        $article = $Art->find($id);

        if ($article != null) {
            if($this->isAllow($article['moduleid'])) {
                $Art->where("id = $id")->setField("isstickies", 10); //10表示没有置顶
                $Art->where("isstickies > 0 and isstickies < 10")->setDec("isstickies", 1);
                $this->success("新闻已取消置顶!");
            } else {
                $this->hrefBack("没有操作该模块新闻的权限!");
            }
        } else {
            $this->error("操作失败!");
        }
    }


    /**
     * @param $content
     * @param $fileBase     todo 没有用了
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
                if (!preg_match("/$file/", $content)) {  //不存在的图片才会删除
                    unlink($_SERVER['DOCUMENT_ROOT'] . $fileBase . $file);
                }
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

    private function deleteFiles($content)
    {
        $fileRoot = $_SERVER['DOCUMENT_ROOT'];
        preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"]|<IMG[^>]+src=[\'"]([^\'"]+)[\'"]/', $content, $files);
        dump($files);
        foreach ($files as $file) {
            foreach ($file as $item) {
                if(is_dir($fileRoot.$item) && $item !=="") {
                    unlink($fileRoot.$file);
                }
            }
        }
        preg_match_all('/<a[^>]+href=[\'"]([^\'"]+)[\'"]|<A[^>]+href=[\'"]([^\'"]+)[\'"]/', $content, $files);
        dump($files);
        foreach ($files as $file) {
            foreach ($file as $item) {
                if(is_dir($fileRoot.$item) && $item !=="") {
                    unlink($fileRoot.$file);
                }
            }
        }
    }
}