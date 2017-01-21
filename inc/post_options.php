<?php

// post type
add_action( 'init', 'wpcp_create_post_type' );
function wpcp_create_post_type() {
	register_post_type( 'cast',
		array(
			'labels' => array(
				'name' => __( 'キャスト' ),
				'singular_name' => __( 'キャスト' )
			),
			'public' => true,
			'menu_position' =>5,
			'has_archive' => true,
			'supports' => array('title', 'thumbnail'),
			'rewrite' => array(
			            'slug' => 'cast',
			            'with_front' => true)
		)
	);
}

// taxonomy
add_action( 'init', 'wpcp_taxo_init' );
function wpcp_taxo_init() {
	$opts = get_option('wpcp_options');
	foreach($opts as $key => $value){
		if(strpos($key, 'taxo-') !==false && $value){
			register_taxonomy(
				'cast-'.$key,
				'cast',
				array(
					'hierarchical' => true, 
					'label' => __( $value ),
					'rewrite' => array( 'slug' => 'cast-'.$key )
				)
			);
		}
	}
}



// custom field
add_action('admin_menu', 'wpcp_admin_menu');
add_action('save_post', 'wpcp_save_post');

function wpcp_admin_menu() {
	add_submenu_page( 'edit.php?post_type=cast', 'スケジュール', 'スケジュール', 'level_8', 'edit.php?post_type=cast&wpcp_page=schedule', '');
	add_meta_box('wpcp_pf', 'プロフィール', 'wpcp_input_profile', 'cast', 'normal', 'high');
	add_meta_box('wpcp_sd', 'スケジュール', 'wpcp_input_schedule', 'cast', 'normal', 'high');
	add_meta_box('wpcp_img', '画像', 'wpcp_input_images', 'cast', 'normal', 'high');
}


// custom field profile
function wpcp_input_profile() {
	global $post;
	echo '<ul>';
	echo '<li style="display:inline-block;margin-right:20px;"><label for="wpcp_pf_name">名前</label> <input type="text" name="wpcp_pf_name" size="15" value="'.get_post_meta($post->ID, 'wpcp_pf_name', true).'" id="wpcp_pf_name" /></li>';
	echo '<li style="display:inline-block;margin-right:20px;"><label for="wpcp_pf_age">年齢</label> <input type="text" name="wpcp_pf_age" size="5" value="'.get_post_meta($post->ID, 'wpcp_pf_age', true).'" id="wpcp_pf_age" /> 歳</li>';
	echo '</ul><ul>';
	echo '<li style="display:inline-block;margin-right:20px;"><label for="wpcp_pf_tall">身長</label> <input type="text" name="wpcp_pf_tall" size="5" value="'.get_post_meta($post->ID, 'wpcp_pf_tall', true).'" id="wpcp_pf_tall" /> cm</li>';
	echo '<li style="display:inline-block;margin-right:20px;"><label for="wpcp_pf_bust">バスト</label> <input type="text" name="wpcp_pf_bust" size="5" value="'.get_post_meta($post->ID, 'wpcp_pf_bust', true).'" id="wpcp_pf_bust" /> cm</li>';
	echo '<li style="display:inline-block;margin-right:20px;"><label for="wpcp_pf_waist">ウェスト</label> <input type="text" name="wpcp_pf_waist" size="5" value="'.get_post_meta($post->ID, 'wpcp_pf_waist', true).'" id="wpcp_pf_waist" /> cm</li>';
	echo '<li style="display:inline-block;margin-right:20px;"><label for="wpcp_pf_hips">ヒップ</label> <input type="text" name="wpcp_pf_hips" size="5" value="'.get_post_meta($post->ID, 'wpcp_pf_hips', true).'" id="wpcp_pf_hips" /> cm</li>';
	echo '<li style="display:inline-block;margin-right:20px;"><label for="wpcp_pf_cups">カップ</label> <input type="text" name="wpcp_pf_cups" size="5" value="'.get_post_meta($post->ID, 'wpcp_pf_cups', true).'" id="wpcp_pf_cups" /> cup</li>';
		
	echo '</ul>';
}



