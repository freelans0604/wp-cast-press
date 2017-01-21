<?php

add_action('admin_print_scripts', 'wpcp_js_admin_header' );

function wpcp_js_admin_header(){
    global $post;
    if($post->post_type == 'cast'){
        wpcp_admin_scripts();
        wp_print_scripts( array( 'sack', 'jquery' ));
        $wpcp_options = get_option('wpcp_options');
        $wpcp_opt_sdtxt = array();
        foreach($wpcp_options as $key => $value){
            if(strpos($key,'sdtxt_') !== false && $value){
                $wpcp_opt_sdtxt[] = $value;
            }
        }
        $today_date = date('Y-m-d', strtotime(date_i18n('Y-m-d H:i:s').' - '.$wpcp_options['timezone'].' hour'));
?>
<script type="text/javascript">
//<![CDATA[
var today_date = "<?php echo $today_date; ?>";
var sdtxts = new Array();
<?php
foreach($wpcp_opt_sdtxt as $key => $value){
    echo 'sdtxts['.$key.'] = "'.$value.'"; ';
}
?>
//]]>
</script>
<script type="text/javascript">
//<![CDATA[

function wpcp_sd_num(){
    var num = jQuery('#wpcp_days_num').val();
    jQuery('#wpcp_sd_ul li').hide();
    jQuery('#wpcp_sd_ul li:nth-child(-n+'+num+')').show();
}

function wpcp_sd_save(date, id, val){
    if(val == null){
        val = '';
    }
    var mysack = new sack( 
        "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );    

    mysack.execute = 5;
    mysack.method = 'POST';
    mysack.setVar( "action", "wpcp_sd_save" );
    mysack.setVar( "id", id );
    mysack.setVar( "date", date );
    mysack.setVar( "val", val );
    mysack.encVar( "cookie", document.cookie, false );
    mysack.onError = function() { alert('保存中にエラーが発生しました' )};
    mysack.runAJAX();

    return true;
}

function wpcp_sd_save_id(id){
    date = jQuery('#'+id).attr('data-date');
    val = jQuery('#'+id).val();
    id = jQuery('#'+id).attr('data-id');

    var mysack = new sack( 
        "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );    

    mysack.execute = 5;
    mysack.method = 'POST';
    mysack.setVar( "action", "wpcp_sd_save" );
    mysack.setVar( "id", id );
    mysack.setVar( "date", date );
    mysack.setVar( "val", val );
    mysack.encVar( "cookie", document.cookie, false );
    mysack.onError = function() { alert('保存中にエラーが発生しました' )};
    mysack.runAJAX();

    return true;
}

function wpcp_sd_save_hd_id(id){
    elem = jQuery('#'+id);
    date = elem.attr('data-date');
    if(jQuery('#'+id).prop('checked')){
        val = 1;
    }else{
        val = 0;
    }
    id = elem.attr('data-id');

    var mysack = new sack( 
        "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );    

    mysack.execute = 5;
    mysack.method = 'POST';
    mysack.setVar( "action", "wpcp_sd_save_hd" );
    mysack.setVar( "id", id );
    mysack.setVar( "date", date );
    mysack.setVar( "val", val );
    mysack.encVar( "cookie", document.cookie, false );
    mysack.onError = function() { alert('保存中にエラーが発生しました' )};
    mysack.runAJAX();

    return true;
}





function wpcp_dp_change(){
	var weeks = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    var post_id = jQuery('#wpcp_post_id').val();
    var dp_date = jQuery('#datepicker').val();
    var days_num = jQuery('#wpcp_post_number').val();
    var sd_date = new Array() ;

    var dt = new Date(dp_date);
    dt.setDate(dt.getDate() - 1);
    jQuery('#wpcp_sd_ul li').each(function(){
        dt.setDate(dt.getDate() + 1);
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
        week = dt.getDay();
        color = '';
        if(week == 0){
        	color = 'color:#B8531D;';
        }else if(week == 6){
        	color = 'color:#3C699E;';
        }
        var txt = jQuery(this).find('.wpcp_sd_freetxt_input').val();

    	var ymd = jQuery(this).find('.wpcp_sd_freetxt_btn').attr('data-day');
    	sd_date[ymd] = txt;
    	//alert(sd_date[year+'-'+month+'-'+date]+':'+year+'-'+month+'-'+date);

        jQuery(this).find('.wpcp_sd_input_label').attr({'for':'wpcp_sd_'+year+'-'+month+'-'+date+'_'+post_id, 'style': 'display:inline-block;width:90px;'+color}).text(month+'/'+date+'('+day+')');
        jQuery(this).find('.wpcp_sd_input').attr({'name':'wpcp_sd_'+year+'-'+month+'-'+date+'_'+post_id, 'id':'wpcp_sd_'+year+'-'+month+'-'+date+'_'+post_id, 'data-date': year+'-'+month+'-'+date, 'onchange': 'wpcp_sd_save_id(`wpcp_sd_'+year+'-'+month+'-'+date+'_'+post_id+'`);'});
        jQuery(this).find('.wpcp_sd_freetxt_btn').attr({'data-day':year+'-'+month+'-'+date});
        jQuery(this).find('.wpcp_sd_holiday').attr({'name':'wpcp_sd_'+year+'-'+month+'-'+date+'_'+post_id+'_hd', 'id':'wpcp_sd_'+year+'-'+month+'-'+date+'_'+post_id+'_hd', 'data-date': year+'-'+month+'-'+date, 'onclick': 'wpcp_sd_save_hd_id(`wpcp_sd_'+year+'-'+month+'-'+date+'_'+post_id+'_hd`);'});
        jQuery(this).find('.wpcp_sd_holiday_label').attr({'for':'wpcp_sd_'+year+'-'+month+'-'+date+'_'+post_id+'_hd'});

        jQuery(this).find('.wpcp_sd_holiday').prop("checked",false);
    });

    var mysack = new sack( 
        "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );    

    mysack.execute = 5;
    mysack.method = 'POST';
    mysack.setVar( "action", "wpcp_get_customfield" );
    mysack.setVar( "post_id", post_id );
    mysack.setVar( "days_num", days_num );
    mysack.setVar( "dp_date", dp_date );
    for(var key in sd_date){
    	mysack.setVar( "sd_"+key, sd_date[key]);
    }
    mysack.encVar( "cookie", document.cookie, false );
    mysack.onError = function() { alert('Ajax error in looking up elevation' )};
    mysack.runAJAX();

    return true;
};


function wpcp_dp_list_change(){
    var dp_date = jQuery('#datepicker').val();
    var dt = new Date(dp_date);
    year = dt.getFullYear();
    month = dt.getMonth()+1;
    if(month<10){
        month = '0'+month;
    }
    date = dt.getDate();
    if(date<10){
        date = '0'+date;
    }
    var url = 'edit.php?post_type=cast&wpcp_page=schedule&wpcp_day='+year+'-'+month+'-'+date;
    location.href = url;
}



//]]>
</script>
<?php
    }
}






add_action('wp_ajax_wpcp_get_customfield', 'wpcp_get_customfield' );

function wpcp_get_customfield(){
    $post_id = $_POST['post_id'];
    $days_num = $_POST['days_num'];
    $dp_date = $_POST['dp_date'];

    

    for($i=0;$i<$days_num;$i++){
        $date = date('Y-m-d', strtotime($dp_date.' + '.$i.' days'));
        $value = get_post_meta($post_id, 'wpcp_sd_'.$date, true);
        echo 'jQuery("#wpcp_sd_'.$date.'_'.$post_id.'").val("'.$value.'");';
        $value = get_post_meta($post_id, 'wpcp_sd_'.$date.'_hd', true);
        if($value == 1){
            echo 'jQuery("#wpcp_sd_'.$date.'_'.$post_id.'_hd").prop("checked",true);;';
        }
    }
    die();
}




add_action('wp_ajax_wpcp_sd_save', 'wpcp_sd_save' );

function wpcp_sd_save(){
    $id = $_POST['id'];
    $date = $_POST['date'];
    $val = $_POST['val'];

    if(!$val){
        delete_post_meta($id, 'wpcp_sd_'.$date, get_post_meta($id, 'wpcp_sd_'.$date, true));
    }elseif(get_post_meta($id, 'wpcp_sd_'.$date, true) == ''){
        add_post_meta($id, 'wpcp_sd_'.$date, $val, true);
    }elseif($val != get_post_meta($id, 'wpcp_sd_'.$date, true)){
        update_post_meta($id, 'wpcp_sd_'.$date, $val);
    }
    die();
}


add_action('wp_ajax_wpcp_sd_save_hd', 'wpcp_sd_save_hd' );

function wpcp_sd_save_hd(){
    $id = $_POST['id'];
    $date = $_POST['date'];
    $val = $_POST['val'];

    if(!$val){
        delete_post_meta($id, 'wpcp_sd_'.$date.'_hd', get_post_meta($id, 'wpcp_sd_'.$date.'_hd', true));
    }elseif(get_post_meta($id, 'wpcp_sd_'.$date.'_hd', true) == ''){
        add_post_meta($id, 'wpcp_sd_'.$date.'_hd', $val, true);
    }elseif($val != get_post_meta($id, 'wpcp_sd_'.$date.'_hd', true)){
        update_post_meta($id, 'wpcp_sd_'.$date.'_hd', $val);
    }
    die();
}







?>