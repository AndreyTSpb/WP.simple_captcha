<?php
/*
Plugin Name: Простая капча
Description: Простая капча для валидации форм
Plugin Url:
Author: Андрей
Version: 1.0
License: GPL2
 */
/*
    1)удалить поле для сайта
*/

//add_filter('comment_form_default_fields', 'wert9_captcha'); // капча в блоке полей с емеил и именем
add_filter('comment_form_field_comment', 'wert9_captcha_comment');//капча под полем комментарий
add_filter( 'preprocess_comment', 'wert9_filter_captcha' );

//modification fields in form
function wert9_captcha($fields){
    unset($fields['url']); // del input with url 
    unset($fields['cookies']); // del checkbox for cookies
    
    //add captcha
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