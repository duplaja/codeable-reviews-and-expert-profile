<?php
/*
Plugin Name: Codeable Reviews and Expert Profile
Plugin URI: https://dandulaney.com
GitHub Plugin URI: https://github.com/duplaja/codeable-reviews-and-expert-profile
Description: Gathers Codeable Reviews and Profile Information for a Codeable Expert
Version: 2.1.0
Author: Dan Dulaney
Author URI: https://dandulaney.com
License: GPLv2
License URI: 
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/*******************************************************************
* Checks to see if Gutenberg is set up on the site before attempting to load blocks
*********************************************************************/
 function codeable_reviews_setup_blocks() {
    if(function_exists('register_block_type')) {
		require_once( plugin_dir_path( __FILE__ ) . 'blocks/codeable-review-list.php');
		require_once( plugin_dir_path( __FILE__ ) . 'blocks/codeable-expert-image.php');
		require_once( plugin_dir_path( __FILE__ ) . 'blocks/codeable-hire-button.php');
    }
}
add_action( 'plugins_loaded', 'codeable_reviews_setup_blocks' );

/**************************************
* Function to enqueue plugin stylesheet
***************************************/

function codeable_css_enqueue() {
    wp_enqueue_style( 'codeable-reviews-and-experts-css', plugins_url( 'css/codeable-expert-styles.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'codeable_css_enqueue' );


/****************************************
* Function to pull / store / retrieve expert data
*****************************************/
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

/****************************************
* Function to pull / store / retrieve expert reviews
*****************************************/

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

				//Breaks out and stops if more reviews are requested than actually exist
				if (empty($temp_review_data)) {
					$broke_early = true;
					break;
				}

				$codeable_review_data=array_merge($codeable_review_data,$temp_review_data);
			}
		}


		//Parse off any "overages" from grabbing in batches of 4 (only if we didn't break early)
		$number_on_last_page = $number_of_reviews % 4;
		if ($number_on_last_page != 0 && !isset($broke_early)) {

			$number_to_remove = 4 - $number_on_last_page;

			$codeable_review_data = array_pop_n($codeable_review_data,$number_to_remove);

		}

		// Save the API response so we don't have to call again for another hour.
		set_transient( 'codeable_'.$codeable_id.'_review_'.$number_of_reviews, $codeable_review_data, $four_hours );

		return $codeable_review_data;   
	}
}

//Removes last n elements of array, utility for caching reviews.
function array_pop_n(array $arr, $n) {
    return array_splice($arr, 0, -$n);
}


