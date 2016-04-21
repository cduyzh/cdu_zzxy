<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/17/16
 * Time: 20:06
 */

namespace Admin\Controller;


class DatabaseController extends SuperController
{
    public function index() {
        $pageName = '数据库优化';
        $sql = "select TABLE_NAME, TABLE_ROWS, DATA_LENGTH, INDEX_LENGTH, DATA_FREE FROM information_schema.TABLES where TABLE_SCHEMA = 'zzxy'";
        $tables = M()->query($sql);
//        dump($tables);
        $this->assign(compact(['tables', 'pageName']));
        $this->display('optDatabase');
    }

    public function optimize() {
        $tables = I('table');
        $tables = implode(', ', $tables);
        $sql = "OPTIMIZE TABLE $tables";

        if($tables == null) {
            $this->success('数据库已经是最优!不需要优化!', '/admin/database');
            exit(0);
        }

        try {
            M()->query($sql);
        } catch(\PDOException $e) {
            $this->error("数据库执行错误! ".$e->errorInfo);
            exit(-1);
        }
        $this->success('数据库优化成功!', '/admin/database');
    }

    public function backup() {
        $this->display('backup');
    }

    public function run() {
        $this->display('run');
    }
}