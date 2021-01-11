<?php

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	$pluginClass = 'CDBL_Settings';

	if (!class_exists($pluginClass)){ 
	
		class CDBL_Settings{		

			private static $instance;

			/**
			 * Get an instance of this class.
			 *
			 */
			public static function instance(){
				if (!isset(self::$instance)){
					$class = get_called_class();
					self::$instance =  new $class;
				}
				return self::$instance;
			}
			
			public function __construct(){
				
				$this->init();
			}
			
			public function init() {
				
				add_action( 'admin_init' , [ $this, 'register_settings' ] );
				add_action( 'admin_menu' , [ $this, 'add_settings_page' ] );				
				add_action( 'updated_option' , [ $this, 'prevent_autoload' ], 20, 3); 
				
				add_filter( 'pre_update_option_codeable_auth_options' , [ $this, 'settings_password_save' ], 10, 3); 
			}	
			
			/*************************************************************************************
			* Function to add a link in Settings
			**************************************************************************************/
			public function add_settings_page(){

				add_submenu_page(
					'options-general.php',
					__('Codeable reviews and experts profile Auth Settings', 'codeable-reviews-and-expert-profile'),
					__('Codeable Auth Settings', 'codeable-reviews-and-expert-profile'),			
					'manage_options',
					'codeable-auth-settings',
					[ $this, 'codeable_auth_settings' ],
					50
				);
				
			}	

			/*************************************************************************************
			* Function to register the Codeable Auth Settings
			**************************************************************************************/
			public function register_settings(){
				
				register_setting( 'codeable_auth_options', 'codeable_auth_options' );
				
				add_settings_section( 
					'api_settings', 
					__('Codeable reviews and experts profile Auth Settings', 'codeable-reviews-and-expert-profile'), 
					[ $this, 'codeable_settings_text' ], 
					'codeable_plugin_settings'
				);

				add_settings_field( 
					'codeable_settings_username', 
					__('Username', 'codeable-reviews-and-expert-profile'), 
					[ $this, 'codeable_settings_username' ], 
					'codeable_plugin_settings', 
					'api_settings' 
				);
				
				add_settings_field( 
					'codeable_settings_password', 
					__('Password', 'codeable-reviews-and-expert-profile'), 
					[ $this, 'codeable_settings_password' ], 
					'codeable_plugin_settings', 
					'api_settings'
				);
				
				add_settings_field( 
					'codeable_settings_terms', 
					__('I have written permission from each of the writers of these reviews', 'codeable-reviews-and-expert-profile'), 
					[ $this, 'codeable_settings_terms' ], 
					'codeable_plugin_settings', 
					'api_settings' 
				);
			}		

			/*************************************************************************************
			* Functions to display helpful text for the section and render the fields on the form
			**************************************************************************************/
			public function codeable_settings_text() {
				echo __('Enter you Codeable username and password to authenticate to the protected endpoints. Your password will be saved as a token.', 'codeable-reviews-and-expert-profile') . '  -- TO DO --';
			}

			public function codeable_settings_username(){
				
				$options = self::get_codeable_options();
				$username = sanitize_user($options['username']);
			   
			   echo '<input id="codeable_settings_username" name="codeable_auth_options[username]" type="email" value="'.$username.'" required />';
			}

			public function codeable_settings_password(){
				
				$options = self::get_codeable_options();
				$token = sanitize_text_field($options['token']);
				
				echo '<input id="codeable_settings_password" name="codeable_auth_options[token]" type="password" value="'.$token.'" required />';
			}

			public function codeable_settings_terms(){
				
				$options = self::get_codeable_options();
				$terms_agreed   =  $options['terms_agreed'] === '1' ? 'checked' : '';
				
				echo '<input id="codeable_settings_terms" name="codeable_auth_options[terms_agreed]" type="checkbox" value="1" '.$terms_agreed.' />';    
			}

			public static function get_codeable_options(){
				
				$defaults = [
					'username' => '',
					'token' => '',
					'terms_agreed' => '0'
				];
				
				$settings = get_option( 'codeable_auth_options',  $defaults); 
				
				return $settings;
			}

			/*************************************************************************************
			* Function to add a Settings page to set credentials for endpoints with authentication
			**************************************************************************************/
			public function codeable_auth_settings(){
				?>
					<form action="options.php" method="post">
						<?php 
						settings_fields( 'codeable_auth_options' );
						do_settings_sections( 'codeable_plugin_settings' ); 
						submit_button( __('Save Settings','codeable-reviews-and-expert-profile')); ?>
					</form>
				<?php
			}

			/*************************************************************************************
			* Modify the Codeable password on submission
			**************************************************************************************/

			function settings_password_save( $value, $option, $old_value){

				$value['token'] = '';
				return $value;
			}		

			/*************************************************************************************
			* Function to prevent the Settings API to saving the option as autoload yes
			**************************************************************************************/

			public function prevent_autoload( $option, $old_value, $value ){
				
				if( $option !== 'codeable_auth_options' ){
					return;
				}
				
				update_option ('codeable_auth_options', $value, 'no' );
				return $value;
			}		
		}
	}
	
	//instantiate the class
	if (class_exists($pluginClass)){
		$pluginClass::instance();
	}