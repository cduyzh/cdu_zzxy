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

    public function __construct()
    {
        parent::__construct();
        $moduleActive = 'system';
        $this->assign(compact(['moduleActive']));
    }

    public function index() {
        $pageName = '数据库优化';
        $sql = "select TABLE_NAME, TABLE_ROWS, DATA_LENGTH, INDEX_LENGTH, DATA_FREE FROM information_schema.TABLES where TABLE_SCHEMA = 'zzxy'";
        $tables = M()->query($sql);
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
        $pageName = "备份数据库";
        $path = '/Public/backup/';
        $dbname = 'zzxy_';
        $file = $dbname . date("YmdHis");
        if($_POST != null) {
            $size = I('size') > 0? I('size'):null;
            $zip = I('zip');
            $dbhost = 'localhost';
            $dbuser = 'root';
            $dbpass = '@986078867';

//            todo 兼容版本
//            $type = ' --compatible=mysql323';
//            $type = ' --compatible=mysql40';

            $backup_file = $_SERVER['DOCUMENT_ROOT'] . '/Public/backup/' . $file . '.sql.zip';
            $command = "mysqldump -h$dbhost -u$dbuser -p$dbpass $dbname -c --default_character-set=utf8 | gzip > $backup_file";
            system($command);

//            TODO 压缩大小
//            if($zip == 1 && $size != null) {
//                system("split -b $size"."k $backup_file " . $backup_file .".");
//            }
        }

        //获取某目录下所有文件、目录名（不包括子目录下文件、目录名）
        $handler = opendir($_SERVER['DOCUMENT_ROOT'] . '/Public/backup/');
        while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename != "." && $filename != "..") {
                $files[] = $filename ;
            }
        }
        closedir($handler);

        $this->assign(compact(['pageName', 'file', 'files', 'path']));
        $this->display('backup');
    }

    public function delete() {
        $file = I('file');
        $json['data'] = $file;
        $file = $_SERVER['DOCUMENT_ROOT'] . '/Public/backup/' . $file;
        if (file_exists($file)) {
            unlink($file);
            $json['status'] = 1000;
        } else {
            $json['status'] = 1001;
        }
        echo json_encode($json);
    }

    public function run() {
        $pageName = '运行sql语句';
        $error = null;
        $aim = null;

        if($_POST != null && I('sql')) {
            try {
                $aim = M()->query(I('sql'));
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }
        $this->assign(compact(['pageName', 'aim', 'error']));
        $this->display('run');
    }
}