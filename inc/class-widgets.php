<?php
namespace HomeViet;

class Widgets {

	public static function register_sidebars() {
		register_sidebar([
			'name'          => 'Footer 1',
			'id'            => 'footer-1',
			'description'   => __( 'Add widgets here to appear in your footer.' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		]);

		register_sidebar([
			'name'          => 'Footer 2',
			'id'            => 'footer-2',
			'description'   => __( 'Add widgets here to appear in your footer.' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		]);

		register_sidebar([
			'name'          => 'Footer 3',
			'id'            => 'footer-3',
			'description'   => __( 'Add widgets here to appear in your footer.' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		]);

	}

}
