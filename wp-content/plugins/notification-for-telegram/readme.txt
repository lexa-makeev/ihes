=== Notification for Telegram ===
Contributors: rainafarai
Donate link: https://www.paypal.com/paypalme/rainafarai
Tags: Telegram,post, contact form, CF7, mailchimp, MC4WP, ninjaform, woocommerce, orders, users registration, fail login, shortcode, security
Requires at least: 4.0
Tested up to: 5.8.1
Stable tag: trunk
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Receive Telegram messages notification when: New forms sent, New users or login fails, Woocommerce: orders, cart and lowstock, Pending posts, Mailchimp, send custom message with shortcode

== Description ==

Send Telegram messages notification: 
* When receive a new order in Woocommerce.
* When a Woocommerce order change status.
* New field in Woocommerce checkout page let customers add the own telegram nickname
* Low Stock Product nofications when a product is low stock conditions.
* Shows Telegram Nick link in admin order details page when present
* When receive new forms (supports WPForm , CF7 and Ninjaform)(Var_dump)
* When new user subcribes  or unsubscribes to mailchimp. MC4WP integration
* When new user registers.
* When users login fail.
* When someone adds a product in the Woocommerce cart.
* When a new Pending posts is received. (works with any post type)
* Say function to speak to make the bot say Something to the people
* Cron job detect and notify when Plugins & Core need to update. 
* Send custom message with Shortcode anywhere in your WP.
* Should Work on Multisite


You can enable/disable every notification in the Plugin settings page.

After updating to version> 2 check the settings again, if you have problems in the update uninstall and reinstall the plug sorry for the problem

To configure the plugin, you need a valid Telegram API token. Its easy to get starting a Telegram Bot.
You can learn about obtaining  tokens and generating new ones in 
[this document](https://core.telegram.org/bots#6-botfather/ "Obtaining tokens and generating new ones")  
or follow the info in [this post](https://medium.com/shibinco/create-a-telegram-bot-using-botfather-and-get-the-api-token-900ba00e0f39 "Create a Telegram bot using BotFather and Get the Api Token")  

You also need at least one "chatid" number, that is the recipient to the message will be send. To know you personal chatid number, search on telegram app for "@get_id_bot" or  
[click here ](https://telegram.me/get_id_bot/ "@get_")  

Once You got the 2 fields save the configuration and try the "TEST" button .. you should receive a message in you telegram : "WOW IT WORKS" !! If not, check token and chatid fields again for the correct values.

this plugin is relying on a 3rd party service to geolocate the Ip address https://ip-api.com/
https://ip-api.com/docs/legal  to see the services’ a terms of use and/or privacy policies


SHORTCODE EXAMPLE

`[telegram_mess  message="Im so happy" chatids="0000000," token="000000000:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA" showsitename="1" showip="1" showcity="1" ]`


SHORTCODE OPTIONS:

* message : Your message to be sent. Example (message="hello world")

* chatids : Recipient(s) who will receive your message separated by comma (example chatids="0000000,11111111") If empty , the shortcode will use default value in Plugin option page.

* token:  The token looks something like 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11 
if empty , the shortcode will use default value in Plugin option page.

* showsitename: if set to "1" appends sitename after the message. Defaultvalue is "0" Example (showsitename="1")

* showip: if set to "1" appends user ip address after the message. Default value is "0" Example (showip="1")

* showcity: if set to "1" appends user city name after the message. Default value is "0" Example (showcity="1")


USE SHORTCODE IN YOU PHP CODE

`<?php

$date = date("d-m-Y");

do_shortcode('[telegram_mess  message="'.$date .'" chatids="0000000," token="000000000:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA" showsitename="1" showip="1" showcity="1" ]'); 

?>`

Suggestions for other Notification, hooks  and others plug integrations are Welcome !! 

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= How to obtain Token? =

When You create a telegram bot you will get a Token. 
BotFather @botfather is the one bot to rule them all. It will help you create new bots and change settings for existing ones.

search for @botfather

Creating a new bot
Use the /newbot command to create a new bot. The BotFather will ask you for a name and username, then generate an authorization token for your new bot.

The name of your bot is displayed in contact details and elsewhere.

The Username is a short name, to be used in mentions and t.me links. Usernames are 5-32 characters long and are case insensitive, but may only include Latin characters, numbers, and underscores. Your bot's username must end in 'bot', e.g. 'tetris_bot' or 'TetrisBot'.

The token is a string along the lines of 110201543:AAHdqTcvCH1vGWJxfSeofSAs0K5PALDsaw that is required to authorize the bot and send requests to the Bot API. Keep your token secure and store it safely, it can be used by anyone to control your bot.

Generating an authorization token.
If your existing token is compromised or you lost it for some reason, use the /token command to generate a new one.



= How to obtain the chat_id of a private Telegram channel? =

The easiest way is to invite @get_id_bot in your chat and then type:

/my_id @get_id_bot


= Can i insert more than one recipient chatid? =

Yes you can add more than one chattid  separated by a comma (,)
both in option page and in the shortcode.



== Screenshots ==

1. This is the Global option page in you Dashboard. Enter Token Chatid
2. Choose which notification you want to receive 
3. A shortcode example.
4. Post & Shortcode result on your Mobile app  
5. Login fails result on your Mobile app
6. Woocommerce Setting Tab
7. Cron Setting Tab keep update your system 

== Changelog ==

= 2.4 =
- Added Support for WPFORM : all fields in you telegram notification
- New Option in Woocommerce Tab : Hide/view Billing Information
- New Option in Woocommerce Tab : Hide/view Shipping Information
- Small UI fix 

= 2.3 =
- Fix warning on PHP 8

= 2.2 =
- New option to select the woocommerce trigger for the notification with 3 different actions:
	woocommerce_checkout_order_processed / woocommerce_payment_complete / woocommerce_thankyou
- Show items price with tax or not 
- Fixed activation notice error 

= 2.0 =
New Backend UserGUI Tab division for better User Experience
Full fields Order Notification (Items shipment, billing, ispaid?, and many order details)
Low Stock Product Enable nofications when a product is low stock conditions.
Create a input field in wc check-out page for Telegram nickname.
Say function to speak to make the bot say Something to the people
MC4WP Mailchimp for WordPress plugin integration send a notification when new user subcribes to mailchimp or unsubscribes
Cron message Setup a Cron job to keep updated about Plugins & Core Update 
Added Emoji to messages

= 1.6 =
now you can enable a new field in Woocommerce checkout page let customers add his telegram nickname

= 1.4 = 
Fix new order receaved and Order status change
No duplication now !!

= 1.3 =
Fix message text in shortcode was blank before


= 1.2 =
Add new option on woocommerce notification : only on new orders or on any order status change

= 1.1 =
add icons

= 1.0 =
Initial release



== Upgrade Notice ==

