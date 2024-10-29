<?php


class AistoreWordpressSupportTicket {
    
    public static function aistore_ticket_details(){
        
        if ( !is_user_logged_in() ) {
   
  return "Please login and continue";
    
 
}
global $wpdb;

       $tid= sanitize_text_field($_REQUEST['eid']);
       
       $user_id = get_current_user_id();
       
       
   if(isset($_POST['submit']) and $_POST['action']=='closemark')
{

if ( ! isset( $_POST['aistore_nonce'] ) 
    || ! wp_verify_nonce( $_POST['aistore_nonce'], 'aistore_nonce_action' ) 
) {
   return  _e( 'Sorry, your nonce did not verify', 'aistore' ) ;
    
}

$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}support_ticket
    SET status =  'closed'   WHERE ID = '%d' and and user_id=%d",  $tid, $user_id ) );
   

}

       if(isset($_POST['submit']) and $_POST['action']=='supportmsg')
{

if ( ! isset( $_POST['aistore_nonce'] ) 
    || ! wp_verify_nonce( $_POST['aistore_nonce'], 'aistore_nonce_action' ) 
) {
   return  _e( 'Sorry, your nonce did not verify', 'aistore' ) ;
    
} 

$message=sanitize_text_field($_REQUEST['message']);
 



$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}support_ticket_message ( message, ticket_id,user_id ) VALUES (  %s, %s, %s )", 
array( $message, $tid,$user_id ) ) );



    	 $list_ticket_page_url  =  esc_url( add_query_arg( array(
    'page_id' => get_option('list_ticket_page_id') ,
), home_url() ) );

      
// email to user 
$sender_email = get_the_author_meta( 'user_email', get_current_user_id() );

$details="Support Ticket";

$to = $sender_email;

$subject =$details;


	 $list_ticket_page_url =  esc_url( add_query_arg( array(
    'page_id' => get_option('list_ticket_page_id') ,
	'eid'=> $tid,
), home_url() ) );


 $body="Hello, <br>
 
     <h2>You have successfully created ticket message for the ticket ".$tid." </h2><br> Below is complete detail of the ticket <br/>
    ". "<br>Ticket ID is:".$tid.
     "<br>Ticket Details:<br>".
         $list_ticket_page_url ;
         
    
  $body  .="<br /> If you are not registered in the portal kindly visit my account page and register.";
  

  $headers = array('Content-Type: text/html; charset=UTF-8');
     wp_mail( $to, $subject, $body, $headers );


// email to admin

$admin_email = get_option( 'admin_email' );


$to = $admin_email;


$subject =$details;




 $body="Hello, <br>
     <h2> ".$sender_email." have created ticket message you for the ticket ".$tid." </h2><br> Below is complete detail of the ticket <br/>
    ".
     
     "<br>Ticket ID is : ".$tid.
     "<br>Ticket Details: <br>".
         $list_ticket_page_url ;
    
    
    
  $body  .="<br /> <strong>If you are not registered in the portal kindly visit my account page and register.</strong>";
  
  
  $headers = array('Content-Type: text/html; charset=UTF-8');
     wp_mail( $to, $subject, $body, $headers );
     
     
     
     
     ?>
    
   
<meta http-equiv="refresh" content="0; URL=<?php echo esc_html($list_ticket_page_url) ; ?>" /> 
<?php

}



       
  $ticket = $wpdb->get_row($wpdb->prepare( "SELECT * FROM {$wpdb->prefix}support_ticket WHERE ID=%d and user_id=%d ",$tid,$user_id ));
  
  
  
   ob_start();
       
       ?>
       
   
	      <div><div>
	          
	          
	      <div class="alert alert-success" role="alert">
 <strong><?php   _e('Ticket Status ', 'aistore' ); ?>  <?php echo esc_attr($ticket->status);?></strong>
  </div>
	      <?php
      
      printf(__( "Ticket ID  :  %d", 'aistore' ),$ticket->ID );
  
 
  printf(__( "Category :  %s", 'aistore' ),$ticket->category."<br>"); 
 
 
 if($ticket->status==='pending')   {
?><br><br>
   <form method="POST" action="" name="closemark" enctype="multipart/form-data"> 
 
<?php wp_nonce_field( 'aistore_nonce_action', 'aistore_nonce' ); ?>

  <input  type="submit"  name="submit" value="<?php  _e( 'Close', 'aistore' ) ?>">
  <input type="hidden" name="action" value="closemark" />
</form> <br><br>
 <?php }  ?>
 
 
 

     
         
  <h4><?php   _e('Add Support Message', 'aistore' ); ?></h4>
   <div >
      <form method="POST" action="" name="supportmsg" enctype="multipart/form-data"> 
 
