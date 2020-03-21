<?php
/*
	template name: 注册
	description: template for MTX theme
*/
get_header();
?>
<div class="pagewrapper clearfix">

    <div class="zhuce">
        <form name="registerform" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" class="ludou-reg">
            <p>
                <label for="user_login">用户名<br />
                    <input type="text" name="user_login" tabindex="1" id="user_login" class="input" value="<?php if(!empty($sanitized_user_login)) echo $sanitized_user_login; ?>" />
                </label>
            </p>

            <p>
                <label for="user_email">电子邮件<br />
                    <input type="text" name="user_email" tabindex="2" id="user_email" class="input" value="<?php if(!empty($user_email)) echo $user_email; ?>" size="25" />
                </label>
            </p>

            <p>
                <label for="user_pwd1">密码(至少6位)<br />
                    <input id="user_pwd1" class="input" tabindex="3" type="password" tabindex="21" size="25" value="" name="user_pass" />
                </label>
            </p>

            <p>
                <label for="user_pwd2">重复密码<br />
                    <input id="user_pwd2" class="input" tabindex="4" type="password" tabindex="21" size="25" value="" name="user_pass2" />
                </label>
            </p>

            <p class="submit">
                <input type="hidden" name="ludou_reg" value="ok" />
                <button class="button button-primary button-large" type="submit">注册</button>
            </p>
        </form>

    </div>



</div>
<?php get_footer(); ?>