// custom field schedule
function wpcp_input_schedule() {
	global $post;
	$post_id = $post->ID;
	$num = 35;
    $wpcp_options = get_option('wpcp_options');
    $today = date('Y-m-d', strtotime(date_i18n('Y-m-d H:i:s').' - '.$wpcp_options['timezone'].' hour'));
    $today_date = date('Y-m-d H:i:s', strtotime(date_i18n('Y-m-d H:i:s').' - '.$wpcp_options['timezone'].' hour'));
	$today2 = date('Y/m/d', strtotime(date_i18n('Y-m-d H:i:s').' - '.$wpcp_options['timezone'].' hour'));
	
	wp_nonce_field(wp_create_nonce(__FILE__), 'wpcp_sd_nonce');
	echo '<p><input type="text" value="'.$today2.'" id="datepicker" onchange="wpcp_dp_change();">から';
	echo '<select name="wpcp_days_num" id="wpcp_days_num" onchange="wpcp_sd_num();">';
	$days_nums = array(7, 14, 21, 28, 35);
	foreach($days_nums as $num){
		if($num == 7){
			echo '<option value="'.$num.'" selected>'.$num.'</option>';
		}else{
			echo '<option value="'.$num.'">'.$num.'</option>';
		}
	}
	echo '</select>日間表示</p>';
	echo '<p>※自動的に保存されます</p>';
	echo '<input type="hidden" id="wpcp_post_id" name="wpcp_post_id" value="'.$post_id.'">';
	echo '<input type="hidden" id="wpcp_post_number" name="wpcp_post_number" value="'.$num.'">';
	echo '<ul id="wpcp_sd_ul" class="wpcp_sd_ul">';
	for($i=0;$i<$num;$i++){
		$date = date('Y-m-d', strtotime($today_date.' + '.$i.' days'));
		$date_dsp = date('m/d(D)', strtotime($today_date.' + '.$i.' days'));
		$date_w = date('w', strtotime($today_date.' + '.$i.' days'));
		$color = '';
		if($date_w == 0){
			$color = 'color:#B8531D;';
		}elseif($date_w == 6){
			$color = 'color:#3C699E;';
		}
		if($i > 6){
			echo '<li style="display:none;">';
		}else{
			echo '<li>';
		}
		echo '<label for="wpcp_sd_'.$date.'_'.$post_id.'" class="wpcp_sd_input_label" style="display:inline-block;width:90px;'.$color.'">'.$date_dsp.'</label> <input type="text" id="wpcp_sd_'.$date.'_'.$post_id.'" class="wpcp_sd_input" name="wpcp_sd_'.$date.'_'.$post_id.'" value="'.get_post_meta($post_id, 'wpcp_sd_'.$date, true).'" data-date="'.$date.'" data-id="'.$post_id.'" size="20" onchange="wpcp_sd_save_id(`wpcp_sd_'.$date.'_'.$post_id.'`);"> <input type="button" value="入力補助" data-id="'.$post_id.'" data-day="'.$date.'" class="wpcp_sd_freetxt_btn" />';
		$check = '';
		if(get_post_meta($post_id, 'wpcp_sd_'.$date.'_hd', true) == 1){
			$check = ' checked';
		}
		echo ' <input type="checkbox" data-date="'.$date.'" data-id="'.$post_id.'" onclick="wpcp_sd_save_hd_id(`wpcp_sd_'.$date.'_'.$post_id.'_hd`)" value="1" id="wpcp_sd_'.$date.'_'.$post_id.'_hd" class="wpcp_sd_holiday"'.$check.'><label for="wpcp_sd_'.$date.'_'.$post_id.'_hd" class="wpcp_sd_holiday_label">休み</label>';
		echo '</li>';
		
	}
	echo '</ul>';
}




