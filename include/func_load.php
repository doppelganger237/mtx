<?php

require('shortcode.php'); //短代码
require('optimization.php'); //优化措施
require('avatar.php'); //头像功能
require('server.php'); //第三方服务
require('user.php'); //用户服务
require('seo.php'); //seo功能
require('email.php'); //邮箱功能
require('download.php'); //下载功能
require('login_front.php'); //下载功能
require('weauth.php'); //下载功能

new LoginFront();
new DownloadFront();
new WeAuth();
