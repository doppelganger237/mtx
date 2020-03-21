<?php

/* 短代码集合 */



//添加钮百度云
function TanChuang($atts, $content = null)
{
    /*     extract(shortcode_atts(array(
        "href" => 'http://',
        "filename" => '',
        "password" => ''
    ), $atts));
 */
    ob_start();

    set_query_var('args', shortcode_atts(array(
        "a" => '百度云:www.baidu.com|狗蛋云:goudan.com',
        "filename" => '',
        "password" => '',
        "size" => ''
    ), $atts));
    get_template_part('/pages/tanchuang');

    return ob_get_clean();

    //return '<a class="dl" href="bdybox" target="_blank" rel="nofollow"><i class="fa fa-cloud-download"></i>' . $content . '<i class="fa fa-key" aria-hidden="true"></i> </a>';
}
add_shortcode("tcxz", "TanChuang");
//添加钮Download
function DownloadUrl($atts, $content = null)
{
    extract(shortcode_atts(array(
        "href" => 'http://'
    ), $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-cloud-download"></i>' . $content . '</a>';
}
add_shortcode("dl", "DownloadUrl");
//添加钮git
function GithubUrl($atts, $content = null)
{
    extract(shortcode_atts(array(
        "href" => 'http://'
    ), $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-github-alt"></i>' . $content . '</a>';
}
add_shortcode('gt', 'GithubUrl');
//添加钮Demo
function DemoUrl($atts, $content = null)
{
    extract(shortcode_atts(array(
        "href" => 'http://'
    ), $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-external-link"></i>' . $content . '</a>';
}
add_shortcode('dm', 'DemoUrl');
//使用短代码添加回复后可见内容开始
function reply_to_read($atts, $content = null)
{
    extract(shortcode_atts(array(
        "notice" => '<blockquote>
        <center>
        <p class="reply-to-read">
        注意：本段内容须成功“<a href="' . get_permalink() . '#respond" title="回复本文">回复本文</a>”后“<a href="javascript:window.location.reload();" title="刷新本页">刷新本页</a>”方可查看！
        </p>
        </center>
        </blockquote>'
    ), $atts));
    $email = null;

    $content = do_shortcode($content);
    $content = '<blockquote><p>' . $content . '</p></blockquote>';
    $user_ID = get_current_user_id();
    if ($user_ID > 0) {
        $email = get_user_by('id', $user_ID)->user_email;
        //对博主直接显示内容
        $admin_email = get_bloginfo('admin_email');
        if ($email == $admin_email) {
            return $content;
        }
    } else if (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
        $email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);
    } else {
        return $notice;
    }
    if (empty($email)) {
        return $notice;
    }
    global $wpdb;
    $post_id = get_the_ID();
    $query = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";
    if ($wpdb->get_results($query)) {
        return do_shortcode($content);
    } else {
        return $notice;
    }
}
add_shortcode('reply', 'reply_to_read');
/*绿色提醒框*/
function toz($atts, $content = null)
{
    return '<div id="sc_notice">' . $content . '</div>';
}
add_shortcode('v_notice', 'toz');
/*红色提醒框*/
function toa($atts, $content = null)
{
    return '<div id="sc_error">' . $content . '</div>';
}
add_shortcode('v_error', 'toa');
/*黄色提醒框*/
function toc($atts, $content = null)
{
    return '<div id="sc_warn">' . $content . '</div>';
}
add_shortcode('v_warn', 'toc');
/*灰色提醒框*/
function tob($atts, $content = null)
{
    return '<div id="sc_tips">' . $content . '</div>';
}
add_shortcode('v_tips', 'tob');
/*蓝色提醒框*/
function tod($atts, $content = null)
{
    return '<div id="sc_blue">' . $content . '</div>';
}
add_shortcode('v_blue', 'tod');
/*蓝边文本框*/
function toe($atts, $content = null)
{
    return '<div  class="sc_act">' . $content . '</div>';
}
add_shortcode('v_act', 'toe');
/*灵魂按钮*/
function tom($atts, $content = null)
{
    extract(shortcode_atts(array(
        "href" => 'http://'
    ), $atts));
    return '<a class="lhb" href="' . $href . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('lhb', 'tom');
/*添加视频按钮*/
function too($atts, $content = null)
{
    extract(shortcode_atts(array(
        "play" => '0'
    ), $atts));
    if ($play == 0) {
        return '<video style="width:100%;" src="' . $content . '" controls preload >您的浏览器不支持HTML5的 video 标签，无法为您播放！</video>';
    }
    if ($play == 1) {
        return '<video style="width:100%;" src="' . $content . '" controls preload autoplay >您的浏览器不支持HTML5的 video 标签，无法为您播放！</video>';
    }
}
add_shortcode('video', 'too');
/*添加音频按钮*/
function tkk($atts, $content = null)
{
    extract(shortcode_atts(array(
        "play" => '0'
    ), $atts));
    if ($play == 0) {
        return '<audio style="width:100%;" src="' . $content . '" controls loop>您的浏览器不支持 audio 标签。</audio>';
    }
    if ($play == 1) {
        return '<audio style="width:100%;" src="' . $content . '" controls autoplay loop>您的浏览器不支持 audio 标签。</audio>';
    }
}
add_shortcode('audio', 'tkk');
/*弹窗下载*/
function ton($atts, $content = null)
{
    extract(shortcode_atts(array(
        "href" => 'http://',
        "filename" => '',
        "filesize" => '',
        "filedown" => ''
    ), $atts));
    return '<a class="lhb" id="showdiv" href="#fancydlbox" >文件下载</a><div id="fancydlbox" style="cursor:default;display:none;width:800px;"><div class="part" style="padding:20px 0;"><h2>下载声明:</h2> <div class="fancydlads" align="left"><p>' . _mtx('git_fancydlcp') . '</p></div></div><div class="part" style="padding:20px 0;"><h2>文件信息：</h2> <div class="dlnotice" align="left"><p>文件名称：' . $filename . '<br />文件大小：' . $filesize . '<br />发布日期：' . get_the_modified_time('Y年n月j日') . '</p></div></div><div class="part" id="download_button_part"><a id="download_button" target="_blank" href="' . $href . '"><span></span>' . $filedown . '</a> </div><div class="part" style="padding:20px 0;"><div class="moredl" style="text-align:center;">[更多地址] : ' . $content . '</div></div><div class="dlfooter">' . _mtx('git_fancydlad') . '</div></div>';
}
add_shortcode('fanctdl', 'ton');

//下载单页短代码
function git_download($atts, $content = null)
{
    return '<a class="lhb" href="' . get_permalink(git_page_id('download')) . '?pid=' . get_the_ID() . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('download', 'git_download');
/* 短代码信息框 完毕*/

//简单的下载面板
function xdltable($atts, $content = null)
{
    extract(shortcode_atts(array(
        "file" => "",
        "size" => ""
    ), $atts));
    return '<table class="dltable"><tbody><tr><td style="background-color:#F9F9F9;" rowspan="3"><p>文件下载</p></td><td><i class="fa fa-list-alt"></i>&nbsp;&nbsp;文件名称：' . $file . '</td><td><i class="fa fa-th-large"></i>&nbsp;&nbsp;文件大小：' . $size . '</td></tr><tr><td colspan="2"><i class="fa fa-volume-up"></i>&nbsp;&nbsp;下载声明：' . _mtx('git_dltable_b') . '</td></tr><tr><td colspan="2"><i class="fa fa-download"></i>&nbsp;&nbsp;下载地址：' . $content . '</td></tr></tbody></table>';
}
add_shortcode('dltable', 'xdltable');
//网易云音乐
function music163($atts, $content = null)
{
    extract(shortcode_atts(array(
        "play" => "1"
    ), $atts));
    return '<iframe style="width:100%;max-height:86px;" frameborder="no" border="0" marginwidth="0" marginheight="0" src="http://music.163.com/outchain/player?type=2&id=' . $content . '&auto=' . $play . '&height=66"></iframe>';
}
add_shortcode('netmusic', 'music163');
//登录可见
function login_to_read($atts, $content = null)
{
    $logina = '<a target="_blank" href="' . esc_url(wp_login_url(get_permalink())) . '">登录</a>';
    extract(shortcode_atts(array(
        "notice" => '<blockquote>
        <center>
        <p class="reply-to-read" style="color: blue;">注意：本段内容须“' . $logina . '”后方可查看！</p>
        </center>
        </blockquote>'
    ), $atts));

    if (is_user_logged_in() && !is_null($content) && !is_feed()) {
        return '<div id="e-secret"><fieldset><legend>隐藏的内容</legend>
	' . do_shortcode($content) . '<div class="clear"></div></fieldset></div>';
    }
    return $notice;
}
add_shortcode('login', 'login_to_read');



//给文章加内链短代码,随机缩略图????
function insert_posts($atts, $content = null)
{
    extract(shortcode_atts(array(
        'ids' => ''
    ), $atts));
    global $post;
    $content = '';
    $postids = explode(',', $ids);
    $inset_posts = get_posts(array(
        'post__in' => $postids
    ));
    foreach ($inset_posts as $key => $post) {
        setup_postdata($post);
        $content .= '<div class="neilian"><div class="fll"><a target="_blank" href="' . get_permalink() . '" class="fll linkss"><i class="fa fa-link fa-fw"></i>  ';
        $content .= get_the_title();
        $content .= '</a><p class="note">';
        $content .= get_the_excerpt();
        $content .= '</p></div><div class="frr"><a target="_blank" href="' . get_permalink() . '"><img src=';
        $content .= link_the_thumbnail_src();
        $content .= ' class="neilian-thumb"></a></div></div>';
    }
    wp_reset_postdata();
    return $content;
}
add_shortcode('neilian', 'insert_posts');
//给文章加外链短代码,这玩意速度能用?
function git_external_posts($atts, $content = null)
{
    $result = curl_post($content)['data'];
    $title = preg_match('!<title>(.*?)</title>!i', $result, $matches) ? $matches[1] : '我是标题我是标题我是标题我是标题我是标题我是标题我是标题';
    $tags = get_meta_tags($content);
    $description = $tags['description'];
    $imgpath = MTX_URL . '/assets/img/pic/' . mt_rand(1, 12) . '.jpg';
    global $post;
    $contents = '';
    setup_postdata($post);
    $contents .= '<div class="neilian wailian"><div class="fll"><a target="_blank" href="' . $content . '" class="fll linkss"><i class="fa fa-link fa-fw"></i>  ';
    $contents .= $title;
    $contents .= '</a><p class="note">';
    $contents .= $description;
    $contents .= '</p></div><div class="frr"><a target="_blank" href="' . $content . '"><img src=';
    $contents .= $imgpath;
    $contents .= ' class="neilian-thumb"></a></div></div>';
    wp_reset_postdata();
    return $contents;
}
if (function_exists('curl_init')) {
    add_shortcode('wailian', 'git_external_posts');
}





function pay_nologin($atts, $content = '')
{
    extract(shortcode_atts(array('money' => "1"), $atts));
    $pid = get_the_ID(); //文章ID
    $pay_content = get_post_meta($pid, 'git_pay_content', true); //隐藏的内容
    $pay_log = get_post_meta($pid, 'pay_log', true); //购买记录数据
    $pay_arr = explode(",", $pay_log);
    $pay_count = count($pay_arr); //已购买人数
    $notice = '';
    $notice .= '<style type="text/css">.sbtn{border:0;border-radius:4px;cursor:pointer;display:inline-block;font-size:15px;font-weight:600;letter-spacing:1px;line-height:36px;outline:0;padding:0 18px;text-align:center;text-transform:uppercase;position:relative}.sbtn:hover{transition:all .3s ease-in-out}.sbtn--secondary{background-color:#1dc9b7;color:#fff}.sbtn--secondary:hover{background-color:#18a899}.content-hide-tips{padding:40px 20px 20px;border:1px dashed #ccc;margin:20px 0 40px;background-color:#f6f6f6;border-radius:4px;position:relative}.content-hide-tips .fa-lock{font-size:30px;right:10px;top:5px;font-style:normal;color:#ccc;position:absolute;z-index:1}.content-hide-tips .rate{left:10px;top:5px;position:absolute;z-index:1;font-weight:500;margin:10px}.content-hide-tips .login-false{text-align:center}.content-hide-tips .coin{display:block;text-align:center;margin-top:10px;margin-bottom:10px}.content-hide-tips .coin span{padding:4px 18px;background-color:#fff;color:#f0ad4e;line-height:1;border-radius:20px;font-size:13px;border:1px solid #f0ad4e}.content-hide-tips .t-c{text-align:center;font-size:13px}.content-hide-tips .red{color:#ff3b41}.pc-button{margin:0 auto;text-align:center}.label{display:inline;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em}.label:empty{display:none}.label-warning{background-color:#f0ad4e}.swal-button{line-height: normal;}.swal-footer{text-align:center;}</style>';
    wp_enqueue_script('qrious', 'https://cdn.bootcss.com/qrious/4.0.2/qrious.min.js', false, '1.0', true);
    wp_enqueue_script('pay', MTX_URL . '/dist/js/pay.js', array('jquery'), '1.0', true);
    wp_localize_script('pay', 'ajax', ['url' => admin_url('admin-ajax.php')]);
    wp_enqueue_script('sweetalert', 'https://cdn.bootcss.com/sweetalert/2.0.0/sweetalert.min.js', false, '1.0', true);
    $notice .= '<div id="hide_notice" class="content-hide-tips"><i class="fa fa-lock"></i><span class="rate label label-warning">付费查看内容</span>';
    $notice .= '<div class="login-false">当前隐藏内容需要支付<div class="coin"><span class="label label-warning">' . $money . '元</span></div></div>';
    $notice .= '<p class="t-c">已有<span class="red">' . $pay_count . '</span>人支付</p>';
    $notice .= '<div class="pc-button"><button id="pay_view" type="button" data-action="pay_view" data-money="' . $money . '" data-id="' . $pid . '" class="sbtn sbtn--secondary" onclick="pay_view();">立即查看</button>';
    $notice .= '</div></div>';
    return $notice;
}
add_shortcode('pax', 'pay_nologin');