/****************************
* Function to display the expert image
****************************/
function codeable_display_expert_image( $atts ){

	$atts = shortcode_atts(
		array(
			'codeable_id' => '',			//Codeable expert ID #
			'circle'=> 'yes',			//Whether the image should be circular when displayed
			'class'=> 'codeable-profile-image',	//Optional extra class to add for easier styling
			'loading'=> 'none',
		), $atts, 'expert_image' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	//Pulls expert data from API / Transient
	$codeable_expert_data = codeable_handle_expert_transient($atts['codeable_id']);

	$codeable_image_url= $codeable_expert_data->avatar->large_url;
	
	$return_image = "<img src='".esc_url($codeable_image_url)."'";

	if ($atts['circle'] == 'yes') {
		$return_image .= " style='border-radius: 50%;'";
	}
	if ($atts['class'] !='codeable-profile-image') {

		$class = $atts['class'];
		$return_image .= " class='codeable-profile-image " .esc_attr($class)."'";

	} else {

		$return_image .= " class='codeable-profile-image'";

	}
	
	if($atts['loading'] == 'lazy') {
	
		$return_image .= " loading='lazy'";
	}

	$return_image.=" alt='Codeable Expert Profile Picture'>";
	
	return $return_image;
}
add_shortcode( 'expert_image', 'codeable_display_expert_image' );

/***************************************
* Function to return expert rating ( returns decimal, text)
****************************************/

function codeable_display_expert_rating($atts) {


	$atts = shortcode_atts(
		array(
			'codeable_id' => '',	//Codeable expert ID
		), $atts, 'expert_rating' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	//Pulls expert data from API / transient
	$codeable_expert_data = codeable_handle_expert_transient($atts['codeable_id']);

	return $codeable_expert_data->average_rating;

}
add_shortcode( 'expert_rating', 'codeable_display_expert_rating' );

/***********************************************************
* Function to return number of completed tasks (returns int / text)
*************************************************************/
function codeable_display_expert_completed($atts) {


	$atts = shortcode_atts(
		array(
			'codeable_id' => '', //Expert ID
		), $atts, 'expert_completed' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	$codeable_expert_data = codeable_handle_expert_transient($atts['codeable_id']);

	return $codeable_expert_data->completed_tasks_count;

}
add_shortcode( 'expert_completed', 'codeable_display_expert_completed' );

/****************************************************
* Function to create a button for a preferred task (Hire Me)
****************************************************/

function codeable_display_expert_hire($atts) {

	$atts = shortcode_atts(
		array(
			'codeable_id' => '',	//Expert ID
			'message' => 'Hire Me',	//Message / text on button
			'referoo' => '',	//Referoo code
			'class'=> '',		//Optional class to add to button, for easier styling
			'theme' => 'black',	//default theme (white, black, none)
		), $atts, 'expert_hire' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	$message = $atts['message'];
	
	$codeable_direct_hire_link = 'https://app.codeable.io/tasks/new?preferredContractor='.absint($atts['codeable_id']);
	
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
		$button.=' '.esc_attr($atts['class']);
	}
	
	$button.="'>$message</a>";
	
	return $button;

}

add_shortcode( 'expert_hire', 'codeable_display_expert_hire' );

/**************************************************************
* Function to display expert reviews
**************************************************************/

function codeable_display_reviews($atts){

	$atts = shortcode_atts(
		array(
			'codeable_id' => '',	//expert ID
			'number_to_show'=>'',	//Legacy, not used
			'number_to_pull'=>20,	//how many to store
			'show_title'=>'no',	//show project title or not
			'show_date'=>'no',	//show review date or not
			'min_score'=> '',	//minimum score to allow (disp)
			'max_score'=> '',	//maximum score to allow (disp)
			'sort'=> '',		//order to display in (rand only option currently, beside default new to old)
			'start_at' => 1,	//Which one of the matching stored to start at, useful for chunking.
			'show_x_more' => 0,	//How many to display out of matches (0 is max possible)
			'min_review_length'=> 0,//Minimum review length to disp
			'has_picture'=> 'no',	//Control showing only those with set profile images
			'show_rating'=> 'yes',	//Show rating on each review
			'filter_clients' => '',	//Comma seperated list of client IDs to filter out
			'filter_reviews' => '', //Comma seperated list of review IDs to filter out
			'only_clients' => '',	//Comma seperated list of client IDs to include (filters all others)
			'only_reviews' => '',	//Comma seperated list of review IDs to include (filters all others)
			'schema' => '', 	//Send yes to include review schema (once per page only)
			'schema_desc' => 'Custom WordPress work through Codeable.io', //Product description for schema
			'start_time' => '',	//Unix timestamp, shows reviews after this time
			'end_time' => '', 	//Unix timestamp, shows reviews before this time
			'loading' => 'none',
		), $atts, 'expert_completed' );

	if (empty($atts['codeable_id'])) {

		return 'You must enter a valid Codeable Expert ID';
	}

	//Legacy shortcode att, added to prevent breaking change from number_to_show to better named number_to_pull
	if (!empty($atts['number_to_show'])) {

		$to_pull = $atts['number_to_show'];

	} else {

		$to_pull = $atts['number_to_pull'];
	}
	
	
	//Retrieves (from api or transient) all stored reviews
	$codeable_review_data = codeable_handle_review_transient($atts['codeable_id'],$to_pull);

	$schema = $atts['schema'];
	if ($schema=='yes') {

		$codeable_expert_data = codeable_handle_expert_transient($atts['codeable_id']);

		$schema_data = '
		<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "Product",
		  "aggregateRating": {
		    "@type": "AggregateRating",
		    "ratingValue": "'.$codeable_expert_data->average_rating.'",
		    "reviewCount": "'.$codeable_expert_data->completed_tasks_count.'"
		  },
		  "description": "'.$atts['schema_desc'].'",
		  "name": "'.$codeable_expert_data->full_name.'",
		  "image": "'.$codeable_expert_data->avatar->large_url.'",
		  "review": [
'; 		

	}

	//Filters out / removes all with default images, if that att is set
	if ($atts['has_picture'] == 'yes') {

		$codeable_review_data = array_filter($codeable_review_data,function($review){

			if( $review->reviewer->avatar->medium_url != 'https://s3.amazonaws.com/app.codeable.io/avatars/default/medium_default.png') {
				return true;
			} else {
				return false;
			}
		});

	}

	//Filters out / removes all with review length less than the minimum
	if ($atts['min_review_length'] != 0) {

		$min_review_length = $atts['min_review_length'];

		$codeable_review_data = array_filter($codeable_review_data,function($review) use($min_review_length){
    			return strlen($review->comment) >= $min_review_length;
		});

	}

	//Filters out / removes all above the max score 
	if (!empty($atts['max_score'])) {
		
		$max_score = $atts['max_score'];
		$codeable_review_data = array_filter($codeable_review_data,function($review) use($max_score){
    			return $review->score <= $max_score;
		});

	}

	//Filters out / removes all below the min score 
	if (!empty($atts['min_score'])) {
		
		$min_score = $atts['min_score'];
		$codeable_review_data = array_filter($codeable_review_data,function($review) use($min_score){
    			return $review->score >= $min_score;
		});

	}

	//Shows only reviews published after the unix timestamp for start time 
	if (!empty($atts['start_time'])) {
		
		$start_time = $atts['start_time'];
		$codeable_review_data = array_filter($codeable_review_data,function($review) use($start_time){
    			return $review->timestamp >= $start_time;
		});

	}

	//Shows only reviews published before the unix timestamp for end time
	if (!empty($atts['end_time'])) {
		
		$end_time = $atts['end_time'];
		$codeable_review_data = array_filter($codeable_review_data,function($review) use($end_time){
    			return $review->timestamp <= $end_time;
		});

	}

	//Filters out matching client ID #'s
	if (!empty($atts['filter_clients'])) {
		
		$filter_clients = preg_replace('/\s+/', '', $atts['filter_clients']);
		$clients_array = explode(',',$filter_clients);
		$codeable_review_data = array_filter($codeable_review_data,function($review) use($clients_array){
    			return !in_array($review->reviewer->id,$clients_array);
		});

	}

	//Filters out matching review ID #'s 
	if (!empty($atts['filter_reviews'])) {
		
		$filter_reviews = preg_replace('/\s+/', '', $atts['filter_reviews']);
		$reviews_array = explode(',',$filter_reviews);
		$codeable_review_data = array_filter($codeable_review_data,function($review) use($reviews_array){
    			return !in_array($review->id,$reviews_array);
		});

	}

	//Filters out all NON matching client ID #'s
	if (!empty($atts['only_clients'])) {
		
		$only_clients = preg_replace('/\s+/', '', $atts['only_clients']);
		$clients_array = explode(',',$only_clients);
		$codeable_review_data = array_filter($codeable_review_data,function($review) use($clients_array){
    			return in_array($review->reviewer->id,$clients_array);
		});

	}

	//Filters out all NON matching review ID #'s 
	if (!empty($atts['only_reviews'])) {
		
		$only_reviews = preg_replace('/\s+/', '', $atts['only_reviews']);
		$reviews_array = explode(',',$only_reviews);
		$codeable_review_data = array_filter($codeable_review_data,function($review) use($reviews_array){
    			return in_array($review->id,$reviews_array);
		});

	}

	//Shuffles randomly, doesn't work with the offset
	if ($atts['sort'] == 'rand') {

		shuffle($codeable_review_data);

	}


	$to_return = '<ul class="codeable_reviews">';

	$review_num = 1;
	$showed_this_run = 0;

	foreach ($codeable_review_data as $review) {
		
		if ($review_num < $atts['start_at']) {
			$review_num++;			
			continue;
		}

				
		$task_title = $review->task_title;
		$score = $review->score;
		$time = $review->timestamp;
		$comment = $review->comment;
		$name = $review->reviewer->full_name;
		$image = $review->reviewer->avatar->medium_url;
		$reviewer_id = $review->reviewer->id;
		$review_id = $review->id;


		if($schema=='yes') {

		    if ($showed_this_run != 0) {

			$schema_data.=',';
		    }
		    $schema_data.='
			{
		      "@type": "Review",
		      "author": "'.$name.'",
		      "datePublished": "'.date('Y-m-d',$time).'",
		      "description": "'.$comment.'",
		      "name": "",
		      "reviewRating": {
		        "@type": "Rating",
		        "bestRating": "5",
		        "ratingValue": "'.$score.'",
		        "worstRating": "1"
		      }
		    }';
		} 


		$score_disp = '';

		if ($atts['show_rating'] != 'no') {

			for ($i=0;$i<$score;$i++) {
				$score_disp .= "<img src='".plugins_url( 'img/rating-star.png', __FILE__ )."' alt='Rating Star' class='review_rating_star'";
				
				if($atts['loading'] == 'lazy') {
	
					$score_disp .= " loading='lazy'";
	
				}
				$score_disp.= ">";
			}
		}

		$to_return.= "<li class='codeable_review review_$review_id reviewer_$reviewer_id'>
		<img src='".esc_url($image)."' alt='User Image for Reviewer' class='reviewer_image'";
		
		if($atts['loading'] == 'lazy') {
	
			$to_return .= " loading='lazy'";
	
		}
		
		echo ">
		<div class='review_info'>";

		if($atts['show_title'] == 'yes') {
			$to_return.="<p class='review_task_title'>".esc_html($task_title)."</p>";
		}

		if ($atts['show_rating'] != 'no') {

			$to_return.="<p class='review_rating'>Project Rating: <span style='display:inline-block'>".$score_disp."</span></p>";
		}

		$to_return .= "<p class='review_text'>".esc_html($comment)."</p><p class='reviewer_name'>- ".esc_html($name);

		if($atts['show_date'] == 'yes') {

			$pretty_date = date('F j Y',$time);
			$to_return .=", <span class='review_date'>$pretty_date</span>";
		}

		$to_return.="</p></div></li>";

		$showed_this_run++;

		if ($showed_this_run >= $atts['show_x_more'] && $atts['show_x_more'] !=0) {

			break;

		}

	}
	$to_return.= '</ul>';

	if($schema == 'yes') {

		$schema_data.='
		    
		  ]
		}
		</script>';

		$to_return.=$schema_data;
	}

	return $to_return;

}
add_shortcode('expert_reviews','codeable_display_reviews');
