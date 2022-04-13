<?php
//17
if ( ! defined( 'ABSPATH' ) ) exit;


function cleanString($in,$offset=null)
{
    $out = trim($in);
    if (!empty($out))
    {
        $entity_start = strpos($out,'&',$offset);
        if ($entity_start === false)
        {
            // ideal
            return $out;   
        }
        else
        {
            $entity_end = strpos($out,';',$entity_start);
            if ($entity_end === false)
            {
                 return $out;
            }
            // zu lang um eine entity zu sein
            else if ($entity_end > $entity_start+7)
            {
                 // und weiter gehts
                 $out = cleanString($out,$entity_start+1);
            }
            // gottcha!
            else
            {
                 $clean = substr($out,0,$entity_start);
                 $subst = substr($out,$entity_start+1,1);
                 // &scaron; => "s" / &#353; => "_"
                 $clean .= ($subst != "#") ? $subst : " ";
                 $clean .= substr($out,$entity_end+1);
                 // und weiter gehts
                 $out = cleanString($clean,$entity_start+1);
            }
        }
    }
    return $out;
}







//set notify time out
function nftb_NotifyA() {
 
       if(class_exists('nftb_NotifyA')){
			$NotifyA= new nftb_NotifyA();
			return $NotifyA->nftb_NotifyA();
			 } else {
			
			return false;
			} 
}

function nftb_ip_info($userip) {
$url      = 'http://ip-api.com/json/'.$userip;

 $fb = wp_remote_get( $url  );
 if( ! is_wp_error( $fb ) ) {

 $body = json_decode( wp_remote_retrieve_body( $fb ) );
 $city  = $body->country; // 
 $url = "https://www.google.com/maps/search/?api=1&query=".$body->lat.",".$body->lon;
  return $newmessage. " from City: ".$body->city. ", Country: ".$body->country. " , Region: ".$body->regionName.", Isp: ".$body->isp." - ".$body->as .", Maps: ".$url;

 }
}

function nftb_get_the_user_ip() {
if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
//check ip from share internet
$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
//to check ip is pass from proxy
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
$ip = $_SERVER['REMOTE_ADDR'];
}
return apply_filters( 'wpb_get_ip', $ip );
}

function check_plug($plug){

	if ( is_plugin_active($plug) ) {
  return true;
	} else {
  return false;
	}
	
}



/**
 * Add the field to the checkout
 */
add_action( 'woocommerce_after_order_notes', 'nftb_checkout_field' );
function nftb_checkout_field( $checkout ) {
	
	$TelegramNotify = new nftb_TelegramNotify();
	$notify_woocomerce_checkoutfield =  $TelegramNotify->getValuefromconfig('notify_woocomerce_checkoutfield'); 
	$notify_woocomerce_checkoutfield_txt =  $TelegramNotify->getValuefromconfig('notify_woocomerce_checkoutext'); 
 if (  $notify_woocomerce_checkoutfield) {
    echo '<div id="nftb_checkout_field"><h4>' . __('Telegram') . '</h4>'.$notify_woocomerce_checkoutfield_txt;

    woocommerce_form_field( 'nftb_telegramnickname', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Telegram Nickname'),
        'placeholder'   => __('@YourTelegramNickname'),
        ), $checkout->get_value( 'nftb_telegramnickname' ));

    echo '</div>';
	}
}

//Crea Telegram meta per prdine
add_action( 'woocommerce_checkout_update_order_meta', 'nftb_update_order_meta' );

function nftb_update_order_meta( $order_id ) {


    if ( ! empty( $_POST['nftb_telegramnickname'] ) ) {
        update_post_meta( $order_id, 'Telegram', sanitize_text_field( $_POST['nftb_telegramnickname'] ) );
    }
}



////aggiungi alback end il telegram
add_action( 'woocommerce_admin_order_data_after_billing_address', 'nftb__field_display_admin_order_meta', 10, 1 );

function nftb__field_display_admin_order_meta($order){

$tlgruser = get_post_meta( $order->id, 'Telegram', true );
 if ( ! empty( $tlgruser ) ) {
        echo '<p><strong>'.__('Telegram').':</strong> <a href="https://t.me/'.$tlgruser.'">' .$tlgruser  . '</a></p>';
    }
   
}



//Mostra notice con condizioni
add_action('admin_notices', 'nftb_admin_notice');

function nftb_admin_notice() {
	global $current_user ;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
       
       $all_meta_for_user = get_user_meta($user_id ) ;
       
      // delete_user_meta($user_id, 'nftb_ignore_notyyy');
       // echo "jjj". $all_meta_for_user['nftb_ignore_notyyy'][0];
        
        
       $datetime1 = date_create(); // now
		$datetime2 = date_create($all_meta_for_user['nftb_ignore_notyyy'][0]);
		$interval = date_diff($datetime1, $datetime2);
		$days = $interval->format('%d'); // the time between your last login and now in da
       
      //echo "days". $days;
      global $pagenow;
    
    $current_rel_uri = add_query_arg( NULL, NULL );
   
    //show uot of option page
   // if ( !strpos( $current_rel_uri, 'telegram-notify' )) {   
    
    if ( empty($all_meta_for_user['nftb_ignore_notyyy'][0]) || $days >30){
        echo '<div class="updated" ><p>'; 

        printf(__('<img src="https://ps.w.org/notification-for-telegram/assets/icon-128x128.jpg?rev=2383266" ><h3><a href="https://it.wordpress.org/plugins/notification-for-telegram/#reviews" target="_blank">'.__('Please remeber to RATE Notification for Telegram!!' , 'notification-for-telegram' ).'</a><h3><a href="%1$s">'.__('Hide Notice for now' , 'notification-for-telegram' ).'</a>'), '?page=telegram-notify&nftb_nag_ignore=0');
        echo  "</p></div>";
	//}
}}


