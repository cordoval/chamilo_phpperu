(function ($){

    $('#mediamosa_upload').submit(function(){
         //var timer = setInterval( getUploadProgress, 250);
         getUploadProgress();
    });
  
    function getUploadProgress() {

    alert('start');
       var the_url = mediamosa_url + "/external/mediafile/uploadprogress";
//       $.get(the_url,{id : apc_upload_progress_id },
//                                  parse_upload_progress, 'json'
//                                  );

        var response = $.ajax({
                                url : the_url,
                                dataType : 'json',
                                data : {id : apc_upload_progress_id },
                                success : parse_upload_progress
                              });
            
    }
    function parse_upload_progress(json){
        alert('ok'+json['message']);
        if(json['status'] == 1)
        {
            $('#upload_progress').text(json['message']  + '<br />' + json['precentage'] + '%');
        }
    }

    function ajax_error()
    {
        alert('error');
    }
})(jQuery);



