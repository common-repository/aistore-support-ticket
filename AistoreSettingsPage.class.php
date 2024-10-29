<?php




class AistoreSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'aistore_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'aistore_page_register_setting' ) );
        
    

	
    }

    /**
     * Add options page
     */
public function aistore_add_plugin_page()
{
    // This page will be under "Settings"
  
    
    
    
    
    add_menu_page(__( 'Support Ticket', 'aistore' ),  __('Support Ticket', 'aistore' ), 'administrator', 'support_ticket_list');
    

    add_submenu_page('support_ticket_list', __('Ticket List','aistore'), __('Ticket List','aistore'), 'administrator', 'support_ticket_list', array(
        $this,
        'aistore_support_ticket_list'
    ));
    

    
    add_submenu_page('support_ticket_list', __('Ticket List','aistore'), __('Live Chat','aistore'), 'administrator', 'support_ticket_live_chat', array(
        $this,
        'aistore_support_live_chat'
    ));
    

    
    add_submenu_page('support_ticket_list', __('Setting','aistore'), __('Setting','aistore'), 'administrator', 'aistore_page_setting', array(
        $this,
        'aistore_page_setting'
    ));
    
    
}



// aistore_support_ticket_list

function aistore_support_ticket_list()
{
	

global  $wpdb;


  if (isset($_REQUEST['tid']))
        { 
   $this-> aistore_ticket_details();
return "";
        }
        
         else

            {
                $tid = 0;
                $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}support_ticket" 
                 );  

  }

     
  ?>
    <h1> <?php  _e( 'All Ticket List', 'aistore' ) ?> </h1>
	
	

      

    <?php 
    
    if($results==null)
	{
	    
	     _e( "No Ticket Found", 'aistore' );
	
	}
	else{
	    
	    
	    
	    
	echo ' <table class="widefat fixed striped">'; 
     
    foreach($results as $row): 
        
         

    ?> 
      <tr>

		   
		    <td> <?php 
		   
	
 

$details_escrow_page_id_url=  admin_url( 'admin.php?page=support_ticket_list&tid='.esc_attr($row->ID)   ); ?>
		  
		   
		    <td> <?php 
		   
	
 

$dsupport_ticket_live_chat_url=  admin_url( 'admin.php?page=support_ticket_live_chat&tid='.esc_attr($row->ID)   ); ?>
		  
		  
 
 
 
 <a href="<?php echo esc_url($details_escrow_page_id_url); ?>" >

		   <?php echo esc_attr($row->ID) ; ?> Legacy section </a> </td>
		   
 	   
		    <td>  
 
 <a href="<?php echo esc_url($dsupport_ticket_live_chat_url); ?>" >

		   <?php echo esc_attr($row->ID) ; ?> Live chat  </a> </td>
		   
		   
		   
		   
	 <td> 	 
		   <?php echo esc_attr($row->ID) ; ?> </td>
		  
		   
		   <td> 		   <?php echo esc_attr($row->title) ; ?> </td>
		  
		   <td> 		   <?php echo esc_attr($row->status) ; ?> </td>
		  
		   
		   <td> 		   <?php echo esc_attr($row->category) ; ?> </td>
		   <td> 		   <?php echo esc_attr($row->created_at) ; ?> </td>
		  
              
            </tr>
    <?php endforeach;
    
    echo '</table>';
    
	}
 
}





 public   function aistore_ticket_details(){
        
   
global $wpdb;

       $tid= sanitize_text_field($_REQUEST['tid']);
       
       $user_id = get_current_user_id();
       
       
          if(isset($_POST['submit']) and $_POST['action']=='closemark')
{

if ( ! isset( $_POST['aistore_nonce'] ) 
    || ! wp_verify_nonce( $_POST['aistore_nonce'], 'aistore_nonce_action' ) 
) {
   return  _e( 'Sorry, your nonce did not verify', 'aistore' ) ;
    
}

$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}support_ticket
    SET status =  'closed'   WHERE ID = '%d' and   user_id=%d",  $tid, $user_id ) );
   

}


     if(isset($_POST['submit']) and $_POST['action']=='supportmsg')
{

if ( ! isset( $_POST['aistore_nonce'] ) 
    || ! wp_verify_nonce( $_POST['aistore_nonce'], 'aistore_nonce_action' ) 
) {
   return  _e( 'Sorry, your nonce did not verify', 'aistore' ) ;
    
} 

$message=sanitize_textarea_field($_REQUEST['message']);
$user_id = get_current_user_id();



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
     
     
     
     
  
}





  $ticket = $wpdb->get_row($wpdb->prepare( "SELECT * FROM {$wpdb->prefix}support_ticket WHERE ID=%d  ",$tid  ));
  
  
  
  
       
       ?>
       
   
	      <div class="card"> 
	      <div>
	          
	          <p>For anything plz email wordpress@aistore2030.com </p>
	      <div class="alert alert-success" role="alert">
 <strong><?php   _e('Ticket Status ', 'aistore' ); ?>  <?php echo esc_attr($ticket->status);?></strong>
  </div>

	      <?php
      
      printf(__( "Ticket ID :  %d", 'aistore' ),$ticket->ID );
  
  
  printf(__( "<br />Category :  %s", 'aistore' ),$ticket->category."<br>");
  
 

 
 if($ticket->status==='pending')   {
?>

<br><br>
   <form method="POST" action="" name="closemark" enctype="multipart/form-data"> 
 
<?php wp_nonce_field( 'aistore_nonce_action', 'aistore_nonce' ); ?>

  <input  type="submit"  name="submit" value="<?php  _e( 'Close Ticket ', 'aistore' ) ?>">
  <input type="hidden" name="action" value="closemark" />
</form>
 <?php }  ?>
 
 
 
 
  <br><br>

       
     
         
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
  <input  type="submit"  name="submit" value="<?php  _e( 'Add Message', 'aistore' ) ?>">
  <input type="hidden" name="action" value="supportmsg" />
