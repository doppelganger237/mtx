<?php

//新文章同步到新浪微博
function post_to_sina_weibo($post_ID)
{
    if (get_post_meta($post_ID, 'mtx_weibo_sync', true) == 1) return;
    $get_post_info = get_post($post_ID);
    $get_post_centent = get_post($post_ID)->post_content;
    $get_post_title = get_post($post_ID)->post_title;
    if ($get_post_info->post_status == 'publish' && $_POST['original_post_status'] != 'publish') {
        $appkey = _mtx('git_wbapky_b');
        $username = _mtx('git_wbuser_b');
        $userpassword = _mtx('git_wbpasd_b');
        $request = new WP_Http;
        $keywords = "";
        $tags = wp_get_post_tags($post_ID);
        foreach ($tags as $tag) {
            $keywords = $keywords . '#' . $tag->name . "#";
        }
        $string1 = '【' . strip_tags($get_post_title) . '】：';
        $string2 = $keywords . ' [阅读全文]：' . get_permalink($post_ID);
        /* 微博字数控制，避免超标同步失败 */
        $wb_num = (138 - WeiboLength($string1 . $string2)) * 2;
        $status = $string1 . mb_strimwidth(strip_tags(apply_filters('the_content', $get_post_centent)), 0, $wb_num, '...') . $string2;
        $api_url = 'https://api.weibo.com/2/statuses/update.json';
        $body = array(
            'status' => $status,
            'source' => $appkey
        );
        $headers = array(
            'Authorization' => 'Basic ' . base64_encode("$username:$userpassword")
        );
        $result = $request->post($api_url, array(
            'body' => $body,
            'headers' => $headers
        ));
        /* 若同步成功，则给新增自定义栏目git_weibo_sync，避免以后更新文章重复同步 */
        add_post_meta($post_ID, 'mtx_weibo_sync', 1, true);
    }
}
if (_mtx('git_sinasync_b')) {
    add_action('publish_post', 'post_to_sina_weibo', 0);
}


/*
//获取微博字符长度函数
*/
function WeiboLength($str)
{
    $arr = arr_split_zh($str); //先将字符串分割到数组中
    foreach ($arr as $v) {
        $temp = ord($v); //转换为ASCII码
        if ($temp > 0 && $temp < 127) {
            $len = $len + 0.5;
        } else {
            $len++;
        }
    }
    return ceil($len); //加一取整

}
/*
//拆分字符串函数,只支持 gb2312编码
//参考：http://u-czh.iteye.com/blog/1565858
*/
function arr_split_zh($tempaddtext)
{
    $tempaddtext = iconv("UTF-8", "GBK//IGNORE", $tempaddtext);
    $cind = 0;
    $arr_cont = array();
    for ($i = 0; $i < strlen($tempaddtext); $i++) {
        if (strlen(substr($tempaddtext, $cind, 1)) > 0) {
            if (ord(substr($tempaddtext, $cind, 1)) < 0xA1) { //如果为英文则取1个字节
                array_push($arr_cont, substr($tempaddtext, $cind, 1));
                $cind++;
            } else {
                array_push($arr_cont, substr($tempaddtext, $cind, 2));
                $cind += 2;
            }
        }
    }
    foreach ($arr_cont as &$row) {
        $row = iconv("gb2312", "UTF-8", $row);
    }
    return $arr_cont;
}


