<?php 
// Get the path to the root.
$wp_load = realpath("wp-load.php"); 
while(!file_exists($wp_load)) {
    $wp_load = '../' . $wp_load;
}

require_once($wp_load);

if(!isset($_POST)) : die('Wrong place.'); endif;
if(!wp_verify_nonce($_POST['wpnonce'],'nonce_form_subscribe')) : die('Invalid Request.'); endif;

// the data
$email  = strtolower($_POST['email']);
$postid = strtolower($_POST['postid']);

//secure
$postid = intval($postid);

if(function_exists('filter_var')) {
	$valid_email = filter_var(trim($email),FILTER_VALIDATE_EMAIL);
}else{
	$valid_email = eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($email));	
}

//if the email is valid
if ($valid_email) {
	
	//get all the current emails
	$stack = get_option('deals_subscribed_emails');
        
        if(isset($postid) AND !empty($postid))
            deals_free_send_email($postid, $email);
                
	//if there are no emails in the database
	if(!$stack) {
		//update the option with the first email as an array
		update_option('deals_subscribed_emails', array($email));	
	} else {
		//if the email already exists in the array
		if(in_array($email, $stack)) {
                    if(isset($postid) AND !empty($postid) AND !wp_verify_nonce($_POST['wpnonce'],'popup'))
                        _e("<p class='success'>Check your email, for download the item (Inbox or Spam folder).</p>", 'wpdeals');
                    else
			_e("<p class='success'>Your email has been registered.</p>", 'wpdeals');
		} else {
			// If there is more than one email, add the new email to the array
			array_push($stack, $email);
			
			//update the option with the new set of emails
			update_option('deals_subscribed_emails', $stack);
                        
                        if(isset($postid) AND !empty($postid) AND wp_verify_nonce($_POST['wpnonce'],'popup'))
                            _e("<p class='success'>Check your email, for download the item (Inbox or Spam folder).</p>", 'wpdeals');
                        else
                            _e("<p class='success'>Your email has been registered.</p>", 'wpdeals');
                            
		}
	}
} else {
	_e("<p class='error'><span class='cross'></span>please enter a valid email address</p>", 'wpdeals');
}

exit;
