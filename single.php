<?php get_header();
?>
<div class="content-wrap">
    <div class="content">
        <?php
        if (_mtx('bread_menu')) {
            echo '<div class="breadcrumbs">' . mtx_breadcrumbs() . '</div>';
        }
        if (_mtx('auto_suojin')) {
            /* 挺骚啊小伙子 */
            echo '<style type="text/css">.article-content p {text-indent: 2em;}.article-content p a,.article-content p video,.article-content table p{text-indent: 0 !important;}</style>';
        }
        ?>
        <?php
        while (have_posts()) :
            the_post();
        ?>
            <header class="article-header">
                <h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
                <div class="meta">
                    <?php
                    $category = get_the_category();
                    if ($category[0]) {
                        echo '<span id="mute-category" class="muted"><i class="fa fa-list-alt"></i><a href="' . get_category_link($category[0]->term_id) . '"> ' . $category[0]->cat_name . '</a></span>';
                    }
                    ?>
                    <span class="muted"><i class="fa fa-user"></i> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>"><?php echo get_the_author() ?></a></span>
                    <?php
                    $zhuanzai = get_post_meta($post->ID, 'mtx_zhuanzai_name', true);
                    if ($zhuanzai) {
                        echo '<span class="muted"><i class="fa fa-info-circle"></i> 来源：<a rel="nofollow" target="_blank" href="' . get_post_meta($post->ID, 'mtx_zhuanzai_link', true) . '">' . get_post_meta($post->ID, 'mtx_zhuanzai_name', true) . '</a></span>';
                    } else {
                        echo '<span class="muted"><i class="fa fa-info-circle"></i> 来源：原创</span>';
                    }
                    ?>
                    <span class="muted"><i class="fa fa-clock-o"></i> <?php echo timeago(get_gmt_from_date(get_the_time('Y-m-d G:i:s'))) ?></span>
                    <span class="muted"><i class="fa fa-eye"></i> <?php echo deel_views('次浏览'); ?></span>
                    <?php
                    /* 百度收录提示,毫无卵用 */
                    if (_mtx('git_baidurecord_b') && function_exists('curl_init')) {
                    ?>
                        <span class="muted"><i class="fa fa-flag"></i> <?php baidu_record(); ?></span>
                    <?php
                    }
                    ?>
                    <?php
                    if (comments_open()) {
                        $num =  get_comments_number('0', '1', '%');
                        if ($num != "0") {
                            echo '<span class="muted"><i class="fa fa-comments-o"></i> <a href="' . get_comments_link() . '">' . $num . '个评论</a></span>';
                        }
                    }
                    ?>
                    <span class="muted"><?php edit_post_link('[编辑]'); ?></span>
                </div>
            </header>
            <?php
            if (_mtx('git_adpost_01')) {
                echo '<div class="banner banner-post">' . _mtx('git_adpost_01') . '</div>';
            }

            if (mtx_is_mobile()) {
                if (_mtx('Mobiled_adpost_01')) {
                    echo '<div class="banner-post mobileads">' . _mtx('Mobiled_adpost_01') . '</div>';
                }
            }
            ?>

            <article class="article-content">
                <?php
                the_content();
                ?>
                <?php
                wp_link_pages(array(
                    'before' => '<div class="fenye">',
                    'after' => '',
                    'next_or_number' => 'next',
                    'previouspagelink' => '<span>上一页</span>',
                    'nextpagelink' => ""
                ));
                ?> <?php
                    wp_link_pages(array(
                        'before' => '',
                        'after' => '',
                        'next_or_number' => 'number',
                        'link_before' => '<span>',
                        'link_after' => '</span>'
                    ));
                    ?> <?php
                        wp_link_pages(array(
                            'before' => '',
                            'after' => '</div>',
                            'next_or_number' => 'next',
                            'previouspagelink' => '',
                            'nextpagelink' => "<span>下一页</span>"
                        ));
                        ?>
                <?php if (!defined('UM_DIR')) : ?>
                    <div class="article-social">
                        <?php echo  mtx_get_post_like($class = 'action'); ?>
                    </div>
                    <div class="action-share">
                        <?php deel_share(); ?>
                    </div>
                <?php endif;
                ?>
            </article>
        <?php
        endwhile;
        ?>
        <footer class="article-footer">
            <?php
            the_tags('<div class="article-tags"><i class="fa fa-tags"></i>', '', '</div>');
            ?>
        </footer>
        <nav class="article-nav">
            <span class="article-nav-prev"><?php previous_post_link('<i class="fa fa-angle-double-left"></i> %link'); ?></span>
            <span class="article-nav-next"><?php next_post_link('%link  <i class="fa fa-angle-double-right"></i>'); ?></span>
        </nav>

        <?php
        if (_mtx('git_auther_b')) {
        ?>
            <div class="ab-author clr">
                <div class="img"><?php
                                    echo get_avatar(get_the_author_meta('email'), '110');
                                    ?></div>
                <div class="ab-author-info">
                    <div class="words">
                        <div class="wordsname">关于作者：<?php
                                                    the_author_posts_link();
                                                    ?></div>
                        <div class="authorde"><?php
                                                the_author_meta('description');
                                                ?></div>
                        <div class="authorsocials">
                            <span class="socials-icon-wrap"><a class="ab-img ab-home" target="_blank" href="<?php
                                                                                                            the_author_meta('url'); ?>" title="作者主页"><i class="fa fa-home"></i>作者主页</a></span>
                            <?php
                            if (_mtx('git_pay_qr')) {
                                echo '<span class="socials-icon-wrap"><a id="showdiv" class="ab-img ab-donate" target="_blank" href="#donatecoffee"> <i class="fa fa-coffee"></i>赞助作者 </a></span>';
                            }
                            ?>
                            <span class="socials-icon-wrap"><a class="ab-img ab-email" target="_blank" href="mailto:<?php
                                                                                                                    echo get_the_author_meta('user_email'); ?>" title="给我写信"><i class="fa fa-envelope"></i></a></span>
                            <?php
                            if (get_the_author_meta('sina_weibo')) {
                                echo '<span class="socials-icon-wrap"><a class="ab-img ab-sinawb" target="_blank" href="' . get_the_author_meta('sina_weibo') . '" title="微博"><i class="fa fa-weibo"></i></a></span>';
                            }
                            ?>
                            <?php
                            if (get_the_author_meta('twitter')) {
                                echo '<span class="socials-icon-wrap"><a class="ab-img ab-twitter" target="_blank" href="' . get_the_author_meta('twitter') . '" title="Twitter"><i class="fa fa-twitter"></i></a></span>';
                            }
                            ?>
                            <?php
                            if (get_the_author_meta('github')) {
                                echo '<span class="socials-icon-wrap"><a class="ab-img ab-git" target="_blank" href="' . get_the_author_meta('github') . '" title="Git"><i class="fa fa-git"></i></a></span>';
                            }
                            ?>
                            <?php
                            if (get_the_author_meta('baidu')) {
                                echo '<span class="socials-icon-wrap"><a class="ab-img ab-weixin" target="_blank" href="https://tieba.baidu.com/home/main?un=' . get_the_author_meta('baidu') . '&ie=utf-8" id="ab-weixin-a" title="百度贴吧"><i class="fa fa-paw"></i></a></span>';
                            }
                            ?>
                            <?php
                            if (get_the_author_meta('qq')) {
                                echo '<span class="socials-icon-wrap"><a class="ab-img ab-qq" target="_blank" href="tencent://message/?uin=' . get_the_author_meta('qq') . '&Site=&Menu=yes" title="QQ交谈"><i class="fa fa-qq"></i></a></span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
        <div class="related_top">
            <?php
            include('modules/related.php');
            ?>
        </div>
        <?php
        /*  手机端广告 */
        if (mtx_is_mobile()) {
            if (_mtx('Mobiled_adpost_02')) echo '<div id="comment-ad" class="banner-related mobileads">' . _mtx('Mobiled_adpost_02') . '</div>';
        }
        if (_mtx('git_adpost_02')) echo '<div id="comment-ad" class="banner banner-related">' . _mtx('git_adpost_02') . '</div>';
        comments_template('', true);
        if (_mtx('git_adpost_03')) echo '<div class="banner banner-comment">' . _mtx('git_adpost_03') . '</div>';
        ?>
    </div>
</div>
<?php
get_sidebar();
get_footer();
?>