<?php
/**
 * @package sprayloc
 */

defined('ABSPATH') or die('No direct access!');

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
    // wp_enqueue_script('create_thumbnails_js', get_stylesheet_directory_uri(). '/js/create_thumbnails.js', 9999);
}



function wp_get_menu_array($current_menu)
{
    $array_menu = wp_get_nav_menu_items($current_menu);
    // var_dump($array_menu);
    $menu = array();
    foreach ((array)$array_menu as $m) {
        if (empty($m->menu_item_parent)) {
            if ($m->url == '/') {
                $url = home_url('/');
            // var_dump($url);
                // var_dump(get_page_link());
                // var_dump(site_url());
            } else {
                $url = $m->url;
            }
            $menu[$m->ID] = array();
            $menu[$m->ID]['ID']      =   $m->ID;
            $menu[$m->ID]['title']       =   $m->title;
            $menu[$m->ID]['url']         =   $url;
            $menu[$m->ID]['active']      =   get_page_link() == $url;
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

/// AJAX STUFF

function sprayloc_frontend_scripts()
{
    wp_enqueue_script('create_thumbnails_js', get_stylesheet_directory_uri(). '/js/create_thumbnails.js', ['jquery'], time(), true);


    // Change the value of 'ajax_url' to admin_url( 'admin-ajax.php' )
    // Change the value of 'total_likes' to get_option( 'sprayloc_likes' )
    // Change the value of 'nonce' to wp_create_nonce( 'sprayloc_likes_nonce' )
    wp_localize_script(
        'create_thumbnails_js',
        'sprayloc_globals',
        [
      'ajax_url'    => admin_url('admin-ajax.php'),
      'nonce'       => wp_create_nonce('sprayloc_likes_nonce')
    ]
    );
}
add_action('wp_enqueue_scripts', 'sprayloc_frontend_scripts');


// function sprayloc_add_like()
// {

//   // Change the parameter of check_ajax_referer() to 'sprayloc_likes_nonce'
//     check_ajax_referer('sprayloc_likes_nonce');

//     $likes = intval(get_option('sprayloc_likes'));
//     $new_likes = $likes + 1;
//     $success = update_option('sprayloc_likes', $new_likes);

//     if (true == $success) {
//         $response['total_likes'] = $new_likes;
//         $response['type'] = 'success';
//     }

//     $response = json_encode($response);
//     echo $response;
//     die();
// }
// // // Change 'wp_ajax_your_hook' to 'wp_ajax_sprayloc_add_like'
// // // Or change to 'wp_ajax_nopriv_your_hook' to 'wp_ajax_nopriv_sprayloc_add_like'
// // // Change 'your_hook' to 'sprayloc_add_like'
// add_action('wp_ajax_sprayloc_add_like', 'sprayloc_add_like');
// add_action('wp_ajax_nopriv_sprayloc_add_like', 'sprayloc_add_like');

function sprayloc_thumbnails_update()
{
    check_ajax_referer('sprayloc_likes_nonce');

    $response['total_likes'] = 42;
    $response['type'] = 'success';

    // require(get_stylesheet_directory_uri(). '/inc/create_thumbnails.php');

    $response = json_encode($response);
    echo $response;

    die();
}

add_action('wp_ajax_sprayloc_thumbnails_update', 'sprayloc_thumbnails_update');
add_action('wp_ajax_nopriv_sprayloc_thumbnails_update', 'sprayloc_thumbnails_update');
