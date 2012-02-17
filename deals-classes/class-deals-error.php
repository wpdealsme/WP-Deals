<?php
/**
 * Handles error messages
 *
 * @class       Deals_Error
 * @package	WP Deals
 * @category	Class
 * @author	WP Deals
 */

class Deals_Error {
    
        var $errors = array(); // Stores store errors
        var $messages = array(); // Stores store messages
                
	/** constructor */
	function __construct() {
		
		// Non-admin and ajax requests
		if ( !is_admin() ) :
			
			// Load messages
			$this->load_messages();
			
			// Hooks
			add_filter( 'wp_redirect', array(&$this, 'redirect'), 1, 2 );
                        add_action( 'wp_footer', array(&$this, 'show_messages'), 11);
			
		else :
		endif;
                
	}
        
        
	
        /*-----------------------------------------------------------------------------------*/
	/* Messages */
	/*-----------------------------------------------------------------------------------*/ 
    
	    /**
		 * Load Messages
		 */
		function load_messages() { 
			if (isset($_SESSION['errors'])) $this->errors = $_SESSION['errors'];
			if (isset($_SESSION['messages'])) $this->messages = $_SESSION['messages'];
			
			unset($_SESSION['messages']);
			unset($_SESSION['errors']);
		}

		/**
		 * Add an error
		 */
		function add_error( $error ) { $this->errors[] = $error; }
		
		/**
		 * Add a message
		 */
		function add_message( $message ) { $this->messages[] = $message; }
		
		/** Clear messages and errors from the session data */
		function clear_messages() {
			$this->errors = $this->messages = array();
			unset($_SESSION['messages']);
			unset($_SESSION['errors']);
		}
		
		/**
		 * Get error count
		 */
		function error_count() { return sizeof($this->errors); }
		
		/**
		 * Get message count
		 */
		function message_count() { return sizeof($this->messages); }
		
		/**
		 * Output the errors and messages
		 */
		function show_messages() {
		
			if (isset($this->errors) && sizeof($this->errors)>0) :
				echo '<div class="layer"></div><div class="updated deals-error">'.$this->errors[0].'</div>';
				$this->clear_messages();
				return true;
			elseif (isset($this->messages) && sizeof($this->messages)>0) :
				echo '<div class="layer"></div><div class="updated deals-message">'.$this->messages[0].'</div>';
				$this->clear_messages();
				return true;
			else :
				return false;
			endif;
		}
		
		/**
		 * Redirection hook which stores messages into session data
		 *
		 * @param   location
		 * @param   status
		 * @return  location
		 */
		function redirect( $location, $status ) {
			global $is_IIS;

			// IIS fix
			if ($is_IIS) session_write_close();
		
			$_SESSION['errors'] = $this->errors;
			$_SESSION['messages'] = $this->messages;
			
			return $location;
		}
                
}
?>
