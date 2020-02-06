<?php
/*
Plugin Name: Простая капча
Description: Простая капча для валидации форм
Plugin Url:
Author: Андрей
Version: 1.0
License: GPL2
 */
add_action('init', 'start_session', 1);
add_action('wp_enqueue_scripts','wert9_wp_register_styles_scripts_captcha');  // connect CSS and JS
add_filter('comment_form_default_fields', 'wert9_captcha'); // капча в блоке полей с емеил и именем
//add_filter('comment_form_field_comment', 'wert9_captcha_comment');//капча под полем комментарий
add_filter( 'preprocess_comment', 'wert9_filter_captcha' );

function start_session() {
    if(!session_id()) {
        session_start();
    }
}

function wert9_wp_register_styles_scripts_captcha(){
    //Registred CSS
    wp_register_style('wert9-captcha-css', plugins_url('css/wert9_style.css', __FILE__));
    wp_enqueue_style('wert9-captcha-css');
}


//modification fields in form
function wert9_captcha($fields){
    unset($fields['url']); // del input with url 
    unset($fields['cookies']); // del checkbox for cookies
    
    //add captcha
    $a = rand(1,10);
    $b = rand(1,10);
    // начинаем сессию
    $_SESSION['summ'] = $a + $b;

    $img1 = plugins_url('/tp/img/'.$a.'.png', __FILE__);
    $img2 = plugins_url('/tp/img/'.$b.'.png', __FILE__);
    ob_start();
    include dirname(__FILE__)."/tp/captcha.php"; // file with template HTML captcha
    $fields['captcha'] = ob_get_contents();
    ob_end_clean();

    return $fields;
}

function wert9_filter_captcha($commentdata){

    if(is_user_logged_in()) return; // если пользователь авторизован капчу не проверяем

    if(!isset($_POST['captcha'])){
        $message = '<p>Капча не отмечена</p><p><a href="javascript:history.back()">← Назад</a></p>';
        $title = "Ошибка";
        $args ="";
        wp_die($message, $title, $args);
    }elseif($_POST['captcha'] != $_SESSION['summ']){
        $message = '<p>Капча не '.$_SESSION['summ'].' отмечена</p><p><a href="javascript:history.back()">← Назад</a></p>';
        $title = "Ошибка";
        $args ="";
        wp_die($message, $title, $args);
    }
    return $commentdata;

}

function wert9_captcha_comment($comment_field){
    if(is_user_logged_in()) return $comment_field;

    //add captcha
    ob_start();
    include dirname(__FILE__)."/tp/captcha.php"; // file with template HTML captcha
    $comment_field .= ob_get_contents();
    ob_end_clean();

    return $comment_field;
}