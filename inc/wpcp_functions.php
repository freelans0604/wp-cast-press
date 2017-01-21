<?php

function wpcp_the_content($post_id='') {
	if(!$post_id){
		global $post;
		$post_id = $post->ID;
	}
	$html = '';
	$my_images = wpcp_get_meta_images();
	$my_name = get_post_meta($post_id, 'wpcp_pf_name',true);
	$my_age = get_post_meta($post_id, 'wpcp_pf_age',true);
	$my_tall = get_post_meta($post_id, 'wpcp_pf_tall',true);
	$my_bust = get_post_meta($post_id, 'wpcp_pf_bust',true);
	$my_waist = get_post_meta($post_id, 'wpcp_pf_waist',true);
	$my_hips = get_post_meta($post_id, 'wpcp_pf_hips',true);
	$my_cups = get_post_meta($post_id, 'wpcp_pf_cups',true);
	$my_schedule = wpcp_the_schedule($post_id,'',7);

	$images = '<div class="wpcp_imgs">';
	if($my_images){
		$images .= '<div class="main_img">'.wp_get_attachment_image( $my_images[0], 'full').'</div>';
		$images .= '<ul>';
		foreach($my_images as $value){
			$images .= '<li>'.wp_get_attachment_image( $value, 'full').'</li>';
		}
		$images .= '</ul>';
	}
	$images .= '</div>';

	$schedule = '<table class="wpcp_schedule_table">';
	foreach($my_schedule as $value){
		$schedule .= '<tr><td>'.date('n/j', strtotime($value['date'])).'</td><td>'.$value['schedule'].'</td></tr>';
	}
	$schedule .= '</table>';
	
	$taxo = '';
	$opts = get_option('wpcp_options');
	foreach($opts as $key => $value){
		if(strpos($key,'cast-taxo-') !== false){
			$terms = get_the_terms( $post_id, $key);
			if($terms){
				$taxo .= '<div class="wpcp_'.$key.'">';
				$taxo .= '<p class="ttl">'.$value.'</p>';
				$taxo .= '<ul class="wpcp_'.$key.'_ul">';
				foreach($terms as $value2){
					$taxo .= '<li><a href="'.get_term_link( $value2->term_id, $key ).'">'.$value2->name.'</a></li>';
				}
				$taxo .= '</ul>';
				$taxo .= '</div>';
			}
		}
	}
	
	


	$html .= <<<EOS
	<div class="wpcp_cast-pf">
		<div class="cast-profile">
			<ul class="profile_ul">
				<li>Name : {$my_name}</li>
				<li>Age : {$my_age}</li>
				<li>Tall : {$my_tall} cm</li>
				<li>Bust : {$my_bust} cm ({$my_cups} cup)</li>
				<li>Waist : {$my_waist} cm</li>
				<li>Hip : {$my_hips} cm</li>
			</ul>
			{$taxo}

		</div>
		<div class="cast-images">
			{$images}
		</div>
		<div class="cast-schedule">
			{$schedule}
		</div>
	</div>

EOS;
	return $html;
}

function wpcp_the_schedule($post_id='', $today='', $days=''){
	if(!$post_id){
		global $post;
		$post_id = $post->ID;
	}
	if(!$today){
	    $wpcp_options = get_option('wpcp_options');
	    $today_date = date('Y-m-d', strtotime(date_i18n('Y-m-d H:i:s').' - '.$wpcp_options['timezone'].' hour'));
		$today = date('Y-m-d',strtotime($today_date));
	}
	if(!$days){
		$days = 1;
	}
	$schedules = array();
	for($i=0;$i<$days;$i++){
		$date = date('Y-m-d', strtotime($today.' + '.$i.' days'));
		$schedule = get_post_meta($post_id, 'wpcp_sd_'.$date,true);
		if(!$schedule){
			$schedule = '未定';
		}
		$schedules[] = array('date'=>$date, 'schedule'=>$schedule);

	}
	return $schedules;


}

function wpcp_get_meta_images($post_id=''){
	if(!$post_id){
		global $post;
		$post_id = $post->ID;
	}
	$images = get_post_meta($post_id, 'wpcp_img',false);
	$images = $images[0];

	return $images;
}




?>