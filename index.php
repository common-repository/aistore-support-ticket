<?php
/*
Plugin Name: Aistore Support Ticket
Version:  1.0
Plugin URI: #
Author: susheelhbti
Author URI: http://www.aistore2030.com/
Description: Wordpress Users is a list of all registers users and users profile.  
Contributors: susheelhbti
Donate link: http://www.aistore2030.com/
Tags: Aistore, Aistore Support Ticket, Simple support ticket system, best ticketing system,support ticket system design ,helpdesk , Support Ticket, ticket system 
License: GPLv2


Tested up to: 5.8
Requires PHP: 5.6.20


Stable tag: 1.0.0
Version : 1.0



*/

function aistore_support_ticket_plugin_table_install()
{
    global $wpdb;
    
  
    $table_support_ticket = "CREATE TABLE   IF NOT EXISTS  " . $wpdb->prefix . "support_ticket  (
  ID int(100) NOT NULL  AUTO_INCREMENT,
 title  varchar(100)   NOT NULL,
    description  varchar(100)   NOT NULL,
   category  varchar(100)   NOT NULL,
    user_id int(100) NOT NULL ,
  status  varchar(100)   NOT NULL DEFAULT 'pending',
   created_at  timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (ID)
) ";
    
    
    $table_support_ticket_message = "CREATE TABLE   IF NOT EXISTS  " . $wpdb->prefix . "support_ticket_message  (
  ID int(100) NOT NULL  AUTO_INCREMENT,
 message  text   NOT NULL,
   user_id int(100) NOT NULL ,
    ticket_id  varchar(100)   NOT NULL,
   created_at  timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (ID)
) ";
    
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    dbDelta($table_support_ticket);
      dbDelta($table_support_ticket_message);
}
register_activation_hook(__FILE__, 'aistore_support_ticket_plugin_table_install');

include_once dirname(__FILE__) . '/AistoreWordpressSupportTicket.class.php';

include_once dirname(__FILE__) . '/AistoreSettingsPage.class.php';

include_once dirname(__FILE__) . '/AistoreAdminAjax.php';


add_shortcode('aistore_add_support_ticket', array(
    'AistoreWordpressSupportTicket',
    'aistore_add_support_ticket'
));

add_shortcode('aistore_list_support_ticket', array(
    'AistoreWordpressSupportTicket',
    'aistore_list_support_ticket'
));


add_shortcode('aistore_ticket_details', array(
    'AistoreWordpressSupportTicket',
    'aistore_ticket_details'
));

 
 function aistore_new_modify_user_table_ticket( $column ) {

       
    $column['tickets'] =   _e( 'Tickets', 'aistore' );
    return $column;
}

add_filter( 'manage_users_columns', 'aistore_new_modify_user_table_ticket' );




function aistore_new_modify_user_table_row_ticket( $val, $column_name, $user_id ) {



    switch ($column_name) {

 
        case 'tickets':
      
           $url = admin_url('admin.php'); 
         $link= '<a href="'.$url.'?page=support_ticket_list&id='.$user_id.'">Tickets</a>';

   
       
 return $link;

   
        default:
    }


    return $val;

}


add_filter( 'manage_users_custom_column', 'aistore_new_modify_user_table_row_ticket', 10, 3 );
 