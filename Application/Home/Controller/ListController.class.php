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

//        get page total number
        try {
            $pageNum = ceil($Art->where("moduleid = $thisMod[id]")->count()/15);
        } catch (\Exception $e) {
            $this->hrefBack('数据错误!请刷新重试!');
        }

        if ($page >= $pageNum) $page = $page % $pageNum;  //  防止页数超出

        if($thisMod['fid'] == 0) {  // parent module
            try {
                $_flag = $Mod->where("fid = $thisMod[id]")->order('listnum desc')->limit(1)->select()[0];
            } catch (\Exception $e) {
                $this->hrefBack('数据错误!请刷新重试!');
            }

            $thisMod = $_flag == null? $thisMod: $_flag;

            $modArticles = $Art->where("moduleid = $thisMod[id]")
                ->order("addtime desc, listnum desc")->limit(15)->page($page)
                ->getField('id, id, title, isstickies, isbold, moduleid, addtime, sortcontent');


        } elseif ($thisMod['fid'] > 0) {    //child module

            $id = $thisMod['fid'];  // Convenient for get "results"

            $modArticles = $Art->where("moduleid = $thisMod[id]")
                ->order("addtime desc, listnum desc")->limit(15)->page($page)
                ->getField('id, id, title, isstickies, isbold, moduleid, addtime, sortcontent');

        }else { //error data

            $this->hrefBack('没有找到该模块内容!');

        }

//            get left children module types
        try {
            $results = $Mod->where("fid = $id and m_display = 0")
                ->order("listnum desc")->getField("id, id, modulename");
        } catch (\Exception $e) {
            $this->hrefBack($e->getMessage());
        }

        $fid = $thisMod['fid'] == 0? $thisMod['id'] : $thisMod['fid'];
        $parentMod = $Mod->find($fid);

//        sent to template
        $this->assign(compact(['thisMod', 'results', 'modArticles', 'page', 'pageNum', 'parentMod']));

        switch ($thisMod['moduletype']) {
            case "DownLoad":
            case "News":
                if ($thisMod['moption'] == 'Both') {    // both with image and text

                    $teachers = $Art->where("moduleid = $thisMod[id]")
                        ->order("addtime desc, listnum desc")->limit(15)->page($page)
                        ->select();

                    foreach ($teachers as $key=>$item) {
                        preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"]|<IMG[^>]+src=[\'"]([^\'"]+)[\'"]/', $item['content'], $url);
                        $teachers[$key]['url'] = $url[1][0] == "" ? $url[2][0] : $url[1][0];
                        $teachers[$key]['content'] = strip_tags($item['content']);
                    }

                    $this->assign(compact('teachers'));
                    $this->display('/teachers');
                    exit(0);
                }
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