//百度收录提示
if (_mtx('git_baidurecord_b') && function_exists('curl_init')) {
    function baidu_check($url, $post_id)
    {
        $baidu_record = get_post_meta($post_id, 'baidu_record', true);
        if ($baidu_record != 1) {
            $url = 'http://www.baidu.com/s?wd=' . $url;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $rs = curl_exec($curl);
            curl_close($curl);
            if (!strpos($rs, '没有找到该URL，您可以直接访问') && !strpos($rs, '很抱歉，没有找到与')) {
                update_post_meta($post_id, 'baidu_record', 1) || add_post_meta($post_id, 'baidu_record', 1, true);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }
    function baidu_record()
    {
        global $wpdb;
        $post_id = null === $post_id ? get_the_ID() : $post_id;
        if (baidu_check(get_permalink($post_id), $post_id) == 1) {
            echo '<a target="_blank" title="点击查看" rel="external nofollow" href="http://www.baidu.com/s?wd=' . get_the_title() . '">已收录</a>';
        } else {
            echo '<a style="color:red;" rel="external nofollow" title="点击提交，谢谢您！" target="_blank" href="http://zhanzhang.baidu.com/sitesubmit/index?sitename=' . get_permalink() . '">未收录</a>';
        }
    }
}

//七牛CDN
if (!is_admin() && _mtx('cdn_enable')) {
    add_action('wp_loaded', 'Googlo_ob_start');
    function Googlo_ob_start()
    {
        ob_start('Googlo_qiniu_cdn_replace');
    }
    function Googlo_qiniu_cdn_replace($html)
    {
        $local_host = home_url(); //博客域名
        $qiniu_host = _mtx('cdn_url'); //七牛域名
        $cdn_exts = _mtx('cdn_url_format'); //扩展名（使用|分隔）
        $cdn_dirs = _mtx('cdn_url_dir'); //目录（使用|分隔）
        $cdn_dirs = str_replace('-', '\-', $cdn_dirs);
        if ($cdn_dirs) {
            $regex = '/' . str_replace('/', '\/', $local_host) . '\/((' . $cdn_dirs . ')\/[^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
            $html = preg_replace($regex, $qiniu_host . '/$1$4', $html);
        } else {
            $regex = '/' . str_replace('/', '\/', $local_host) . '\/([^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
            $html = preg_replace($regex, $qiniu_host . '/$1$3', $html);
        }
        return $html;
    }
}

function getCDNURI(){

    if(_mtx('cdn_enable')) 
    return str_replace(home_url(),_mtx('cdn_url'),get_stylesheet_directory_uri());

    return get_stylesheet_directory_uri();
}

//CDN水印
if (_mtx('cdn_water')) {
    function cdn_water($content)
    {
        if (get_post_type() == 'post') {
            $pattern = "/<img(.*?)src=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
            $replacement = '<img$1src=$2$3.$4!water.jpg$5$6>';
            $content = preg_replace($pattern, $replacement, $content);
        }
        return $content;
    }
    add_filter('the_content', 'cdn_water');
}

//自动替换媒体库图片的域名
if (is_admin() && _mtx('cdn_url') && _mtx('cdn_jingxiang')) {
    function attachment_replace($text)
    {
        $replace = array(
            home_url()  => _mtx('cdn_url')
        );
        $text = str_replace(array_keys($replace), $replace, $text);
        return $text;
    }
    add_filter('wp_get_attachment_url', 'attachment_replace');
}

//百度主动推送
if (_mtx('baidu_auto_submit_url')) {
    function Git_Baidu_Submit($post_ID)
    {
        $url = get_permalink($post_ID);
        $api = _mtx('baidu_auto_submit_url');
        $request = new WP_Http;
        if (get_post_meta($post_ID, 'git_baidu_submit', true) == 1) {
            return;
        }
        $result = $request->request($api, array(
            'method' => 'POST',
            'body' => $url,
            'headers' => 'Content-Type: text/plain'
        ));
        if (is_array($result) && !is_wp_error($result) && $result['response']['code'] == '200') {
            error_log('baidu_submit_result：' . $result['body']);
            $result = json_decode($result['body'], true);
        }
        if (array_key_exists('success', $result)) {
            add_post_meta($post_ID, 'git_baidu_submit', 1, true);
        }
    }

/*     function Git_Baidu_Submit_Update($post_ID){
        $url = get_permalink($post_ID);
        $api = _mtx('git_sitemap_api');
        $request = new WP_Http;

        $result = $request->request(str_replace('urls','update',$api), array(
            'method' => 'POST',
            'body' => $url,
            'headers' => 'Content-Type: text/plain'
        ));
        if (is_array($result) && !is_wp_error($result) && $result['response']['code'] == '200') {
            error_log('baidu_submit_result：' . $result['body']);
            $result = json_decode($result['body'], true);
        }

    } */
/* 
    add_action( 'post_updated', 'Git_Baidu_Submit_Update', 0, 1); */
    add_action('publish_post', 'Git_Baidu_Submit', 0);
}



/* 
//在登录框添加额外的微信登录
if (_mtx('weauth_oauth')) {
    function weixin_login_button()
    {
        echo '<p><a class="button button-large" href="' . get_permalink(git_page_id('weauth')) . '">微信登录</a></p><br>';
    }
    add_action('login_form', 'weixin_login_button');

    function weixin_login_middle()
    {
        return '<p><a class="lhb" href="' . get_permalink(git_page_id('weauth')) . '">微信登录</a></p><br>';
    };
    add_filter('login_form_middle', 'weixin_login_middle', 10, 1);
}
 */
//评论微信推送
if (_mtx('git_Server') && !is_admin()) {
    function sc_send($comment_id)
    {
        $text = '网站上有新的评论，请及时查看'; //微信推送信息标题
        $comment = get_comment($comment_id);
        $desp = '' . $comment->comment_content . '
***
<br>
* 评论人 ：' . get_comment_author($comment_id) . '
* 文章标题 ：' . get_the_title() . '
* 文章链接 ：' . get_the_permalink($comment->comment_post_ID) . '
	'; //微信推送内容正文
        $key = _mtx('git_Server_key');
        $postdata = http_build_query(array(
            'text' => $text,
            'desp' => $desp
        ));
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        return $result = file_get_contents('http://sc.ftqq.com/' . $key . '.send', false, $context);
    }
    add_action('comment_post', 'sc_send', 19, 2);
}

//增加B站视频
wp_embed_unregister_handler('bili');
function wp_bili($matches, $attr, $url, $rawattr)
{
    if (mtx_is_mobile()) {
        $height = 200;
    } else {
        $height = 480;
    }
    $iframe = '<iframe width=100% height=' . $height . 'px src="//www.bilibili.com/blackboard/player.html?aid=' . esc_attr($matches[1]) . '" scrolling="no" border="0" framespacing="0" frameborder="no"></iframe>';
    return apply_filters('iframe_bili', $iframe, $matches, $attr, $url, $ramattr);
}
wp_embed_register_handler('bili_iframe', '#https://www.bilibili.com/video/av(.*?)/#i', 'wp_bili');

//bing美图自定义登录页面背景
function custom_login_head()
{
    if (_mtx('git_loginbg')) {
        $imgurl = _mtx('git_loginbg');
    } else {
        $imgurl = get_transient('Bing_img');
        if (false === $imgurl) {
            $arr = json_decode(curl_post('https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1')['data']);
            $imgurl = 'https://cn.bing.com' . $arr->images[0]->url;
            set_transient('Bing_img', $imgurl, 60 * 60 * 24);
        }
    }
    if (defined('UM_DIR')) {
        echo '<style type="text/css">#um_captcha{width:170px!important;}</style>';
    }
    echo '<style type="text/css">#reg_passmail{display:none!important}body{background: url(' . $imgurl . ') center center no-repeat;-moz-background-size: cover;-o-background-size: cover;-webkit-background-size: cover;background-size: cover;background-attachment: fixed;}.login label,a {font-weight: bold;}.login-action-register #login{padding: 5% 0 0;}.login p {line-height: 1;}.login form {margin-top: 10px;padding: 16px 24px 16px;}h1 a { background-image:url(' . home_url() . '/favicon.ico)!important;width:32px;height:32px;-webkit-border-radius:50px;-moz-border-radius:50px;border-radius:50px;}#registerform,#loginform {background-color:rgba(251,251,251,0.3)!important;}.login label,a{color:#000!important;}form label input{margin-top:10px!important;}@media screen and (max-width:600px){.login-action-register h1 {display: none;}.login-action-register #login{top:50%!important;}}</style>';
}
add_action('login_head', 'custom_login_head');

// add youku using iframe
function wp_iframe_handler_youku($matches, $attr, $url, $rawattr)
{
    if (mtx_is_mobile()) {
        $height = 200;
    } else {
        $height = 485;
    }
    $iframe = '<iframe width=100% height=' . $height . 'px src="http://player.youku.com/embed/' . esc_attr($matches[1]) . '" frameborder=0 allowfullscreen></iframe>';
    return apply_filters('iframe_youku', $iframe, $matches, $attr, $url, $ramattr);
}
wp_embed_register_handler('youku_iframe', '#http://v.youku.com/v_show/id_(.*?).html#i', 'wp_iframe_handler_youku');
wp_embed_unregister_handler('youku');

////////////////weauth//////////////

