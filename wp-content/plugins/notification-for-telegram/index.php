<?php
/**
* Plugin Name: Notification for Telegram
* Plugin URI: https://www.reggae.it/my-wordpress-plugins
 * Description: Receive notification in Telegram from wordpress
 * Version: 2.4
 * Author: Andrea Marinucci
 * Author URI: 
 * Text Domain: notification-for-telegram
 * Domain Path: /languages
**/

if ( ! defined( 'ABSPATH' ) ) exit;


include( plugin_dir_path( __FILE__ ) . 'include/tnfunction.php');
include( plugin_dir_path( __FILE__ ) . 'include/nftncron.php');
include( plugin_dir_path( __FILE__ ) . 'include/nftb_optionpage.php');

function nftb_init_method() {
// LOAD JQUERY SCRIPTS

//Enqueue Admin CSS on Job Board Settings page only
if ( isset( $_GET['page'] ) && $_GET['page'] == 'telegram-notify' ) {
    // Enqueue Core Admin Styles
    wp_enqueue_style( 'nftb_plugin_script2', plugins_url ( '/mystyle.css', __FILE__ ));
   
     // JS
    wp_register_script('nftb_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
    wp_enqueue_script('nftb_bootstrap');

    // CSS
    wp_register_style('nftb_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
    wp_enqueue_style('nftb_bootstrap');
   
 
wp_enqueue_script('nftb_plugin_script', plugins_url('/myjs.js', __FILE__), array('jquery') );

    }
       
}    


//add_action('admin_enqueue_scripts', 'nftb_init_method');
add_action('init', 'nftb_init_method');

//trim css per carcaricrae il mio 
function nftb_trim_css_version($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'nftb_trim_css_version', 9999);

// Activation 
register_activation_hook( __FILE__, 'nftb_plugin_activate' );
function nftb_plugin_activate() {
	

}


function nftb_my_plugin_init() {
  load_plugin_textdomain( 'notification-for-telegram', false, 'notification-for-telegram/languages' );
}
add_action('init', 'nftb_my_plugin_init');



//add jquery to footer for TEST button
add_action( 'admin_footer', 'nftb_test_javascript' ); // Write our JS below here
function nftb_test_javascript() { ?>
	<script type="text/javascript" >
		jQuery(document).ready(function($) {
			var data = {
			'action': 'nftb_test_action',
			'whatever': 1234
		};
		
		

		var alerts = 'Check your Telegram if you got the message | WOW IT WORKS| the plug is connected to Telgram API';
		
		if ($("#saysomething").length > 0) {
     var saysomething = document.getElementById('saysomething').value;
        //do something
     
		
		
		
	$("#saysomething").keyup(function() {
        if ($.trim($('#saysomething').val()).length < 1) {

       $("#buttonTest").text("TEST");
		  alerts = 'Check your Telegram if you got the message |WOW IT WORKS| the plug is connected to Telgram API';
       
        } else {
        	saysomething = document.getElementById('saysomething').value;
		 alerts = 'Sent message : '+saysomething;
		 
       $("#buttonTest").text("Send Message");
        }
    });
		
			
     }; 


		

		

	$("#buttonTest").on('click', function(){
	 
	
	
	
	 $.ajax({
				url: ajaxurl, 
				type: "POST",
				data: {
					action: 'nftb_test_action',
					token: 'token',
					chatids: 'chatids',
					saysomething: saysomething
							},
				cache: false,
				success: function(dataResult){
				 alert(alerts); 
 				 }});
		});
		
		$("#buttoncron").on('click', function(){
	
	 var timex = document.getElementById('notify_update').value;
	 // alert(timex); 
	 $.ajax({
				url: ajaxurl, 
				type: "POST",
				data: {
					action: 'nftb_cron_action',
					intervallo: timex
							},
				cache: false, 
				success: function(response){
                    
                    document.getElementById("notify_update").checked = false;
                   

					document.getElementsByClassName('button button-primary')[0].click();
					alert("Clean & Reload");
                    //echo what the server sent back...
                
                }});
		});	
		
		
		
		
		$("#buttoncronset").on('click', function(){
	
	 var timex = document.getElementById('notify_update_time').value;
	//  alert(timex); 
	 $.ajax({
				url: ajaxurl, 
				type: "POST",
				data: {
					action: 'nftb_cron_action_set',
					intervallo: timex
							},
				cache: false, 
				success: function(response){
                    
                    //document.getElementById("notify_update").checked = false;
                    document.getElementById("notify_update").checked = true;

					//document.getElementsByClassName('button button-primary')[0].click();
					document.getElementsByClassName('button button-primary')[0].click();
					//alert("Cron Scheduled"+response);
                    //echo what the server sent back...
                
                }});
		});	
		
		
		
	});
	</script> <?php
}
 






//Fuction to send test connection
add_action( 'wp_ajax_nftb_test_action', 'nftb_test_action' );
function nftb_test_action() {
	
	  $telegram_notify_options = get_option( 'telegram_notify_option_name' ); // Array of All Options
 $notify_ninjaform2 = $telegram_notify_options['notify_update']; // Active service
	$notify_update_time = $telegram_notify_options['notify_update_time']; // Token
	
	$saysomething = isset($_POST['saysomething']) ? $_POST['saysomething'] : null;

	
	//$whatever = intval( $_POST['whatever'] );
	$TelegramNotify = new nftb_TelegramNotify();
	$token =  $TelegramNotify->getValuefromconfig('token_0');
	$chatids_ = $TelegramNotify->getValuefromconfig('chatids_');

	
	
	
	$apiToken = $token ;
	$blog_title = get_the_title( $post_id );
	$users=explode(",",$chatids_);
	$bloginfo = get_bloginfo( 'name' );
	



 if( ( $saysomething ) ) {
 
 $testmessage = "\xF0\x9F\x93\xA3 Message from ". $bloginfo.": ".$saysomething;
}else {

$testmessage = ("\xF0\x9F\x9A\x80 WOW IT WORKS on ".$bloginfo);

}

	
	foreach ($users as $user)
		{
    	if (empty($user)) continue;
    	$data = [
        'chat_id' => $user,
        'text' => cleanString($testmessage) ];
 
        	$response = wp_remote_get( "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), array( 'timeout' => 120, 'httpversion' => '1.1' ) );
        			}
		wp_die(); // this is required to terminate immediately and return a proper response
}



//SHORTCODE
add_shortcode( 'telegram_mess', 'nftb_telegram_mess' );
function nftb_telegram_mess_init(){
 function nftb_telegram_mess($atts) {
 
 	$TelegramNotify = new nftb_TelegramNotify();
	$token =  $TelegramNotify->getValuefromconfig('token_0');
	$chatids_ = $TelegramNotify->getValuefromconfig('chatids_');
	$apiToken = $token ;
	$blog_title = get_the_title( $post_id );	
	$bloginfo = get_bloginfo( 'name' );
 
 
 //options default
 $a = shortcode_atts( array(
 'token' => $token ,
 'chatids' => $chatids_,
 'message' => 'no message',
 'showip' => '0',
 'showcity' => '0',
 'showsitename'=> '0'
  ), $atts );
 
 $newtoken = $a['token'];
 $newmessage = $a['message'];
 
 if ($a['showsitename'] == "1") { 
 $newmessage = $newmessage." - Message from  ".$bloginfo;
 }
  if ($a['showip'] == "1") { 
 $newmessage = $newmessage. " ,IP: ".nftb_get_the_user_ip();
 }
 
  if ($a['showcity'] == "1") { 
  $userip = nftb_get_the_user_ip();
//  $details = json_decode(wp_remote_get("http://ipinfo.io/{$userip}/json"));
  
  
  $newmessage =  $newmessage .nftb_ip_info($userip);
  
 }
 
$users=explode(",",$a['chatids']);
	foreach ($users as $user)
		{
    	if (empty($user)) continue;
    	$data = [
        'chat_id' => $user,
        'text' => $newmessage];
        
    
        	//$response = @file_get_contents("https://api.telegram.org/bot$newtoken/sendMessage?" . http_build_query($data) );
	 		$response = wp_remote_get( "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), array( 'timeout' => 120, 'httpversion' => '1.1' ) );
	 					
		}
  }
}



add_action('init', 'nftb_telegram_mess_init');

// end shortcode




// All Post type  Notification  Add the hook action
add_action('transition_post_status', 'nftb_send_new_post', 10, 3);

// Listen for publishing of a new post
function nftb_send_new_post($new_status, $old_status, $post) {

 $TelegramNotify2 = new nftb_TelegramNotify();
 if ($TelegramNotify2->getValuefromconfig('notify_newpost')) {
	
	$post_id = $post->ID;
    
	$blog_title = get_the_title( $post_id );
	$TTiltle =  html_entity_decode($blog_title);
	$bloginfo = get_bloginfo( 'name' );
	$author_id = get_post_field ('post_author', $post_id);
	//$poststatustel = get_post_status( $post_id);
	$display_name = get_the_author_meta( 'display_name' , $author_id ); 
	global $wpdb;


	$posturl = get_edit_post_link($post_id );
	
	// Fire hooks only when hits senf to revision first time	
	 if('pending' === $new_status && 'draft' === $old_status ) {
    // Do something!
 
   			 update_post_meta($post_id, 'votes_count', '0');
    		 $messaggio = 'New '.$post->post_type.' on '.$bloginfo.' by user '. $display_name . ' : '.$TTiltle;
  			
  			//nftb_send_teleg_message($messaggio);
  			
  			nftb_send_teleg_message($messaggio,'EDIT POST','www.reggae.it','');
  			
  			}
  			
  		 if('draft' === $new_status && 'publish' === $old_status ) {
    // Do something!
 
   			 update_post_meta($post_id, 'votes_count', '0');
    		 $messaggio = 'New revision  '.$post->post_type.' on '.$bloginfo.' by user '. $display_name . ' : '.$TTiltle;
  			
  			//nftb_send_teleg_message($messaggio);
  			nftb_send_teleg_message($messaggio,'EDIT POST' ,$posturl,'');
  			
  			
  			}
  				
  			
  			 if('publish' === $new_status && 'pending' === $old_status ) {
    // Do something!
 
   			 update_post_meta($post_id, 'votes_count', '0');
    		 $messaggio = 'Published new '.$post->post_type.' on '.$bloginfo.' by user '. $display_name . ' : '.$TTiltle;
  			
  			nftb_send_teleg_message($messaggio,'EDIT POST' ,$posturl,'');
  			
  			}
	}
}


//WP FORM
add_action("wpforms_process_complete", 'nftb_function_save_custom_form_data');

function nftb_function_save_custom_form_data($params) {
$TelegramNotify2 = new nftb_TelegramNotify();
	if ($TelegramNotify2->getValuefromconfig('notify_wpform')) {

	$bloginfo = get_bloginfo( 'name' );
  $defmessage = "" ;
    foreach($params as $idx=>$item) {
        $field_name = $item['name'];
        $fiel_value = $item['value'];
        
       
  $defmessage = $defmessage ."\r\n".$field_name." : ".$fiel_value;
        
        // Do whatever you need
    }
    
    nftb_send_teleg_message( "NEW Form Wpform".$bloginfo."\r\n ".$defmessage, '','');
    return true;
     } //

}



//CF7
add_action('wpcf7_before_send_mail','nftb_after_sent_mail');
function nftb_after_sent_mail($cf7){

$TelegramNotify2 = new nftb_TelegramNotify();
 if ($TelegramNotify2->getValuefromconfig('notify_cf7')) {

	global $filename ;
	global $name ;
	global $youremail;
	global $posted_data;
	global  $wpcf ;

	$bloginfo = get_bloginfo( 'name' );
	$wpcf = WPCF7_ContactForm::get_current();
	$submission = WPCF7_Submission::get_instance();
	$posted_data = $submission->get_posted_data();


	$dumpone = $debug = var_export($posted_data, true);
	// $dumpone = var_dump($dumpone);

	if( !empty($posted_data["your-name"])){  //use a field unique to your form
       $name =    $posted_data["your-name"];
       $youremail =    $posted_data["your-email"];
       $yoursubject =    $posted_data["your-subject"];
       $yourmessage =    $posted_data["your-message"];
       $yourmobile =    $posted_data["your-mobile"];
       $yourtelegramuser =    $posted_data["your-telegramuser"];
	}


        nftb_send_teleg_message("NEW Form ".$bloginfo." from :".$posted_data["your-name"]." VarDump:".$dumpone);
       
       //Stop mail in debug 
       // add_filter('wpcf7_skip_mail', 'nftb_abort_mail_sending');     
        
  }       
} 

//FUNCTION TO STOP THE CF7 MAIL for DEBUG 
function nftb_abort_mail_sending($contact_form){
    return true;
}




	
$telegram_notify_options = get_option( 'telegram_notify_option_name_tab3' ); // Array of All Options

$order_trigger_selected = isset($telegram_notify_options['order_trigger']) ? $telegram_notify_options['order_trigger'] : null;

if( (!$order_trigger_selected )) { 
add_action( 'woocommerce_checkout_order_processed',  'nftb_detect_new_order_on_checkout' );
 } else { 


//printf('xxxx'.$order_trigger_selected );

if ($order_trigger_selected === "woocommerce_checkout_order_processed")  { 
add_action( 'woocommerce_checkout_order_processed',  'nftb_detect_new_order_on_checkout' );
} 

if ($order_trigger_selected === "woocommerce_thankyou")  {
add_action( 'woocommerce_thankyou', 'nftb_detect_new_order_on_checkout', 10, 1 ); 

} 
if ($order_trigger_selected === "woocommerce_payment_complete")  { 

add_action( 'woocommerce_payment_complete', 'nftb_detect_new_order_on_checkout' );

}
 
}



 

function nftb_detect_new_order_on_checkout($order_id) {
     $TelegramNotify2 = new nftb_TelegramNotify();

 if ($TelegramNotify2->getValuefromconfig('notify_woocomerce_order')) {
   
   $order = wc_get_order( $order_id);
  $bloginfo = get_bloginfo( 'name' );


	
 
   
if ( $order ) {
  $total =  $order->get_total();
  $first_name =  $order->get_billing_first_name();
  $last_name = $order->get_billing_last_name();
  $shipping_city =	$order->get_shipping_city();
  $shipping_state = $order->get_shipping_country();
  $pagamento = $order->get_payment_method_title();
  $billing_email = $order->get_billing_email();
  $order_date = $order->order_date;
  $order_date2 = $order->get_date_created()->format ('j F Y, g:i a');
  $order_telegram =   get_post_meta( $order->id, 'Telegram', true );
  $order_status = $order->get_status();
  $currency_code = $order->get_currency();
  $currency_symbol = get_woocommerce_currency_symbol( $currency_code );
  $billing_phone = $order->get_billing_phone();
  


  $shipline = "SHIP TO :\r\n".$order->shipping_first_name. " ".$order->shipping_last_name."\r\n". "Company :".$order->shipping_company."\r\n";
  $shipline = $shipline . "Address: ".	$order->shipping_address_1 . " ". $order->shipping_address_2. "\r\n";
  $shipline = $shipline . "City: ". $order->shipping_city. "\r\nState:".$order->shipping_state. "\r\n".$order->shipping_postcode. "\r\n".	$order->shipping_country;


  $billingline = "BILL TO :\r\n".$order->billing_first_name. " ".$order->billing_last_name."\r\n". "Company :".$order->billing_company."\r\n";
  $billingline = $billingline . "Address: ".	$order->billing_address_1 . " ". $order->billing_address_2. "\r\n";
  $billingline = $billingline . "City: ". $order->billing_city. "\r\nState:".$order->billing_state. "\r\n".$order->billing_postcode. "\r\n".	$order->billing_country;


	 global  $woocommerce;


  if($order->is_paid())
            $paid = __('Order Paid');
        else
            $paid = __('Order NOT Paid');


    get_woocommerce_currency_symbol();

   // etc.
   // etc.
     // $order2 = new WC_Order( $order_id ); 
      foreach ($order->get_items() as $item_id => $item) {        
        //Get the product ID        
        $product_id = $item->get_product_id(); 
        // Get the WC_Product object         
        $product = $item->get_product();         
        $product_sku    = $product->get_sku();         
        $description = get_post($item['product_id'])->post_content; 
        // Name of the product
        $item_name    = $item->get_name();    
        $quantity     = $item->get_quantity();           
        $tax_class    = $item->get_tax_class();  
        // Line subtotal (non discounted)       
        $line_subtotal     = $item->get_subtotal();
        // Line subtotal tax (non discounted)      
        $line_subtotal_tax = $item->get_subtotal_tax(); 
        // Line total (discounted)     
        $line_total        = $item->get_total(); 
        // Line total tax (discounted)        
        $line_total_tax    = $item->get_total_tax();        
    
    
    
    //se checcato aggiungi tasse
     if ($TelegramNotify2->getValuefromconfig('price_with_tax')) {
    
    $line_total = wc_round_tax_total($item->get_total()) + wc_round_tax_total($item->get_total_tax()); // Discounted total with tax
     }
    
    $linea = $linea.$quantity." x " .$item_name . " - ". $line_total." ". $currency_code."\r\n" ;
    
    } 


   
  $telegraminmessage = "";
  // add @ if not present 
 if( !empty($order_telegram)){  
   
   if(strpos($order_telegram, '@') !== false){
   $order_telegram = $order_telegram;
} else{
   $order_telegram= "@".$order_telegram;
}
     $telegraminmessage = "\r\nTelegram user: ". $order_telegram;
 
  } 
  


	if ( nftb_NotifyA() ) {  
	
	$unlockobj= new nftb_NotifyA();
			$phoneline = $unlockobj->get_order_phone($order_id);
	
	  }

  
  
  
  $defmessage = "Новый заказ ".$order_id."\r\nФИО: ". $first_name. " ". $last_name.", Почта: ".  $billing_email .", Телефон: ".  $billing_phone ."\r\nИтоговая сумма: ".$total." ".$currency_code;
  $defmessage = $defmessage ."\r\nДата заказа: ".$order_date2; 
  
  $defmessage = $defmessage . $telegraminmessage ;
  $defmessage = $defmessage .$phoneline;
   $defmessage = $defmessage ."\r\n\r\nТовары:\r\n";
   $defmessage = $defmessage . $linea;
    $defmessage = $defmessage ."";
   
  if ($TelegramNotify2->getValuefromconfig('hide_bill')) { $defmessage = $defmessage . "\r\n\r\n\xF0\x9F\x93\x9D". $billingline; }
  if ($TelegramNotify2->getValuefromconfig('hide_ship')) { $defmessage = $defmessage . "\r\n\r\n\xF0\x9F\x9A\x9A". $shipline; }
  
    
    
    
  //  $defmessage = $defmessage . "\r\n". get_admin_url( null, 'post.php?post='.$order_id.'&action=edit', 'http' );
    $editurl = get_admin_url( null, 'post.php?post='.$order_id.'&action=edit', 'http' ); 
   
    // nftb_send_teleg_message( $defmessage);
//switch doub 

 nftb_send_teleg_message( $defmessage, 'Редактировать заказ N. '.$order_id ,$editurl,'');
 //$defmessage $eventualechtid, $urlname, $urllink)
	  add_option('nftb_new_order_id_for_notification_'.$order_id,'notify' );
   
  
   
}

  }   
    
   
}

//LOW STOCK
add_action( 'woocommerce_low_stock', 'nftb_woocommerce_low_stock', 10, 1 ); 
function nftb_woocommerce_low_stock( $product ) { 
    $TelegramNotify2 = new nftb_TelegramNotify();
//global $product;
 if ($TelegramNotify2->getValuefromconfig('notify_woocomerce_lowstock')) {

   

 
  
  $prodname = $product->get_name();
  $id = $product->get_id();
  $bloginfo = get_bloginfo( 'name' );
  $stock_resold =$product->get_manage_stock();
  $stock_quantity = $product->get_stock_quantity();

  
   $defmessage = $defmessage . "\r\n Edit Now -> ". get_admin_url( null, 'post.php?post='.$id.'&action=edit', 'http' );
  
  
  

  
  
   $defmessage = "\xF0\x9F\x98\xB1 Low Stock Warning on ".$prodname. ". You have only ".$stock_quantity." on ".$bloginfo." low stock limit ". $stock_resold. " " .$defmessage;
 
   nftb_send_teleg_message( $defmessage);
   
   
    }
   
}; 
         
// add the action 









//orderSTATUS CHANGE
add_action( 'woocommerce_order_status_changed', 'nftb_mysite_woocommerce_status_change', 99, 3 );
function nftb_mysite_woocommerce_status_change($order_id, $old_status, $new_status ) {

 $TelegramNotify2 = new nftb_TelegramNotify();

 if ($TelegramNotify2->getValuefromconfig('notify_woocomerce_order_change')) {
   
   $order = wc_get_order( $order_id);
  $bloginfo = get_bloginfo( 'name' );

  
if ( $order ) {
  $total =  $order->get_total();
  $first_name =  $order->get_billing_first_name();
  $last_name = $order->get_billing_last_name();
  $shipping_city =	$order->get_shipping_city();
  $shipping_state = $order->get_shipping_country();
  $pagamento = $order->get_payment_method_title();
  $billing_email = $order->get_billing_email();
    $order_date = $order->order_date;
    $currentdate =  date("Y-m-d H:i:s");
    
    $ts1 = strtotime($order_date);
$ts2 = strtotime( $currentdate);     
$seconds_diff = $ts2 - $ts1;                            
$time = ($seconds_diff/1);
    
   // etc.
   // etc.
}


 $options = get_option('nftb_new_order_id_for_notification_'.$order_id);
//(strcasecmp($new_status, 'on-hold') == 1)




// check notocication activi for all changes
 if ($TelegramNotify2->getValuefromconfig('notify_woocomerce_order_change') && (!$options))    {

     nftb_send_teleg_message("Order ".$order_id." status change on ".$bloginfo . " from ". $old_status." to ".$new_status. " | ". $first_name. " ". $last_name.", ".  $billing_email .", Total : ".$total.", (".$pagamento.") ".", shipping info: ".$shipping_city ." / ".  $shipping_state." / Order Date ".$order_date  );
   
     } 
     
     delete_option( 'nftb_new_order_id_for_notification_'.$order_id );      
     
  }   
}



//add to cart
add_action( 'woocommerce_add_to_cart', 'nftb_action_woocommerce_add_to_cart', 10, 6 ); 
// define the woocommerce_add_to_cart callback 
function nftb_action_woocommerce_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) { 
 $TelegramNotify2 = new nftb_TelegramNotify();
if ($TelegramNotify2->getValuefromconfig('notify_woocomerce_addtocart_item')) {

    $bloginfo = get_bloginfo( 'name' );
    
    global $product;

	// If the WC_product Object is not defined globally
	
   	 $product = wc_get_product( $product_id);
	

	$myprodname = $product->get_name();
     nftb_send_teleg_message("NEW prod in the cart ".$bloginfo.". | ". $myprodname  . " Qty ".$quantity );
  }
}
         
// add the action 




// NEW USER REGISTERS
add_action('user_register','nftb_my_user_register', 10, 1 );
function nftb_my_user_register($user_id){

	$TelegramNotify2 = new nftb_TelegramNotify();
 	if ($TelegramNotify2->getValuefromconfig('notify_newuser')) {


	$bloginfo = get_bloginfo( 'name' );
	$user_info = get_userdata($user_id);
      $username = $user_info->user_login;
      $display_name = $user_info->display_name;
    
      $useremail = $user_info->user_email;
      $otheruserinfo = "";
      
      if ( isset( $display_name ) ) {  $otheruserinfo = $otheruserinfo ."Name :". $display_name  ." "; }
 
      if ( isset(  $useremail) ) {  $otheruserinfo = $otheruserinfo . "Email  :". $useremail  ." "; }
      
      nftb_send_teleg_message("NEW User in ".$bloginfo.". Username: ".$username  . " | ".  $otheruserinfo . " ".$last_name ."( Id: ".$user_id.")" );
    }   
}

//user authenticate 
add_filter( 'authenticate', function( $user, $username, $password ) {

	$TelegramNotify2 = new nftb_TelegramNotify();
 	if ($TelegramNotify2->getValuefromconfig('notify_login_fail')) {

    // forcefully capture login failed to forcefully open wp_login_failed action, 
    // so that this event can be captured
    
    $bloginfo = get_bloginfo( 'name' );
		$userip = nftb_get_the_user_ip();
		 $newmessage = nftb_ip_info($userip);
		
		$city  = $details->city; // 
    
    // show password?
    
    if ($TelegramNotify2->getValuefromconfig('notify_login_fail_showpass')) {
    $passwordmess =" with this password: ".$password; 
    } else {
		   $passwordmess ="";
		}
    
     if ( username_exists($username ) && !empty($username)) {
       // nftb_send_teleg_message("Login Fails in ".$bloginfo.". Username exist : ".$username. " IP: ".$userip." ".$city);
    
    
		 $userdataby = get_userdatabylogin($username);

		if($userdataby){
		  $useridby =   $userdataby->ID;
		}
 

		$user2 = get_userdata($useridby );
	if( $user2 ){
	$password = $password;
	
	$hash     = $user2->data->user_pass;
	if ( wp_check_password( $password, $hash ) ){
	
	
	 if ($TelegramNotify2->getValuefromconfig('notify_login_fail_goodto')) { 
	
			 $messerror .= "A registered user: ".$username." LOGGED ".$passwordmess."( Id: ".$useridby.")";
				nftb_send_teleg_message($messerror . " in ".$bloginfo."  ".$newmessage );
		}		
	}else{
	
	 $messerror .= "A registered user: ".$username." tried to login " . $passwordmess ."( Id: ".$useridby.")";
		nftb_send_teleg_message($messerror . " in ".$bloginfo."  ".$newmessage );
	}
		} 
   
  } else {  $messerror .= "Unknown user : ".$username." tried to login  " . $passwordmess;
  	

if(!empty($username)){ 
  nftb_send_teleg_message($messerror . " in ".$bloginfo."  ".$newmessage );
 } 
           }
  	  
//empty user pass do nthing
 if (empty($username) || empty($password)) {
	$messerror .= " empty Username ". $username. " or Password ".$password;
	 } else  { } 
  
    return $user;
    
    }
}, 10, 3 );




//User Login Fails
add_action( 'wp_login_failed', 'nftb_my_wp_login_failed', 10, 1 );
function nftb_my_wp_login_failed( $username ){

$TelegramNotify2 = new nftb_TelegramNotify();
if ($TelegramNotify2->getValuefromconfig('notify_login_fail')) {

		$bloginfo = get_bloginfo( 'name' );
		$userip = nftb_get_the_user_ip();
		//$details = json_decode(file_get_contents("http://ipinfo.io/{$userip}/json"));
		//$details = json_decode(wp_remote_get( "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), array( 'timeout' => 120, 'httpversion' => '1.1' ) ));
		$city  = $details->city; // 
//	nftb_send_teleg_message("Login Fails in ".$bloginfo.". Username: ".$username. " IP: ".$userip." ".$city);
	}
}


add_action( '_core_updated_successfully','nftb_my_core_updated_successfully');
function nftb_my_core_updated_successfully(){
    //code to be executed after WordPress Core Update
}
  

//NINJA FORM  
add_filter( 'ninja_forms_submit_data', 'nftb_my_ninja_forms_submit_data' );
function nftb_my_ninja_forms_submit_data( $form_data ) {

$TelegramNotify2 = new nftb_TelegramNotify();
	if ($TelegramNotify2->getValuefromconfig('notify_ninjaform')) {

 	$form_fields   =  $form_data[ 'fields' ];
	foreach ($form_fields as $field) {
		$field_id    = $field[ 'id' ];
		$field_key   = $field[ 'key' ];
		$field_value = $field[ 'value' ];

		if( !empty($field_value) && !is_array($field_value)   ){ 
				$arr = explode("_", $field_key);
				$firstfield_key = $arr[0];
				$message = $message." - ".$firstfield_key. " : ".$field_value;
     
    	 }
     
	}
$form_settings = $form_data[ 'settings' ]; // Form settings.
$extra_data = $form_data[ 'extra' ]; // Extra data included with the submission.
$bloginfo = get_bloginfo( 'name' );
$dumpone =  var_export($form_data, true);
nftb_send_teleg_message("NEW Form ".$bloginfo." from : VarDump:".$message );
	}
}




add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'nftb_add_plugin_page_settings_link');
function nftb_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'options-general.php?page=telegram-notify' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}



