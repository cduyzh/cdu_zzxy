<?php
namespace Home\Controller;

class IndexController extends BaseController {
    public function index(){
//        取出首页模块显示限制
        $siteIndex = M('settings')->where('variable like \'%siteindex%\'')->select();
//        取出各种新闻
        $articles[0] = $this->getSliderNews($siteIndex[0]['value']);
        for ($i = 1; $i< count($siteIndex); ++$i) {
            $articles[$i] = $this->getIndexArticles($siteIndex[$i]['value']);
            $siteIndex[$i] = explode(',', $siteIndex[$i]['value'])[3];
        }

//        dump($articles[6]);
        $this->assign(compact(['articles', 'siteIndex']));
        return $this->display('/index');
    }

    /**
     * @param null $params: get the setting param
     * @return mixed
     */
    public function getIndexArticles($params = null) {
        if($params != null) {
            $params = explode(',', $params);
            $sql = "select `id`,`title`,`isbold`,`isstickies`, `moduleid`,`addtime` from `hj_sitearticle` where `moduleid` in (
                		select id from `hj_sitemodule` where id = $params[0] or fid = $params[0]
                    ) order by addtime desc limit $params[1]";
            return M('sitearticle')->query($sql);
        }
        return false;
    }

    /**
     * @param null $params
     * @return mixed
     */
    public function getSliderNews($params = null) {
        if($params != null) {
            $params = explode(',', $params);
            $sql = "select `id`,`title`,`isbold`,`content`, `isstickies` from `hj_sitearticle` where `moduleid` in (
                		select id from `hj_sitemodule` where id = 23 or fid = 23
                    ) AND `content` LIKE '%<img%' or `content` LIKE '%<IMG%'
                    order by addtime desc limit 20";
//            todo 演示示例,真实代码在下边注释中
//            $sql = "select `id`,`title`,`isbold`,`content`, `isstickies` from `hj_sitearticle` where `moduleid` in (
//                		select id from `hj_sitemodule` where id = 23 or fid = 23
//                    ) AND `content` LIKE '%<img%' or `content` LIKE '%<IMG%'
//                    order by addtime desc limit 20";
            $results = M('sitearticle')->query($sql);

            foreach ($results as $key=>$item) {
                preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"]|<IMG[^>]+src=[\'"]([^\'"]+)[\'"]/', $item['content'], $url);
                $results[$key]['url'] = $url[1][0];
                $results[$key]['content'] = strip_tags($item['content']);
            }
            return $results;
        }
        return false;
    }
}