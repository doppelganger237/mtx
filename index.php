<?php get_header(); ?>
<div class="content-wrap">
    <div class="content">
        <?php
        if ($paged && $paged > 1) {
            //在下一页显示当前的页数
            printf('<header class="archive-header"><h1>最新发布 第' . $paged . '页</h1><div class="archive-header-info"><p>' . get_option('blogname') . get_option('blogdescription') . '</p></div></header>');
        } else {
            // 如果开启幻灯片则include
            if (_mtx('swiper_enable')) {
                include 'modules/slick.php';
                if(_mtx('adindex_02'))
                echo '<div class="banner banner-sticky">' . _mtx('adindex_02') . '</div>';
            }
        }

        // 手机端广告
        if (mtx_is_mobile() && _mtx('Mobiled_adindex_02')) {
            echo '<div class="banner-sticky mobileads">' . _mtx('Mobiled_adindex_02') . '</div>';
        }
        if (_mtx('git_cms_b')) {
            include 'modules/cms.php';
        } else {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            if (_mtx('orderbygx')) {
                $orderby = 'modified';
            } else {
                $orderby = 'date';
            }
            $args = array(
                'ignore_sticky_posts' => 1,
                'paged' => $paged,
                'orderby' => $orderby
            );
            query_posts($args);
            if (_mtx('git_card_b')) {
                include 'modules/card.php';
            } else {
                include 'modules/excerpt.php';
            }
        }
        ?>
    </div>
</div>
<?php get_sidebar();
get_footer(); ?>