// custom field images
function wpcp_input_images(){
	$post_id = get_the_ID();
	$html = '';
	if(get_post_meta($post_id,'wpcp_img',false)){
		$images = get_post_meta($post_id,'wpcp_img',false);
		foreach($images[0] as $key => $value){
			$url = wp_get_attachment_image_src( $value, 'thumbnail');
			$html .= <<<EOS
				<ul id="wpcp_img_{$key}">
					<li><img src='{$url[0]}'/><input type='hidden' name='wpcp_img[]' value='{$value}' /></li>
					<li><a href="#" class="wpcp_img_remove">削除する</a></li>
				</ul>
EOS;
		}
	}
	echo <<<EOS
		<style type="text/css">
		#wpcp_imgs ul {
			display:inline-block;
			vertical-align:top;
			margin: 10px;
			height: 130px;
			overflow:hidden;
			text-align:center;
		}
		#wpcp_imgs img{
			max-width: 100px;
			max-height: 100px;
			height:auto;
			border: 1px solid #cccccc;
		}
		</style>
 
        <div>
         <button id="wpcp_img_btn" type="button">画像を選択</button>
         <div id="wpcp_imgs">{$html}</div>
        </div>
EOS;
}
 
function wpcp_admin_scripts(){
	global $wp_scripts;
	$ui = $wp_scripts->query( 'jquery-ui-core' );
	wp_enqueue_media();
	wp_enqueue_style( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/'.$ui->ver.'/themes/smoothness/jquery-ui.css');
	wp_enqueue_style( 'admin', plugins_url('wp-cast-press/css/admin.css'));
	wp_enqueue_script('adminjs', plugins_url('wp-cast-press/js/admin.js'),array(),'1.0.0',false);
	wp_enqueue_script('google-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/'.$ui->ver.'/jquery-ui.min.js',array(),'1.0.0',true);
	wp_enqueue_script('datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js',array(),'1.0.0',true);

}



// custom field save
function wpcp_save_post($post_id){
	$my_nonce = isset($_POST['wpcp_sd_nonce']) ? $_POST['wpcp_sd_nonce'] : null;
	if(!wp_verify_nonce($my_nonce, wp_create_nonce(__FILE__))) {
		return $post_id;
	}
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }
	if(!current_user_can('edit_post', $post_id)) { return $post_id; }
	$array = array();
	foreach($_POST as $key => $value){
		if(strpos($key,'wpcp_') !== false && $key != 'wpcp_img'){
			$data = $_POST[$key];
			if(get_post_meta($post_id, $key) == '' && $date){
				add_post_meta($post_id, $key, $data, true);
			}elseif($data != get_post_meta($post_id, $key, true)){
				update_post_meta($post_id, $key, $data);
			}elseif(!$data){
				delete_post_meta($post_id, $key, get_post_meta($post_id, $key, true));
			}
		}
	}
	$wpcp_img = isset($_POST['wpcp_img']) ? $_POST['wpcp_img']: null;
	$wpcp_img_ex = get_post_meta($post_id, 'wpcp_img', true);
	if($wpcp_img){
		update_post_meta($post_id, 'wpcp_img',$wpcp_img);
	}else{
		delete_post_meta($post_id, 'wpcp_img',$wpcp_img_ex);
	}
}




