<?php
/**
 * @package sprayloc
 */
require_once("inc/logo.php");

// ini_set("xdebug.var_display_max_children", '-1');
// ini_set("xdebug.var_display_max_data", '-1');
ini_set("xdebug.var_display_max_depth", '-1');

add_action('after_setup_theme', 'generic_setup');
function generic_setup()
{
    load_theme_textdomain('generic', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array( 'search-form' ));
    global $content_width;
    if (! isset($content_width)) {
        $content_width = 1920;
    }
    register_nav_menus(array( 'header-menu' => esc_html__('Header Menu', 'sprayloc-theme') ));
    register_nav_menus(array( 'footer-menu' => esc_html__('Footer Menu', 'sprayloc-theme') ));
}

add_filter('nav_menu_css_class', 'special_nav_class', 10, 2);

function special_nav_class($classes, $item)
{
    if (in_array('current-menu-item', $classes)) {
        $classes[] = 'active ';
    }
    return $classes;
}


add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
function my_theme_enqueue_styles()
{
    // wp_enqueue_style( 'child-style', get_template_directory_uri(  ). '/style.css');
    wp_enqueue_style('bootstrap_css', get_stylesheet_directory_uri(). '/css/bootstrap.min.css');
    wp_enqueue_style('lightbox_css', get_stylesheet_directory_uri(). '/css/lightbox.min.css');
    wp_enqueue_style('sprayloc_scss_style', get_stylesheet_directory_uri(). '/scss/sprayloc_style.css');


    wp_enqueue_script('bootstrap_js', get_stylesheet_directory_uri(). '/js/bootstrap.min.js', array('jquery'), 1.0, true);
    wp_enqueue_script('lightbox_js', get_stylesheet_directory_uri(). '/js/lightbox.min.js', array('jquery'), 1.0, true);
    wp_enqueue_script('gsap_js', get_stylesheet_directory_uri(). '/js/gsap.min.js', array('jquery'), 1.0, true);

    wp_enqueue_script('vue_js', 'https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js', 9999);
    wp_enqueue_script('vue_js_router', 'https://unpkg.com/vue-router@2.0.0/dist/vue-router.js', 9999);
    wp_enqueue_script('fontawsome_js', 'https://use.fontawesome.com/releases/v5.0.1/js/all.js', 9999);

    wp_enqueue_script('gui2one_js', get_stylesheet_directory_uri(). '/scripts.js', 9999);
}

// function add_additional_class_on_li($classes, $item, $args)
// {
//     $new_classes = array();
//     if (isset($args->add_li_class)) {
//         $new_classes[] = $args->add_li_class;
//     }
//     return $new_classes;
// }
// add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);


// function add_menuclass($ulclass)
// {
//     return preg_replace('/<a /', '<a class="nav-link"', $ulclass);
// }
// add_filter('wp_nav_menu', 'add_menuclass');


function wp_get_menu_array($current_menu)
{
    $array_menu = wp_get_nav_menu_items($current_menu);
    // var_dump($array_menu);
    $menu = array();
    foreach ((array)$array_menu as $m) {
        if (empty($m->menu_item_parent)) {
            $menu[$m->ID] = array();
            $menu[$m->ID]['ID']      =   $m->ID;
            $menu[$m->ID]['title']       =   $m->title;
            $menu[$m->ID]['url']         =   $m->url;
            $menu[$m->ID]['children']    =   array();
        }
    }
    $submenu = array();
    foreach ((array)$array_menu as $m) {
        if ($m->menu_item_parent) {
            $submenu[$m->ID] = array();
            $submenu[$m->ID]['ID']       =   $m->ID;
            $submenu[$m->ID]['title']    =   $m->title;
            $submenu[$m->ID]['url']  =   $m->url;
            $menu[$m->menu_item_parent]['children'][$m->ID] = $submenu[$m->ID];
        }
    }
    return $menu;
}



function custom_menu_V2()
{
    $items = wp_get_menu_array('header-menu');
    // var_dump($items);
    $str = '<div id="main-nav" class="navbar navbar-dark bg-dark ">';
    // $str .= createLogo();
    $str .= '<a href='.site_url().'>';
    $str .= '<div class="navbar-brand">'.createLogo().'<span class="logo-bold">Spray</span><span class="logo-thin">Loc</span></div>';
    $str .= '</a>';
    $str .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>';
    $str .= '<ul class="bg-dark">';

    $current_url = "".$_SERVER["REQUEST_SCHEME"]."://".$_SERVER['HTTP_HOST']."".$_SERVER["REQUEST_URI"];
    foreach ($items as $value) {
        if (empty($value['children'])) {
            $is_active_link = $value["url"] == $current_url ? "active" : "";
            
            
            
            $str .= "
            <li id='menu-item-".$value["title"]."'>
            <a class='menu-item ".$is_active_link."'  href='".$value['url']."'>".$value['title']."
            </a></li>";
        } else {
            // var_dump($value['children'] );
            $str .= '
            <li class="nav-item navbar-dark bg-dark dropdown">
                <a class="menu-item dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                '.$value['title'].'
                </a>
                <div class="dropdown-menu navbar-dark bg-dark" aria-labelledby="navbarDropdown">';

            foreach (array_values($value['children']) as $child) {
                $str .= '<a class="dropdown-item navbar-dark bg-dark" href="'.$child['url'].'">'.$child['title']."";
                $str .= '</a>';
            }

            
            $str .= '</div>';

            $str .= '</li>';
        }
    }

    $str .= '</ul></div>';

    $str .= '<div id="sidebar-nav" class="hide navbar navbar-dark bg-dark">';
    $str .= '<ul>';

    foreach ($items as $value) {
        if (empty($value['children'])) {
            $str .= "
            <li>
            <a class='nav-link'  href='".$value['url']."'>".$value['title']."
            </a></li>";
        } else {
            // var_dump($value['children'] );
            $str .= '
            <li class="nav-item dropdown navbar-dark bg-dark">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                '.$value['title'].'
                </a>
                <div class="dropdown-menu navbar-dark bg-dark" aria-labelledby="navbarDropdown">';

            foreach (array_values($value['children']) as $child) {
                $str .= '<a class="dropdown-item navbar-dark bg-dark" href="'.$child['url'].'">'.$child['title']."";
                $str .= '</a>';
            }


            $str .= '</div></li>';
        }
    }

    $str .= '</ul>';
    $str .= '</div>';
    
    return $str;
}



// add_filter( 'cron_schedules', 'example_add_cron_interval' );

// function example_add_cron_interval( $schedules ) {
//  $schedules['two_minutes'] = array(
//  'interval' => 120,
//  'display' => esc_html__( 'Every 2 minutes' ),
//  );

// return $schedules;
//  }

// add_action( 'my_hookname', 'cron_function' );

// function cron_function(){
//     $url = "http://sprayloc-dev.com/api_test/get_files.php";
//     $content = file_get_contents($url);
//     echo $content;
// }