<?php wp_nonce_field( 'aistore_nonce_action', 'aistore_nonce' ); ?>


  
 

   <div class="mb-3">   <label><?php   _e('Message', 'aistore' ); ?></label>

   <?php
   
      $editor=array(
    'tinymce'       => array(
        'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright, link,unlink,  ',
        'toolbar2'      => '', 
        'toolbar3'      => ''  
       
           ),   
         'textarea_rows' => 1 ,
    'teeny' => true,
    'quicktags' => false,
     'media_buttons' => false 
);
  
$content   = '';
$editor_id = 'message';


   $short_description =$editor;


   
wp_editor( $content, $editor_id,   $short_description);
 
?></div>
   

  
<br><br>
  <input  type="submit"  name="submit" value="<?php  _e( 'Submit', 'aistore' ) ?>">
  <input type="hidden" name="action" value="supportmsg" />
</form></div><br><br>ggg
<?php

 

   $results = $wpdb->get_results( 
                     $wpdb->prepare("SELECT * FROM {$wpdb->prefix}support_ticket_message WHERE ( ticket_id in (select id from {$wpdb->prefix}support_ticket   where user_id=%d ) ) and  ticket_id=%d  order by id desc limit 100", $user_id, $tid) 

                 );   

 

 
 if($results==null)
	{
	      echo "<div class='no-result'>";
	      
	     _e( 'Ticket Message List Not Found', 'aistore' ); 
	  echo "</div>";
	}
	else{
   
 
     
  ?>
  <h3><u><?php   _e( 'Ticket Message List', 'aistore' ); ?></u> </h3><br><br>
    <table class="table">
     
     

    <?php 
    
     
   
    foreach($results as $row):
 
 ?>
      <tr>	    <?php echo esc_attr($row->message) ; ?>  <br>
		   <?php echo esc_attr($row->created_at) ; ?> 

            </tr>
            <br><br>
    <?php endforeach;
	
	}?>

    </table>
	
  <?php
   return ob_get_clean();    

    }
    
public static function aistore_list_support_ticket(){
if ( !is_user_logged_in() ) {
   
    return "Please login and continue";
    
    
 
}
    ob_start();
$user_id = get_current_user_id();


global $wpdb;

  $results = $wpdb->get_results( 
                     $wpdb->prepare("SELECT * FROM {$wpdb->prefix}support_ticket WHERE user_id=%s  order by id desc limit 100", $user_id) 

                 );

 
 
 if($results==null)
	{
	      echo "<div class='no-result'>";
	      
	     _e( 'Ticket List Not Found', 'aistore' ); 
	  echo "</div>";
	}
	else{
   
 
     
  ?>
  
    <table class="table">
     
        <tr>
      
    <th><?php   _e( 'Id', 'aistore' ); ?></th>
        <th><?php   _e( 'Title', 'aistore' ); ?></th>
         <th><?php   _e( 'Category', 'aistore' ); ?></th>
       
		
		    <th><?php   _e( 'Date', 'aistore' ); ?></th>
		 
</tr>

    <?php 
    
     
   
    foreach($results as $row):
  
  
 ?>
 
 
      
    
      <tr>
           
		 
		   
		   
		   <td> <?php 
		   
	

$details_escrow_page_id_url= esc_url( add_query_arg( 'eid', $row->ID, get_permalink(  get_option('details_ticket_page_id') ) ) );

		    
 ?>  
 
 
 <a href="<?php echo $details_escrow_page_id_url; ?>" >

		   <?php echo esc_attr($row->ID) ; ?> </a> </td>
		   
		   
		   
		   
		   
		   
		   
  <td> 		   <?php echo esc_attr($row->title) ; ?> </td>
  
		   
		  	 
		   <td> 		   <?php echo esc_attr($row->category) ; ?> </td>

		    <td> 		   <?php echo esc_attr($row->created_at) ; ?> </td>

            </tr>
    <?php endforeach;
	
	}?>

    </table>
	
	
	
	
	

    <?php 
 return ob_get_clean();   


}