// list custom field schedule
function wpcp_manage_posts_columns($columns) {
	global $post;
	if($post->post_type == 'cast'){
		$wpcp_page = isset($_GET['wpcp_page']) ? $_GET['wpcp_page']: null;
		if($wpcp_page == ''){
			$columns['wpcp_thum'] = '写真';
			$columns['wpcp_name'] = '名前';
			$columns['wpcp_age'] = "年齢";
			$columns['wpcp_pf'] = "サイズ";
			$columns['wpcp_sd'] = '近日の出勤';
		}elseif($wpcp_page == 'schedule'){
			$wpcp_day = isset($_GET['wpcp_day']) ? $_GET['wpcp_day']: null;
			if(!$wpcp_day){
			    $wpcp_options = get_option('wpcp_options');
			    $today_date = date('Y-m-d', strtotime(date_i18n('Y-m-d H:i:s').' - '.$wpcp_options['timezone'].' hour'));
				$wpcp_day = date('Y-m-d',strtotime($today_date));
			}
			for($i=0;$i<7;$i++){
				$date = date('n/j(D)', strtotime($wpcp_day.' + '.$i.' day'));
				$date_ymd = date('Y-m-d', strtotime($wpcp_day.' + '.$i.' day'));
				$columns['wpcp_sd_'.$date_ymd] = $date;
			}
			unset($columns['date']);
		}
	}
	return $columns;
}
add_filter( 'manage_posts_columns', 'wpcp_manage_posts_columns' );

function wpcp_sort_column($columns){
	global $post;
	if($post->post_type == 'cast'){
		$wpcp_page = isset($_GET['wpcp_page']) ? $_GET['wpcp_page']: null;
		if($wpcp_page == ''){
			$columns = array(
				'title' => 'タイトル',
				'wpcp_thum' => '写真',
				'wpcp_name' => '名前',
				'wpcp_age' => '年齢',
				'wpcp_pf' => 'サイズ',
				'wpcp_sd' => '近日の出勤',
				'date' => '日時',
			);
		}
	}
	return $columns;

}
add_filter( 'manage_posts_columns', 'wpcp_sort_column');

function wpcp_add_column($column_name, $post_id) {
	global $post;
	if($post->post_type == 'cast'){
		if( $column_name == 'wpcp_thum') {
			$stitle = get_the_post_thumbnail($post_id, 'thumbnail', array( 'style'=>'width:70px;height:auto;' ));
		}
		if( $column_name == 'wpcp_name' ) {
			$stitle = get_post_meta($post_id, 'wpcp_pf_name', true);
		}
		if( $column_name == 'wpcp_age' ) {
			$age = get_post_meta($post_id, 'wpcp_pf_age', true);
			$stitle = $age;
		}
		if( $column_name == 'wpcp_pf' ) {
			$tall = get_post_meta($post_id, 'wpcp_pf_tall', true);
			$bust = get_post_meta($post_id, 'wpcp_pf_bust', true);
			$waist = get_post_meta($post_id, 'wpcp_pf_waist', true);
			$hips = get_post_meta($post_id, 'wpcp_pf_hips', true);
			$cups = get_post_meta($post_id, 'wpcp_pf_cups', true);
			$stitle = 'T.'.$tall.'<br>B.'.$bust.'('.$cups.') W.'.$waist.' H.'.$hips;
		}
		if( $column_name == 'wpcp_sd' ) {
			$wpcp_day = isset($_GET['wpcp_day']) ? $_GET['wpcp_day']: null;
			if(!$wpcp_day){
			    $wpcp_options = get_option('wpcp_options');
			    $today_date = date('Y-m-d', strtotime(date_i18n('Y-m-d H:i:s').' - '.$wpcp_options['timezone'].' hour'));
				$wpcp_day = date('Y-m-d',strtotime($today_date));
			}
			for($i=0;$i<2;$i++){
				$date = date('n/j(D)', strtotime($wpcp_day.' + '.$i.' day'));
				$date_ymd = date('Y-m-d', strtotime($wpcp_day.' + '.$i.' day'));
				if($i!=0){
					$stitle .= '<br>';
				}
				$txt = get_post_meta($post_id, 'wpcp_sd_'.$date_ymd, true);
				if(!$txt){
					$txt = '未定';
				}
				$stitle .= $date.'<br> - '.$txt;
			}
		}
		if( strpos($column_name,'wpcp_sd_') !== false ) {
			$date = str_replace('wpcp_sd_', '', $column_name);
			$stitle = '<input type="text" id="wpcp_sd_'.$date.'_'.$post_id.'" class="wpcp_sd_input" name="wpcp_sd_'.$date.'_'.$post_id.'" value="'.get_post_meta($post_id, 'wpcp_sd_'.$date, true).'" data-date="'.$date.'" data-id="'.$post_id.'" size="12" onchange="wpcp_sd_save_id(`wpcp_sd_'.$date.'_'.$post_id.'`);">';
			$stitle .= '<br><input type="button" value="入力補助" data-id="'.$post_id.'" data-day="'.$date.'" class="wpcp_sd_freetxt_btn" />';
			$check = '';
			if(get_post_meta($post_id, 'wpcp_sd_'.$date.'_hd', true) == 1){
				$check = ' checked';
			}
			$stitle .= '<br><input type="checkbox" data-date="'.$date.'" data-id="'.$post_id.'" onclick="wpcp_sd_save_hd_id(`wpcp_sd_'.$date.'_'.$post_id.'_hd`)" value="1" id="wpcp_sd_'.$date.'_'.$post_id.'_hd" class="wpcp_sd_holiday"'.$check.'><label for="wpcp_sd_'.$date.'_'.$post_id.'_hd">休み</label>';
		}
		if ( isset($stitle) && $stitle ) {
			echo $stitle;
		} else {
			echo __('None');
		}
	}
}
add_action( 'manage_posts_custom_column', 'wpcp_add_column', 10, 2 );



