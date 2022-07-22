<?php
/**
 * Plugin name: YOODULE
 * Plugin URI: https://stripe.com
 * Description: Get Data from Stripe via APIs into WordPress
 * Author: OLA
 * Author URI: https://github.com/beatbeast/Yoodule-Stripe.
 * version: 0.1.0
 * License: GPL2 or later.
 * text-domain: yoodule-stripe
 */

// If this file is access directly, abort!!!
defined( 'ABSPATH') or die( 'Unauthorized Access');

//import strie php library
require_once('stripe-php-8.11.0/init.php');

// Create Shortcode for yoodule-stripe.
add_shortcode( 'yoodule-stripe', 'yoodule_rest_ajax_shortcode' );


function yoodule_rest_ajax_shortcode () {
    ?>
        <div class="display" >
		
		<table id="yoodule-table" width="100%">
  <thead>
    <tr role="row">
      <th>ID</th>
      <th>Object</th>
      <th>Currency</th>
      <th>Product</th>
      <th>Type</th>
      <th>Unit_amount</th>
    </tr>

  </thead>
 
</table>
</div>
    <?php
    // Write AJAX to show the infomation in the shortcode.
    wp_enqueue_script( 'stripe-ajax-scripts', plugins_url( 'assets/js/stripe-ajax.js', __FILE__ ), ['jquery'], '0.1.0', true );
    
}



// Create new endpoint to provide data.
add_action( 'rest_api_init', 'yoodule_rest_ajax_endpoint' );

function yoodule_rest_ajax_endpoint() {
    register_rest_route(
        'yoodule',
        'rest-ajax',
        [
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => 'yoodule_rest_ajax_callback',
        ]
    );
}

// REST Endpoint information.
function yoodule_rest_ajax_callback() {

		$strp_key = get_option( 'mt_api_key' );
		$strpe = new \Stripe\StripeClient($strp_key);
		$strprice = $strpe->prices->all();
		$stpRes = $strprice->getLastResponse()->body;
		
 		$Res = json_decode($strprice->getLastResponse()->body);
		$Wot = $Res->data;

    return $Res->data;

}




/**
 * Function that initializes the plugin.
 */
function yoodule_get_send_data() {

   
    $str_key = get_option( 'mt_api_key' );
			

		// var_dump($str_key);
		$stripe = new \Stripe\StripeClient($str_key);
		$customer = $stripe->prices->all();
		$yeh = $customer->getLastResponse()->body;
		$stpRes = json_decode($customer->getLastResponse()->body);
		var_dump($stpRes);

        ?>
        <table id="table" width="100%">
          <thead>
            <tr role="row">
              <th>ID</th>
              <th>name</th>
              <th>currency</th>
              <th>product</th>
            </tr>
          </thead>
          <tbody>
        
        </tbody>
        </table>
        <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" />
        <script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript">
            var fed = <? echo $yeh ?>;
            var abc = new Request('<? $yeh ?>');
            
        jQuery(document).ready(function($){
            console.log(fed.data);
                $('#table').DataTable({
                data: fed.data,
                    columns: [
                    { data: 'unit_amount' },
                    { data: 'object' },
                    { data: 'currency' },
                    { data: 'product' },
                    
                    ]
            });
        });
        </script>
        
        
        <?php

}

/**
 * Register a custom menu page to view the information queried.
 */
function yoodule_register_my_custom_menu_page() {
	add_menu_page(
		__( 'YOODULE STRIPE Settings', 'yoodule-stripe' ),
		'YOODULE STRIPE',
		'manage_options',
		'yoodule',
        'yoodule_get_send_data',
        'dashicons-testimonial',
		16
	);
	
    //adding a submenu called API Settings
	add_submenu_page( 
        'yoodule', 
        'api keys', 
        'API Settings', 
        'manage_options', 
        'api_creds', 
        'yoodule_api_creds'
    ); 

}

/**
 * Return view for the API Settings submenu page.
 */
function yoodule_api_creds () {
	 //must check that the user has the required capability 
     if (!current_user_can('manage_options'))
     {
       wp_die( __('You do not have sufficient permissions to access this page.') );
     }
 
     // variables for the field and option names 
     $opt_name = 'mt_stripe_customer';
     $opt_api_key = 'mt_api_key';
     $hidden_field_name = 'mt_submit_hidden';
     $data_field_name = 'mt_stripe_customer';
     $data_field_key = 'mt_api_key';
 
     // Read in existing option value from database
     $opt_val = get_option( $opt_name );
     $opt_key = get_option( $opt_api_key );
 
     // See if the user has posted us some information
     // If they did, this hidden field will be set to 'Y'
     if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
         // Read their posted value
         $opt_val = $_POST[ $data_field_name ];
         $opt_key = $_POST[ $data_field_key ];
 
         // Save the posted value in the database
         update_option( $opt_name, $opt_val );
         update_option( $opt_api_key, $opt_key );
         // Put a "settings saved" message on the screen
 
     ?>
     <div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
     <?php
 
     }
 
     // Now display the settings editing screen
 
     echo '<div class="wrap">';
 
     // header
 
     echo "<h2>" . __( 'Stripe API Configurations', 'menu-test' ) . "</h2>";
     echo "<p>" . __( 'Input your Stripe customer account and Secret key into the input fields inorder to display stripe prices', 'menu-test' ) . "</p>";
     // settings form
     
         ?>
 
     <form name="form1" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
    
        <p><?php _e("Stripe Account:", 'menu-test' ); ?> 
            <input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
        </p>
        <hr />
        <p><?php _e("Stripe API Key:", 'menu-test1' ); ?> 
            <input type="text" name="<?php echo $data_field_key; ?>" value="<?php echo $opt_key; ?>" size="20">
        </p>
        <hr />
    
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>
 
     </form>
     
     </div>
 
    <?php
 
}

add_action( 'admin_menu', 'yoodule_register_my_custom_menu_page' );


/**
* Load custom CSS and JavaScript.
*/
add_action( 'wp_enqueue_scripts', 'wpdocs_my_enqueue_scripts' );
function wpdocs_my_enqueue_scripts() : void {
    // Enqueue my styles.
    wp_enqueue_style( 'datatables-style', 'https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css' );
     
    // Enqueue my scripts.
    wp_enqueue_script( 'datatables', 'https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js', ['jquery'], null, true );
     wp_localize_script( 'datatables', 'datatablesajax', array('url' => admin_url('admin-ajax.php')) );

}
?>