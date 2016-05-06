<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 5/5/16
 * Time: 12:35
 */

namespace Admin\Controller;


class FileController extends SuperController
{

    public function upload(){
        $config = [
            'maxSize'    =>    3145728000000,
            'rootPath'   =>    $_SERVER['DOCUMENT_ROOT'].'/Public/files/',
            'savePath'   =>    date('Ymdh').'/',
            'saveName'   =>    array('uniqid',''),
            'autoSub'    =>    false,
            'replace'    =>    true,
        ];
        $upload = new \Think\Upload($config);// 实例化上传类
        //         上传单个文件
        $info   =   $upload->uploadOne(reset($_FILES));
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            $json['link'] = '/Public/files/' . $info['savepath'] . $info['savename'];
            echo json_encode($json);
        }
    }

    public function delete() {
        $file = I('file');
        $file = $_SERVER['DOCUMENT_ROOT'].$file;
        if (file_exists()) {
            $json['status'] = 1000;
            $json['data'] = '文件已删除!';
            echo json_encode($json);
        }
    }
}