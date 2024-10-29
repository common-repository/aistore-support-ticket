<?php
add_action( 'admin_footer', 'aistore_my_action_javascript' ); 

// Write our JS below here

function aistore_my_action_javascript() { ?>

 


	<script type="text/javascript" >
	
	
	 // var socket = io("https://aistore2030socket.herokuapp.com");

      var messages = document.getElementById('messages');
      
      
      
	jQuery(document).ready(function($) {
	    
	    <?php
	    
       $ticket_id=esc_attr( sanitize_text_field($_REQUEST['tid']));
       
     ?>
       
	    
	   aistore_get_chat_message(<?php echo $ticket_id; ?>);
	    
	    
/*	    
      socket.on('reload', function(id) {
          console.log(id);
	   aistore_get_chat_message(<?php echo $ticket_id; ?>);
      });
*/

  $("form#chatform").submit(function (event) {
      
      
  
 var form = $(this);
  
	 
		jQuery.post(ajaxurl, form.serialize(), function(response) {
		console.log(response);
		 
		 
		 aistore_get_chat_message(<?php echo $ticket_id; ?>);
		 
		 
		}); 
		
		  event.preventDefault();
		  
		  
	});
	});
		
	
	 function aistore_get_chat_message(ticket_id)
	 {
	     
	        jQuery('#chat_message').html("loading");
	     var data = {
			'action': 'aistore_get_chat_message',
			'ticket_id': ticket_id
		};
		
	     	jQuery.get(ajaxurl, data, function(response) {
	
	 

                        var student = '';

                        // ITERATING THROUGH OBJECTS

                        jQuery.each(JSON.parse(response), function (key, value) {

                      

    student += "<div class='message_row'>" ;
   

    student += "<div class='message'>"+  value.message + "</div> <br /><br /> ";

                      

      student +=value.ID + "  "+  value.created_at + " "+value.user_email ;
                            
                    
                            
  student +="<hr />";            
                 

    student +=   "</div>";
           
                         

                         

                        });

                        //INSERTING ROWS INTO TABLE 

                        jQuery('#chat_message').html(student);

                    });
                    
                    
                    
                    
                    
                  
                  
	     
	 }





	</script> <?php
}


add_action( 'wp_ajax_aistore_submit_chat', 'aistore_submit_chat' );

function aistore_submit_chat() {
	global $wpdb; // this is how you get access to the database



if ( ! isset( $_POST['aistore_nonce'] ) 
    || ! wp_verify_nonce( $_POST['aistore_nonce'], 'aistore_nonce_action' ) 
) {
   return  _e( 'Sorry, your nonce did not verify', 'aistore' ) ;
    
} 
 
$message=sanitize_textarea_field($_REQUEST['message']);
$ticket_id=sanitize_textarea_field($_REQUEST['ticket_id']);
$user_id = get_current_user_id();

//file_get_contents("https://aistore2030socket.herokuapp.com/reload");  
// for socket based chat please talk to me


$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}support_ticket_message ( message, ticket_id,user_id ) VALUES (  %s, %s, %s )", 
array( $message, $ticket_id,$user_id ) ) );

 //echo "submitted";

	wp_die(); // this is required to terminate immediately and return a proper response
}







add_action( 'wp_ajax_aistore_get_chat_message', 'aistore_get_chat_message' );

function aistore_get_chat_message() {
	global $wpdb; // this is how you get access to the database



 
$ticket_id=sanitize_textarea_field($_REQUEST['ticket_id']);
$user_id = get_current_user_id();

 


  $results = $wpdb->get_results($wpdb->prepare( "SELECT m.*,u.user_email FROM {$wpdb->prefix}support_ticket_message m , {$wpdb->prefix}users u WHERE m.ticket_id=%d and (m.user_id =u.id) order by id desc limit 50",$ticket_id  ));
  
  
  
echo json_encode( $results);

	wp_die(); // this is required to terminate immediately and return a proper response
}


