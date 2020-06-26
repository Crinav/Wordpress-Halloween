<?php 

// Ajouter la prise en charge des images mises en avant
add_theme_support( 'post-thumbnails' );

// Ajouter automatiquement le titre du site dans l'en-tête du site
add_theme_support( 'title-tag' );

//Ajouter les supports des menus
add_theme_support( 'menus' );

// add menu
register_nav_menus( array(
    'main' => 'Menu 1',
    'footer' => 'Bas de page',
) );

add_action('admin_post_nopriv_car_form', 'carFormSubmit');
add_action('admin_post_car_form', 'carFormSubmit');

// Surveille $_POST
function check_Forms(){
    //enregistrement pseudo et email
    if(!empty($_POST['email'])){
        global $wpdb;
        $email = $_POST['email'];
        $pseudo = $_POST['pseudo'];
        $resultats = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT email FROM {$wpdb->prefix}members WHERE email = %s",
                $email
            )
        );
        if(!$resultats[0]->email){
            $wpdb->insert(
                $wpdb->prefix.'members',
                array(
                    'pseudo' => $pseudo,
                    'email' => $email,
                ),
                array(
                    '%s',
                    '%s',
                )
            );
            //send mail
            ini_set("smtp_port","1025");
            $to = $email;
            $subject = 'Bienvenue sur Halloween party !';
            $body = '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
            </head>
            <body>
            <div class="container">
            <h1>Profite de cette soirée '.$pseudo.' !!!</h1>
                <p> N\'oublie pas de prendre une photo de ton costume ! et envoies la grâce à ce bouton :</p>
            </div>
            <form action="http://localhost/wordpress/" method="POST">
            <div class="form-group">
                <input type="text" class="form-control w-25 " name="photo" placeholder="http://www.exemple.com/photo" required>
            </div>
            <div class="form-group">
                <input type="hidden"  name="email_photo"  value="'.$email.'" required>
            </div>
            <button type="submit" class="btn btn-warning w-25">Envoyer</button>
            </form>;
            </body>
            </html> 
            
            ';
            $headers = array("Content-Type: text/html; charset=UTF-8");
            wp_mail( $to, $subject, $body, $headers );
            wp_redirect('http://localhost/wordpress/questions/');
            exit();
            
        }
        else{
            wp_redirect('http://localhost/wordpress/');
            exit();
        }
    }

    // Question embarassante
    if(!empty($_POST['question_embarrassante'])){
        global $wpdb;
        $question = stripslashes_deep($_POST['question_embarrassante']);
        $resultats = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}members ORDER BY ID DESC LIMIT 1"
            )
        );
        $id_members = $resultats->id;

        $wpdb->insert(
            $wpdb->prefix.'questions',
            array(
                'id_theme' => null,
                'question' => $question,
                'id_author' => $id_members,
            ),
            array(
                '%d',
                '%s',
                '%d',
            )
        );
    }

    //reception de la photo et enregistrement
    if(!empty($_POST['photo'])){
        global $wpdb;
        $photo = stripslashes_deep($_POST['photo']);
        $email = $_POST['email'];
        $wpdb->update($wpdb->prefix . 'member', array('photo' => $photo), array('email' => $email));
    }

    // enregistre en bdd les réponses
    if(!empty($_POST['0'])){
        global $wpdb;
        $resultats = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}members ORDER BY ID DESC LIMIT 1"
            )
        );
        $id_member = $resultats->id;        
        foreach ($_POST as $key => $value) {
            $wpdb->insert(
                $wpdb->prefix.'reponses',
                array(
                    'id_member' => $id_member,
                    'id_question' => $_POST["id_question".$key],
                    'reponse' => $_POST[$key],
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                )
            );
        }
    }
}

add_action('init', 'check_Forms');


// titre du site dans l'onglet de navigation
function title_tag($title){
    $title['tagline'] = ' Lucile & Christophe';
    return $title;
}
add_filter('document_title_parts', 'title_tag');
function title_sep(){
    return '|';
}
add_filter('document_title_separator', 'title_sep');



// déclaration des scripts CSS et Jquery
function halloween_register_assets() {
    
    // Déclarer jQuery
    wp_enqueue_script( 
        'jquery', 
        'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', 
        false, 
        '3.3.1', 
        true 
    );
    
    // Déclarer le JS
	wp_enqueue_script( 
        'halloween', 
        get_template_directory_uri() . '/js/script.js', 
        array( 'jquery' ), 
        '1.0', 
        true
    );

    // Envoyer une variable de PHP à JS proprement
    wp_localize_script( 'capitaine', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
    
    // Déclarer style.css à la racine du thème
    wp_enqueue_style( 
        'halloween',
        get_stylesheet_uri(), 
        array(), 
        '1.0'
    );
  	
    // Déclarer un autre fichier CSS
    wp_enqueue_style( 
        'halloween', 
        get_template_directory_uri() . '/style.css',
        array(), 
        '1.0'
    );
}
add_action( 'wp_enqueue_scripts', 'halloween_register_assets' );



require_once 'widgets/AccueilWidget.php';
register_widget(AccueilWidget::class); 
// déclarer une sidebar
function halloween_register_sidebar(){
   
    register_sidebar( array(
        'id' => 'questionnaire-sidebar',
        'name' => 'Questionnaire sidebar',
        'before_widget'  => '<div class="sitesidebarwidget %2$s" id="%1$s>',
        'after_widget'  => '</div>',
        'before_title' => '<p class="sitesidebarwidget__title">',
        'after_title' => '</p>',
    ) );
}
add_action('widgets_init', 'halloween_register_sidebar');


require_once 'widgets/LoginWidget.php';
register_widget(LoginWidget::class); 
// déclarer une sidebar
function halloween_register_sidebar_login(){
   
    register_sidebar( array(
        'id' => 'login-sidebar',
        'name' => 'Login sidebar',
        'before_widget'  => '<div class=" %2$s" id="%1$s>',
        'after_widget'  => '</div>',
        'before_title' => '<p class="sitesidebarwidget__title">',
        'after_title' => '</p>',
    ) );
}
add_action('widgets_init', 'halloween_register_sidebar_login');

require_once 'widgets/PoseQuestionWidget.php';
register_widget(QuestionWidget::class);
// déclarer une sidebar Pose question
function halloween_register_sidebar_pose_question(){

    register_sidebar( array(
        'id' => 'question-sidebar',
        'name' => 'Pose question sidebar',
        'before_widget'  => '<div class="sitesidebarwidget %2$s" id="%1$s>',
        'after_widget'  => '</div>',
        'before_title' => '<p class="sitesidebarwidget__title">',
        'after_title' => '</p>',
    ) );
}
add_action('widgets_init', 'halloween_register_sidebar_pose_question');


