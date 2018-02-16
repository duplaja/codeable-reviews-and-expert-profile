<?php
/*
Plugin Name: Codeable Reviews and Expert Profile
Plugin URI: https://dandulaney.com
Description: Gathers Codeable Reviews and Profile Information for a Codeable Expert
Version: 0.9
Author: Dan Dulaney
Author URI: https://dandulaney.com
License: GPLv2
License URI: 
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function codeable_css_enqueue() {
    wp_enqueue_style( 'codeable-reviews-and-experts-css', plugins_url( 'css/codeable-expert-styles.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'codeable_css_enqueue' );


function codeable_handle_expert_transient($codeable_id) {


	// Do we have this information in our transients already?
	$transient = get_transient( 'codeable_'.$codeable_id.'_expert' );
  
	// Yep!  Just return it and we're done.
	if( ! empty( $transient ) ) {

	// The function will return here every time after the first time it is run, until the transient expires.
		return $transient;

	// Nope!  We gotta make a call.
	} else {
   
		$response = wp_remote_get('https://api.codeable.io/users/'.$codeable_id);
		$codeable_expert_data = json_decode(wp_remote_retrieve_body($response));

		// Save the API response so we don't have to call again until tomorrow.
		set_transient( 'codeable_'.$codeable_id.'_expert', $codeable_expert_data, DAY_IN_SECONDS );

		return $codeable_expert_data;   
	}
}

function codeable_handle_review_transient($codeable_id,$number_of_reviews) {

	// Do we have this information in our transients already?
	$transient = get_transient( 'codeable_'.$codeable_id.'_review_'.$number_of_reviews );
  
	// Yep!  Just return it and we're done.
	if( ! empty( $transient ) ) {

	// The function will return here every time after the first time it is run, until the transient expires.
		return $transient;

	// Nope!  We gotta make a call.
	} else {
   
		$four_hours = 4 * HOUR_IN_SECONDS;
   
		$number_of_pages = ceil($number_of_reviews / 4);
		$response = wp_remote_get('https://api.codeable.io/users/'.$codeable_id.'/reviews/');
		$codeable_review_data = json_decode(wp_remote_retrieve_body($response));

		if ($number_of_reviews > 4) {
		
			for($i=2;$i<=$number_of_pages;$i++) {


				$response = wp_remote_get('https://api.codeable.io/users/'.$codeable_id.'/reviews/?page='.$i);
				$temp_review_data = json_decode(wp_remote_retrieve_body($response));


				$codeable_review_data=array_merge($codeable_review_data,$temp_review_data);
			}
		}

		// Save the API response so we don't have to call again for four hours.
		set_transient( 'codeable_'.$codeable_id.'_review_'.$number_of_reviews, $codeable_review_data, $four_hours );

		return $codeable_review_data;   
	}
}


function codeable_display_expert_image( $atts ){

	$atts = shortcode_atts(
		array(
			'codeable_id' => '',
			'circle'=> 'yes',
			'class'=> 'codeable-profile-image',
		), $atts, 'expert_image' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	$codeable_expert_data = codeable_handle_expert_transient($atts['codeable_id']);

	$codeable_image_url= $codeable_expert_data->avatar->large_url;
	
	$return_image = "<img src='".$codeable_image_url."'";

	if ($atts['circle'] == 'yes') {
		$return_image .= " style='border-radius: 50%;'";
	}
	if ($atts['class'] !='codeable-profile-image') {

		$class = $atts['class'];
		$return_image .= " class='codeable-profile-image $class'";

	} else {

		$return_image .= " class='codeable-profile-image'";

	}

	$return_image.=">";
	
	return $return_image;
}
add_shortcode( 'expert_image', 'codeable_display_expert_image' );

function codeable_display_expert_rating($atts) {


	$atts = shortcode_atts(
		array(
			'codeable_id' => '',
		), $atts, 'expert_rating' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	$codeable_expert_data = codeable_handle_expert_transient($atts['codeable_id']);

	return $codeable_expert_data->average_rating;

}
add_shortcode( 'expert_rating', 'codeable_display_expert_rating' );

function codeable_display_expert_completed($atts) {


	$atts = shortcode_atts(
		array(
			'codeable_id' => '',
		), $atts, 'expert_completed' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	$codeable_expert_data = codeable_handle_expert_transient($atts['codeable_id']);

	return $codeable_expert_data->completed_tasks_count;

}
add_shortcode( 'expert_completed', 'codeable_display_expert_completed' );

function codeable_display_expert_hire($atts) {

	$atts = shortcode_atts(
		array(
			'codeable_id' => '',
			'message' => 'Hire Me',
			'referoo' => '',
			'class'=> '',
			'theme' => 'black',
		), $atts, 'expert_hire' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	$message = $atts['message'];
	
	$codeable_direct_hire_link = 'https://app.codeable.io/tasks/new?preferredContractor='.$atts['codeable_id'];
	
	if (!empty($atts['referoo'])) {
		
		$codeable_direct_hire_link.='&ref='.$atts['referoo'];
	}
	
	$button = "<a href='$codeable_direct_hire_link' class='codeable-hire-button";

	if ($atts['theme']=='black') {
		$button.=' hire-button-black';
	} 
	elseif ($atts['theme']== 'white') {
		$button.=' hire-button-white';
	}
	
	if (!empty($atts['class'])) {
		$button.=' '.$atts['class'];
	}
	
	$button.="'>$message</a>";
	
	return $button;

}

add_shortcode( 'expert_hire', 'codeable_display_expert_hire' );


function codeable_display_reviews($atts){

	$atts = shortcode_atts(
		array(
			'codeable_id' => '',
			'number_to_show'=>4,
			'show_title'=>'no',
			'show_date'=>'no',
		), $atts, 'expert_completed' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	$codeable_review_data = codeable_handle_review_transient($atts['codeable_id'],$atts['number_to_show']);

	//var_dump($codeable_review_data);
	$to_return = '<ul class="codeable_reviews">';
	$review_number = 0;
	foreach ($codeable_review_data as $review) {

		$review_number++;

		$task_title = $review->task_title;
		$score = $review->score;
		$time = $review->timestamp;
		$comment = $review->comment;
		$name = $review->reviewer->full_name;
		$image = $review->reviewer->avatar->medium_url;

		$score_disp = '';

		for ($i=0;$i<$score;$i++) {
			$score_disp .= "<img src='".plugins_url( 'img/rating-star.png', __FILE__ )."' class='review_rating_star'>";
		}

		$to_return.= "<li class='codeable_review'>
		<img src='$image' class='reviewer_image'>
		<div class='review_info'>";

		if($atts['show_title'] == 'yes') {
			$to_return.="<p class='review_task_title'>$task_title</p>";
		}
		$to_return.="<p class='review_rating'>Project Rating: <span style='display:inline-block'>$score_disp</span></p><p class='review_text'>$comment</p><p class='reviewer_name'>- $name";

		if($atts['show_date'] == 'yes') {

			$pretty_date = date('F j Y',$time);
			$to_return .=", <span class='review_date'>$pretty_date</span>";
		}

		$to_return.="</p></div></li>";

		if ($review_number == $atts['number_to_show']) {
			break;
		}
	}
	$to_return.= '</ul>';

	return $to_return;

}
add_shortcode('expert_reviews','codeable_display_reviews');