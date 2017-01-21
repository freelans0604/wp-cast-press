jQuery(document).ready(function($){

    $('#datepicker').datepicker({
            changeMonth: true,
            changeYear: true
        });




    var custom_uploader;
    $('#wpcp_img_btn').click(function(e) {
        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media({
            title: 'Choose Image',
            library: {
                type: 'image'
            },
            button: {
                text: 'Choose Image'
            },
            multiple: true
        });
        custom_uploader.on('select', function() {
            var images = custom_uploader.state().get('selection');
            var date = new Date().getTime();
            images.each(function(file){
                img_id = file.toJSON().id+"_"+date;
                $('#wpcp_imgs').append('<ul id=wpcp_img_'+ img_id +'></ul>')
                .find('ul:last').append('<li><img src="'+file.toJSON().url+'" />'
                    +'<input type="hidden" name="wpcp_img[]" value="'+file.toJSON().id+'" /></li>'
                    +'<li><a href="#" class="wpcp_img_remove">削除する</a></li>');
            });
        });
        custom_uploader.open();
    });
    $( ".wpcp_img_remove" ).live( 'click', function( e ) {
 
        e.preventDefault();
        e.stopPropagation();
 
        img_obj = $(this).parents('ul');
        if(img_obj.length >0){
            img_obj.remove();
        }
    });


    // schedule_input
    var weeks = ["日", "月", "火", "水", "木", "金", "土"];
    var dt = new Date(today_date);
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
    $('body').append('<div id="wpcp_sd_freetxt_box_wrap" class="wp-core-ui hidden"><div id="wpcp_sd_freetxt_box"></div></div>');
    $('#wpcp_sd_freetxt_box').append('<p class="date">'+year+'/'+month+'/'+date+'('+day+')</p><div class="select_wrap"></div><div class="btn_wrap"></div>');
    $('#wpcp_sd_freetxt_box .select_wrap').append('<div class="select_box"><select name="start_hour"></select> : <select name="start_time"></select> ~ <select name="end_hour"></select> : <select name="end_time"></select></div>');
    $('#wpcp_sd_freetxt_box .select_wrap').append('<div class="txt_select_box">テキスト入力 <select name="txt"><option value="-">-</option></select></div>');
    for(var i=0;i<24;i++){
        num = i;
        $('#wpcp_sd_freetxt_box select[name="start_hour"], #wpcp_sd_freetxt_box select[name="end_hour"]')
        .append('<option value="'+num+'">'+num+'</option>');
    }
    for(var i=0;i<60;i=i+5){
        num = i;
        if(i<10){
            num = '0'+i;
        }
        $('#wpcp_sd_freetxt_box select[name="start_time"], #wpcp_sd_freetxt_box select[name="end_time"]')
        .append('<option value="'+num+'">'+num+'</option>');
    }
    var elem = $('#wpcp_sd_freetxt_box .txt_select_box select');
    for(var i=0;i<sdtxts.length;i++){
        elem.append('<option value="'+sdtxts[i]+'">'+sdtxts[i]+'</option>');
    }
    $('#wpcp_sd_freetxt_box .btn_wrap')
        .append('<p>※「-」以外はテキスト入力が優先されます。<br>※0:00~0:00、「-」で入力を押すと<br>入力内容が削除されます。</p>')
        .append('<input type="hidden" name="date" id="wpcp_sd_freetxt_box_date" value="'+year+'-'+month+'-'+date+'">')
        .append('<input type="hidden" name="id" id="wpcp_sd_freetxt_box_id" value="">')
        .append('<input type="hidden" name="value" id="wpcp_sd_freetxt_box_value" value="">')
        .append('<input type="button" value="閉じる" class="button" id="wpcp_sd_freetxt_box_close" />')
        .append(' <input type="button" value="入力する" class="button" id="wpcp_sd_freetxt_box_submit" />');




    $('.wpcp_sd_freetxt_btn').click(function(){
        var dataday = $(this).attr('data-day');
        var dataid = $(this).attr('data-id');
        var input_val = $('#wpcp_sd_'+dataday+'_'+dataid).val();
        var dt = new Date(dataday);
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
        if(input_val.match(/^[0-9]+[0-9]*:[0-9]+[0-9]~[0-9]+[0-9]*:[0-9]+[0-9]$/)){
            var array = input_val.split('~');
            var array_s = array[0].split(':');
            var array_e = array[1].split(':');
            $('#wpcp_sd_freetxt_box select[name="start_hour"]').val(array_s[0]);
            $('#wpcp_sd_freetxt_box select[name="start_time"]').val(array_s[1]);
            $('#wpcp_sd_freetxt_box select[name="end_hour"]').val(array_e[0]);
            $('#wpcp_sd_freetxt_box select[name="end_time"]').val(array_e[1]);

            if(!$('#wpcp_sd_freetxt_box select[name="start_hour"]').val()){
                $('#wpcp_sd_freetxt_box select[name="start_hour"]').val('0');
            }
            if(!$('#wpcp_sd_freetxt_box select[name="start_time"]').val()){
                $('#wpcp_sd_freetxt_box select[name="start_time"]').val('00');
            }
            if(!$('#wpcp_sd_freetxt_box select[name="end_hour"]').val()){
                $('#wpcp_sd_freetxt_box select[name="end_hour"]').val('0');
            }
            if(!$('#wpcp_sd_freetxt_box select[name="end_time"]').val()){
                $('#wpcp_sd_freetxt_box select[name="end_time"]').val('00');
            }
            
            $('#wpcp_sd_freetxt_box select[name="txt"]').val('-');
        }else{
            $('#wpcp_sd_freetxt_box select[name="start_hour"]').val('0');
            $('#wpcp_sd_freetxt_box select[name="start_time"]').val('00');
            $('#wpcp_sd_freetxt_box select[name="end_hour"]').val('0');
            $('#wpcp_sd_freetxt_box select[name="end_time"]').val('00');

            if(input_val){
                $('#wpcp_sd_freetxt_box select[name="txt"]').val(input_val);
            }
            if(!$('#wpcp_sd_freetxt_box select[name="txt"]').val()){
                $('#wpcp_sd_freetxt_box select[name="txt"]').val('-');
            }
        }
        $('#wpcp_sd_freetxt_box .date').text(year+'/'+month+'/'+date+'('+day+')');
        $('#wpcp_sd_freetxt_box_date').val(year+'-'+month+'-'+date);
        $('#wpcp_sd_freetxt_box_id').val(dataid);
        $('#wpcp_sd_freetxt_box_value').val(input_val);
        $('#wpcp_sd_freetxt_box_wrap').removeClass('hidden');

    });
    $('#wpcp_sd_freetxt_box_submit').click(function(){
        var date = $('#wpcp_sd_freetxt_box_date').val();
        var txt;
        var sHour = $('#wpcp_sd_freetxt_box select[name="start_hour"]').val();
        var sTime = $('#wpcp_sd_freetxt_box select[name="start_time"]').val();
        var eHour = $('#wpcp_sd_freetxt_box select[name="end_hour"]').val();
        var eTime = $('#wpcp_sd_freetxt_box select[name="end_time"]').val();
        if(!(sHour == '0' && sTime == '00' && eHour == '0' && eTime == '00')){
            txt = sHour+':'+sTime+'~'+eHour+':'+eTime;
        }
        if($('#wpcp_sd_freetxt_box select[name="txt"]').val() != '-'){
            txt = $('#wpcp_sd_freetxt_box select[name="txt"]').val();
        }
        var dataid = $('#wpcp_sd_freetxt_box_id').val();
        var dateval = $('#wpcp_sd_freetxt_box_value').val();
        $('#wpcp_sd_'+date+'_'+dataid).val(txt);
        wpcp_sd_save(date, dataid, txt);
        
        $('#wpcp_sd_freetxt_box_wrap').addClass('hidden');
    });

    $(window).on('beforeunload', function(){
        document.activeElement.blur();
    });

    $('#wpcp_sd_freetxt_box_close').click(function(){
        $('#wpcp_sd_freetxt_box_wrap').addClass('hidden');
    });


});