public static function aistore_add_support_ticket(){
    
  if ( !is_user_logged_in() ) {
   
    return "Please login and continue";
    
    
 
}
    ob_start();

      $editor=array(
    'tinymce'       => array(
        'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright, link,unlink,  ',
        'toolbar2'      => '', 
        'toolbar3'      => ''  
       
           ),   
         'textarea_rows' => 1 ,
    'teeny' => true,
    'quicktags' => false,
     'media_buttons' => false 
);
    $user_id=get_current_user_id();
    ?>
    
 <h3><?php _e("Add Ticket", "blank"); ?></h3> 
<?php
   
if(isset($_POST['submit']) and $_POST['action']=='support')
{

if ( ! isset( $_POST['aistore_nonce'] ) 
    || ! wp_verify_nonce( $_POST['aistore_nonce'], 'aistore_nonce_action' ) 
) {
   return  _e( 'Sorry, your nonce did not verify', 'aistore' ) ;
    
} 


 


$title=sanitize_text_field($_REQUEST['title']);

$description=sanitize_text_field($_REQUEST['description']);
 
 $category=sanitize_text_field($_REQUEST['category']);


 



global $wpdb;   

$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}support_ticket ( title, category, description,user_id ) VALUES ( %s, %s, %s, %s )", 
array( $title, $category, $description,$user_id ) ) );

	 $list_ticket_page_url  =  esc_url( add_query_arg( array(
    'page_id' => get_option('list_ticket_page_id') ,
), home_url() ) );

  $tid = $wpdb->insert_id;
    
   
// email to user 
$sender_email = get_the_author_meta( 'user_email', get_current_user_id() );
$details="Support Ticket";
$to = $sender_email;
$subject =$details;


	 $list_ticket_page_url =  esc_url( add_query_arg( array(
    'page_id' => get_option('list_ticket_page_id') ,
	'eid'=> $tid,
), home_url() ) );


 $body="Hello, <br>
 
     <h2>You have successfully created for the ticket ".$tid." </h2><br> Below is complete detail of the ticket <br/>
    ". "<br>Ticket ID is:".$tid.
     "<br>Ticket Details:<br>".
         $list_ticket_page_url ;
    
  $body  .="<br /> If you are not registered in the portal kindly visit my account page and register.";
  

  $headers = array('Content-Type: text/html; charset=UTF-8');
     wp_mail( $to, $subject, $body, $headers );


// email to admin

$admin_email = get_option( 'admin_email' );
$to = $admin_email;
$subject =$details;




 $body="Hello, <br>
     <h2> ".$sender_email." have created you for the ticket ".$tid." </h2><br> Below is complete detail of the ticket <br/>
    ".
     
     "<br>Ticket ID is : ".$tid.
     "<br>Ticket Details: <br>".
         $list_ticket_page_url ;
    
  $body  .="<br /> <strong>If you are not registered in the portal kindly visit my account page and register.</strong>";
  
  $headers = array('Content-Type: text/html; charset=UTF-8');
     wp_mail( $to, $subject, $body, $headers );
     
     
     ?>
<meta http-equiv="refresh" content="0; URL=<?php echo esc_html($list_ticket_page_url) ; ?>" /> 
<?php



}else{   
     

 
      ?>
      <div >
      <form method="POST" action="" name="support" enctype="multipart/form-data"> 
 
<?php wp_nonce_field( 'aistore_nonce_action', 'aistore_nonce' ); ?>


    <br>      
        <div class="mb-3">   
<label><?php  _e('Title', 'aistore' ); ?></label><br>
  <input class="input" type="text" id="title" name="title" required></div><br><br>
  
  
 
    <div class="mb-3">   
<label><?php   _e('Category', 'aistore' ); ?></label><br>
  <select name="category" id="category">
<option value="<?php   _e('Disputed', 'aistore' ); ?>"><?php   _e('Disputed', 'aistore' ); ?>  </option>
<option value="<?php   _e('Support', 'aistore' ); ?>"><?php   _e('Support', 'aistore' ); ?></option>
<option value="<?php   _e('Other', 'aistore' ); ?>"><?php   _e('Other', 'aistore' ); ?> </option>
  </select></div><br><br>
  
  
 

   <div class="mb-3">   <label><?php   _e('Description', 'aistore' ); ?></label>

   <?php
  
$content   = '';
$editor_id = 'description';


   $short_description =$editor;


   
wp_editor( $content, $editor_id,   $short_description);
 
?></div>
   

  
<br><br>
  <input  type="submit"  name="submit" value="<?php  _e( 'Submit', 'aistore' ) ?>">
  <input type="hidden" name="action" value="support" />
</form></div>
      <?php
   
}
   return ob_get_clean(); 
}

} 