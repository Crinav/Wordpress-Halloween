<?php
/*
Plugin Name: halloween_quest_plugin
Plugin URI: http://lulu&chris.com
Description: crée une série de questions réponses pour un quizz
Author: Chris
Version: 0.1
*/

//se déclenche à l'activation du plugin
register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'questions';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
        id_theme mediumint(9) ,
		question varchar(150) NOT NULL,
		id_author mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
		);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $table_name2 = $wpdb->prefix . 'theme';
    $sql2 = "CREATE TABLE IF NOT EXISTS $table_name2 (
		id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
        title varchar(50) NOT NULL,
		nb_question mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
		);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql2);

    $table_name3 = $wpdb->prefix . 'reponses';
    $sql3 = "CREATE TABLE IF NOT EXISTS $table_name3 (
        id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
        id_member mediumint(9) NOT NULL,
        id_question mediumint(9) NOT NULL,
        reponse varchar(150) NOT NULL,
        PRIMARY KEY  (id)
        );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql3);
});


//se declenche à la désactivation du plugin
register_deactivation_hook(__FILE__, function () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'questions';
    $wpdb->query("DROP TABLE IF EXISTS " . $table_name);
    $table_name2 = $wpdb->prefix . 'theme';
    $wpdb->query("DROP TABLE IF EXISTS " . $table_name2);
    $table_name3 = $wpdb->prefix . 'reponses';
    $wpdb->query("DROP TABLE IF EXISTS " . $table_name3);
});

//Surveille $_POST
function check_Post()
{
    global $wpdb;
    // Enregistre le theme
    if (!empty($_POST['title'])) {
        $title = $_POST['title'];
        $wpdb->insert(
            $wpdb->prefix . 'theme',
            array(
                'title' => $title,
                'nb_question' => 5,
            ),
            array(
                '%s',
                '%d',
            )
        );
    }
    // Enregistre les 5 questions
    if (!empty($_POST['question1'])) {
        $resultats = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}theme ORDER BY ID DESC LIMIT 1"
            )
        );
        $id_theme = $resultats->id;



        foreach ($_POST as $value) {
            if ($value !== '') {
                $wpdb->insert(
                    $wpdb->prefix . 'questions',
                    array(
                        'id_theme' => $id_theme,
                        'question' => $value,
                        'id_author' => 0,
                    ),
                    array(
                        '%d',
                        '%s',
                        '%d',
                    )
                );
            }
        }
    }
    // Enregistre une nouvelle question dans un theme
    if (!empty($_POST['question_sup'])) {
        $question = $_POST['question_sup'];
        $id = $_POST['id_theme'];
        // check si le theme existe bien
        $resultats = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, nb_question FROM {$wpdb->prefix}theme WHERE id = {$id}"
            )
        );
        if ($resultats[0]->id) {
            //enregistre la question
            $wpdb->insert(
                $wpdb->prefix . 'questions',
                array(
                    'question' => $question,
                    'id_theme' => $id,
                    'id_author' => 0,
                ),
                array(
                    '%s',
                    '%d',
                    '%d',
                )
            );
            //met a jour le nb de questions
            $wpdb->update($wpdb->prefix . 'theme', array('nb_question' => $resultats[0]->nb_question + 1), array('id' => $id));
        }
    }

    if (!empty($_POST['add_id_quest'])) {
        $id_question = $_POST['add_id_quest'];
        $id_theme = $_POST['add_id_theme'];
        // check si le theme existe bien
        $resultats = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, nb_question FROM {$wpdb->prefix}theme WHERE id = {$id_theme}"
            )
        );
        if ($resultats[0]->id) {
        
            //met a jour le nb de questions
            $wpdb->update($wpdb->prefix . 'theme', array('nb_question' => $resultats[0]->nb_question + 1), array('id' => $id_theme));
            $wpdb->update($wpdb->prefix . 'questions', array('id_theme' => $id_theme ), array('id' => $id_question));

        }
    }

    //enregistrement pseudo et email PAR ADMIN
    if(!empty($_POST['admin_email'])){
        $email = $_POST['admin_email'];
        $pseudo = $_POST['admin_pseudo'];
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
        }
    }

    //update utilisateur par ADMIN
    if(!empty($_POST['admin_up_email'])){
        $email = $_POST['admin_up_email'];
        $pseudo = $_POST['admin_up_pseudo'];
        $photo = $_POST['admin_up_photo'];
        $id = $_POST['id'];
        $wpdb->update($wpdb->prefix . 'members', array('pseudo' => $pseudo, 'email' => $email, 'photo' => $photo), array('id' => $id));
    }

    //delete un utilisateur par ADMIN
    if(!empty($_POST['del_id'])){
        $id = $_POST['del_id'];
        $wpdb->delete(
            $wpdb->prefix.'members',
            array('id' => $id)
        );
        $wpdb->delete('wp_questions', array('id_author' => $id));
        $wpdb->delete(
            $wpdb->prefix.'reponses',
            array('id_member' => $id)
        );
    }
}
add_action('init', 'check_Post');

