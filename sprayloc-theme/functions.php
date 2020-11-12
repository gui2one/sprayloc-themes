<?php
/**
 * @package sprayloc
 */

// ini_set("xdebug.var_display_max_children", '-1');
// ini_set("xdebug.var_display_max_data", '-1');
ini_set("xdebug.var_display_max_depth", '-1');

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    // wp_enqueue_style( 'child-style', get_template_directory_uri(  ). '/style.css');
    wp_enqueue_style( 'bootstrap_css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css');
    wp_enqueue_style( 'sprayloc_scss_style', get_stylesheet_directory_uri(  ). '/scss/sprayloc_style.css');


    wp_enqueue_script( 'bootstrap_js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), 1.0, true );
}

function add_additional_class_on_li($classes, $item, $args) {
    $new_classes = array();
    if(isset($args->add_li_class)) {
        $new_classes[] = $args->add_li_class;
    }
    return $new_classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);


function add_menuclass($ulclass) {
   return preg_replace('/<a /', '<a class="nav-link"', $ulclass);
}
add_filter('wp_nav_menu','add_menuclass');


function wp_get_menu_array($current_menu) {
 
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

function custom_menu(){


    $items = wp_get_menu_array('header-menu');
    // var_dump($items);
    $str = '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">';
        $str .= '  <a class="navbar-brand" href="#">SprayLoc</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">';
    $str .= '<ul class="navbar-nav ml-auto" >';

    foreach($items as $value){
        if(empty($value['children'] ))
        {
            
            $str .= "
            <li class='nav-item active'>
            <a class='nav-link'  href='".$value['url']."'>".$value['title']."
            </a></li>";
        }
        else{
            // var_dump($value['children'] );
            $str .= '
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                '.$value['title'].'
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">';
            $str .= '<a class="dropdown-item" href="'.array_values($value['children'])[0]['url'].'">'.array_values($value['children'])[0]['title']."";
            $str .= '</a>';
            $str .= '</div>';
            $str .= '</li>';
        }
    }

    $str .= '</ul></nav>';
    return $str;
}