<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);


/* Define some constant */
define('MTX_VER', wp_get_theme()->get('Version'));
define('MTX_URL', get_template_directory_uri());


require('admin/theme-widgets.php');
require('admin/theme-metabox.php');
require('include/func_load.php');
require('admin/theme-ajax.php');

/* 

Prepare to rebuild points system

if (!defined('POINTS_CORE_DIR')) {
    require('modules/points.php');
}
 */

/*  Require functions for admin */
if (is_admin()) {
    require_once get_stylesheet_directory() . '/functions-admin.php';
}


function mtx_setup()
{
    //添加主题特性
    add_theme_support('post-thumbnails'); //缩略图设置
    add_theme_support('post-formats', array('aside')); //增加文章形式
    add_editor_style('editor-style.css');
    //定义菜单
    if (function_exists('register_nav_menus')) {
        register_nav_menus(array(
            'nav' => '网站导航',
            'pagemenu' => '页面导航'
        ));
    }
}
add_action('after_setup_theme', 'mtx_setup');

function mtx_widgets_init()
{
    $sidebars = array(
        'widget_sitesidebar' => '全站侧栏',
        'widget_sidebar' => '首页侧栏',
        'widget_othersidebar'    => '分类/标签/搜索页侧栏',
        'widget_postsidebar'     => '文章页侧栏',
        'widget_pagesidebar'     => '页面侧栏'
    );
    foreach ($sidebars as $key => $value) {
        register_sidebar(array(
            'name'          => $value,
            'id'            => $key,
            'before_widget' => '<div class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        ));
    };
}
add_action('widgets_init', 'mtx_widgets_init');


//有几把用
function git_err($ErrMsg)
{
    header('HTTP/1.1 405 Method Not Allowed');
    //echo $ErrMsg;
    exit($ErrMsg);
}

function _mtx($name, $default = false)
{
    $option_name = 'mtx';
    $options = get_option($option_name);
    if (isset($options[$name])) {
        return $options[$name];
    }

    return $default;
}

//显示数据库查询次数、查询时间及内存占用的代码
function mtx_performance($visible = false)
{
    $stat = sprintf('%d 次查询 用时 %.3f 秒, 耗费了 %.2fMB 内存', get_num_queries(), timer_stop(0, 3), memory_get_peak_usage() / 1024 / 1024);
    echo $visible ? $stat : "<!-- {$stat} -->";
}
add_action('wp_footer', 'mtx_performance', 20);


//面包屑导航
function mtx_breadcrumbs()
{
    if (!is_single() || get_post_type() != 'post') {
        return false;
    }
    $categorys = get_the_category();
    $category = $categorys[0];
    return '<a title="返回首页" href="' . home_url() . '"><i class="fa fa-home"></i></a> <small>></small> ' . get_category_parents($category->term_id, true, ' <small>></small> ') . '<span class="muted">' . get_the_title() . '</span>';
}

//无用
function _moloader($name = '', $apply = true)
{
    if (!function_exists($name)) {
        include get_stylesheet_directory() . '/modules/' . $name . '.php';
    }

    if ($apply && function_exists($name)) {
        $name();
    }
}

function mtx_script()
{
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_style('style', MTX_URL . '/dist/css/main.css', false, '1.0');
        wp_enqueue_style('style');

        //使用requirejs管理
        wp_register_script('requirejs', 'https://cdn.jsdelivr.net/npm/requirejs@2.3.6/require.min.js', array(), MTX_VER, true);

        //蛋疼
        if (_mtx('jqcdn_source') == '1') {
            wp_register_script('jquery', MTX_URL . '/dist/js/main.js', array('requirejs'), MTX_VER, true);
        } else {
            wp_register_script('jquery', MTX_URL . '/dist/js/main.js', array('requirejs'), MTX_VER, true);
        }
        wp_enqueue_script('requirejs');
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'mtx_script');


if (!function_exists('mtx_paging')) {
    function mtx_paging()
    {
        //分页前广告
        if (_mtx('feed_ad')) {
            echo '<article class="excerpt">' . _mtx('feed_ad') . '</article>';
        }
        $p = 4;
        if (is_singular()) {
            return;
        }
        global $wp_query, $paged;
        $max_page = $wp_query->max_num_pages;
        if ($max_page == 1 ) {
            return;
        }
        
        echo '<div class="pagination"><ul>';
        if (empty($paged)) {
            $paged = 1;
        }
        // echo '<span class="pages">Page: ' . $paged . ' of ' . $max_page . ' </span> ';
        echo '<li class="prev-page">';
        previous_posts_link('上一页');
        echo '</li>';
        if ($paged > $p + 1) {
            p_link(1, '<li>第一页</li>');
        }
        if ($paged > $p + 2) {
            echo "<li><span>&middot;&middot;&middot;</span></li>";
        }
        for ($i = $paged - $p; $i <= $paged + $p; $i++) {
            if ($i > 0 && $i <= $max_page) {
                $i == $paged ? print "<li class=\"active\"><span>{$i}</span></li>" : p_link($i);
            }
        }
        if ($paged < $max_page - $p - 1) {
            echo "<li><span> ... </span></li>";
        }
        //if ( $paged < $max_page - $p ) p_link( $max_page, '&raquo;' );
        echo '<li class="next-page">';
        next_posts_link('下一页');
        echo '</li>';
        // echo '<li><span>共 '.$max_page.' 页</span></li>';
        echo '</ul></div>';

        if (_mtx('ajaxpager')) {
            echo '<div class="pagination-bottom">
        <div class="pagination-loading"><i class="fa fa-spinner fa-spin"></i> 数据载入中...</div>
        </div>';
        }
    }

    function p_link($i, $title = '')
    {
        if ($title == '') {
            $title = "第 {$i} 页";
        }
        echo "<li><a href='", esc_html(get_pagenum_link($i)), "'>{$i}</a></li>";
    }
}
function deel_strimwidth($str, $start, $width, $trimmarker)
{
    $output = preg_replace('/^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $start . '}((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $width . '}).*/s', '\1', $str);
    return $output . $trimmarker;
}

if (!function_exists('deel_views')) {
    function deel_record_visitors()
    {
        if (is_singular()) {
            global $post;
            $post_ID = $post->ID;
            if ($post_ID) {
                $post_views = (int) get_post_meta($post_ID, 'views', true);
                if (!update_post_meta($post_ID, 'views', $post_views + 1)) {
                    add_post_meta($post_ID, 'views', 1, true);
                }
            }
        }
    }

    add_action('wp_head', 'deel_record_visitors');
    function deel_views($after = '')
    {
        global $post;
        $post_ID = $post->ID;
        $views = (int) get_post_meta($post_ID, 'views', true);
        return $views . $after;
    }
}


//页面伪静态
if (_mtx('pagehtml_enable')) {
    function html_page_permalink()
    {
        global $wp_rewrite;
        if (!strpos($wp_rewrite->get_page_permastruct(), '.html')) {
            $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
        }
    }

    add_action('init', 'html_page_permalink', -1);
    function git_search_url_rewrite()
    {
        if (is_search() && !empty($_GET['s']) && !is_admin()) {
            wp_redirect(home_url("/search/") . urlencode(get_query_var('s')));
            exit();
        }
    }
    add_action('template_redirect', 'git_search_url_rewrite');
}


/*
 * 友情链接
 */
/* function get_the_link_items($id = null)
{
    $bookmarks = get_bookmarks('orderby=date&category=' . $id);
    $output = '';
    if (!empty($bookmarks)) {
        $output .= '<ul class="link-items fontSmooth">';
        foreach ($bookmarks as $bookmark) {
            if (empty($bookmark->link_description)) {
                $bookmark->link_description = __('This guy is so lazy ╮(╯▽╰)╭', 'sakura');
            }

            if (empty($bookmark->link_image)) {
                $bookmark->link_image = 'https://view.moezx.cc/images/2017/12/30/Transparent_Akkarin.th.jpg';
            }

            $output .= '<li class="link-item"><a class="link-item-inner effect-apollo" href="' . $bookmark->link_url . '" title="' . $bookmark->link_description . '" target="_blank" rel="friend"><img class="lazyload" onerror="imgError(this,1)" data-src="' . $bookmark->link_image . '" src="https://cdn.jsdelivr.net/gh/moezx/cdn@3.0.2/img/svg/loader/trans.ajax-spinner-preloader.svg"><span class="sitename">' . $bookmark->link_name . '</span><div class="linkdes">' . $bookmark->link_description . '</div></a></li>';
        }
        $output .= '</ul>';
    }
    return $output;
}

function get_link_items()
{
    $linkcats = get_terms('link_category');
    if (!empty($linkcats)) {
        foreach ($linkcats as $linkcat) {
            $result .= '<h3 class="link-title"><span class="link-fix">' . $linkcat->name . '</span></h3>';
            if ($linkcat->description) {
                $result .= '<div class="link-description">' . $linkcat->description . '</div>';
            }

            $result .= get_the_link_items($linkcat->term_id);
        }
    } else {
        $result = get_the_link_items();
    }
    return $result;
} */


//baidu分享
$dHasShare = false;
function deel_share()
{
    if (!_mtx('bdshare')) return false;
    echo '<div class="bdsharebuttonbox">
    <span>分享到：</span>
    <a class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
    <a class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
    <a class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
    <a class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
    <a class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a>
    <a class="bds_bdhome" data-cmd="bdhome" title="分享到百度新首页"></a>
    <a class="bds_tqf" data-cmd="tqf" title="分享到腾讯朋友"></a>
    <a class="bds_youdao" data-cmd="youdao" title="分享到有道云笔记"></a>
    <a class="bds_more" data-cmd="more">更多</a> <span>(</span><a class="bds_count" data-cmd="count" title="累计分享0次">0</a><span>)</span>
    </div>';
    /*  echo '<span class="action action-share bdsharebuttonbox"><i class="fa fa-share-alt"></i>点击分享<div class="action-popover"><div class="popover top in"><div class="arrow"></div><div class="popover-content"><a href="#" class="sinaweibo fa fa-weibo" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_qzone fa fa-star" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="qq fa fa-qq" data-cmd="sqq" title="分享到QQ好友"></a><a href="#" class="bds_renren fa fa-renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin fa fa-weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_more fa fa-ellipsis-h" data-cmd="more"></a></div></div></div></span>';
    */
    global $dHasShare;
    $dHasShare = true;
}

//搜索表单
function git_searchform()
{
?>
    <form method="get" class="searchform themeform" action="<?php echo get_option('home'); ?>">
        <input type="text" class="search" placeholder="<?php echo _mtx('search_placeholder'); ?>" name="s" x-webkit-speech />
        <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
    </form>
    </div>
    </div>
    <?php
}

//最新发布加new 单位'小时'
function deel_post_new($timer = '48')
{
    $t = (strtotime(date("Y-m-d H:i:s")) - strtotime($post->post_date)) / 3600;
    if ($t < $timer) echo "<i>new</i>";
}

//修改评论表情调用路径
function deel_smilies_src($img_src, $img, $siteurl)
{
    return MTX_URL . '/assets/img/smilies/' . $img;
}

add_filter('smilies_src', 'deel_smilies_src', 1, 10);

//评论框自动勾选
function deel_add_checkbox()
{
    echo '<label for="comment_mail_notify" class="checkbox inline" style="padding-top:0;"><input name="comment_mail_notify" id="comment_mail_notify" value="comment_mail_notify" checked="checked" type="checkbox">评论通知</label>';
}
add_action('comment_form', 'deel_add_checkbox');

//时间显示方式‘xx以前’
function time_ago($type = 'commennt', $day = 7)
{
    $d = $type == 'post' ? 'get_post_time' : 'get_comment_time';
    if (time() - $d('U') > 60 * 60 * 24 * $day) return;
    echo ' (', human_time_diff($d('U'), strtotime(current_time('mysql', 0))), '前)';
}

function timeago($ptime)
{
    $ptime = strtotime($ptime);
    $etime = time() - $ptime;
    if ($etime < 1) return '刚刚';
    $interval = array(
        12 * 30 * 24 * 60 * 60 => '年前 (' . date('Y-m-d', $ptime) . ')',
        30 * 24 * 60 * 60 => '个月前 (' . date('m-d', $ptime) . ')',
        7 * 24 * 60 * 60 => '周前 (' . date('m-d', $ptime) . ')',
        24 * 60 * 60 => '天前',
        60 * 60 => '小时前',
        60 => '分钟前',
        1 => '秒前'
    );
    foreach ($interval as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}

//评论样式
function mtx_comment_list($comment, $args, $depth)
{
    echo '<li ';
    comment_class();
    echo ' id="comment-' . get_comment_ID() . '">';
    //头像
    echo '<div class="c-avatar">';
    echo str_replace(' src=', ' data-original=', get_avatar($comment->comment_author_email, $size = '54', deel_avatar_default()));
    //内容
    echo '<div class="c-main" id="div-comment-' . get_comment_ID() . '">';
    //表情,直接给懒加载?
    echo str_replace(' src=', ' data-original=', convert_smilies(get_comment_text()));
    if ($comment->comment_approved == '0') {
        echo '<span class="c-approved">您的评论正在排队审核中，请稍后！</span><br />';
    }
    echo '<div class="c-meta">';
    if ($comment->user_id !== '0') {
        echo '<span class="c-author"><a target="_blank" href="' . get_author_posts_url($comment->user_id) . '">' . get_comment_author() . '</a>';
    } else {
        echo '<span class="c-author">' . get_comment_author_link() . '';
    }
    /* 鸡肋 */
    if ($comment->user_id == '1') {
        echo '<a title="博主认证" class="vip"></a>';
    } elseif (_mtx('git_vip')) {
        echo get_author_class($comment->comment_author_email, $comment->user_id);
    }

    echo '</span>';

    echo get_comment_time('Y-m-d H:i ');
    echo time_ago() . ' ';
    if ($comment->comment_approved !== '0') {
        echo comment_reply_link(array_merge($args, array(
            'add_below' => 'div-comment',
            'depth' => $depth,
            'max_depth' => $args['max_depth'],
            'login_text' => '<a target="_blank" class="signin-loader fancy-sign" href="#sign">登录以回复</a>'
        )));
        echo edit_comment_link('(编辑)', ' - ', '');
        if (_mtx('comment_ua')) echo '<span style="color: #ff6600;"> ' . user_agent($comment->comment_agent) . '</span>';
    }
    echo '</div>';
    echo '</div></div>';
}

//点赞
add_action('wp_ajax_nopriv_bigfa_like', 'bigfa_like');
add_action('wp_ajax_bigfa_like', 'bigfa_like');
function bigfa_like()
{
    global $wpdb, $post;
    $id = filter_var($_POST["um_id"], FILTER_SANITIZE_NUMBER_INT);
    $action = $_POST["um_action"];
    if ($action == 'ding') {
        $bigfa_raters = get_post_meta($id, 'bigfa_ding', true);
        $expire = time() + 99999999;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost
        setcookie('bigfa_ding_' . $id, $id, $expire, '/', $domain, false);
        if (!$bigfa_raters || !is_numeric($bigfa_raters)) {
            update_post_meta($id, 'bigfa_ding', 1);
        } else {
            update_post_meta($id, 'bigfa_ding', ($bigfa_raters + 1));
        }
        echo get_post_meta($id, 'bigfa_ding', true);
    }
    die;
}


//付费可见
function pay_buy()
{
    if (isset($_POST['point']) && isset($_POST['userid']) && isset($_POST['id']) && $_POST['action'] == 'pay_buy') {
        Points::set_points(
            -$_POST['point'],
            $_POST['userid'],
            array(
                'description' => $_POST['id'],
                'status' => get_option('points-points_status', POINTS_STATUS_ACCEPTED)
            )
        ); //扣除金币
        $pay_content = get_post_meta($_POST['id'], 'pay_content', true);
        exit($pay_content);
    }
}

add_action('wp_ajax_pay_buy', 'pay_buy');
add_action('wp_ajax_nopriv_pay_buy', 'pay_buy');

/*免登陆购买开始*/

//获取加密内容
function getcontent()
{
    $id = $_POST["id"];
    $action = $_POST["action"];
    if (isset($id) && $_POST['action'] == 'getcontent') {
        $pay_content = get_post_meta($id, 'git_pay_content', true);
        exit($pay_content);
    }
}

add_action('wp_ajax_getcontent', 'getcontent');
add_action('wp_ajax_nopriv_getcontent', 'getcontent');

///提取码检测
function check_code()
{
    $id = $_POST['id'];
    $code = $_POST['code'];
    if (empty($code)) exit('0');
    if (isset($id) && $_POST['action'] == 'check_code') {
        $pay_log = get_post_meta($id, 'pay_log', true);
        //购买记录数据
        $pay_arr = explode(",", $pay_log);
        if (in_array($code, $pay_arr)) {
            exit('1');
        } else {
            exit('0');
        }
    }
}

add_action('wp_ajax_check_code', 'check_code');
add_action('wp_ajax_nopriv_check_code', 'check_code');

//在线充值
function payjs_view()
{
    $id = $_POST['id'];
    $money = $_POST['money'];
    $way = $_POST['way'];
    if (isset($id) && isset($money) && isset($way) && $_POST['action'] == 'payjs_view') {
        $config = [
            'mchid' => _mtx('git_payjs_id'),   // 配置商户号
            'key' => _mtx('git_payjs_secret'),   // 配置通信密钥
        ];
        // 初始化
        $payjs = new Payjs($config);
        $data = [
            'body' => '在线付费查看',   // 订单标题
            'attach' => 'P' . $id,
            'out_trade_no' => git_order_id(),       // 订单号
            'total_fee' => intval($money) * 100,             // 金额,单位:分
            'notify_url' => MTX_URL . '/modules/push.php',
            'hide' => '1'
        ];

        if ($way == 1) $data['type'] = 'alipay';
        $result_money = intval($money);
        $result_trade_no = $data['out_trade_no'];
        if (mtx_is_mobile()) {
            $rst = $payjs->cashier($data); //手机使用
            $result_img = $rst;
        } else {
            $rst = $payjs->native($data); //电脑使用
            $result_img = $rst['code_url'];
        }
        $result = $result_money . '|' . $result_img . '|' . $result_trade_no;
    }
    exit($result);
}

add_action('wp_ajax_payjs_view', 'payjs_view');
add_action('wp_ajax_nopriv_payjs_view', 'payjs_view');

function checkpayjs()
{
    $id = $_POST['id'];
    $orderid = $_POST['orderid'];
    if (isset($id) && isset($orderid) && $_POST['action'] == 'checkpayjs') {
        $sid = get_transient('P' . $id);
        if (strpos($sid, 'E20') !== false && $orderid == $sid) {
            exit('1'); //OK
        } else {
            exit('0'); //no
        }
    }
}

add_action('wp_ajax_checkpayjs', 'checkpayjs');
add_action('wp_ajax_nopriv_checkpayjs', 'checkpayjs');


function addcode()
{
    $id = $_POST['id'];
    $code = $_POST['code'];
    if (isset($id) && isset($code) && $_POST['action'] == 'addcode') {
        $pay_log = get_post_meta($id, 'pay_log', true); //购买记录数据
        if (empty($pay_log)) {
            add_post_meta($id, 'pay_log', $code, true);
        } else {
            update_post_meta($id, 'pay_log', $pay_log . ',' . $code);
        }
        $pay_log = get_post_meta($id, 'pay_log', true); //购买记录数据
        $pay_arr = explode(",", $pay_log);
        if (in_array($code, $pay_arr)) {
            exit('1'); //OK
        } else {
            exit('0'); //NO
        }
    }
}

add_action('wp_ajax_addcode', 'addcode');
add_action('wp_ajax_nopriv_addcode', 'addcode');

/*免登陆购买结束*/

//在线充值
function pay_chongzhi()
{
    if (isset($_POST['jine']) && $_POST['action'] == 'pay_chongzhi') {
        $config = [
            'mchid' => _mtx('git_payjs_id'),   // 配置商户号
            'key' => _mtx('git_payjs_secret'),   // 配置通信密钥
        ];
        // 初始化
        $payjs = new Payjs($config);
        $data = [
            'body' => '积分充值',   // 订单标题
            'attach' => get_current_user_id(),   // 订单备注
            'out_trade_no' => git_order_id(),       // 订单号
            'total_fee' => intval($_POST['jine']) * 100,             // 金额,单位:分
            'notify_url' => MTX_URL . '/modules/push.php',
            'hide' => '1'
        ];


        $result_money = intval($_POST['jine']);

        $result_trade_no = $data['out_trade_no'];

        if (_mtx('git_payjs_alipay') && $_POST['way'] == 'alipay') {
            $data['type'] = 'alipay';
            $result_way = '支付宝';
        } else {
            $result_way = '微信';
        }

        if (mtx_is_mobile()) {
            $rst = $payjs->cashier($data); //手机使用
            $result_img = $rst;
        } else {
            $rst = $payjs->native($data); //电脑使用
            $result_img = $rst['code_url'];
        }
        $result = $result_money . '|' . $result_way . '|' . $result_img . '|' . $result_trade_no;
        exit($result);
    }
}

add_action('wp_ajax_pay_chongzhi', 'pay_chongzhi');
add_action('wp_ajax_nopriv_pay_chongzhi', 'pay_chongzhi');

//检查付款情况
function payrest()
{
    if (isset($_POST['check_trade_no']) && $_POST['action'] == 'payrest') {
        if (git_check($_POST['check_trade_no'])) {
            exit('1');
        } else {
            exit('0');
        }
    }
}

add_action('wp_ajax_payrest', 'payrest');
add_action('wp_ajax_nopriv_payrest', 'payrest');

//临时禁止后台登录,因为验证码功能在前台
function prevent_wp_login()
{
    // WP tracks the current page - global the variable to access it
    global $pagenow;
    // Check if a $_GET['action'] is set, and if so, load it into $action variable
    $action = (isset($_GET['action'])) ? $_GET['action'] : '';
    // Check if we're on the login page, and ensure the action is not 'logout'
    if ($pagenow == 'wp-login.php' && (!$action || ($action && !in_array($action, array('logout', 'lostpassword', 'rp', 'resetpass'))))) {
        // Load the home page url
        $page = get_bloginfo('url');
        // Redirect to the home page
        wp_redirect($page);
        // Stop execution to prevent the page loading for any reason
        exit();
    }
}
add_action('init', 'prevent_wp_login');


//最热排行
function hot_posts_list()
{
    if (_mtx('hot_list_desc') == '2') {
        $result = get_posts(array(
            'post__in' => get_option('sticky_posts'),
            'order' => 'DESC',
            'orderby' => 'comment_count',
            'posts_per_page' => '10'
        ));
    } elseif (_mtx('hot_list_desc') == '1') {
        $result = get_posts("numberposts=5&orderby=comment_count&order=desc");
    }
    $output = '';
    if (empty($result)) {
        $output = '<li>暂无数据</li>';
    } else {
        $i = 1;
        foreach ($result as $topten) {
            $postid = $topten->ID;
            $title = $topten->post_title;
            $commentcount = $topten->comment_count;
            $output .= '<li><p><span class="post-comments">评论 (' . $commentcount . ')</span><span class="muted"><a href="javascript:;" data-action="ding" data-id="' . $postid . '" id="Addlike" class="action';
            if (isset($_COOKIE['bigfa_ding_' . $postid])) $output .= ' actived';
            $output .= '"><i class="fa fa-thumbs-o-up"></i><span class="count">';
            if (get_post_meta($postid, 'bigfa_ding', true)) {
                $output .= get_post_meta($postid, 'bigfa_ding', true);
            } else {
                $output .= '0';
            }
            $output .= '</span>赞</a></span></p><span class="label label-' . $i . '">' . $i . '</span><a href="' . get_permalink($postid) . '" title="' . $title . '">' . $title . '</a></li>';
            $i++;
        }
    }
    echo $output;
}

//在 WordPress 编辑器添加“下一页”按钮
function add_next_page_button($mce_buttons)
{
    $pos = array_search('wp_more', $mce_buttons, true);
    if ($pos !== false) {
        $tmp_buttons = array_slice($mce_buttons, 0, $pos + 1);
        $tmp_buttons[] = 'wp_page';
        $mce_buttons = array_merge($tmp_buttons, array_slice($mce_buttons, $pos + 1));
    }
    return $mce_buttons;
}

add_filter('mce_buttons', 'add_next_page_button');
//判断手机广告
function mtx_is_mobile()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    } elseif ((strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') === false) // many mobile devices (all iPh, etc.)
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'NetType/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'HUAWEI') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'TBS/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Mi') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false
    ) {
        return true;
    } else {
        return false;
    }
}

//搜索结果排除所有页面
function search_filter_page($query)
{
    if ($query->is_search && !$query->is_admin) {
        $query->set('post_type', 'post');
    }
    return $query;
}

add_filter('pre_get_posts', 'search_filter_page');

//输出缩略图地址
function post_thumbnail_src()
{
    global $post;
    if ($values = get_post_custom_values("git_thumb")) { //输出自定义域图片地址
        $values = get_post_custom_values("git_thumb");
        $post_thumbnail_src = $values[0];
    } elseif (has_post_thumbnail()) { //如果有特色缩略图，则输出缩略图地址
        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        $post_thumbnail_src = $thumbnail_src[0];
    } else {
        $post_thumbnail_src = '';
        ob_start();
        ob_end_clean();
        $output = preg_match_all('/<img.+?src=[\"\']((?:https?|www).*)[\"\'].*?>/i', $post->post_content, $matches);
        @$post_thumbnail_src = $matches[1][0]; //获取该图片 src
        if (empty($post_thumbnail_src)) { //如果日志中没有图片，则显示随机图片
            $random = mt_rand(1, 12);
            echo MTX_URL;
            echo '/assets/img/pic/' . $random . '.jpg';
        }
    };
    echo $post_thumbnail_src;
}

// 逻辑好多了
function post_thumbnail($addition, $random = false)
{
    global $post;

    $format = '<img class="thumb" %s="%s" alt="%s"  >';
    $src = _mtx('lazyload') ?  'data-original' : 'src';



    if ($values = get_post_custom_values("git_thumb")) { //输出自定义域图片地址
        $values = get_post_custom_values("git_thumb");
        $post_thumbnail_src = $values[0];
    } elseif (has_post_thumbnail()) { //如果有特色缩略图，则输出缩略图地址
        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        $post_thumbnail_src = $thumbnail_src[0];
    } else {
        $post_thumbnail_src = '';
        $output = preg_match_all('/<img.+src=[\'"](http[s]{0,1}:[^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        $post_thumbnail_src = $matches[1][0];
    };

    if (empty($post_thumbnail_src)) {
        if ($random) {
            $random = mt_rand(1, 12);
            $post_thumbnail_src = MTX_URL . '/assets/img/pic/' . $random . '.jpg';
        } else {

            return null;
        }
    }


    if ($addition) $post_thumbnail_src .= $addition;

    return sprintf($format, $src, $post_thumbnail_src, $post->post_title);
}



//cURL库 有几把用,
if (function_exists('curl_init')) {
    function curl_post($url, $postfields = '', $headers = '', $timeout = 20, $file = 0)
    {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false,
            CURLOPT_POST => true,
            CURLOPT_MAXREDIRS => 20,
            CURLOPT_USERAGENT => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36',
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        );
        if (is_array($postfields) && $file == 0) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($postfields);
        } else {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        }
        curl_setopt_array($ch, $options);
        if (is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $result = curl_exec($ch);
        $code = curl_errno($ch);
        $msg = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return array(
            'data' => $result,
            'code' => $code,
            'msg' => $msg,
            'info' => $info
        );
    }
}
//添加文章版权信息
function git_copyright($content)
{
    if ((is_single() || is_feed()) && _mtx('copyright_info')) {
        $copyright = str_replace(array(
            '{{title}}',
            '{{link}}'
        ), array(
            get_the_title(),
            get_permalink()
        ), stripslashes(_mtx('copyright_info')));
        $content .= '<hr /><div class="open-message">' . $copyright . '</div>';
    }
    return $content;
}

add_filter('the_content', 'git_copyright', 20);
//fancybox图片灯箱效果
function fancybox($content)
{
    $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>(.*?)<\\/a>/i";
    $replacement = '<a$1href=$2$3.$4$5 rel="box" class="fancybox"$6>$7</a>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
add_filter('the_content', 'fancybox');

//输出WordPress表情
function fa_get_wpsmiliestrans()
{
    global $wpsmiliestrans;
    $wpsmilies = array_unique($wpsmiliestrans);
    $output = '';
    foreach ($wpsmilies as $alt => $src_path) {
        $output .= '<a class="add-smily" data-smilies="' . $alt . '"><img class="wp-smiley" style="height:24px;width:24px;" src="' . MTX_URL . '/assets/img/smilies/' . rtrim($src_path, "gif") . 'gif" /></a>';
    }
    return $output;
}
add_action('media_buttons_context', 'fa_smilies_custom_button');

function fa_smilies_custom_button($context)
{
    $context = '';
    $context .= '<style>.smilies-wrap{background:#fff;border: 1px solid #ccc;box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.24);padding: 10px;position: absolute;top: 60px;width: 375px;display:none}.smilies-wrap img{height:24px;width:24px;cursor:pointer;margin-bottom:5px} .is-active.smilies-wrap{display:block}</style> <a id="insert-media-button" style="position:relative" class="button insert-smilies add_smilies" title="添加表情" data-editor="content" href="javascript:;">^_^ 添加表情</a><div class="smilies-wrap">' . fa_get_wpsmiliestrans() . '</div><script>jQuery(document).ready(function(){jQuery(document).on("click", ".insert-smilies",function() { if(jQuery(".smilies-wrap").hasClass("is-active")){jQuery(".smilies-wrap").removeClass("is-active");}else{jQuery(".smilies-wrap").addClass("is-active");}});jQuery(document).on("click", ".add-smily",function() { send_to_editor(" " + jQuery(this).data("smilies") + " ");jQuery(".smilies-wrap").removeClass("is-active");return false;});});</script>';
    return $context;
}



//有几把用的功能
//首页隐藏一些分类
function exclude_category_home($query)
{
    if ($query->is_home) {
        $query->set('cat', _mtx('git_blockcat')); //隐藏这两个分类
    }
    return $query;
}

add_filter('pre_get_posts', 'exclude_category_home');
function git_exclude_category_search($query)
{
    if (!$query->is_admin && $query->is_search) {
        $query->set('cat', _mtx('git_blockcat_search')); //隐藏这两个分类
    }
    return $query;
}

add_filter('pre_get_posts', 'git_exclude_category_search');
function git_exclude_category_rss($query)
{
    if ($query->is_feed) {
        $query->set('cat', _mtx('git_blockcat_rss')); //隐藏这两个分类
    }
    return $query;
}

add_filter('pre_get_posts', 'git_exclude_category_rss');




//获取所有站点分类id
function Bing_category()
{
    $cat_ids = get_transient('Bing_category');
    if (false === $cat_ids) {
        $categories = get_terms('category', 'hide_empty=0');
        $k = [];
        foreach ($categories as $categorie) {
            $k[] = $categorie->term_id;
        }
        $cat_ids = implode(",", $k);
        set_transient('Bing_category', $cat_ids, 60 * 60 * 24 * 5); //缓存5天
    }
    $cat_ids = explode(",", $cat_ids);
    foreach ($cat_ids as $catid) {
        $cat_name = get_cat_name($catid);
        $output = '<span>' . $cat_name . "=(<b>" . $catid . '</b>)</span>&nbsp;&nbsp;';
        echo $output;
    }
}


//主题自动更新服务
/* if (!_mtx('git_updates_b')) {
    include 'modules/updates.php';
    new ThemeUpdateChecker('MTX-Remastered', 'https://u.gitcafe.net/api/info.json');
} */
// 本来就会被过滤好吗
//评论拒绝HTML代码
/* if (_mtx('git_html_comment')) {
    function git_comment_post($incoming_comment)
    {
        $incoming_comment['comment_content'] = htmlspecialchars($incoming_comment['comment_content']);
        $incoming_comment['comment_content'] = str_replace("'", '&apos;', $incoming_comment['comment_content']);
        return $incoming_comment;
    }

    function git_comment_display($comment_to_display)
    {
        $comment_to_display = str_replace('&apos;', "'", $comment_to_display);
        return $comment_to_display;
    }

    add_filter('preprocess_comment', 'git_comment_post', '', 1);
    add_filter('comment_text', 'git_comment_display', '', 1);
    add_filter('comment_text_rss', 'git_comment_display', '', 1);
    add_filter('comment_excerpt', 'git_comment_display', '', 1);
}
 */

//中文文件重命名
function mtx_upload_filter($file)
{
    $time = date("YmdHis");
    $file['name'] = $time . "" . mt_rand(1, 100) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
    return $file;
}

add_filter('wp_handle_upload_prefilter', 'mtx_upload_filter');


//UA信息
if (_mtx('comment_ua')) {
    function user_agent($ua)
    {
        //开始解析操作系统
        $os = null;
        if (preg_match('/Windows NT 6.0/i', $ua)) {
            $os = 'Windows Vista';
        } elseif (preg_match('/Windows NT 6.1/i', $ua)) {
            $os = 'Windows 7';
        } elseif (preg_match('/Windows NT 6.2/i', $ua)) {
            $os = 'Windows 8';
        } elseif (preg_match('/Windows NT 6.3/i', $ua)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 10.0/i', $ua)) {
            $os = 'Windows 10';
        } elseif (preg_match('/Windows NT 5.1/i', $ua)) {
            $os = 'Windows XP';
        } elseif (preg_match('/Mac OS X/i', $ua)) {
            $os = 'Mac OS X';
        } elseif (preg_match('#Linux#i', $ua)) {
            $os = 'Linux ';
        } elseif (preg_match('#Windows Phone#i', $ua)) {
            $os = 'Windows Phone ';
        } elseif (preg_match('/Windows NT 5.2/i', $ua) && preg_match('/Win64/i', $ua)) {
            $os = 'Windows XP 64 bit';
        } elseif (preg_match('/Android ([0-9.]+)/i', $ua, $matches)) {
            $os = 'Android ' . $matches[1];
        } elseif (preg_match('/iPhone OS ([_0-9]+)/i', $ua, $matches)) {
            $os = 'iPhone ' . $matches[1];
        } else {
            $os = '未知操作系统';
        }
        if (preg_match('#(Camino|Chimera)[ /]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Camino ' . $matches[2];
        } elseif (preg_match('#SE 2([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = '搜狗浏览器 2' . $matches[1];
        } elseif (preg_match('#360([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = '360浏览器 ' . $matches[1];
        } elseif (preg_match('#Maxthon( |\\/)([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Maxthon 浏览器' . $matches[2];
        } elseif (preg_match('#Chrome/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Chrome ' . $matches[1];
        } elseif (preg_match('#XiaoMi/MiuiBrowser/([0-9.]+)#i', $ua, $matches)) {
            $browser = '小米浏览器 ' . $matches[1];
        } elseif (preg_match('#Safari/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Safari浏览器 ' . $matches[1];
        } elseif (preg_match('#opera mini#i', $ua)) {
            preg_match('#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches);
            $browser = 'Opera Mini ' . $matches[1];
        } elseif (preg_match('#Opera.([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Opera ' . $matches[1];
        } elseif (preg_match('#TencentTraveler ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = '腾讯TT浏览器 ' . $matches[1];
        } elseif (preg_match('#(UCWEB|UBrowser|UCBrowser)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'UC浏览器 ' . $matches[1];
        } elseif (preg_match('#(QQ|TIM)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = '手机QQ ' . $matches[1];
        } elseif (preg_match('#Vivaldi/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Vivaldi浏览器 ' . $matches[1];
        } elseif (preg_match('#wp-(iphone|android)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'WordPress客户端 ' . $matches[1];
        } elseif (preg_match('#MicroMessenger/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = '微信浏览器 ' . $matches[1];
        } elseif (preg_match('#Edge ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = '微软Edge浏览器 ' . $matches[1];
        } elseif (preg_match('#MSIE ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Internet Explorer ' . $matches[1];
        } elseif (preg_match('#(Firefox|Phoenix|SeaMonkey|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Firefox浏览器 ' . $matches[2];
        } else {
            $browser = '未知浏览器';
        }
        return $os . "  |  " . $browser;
    }
}

//压缩html代码,没卵用,插件就可以做到
if (_mtx('html_compress')) {
    function wp_compress_html()
    {
        function wp_compress_html_main($buffer)
        {
            if (substr(ltrim($buffer), 0, 5) == '<?xml') return $buffer;
            $initial = strlen($buffer);
            $buffer = explode("<!--wp-compress-html-->", $buffer);
            $count = count($buffer);
            for ($i = 0; $i <= $count; $i++) {
                if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
                    $buffer[$i] = str_replace("<!--wp-compress-html no compression-->", " ", $buffer[$i]);
                } else {
                    $buffer[$i] = str_replace("\t", " ", $buffer[$i]);
                    $buffer[$i] = str_replace("\n\n", "\n", $buffer[$i]);
                    $buffer[$i] = str_replace("\n", "", $buffer[$i]);
                    $buffer[$i] = str_replace("\r", "", $buffer[$i]);
                    while (stristr($buffer[$i], '  ')) {
                        $buffer[$i] = str_replace("  ", " ", $buffer[$i]);
                    }
                }
                $buffer_out .= $buffer[$i];
            }
            $final = strlen($buffer_out);
            if ($initial !== 0) {
                $savings = ($initial - $final) / $initial * 100;
            } else {
                $savings = 0;
            }
            $savings = round($savings, 2);
            $buffer_out .= "\n<!--压缩前的大小: {$initial} bytes; 压缩后的大小: {$final} bytes; 节约：{$savings}% -->";
            return $buffer_out;
        }

        ob_start("wp_compress_html_main");
    }

    add_action('get_header', 'wp_compress_html');
    function git_unCompress($content)
    {
        if (preg_match_all('/(crayon-|<?xml|script|textarea|<\\/pre>)/i', $content, $matches)) {
            $content = '<!--wp-compress-html--><!--wp-compress-html no compression-->' . $content;
            $content .= '<!--wp-compress-html no compression--><!--wp-compress-html-->';
        }
        return $content;
    }

    add_filter('the_content', 'git_unCompress');
}

//增强编辑器开始
function git_editor_buttons($buttons)
{
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'backcolor';
    return $buttons;
}
add_filter('mce_buttons_3', 'git_editor_buttons');

//获取访客VIP样式
if (_mtx('git_vip')) :
    function get_author_class($comment_author_email, $user_id)
    {
        $author_count = get_transient('author_count');
        if (false === $author_count) {
            global $wpdb;
            $author_count = count($wpdb->get_results("SELECT comment_ID as author_count FROM $wpdb->comments WHERE comment_author_email = '$comment_author_email' "));
            set_transient('author_count', $author_count, 60 * 60 * 2); //缓存2小时
        }
        if ($author_count >= 1 && $author_count < _mtx('git_vip1')) echo '<a class="vip1" title="评论达人 LV.1"></a>';
        else if ($author_count >= _mtx('git_vip1') && $author_count < _mtx('git_vip2')) echo '<a class="vip2" title="评论达人 LV.2"></a>';
        else if ($author_count >= _mtx('git_vip2') && $author_count < _mtx('git_vip3')) echo '<a class="vip3" title="评论达人 LV.3"></a>';
        else if ($author_count >= _mtx('git_vip3') && $author_count < _mtx('git_vip4')) echo '<a class="vip4" title="评论达人 LV.4"></a>';
        else if ($author_count >= _mtx('git_vip4') && $author_count < _mtx('git_vip5')) echo '<a class="vip5" title="评论达人 LV.5"></a>';
        else if ($author_count >= _mtx('git_vip5') && $author_count < _mtx('git_vip6')) echo '<a class="vip6" title="评论达人 LV.6"></a>';
        else if ($author_count >= _mtx('git_vip6')) echo '<a class="vip7" title="评论达人 LV.7"></a>';
    }
endif;



// 评论添加@，来自：http://www.ludou.org/wordpress-comment-reply-add-at.html
function git_comment_add_at($comment_text, $comment = '')
{
    if ($comment->comment_parent > 0) {
        $comment_text = '<a href="#comment-' . $comment->comment_parent . '">@' . get_comment_author($comment->comment_parent) . '</a> ' . $comment_text;
    }
    return $comment_text;
}
add_filter('get_comment_text', 'git_comment_add_at', 20, 2);


//导航单页函数
function get_the_link_items($id = null)
{
    $bookmarks = get_bookmarks('orderby=date&category=' . $id);
    $output = '';
    if (!empty($bookmarks)) {
        $output .= '<div class="link_items fontSmooth">';
        foreach ($bookmarks as $bookmark) {
            $output .= '<div class="link_item"><a class="link_item_inner apollo_' . $bookmark->link_rating . '" rel="nofollow" href="' . $bookmark->link_url . '" title="' . $bookmark->link_description . '" target="_blank" ><span class="sitename sitecolor_' . mt_rand(1, 14) . '">' . $bookmark->link_name . '</span></a></div>';
        }
        $output .= '</div>';
    }
    return $output;
}

function get_link_items()
{
    $linkcats = get_terms('link_category', 'orderby=count&hide_empty=1&exclude=' . _mtx('git_linkpage_cat'));
    if (!empty($linkcats)) {
        foreach ($linkcats as $linkcat) {
            $result .= '<h2 class="link_title">' . $linkcat->name . '</h2>';
            if ($linkcat->description) $result .= '<div class="link_description">' . $linkcat->description . '</div>';
            $result .= get_the_link_items($linkcat->term_id);
        }
    } else {
        $result = get_the_link_items();
    }
    return $result;
}




//获取页面id，并且不可重用
function git_page_id($pagephp)
{
    global $wpdb;
    $pagephp = esc_sql($pagephp);
    $pageid = $wpdb->get_row("SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_value` = 'pages/{$pagephp}.php'", ARRAY_A)['post_id'];
    return $pageid;
}

//根据订单描述金币数据，d=订单号 u=用户id
function git_check($d, $u = null)
{
    global $wpdb;
    $des = " WHERE `description` = '" . $d . "'";
    $userid = "";
    if (isset($u) && ($u !== null)) {
        $userid = " AND `user_id` = '" . $u . "'";
    }
    $result = $wpdb->query("SELECT `point_id` FROM " . Points_Database::points_get_table("users") . $des . $userid . " AND `status` = 'accepted' LIMIT 3", ARRAY_A);
    return $result; //0=无订单结果，1=有订单结果，>1均为异常数据
}

// 内链图片src
function link_the_thumbnail_src()
{
    global $post;
    ob_start();
    ob_end_clean();
    $content = $post->post_content;
    preg_match('/src="(.*?)"/i', $content, $matches, PREG_OFFSET_CAPTURE, 0);
    $post_thumbnail_src = $matches[1][0];
    if (empty($post_thumbnail_src)) {
        $post_thumbnail_src = MTX_URL . '/assets/img/pic/' . mt_rand(1, 12) . '.jpg';
    }
    return $post_thumbnail_src;
}

//文章目录,来自露兜,云落修改
if (_mtx('git_article_list')) {
    function article_index($content)
    {
        $matches = array();
        $ul_li = '';
        $r = "/<h2>([^<]+)<\/h2>/im";
        if (is_single() && preg_match_all($r, $content, $matches)) {
            foreach ($matches[1] as $num => $title) {
                $title = trim(strip_tags($title));
                $content = str_replace($matches[0][$num], '<h2 id="title-' . $num . '">' . $title . '</h2>', $content);
                $ul_li .= '<li><a href="#title-' . $num . '">' . $title . "</a></li>\n";
            }
            $content = '<div id="article-index">
                            <strong>文章目录<a class="hidetoc">[隐藏]</a></strong>
                            <ul id="index-ul">' . $ul_li . '</ul>
                        </div>' . $content;
        }
        return $content;
    }

    add_filter('the_content', 'article_index');
}

//评论地址更换
function git_comment_author($query_vars)
{
    if (array_key_exists('author_name', $query_vars)) {
        global $wpdb;
        $author_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='first_name' AND meta_value = %s", $query_vars['author_name']));
        if ($author_id) {
            $query_vars['author'] = $author_id;
            unset($query_vars['author_name']);
        }
    }
    return $query_vars;
}
add_filter('request', 'git_comment_author');


function git_comment_author_link($link, $author_id, $author_nicename)
{
    $my_name = get_user_meta($author_id, 'first_name', true);
    if ($my_name) {
        $link = str_replace($author_nicename, $my_name, $link);
    }
    return $link;
}
add_filter('author_link', 'git_comment_author_link', 10, 3);


//生成订单号编码
function git_order_id()
{
    date_default_timezone_set('Asia/Shanghai');
    $order_id = 'E' . date("YmdHis") . mt_rand(10000, 99999);
    return $order_id;
}

//默认头像
function deel_avatar_default()
{
    return MTX_URL . '/assets/img/default.png';
}

//懒加载
if (_mtx('lazyload')) {
    function lazyload($content)
    {
        if (!is_feed() || !is_robots()) {
            $content = preg_replace('/<img(.+)src=[\'"]([^\'"]+)[\'"](.*)>/i', "<img\$1data-original=\"\$2\" \$3>\n<noscript>\$0</noscript>", $content);
        }
        return $content;
    }
    add_filter('the_content', 'lazyload');
}

//只搜索文章标题
function git_search_by_title($search, $wp_query)
{
    if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
        global $wpdb;
        $q = $wp_query->query_vars;
        $n = !empty($q['exact']) ? '' : '%';
        $search = array();
        foreach ((array) $q['search_terms'] as $term) {
            $search[] = $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", $n . $wpdb->esc_like($term) . $n);
        }
        if (!is_user_logged_in()) {
            $search[] = "{$wpdb->posts}.post_password = ''";
        }
        $search = ' AND ' . implode(' AND ', $search);
    }
    return $search;
}

add_filter('posts_search', 'git_search_by_title', 10, 2);

//HTML5 桌面通知
function Notification_js()
{

    if (!_mtx('desktop_notification_enable')) {

        return;
    }
    if (_mtx('desktop_notification_days') && _mtx('desktop_notification_title') && _mtx('desktop_notification_body') && _mtx('desktop_notification_icon') && _mtx('desktop_notification_cookie')) {
    ?>
        <script type="text/javascript">
            if (window.Notification) {
                function setCookie(name, value) {
                    var exp = new Date();
                    exp.setTime(exp.getTime() + <?php echo _mtx('desktop_notification_days'); ?> * 24 * 60 * 60 * 1000);
                    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
                }

                function getCookie(name) {
                    var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
                    if (arr != null) return unescape(arr[2]);
                    return null
                }

                var popNotice = function() {
                    if (Notification.permission == "granted") {
                        setTimeout(function() {
                            var n = new Notification("<?php
                                                        echo _mtx('desktop_notification_title'); ?>", {
                                body: "<?php
                                        echo _mtx('desktop_notification_body'); ?>",
                                icon: "<?php
                                        echo _mtx('desktop_notification_icon'); ?>"
                            });
                            n.onclick = function() {
                                window.location.href = "<?php
                                                        echo _mtx('desktop_notification_link'); ?>";
                                n.close()
                            };
                            n.onclose = function() {
                                setCookie("MTX_Notification", "<?php
                                                                echo _mtx('desktop_notification_cookie'); ?>")
                            }
                        }, 2 * 1000)
                    }
                };
                if (getCookie("MTX_Notification") == "<?php
                                                        echo _mtx('desktop_notification_cookie'); ?>") {
                    console.log("您已关闭桌面弹窗提醒，有效期为<?php
                                                echo _mtx('desktop_notification_days'); ?>天！")
                } else {
                    if (Notification.permission == "granted") {
                        popNotice()
                    } else if (Notification.permission != "denied") {
                        Notification.requestPermission(function(permission) {
                            popNotice()
                        })
                    }
                }
            } else {
                console.log("您的浏览器不支持Web Notification")
            }
        </script>
    <?php
    }
}

add_action('get_footer', 'Notification_js');

//标签增加另外选项
class Git_Tax_Image
{
    function __construct()
    {
        add_action('post_tag_add_form_fields', array($this, 'add_tax_image_field'));
        add_action('post_tag_edit_form_fields', array($this, 'edit_tax_image_field'));
        add_action('edited_post_tag', array($this, 'save_tax_meta'), 10, 2);
        add_action('create_post_tag', array($this, 'save_tax_meta'), 10, 2);
    } // __construct

    public function add_tax_image_field()
    {
    ?>
        <div class="form-field">
            <label for="term_meta[tax_image]">标签封面</label>
            <input type="text" name="term_meta[tax_image]" id="term_meta[tax_image]" value="" />
            <p class="description">输入标签封面图片URL</p>
        </div><!-- /.form-field -->
        <div class="form-field">
            <label for="term_meta[tax_title]">标签标题</label>
            <input type="text" name="term_meta[tax_title]" id="term_meta[tax_title]" value="" />
            <p class="description">输入标签标题</p>
        </div>

    <?php
    } // add_tax_image_field

    public function edit_tax_image_field($term)
    {
        $term_id = $term->term_id;
        $term_meta = get_option("ludou_taxonomy_$term_id");
        $image = $term_meta['tax_image'] ? $term_meta['tax_image'] : '';
        $keywords = $term_meta['tax_title'] ? $term_meta['tax_title'] : '';

    ?>
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_image]">标签封面</label>
            <td>
                <input type="text" name="term_meta[tax_image]" id="term_meta[tax_image]" value="<?php echo esc_url($image); ?>" />
                <p class="description">输入标签封面图片URL</p>
            </td>
            </th>
        </tr><!-- /.form-field -->
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_title]">标签标题</label>
            <td>
                <input type="text" name="term_meta[tax_title]" id="term_meta[tax_title]" value="<?php echo $keywords; ?>" />
                <p class="description">输入标签标题</p>
            </td>
            </th>
        </tr>
<?php
    } // edit_tax_image_field

    public function save_tax_meta($term_id)
    {

        if (isset($_POST['term_meta'])) {
            $t_id = $term_id;
            $term_meta = array();
            $term_meta['tax_image'] = isset($_POST['term_meta']['tax_image']) ? esc_url($_POST['term_meta']['tax_image']) : '';
            $term_meta['tax_title'] = isset($_POST['term_meta']['tax_title']) ? $_POST['term_meta']['tax_title'] : '';
            update_option("ludou_taxonomy_$t_id", $term_meta);
        } // if isset( $_POST['term_meta'] )
    } // save_tax_meta

} // Git_Tax_Image

$wptt_tax_image = new Git_Tax_Image();
//WordPress函数代码结束,打算在本文件添加代码的建议参照这个方法：http://gitcafe.net/archives/4032.html



if (_mtx('feed_disable')) {

    function disable_feed()
    {
        wp_die(__('Feed已经关闭, 请访问网站首页!'));
    }
    add_action('do_feed', 'disable_feed', 1);
    add_action('do_feed_rdf', 'disable_feed', 1);
    add_action('do_feed_rss', 'disable_feed', 1);
    add_action('do_feed_rss2', 'disable_feed', 1);
    add_action('do_feed_atom', 'disable_feed', 1);
}



function mtx_get_post_like($class = '', $pid = '', $text = '')
{
    $pid = $pid ? $pid : get_the_ID();
    $text = $text ? $text : '赞';
    $like = get_post_meta($pid, 'bigfa_ding', true);
    if (mtx_is_my_like($pid)) {
        $class .= ' actived';
    }
    return '<a href="javascript:;" id="Addlike" data-action="ding" class="' . $class . '" data-id="' . $pid . '"><i class="fa fa-thumbs-o-up"></i>' . $text . '(<span class="count">' . ($like ? $like : 0) . '</span>)</a>';
}

// 以后再支持记录用户的点赞,不过这种东西太好破解了
function mtx_is_my_like($pid = '')
{

    if (isset($_COOKIE['bigfa_ding_' . $pid])) return true;
    return false;
    /* 
    if( !is_user_logged_in() ) return false;
    $pid = $pid ? $pid : get_the_ID();
    $likes = get_user_meta( get_current_user_id(), 'like-posts', true );
    $likes = $likes ? unserialize($likes) : array();
    return in_array($pid, $likes) ? true : false; */
}

// 直接用SEO插件就完事了

function _get_delimiter()
{

    return _mtx('_get_delimiter') ? _mtx('_get_delimiter') : '-';
}
//以后再支持副标题把
function _title()
{
    global $new_title;
    if ($new_title) return $new_title;

    global $paged;

    $html = '';
    $t = trim(wp_title('', false));

    /*     if ((is_single() || is_page()) && get_the_subtitle(false)) {
        $t .= get_the_subtitle(false);
    } */

    if ($t) {
        $html .= $t . _get_delimiter();
    }

    $html .= get_bloginfo('name');

    if (is_home()) {

        if ($paged > 1) {
            $html .= _get_delimiter() . '最新发布';
        } else if (get_option('blogdescription')) {
            $html .= _get_delimiter() . get_option('blogdescription');
        }
    }

    if (is_category()) {
        /* 
        global $wp_query;
        $cat_ID = get_query_var('cat'); */
    }

    if ((is_single() || is_page())) {
        global $post;
        $post_ID = $post->ID;
        $seo_title = trim(get_post_meta($post_ID, 'title', true));
        if ($seo_title) $html = $seo_title;
    }

    if ($paged > 1) {
        $html .= _get_delimiter() . '第' . $paged . '页';
    }

    return $html;
}


?>