<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.wpconcierges.com/plugin-resources/hyper-fair-registration/
 * @since      1.0.0
 *
 * @package    hyperfair_registration
 * @subpackage hyperfair_registration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    hyperfair_registration
 * @subpackage hyperfair_registration/public
 * @author     Your Name <email@example.com>
 */
class wpc_hyperfair_registration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hyperfair_registration    The ID of this plugin.
	 */
	private $hyperfair_registration;
  private $postback_url;
  private $postback_params;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $hyperfair_registration       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $hyperfair_registration, $version ) {

		$this->plugin_name = $hyperfair_registration;
		$this->version = $version;
    $this->plugin_options = get_option($this->plugin_name."-options",array());
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in hyperfair_registration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The hyperfair_registration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in hyperfair_registration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The hyperfair_registration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

	}

	public function send_woo_thankyou_order($order_id){
		if($this->plugin_options['hpfr-hook-name']=='woocommerce'){
			$order = wc_get_order( $order_id );
			$woo_values = $this->get_order_values($order,$order_id);
			$this->postback_params = json_encode($this->get_hyperfair_values($woo_values));
			$this->postback_url = $this->get_postback_url();
			$this->do_postback_woo();
		}
	}  

	public function send_mepr_thankyou_order($event){
			if($this->plugin_options['hpfr-hook-name']=='memberpress'){
        $mepr_values = $this->get_mepr_values($event);
        $this->postback_params = json_encode($this->get_hyperfair_values($mepr_values));
		    $this->postback_url = $this->get_postback_url();
		    $this->do_postback_woo();
		  }
	}

	private function get_hyperfair_values($values){
		$hpvalues = array();
		
    $hpvalues['secret']=$this->plugin_options['hpfr-secret'];
    $hpvalues['email'] = $values['billing_email'];
    $hpvalues['password'] = $values['billing_password'];//(at least 6 characters)
    $hpvalues['firstName'] = $values['billing_first_name'];
    $hpvalues['lastName'] = $values['billing_last_name'];
    $hpvalues['gender'] = $values['gender'];
    
    $hpvalues['__DEBUG__'] = $this->get_debug_mode();
    
	  return $hpvalues;
	} 

  private function get_debug_mode(){
  	$debug = false;
    if(isset($this->plugin_options['hpfr-debug-mode'])){
    	
    	if($this->plugin_options['hpfr-debug-mode']=="test"){
    		$debug = true;
    	}else{
    		$debug = false;
    	}
    }else{
      $debug = false;	
    }
    return $debug;
  }
  
	private function get_mepr_values($event){
		$values = array();
		$txn = $event->get_data();
		$user_id = $txn->user_id;
		$user = get_user_by("ID",$user_id);
		$user_meta = get_user_meta($user_id);
     
    if(isset($user_meta['billing_email'][0])){
			$values['billing_email']      =$user_meta['billing_email'][0];
		}elseif(isset($user_meta['mepr_email'][0])){
			$values['billing_email']      =$user_meta['mepr_email'][0];
		}else{
			$values['billing_email']      =$user->user_email;
		}
		
    $values['billing_first_name'] =$user_meta['first_name'][0];
    $values['billing_last_name']  =$user_meta['last_name'][0];
    $values['billing_password']   =$this->default_password();
    $values['gender']             =$this->get_gender($user_meta);
   

	  return $values;
	}
	
	private function get_gender($user_meta){
		$gender = "";
		
		if(isset($user_meta['gender'][0])){
			$gender = $user_meta['gender'][0];
		}else{
			$gender = "male";
		}
		
		return $gender;
	}
	
	private function default_password(){
		  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
	}

	private function get_postback_url(){
		$place_name = $this->plugin_options['hpfr-place-name'];
		
		$url = "https://secure.hyperfair.com/global/".$place_name."/registrationservice/";
		return $url;

	}

	public function get_order_values($order,$order_id){
	    $values =array();
        
	  $order_items = array();
	  $order_type =$order->get_type();
		$order_meta = get_post_meta($order_id);
	  $values['billing_email']=$order_meta['_billing_email'][0];
    $values['billing_phone']=$order_meta['_billing_phone'][0];
    $values['billing_first_name']=$order_meta['_billing_first_name'][0];
    $values['billing_last_name']=$order_meta['_billing_last_name'][0];
    $values['billing_company']=$order_meta['_billing_company'][0];
    $values['billing_address_1']=$order_meta['_billing_address_1'][0];
    $values['billing_address_2']=$order_meta['_billing_address_2'][0];
    $values['billing_city']=$order_meta['_billing_city'][0];
    $values['billing_state']=$order_meta['_billing_state'][0];
    $values['billing_postcode']=$order_meta['_billing_postcode'][0];
    $values['billing_country']=$order_meta['_billing_country'][0];
    $values['billing_password']   =$this->default_password();
    
    $values['shipping_first_name']=$order_meta['_shipping_first_name'][0];
    $values['shipping_last_name']=$order_meta['_shipping_last_name'][0];
    $values['shipping_company']=$order_meta['_shipping_company'][0];
    $values['shipping_address_1']=$order_meta['_shipping_address_1'][0];
    $values['shipping_address_2']=$order_meta['_shipping_address_2'][0];
    $values['shipping_city']=$order_meta['_shipping_city'][0];
    $values['shipping_state']=$order_meta['_shipping_state'][0];
    $values['shipping_postcode']=$order_meta['_shipping_postcode'][0];
    $values['shipping_country']=$order_meta['_shipping_country'][0];
    
    $values['order_tax']= $order_meta['_order_tax'][0];
    $values['order_total']= $order_meta['_order_total'][0];
    
    $values['cart_discount']= $order_meta['_cart_discount'][0];
    $values['cart_discount_tax']= $order_meta['_cart_discount_tax'][0];
    
    $values['order_shipping']= $order_meta['_order_shipping'][0];
    $values['order_shipping_tax']= $order_meta['_order_shipping_tax'][0];
    
    $values['order_currency']= $order_meta['_order_currency'][0];
    $values['payment_method']= $order_meta['_payment_method'][0];
    
    $values['customer_ip_address'] = $order_meta['_customer_ip_address'][0];
    
    $values['gender']             =$this->get_gender($order_meta);
    
    if(isset($order_meta['_transaction_id'][0]))
    $values['transaction_id'] = $order_meta['_transaction_id'][0];
    else
    $values['transaction_id'] = "";
    
    if(isset($order_meta['_paid_date'][0]))
    $values['paid_date'] = $order_meta['_paid_date'][0];
    else
    $values['paid_date'] = "";
    
    if(isset($order_meta['_customer_user'][0]))
    	$values['customer_user'] = $order_meta['_customer_user'][0];
    else
    	$values['customer_user'] = 0;
    
   	if(isset($order_meta['_order_number'][0])) 
    	$values['order_number']=$order_meta['_order_number'][0];
   	else
    	$values['order_number']=$order_id;
    	
    	  $is_subscription = false;
        if(function_exists("wcs_order_contains_subscription") && wcs_order_contains_subscription($order,$order_type)){
        	$subscription = new WC_Subscription($order_id);
        	$items= $subscription->order->get_items();
        	$is_subscription = true;
        	
        }else{
        	$items = $order->get_items();
        }
      
        
        $total_items=0;
		    $total_discount_amount=0;
		 
		 
        foreach($items as  $item_id => $item){
        
					$variation_id=$item->get_variation_id();
		 			if($variation_id>0){
		 				if(class_exists("WC_Product_Variation"))
		 			  $product = new WC_Product_Variation($variation_id);
		 			  else
		 			  $product = $item->get_product();
		 			}elseif($is_subscription){
		 				$product = $item->get_product();
		 			}else{
		 				if(class_exists("WC_Product"))
		 				$product = new WC_Product($item['product_id']);
		 				else
		 				$product = $item->get_product();
		 			}
		     
		      if(isset($product)){
		      	$tmp = array();
		      	$tmp['product_id'] = $item['product_id'];
						$tmp['name']=$product->get_name();
         
      			$tmp['cost'] =$item_cost =strip_tags($product->get_price());
      		
      			$tmp['quantity'] = $quantity = $order->get_item_meta($item_id, '_qty', true);
           	$tmp['sku'] = $product->get_sku();
           	
           	array_push($order_items,$tmp);
      			$total_items+=($item_cost*$quantity);
      	  }
        }
		   $values['product_total']=$total_items;
		   $values['products']=$order_items;
       $values = json_encode($values);
       
      return $values;
	}
	
	public function get_login_token(){
		global $current_user;
		$return_values = array();
		
		$place_name = $this->plugin_options['hpfr-place-name'];
		$email = $current_user->user_email;
		
		$url = "https://secure.hyperfair.com/global/".$place_name."/setlogintoken/";
		$hpvalues = array();
		
		$hpvalues['secret']= $this->plugin_options['hpfr-secret'];
		$hpvalues['email']= $email;
		$hpvalues['__DEBUG__'] = $this->get_debug_mode();
		
		$json = json_encode($hpvalues);
		$token_info = $this->do_login_token($url,$json);
		
		if($token_info['status']==200){
			$return_values['status']="success";
			$return_values['email']=$email;
			$return_values['token']=$token_info['token'];
			$return_values['place']="https://platform.hyperfair.com/global/login/place/".$place_name;
		}else{
			$return_values['status']="failed";
		}
		
		return $return_values;
	}

	private function do_postback_woo(){        
        if ($this->postback_url && strlen($this->postback_url) > 5) {
          	$args = array("body"=>array("userdata"=>$this->postback_params),"headers"=>array("Content-Type: application/x-www-form-urlencoded"));
          	$response = wp_remote_post($this->postback_url,$args);  
		    }
		    print_r($response);
        return $response;    
  }   
 
  private function do_login_token($url,$json){        
  	$return_values = array();
  	
        if ($url && strlen($url) > 5) {
          	$args = array("body"=>array("userdata"=>$json),"headers"=>array("Content-Type: application/x-www-form-urlencoded"));
          	$response = wp_remote_post($url,$args); 
          	if(isset($response['response']['code']) && (int)$response['response']['code']==200){
          		$return_values['token'] = $response['body'];
          	}else{
          		$return_values['token'] = "";
          	} 
          	$return_values['status'] = $response['response']['code'];
		    }
		   
        return $return_values;    
  }   

}
