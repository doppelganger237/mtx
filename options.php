<?php

/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */
function optionsframework_option_name()
{
	// Change this to use your theme slug
	return 'MTX';
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'theme-textdomain'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options()
{

	// Test data
	$test_array = array(
		'one' => __('One', 'theme-textdomain'),
		'two' => __('Two', 'theme-textdomain'),
		'three' => __('Three', 'theme-textdomain'),
		'four' => __('Four', 'theme-textdomain'),
		'five' => __('Five', 'theme-textdomain')
	);

	// Multicheck Array
	$multicheck_array = array(
		'one' => __('French Toast', 'theme-textdomain'),
		'two' => __('Pancake', 'theme-textdomain'),
		'three' => __('Omelette', 'theme-textdomain'),
		'four' => __('Crepe', 'theme-textdomain'),
		'five' => __('Waffle', 'theme-textdomain')
	);

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one' => '1',
		'five' => '1'
	);

	// Background Defaults
	$background_defaults = array(
		'color' => '',
		'image' => '',
		'repeat' => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	);

	// Typography Defaults
	$typography_defaults = array(
		'size' => '15px',
		'face' => 'georgia',
		'style' => 'bold',
		'color' => '#bada55'
	);

	// Typography Options
	$typography_options = array(
		'sizes' => array('6', '12', '14', '16', '20'),
		'faces' => array('Helvetica Neue' => 'Helvetica Neue', 'Arial' => 'Arial'),
		'styles' => array('normal' => 'Normal', 'bold' => 'Bold'),
		'color' => false
	);

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ($options_tags_obj as $tag) {
		$options_tags[$tag->term_id] = $tag->name;
	}


	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = '选择一个页面:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/images/';

	$options = array();
//做锤子I18N
	$options[] = array(
		'name' => __('常规选项', 'mtx'),
		'type' => 'heading'
	);
	$options[] = array(
		'name' => __('开启滚动栏', 'mtx'),
		'id' => 'speedbar_enabled',
		'desc' => __('是否开启滚动栏公告栏', 'mtx'),
		'type' => "checkbox",
		'std' => true
	);

	$options[] = array(
		'name' => '滚动公告栏',
		'desc' => '最新消息显示在全站导航条下方，非常给力的推广位置',
		'id' => 'gundong',
		'type' => 'textarea',
		'std' => '<li>欢迎使用<a target="_blank" href="https://meitianxue.net/">每天学</a>制作的主题</li>
<li>延续了Git的滚动信息框</li>',
		
	);

	$options[] = array(
		'name' => '找回密码页面',
		'desc' => '输入找回密码页面的ID',
		'id' => 'user_rp',
		'options' => $options_pages,
		'type' => 'select'
	);
	$options[] = array(
		'name' => '列表Ajax下拉加载',
		'desc' => '开启本选项之后网站会采用ajax方式下拉自动加载,因为Card模式暂时关闭,所以只在博客模式有效果',
		'id' => 'ajaxpager',
		'type' => 'checkbox',
		'std'	=> true
	);

	$options[] = array(
		'name' => '关闭Feed',
		'desc' => '没任何用的功能',
		'id' => 'feed_disable',
		'type' => 'checkbox',
		'std'	=> true
	);

	$options[] = array(
		'name' => '开启热门排行',
		'desc' => '首页显示热门排行',
		'id' => 'hot_list_check',
		'type' => 'checkbox',
		'std'	=> true
	);
	$options[] = array(
		'name' => '排行名称',
		'desc' => '这里是显示在网站首页热门排行那里',
		'id' => 'hot_list_title',
		'type' => 'text',
		'std' => '本周热门'
	);
	$options[] = array(
		'name' => '热门排行排序根据',
		'desc' => '选择一个参数作为排序的根据，可以选择评论数目，文章置顶，置顶文章最多10篇',
		'id' => 'hot_list_desc',
		'type' => 'radio',
		'options' => 	 array(
				'1' => '评论数目',
				'2' => '文章置顶'
		),
		'std' => '1'
	);

	$options[] = array(
		'name' => '用户登录信息',
		'desc' => '开启',
		'id' => 'sign_info',
		'type' => 'checkbox',
		'std'	=> true
	);
	$options[] = array(
		'name' => '百度分享',
		'desc' => '开启并且同时开启打赏功能，支持https',
		'id' => 'bdshare',
		'type' => 'checkbox',
		'std'	=> true
	);

	$options[] = array(
		'name' => '搜索框',
		'desc' => '占位文本',
		'id' => 'search_placeholder',
		'type' => 'text',
		'std' => '输入内容并回车'
	);
	$options[] = array(
		'name' => '评论',
		'desc' => '评论框占位文本',
		'id' => 'comment_placeholder',
		'type' => 'text',
		'std' => '说点什么吧…'
	);



	$options[] = array(
		'name' => __('SEO', 'mtx'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __('全站连接符', 'mtx'),
		'id' => 'delimiter',
		'desc' => __('一经选择，切勿更改，对SEO不友好，一般为“-”或“_”', 'mtx'),
		'std' => _mtx('delimiter') ? _mtx('delimiter') : '-',
		'type' => 'text',
		'class' => 'mini'
	);

	$options[] = array(
		'name' => '自动内链',
		'desc' => '启用',
		'id' => 'autolink',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '链接去掉Categroy',
		'desc' => '启用  【开启后，需要至设置——固定连接——重新保存一下，否则会发生404错误】',
		'id' => 'categroy_b',
		'type' => 'checkbox',
		'std'	=> true
	);
	$options[] =array(
		'name' => '关键词出现数目',
		'desc' => '文章中少于这个数字的关键词将不自动内链，默认是1，即全部链接',
		'id' => 'autolink_condition',
		'type' => 'text',
		'class' => 'mini', //mini, tiny, small
		'std' => 1
	);

	$options[] =array(
		'name' => '关键词链接次数',
		'desc' => '文章中最多链接的次数，默认是6',
		'id' => 'autolink_max',
		'type' => 'text',
		'class' => 'mini', //mini, tiny, small
		'std' => 6
	);

	$options[] =	array(
		'name' => '图片自动添加alt以及title',
		'desc' => '启用',
		'id' => 'auto_imgalt',
		'type' => 'checkbox',
		'std'	=> true
	);

	$options[] = array(
		'name' => '外链自动GO跳转',
		'desc' => '启用 【启用之后需要新建页面，模板选择Go跳转页面，别名为go】',
		'id' => 'auto_go',
		'type' => 'checkbox',
		'std'	=> true
	);

	$options[] =array(
		'name' => '外链自动添加nofollow',
		'desc' => '启用',
		'id' => 'auto_nofollow',
		'type' => 'checkbox'
	);


	$options[] =array(
		'name' => '主动推送接口地址，填写本项即开启推送',
		'desc' => '在百度站长平台获取主动推送接口地址，比如：http://data.zz.baidu.com/urls?site=域名&token=一组字符',
		'id' => 'baidu_auto_submit_url',
		'type' => 'text'
	);


	$options[] = array(
		'name' => __('文章设置', 'mtx'),
		'type' => 'heading'
	);


/* 	$options[] = array(
		'name' =>'列表文章属性',
		'id' => 'excerpt_attitudes',
		'std' => array(), // These items get checked by default
		'type' => 'multicheck',
		'options' =>array(
			'1' => '不显示访客数',
			'2' => '不显示作者名',
			'3' => '不显示评论数',
			'4' => '不显示发布时间',
			'5' => '不显示喜欢数'
	)); */


	$options[] = array(
		'name' => '文章面包屑',
		'desc' => '开启',
		'id' => 'bread_menu',
		'type' => 'checkbox',
		'std'	=> true
	);
	$options[] = array(
		'name' => '自动首行缩进',
		'desc' => '开启 【开启后对文字内容自动首行缩进2格】',
		'id' => 'auto_suojin',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '自动中英文空格',
		'desc' => '开启 【开启后对中英文会间隔开，但是会打乱部分关键词】',
		'id' => 'auto_space',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '文章按照更新日期排序',
		'desc' => '开启 【开启按照最新更新排序，不开启则按照默认时间排序】',
		'id' => 'orderbygx',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => '文章自增別名',
		'desc' => '新站如果要使文章地址连续建议一直保持开启! 具体查看这篇文章：https://meitianxue.net/articles/270.html',
		'id' => 'sequential_postname',
		'type' => 'checkbox',
		'std'	=> false
	);

	$options[] = array(
		'name' => '过滤外语评论',
		'desc' => '开启 【启用后，将屏蔽所有含有日文以及英语的评论，外贸站慎用】',
		'id' => 'spam_lang',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '关键词，IP，邮箱屏蔽',
		'desc' => '开启 【启用后，在WordPress-设置-讨论-黑名单中添加想要屏蔽的关键词，邮箱，网址，IP地址，每行一个】<a class="button-primary" target="_blank" href="https://img.alicdn.com/imgextra/i4/1597576229/TB2FnxnlpXXXXcDXXXXXXXXXXXX_!!1597576229.png">如图设置</a>',
		'id' => 'spam_keywords',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '屏蔽含有链接的评论',
		'desc' => '开启 【启用后，屏蔽内容或者评论昵称含有链接的评论，如果您的评论需要输入链接或者图片的话，请慎选！！！】',
		'id' => 'spam_url',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '屏蔽长链接评论',
		'desc' => '开启 【启用后，屏蔽含有过长网址(超过50个字符)的评论，当然如果你已经选择了上面的选项的话，就不用选择了】',
		'id' => 'spam_long',
		'type' => 'checkbox'
	);
	$options[] = array(
		'title' => '评论设置属性',
		'type' => 'title'
	);





	$options[] = array(
		'name' => '评论框属性',
		'desc' => '不显示插入图片',
		'id' => 'comment_tietu',
		'type' => 'checkbox',
		'std'	=> true
	);
$options[] = array(
		'desc' => '不显示加粗',
		'id' => 'comment_jiacu',
		'type' => 'checkbox',
		'std'	=> true
);
$options[] = array(
		'desc' => '不显示删除线',
		'id' => 'comment_shanchu',
		'type' => 'checkbox',
		'std'	=> true
);
$options[] = array(
		'desc' => '不显示居中',
		'id' => 'comment_juzhong',
		'type' => 'checkbox',
		'std'	=> true
);
$options[] = array(
		'desc' => '不显示斜体',
		'id' => 'comment_xieti',
		'type' => 'checkbox',
		'std'	=> true
);


	$options[] = array(
		'name' => '文章摘要',
		'desc' => '个字',
		'id' => 'excerpt_length',
		'type' => 'text',
		'class' => 'mini', //mini, tiny, small
		'std' => '150',
	);

	$options[] = array(
		'name' => '作者模块',
		'desc' => '启用',
		'id' => 'auther_module',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => '相关文章显示条数',
		'desc' => '条&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 这是是显示文章下面的相关文章数目的',
		'id' => 'related_count',
		'type' => 'text',
		'class' => 'mini', //mini, tiny, small
		'std' => 8
	);
	$options[] = array(
		'name' => '禁止站内文章Pingback',
		'desc' => '开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 开启后，不会发送站内Pingback，建议开启',
		'id' => 'disable_pingback',
		'type' => 'checkbox',
		'std'	=> true
	);
	$options[] = array(
		'name' => '禁止后台编辑时自动保存',
		'desc' => '开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 开启后，后台编辑文章时候不会定时保存，有效缩减数据库存储量，而且自动保存会占用pid',
		'id' => 'disable_autosave',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '文章版权声明',
		'desc' => '此处输入的文字将出现在每篇文章最底部，你可以使用：{{title}}表示文章标题，{{link}}表示文章链接',
		'id' => 'copyright_info',
		'type' => 'textarea',
		'std' => '每天学 , 版权所有丨如未注明 , 均为原创丨本网站采用<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" rel="nofollow" target="_blank" title="BY-NC-SA授权协议">BY-NC-SA</a>协议进行授权 <br >转载请注明原文链接：<a href="{{link}}" target="_blank" title="{{title}}">{{title}}</a>'
	);

	$options[] = array(
		'name' => __('样式设置', 'mtx'),
		'type' => 'heading'
	);

	$options[]  = array(
				'name' => '透明导航栏',
				'desc' => '开启   【开启后您的菜单导航栏就会变成半透明】',
				'id' => 'touming_nav',
				'type' => 'checkbox',
				'std'	=> true
	);


	$options[]  = array(
				'name' => '自定义图片logo',
				'desc' => '开启【开启后您的头部背景将显示默认图片logo，不开启则显示默认文字logo】',
				'id' => 'head_enable_customlogo',
				'type' => 'checkbox'
	);
	
	$options[]  = 	array(
				'name' => '自定义头部logo',
				'desc' => '请在这里输入您的图片路径',
				'id' => 'head_customlogo',
				'type' => 'upload',
				'std' => ''
	);
	$options[]  = array(
				'name' => 'logo居左',
				'desc' => '开启【开启后您的logo将居左显示】',
				'id' => 'make_logo_left',
				'type' => 'checkbox'
	);
		

	$options[]  = 	array(
				'name' => '侧边栏跟随',
				'desc' => '开启 【开启后为下面的选项选择侧边栏序号，注意是从2开始数的】',
				'id' => 'sideroll_enable',
				'type' => 'checkbox'
	);

	$options[] = array(
		'id' => 'sideroll_index',
		'std' => '1 2',
		'class' => 'mini',
		'desc' => __('设置随动模块，多个模块之间用空格隔开即可！默认：“1 2”，表示第1和第2个模块，建议最多3个模块 ', 'mtx'),
		'type' => 'text');
		$options[] = array(
			'name' => '启用幻灯片',
			'id' => 'swiper_enable',
			'type' => "checkbox",
			'std' => true,
			'desc' => '开启');


			$options[] =	array(
			'name' => '移动端不显示',
			'desc' => '开启',
			'id' => 'swiper_disable_mobile',
			'type' => 'checkbox'
	);

	$options[] =array(
			'name' => '幻灯片一图片',
			'desc' => '在这里输入您的幻灯片的图片路径',
			'id' => 'swiper_1_image',
			'type' => 'text',
			'std' => 'https://p.ssl.qhimg.com/t018a12da24a5687855.jpg'
	);
	$options[] =array(
			'name' => '幻灯片一链接',
			'desc' => '在这里输入您的幻灯片的引用链接',
			'id' => 'swiper_1_url',
			'type' => 'text',
			'std' => ''
	);
	$options[] =array(
			'name' => '幻灯片一标题',
			'desc' => '在这里输入您的幻灯片的标题',
			'id' => 'swiper_1_title',
			'type' => 'text',
			'std' => ''
	);
	$options[] =	array(
			'name' => '幻灯片二图片',
			'desc' => '在这里输入您的幻灯片的图片路径',
			'id' => 'swiper_2_image',
			'type' => 'text',
			'std' => 'https://p.ssl.qhimg.com/t019de3d2e67ceef590.jpg'
	);
	$options[] =	array(
			'name' => '幻灯片二链接',
			'desc' => '在这里输入您的幻灯片的引用链接',
			'id' => 'swiper_2_url',
			'type' => 'text',
			'std' => ''
	);
	$options[] =array(
			'name' => '幻灯片二标题',
			'desc' => '在这里输入您的幻灯片的标题',
			'id' => 'swiper_2_title',
			'type' => 'text',
			'std' => ''
	);
//不加了先,累死了
/* 
		$options[] = array(
				'name' => '网站是否开启卡片式',
				'desc' => '启用 【不启用的话，显示是传统博客形式】',
				'id' => 'card_layout',
				'type' => 'checkbox'
		);
		$options[] = array(
				'name' => '选择分类展示形式',
				'desc' => '选择一种风格作为分类页面的展示形式，有卡片式和列表式',
				'id' => 'cat_layout_style',
				'type' => 'radio',
				'options' => array(
						'1' => '卡片风格',
						'2' => '列表风格'
				),
				'std' => '2'
			);
			$options[] = array(
				'name' => '选择标签展示形式',
				'desc' => '选择一种风格作为标签页面的展示形式，有卡片式和列表式',
				'id' => 'tag_layout_style',
				'type' => 'radio',
				'options' => array(
						'1' => '列表',
						'2' => '卡片风格'
				),
				'std' => '2'
			); */

		

	


	
	
	$options[] = array(
		'name' => __('底部设置', 'mtx'),
		'type' => 'heading'
	);
	$options[] = array(
		'name' => '超级Footer',
		'desc' => '启用【开启后，下面的设置才会有效】',
		'id' => 'superfoot_enable',
		'type' => 'checkbox',
		'std'	=> true
	);
	$options[] = array(
		'name' => 'Footer1标题',
		'desc' => '在这里输入第一个footer的标题',
		'id' => 'superfoot_1_title',
		'type' => 'text',
		'std' => '版权声明'
	);
	$options[] = array(
		'name' => 'Footer1内容',
		'desc' => '在这里输入第一个footer的内容',
		'id' => 'superfoot_1_content',
		'type' => 'textarea',
		'std' => '本站的文章和资源来自互联网或者站长<br>的原创，按照 CC BY -NC -SA 3.0 CN<br>协议发布和共享，转载或引用本站文章<br>应遵循相同协议。如果有侵犯版权的资<br>源请尽快联系站长，我们会在24h内删<br>除有争议的资源。'
);
$options[] = array(
		'name' => 'Footer2标题',
		'desc' => '在这里输入第二个footer的标题',
		'id' => 'superfoot_2_title',
		'type' => 'text',
		'std' => '网站驱动'
);
$options[] = array(
		'name' => 'Footer2内容',
		'desc' => '在这里输入第二个footer的内容',
		'id' => 'superfoot_2_content',
		'type' => 'textarea',
		'std' => '<ul><li><a href="https://meitianxue.net/" title="每天学" target="_blank">每天学</a></li></ul>'
);
$options[] = array(
		'name' => 'Footer3标题',
		'desc' => '在这里输入第三个footer的标题',
		'id' => 'superfoot_3_title',
		'type' => 'text',
		'std' => '友情链接'
);
$options[] = array(
		'name' => 'Footer3内容',
		'desc' => '在这里输入第三个footer的内容',
		'id' => 'superfoot_3_content',
		'type' => 'textarea',
		'std' => '<ul><li><a href="https://meitianxue.net/" title="每天学" target="_blank">每天学</a></li></ul>'
);
$options[] = array(
		'name' => 'Footer4标题',
		'desc' => '在这里输入第四个footer的标题',
		'id' => 'superfoot_4_title',
		'type' => 'text',
		'std' => '支持主题'
);
$options[] = array(
		'name' => 'Footer4内容',
		'desc' => '在这里输入第四个footer的内容',
		'id' => 'superfoot_4_content',
		'type' => 'textarea',
		'std' => '<ul><li><a href="https://meitianxue.net/" title="每天学" target="_blank">每天学</a></li></ul>'
);
$options[] = array(
		'name' => '网站footer公共代码',
		'desc' => '在全站页面footer部分出现，可放置网站的版权信息等等',
		'id' => 'foot_code',
		'type' => 'textarea',
		'std' => 'Copyright © 2020 <a href="/" title="每天学">每天学</a> | <a href="/tags.html" target="_blank" title="标签云">标签云</a>  '
);


	
$options[] = array(
	'name' => __('广告设置', 'mtx'),
	'type' => 'heading'
);

$options[] = array(
	'name' => '网站顶部右侧广告',
	'desc' => '开启 【这里需要logo居左才可以生效】',
	'id' => 'git_toubuads',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：Feed页面',
	'desc' => '推荐使用谷歌in-feed',
	'id' => 'feed_ad',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：404页面广告',
	'desc' => '开启',
	'id' => 'git_404ad',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：全站 - 导航下横幅',
	'desc' => '显示在公告栏下',
	'id' => 'git_adsite_01',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：幻灯片下广告',
	'desc' => '如果幻灯没开启，则不显示',
	'id' => 'adindex_02',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：文章页 - 页面标题下',
	'desc' => '开启',
	'id' => 'git_adpost_01',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：文章页 - 相关文章下',
	'desc' => '开启',
	'id' => 'git_adpost_02',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：文章页 - 网友评论下',
	'desc' => '开启',
	'id' => 'git_adpost_03',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：下载单页上横幅',
	'desc' => '开启',
	'id' => 'git_downloadad1',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '广告：下载单页下横幅',
	'desc' => '开启',
	'id' => 'git_downloadad2',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '手机广告：全站正文列表最',
	'desc' => '开启【手机广告只适合在手机中投放。例如百度联盟移动广告，PC端不会显示。下同】',
	'id' => 'Mobiled_adindex_02',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '手机广告：文章页 - 页面标题下',
	'desc' => '开启',
	'id' => 'Mobiled_adpost_01',
	'type' => 'textarea',
	'std' => ''
);
$options[] = array(
	'name' => '手机广告：文章页 - 相关文章下',
	'desc' => '开启',
	'id' => 'Mobiled_adpost_02',
	'type' => 'textarea',
	'std' => ''
);




	
$options[] = array(
	'name' => __('优化设置', 'mtx'),
	'type' => 'heading'
);
$options[] = array(
	'name' => '禁用REST API',
	'desc' => '禁用  【禁用后，APP开发或者小程序开发会有影响】',
	'id' => 'restapi_disable',
	'type' => 'checkbox'
);

$options[] = array(
	'name' => '百度收录提示',
	'desc' => '启用   【开启后，将会在文章标题下显示百度收录状态，需要curl扩展的支持，否则不生效】',
	'id' => 'baidurecord_enable',
	'type' => 'checkbox'
);

$options[] = array(
	'name' => '复制弹窗提醒',
	'desc' => '启用   【启用后，访客复制之后会弹出提示弹窗】',
	'id' => 'copydialog_enable',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => '页面&站内搜索伪静态',
	'desc' => '启用   【开启后，请前往固定连接重新保存一下，否则404】',
	'id' => 'pagehtml_enable',
	'type' => 'checkbox',
	'str'	=> true
);
$options[] = array(
	'name' => '评论UA',
	'desc' => '启用',
	'id' => 'comment_ua',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => '取消静态资源的版本查询',
	'desc' => '启用',
	'id' => 'query_cancel',
	'type' => 'checkbox'
);



$options[] = array(
	'name' => '限制每个IP的注册',
	'desc' => '鸡肋功能,历史性保留,启用 【启用之后，主题会在网站根目录生成ips.txt文件，里面的ip就是保存的已注册用户的ip】',
	'id' => 'regist_ips',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => '每个IP允许注册的用户数',
	'desc' => '请输入每个IP允许的注册数目，默认为1',
	'id' => 'regist_ips_num',
	'type' => 'number',
	'std' => 1
);
$options[] = array(
	'name' => '新用户注册站长邮件',
	'desc' => '开启',
	'id' => 'user_notification_to_admin',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => '自定义注册欢迎邮件',
	'desc' => '开启  【本功能为用户注册后发一个体验较好的邮件，开启后同时关闭默认欢迎邮件】',
	'id' => 'user_notification_to_user',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => '开启用户注册成功重定向',
	'desc' => '开启',
	'id' => 'register_redirect_ok',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => '注册成功后重定向',
	'desc' => '选择一个重定向链接,如果选择自定义URL，请在下方填写好跳转链接',
	'id' => 'redirect_choise',
	'type' => 'radio',
	'options' => array(
			'1' => '网站首页',
			'2' => '前台个人中心',
			'3' => '后台台个人中心',
			'4' => '自定义URL'
	),
	'std' => '1'
);
$options[] = array(
	'name' => '自定义注册重定向URL',
	'desc' => '如果自定义跳转开启的话,这里一定要填写链接',
	'id' => 'register_redirect_url',
	'type' => 'text',
	'std' => ''
);





$options[] = array(
	'name' => __('高级设置', 'mtx'),
	'type' => 'heading'
);

$options[] = array(
	'name' => '头像来源设置',
	'desc' => '头像设置',
	'id' => 'avater_source',
	'type' => 'radio',
	'options' => array(
			'1' => '随机头像【速度最快但随机】',
			'2' => '头像镜像【精确头像但速度略慢】'
	),
	'std' => '1'
);
$options[] = array(
	'name' => 'jQuery来源设置',
	'desc' => '选择一个适合自己的jQuery公共库来源',
	'id' => 'jqcdn_source',
	'type' => 'radio',
	'options' => array(
			'1' => '远程jQuery库【底部加载，速度快，兼容差】',
			'2' => '本地jQuery库【头部加载，速度慢，兼容好】'
	),
	'std' => '1'
);
$options[] = array(
	'name' => 'HTML代码压缩',
	'desc' => '启用 【开启后，将压缩网页HTML代码，可读性会降低，但是性能略有提升】',
	'id' => 'html_compress',
	'type' => 'checkbox',
	'str'	=> true
);
$options[] = array(
	'name' => '图片懒加载',
	'desc' => '启用 【开启后，网站图片将进行懒加载】',
	'id' => 'lazyload',
	'type' => 'checkbox',
	'std'	=> true
);
$options[] =array(
	'name' => '侧边栏缓存',
	'desc' => '启用 【开启后，将会自动缓存小工具，如果想禁止缓存某个小工具，可以去小工具页面排除】',
	'id' => 'sidebar_cache',
	'type' => 'checkbox'
);

/* $options[] =array(
	'name' => '是否启用微信扫码登录',
	'desc' => '启用 【开启后，新建微信登录页面即可，另外需要HTTPS】',
	'id' => 'weauth_oauth',
	'type' => 'checkbox'
);
$options[] =array(
	'name' => '是否启用强制微信登录',
	'desc' => '启用 【开启后，将禁用WordPress自带的登录，所有登录地址都跳转到微信的登录，如需临时使用自带登录，可以使用这个链接：你的域名/wp-login.php?loggedout=true】',
	'id' => 'weauth_oauth_force',
	'type' => 'checkbox'
);
 */
$options[] = array(
	'name' => 'CDN开启',
	'desc' => '',
	'id' => 'cdn_enable',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => 'CDN加速域名',
	'desc' => '输入本项目之前，必须开启本功能，输入您的七牛云自定义域名，必须带 <font color="#cc0000"><strong>http://</strong></font>  结尾不能带/  <a class="button-primary" target="_blank" href="https://meitianxue.net/go/qiniu" title="立刻注册七牛，免费使用免备案高速CDN">注册七牛</a>，并获取链接<a rel="nofollow" href="http://71bbs.people.com.cn/postImages/89/CF/7B/F5/1509845597173.jpg" target="_blank">如图</a>',
	'id' => 'cdn_url',
	'type' => 'text',
	'std' => ''
);
$options[] = array(
	'name' => 'CDN镜像文件格式',
	'desc' => '在输入框内添加准备镜像的文件格式，比如jpg，png，gif，mp3，mp4（使用|分隔）',
	'id' => 'cdn_url_format',
	'type' => 'text',
	'std' => 'png|jpg|jpeg|gif|ico|html|7z|zip|rar|pdf|ppt|wmv|mp4|avi|mp3|txt'
);
$options[] = array(
	'name' => 'CDN镜像文件夹',
	'desc' => '在输入框内添加准备镜像的文件夹，比如wp-content|wp-includes（使用|分隔）',
	'id' => 'cdn_url_dir',
	'type' => 'text',
	'std' => 'wp-content|wp-includes'
);
$options[] = array(
	'name' => 'CDN自定义样式',
	'desc' => '有些CDN可以压缩图片,如果你的CDN有这个功能可以开启',
	'id' => 'cdn_url_style',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => 'CDN水印',
	'desc' => '愚蠢的功能',
	'id' => 'cdn_water',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => 'CDN镜像后台化',
	'desc' => '启用【一般可不启用，如果您启用CDN镜像之后并在FTP删除了本地文件，则必须开启】',
	'id' => 'cdn_jingxiang',
	'type' => 'checkbox'
);

$options[] = array(
	'name' => 'HTML5 桌面推送',
	'desc' => '愚蠢的功能',
	'id' => 'desktop_notification_enable',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => 'HTML5推送标题【必选】',
	'desc' => '显示在弹窗顶部',
	'id' => 'desktop_notification_title',
	'type' => 'text',
	'std' => 'Hi,你好!'
);
$options[] = array(
	'name' => 'HTML5推送间隔【必选】',
	'desc' => '输入数字，当自动关闭或者用户关闭之后多久再次弹窗，默认10天',
	'id' => 'desktop_notification_days',
	'type' => 'number',
	'std' => 10
);
$options[] = array(
	'name' => 'HTML5推送COOKIE【必选】',
	'desc' => '修改COOKIE值可以强制向访客推送新的信息，无视时间间隔，不能使用中文，默认233',
	'id' => 'desktop_notification_cookie',
	'type' => 'text',
	'std' => '233'
);
$options[] = array(
	'name' => 'HTML5推送图片【必选】',
	'desc' => '填写一个正方形的图片，显示在推送信息左侧，默认为默认头像',
	'id' => 'desktop_notification_icon',
	'type' => 'text',
	'std' => deel_avatar_default()
);
$options[] = array(
	'name' => 'HTML5推送链接【可选】',
	'desc' => '当用户点击弹窗的时候说点击的链接，默认为每天学',
	'id' => 'desktop_notification_link',
	'type' => 'text',
	'std' => 'https://meitianxue.net'
);
$options[] = array(
	'name' => 'HTML5推送内容',
	'desc' => '在这里输入推送主体内容，字数合适就行，不能太多【必选】',
	'id' => 'desktop_notification_body',
	'type' => 'textarea',
	'std' => '每天学,重置Git主题,分享互联网实用干货知识和生活里的小知识'
);


$options[] = array(
	'name' => '接管SMTP',
	'desc' => '',
	'id' => 'smtp_enable',
	'type' => 'checkbox'
);

$options[] = array(
	'name' => '发件人地址',
	'desc' => '请输入您的邮箱地址',
	'id' => 'mail_address',
	'type' => 'text',
	'std' => ''
);
$options[] = array(
	'name' => '发件人昵称',
	'desc' => '请输入您的网站名称',
	'id' => 'mail_name',
	'type' => 'text',
	'std' => ''
);
$options[] = array(
	'name' => 'SMTP服务器地址',
	'desc' => '请输入您的邮箱的SMTP服务器，查看<a class="button-primary" target="_blank" href="http://wenku.baidu.com/link?url=Xc_mRFw2K-dimKX845QalqLpZzly07mC4a_t_QjOSPov0uFx3MWTl3wgw4tOAyTbDlS7lT8TOAj8VOxDYU186wQLKPt1fKncz7k_jbP_RQi">查看常用SMTP地址</a>',
	'id' => 'mail_server',
	'type' => 'text',
	'std' => 'smtp.qq.com'
);
$options[] = array(
	'name' => 'SSL安全连接',
	'desc' => '启用',
	'id' => 'mail_ssl',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => 'SMTP服务器端口',
	'desc' => '请输入您的smtp端口，一般QQ邮箱25就可以了,如果选择了上面的SSL，推荐使用465端口',
	'id' => 'mail_port',
	'type' => 'text',
	'class' => 'mini', //mini, tiny, small
	'std' => 465
);
$options[] = array(
	'name' => '邮箱账号',
	'desc' => '请输入您的邮箱地址，比如我的1013370209@qq.com',
	'id' => 'mail_user',
	'type' => 'text',
	'std' => ''
);
$options[] = array(
	'name' => '邮箱密码',
	'desc' => '请输入您的邮箱授权码',
	'id' => 'mail_password',
	'type' => 'password',
	'std' => ''
);

/* $options[] = array(
	'name' => '百度站内搜索',
	'desc' => '开启 【开启百度站内搜索将关闭自带搜索】',
	'id' => 'search_baidu',
	'type' => 'checkbox'
);
$options[] = array(
	'name' => '百度站内搜索代码',
	'desc' => '将从百度搜索获取的代码添加到本输入框',
	'id' => 'search_baidu_code',
	'type' => 'textarea',
	'std' => ''
); */

$options[] = array(
	'name' => __('社交设置', 'mtx'),
	'type' => 'heading'
);




$options[] = array(
	'name' => '微信二维码',
	'desc' => '请输入您的二维码图片路径',
	'id' => 'mtx_weixin_qr',
	'type' => 'text',
	'std' => 'https://cdn.meitianxue.net/wp-content/themes/git/assets/img/wechat.png'
);
$options[] = array(
	'name' => '腾讯QQ',
	'desc' => '直接输入QQ号即可',
	'id' => 'mtx_qq',
	'type' => 'text',
	'std' => '1013370209'
);
$options[] = array(
	'name' => '腾讯QQ二维码',
	'desc' => '二维码路径',
	'id' => 'mtx_qq_qr',
	'type' => 'text',
	'std' => 'https://cdn.meitianxue.net/wp-content/themes/git/assets/img/QQ.jpg'
);

$options[] = array(
	'name' => '支付宝二维码',
	'desc' => '请输入您的支付宝图片路径',
	'id' => 'mtx_alipay_qr',
	'type' => 'text',
	'std' => 'https://cdn.meitianxue.net/wp-content/themes/git/assets/img/alipay.png'
);
$options[] = array(
	'name' => '自定义社交网络名字',
	'desc' => '输入您的其他的社交网络名字，比如：github',
	'id' => 'mtx_customicon_name',
	'type' => 'text',
	'std' => 'Telegram'
);
$options[] = array(
	'name' => '自定义社交网络链接',
	'desc' => '输入您的其他的社交网络链接',
	'id' => 'mtx_customicon_url',
	'type' => 'text',
	'std' => 'https://t.me/dongbai'
);
$options[] = array(
	'name' => '自定义社交网络图标',
	'desc' => '输入您的其他的社交网络图标，使用awesome图标，格式类似于fa-github',
	'id' => 'mtx_customicon_icon',
	'type' => 'text',
	'std' => 'fa-telegram'
);


$options[] = array(
	'name' => __('自定义代码', 'mtx'),
	'type' => 'heading'
);
$options[] = array(
	'name' => '流量统计代码',
	'desc' => '统计网站流量，推荐使用百度统计，国内比较优秀且速度快；还可使用Google统计、CNZZ等',
	'id' => 'tongji_code',
	'type' => 'textarea'
);
$options[] = array(
	'name' => '网站头部代码',
	'desc' => '会自动出现在页面头部（head区域），可放置广告代码等自定义代码的全局代码块',
	'id' => 'headcode',
	'type' => 'textarea'
);
$options[] = array(
	'name' => '网站自定义样式CSS',
	'desc' => '网站全局CSS代码，可以直接加入css代码，比如：.authorsocials i{font-size:16px;width:20px;height:18px}',
	'id' => 'customcss',
	'type' => 'textarea'
);

$options[] = array(
	'name' => '全站底部脚本代码',
	'desc' => '可放置广告代码等自定义（css或js）的全局代码块',
	'id' => 'footer_code',
	'type' => 'textarea',
	'std' => ''
);



	/**
	 * For $settings options see:
	 * http://codex.wordpress.org/Function_Reference/wp_editor
	 *
	 * 'media_buttons' are not supported as there is no post to attach items to
	 * 'textarea_name' is set by the 'id' you choose
	 */

	/* 	$wp_editor_settings = array(
		'wpautop' => true, // Default
		'textarea_rows' => 5,
		'tinymce' => array( 'plugins' => 'wordpress,wplink' )
	);

	$options[] = array(
		'name' => __( 'Default Text Editor', 'theme-textdomain' ),
		'desc' => sprintf( __( 'You can also pass settings to the editor.  Read more about wp_editor in <a href="%1$s" target="_blank">the WordPress codex</a>', 'theme-textdomain' ), 'http://codex.wordpress.org/Function_Reference/wp_editor' ),
		'id' => 'example_editor',
		'type' => 'editor',
		'settings' => $wp_editor_settings
	); */

	return $options;
}
