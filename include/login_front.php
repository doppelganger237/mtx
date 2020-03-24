<?php
//等待跟log.php结合

class LoginFront
{

  function login_html()
  {

?>
    <div class="sign" id="sign" style="display:none;">
      <div class="sign-tips"></div>
      <form id="sign-in">
        <h3><small class="signup-loader">切换注册</small>登录</h3>
        <div class="open-oauth text-center"><a class="btn btn-weixin wx_openid"><i class="fa fa-weixin"></i></a></div>
        <h6>
          <label for="inputEmail">账号</label>
          <input type="text" name="username" class="form-control" id="inputEmail" placeholder="用户名或邮箱">
        </h6>
        <h6>
          <label for="inputPassword">密码</label>
          <input type="password" name="password" class="form-control" id="inputPassword" placeholder="登录密码">
        </h6>
        <h6>
          <label for="captcha">验证码</label>
          <div>
            <input style="width: 190px;display: inline;" type="text" name="captcha" id="login-captcha" class="form-control" id="captcha" placeholder="验证码">
            <img class="captcha-img" width="100" height="30">
          </div>
        </h6>
        <div class="sign-submit">
          <label><input type="checkbox" checked="checked" name="remember" value="forever">记住我</label>
          <div class="sign-info pull-right"><a href="<?php echo _mtx('user_rp') ? get_permalink(_mtx('user_rp')) : '' ?>">找回密码？</a></div>
          <input type="button" class="btn btn-primary btn-block signsubmit-loader" name="submit" value="登录">
          <input type="hidden" name="action" value="signin">
        </div>
      </form>
      <form id="sign-up">
        <h3><small class="signin-loader">切换登录</small>注册</h3>
        <div class="open-oauth text-center"><a class="btn btn-weixin wx_openid"><i class="fa fa-weixin"></i></a></div>
        <h6>
          <label for="inputName">昵称</label>
          <input type="text" name="name" class="form-control" id="inputName" placeholder="设置昵称">
        </h6>
        <h6>
          <label for="inputEmail2">邮箱</label>
          <input type="email" name="email" class="form-control" id="inputEmail2" placeholder="邮箱">
        </h6>
        <h6>
          <label for="captcha">验证码</label>
          <div>
            <input style="width: 190px;display: inline;" type="text" name="captcha" class="form-control" id="register-captcha" placeholder="验证码">
            <img class="captcha-img" width="100" height="30">
          </div>
        </h6>
        <div class="sign-submit">
          <input type="button" class="btn btn-primary btn-block signsubmit-loader" name="submit" value="快速注册">
          <input type="hidden" name="action" value="signup">
        </div>
      </form>
    </div>

<?
  }



  function __construct()
  {
    if (!is_user_logged_in()) {
      add_action('wp_footer', array($this, 'login_html'), 100);
    }
  }
}