</form></div><br><br>
<?php





  $results = $wpdb->get_results($wpdb->prepare( "SELECT m.*,u.user_email FROM {$wpdb->prefix}support_ticket_message m , {$wpdb->prefix}users u WHERE m.ticket_id=%d and (m.user_id =u.id) order by id desc  ",$tid  ));
  
  
  
  
 
 if($results==null)
	{
	      echo "<div class='no-result'>";
	      
	     _e( 'Ticket Message List Not Found', 'aistore' ); 
	  echo "</div>";
	}
	else{
   
 
     ?>
 <h3><u><?php   _e( 'Ticket Message List', 'aistore' ); ?></u> </h3><br><br>
    
     <?php 
   
    foreach($results as $row):
 
 ?>
  	    <?php echo  html_entity_decode(   $row->message  )   ; ?>  <br> <br> <br>
	  <strong>	   <?php echo esc_attr($row->created_at) ; ?> 	   <?php echo esc_attr($row->user_email) ; ?> 
 </strong>
         <hr />  <hr />
    <?php endforeach;
	
	}?>

 
	
  <?php
 

    }
    
    
    

// page Setting

function aistore_page_register_setting() {
	//register our settings
	register_setting( 'aistore_page', 'add_ticket_page_id' );
	register_setting( 'aistore_page', 'list_ticket_page_id' );
	register_setting( 'aistore_page', 'details_ticket_page_id' );



}

 function aistore_page_setting() {
	 
	  $pages = get_pages(); 
	
	   ?>
	  <div class="wrap">
	  
	  <div class="card">
	  
<h3><?php  _e( 'Support Ticket Setting', 'aistore' ) ?></h3>
 
	                     
<p>For anything plz email wordpress@aistore2030.com </p>
  

<p><?php  _e( 'Create 3 pages with short codes and select here  ', 'aistore' ) ?></p>


<form method="post" action="options.php">
    <?php settings_fields( 'aistore_page' ); ?>
    <?php do_settings_sections( 'aistore_page' ); ?>
	
    <table class="form-table">
	
	 <tr valign="top">
        <th scope="row"><?php  _e( 'Create Ticket form', 'aistore' ) ?></th>
        <td>
		<select name="add_ticket_page_id"  >
		 
		 
     <?php 

                    foreach($pages as $page){ 
					
					if($page->ID==get_option('add_ticket_page_id'))
					{
		 echo '	<option selected value="'.esc_attr($page->ID).'">'.esc_attr($page->post_title) .'</option>';
		 
		  } else {
                      
   echo '	<option value="'.esc_attr($page->ID).'">'.esc_attr($page->post_title) .'</option>';
		 
		

		}  
	 } ?> 
	 
	 
</select>
<p>
     <?php   _e(' Create a page add this shortcode ', 'aistore' ); ?> 
  [aistore_add_support_ticket]      <?php   _e('and then select that page here. ', 'aistore' ); ?>  
    </p>



</td>
        </tr>  
        
        	
	
	
		
		  <tr valign="top">
        <th scope="row"><?php  _e( 'Ticket List page', 'aistore' ) ?></th>
        <td>
		<select name="list_ticket_page_id">
		  
		   <?php 
                    foreach($pages as $page){ 
					
					if($page->ID==get_option('list_ticket_page_id'))
					{
		 echo '	<option selected value="'.esc_attr($page->ID).'">'.esc_attr($page->post_title) .'</option>';
		 
		  } else {
                      
   echo '	<option  value="'.esc_attr($page->ID).'">'.esc_attr($page->post_title) .'</option>';
		 
		

		}  
	 } ?> 

		   
		   
		   
</select>



<p>
     <?php   _e(' Create a page add this shortcode ', 'aistore' ); ?> 
  [aistore_list_support_ticket]    <?php   _e('and then select that page here. ', 'aistore' ); ?>  
    </p>


</td>
        </tr>  
		
		
		 <tr valign="top">
        <th scope="row"><?php  _e( 'Ticket Details Page', 'aistore' ) ?></th>
        <td>
		<select name="details_ticket_page_id" >
		 
		 
		  <?php 
                    foreach($pages as $page){ 
                        
				

					if($page->ID==get_option('details_ticket_page_id'))
					{
		 echo '	<option selected value="'.esc_attr($page->ID).'">'.esc_attr($page->post_title) .'</option>';
		 
		  } else {
                      
   echo '	<option  value="'.esc_attr($page->ID).'">'.esc_attr($page->post_title) .'</option>';
		 
		

		}  
	 } ?> 
	 
	 
		 
					  
					
 
</select>



<p>
    <?php   _e(' Create a page add this shortcode ', 'aistore' ); ?> 
  [aistore_ticket_details]    <?php   _e('and then select that page here. ', 'aistore' ); ?>   </p>




</td>
        </tr>  </table>
        
        
       
  
  

    
    <?php submit_button(); ?>

</form>
</div>
</div>
 <?php
	 
 }