function theme_options_panel()
{
    add_menu_page('Theme page title', 'Admin', 'manage_options', 'theme-options', 'wps_theme_func');
    add_submenu_page('theme-options', 'Settings page title', 'Créer un questionnaire', 'manage_options', 'questionnaire', 'wps_theme_func_settings');
    add_submenu_page(null, 'FAQ page title', 'FAQ', 'manage_options', 'questions', 'wps_theme_func_faq');
    add_submenu_page('theme-options', 'Settings theme', 'Thèmes/Questions', 'manage_options', 'manage-questions', 'wps_theme_func_manage');
    add_submenu_page('theme-options', 'Settings quest', 'Questions des membres', 'manage_options', 'manage-questions-membre', 'wps_theme_func_manage_quest');
    add_submenu_page('theme-options', 'Settings create', 'Create', 'manage_options', 'manage-create', 'wps_theme_func_create');
    add_submenu_page('theme-options', 'Settings read', 'Read', 'manage_options', 'manage-read', 'wps_theme_func_read');
    add_submenu_page('theme-options', 'Settings update', 'Update', 'manage_options', 'manage-update', 'wps_theme_func_update');
    add_submenu_page('theme-options', 'Settings delete', 'Delete', 'manage_options', 'manage-delete', 'wps_theme_func_delete');

}

add_action('admin_menu', 'theme_options_panel');


function wps_theme_func()
{
    echo '<div class="wrap"><div class="icon32"><br></div></div>';
}

function wps_theme_func_settings()
{
    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
    <h1>Questionnaire</h1>
    <form method="POST" action="?page=questions">
    <table class="form-table" role="presentation">
    <tbody>
    <tr>
    <td>
        <label for="title">Titre du thème</label><br>
        <input type="text" class="regular-text" name="title" >
    </td>
    </tr>
    </tbody>
    </table>
        <button type="submit" class="button button-primary">Valider</button>
    </form>

    </div>';
}
function wps_theme_func_faq()
{
    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
    <h1>Questions</h1></div>
    <div>
    <form method="POST" action="http://localhost/wordpress/wp-admin/admin.php?page=questionnaire">
    <table class="form-table" role="presentation">
    <tbody>
    <tr>
    <td>
        <input type="text" class="regular-text" name="question1" >
    </td>
    </tr>
    <tr>
    <td>
        <input type="text" class="regular-text" name="question2" >
    </td>
    </tr>
    <tr>
    <td>
        <input type="text" class="regular-text" name="question3" >
    </td>
    </tr>
    <tr>
    <td>
        <input type="text" class="regular-text" name="question4" >
    </td>
    </tr>
    <tr>
    <td>
        <input type="text" class="regular-text" name="question5" >
    </td>
    </tr>
    </tbody>
    </table>
        <button type="submit" class="button button-primary" >Valider les questions</button>
    </form>
    </div>';
}

function wps_theme_func_manage()
{
    global $wpdb;
    $theme = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}theme");

    echo ' 
    <div class="container">
        <h1>Thèmes et Questions</h1>
        <table class="wp-list-table widefat fixed striped pages">
	    ';
    foreach ($theme as $th) {
        echo '
            <thead>
                <tr>
                    <th><h3>' . $th->title . ' ID : ' . $th->id . '</h3></th>
                </tr>
            </thead>
            
            <tbody>
            
            ';
        $questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}questions WHERE id_theme ={$th->id}");

        foreach ($questions as $question) {
            echo '
                <tr>
                    <td class="colspanchange" colspan="4">' . $question->question . '</td>
                </tr>';
        }
        echo '
            </tbody>
                
                ';
    }
    echo '<form method="POST" action="">
                <input type="text" class="regular-text" name="question_sup" ><input type="number" name="id_theme" placeholder="id" >
                <button type="submit" class="button button-primary" >Enregistrer une nouvelle question</button>
            </form>
            </table>
            
    </div>';
}

