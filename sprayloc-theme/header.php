<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>

        <div class="btn-page-top">  
        <i class="fas fa-chevron-up"></i>  
        <span class="text">Haut de Page</span>
        </div>

        <div id="main-wrapper" >




          <?php  echo custom_menu_V2(); ?>
          

            <!-- </div> -->
          </nav>            


          <div id="sprayloc-container">

          <?php
          
            $api_key = get_option("sprayloc_plugin_admin_option_name")["rentman_api_key"];
          
          
          ?>