<?php
/**
 * Created by PhpStorm.
 * User: Mr.Mitol
 * Date: 4/18/16
 * Time: 17:08
 */

namespace Admin\Controller;


use Think\Controller;

class LoginController extends Controller
{
    public function index() {
        $name = I('username');
        $password = I('password');


        if(session('?user')) {
            redirect('/admin');
        }

        if($_POST != null) {
            $user = M('systemuser')->where(['username'=>$name, 'password'=>md5($password)])
                ->find();
            if(!$user) {
                $this->error('账号密码错误!请检查!');
            } else {
                session('user', $user);
                $data['id'] = $user['id'];
                $data['lastloginip'] = get_client_ip();
                $data['lastlogintime'] = strtotime('now');
                M('systemuser')->data($data)->save();
                redirect('/admin');
            }
        } else {
            $this->display('/login');
        }
    }

    public function logout() {
        dump($_SESSION);
        if($_SESSION['user'] !=null) {
            session(null);
            redirect('/admin/login');
        }
    }
}