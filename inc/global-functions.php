<?php

function debug($var) {
	?>
	<pre><?php print_r($var); ?></pre>
	<?php
}

function debug_log($var) {
	error_log(print_r($var,true));
}

function unyson_exists() {
	return (defined('FW')) ? true : false;
}

function has_role($role, $user=null) {
	if($user==null) $user = wp_get_current_user();
	if($user instanceof WP_User) {
		return in_array($role, (array) $user->roles);
	} else {
		return false;
	}
}

function is_litespeed_cache_active() {
	
	if(!function_exists('is_plugin_active')) require_once(ABSPATH . "wp-admin" . '/includes/plugin.php');

	if(is_plugin_active('litespeed-cache/litespeed-cache.php')) {
		return true;
	}

	return false;
}

function map_term_id($term) {
	return $term->term_id;
}

function get_term_parents($term, $parents=[]) {
	
	if($term->parent>0) {
		$parent = get_term_by( 'term_id', $term->parent, $term->taxonomy );
		$parents[] = $parent;
		$parents = get_term_parents($parent, $parents);
	}

	return $parents;
}

function wp_get_the_content($content) {
	return apply_filters( 'the_content', $content );
}

function wp_format_content($raw_string='') {
	global $wp_embed;
	
	$content = wp_kses_post( $raw_string );

	$content = do_blocks($content);
	$content = wptexturize($content);
	$content = convert_smilies($content);
	$content = convert_chars($content);
	$wp_embed->run_shortcode($content);
	$content = wpautop($content);
	$content = shortcode_unautop($content);
	$content = prepend_attachment($content);
	$content = wp_filter_content_tags($content);
	$content = do_shortcode($content);
	$content = wp_replace_insecure_home_url($content);
	$content = $wp_embed->autoembed($content);

	return $content;
}

function wp_do_shortcode( $tag, array $atts = array(), $content = null ) {
	global $shortcode_tags;

	if ( ! isset( $shortcode_tags[$tag] ) ) {
		return false;
	}

	return call_user_func( $shortcode_tags[$tag], $atts, $content, $tag );
}

function unescape( $str ) {
	return html_entity_decode( $str, ENT_QUOTES, 'UTF-8' );
}

function sanitize_phone_number($phone_number) {
	$phone_number = preg_replace('/\D/', '', $phone_number);
	$phone_number = phone_8420($phone_number);
	if(preg_match('/^0\d{9}$/', $phone_number)) {
		return $phone_number;
	}
	return '';
}

function phone_8420($phone_no) {
	return preg_replace('/^\+?84/', '0', $phone_no);
}

function phone_0284($phone_no) {
	return preg_replace('/^0/', '+84', $phone_no);
}