// admin menu label
function wpcp_label() {
	global $wp_post_types;
	$labels = &$wp_post_types['cast']->labels;
	$labels->name = 'キャスト';
	$labels->singular_name = 'キャスト';
	$labels->add_new = _x('キャストの追加', 'キャスト');
	$labels->add_new_item = 'キャストの新規追加';
	$labels->edit_item = 'キャストの編集';
	$labels->new_item = '新規キャスト';
	$labels->view_item = 'キャストを表示';
	$labels->search_items = 'キャストを検索';
	$labels->not_found = 'キャストが見つかりませんでした';
	$labels->not_found_in_trash = 'ゴミ箱にキャストは見つかりませんでした';
	if($page = isset($_GET['wpcp_page'])){
		$labels->name = 'スケジュール';
		$labels->singular_name = 'スケジュール';
	}
	
}
add_action( 'init', 'wpcp_label' );


// admin title button
function wpcp_title_btn(){
	global $post;
	if($post->post_type == 'cast'){
		if(isset($_GET['wpcp_page']) == 'schedule'){
			if(isset($_GET['wpcp_day'])){
				$today = date('Y/m/d', strtotime($_GET['wpcp_day']));
			}else{
			    $wpcp_options = get_option('wpcp_options');
			    $today_date = date('Y-m-d', strtotime(date_i18n('Y-m-d H:i:s').' - '.$wpcp_options['timezone'].' hour'));
				$today = date('Y/m/d',strtotime($today_date));
			}
			?>
				<script>
				jQuery(function($){
					$('h1').append('<a href="<?php echo admin_url();?>edit.php?post_type=cast" class="page-title-action">キャスト一覧</a>');
					$('#menu-posts-cast li, #menu-posts-cast li a').removeClass('current');
					var elem = $('#menu-posts-cast li a[href="edit.php?post_type=cast&wpcp_page=schedule"]');
					elem.parent('li').addClass('current');
					$('#posts-filter .tablenav.top .alignleft.actions.bulkactions').append('<input type="text" value="<?php echo $today; ?>" id="datepicker" onchange="wpcp_dp_list_change();" size="12">から7日間表示');
					$('#datepicker').datepicker({
							changeMonth: true,
							changeYear: true
						});
					$('#posts-filter thead th[id^="wpcp_sd_"]').each(function(){
						var elemid = $(this).attr('id');
						var date = elemid.replace(/wpcp_sd_/g, '');
						$(this).append('<input type="button" data-day="'+date+'" data-txt="店休日" value="店休設定" class="wpcp_sd_shopholiday_btn">');
						$(this).append('<input type="button" data-day="'+date+'" data-txt="" value="店休解除" class="wpcp_sd_unshopholiday_btn">');
					});
				    $('.wpcp_sd_shopholiday_btn').click(function(event){
				        var datadate = $(this).attr('data-day');
				        var datatxt = $(this).attr('data-txt');
				        var weeks = ["日", "月", "火", "水", "木", "金", "土"];
				        var dt = new Date(datadate);
				        year = dt.getFullYear();
				        month = dt.getMonth()+1;
				        if(month<10){
				            month = '0'+month;
				        }
				        date = dt.getDate();
				        if(date<10){
				            date = '0'+date;
				        }
				        day = weeks[dt.getDay()];

				        if(confirm("すべてのキャストが「店休日」に上書きされ「休み」にチェックが入ります。\n"+year+'/'+month+'/'+date+'('+day+')を店休日に設定しますか？')){
					        $('#the-list tr').each(function(){
					            var elem = $(this).find('.wpcp_sd_input[id^="wpcp_sd_'+datadate+'"]');
					            var id = elem.attr('data-id');
					            elem.val(datatxt);
					            wpcp_sd_save(datadate, id, datatxt);

					            var elem = $(this).find('.wpcp_sd_holiday[id^="wpcp_sd_'+datadate+'"]');
					            id = elem.attr('id');
					            elem.prop("checked",true);
					            wpcp_sd_save_hd_id(id);
					        });
					    }
				    });
				    $('.wpcp_sd_unshopholiday_btn').click(function(event){
				        var datadate = $(this).attr('data-day');
				        var datatxt = $(this).attr('data-txt');
				        var weeks = ["日", "月", "火", "水", "木", "金", "土"];
				        var dt = new Date(datadate);
				        year = dt.getFullYear();
				        month = dt.getMonth()+1;
				        if(month<10){
				            month = '0'+month;
				        }
				        date = dt.getDate();
				        if(date<10){
				            date = '0'+date;
				        }
				        day = weeks[dt.getDay()];

				        if(confirm("すべてのキャストが「」(空白)に上書きされ「休み」のチェックが外れます。\n"+year+'/'+month+'/'+date+'('+day+')の店休日を解除しますか？')){
					        $('#the-list tr').each(function(){
					            var elem = $(this).find('.wpcp_sd_input[id^="wpcp_sd_'+datadate+'"]');
					            var id = elem.attr('data-id');
					            elem.val(datatxt);
					            wpcp_sd_save(datadate, id, datatxt);

					            var elem = $(this).find('.wpcp_sd_holiday[id^="wpcp_sd_'+datadate+'"]');
					            id = elem.attr('id');
					            elem.prop("checked",false);
					            wpcp_sd_save_hd_id(id);
					        });
					    }
				    });

				});
				</script>
			<?php
		}else{
			?>
				<script>
				jQuery(function($){
					$('h1').append('<a href="<?php echo admin_url();?>edit.php?post_type=cast&wpcp_page=schedule" class="page-title-action">スケジュール</a>');
				});
				</script>
			<?php
		}
	}
}
add_action( 'admin_footer-edit.php', 'wpcp_title_btn' );

// admin list refinement
add_action( 'load-edit.php' , 'wpcp_refinement_hide' );
function wpcp_refinement_hide() {
	add_filter( 'disable_months_dropdown' , 'custom_disable_months_dropdown' , 10 , 2 );
	function custom_disable_months_dropdown( $false , $post_type ) {
		$disable_months_dropdown = $false;
		$disable_post_types = array( 'cast' );
		if( in_array( $post_type , $disable_post_types ) ) {
			$disable_months_dropdown = true;
		}
		return $disable_months_dropdown;
	}
}

function wpcp_pre_get_posts( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$query->set( 'post_type', 'cast' );
	}
}
add_action( 'pre_get_posts', 'wpcp_pre_get_posts' );





?>