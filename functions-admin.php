<?php


// require settings and options
define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/settings/' );
require_once get_stylesheet_directory() . '/settings/options-framework.php';

$optionsfile = locate_template( 'options.php' );
load_template( $optionsfile );

//require_once get_stylesheet_directory() . '/options.php';
//require_once get_stylesheet_directory() . '/settings/update.php';

add_action( 'optionsframework_custom_scripts', 'optionsframework_custom_scripts' );

function optionsframework_custom_scripts() { ?>
	<script type="text/javascript">
	jQuery(document).ready(function() {

		jQuery('#example_showhidden').click(function() {
	  		jQuery('#section-example_text_hidden').fadeToggle(400);
		});

		if (jQuery('#example_showhidden:checked').val() !== undefined) {
			jQuery('#section-example_text_hidden').show();
		}

	});
	</script>

	<?php
}



//添加后台左下角文字
function git_admin_footer_text($text)
{
    $text = '感谢使用<a target="_blank" href="https:/meitianxue.net/" >Mtx主题 ' . MTX_VER . '</a>进行创作';
    return $text;
}

add_filter('admin_footer_text', 'git_admin_footer_text');

//添加编辑器快捷按钮
function my_quicktags()
{
    global $pagenow;
    if ($pagenow == 'post-new.php' || $pagenow == 'post.php') {
        wp_enqueue_script('my_quicktags', MTX_URL . '/dist/js/my_quicktags.js', array(
            'quicktags'
        ), '1.0', true);
    }
};
add_action('admin_print_scripts', 'my_quicktags');

// 更改后台字体
function git_admin_style()
{
    echo '<style type="text/css">
	.setting select.link-to option[value="post"],.setting select[data-setting="link"] option[value="post"]{display:none;}
	#wp-admin-bar-git_guide>.ab-item::before {content:"\f331";top:3px;}#wp-admin-bar-git_option>.ab-item::before{content:"\f507";top:3px;}.users #the-list tr:hover{background:rgba(132,219,162,.61)}#role {width:8%;}* { font-family: "Microsoft YaHei" !important; }.wp-admin img.rand_avatar {max-Width:50px !important;}i, .ab-icon, .mce-close, i.mce-i-aligncenter, i.mce-i-alignjustify, i.mce-i-alignleft, i.mce-i-alignright, i.mce-i-blockquote, i.mce-i-bold, i.mce-i-bullist, i.mce-i-charmap, i.mce-i-forecolor, i.mce-i-fullscreen, i.mce-i-help, i.mce-i-hr, i.mce-i-indent, i.mce-i-italic, i.mce-i-link, i.mce-i-ltr, i.mce-i-numlist, i.mce-i-outdent, i.mce-i-pastetext, i.mce-i-pasteword, i.mce-i-redo, i.mce-i-removeformat, i.mce-i-spellchecker, i.mce-i-strikethrough, i.mce-i-underline, i.mce-i-undo, i.mce-i-unlink, i.mce-i-wp-media-library, i.mce-i-wp_adv, i.mce-i-wp_fullscreen, i.mce-i-wp_help, i.mce-i-wp_more, i.mce-i-wp_page, .qt-fullscreen, .star-rating .star,.qt-dfw{ font-family: dashicons !important; }.mce-ico { font-family: tinymce, Arial}.fa { font-family: FontAwesome !important; }.genericon { font-family: "Genericons" !important; }.appearance_page_scte-theme-editor #wpbody *, .ace_editor * { font-family: Monaco, Menlo, "Ubuntu Mono", Consolas, source-code-pro, monospace !important; }
    </style>';
}

add_action('admin_head', 'git_admin_style');

//管理后台添加按钮
function git_custom_adminbar_menu($wp_admin_bar)
{
    if (!is_user_logged_in()) {
        return;
    }
    if (!is_super_admin() || !is_admin_bar_showing()) {
        return;
    }
    $wp_admin_bar->add_menu(array(
        'id' => 'git_option',
        'title' => '主题选项', /* 设置链接名 */
        'href' => 'admin.php?page=mtx-theme-options'
    ));
    $wp_admin_bar->add_menu(array(
        'id' => 'git_guide',
        'title' => 'Git主题使用文档', /* 设置链接名 */
        'href' => 'http://gitcafe.net/archives/3275.html', /* 设置链接地址 */
        'meta' => array(
            'target' => '_blank'
        )
    ));
}

add_action('admin_bar_menu', 'git_custom_adminbar_menu', 100);

//后台文章重新排序
function git_post_order_in_admin($wp_query)
{
    if (is_admin()) {
        $wp_query->set('orderby', 'modified');
        $wp_query->set('order', 'DESC');
    }
}

add_filter('pre_get_posts', 'git_post_order_in_admin');



//后台快捷键回复
function hui_admin_comment_ctrlenter()
{
    echo '<script type="text/javascript">
        jQuery(document).ready(function($){
            $("textarea").keypress(function(e){
                if(e.ctrlKey&&e.which==13||e.which==10){
                    $("#replybtn").click();
                }
            });
        });
    </script>';
};
add_action('admin_footer', 'hui_admin_comment_ctrlenter');