//dismiss button
add_action('admin_init', 'nftb_nag_ignore');
function nftb_nag_ignore() {

       
	global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['nftb_nag_ignore']) && '0' == $_GET['nftb_nag_ignore'] ) {
        
         add_user_meta($user_id, 'nftb_ignore_notyyy', date('d.m.Y',strtotime("-0 days")), true);
          
     
	}
}






function nftb_send_requestupdate2($message) {


 		nftb_send_teleg_message("-".$message);
  
}



function nftb_plugin_update_message( $data, $response ) {
	if( isset( $data['upgrade_notice'] ) ) {
		printf(
			'<div class="update-message">%s</div>',
			wpautop( $data['upgrade_notice'] )
		);
	}
}

$filez   = basename( __FILE__ );
$folderz = basename( dirname( __FILE__ ) );
$hookz = "in_plugin_update_message-{$folderz}/{$filez}";
add_action( $hookz, 'nftb_plugin_update_message', 10, 2 ); // 10:priority, 2:arguments #


//add_action( 'in_plugin_update_message-your-plugin/your-plugin.php', 'nftb_plugin_update_message', 10, 2 );




//mailchim subscribe
add_action( 'mc4wp_form_subscribed', function( MC4WP_Form $form ) {
 	$TelegramNotify = new nftb_TelegramNotify();
	$notify_mailchimp_sub =  $TelegramNotify->getValuefromconfig('notify_mailchimp_sub'); 
	
		 if( isset( $notify_mailchimp_sub) ) {
  
		  $data = $form->get_data();	
			// use email as username
			$username = $data['EMAIL'];	
		nftb_send_teleg_message(__('New Mailchimp subscribed : ' , 'notification-for-telegram' ).$username); 
  
  	}
  
});



//mailchiim unsuscribe
add_action( 'mc4wp_form_unsubscribed', function(MC4WP_Form $form) {

	$TelegramNotify = new nftb_TelegramNotify();
	$notify_mailchimp_unsub =  $TelegramNotify->getValuefromconfig('notify_mailchimp_unsub'); 

 		if( isset( $notify_mailchimp_unsub ) ) {

  			$data = $form->get_data();
			// use email as username
			$username = $data['EMAIL'];
  
  			nftb_send_teleg_message(__('New Mailchimp Unsubscribed : ' , 'notification-for-telegram' ).$username); 
  			
			}
});


//commenti to implemnet
function nftb_show_message_function( $comment_ID, $comment_approved ) {
    if( 1 === $comment_approved ){
        //function logic goes here
    }
}
add_action( 'comment_post', 'nftb_show_message_function', 10, 2 );




function nftb_send_teleg_message( $messaggio) {

	$arg_list = func_get_args();

	
	//preapra le variabili  $message , $urlname, $urllink, $eventualechtid
	//ex  nftb_send_teleg_message( $defmessage, 'EDIT ORDER N. '.$order_id ,$editurl,'');
		
    $messaggio = cleanString($arg_list[0]);
    
    //Ordina le variabili 
    $eventualechtid = $arg_list[3];
    $eventualechtid = isset($eventualechtid ) ? $eventualechtid  : null;
    $urlname =  $arg_list[1];
    $urlname = isset($urlname) ? $urlname  : null;
    $urllink=  $arg_list[2];
    $urllink = isset($urllink) ? $urllink  : null;
    
	$TelegramNotify = new nftb_TelegramNotify();
	$token =  $TelegramNotify->getValuefromconfig('token_0');
	$chatids_ = $TelegramNotify->getValuefromconfig('chatids_');
	
	//se arrivano diferrenti chatid usale
	if ( ( $eventualechtid ) ) { $chatids_ = $eventualechtid; }
	
	
	$apiToken = $token ;
	$blog_title = get_the_title( $post_id );
	$users=explode(",",$chatids_);
	
	 
	
	foreach ($users as $user) {

	if (empty($user)) continue;

		if (( $urllink && $urlname) ) { 
	
	
		//MESSAGGIO CON LINK
 		$keyboard = array(
		"inline_keyboard" => array(array(array(
		"text" => __(  $urlname , 'notification-for-telegram' ),
		"url" => $urllink ) )) );

		$data = [
        'chat_id' => $user,
        'text' => __(  $messaggio , 'notification-for-telegram' ),
        'reply_markup' => json_encode($keyboard)  ];
	
		 }	else {
		 //MESSAGGIO SENZALINK
 		$data = [
        'chat_id' => $user,
        'text' => $messaggio ];
    	 // $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	
 		}
	$response = wp_remote_get( "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), array( 'timeout' => 120, 'httpversion' => '1.1' ) );
	
	
	
	
	} //fine for

//INVITA x RATE  RATE
$rand = rand(1, 16);
	 if( $rand == 8  ) {


$users=explode(",",$chatids_);
	
	 
	
	foreach ($users as $user) {

	if (empty($user)) continue;

	 
	 $keyboard = array(
"inline_keyboard" => array(array(array(
"text" => __(  'Rate this Plugin !' , 'notification-for-telegram' ),
"url" => "https://it.wordpress.org/plugins/notification-for-telegram/"
)

))
);
	 
	 
	 $data = [
        'chat_id' => $user,
        'text' => __(  "I'm really 😋 happy you are using my plugin !! 🙏
If you have time remember to Rate me on wordpress rep ! !! 🙌 " , 'notification-for-telegram' ),
        'reply_markup' => json_encode($keyboard) 


 ];
	$response = wp_remote_get( "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), array( 'timeout' => 120, 'httpversion' => '1.1','disable_web_page_preview'=>True ) );

	}
	}

 }			






?>