function aistore_support_live_chat()
{
    
    global $wpdb;
    
    
       $ticket_id= sanitize_text_field($_REQUEST['tid']);
 

$user_id = get_current_user_id();


  $ticket = $wpdb->get_row($wpdb->prepare( "SELECT * FROM {$wpdb->prefix}support_ticket WHERE ID=%d  ",$ticket_id  ));
  
  
  
  
       
     
             if(isset($_POST['submit']) and $_POST['action']=='closemark')
{

if ( ! isset( $_POST['aistore_nonce'] ) 
    || ! wp_verify_nonce( $_POST['aistore_nonce'], 'aistore_nonce_action' ) 
) {
   return  _e( 'Sorry, your nonce did not verify', 'aistore' ) ;
    
}

$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}support_ticket
    SET status =  'closed'   WHERE ID = '%d' and   user_id=%d",  $ticket_id, $user_id ) );
   

}  ?>
   
	      <div class="card"> 
	      <div>
	          
	          <p>For anything plz email wordpress@aistore2030.com </p>
	      <div class="alert alert-success" role="alert">
 <strong><?php   _e('Ticket Status ', 'aistore' ); ?>  <?php echo esc_attr($ticket->status);?></strong>
  </div>

	      <?php
      
      printf(__( "Ticket ID :  %d", 'aistore' ),$ticket->ID );
  
  
  printf(__( "<br />Category :  %s", 'aistore' ),$ticket->category."<br>");
  
 

 
 if($ticket->status==='pending')   {
?>

<br><br>
   <form method="POST" action="" name="closemark" enctype="multipart/form-data"> 
 
<?php wp_nonce_field( 'aistore_nonce_action', 'aistore_nonce' ); ?>

  <input  type="submit"  name="submit" value="<?php  _e( 'Close Ticket ', 'aistore' ) ?>">
  <input type="hidden" name="action" value="closemark" />
</form>
 <?php }  ?>
 
 
 
 
  <br><br>



    <form id="chatform"  >
     
      
      <textarea name="message" > </textarea>
    
<?php wp_nonce_field( 'aistore_nonce_action', 'aistore_nonce' ); ?>
      <input type="hidden" name="action" value="aistore_submit_chat">
         <input type="hidden" name="ticket_id" value="<?php echo esc_attr($ticket_id);?>">
      
      <button type="submit">Send</button>
    </form>
    
    
 
    <div id="chat_message"></div>
    
    
  <?php 

}

}


    


if( is_admin() )
    $AistoreSettingsPage = new AistoreSettingsPage(); 


