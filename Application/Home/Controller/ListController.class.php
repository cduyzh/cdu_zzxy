<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 5/1/16
 * Time: 17:52
 */

namespace Home\Controller;


use Think\Exception;

class ListController extends BaseController
{

    public function index() {
        $id = I('id');
        $page = I('page') < 1 ? 0 : I('page');
        $Mod = M('sitemodule');
        $Art = M('sitearticle');
        $thisMod = $Mod->find($id);
//        获取 page 总页数
        $pageNum = ceil($Art->where("moduleid = $thisMod[id]")->count()/15);
        if ($page >= $pageNum) $page = $page % $pageNum;  //  防止页数超出

        if($thisMod['fid'] == 0) {  //分类模块

            $_flag = $Mod->where("fid = $thisMod[id]")->order('listnum desc')->limit(1)->select()[0];

            $thisMod = $_flag == null? $thisMod: $_flag;

            $modArticles = $Art->where("moduleid = $thisMod[id]")
                ->order("addtime desc, listnum desc")->limit(15)->page($page)
                ->getField('id, id, title, isstickies, isbold, moduleid, addtime');


        } elseif ($thisMod['fid'] > 0) {    //子模块

            $id = $thisMod['fid'];  // 此时用于在下边方便取出 results

            $modArticles = $Art->where("moduleid = $thisMod[id]")
                ->order("addtime desc, listnum desc")->limit(15)->page($page)
                ->getField('id, id, title, isstickies, isbold, moduleid, addtime');

        }else { //错误数据

            $this->hrefBack('没有找到该模块内容!');

        }

//            取左边的二级分类
        try {
            $results = $Mod->where("fid = $id and m_display = 0")
                ->order("listnum desc")->getField("id, id, modulename");
        } catch (\Exception $e) {
            $this->hrefBack($e->getMessage());
        }

        $fid = $thisMod['fid'] == 0? $thisMod['id'] : $thisMod['fid'];
        $parentMod = $Mod->find($fid);

//        传递模板
        $this->assign(compact(['thisMod', 'results', 'modArticles', 'page', 'pageNum', 'parentMod']));

        switch ($thisMod['moduletype']) {
            case "DownLoad":
            case "News":
                $this->display('/second');
                break;
            case "Simple":
                    $id = reset($modArticles)['id'];
                    if ($id != null) {
                        $Art->where("id = $id")->setInc("hit", 1);
                        $modArticle = $Art->find($id);
                    }
                    $this->assign(compact(['modArticle']));
                    $this->display('/single');
                break;
            case "Sub":
                $this->hrefBack("系统错误!刷新重试!");
                break;
            default:
                echo 'heh';
                break;
        }
    }
}