<?php
/**
 * Plugin Name: Избранные записи
 * Description: Плагин добавляет записи блога в избранные, чтобы прочитать позже. Данный плагин доступен для авторизированных пользователей.
 * Author:      Webvoodoo
 * Version:     1.0
 *
 */


require __DIR__ . "/functions.php";
require __DIR__ . "/SLAB_Favorites_Widget.php"; // Connect widget class */

add_filter("the_content", "slab_favorites"); //add_filter( $tag, $function_to_add, $priority, $accepted_args );
add_action("wp_enqueue_scripts", "slab_favorites_js_and_css_connect"); //register scripts and styles
add_action("wp_ajax_slab_send", "wp_ajax_slab_send"); // add post to favorite

add_action("admin_enqueue_scripts", "slab_favorites_admin_js_and_css_connect");// connect admin js and css files
add_action("wp_dashboard_setup", "slab_favorites_dashboard_widget"); // add dashboard widget
add_action('wp_ajax_slab_del', 'wp_ajax_slab_del'); //delete one post from dashboard widget
add_action('wp_ajax_slab_del_all', 'wp_ajax_slab_del_all'); //delete all posts from dashboard widget


add_action('widgets_init', 'slab_favorites_widget'); //initialization widget
function slab_favorites_widget(){
	register_widget('SLAB_Favorites_Widget'); //create class file
}

add_action('wp_ajax_slab_del_all_from_widget', 'wp_ajax_slab_del_all_from_widget');

















