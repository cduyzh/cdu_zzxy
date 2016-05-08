<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 5/1/16
 * Time: 17:51
 */

namespace Home\Controller;


class showController extends BaseController
{
    public function index() {
        $id = I('id');
        $Mod = M('sitemodule');
        $Art = M('sitearticle');

        if($id == null) {
            $mid = I('mid');

            $module = $Mod->find($id);

            if ($module == null) {
                $this->hrefBack('数据错误!请刷新重试!');
            }
            $module['cmodule'] = $Mod->where("fid = $module[id] and m_display = 0")->order('listnum desc')->select();
            $this->assign(compact(['module']));
            $this->display('/second');
        } else {
            $modArticle = $Art->find($id);
            if ($modArticle == null) {
                $this->hrefBack('没有查找到数据!请刷新重试!');
            }

            $Art->where("id = $id")->setInc('hit');

            $thisMod = $Mod->find($modArticle['moduleid']);
            $fid = $thisMod['fid'] == 0? $thisMod['id'] : $thisMod['fid'];
            $parentMod = $Mod->find($fid);
            if($thisMod != null) {
                try {
                    $results = $Mod->where("fid = $thisMod[fid] and fid != 0 and m_display = 0")->order("listnum desc")->getField("id, id, modulename");
                } catch (\Exception $e) {
                    $this->hrefBack($e->getMessage());
                }
            } else {
                $this->hrefBack('没有找到该模块内容!');
            }
            $pre = $Art->where("(moduleid = $modArticle[moduleid]) and (id > $modArticle[id]) and (listnum > 0)")
                ->order("id desc")->limit(1)->getField('id, id, title');
            $next = $Art->where("(moduleid = $modArticle[moduleid]) and (id < $modArticle[id]) and (listnum > 0)")
                ->order("id desc")->limit(1)->getField('id, id, title');
            $pre = reset($pre);
            $next = reset($next);
            $this->assign(compact(['modArticle', 'pre', 'next', 'results', 'thisMod', 'parentMod']));
            $this->display('/single');
        }
    }
}