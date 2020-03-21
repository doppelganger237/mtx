<!DOCTYPE HTML>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,IE=10,IE=9,IE=8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link href="/favicon.ico" rel="icon" type="image/x-icon" />
    <?php wp_head();
    if (_mtx('headcode')) echo _mtx('headcode'); ?>
    <!-- 折寿  -->
    <title><?php echo _title(); ?></title>

    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/html5shiv/r29/html5.min.js"></script><![endif]-->

    <!-- 准备删除,写这里影响心情 -->
    <script>
        var ajax = {
            get: function(t, e) {
                var s = new XMLHttpRequest || new ActiveXObject("Microsoft,XMLHTTP");
                s.open("GET", t, !0), s.onreadystatechange = function() {
                    (4 == s.readyState && 200 == s.status || 304 == s.status) && e.call(this, s.responseText)
                }, s.send()
            },
            post: function(t, e, s) {
                var n = new XMLHttpRequest || new ActiveXObject("Microsoft,XMLHTTP");
                n.open("POST", t, !0), n.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"), n.onreadystatechange = function() {
                    4 != n.readyState || 200 != n.status && 304 != n.status || s.call(this, n.responseText)
                }, n.send(e)
            }
        };

        function setCookie(e, t, o) {
            var i = new Date;
            i.setDate(i.getDate() + o), document.cookie = e + "=" + escape(t) + (null == o ? "" : ";expires=" + i.toGMTString())
        };

        function getCookie(e) {
            var t, n = new RegExp("(^| )" + e + "=([^;]*)(;|$)");
            return (t = document.cookie.match(n)) ? t[2] : null
        };
    </script>
    <?php
    if (_mtx('customcss')) echo '<style type="text/css">' . _mtx('git_customcss') . '</style>';
    ?>
</head>
<?php flush();
?>

<body <?php body_class(); ?>>

    <!-- 进度条 -->
    <div class="scrollbar" id="bar"></div>
    <header id="header" class="header">
        <?php
        if (_mtx('touming_nav')) echo '<style type="text/css">#nav-header{background-color: rgba(85,84,85, 0.5);background: rgba(85,84,85, 0.5);color: rgba(85,84,85, 0.5);}</style>';
        ?>
        <div class="container-inner">
            <?php if (_mtx('make_logo_left') && !mtx_is_mobile()) {
                echo '<div class="g-logo pull-left">';
            } else {
                echo '<div class="g-logo pull-center">';
            }
            ?>
            <a href="/">
                <h1>
                    <?php if (!_mtx('head_enable_customlogo')) : ?>
                        <span class="g-mono"><?php bloginfo('name'); ?></span> <span class="g-bloger"><?php bloginfo('description'); ?></span>
                    <?php else :  ?>
                        <?php if (_mtx('head_customlogo')) {
                            echo sprintf('<img title="%s" alt ="%s" src="%s" >', get_bloginfo('name'), get_bloginfo('name'), _mtx('head_customlogo'));
                        } else {
                            echo sprintf('<img title="%s" alt ="%s" src="%s" >', get_bloginfo('name'), get_bloginfo('name'), esc_url(MTX_URL) . '/assets/img/logo.png');
                        }
                        ?>
                    <?php endif ?>
                </h1>
            </a></div>
        </div>

        <!-- 广告 -->
        <?php
        if (_mtx('git_toubuads') && _mtx('make_logo_left') && !mtx_is_mobile()) {
            echo '<div id="toubuads">' . _mtx('git_toubuads') . '</div>';
        }
        ?>
        <div id="nav-header" class="navbar">
            <!-- 搜索框 -->
            <div class="toggle-search pc-hide" style="float:right;position:absolute;top:0;right:0;"><i class="fa fa-search"></i></div>
            <div class="search-expand pc-hide" style="display:none;">
                <!-- 手机端? -->
                <div class="search-expand-inner pc-hide">
                    <?php if (_mtx('search_baidu')) : echo _mtx('search_baidu_code'); ?>
                </div>
            </div>
        <?php else : git_searchform();  ?>
        <?php endif ?>
        <!-- 很骚的导航栏 -->
        <ul class="nav">
            <?php
            echo str_replace('</ul></div>', '', preg_replace('/<div[^>]*><ul[^>]*>/', '', wp_nav_menu(array('theme_location' => 'nav', 'echo' => false))));
            ?>
            <li style="float:right;">
                <div class="toggle-search m-hide"><i class="fa fa-search"></i></div>
                <div class="search-expand" style="display: none;">
                    <div class="search-expand-inner">
                        <?php if (_mtx('search_baidu')) : echo _mtx('search_baidu_code'); ?>
                    </div>
                </div>
            <?php else : git_searchform();  ?>
            <?php endif ?>
            </li>
        </ul>
        </div>
    </header>
    <!-- 折寿,只允许弹窗 -->
    <section class="container">



    <?php if (_mtx('speedbar_enabled')) :?>
        <div class="speedbar">
            <div class="bull" style="float: left;margin-right: 8px;color: #666;font-size: 14px;"><i class="fa fa-volume-up" aria-hidden="true"></i></div>
            <?php
                $uid = get_current_user_id();
                $u_name = get_user_meta($uid, 'nickname', true);
            ?>
                <div class="login-sign pull-right">
                <?php if (is_user_logged_in()) {
                    /*判断是否登录 */
                    if (current_user_can('manage_options')) {
                        /*如果是管理员的话...*/
                        echo '<i class="fa fa-user"></i> <a href="' . esc_url(admin_url()) . '">' . $u_name . '</a>';
                        echo '&nbsp;&nbsp;<i class="fa fa-power-off"></i> ';
                        echo wp_loginout();
                        echo '';
                    } else {
                        /*如果不是管理员的话就..*/
                        echo '<i class="fa fa-user"></i> <a target="_blank" href="' . esc_url(get_author_posts_url($uid)) . '">' . $u_name . '</a>';
                        echo '&nbsp;&nbsp;<i class="fa fa-power-off"></i> ';
                        echo wp_loginout();
                        echo '';
                    }
                } else {
                    /*如果没有登录的话就...*/
/*                     global $wp;
                    $current_url = home_url(add_query_arg(array(), $wp->request)); */
                    echo '<i class="fa fa-sign-in" ></i>  <a target="_blank" class="signin-loader fancy-sign" href="#sign">登录</a>';
                    if (get_option('users_can_register')) {
                        echo '&nbsp;&nbsp;<i class="fa fa-pencil-square-o" ></i>  <a target="_blank" class="signup-loader fancy-sign" href="#sign">注册</a>';
                    }
                };
            
                ?>
                </div>

                <div class="toptip" id="callboard">
                    <ul style="font-size:16px;margin-top:2px;">
                        <?php echo _mtx('gundong');
                        
                        ?>
                    </ul>
                </div>

            <?php endif?>
        </div>

        <!-- 广告 -->
        <?php
        if (_mtx('git_adsite_01')) echo '<div class="banner banner-site">' . _mtx('git_adsite_01') . '</div>';
        ?>