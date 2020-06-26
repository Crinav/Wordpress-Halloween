<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@700&display=swap" rel="stylesheet">  
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<header class="header">
    <a class="a_header" href="<?php echo home_url( '/' ); ?>">
      <img src="<?php echo get_template_directory_uri(); ?>/img/blood3.png" width="250px" alt="Logo">
      <h3 class="titre_header_h3">Halloween party</h3>
      <p>by The Partner</p>
    </a>
    <?php 
        wp_nav_menu( 
            array( 
                'theme_location' => 'main', 
                'container' => 'ul', // afin d'éviter d'avoir une div autour 
                'menu_class' => 'site__header__menu', // ma classe personnalisée 
            ) 
        );
    ?>
</header>
    
    <?php wp_body_open(); ?>