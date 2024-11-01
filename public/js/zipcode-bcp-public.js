(function ($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(document).on('click', '#zbcp_check_serving_zipcode', function (e) {
        var zipcode = $('#zbcp_serving_zipcode').val();
        var zbcp_serving_id = $('#zbcp_serving_id').val();
        $(this).val('Checking...');
        if(zipcode!=''){
            $.ajax({
                type: 'POST',
                url: frontend_ajax_object.ajaxurl,
                data: {
                    action: "check_zipcode_from_post_page_meta",
                    zipcode: zipcode,
                    post_id: zbcp_serving_id,
                },
                success: function (data) {
                    var res = JSON.parse(data); 
                    if(res.status == true){
                        window.location = res.result; 
                    }else{
                       $('#zbcp_messages').removeClass('zbcp_success_message');
                       $('#zbcp_messages').addClass('zbcp_error_message');
                       $('#zbcp_messages').html(res.result);
                    }
                    if(res.showSearch==true){
                        $('#zbcp_search_form_wrapper').fadeIn();
                    }else{
                        $('#zbcp_search_form_wrapper').fadeOut();
                    }
                    $('#zbcp_check_serving_zipcode').val('Check');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                    $('#zbcp_check_serving_zipcode').val('Check');
                }
            });
        }else{
            $('#zbcp_messages').removeClass('zbcp_success_message');
            $('#zbcp_messages').addClass('zbcp_error_message');
            $('#zbcp_messages').html(frontend_ajax_object.cdzc_settings_no_zipcode_added);
            $('#zbcp_check_serving_zipcode').val('Check');
        }
        e.preventDefault();
    });
    
    $(document).on('click', '#zbcp_submit_email_zipcode', function (e) {
        var zipcode = $('#zbcp_serving_zipcode').val();
        var emailVal = $('#zbcp_email_against_zipcode').val();
        var zbcp_post_id_against_zipcode = $('#zbcp_post_id_against_zipcode').val();
        var zbcp_post_type_against_zipcode = $('#zbcp_post_type_against_zipcode').val();
        var zbcp_post_title_against_zipcode = $('#zbcp_post_title_against_zipcode').val();
        
        $(this).val('Submitting...');
        if(zipcode!=''){
            if(emailVal!='' && validateEmail(emailVal)){
               $.ajax({
                type: 'POST',
                url: frontend_ajax_object.ajaxurl,
                data: {
                    action: "submit_email_against_zipcode",
                    zipcode: zipcode,
                    email: emailVal,
                    post_id: zbcp_post_id_against_zipcode,
                    post_type: zbcp_post_type_against_zipcode,
                    post_title: zbcp_post_title_against_zipcode
                },
                success: function (data) {
                    var res = JSON.parse(data); 
                    if(res.status == true){
                        $('#zbcp_messages').removeClass('zbcp_error_message');
                       $('#zbcp_messages').addClass('zbcp_success_message');
                       $('#zbcp_messages').html(res.result);
                    }else{
                        $('#zbcp_messages').removeClass('zbcp_success_message');
                       $('#zbcp_messages').addClass('zbcp_error_message');
                       $('#zbcp_messages').html(res.result);
                    }
                    $('#zbcp_submit_email_zipcode').val('Submit');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#zbcp_messages').removeClass('zbcp_success_message');
                    $('#zbcp_messages').addClass('zbcp_error_message');
                    $('#zbcp_messages').html('There is an error, please try again');
                    $('#zbcp_submit_email_zipcode').val('Submit');
                }
              });
            }else{
                $('#zbcp_messages').removeClass('zbcp_success_message');
                $('#zbcp_messages').addClass('zbcp_error_message');
                $('#zbcp_messages').html(frontend_ajax_object.cdzc_settings_no_valid_email);
                $('#zbcp_submit_email_zipcode').val('Submit');
            }
        }else{
            $('#zbcp_messages').removeClass('zbcp_success_message');
            $('#zbcp_messages').addClass('zbcp_error_message');
            $('#zbcp_messages').html(frontend_ajax_object.cdzc_settings_no_zipcode_added);
            $('#zbcp_submit_email_zipcode').val('Submit');
        }
        e.preventDefault();
    });
   
    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

})(jQuery);
