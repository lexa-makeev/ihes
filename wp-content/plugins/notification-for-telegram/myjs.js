jQuery(document).ready(function($){

  

$(function() {
  
  $('#notify_login_fail').on('change', function(e) {
    if($(this).is(":checked")) {
      $('#notify_login_fail_showpass').removeAttr('disabled');
            	$('#notify_login_fail_goodto').removeAttr('disabled');
    } else {
   $('#notify_login_fail_showpass').attr('disabled','disabled');
            $('#notify_login_fail_goodto').attr('disabled','disabled');
    }
    
  });
  
});


$('#notify_login_fail_showpass').attr('disabled','disabled');
$('#notify_login_fail_goodto').attr('disabled','disabled');

if($("#notify_login_fail").is(':checked')){
         $('#notify_login_fail_showpass').removeAttr('disabled');
        $('#notify_login_fail_goodto').removeAttr('disabled');

   }
 

 

  
});
      




 