//后台日志阅读统计
add_filter('manage_posts_columns', 'postviews_admin_add_column');
function postviews_admin_add_column($columns)
{
    $columns['views'] = '阅读';
    return $columns;
}

add_action('manage_posts_custom_column', 'postviews_admin_show', 10, 2);
function postviews_admin_show($column_name, $id)
{
    if ($column_name != 'views') return;
    $post_views = get_post_meta($id, "views", true);
    echo $post_views;
}

//////// 后台评论列表获取表情按钮//////
function zfunc_smiley_button($custom = false, $before = '', $after = '')
{
    if ($custom == true) $smiley_url = site_url() . '/wp-includes/images/smilies';
    else $customsmiley_url = MTX_URL . '/assets/img/smilies';
    echo $before;
?>
    <a href="javascript:grin(':?:')"><img src="<?php echo $customsmiley_url; ?>/icon_question.gif" alt="" /></a>
    <a href="javascript:grin(':razz:')"><img src="<?php echo $customsmiley_url; ?>/icon_razz.gif" alt="" /></a>
    <a href="javascript:grin(':sad:')"><img src="<?php echo $customsmiley_url; ?>/icon_sad.gif" alt="" /></a>
    <a href="javascript:grin(':evil:')"><img src="<?php echo $customsmiley_url; ?>/icon_evil.gif" alt="" /></a>
    <a href="javascript:grin(':!:')"><img src="<?php echo $customsmiley_url; ?>/icon_exclaim.gif" alt="" /></a>
    <a href="javascript:grin(':smile:')"><img src="<?php echo $customsmiley_url; ?>/icon_smile.gif" alt="" /></a>
    <a href="javascript:grin(':oops:')"><img src="<?php echo $customsmiley_url; ?>/icon_redface.gif" alt="" /></a>
    <a href="javascript:grin(':grin:')"><img src="<?php echo $customsmiley_url; ?>/icon_biggrin.gif" alt="" /></a>
    <a href="javascript:grin(':eek:')"><img src="<?php echo $customsmiley_url; ?>/icon_surprised.gif" alt="" /></a>
    <a href="javascript:grin(':shock:')"><img src="<?php echo $customsmiley_url; ?>/icon_eek.gif" alt="" /></a>
    <a href="javascript:grin(':???:')"><img src="<?php echo $customsmiley_url; ?>/icon_confused.gif" alt="" /></a>
    <a href="javascript:grin(':cool:')"><img src="<?php echo $customsmiley_url; ?>/icon_cool.gif" alt="" /></a>
    <a href="javascript:grin(':lol:')"><img src="<?php echo $customsmiley_url; ?>/icon_lol.gif" alt="" /></a>
    <a href="javascript:grin(':mad:')"><img src="<?php echo $customsmiley_url; ?>/icon_mad.gif" alt="" /></a>
    <a href="javascript:grin(':twisted:')"><img src="<?php echo $customsmiley_url; ?>/icon_twisted.gif" alt="" /></a>
    <a href="javascript:grin(':roll:')"><img src="<?php echo $customsmiley_url; ?>/icon_rolleyes.gif" alt="" /></a>
    <a href="javascript:grin(':wink:')"><img src="<?php echo $customsmiley_url; ?>/icon_wink.gif" alt="" /></a>
    <a href="javascript:grin(':idea:')"><img src="<?php echo $customsmiley_url; ?>/icon_idea.gif" alt="" /></a>
    <a href="javascript:grin(':arrow:')"><img src="<?php echo $customsmiley_url; ?>/icon_arrow.gif" alt="" /></a>
    <a href="javascript:grin(':neutral:')"><img src="<?php echo $customsmiley_url; ?>/icon_neutral.gif" alt="" /></a>
    <a href="javascript:grin(':cry:')"><img src="<?php echo $customsmiley_url; ?>/icon_cry.gif" alt="" /></a>
    <a href="javascript:grin(':mrgreen:')"><img src="<?php echo $customsmiley_url; ?>/icon_mrgreen.gif" alt="" /></a>
    <?php
    echo $after;
}


//Ajax_data_zfunc_smiley_button
function Ajax_data_zfunc_smiley_button()
{
    if (isset($_GET['action']) && $_GET['action'] == 'Ajax_data_zfunc_smiley_button') {
        nocache_headers();
        zfunc_smiley_button(false, '<br />');
        die();
    }
}



add_action('admin_init', 'Ajax_data_zfunc_smiley_button');

//后台回复评论支持表情插入
function zfunc_admin_enqueue_scripts($hook_suffix)
{
    if ($hook_suffix == 'edit-comments.php') {
        wp_enqueue_script('zfunc-comment-reply', MTX_URL . '/dist/js/admin_reply.js', false, '1.0', true);
    }
}

add_action('admin_enqueue_scripts', 'zfunc_admin_enqueue_scripts');




?>

