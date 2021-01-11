<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*************************************************************************************
* Function to add a link in Settings
**************************************************************************************/
function codeable_add_settings_page(){

	add_submenu_page(
		'options-general.php',
		__('Codeable reviews and experts profile Auth Settings', 'codeable-reviews-and-expert-profile'),
		__('Codeable Auth Settings', 'codeable-reviews-and-expert-profile'),			
		'manage_options',
		'codeable-auth-settings',
		'codeable_auth_settings',
		50
	);
	
}
add_action( 'admin_menu' , 'codeable_add_settings_page');

/*************************************************************************************
* Function to register the Codeable Auth Settings
**************************************************************************************/
function codeable_register_settings(){
	
	register_setting( 'codeable_auth_options', 'codeable_auth_options' );
    
	add_settings_section( 
		'api_settings', 
		__('Codeable reviews and experts profile Auth Settings', 'codeable-reviews-and-expert-profile'), 
		'codeable_settings_text', 
		'codeable_plugin_settings'
	);

    add_settings_field( 
		'codeable_settings_username', 
		__('Username', 'codeable-reviews-and-expert-profile'), 
		'codeable_settings_username', 
		'codeable_plugin_settings', 
		'api_settings' 
	);
    
	add_settings_field( 
		'codeable_settings_password', 
		__('Password', 'codeable-reviews-and-expert-profile'), 
		'codeable_settings_password', 
		'codeable_plugin_settings', 
		'api_settings'
	);
	
    add_settings_field( 
		'codeable_settings_terms', 
		__('I have written permission from each of the writers of these reviews', 'codeable-reviews-and-expert-profile'), 
		'codeable_settings_terms', 
		'codeable_plugin_settings', 
		'api_settings' 
	);
}
add_action( 'admin_init' , 'codeable_register_settings');

/*************************************************************************************
* Functions to display helpful text for the section and render the fields on the form
**************************************************************************************/
function codeable_settings_text() {
    echo __('Enter you Codeable username and password to authenticate to the protected endpoints. Your password will be saved as a token.', 'codeable-reviews-and-expert-profile') . '  -- TO DO --';
}

function codeable_settings_username(){
	
    $options = get_codeable_options();
	$username = sanitize_user($options['username']);
   
   echo '<input id="codeable_settings_username" name="codeable_auth_options[username]" type="email" value="'.$username.'" required />';
}

function codeable_settings_password(){
	
    $options = get_codeable_options();
	$token = sanitize_text_field($options['token']);
	
    echo '<input id="codeable_settings_password" name="codeable_auth_options[token]" type="password" value="'.$token.'" required />';
}

function codeable_settings_terms(){
	
    $options = get_codeable_options();
	$terms_agreed   =  $options['terms_agreed'] === '1' ? 'checked' : '';
	
	echo '<input id="codeable_settings_terms" name="codeable_auth_options[terms_agreed]" type="checkbox" value="1" '.$terms_agreed.' />';    
}

function get_codeable_options(){
	
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
function codeable_auth_settings(){
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

function codeable_settings_password_save( $value, $option, $old_value){

	$value['token'] = '';
	return $value;
}
add_filter( 'pre_update_option_codeable_auth_options' , 'codeable_settings_password_save', 10, 3); 

/*************************************************************************************
* Function to prevent the Settings API to saving the option as autoload yes
**************************************************************************************/

function codeable_autoload_no( $value, $new_value, $option){

	update_option ('codeable_auth_options', $value, 'no');
	return $value;
}
add_filter( 'update_option_codeable_auth_options' , 'codeable_autoload_no', 10, 3); 