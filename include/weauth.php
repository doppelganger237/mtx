<?php
//丑点丑点吧,至少能用
class WeAuth
{
  function __construct()
  {
    add_action('init',  array($this, 'weauth_oauth_init'));
    add_action('wp_ajax_bind_email_check', array($this,  'bind_email_check'));
    add_action('wp_ajax_nopriv_bind_email_check',  array($this, 'bind_email_check'));
    add_action('wp_ajax_weauth_oauth_login',  array($this, 'weauth_oauth_login'));
    add_action('wp_ajax_nopriv_weauth_oauth_login',  array($this, 'weauth_oauth_login'));
    add_action('wp_ajax_weauth_qr_gen',  array($this, 'weauth_qr_gen'));
    add_action('wp_ajax_nopriv_weauth_qr_gen',  array($this, 'weauth_qr_gen'));
    add_action('wp_ajax_weauth_check', array($this, 'weauth_check'));
    add_action('wp_ajax_nopriv_weauth_check',  array($this, 'weauth_check'));
    add_action('init',  array($this, 'add_cookie'));
  }

  //随机生成sk
  function get_weauth_token()
  {
    $sk = date("YmdHis") . mt_rand(10, 99);
    //缓存SK,好像没意义
    set_transient($sk, 1, 60 * 6);
    $key = $_SERVER['HTTP_HOST'] . '@' . $sk;
    return $key;
  }

  //ajax生成登录二维码
  function weauth_qr_gen()
  {
    $rest = implode("|", $this->get_weauth_qr());
    exit($rest);
  }


  //生成KEY和二维码
  function get_weauth_qr()
  {
    $qr64 = [];
    $qr64['key'] = $this->get_weauth_token();
    $qr64['qrcode'] = json_decode(file_get_contents('https://wa.isdot.net/qrcode?str=' . $qr64['key']), true)['qrcode'];
    return $qr64;
  }

  //检查登录状况,这里要返回非零就可以l
  function weauth_check()
  {
    if (isset($_POST['sk'])) {
      $rest = substr($_POST['sk'], -16); //key
      $weauth_cache = get_transient($rest . 'ok');
      if (!empty($weauth_cache)) {
        exit($rest); //key
      }
    }
  }

  //这只是生成账号而已,给weauth服务器提供的接口
  function weauth_oauth()
  {
    $weauth_user = $_GET['user'];
    $weauth_sk = esc_attr($_GET['sk']);
    $weauth_res = get_transient($weauth_sk);
    if (empty($weauth_res)) {
      return;
    }
    $weauth_user = stripslashes($weauth_user);
    $weauth_user = json_decode($weauth_user, true);
    $nickname = $weauth_user['nickName'];
    $wxavatar = $weauth_user['avatarUrl'];
    $openid = $weauth_user['openid'];
    $login_name = 'wx_' . wp_create_nonce($openid);
    if (is_user_logged_in()) {
      $user_id = get_current_user_id();
      update_user_meta($user_id, 'wx_openid', $openid);
      update_user_meta($user_id, 'simple_local_avatar', $wxavatar);
    } else {
      $weauth_user = get_users(
        array(
          'meta_key ' => 'wx_openid',
          'meta_value' => $openid
        )
      );
      if (is_wp_error($weauth_user) || !count($weauth_user)) {
        $random_password = wp_generate_password(12, false);
        $userdata = array(
          'user_login' => $login_name,
          'display_name' => $nickname,
          'user_pass' => $random_password,
          'nickname' => $nickname
        );
        $user_id = wp_insert_user($userdata);
        update_user_meta($user_id, 'wx_openid', $openid);
        update_user_meta($user_id, 'simple_local_avatar', $wxavatar);
      } else {
        $user_id = $weauth_user[0]->ID;
      }
    }
    set_transient($weauth_sk . 'ok', $user_id, 30); //用于登录的随机数，有效期为30秒
  }
  //初始化
  function weauth_oauth_init()
  {
    if (isset($_GET['user']) && isset($_GET['sk'])) {
      $this->weauth_oauth();
    }
  }

  //weauth自动登录
  function bind_email_check()
  {
    $mail = isset($_POST['email']) ? $_POST['email'] : false;
    if ($mail) {

      if (email_exists($mail)) {
        echo 0;
      } else {

        if(!is_email($mail)){
          echo 0;
          wp_die();
        };

        $user_id = get_current_user_id();
        wp_update_user(array('ID' => $user_id, 'user_email' => $mail));
        echo $user_id;
      }
    }
    wp_die();
  }


  function add_cookie()
  {
    $user_ID = get_current_user_id();
    if ($user_ID > 0) {
      $email = get_user_by('id', $user_ID)->user_email;
      if (!empty($email) && !isset($_COOKIE['bind'])) {
        setcookie('bind', 1, time() + 2592000, COOKIEPATH, COOKIE_DOMAIN, false);
      }
    } else {
      setcookie("bind", 1, time() - 1);
    }
  }

  //weauth自动登录,真正的接口
  function weauth_oauth_login()
  {
    $key = isset($_POST['spam']) ? $_POST['spam'] : false;
    $mail = isset($_POST['email']) ? $_POST['email'] : false;
    if ($key && $_POST['action'] == 'weauth_oauth_login') {
      $user_id = get_transient($key . 'ok');
      if ($user_id != 0) {
        wp_set_auth_cookie($user_id, true);
        if ($mail && !empty($mail) && is_email($mail)) {
          wp_update_user(array('ID' => $user_id, 'user_email' => $mail));
        }
        exit(wp_unique_id());
      }
    }
  }
}
