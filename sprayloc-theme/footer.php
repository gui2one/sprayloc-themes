<?php

    function make_footer_menu()
    {
        $items = wp_get_menu_array('header-menu');
    
        $str = '<ul class="footer-menu">';
        foreach ($items as $item) {
            $str .= "<li>";
            $str .= "<a href=".$item['url'].">";
            $str .= $item["title"];
            $str .= "</a>";
            $str .= "</li>";
        }
        
        $str .= "</ul>";
        return $str;
    }


?>


</div>
<footer id="footer" role="contentinfo">
    <div class="footer-content">
        <?php echo make_footer_menu(); ?>
        <div class="contact-infos">

            <div>contact infos .....</div>
            <div>contact infos .....</div>
        </div>
    </div>
    <div id="copyright">
        &copy; <?php echo esc_html(date_i18n(__('Y', 'sprayloc-theme'))); ?> <?php echo esc_html(get_bloginfo('name')); ?>
    </div>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>