function wps_theme_func_manage_quest()
{
        global $wpdb;
        echo '
    <div class="container">
    <h1>Questions des membres</h1> 
    <table class="wp-list-table widefat fixed striped pages"> 
            <thead>
                <tr>
                    <th class="colspanchange" colspan="3"><h3>Question</h3></th>
                    <th class="colspanchange" colspan="3"><h3>Membre</h3></th>
                </tr>
                
            </thead>
            
            <tbody> 
    ';
        $quest_orph = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}questions WHERE id_theme IS NULL");
        foreach ($quest_orph as $value) {
            $member = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}members WHERE id = $value->id_author");
            echo '
        <tr>
            <td class="colspanchange" colspan="3">' . $value->question . '</td>
            <td class="colspanchange" colspan="3">pseudo : <b>' . $member[0]->pseudo . '</b> / email : <b>' . $member[0]->email . '</b></td>
            <td class="colspanchange" colspan="4">
                <form action="" method="POST">
                    ajouter cette question au thème n° : 
                    <input type="hidden" name="add_id_quest" value="' . $value->id . '">
                    <input type="number" name="add_id_theme" placeholder="id" >
                    <button type="submit" class="button button-primary" >Valider</button>
                </form>
            </td>
        </tr>
        ';
        }
        echo '
            </tbody>
            </table>
            </div>';
    }

    function wps_theme_func_create()
{
    echo ' <div class="container">
    <h1>Créer un utilisateur</h1>
    <form action="" method="POST">  
        <div class="form-group ">
            <input type="text" class="form-control w-75 btn-login" id="pseudo" name="admin_pseudo" placeholder="Pseudo" required>
        </div>
        <div class="form-group">
            <input type="email" class="form-control w-75 btn-login" id="email" name="admin_email"  aria-describedby="emailHelp" placeholder="Entrez votre email" required>
        </div>
        
        <button type="submit" class="btn btn-warning w-25">Entrer</button>
        
        </form>
    </div>';
}

function wps_theme_func_read()
{
    global $wpdb;
    echo ' <div class="container">
    <h1>Tous les utilisateurs</h1>
    <table class="wp-list-table widefat fixed striped pages"> 
            <thead>
                <tr>
                    <th class="colspanchange" colspan="1"><h3>ID</h3></th>
                    <th class="colspanchange" colspan="2"><h3>Pseudo</h3></th>
                    <th class="colspanchange" colspan="3"><h3>Email</h3></th>
                    <th class="colspanchange" colspan="3"><h3>Modifier</h3></th>
                    <th class="colspanchange" colspan="3"><h3>Supprimer</h3></th>
                </tr>
            </thead>
            <tbody> 
    ';
        $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}members");
        foreach ($users as $value) {
            echo '
        <tr>
            <td class="colspanchange" colspan="1">'.$value->id.'</td>
            <td class="colspanchange" colspan="2">' . $value->pseudo .'</td>
            <td class="colspanchange" colspan="3"><b>' . $value->email . '</b></td>
            <td class="colspanchange" colspan="3">
                    <a href="admin.php?page=manage-update&id='.$value->id.'" >Modifier</a>
            </td>
            <td class="colspanchange" colspan="3">
                    <a href="admin.php?page=manage-delete&id='.$value->id.'" >Supprimer</a>
            </td>
        </tr>
        ';
        }
        echo '
            </tbody>
            </table>
    </div>';
}

function wps_theme_func_update()
{
    global $wpdb;
    $id = $_GET['id'];
    $user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}members WHERE id= {$id}");
    echo ' <div class="container">
    <h1>Modifier un utilisateur</h1>
    <form action="admin.php?page=manage-read" method="POST">  
    <input type="hidden" name="id" value="'.$id.'">
        <div class="form-group ">
            <input type="text" class="form-control w-75" id="pseudo" name="admin_up_pseudo" placeholder="'.$user->pseudo.'">
        </div>
        <div class="form-group">
            <input type="email" class="form-control w-75" id="email" name="admin_up_email"  placeholder="'.$user->email.'">
        </div>
        <div class="form-group ">
            <input type="text" class="form-control w-75" id="photo" name="admin_up_photo" placeholder="'.$user->photo.'">
        </div>
        <button type="submit" class="btn btn-warning w-25">Enregistrer</button>
        </form>
    </div>';
}

function wps_theme_func_delete()
{
    global $wpdb;
    $id = $_GET['id'];
    $user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}members WHERE id= {$id}");
    echo '
    <div class="health-check-body hide-if-no-js">
    <div class="site-status-has-issues">
    <h1>Supprimer un utilisateur</h1>
        <form action="admin.php?page=manage-read" method="POST">  
            <input type="hidden" name="del_id" value="'.$id.'">
            <div class="card" style="width:400px">
                <img class="card-img-top" src="'.$user->photo.'" alt="Image indisponible..." style="width:100%">
            <div class="card-body">
            <h4 class="card-title">'.$user->pseudo.'</h4>
            <p class="card-text">Email : '.$user->email.'</p>
            <button type="submit" id="del" class="btn btn-warning w-25">Delete</button>
            </div>
            </div>
        </form>
        </div>
    </div>';
}