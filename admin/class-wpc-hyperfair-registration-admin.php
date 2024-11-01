<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.wpconcierges.com/plugin-resources/hyper-fair-registration/
 * @since      1.0.0
 *
 * @package    hyperfair_registration
 * @subpackage hyperfair_registration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    hyperfair_registration
 * @subpackage hyperfair_registration/admin
 * @author     Your Name <email@example.com>
 */
class wpc_hyperfair_registration_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hyperfair_registration    The ID of this plugin.
	 */
	private $hyperfair_registration;

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
	 * @param      string    $hyperfair_registration       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $hyperfair_registration, $version ) {

		$this->plugin_name = $hyperfair_registration;
		$this->version = $version;
    $this->set_options(); 
	}

	/**
	 * Register the stylesheets for the admin area.
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
	 * Register the JavaScript for the admin area.
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

	public function validate_options( $input ) {

		//wp_die( print_r( $input ) );

		$valid 		= array();
		$options 	= $this->get_options_list();

		foreach ( $options as $option ) {

			$name = $option[0];
			$type = $option[1];

			if ( 'repeater' === $type && is_array( $option[2] ) ) {

				$clean = array();

				foreach ( $option[2] as $field ) {

					foreach ( $input[$field[0]] as $data ) {

						if ( empty( $data ) ) { continue; }

						$clean[$field[0]][] = $this->sanitizer( $field[1], $data );

					} // foreach

				} // foreach

				$count = count( $clean );

				for ( $i = 0; $i < $count; $i++ ) {

					foreach ( $clean as $field_name => $field ) {

						$valid[$option[0]][$i][$field_name] = $field[$i];

					} // foreach $clean

				} // for

			} else {

				$valid[$option[0]] = $this->sanitizer( $type, $input[$name] );

			}
			
		}

		return $valid;

	} // validate_options()
	
	private function sanitizer( $type, $data ) {

		if ( empty( $type ) ) { return; }
		if ( empty( $data ) ) { return; }

		$return 	= '';
		$sanitizer 	= new wpc_hyperfair_registration_Sanitize();

		$sanitizer->set_data( $data );
		$sanitizer->set_type( $type );

		$return = $sanitizer->clean();

		unset( $sanitizer );

		return $return;

	} // sanitizer() 

   
  	/**
	 * Registers plugin settings
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
   public function register_settings() {

		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-options',
			array( $this, 'validate_options' )
		);

 	} // register_settings()
	
	 /**
	 * Registers settings fields with WordPress
	 */
	public function register_fields() {
  	
		add_settings_field(
				'hpfr-place-name',
				apply_filters( $this->plugin_name . 'label-hpfr-place-name', esc_html__( 'Hyper Fair Registration Place Name',$this->plugin_name) ),
				array( $this, 'field_text' ),
				$this->plugin_name,
				$this->plugin_name . '-messages',
				array(
					'description' 	=> 'This is the place that users are registering for.',
					'id' 			=> 'hpfr-place-name',
					'value' 		=> '',
				)
			);	
			
			add_settings_field(
				'hpfr-secret',
				apply_filters( $this->plugin_name . 'label-hpfr-secret', esc_html__( 'Hyper Fair Registration Secret',$this->plugin_name) ),
				array( $this, 'field_text' ),
				$this->plugin_name,
				$this->plugin_name . '-messages',
				array(
					'description' 	=> 'This is the secret provided by Hyper Fair.',
					'id' 			=> 'hpfr-secret',
					'value' 		=> '',
				)
			);	

			add_settings_field(
				'hpfr-hook-name',
				apply_filters( $this->plugin_name . 'label-hpfr-hook-name', esc_html__( 'Choose What Plugin to Pull Registration Data From',$this->plugin_name) ),
				array( $this, 'field_select' ),
				$this->plugin_name,
				$this->plugin_name . '-messages',
				array(
					'description' 	=> 'What Plugin has your Data you want to registration with Hyper Fair',
					'id' 			=> 'hpfr-hook-name',
					'value' 		=> '',
					'selections'     => array('woocommerce','memberpress')
				)
			);	
			add_settings_field(
				'hpfr-debug-mode',
				apply_filters( $this->plugin_name . 'label-hpfr-debug-mode', esc_html__( 'Hyper Fair Mode',$this->plugin_name) ),
				array( $this, 'field_select' ),
				$this->plugin_name,
				$this->plugin_name . '-messages',
				array(
					'description' 	=> 'What mode is the hyper fair registration in',
					'id' 			=> 'hpfr-debug-mode',
					'value' 		=> '',
					'selections'     => array('live','test')
				)
			);	
	  }
	  
	  
	/**
	 * Registers settings sections with WordPress
	 */
	public function register_sections() {

		add_settings_section(
			$this->plugin_name . '-messages',
			apply_filters( $this->plugin_name . 'section-title-messages', esc_html__( '',$this->plugin_name) ),
			array( $this, 'section_messages' ),
			$this->plugin_name
		);

	} // register_sections()
	
	/**
	 * Creates a settings section
	 *
	 * @since 		1.0.0
	 * @param 		array 		$params 		Array of parameters for the section
	 * @return 		mixed 						The settings section
	 */
	public function section_messages( $params ) {

		include( plugin_dir_path( __FILE__ ) . 'partials/wpc-hyperfair-registration-admin-section-messages.php' );

	} // section_messages()
	  /**
		 * Creates a select field
		 *
		 * Note: label is blank since its created in the Settings API
		 *
		 * @param 	array 		$args 			The arguments for the field
		 * @return 	string 						The HTML field
		 */
		public function field_select( $args ) {
	
			$defaults['aria'] 			= '';
			$defaults['blank'] 			= '';
			$defaults['class'] 			= 'widefat';
			$defaults['context'] 		= '';
			$defaults['description'] 	= '';
			$defaults['label'] 			= '';
			$defaults['name'] 			= $this->plugin_name . '-options[' . $args['id'] . ']';
			$defaults['selections'] = array();
			$defaults['value'] 			= '';
	
			apply_filters( $this->plugin_name . '-field-select-options-defaults', $defaults );
	
			$atts = wp_parse_args( $args, $defaults );
	
			if ( ! empty( $this->options[$atts['id']] ) ) {
	
				$atts['value'] = $this->options[$atts['id']];
	
			}
	
			if ( empty( $atts['aria'] ) && ! empty( $atts['description'] ) ) {
	
				$atts['aria'] = $atts['description'];
	
			} elseif ( empty( $atts['aria'] ) && ! empty( $atts['label'] ) ) {
	
				$atts['aria'] = $atts['label'];
	
			}
	
			include( plugin_dir_path( __FILE__ ) . 'partials/wpc-hyperfair-registration-admin-field-select.php' );
	
		} // field_select()
	
	  public function field_text( $args ) {
	
			$defaults['class'] 			= 'text wide';
			$defaults['description'] 	= '';
			$defaults['label'] 			= '';
			$defaults['name'] 			= $this->plugin_name . '-options[' . $args['id'] . ']';
			$defaults['placeholder'] 	= '';
			$defaults['type'] 			= 'text';
			$defaults['value'] 			= '';
	
			apply_filters( $this->plugin_name . '-field-text-options-defaults', $defaults );
	
			$atts = wp_parse_args( $args, $defaults );
	
			if ( ! empty( $this->options[$atts['id']] ) ) {
	
				$atts['value'] = $this->options[$atts['id']];
	
			}
	
			include( plugin_dir_path( __FILE__ ) . 'partials/wpc-hyperfair-registration-admin-field-text.php' );
	
		} // field_text()
	 
		

		public function get_options_list() {

			$options = array();
	
			$options[] = array('hpfr-place-name', 'text', 'Hyper Fair Registration Place Name' );
			$options[] = array('hpfr-secret', 'text', 'Hyper Fair Registration Secret' );
			$options[] = array('hpfr-hook-name', 'select', 'WordPress Plugin Hook');
			$options[] = array('hpfr-debug-mode', 'select', 'Hyper Fair Mode');
		
			return $options;
	
		} // get_options_list()


		private function set_options() {
    
			$this->options = get_option( $this->plugin_name . '-options' );
	   
		} // set_options()
		
		public function page_options() {
	  
			include( plugin_dir_path( __FILE__ ) . 'partials/wpc-hyperfair-registration-admin-page-settings.php' );
	
		} // page_options()


		public function hyperfair_admin_menu(){
			add_management_page( 'WPConcierges HyperFair Registration','WPConcierges HyperFair Registration','manage_options',$this->plugin_name,array($this,'page_options'));  
		}

}
