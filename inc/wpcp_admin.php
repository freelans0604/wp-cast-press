<?php

class wpcp_admin {
	function __construct() {
		add_action('admin_menu', array($this, 'add_pages'));
	}
	function add_pages() {
		add_submenu_page( 'edit.php?post_type=cast', '設定', '設定', 'level_8', __FILE__, array($this,'wpcp_option_page'));
	}
	function wpcp_option_page() {
		if ( isset($_POST['wpcp_options'])) {
			check_admin_referer('wpcp_options');
			$opt = $_POST['wpcp_options'];
			update_option('wpcp_options', $opt);
			?><div class="updated fade"><p><strong><?php _e('Options saved.'); ?></strong></p></div><?php
		}
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div><h2>WPCP設定</h2>
			<form action="" method="post">
				<?php
					wp_nonce_field('wpcp_options');
					$opt = get_option('wpcp_options');
					$opt_timezone = isset($opt['timezone']) ? $opt['timezone']: null;
					for($i=1;$i<=10;$i++){
						$num = $i;
						if($i<10){
							$num = '0'.$i;
						}
						${'opt_sdtxt_'.$num} = isset($opt['sdtxt_'.$num]) ? $opt['sdtxt_'.$num]: null;
					}
					for($i=1;$i<=10;$i++){
						$num = $i;
						if($i<10){
							$num = '0'.$i;
						}
						${'opt_taxo_'.$num} = isset($opt['taxo-'.$num]) ? $opt['taxo-'.$num]: null;
					}
				?> 
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="timezone">日付変更時間</label><br>
							<p class="help">※サイトのスケジュールの日付を切り替える時間設定です。WPのタイムゾーンから計算されます。</p></th>
						<td>
							<select name="wpcp_options[timezone]" id="timezone">
							<?php
								for($i=0;$i<=6;$i++){
									if($opt_timezone == $i){
										echo '<option value="'.$i.'" selected>'.$i.'</option>';
									}else{
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
								}
							?>
							</select>時
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">シフトテキスト入力<br>
						<p class="help">※スケジュールの入力補助で選択できます。<br>よく使う時間やテキストを登録しておくと便利です。</p></th>
						<td>
						<?php
							for($i=1;$i<=10;$i++){
								$num = $i;
								if($i<10){
									$num = '0'.$i;
								}
						?>
								<input type="text" name="wpcp_options[sdtxt_<?php echo $num; ?>]" id="sdtxt_<?php echo $num; ?>" value="<?php echo ${'opt_sdtxt_'.$num}; ?>"><br>
								
						<?php } ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">タクソノミー設定<br>
						<p class="help">※「基本プレイ」「オプション」「グレード」などキャストをグループ別けします。<br>半角全角文字が使えます。</p></th>
						<td>
							<?php
								for($i=1;$i<=10;$i++){
									$num = $i;
									if($i<10){
										$num = '0'.$i;
									}
							?>
									cast-taxo-<?php echo $num; ?> : <input type="text" name="wpcp_options[taxo-<?php echo $num; ?>]" id="taxo_<?php echo $num; ?>" value="<?php echo ${'opt_taxo_'.$num}; ?>"><br>
									
							<?php } ?>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
			</form>
		</div>
    <?php
   	}
}
$wpcp_admin = new wpcp_admin;

?>