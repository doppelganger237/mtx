<style>

.slick .swiper-title{


    position: absolute;
    left: 0;
    bottom: -10px;
    padding: 0 10px;
    font: bold 1.3em/2.8em "Microsoft Yahei";
    color: #fff;
}

</style>

<div class="slick">
    <?php

    $inner = '';

    for ($x = 1; $x <= 6; $x++) {
        if (_mtx('swiper_' . $x . '_image')) {
            $inner .= '<div class="swiper-slide"><a href="' . _mtx('swiper_' . $x . '_url') . '"><img src="' . _mtx('swiper_' . $x . '_image') . '" alt="' . _mtx('swiper_' . $x . '_title') . '"><span class="swiper-title">'. _mtx('swiper_' . $x . '_title').'</span></a></div>';
        }
    }

    echo '<div id="' . 'slick' . '" class="swiper-container">
    <div class="swiper-wrapper">' . $inner . '</div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next swiper-button-white"><i class="fa fa-chevron-right"></i></div>
    <div class="swiper-button-prev swiper-button-white"><i class="fa fa-chevron-left"></i></div>
</div>';

    ?>


</div>