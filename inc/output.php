<?php

function wpcp_post_class($classes) {
	global $post;
	if( $post->post_type == 'cast' ) {
		$classes[] = 'type-post';
		$classes[] = 'post';
	}
	return $classes;
}
add_filter('post_class', 'wpcp_post_class');


add_action('the_content', 'wpcp_add_content');
function wpcp_add_content ($content){
	global $post;
	if( $post->post_type == 'cast' ) {
		$my_content = wpcp_the_content($post->ID);
		$content .= $my_content;
	}
	return $content;
}

function wpcp_excerpt_mblength($length) {
	global $post;
	if( $post->post_type == 'cast' ) {
		$length = 1000;
	}
	return $length;
}
add_filter('excerpt_mblength', 'wpcp_excerpt_mblength');

function wpcp_get_the_excerpt($excerpt) {
	global $post;
	if( $post->post_type == 'cast' ) {
		$post_id = $post->ID;
		$my_age = get_post_meta($post_id, 'wpcp_pf_age',true);
		$my_tall = get_post_meta($post_id, 'wpcp_pf_tall',true);
		$my_bust = get_post_meta($post_id, 'wpcp_pf_bust',true);
		$my_waist = get_post_meta($post_id, 'wpcp_pf_waist',true);
		$my_hips = get_post_meta($post_id, 'wpcp_pf_hips',true);
		$my_cups = get_post_meta($post_id, 'wpcp_pf_cups',true);
		$my_schedule = wpcp_the_schedule($post_id);
		$excerpt = '<p>'.$my_age.'歳</p>';
		$excerpt .= '<p>T'.$my_tall.'･'.$my_bust.'('.$my_cups.')･'.$my_waist.'･'.$my_hips.'</p>';
		$excerpt .= '<p>本日:'.$my_schedule[0]['schedule'].'</p>';

	}
	return $excerpt;
}
add_filter( 'get_the_excerpt', 'wpcp_get_the_excerpt' );





?>