<?php

class SLAB_Favorites_Widget extends WP_Widget {

	//settings for widget
	public function __construct() {
		$args = [
			"name" => "Избранное",
			"description" => "Выводит список избранных записей пользователя"
		];
		parent::__construct("slab-favorites-widget","", $args);
	}

	//the form of widget in admin panel
	public function form($instance) {
		extract($instance);
		$title = !empty($instance["title"]) ? esc_attr__($instance["title"]) : 'Избранные записи';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title')?>">Заголовок: </label>
			<input type="text" id="<?php echo $this->get_field_id('title')?>"
			       class="widefat" name="<?php echo $this->get_field_name('title')?>" value="<?php echo $title ?>">

		</p>

<?php
	}

	//the form widget in user panel
	public function widget($args, $instance) {
		if(!is_user_logged_in()) return;
		extract($instance);
		$title = !empty($instance["title"]) ? esc_attr__($instance["title"]) : 'Избранные записи';
		echo $args['before_widget'];
			echo $args['before_widget'];
			?>
			<h3 class="widget-title font-headlines"><span class="wrap"><?php echo $title ?></span></h3>
<?php
					echo $args['after_widget'];
						slab_favorites_show_dashboard_widget();
					echo $args['after_widget'];
	}

	//
	/*public function update() {

	}*/
}