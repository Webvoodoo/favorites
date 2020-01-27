<?php
//make some changes for content
function slab_favorites($content) {
    if(!is_user_logged_in() || !is_single()) return $content; //this function return true if the user logged

    $img = plugins_url("/img/loader.gif", __FILE__);

	global $post;
    if(slab_is_favorite($post->ID)) {
	    return "<p class='slab-favorite-link'><span class='slab-favorite-hidden'>
            <img width='25px' height='25px' src='" . $img . "' ></span>
            <a href='#' data-action='del'>Удалить из избранного</a></p>" . $content;
    }
    return "<p class='slab-favorite-link'><span class='slab-favorite-hidden'>
            <img width='25px' height='25px' src='" . $img . "' ></span>
            <a href='#' data-action='add'>В избранное</a></p>" . $content;
}
//connect js and css files for user settings
function slab_favorites_js_and_css_connect() {
    if(!is_user_logged_in()) return; //if user logout then not connect our scripts

/*1 - name os file, 2 - path, 3 - array scripts which depends of our script
  4 - version, 5 - where to connect script true - footer, default false - head */
    wp_enqueue_script("slab_favorites_script", plugins_url("/js/slab_favorites_script.js", __FILE__), array('jquery'), null, true);
    wp_enqueue_style("slab_favorites_style", plugins_url("/css/slab_favorites_style.css", __FILE__));


    global $post; // get info about posts

    wp_localize_script("slab_favorites_script", "slabFavorites",
                            ["url" => admin_url("admin-ajax.php"),
                            "nonce" => wp_create_nonce("slab-favorites"),
                            "postId" => $post->ID]); // url - file for ajax
    // wp_create_nonce - create unique private key for defence query
}
//add post to favorite
function wp_ajax_slab_send() {
	$img = plugins_url("/img/loader.gif", __FILE__);
    //check key protection: 1 - post value, 2 - name of object
    if(!wp_verify_nonce($_POST["security"], "slab-favorites")) wp_die('Ошибка безопастности');

    $postId = (int)$_POST["postId"]; // id of post
	$user = wp_get_current_user(); // get data about current user

    if($_POST["what_do"] == "add"){
	    if(slab_is_favorite($postId)) wp_die("Уже в избранном");

	    if(add_user_meta($user->ID, "slabFavorites", $postId)) { //adding new meta for current user by key slabFavorites
		    $res = "<li class='cat-item cat-item-" . $postId . "'><a href='" . get_permalink($postId) . "' target='_blank'>" . get_the_title($postId) . "</a>
              <span><a class='slab-favorites-del' data-action='del' href='#' data-post='" . $postId . "'>&#10008;</a></span>
              <span class='slab-favorite-hidden'><img style='display:none' width='25px' height='25px' src='" . $img . "'></span>
              </li>";
	    	wp_die($res);
	    }
	    wp_die("Ошибка при добавлении!");
    }
    if($_POST["what_do"] == "del") {
        if(delete_user_meta($user->ID, "slabFavorites", $postId)) { //delete meta by key slabFavorites
        	wp_die("Удалено");
        }
	    wp_die("Ошибка при удалении!");
    }
}
//check was the post added
function slab_is_favorite($postId){
    $user = wp_get_current_user();
    $favorites = get_user_meta($user->ID, "slabFavorites"); //search all post in favorite by key slabFavorites for current user
    foreach($favorites as $favorite) {
        if($favorite == $postId) return true;
    }
    return false;
}




//add dashboard widget on main page
function slab_favorites_dashboard_widget() {
	wp_add_dashboard_widget('slab_favorites_dashboard', 'Избранные записи', 'slab_favorites_show_dashboard_widget');
}

function slab_favorites_show_dashboard_widget(){
	$img = plugins_url("/img/loader.gif", __FILE__);
	$user = wp_get_current_user();
	$favorites = get_user_meta($user->ID, "slabFavorites"); //search all post in favorite by key slabFavorites for current user
	$favorites = array_reverse($favorites); //sort posts
	if(!$favorites) {
		echo "<ul></ul>";
		return;
	}
	echo "<ul>";
	foreach($favorites as $favorite) {
		echo "<li class='cat-item cat-item-" . $favorite . "'><a href='" . get_permalink( $favorite ) . "' target='_blank'>" . get_the_title( $favorite ) . "</a>
              <span><a class='slab-favorites-del' data-action='del' href='#' data-post='" . $favorite . "'>&#10008;</a></span>
              <span class='slab-favorite-hidden'><img width='25px' height='25px' src='" . $img . "'></span>
              </li>";
	}
		echo "</ul>";
		echo "<div class='slab-favorite-del-all'>
              <button id='slab-favorite-del-all' class='button button-primary'>Удалить всё</button>
              <span class='slab-favorite-hidden'><img width='25px' height='25px' src='". $img . "'></span>
              </div>";

}
//connect js and css files for admin settings
function slab_favorites_admin_js_and_css_connect($hook) {
	if($hook != "index.php") return; //if it`s not main page
	wp_enqueue_script("slab_favorites_script_admin", plugins_url("/js/slab_favorites_script_admin.js", __FILE__), array('jquery'), null, true);
	wp_enqueue_style("slab_favorites_style_admin", plugins_url("/css/slab_favorites_style_admin.css", __FILE__));
	wp_localize_script("slab_favorites_script_admin", "slabFavorites", ['nonce' => wp_create_nonce('slabFavorites')]);
}
//delete one post from widget
function wp_ajax_slab_del() {
	if(!wp_verify_nonce($_POST['security'], 'slabFavorites')){
		wp_die('Ошибка безопастности');
	}
	$postId = (int)$_POST['postId'];
	$user = wp_get_current_user();
	if (!slab_is_favorite($postId)) wp_die("Уже в избранном");
	if (delete_user_meta($user->ID, 'slabFavorites', $postId)) { //delete post for current user
		wp_die('Удалено');
	}
	wp_die('Ошибка удаления');
}
//delete all posts from dashboard widget
function wp_ajax_slab_del_all(){
	if(!wp_verify_nonce($_POST['security'], 'slabFavorites')){
		wp_die('Ошибка безопастности');
	}
	$user = wp_get_current_user();
	if(delete_metadata('user', $user->ID, 'slabFavorites')) { //delete all post for current user
		wp_die('Записи удалены');
	} else wp_die('Ошибка удаления');
}
function wp_ajax_slab_del_all_from_widget(){
	/*if(!wp_verify_nonce($_POST['security'], 'slabFavorites')){
		wp_die('Ошибка безопастности');
	}*/
	$user = wp_get_current_user();
	if(delete_metadata('user', $user->ID, 'slabFavorites')) { //delete all post for current user
		wp_die('Записи удалены');
	} else wp_die('Ошибка удаления');
}