<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width" />
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>

        <div id="wrapper" class="hfeed">
            <!-- <header id="header" role="banner"> -->
            <!-- <div id="branding">
                <div id="site-title">
                    <?php if (is_front_page() || is_home() || is_front_page() && is_home()) {
                        echo '<h1>';
                    } ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_html(get_bloginfo('name')); ?>" rel="home"><?php echo esc_html(get_bloginfo('name')); ?></a>
                    <?php if (is_front_page() || is_home() || is_front_page() && is_home()) {
                        echo '</h1>';
                    } ?>
                </div>
                <div id="site-description"><?php bloginfo('description'); ?></div>
            </div> -->


<?php  echo custom_menu(); ?>
  <!-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">SprayLoc</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
<?php wp_nav_menu(array( 
      'theme_location' => 'main-menu',
      'menu_class' => 'navbar-nav ml-auto',
      'container' => false,
      'add_li_class' =>  'nav-item')); ?> -->
    <!-- <ul class="navbar-nav ml-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Dropdown
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Contact</a>
      </li>
    </ul> -->

  </div>
</nav>            
            <!-- </header> -->

            <div id="container">