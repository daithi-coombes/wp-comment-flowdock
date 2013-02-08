<?php
/**
 * @package api-connection-manager
 * @subpackage wp-comment-flowdock
 */
/*
Plugin Name: WP Comment Flowdock
Plugin URI: http://david-coombes.com
Description: Pushes comments on posts to the flowdock chat
Version: 0.1
Author: Daithi Coombes
Author URI: http://david-coombes.com
*/

/**
 * The plugin should cover two aspects:
 * 1) Email posts to flowdock
 * 2) Comments on posts should be submited to the relevant inbox item in flow
 *
 * The WP Admin can add the API Keys for each of the flows that they want their 
 * blog to have access to. The flows added will then appear in the post-editor 
 * page as checkbox's, when the user creates a post they select the required 
 * flows and the post is submitted. All comments in wordpress on that post are 
 * then also submitted as comments to the flow inbox item.
 */

/**
 * Bootstrap
 */
function wp_post_flowdock_autoload($class){
	$filename = dirname(__FILE__) . "/class-".strtolower( str_replace("_", "-", $class)).".php";
	@include_once $filename;
}
spl_autoload_register("wp_post_flowdock_autoload");
//end Bootstrap


/**
 * 3rd parties
 */
require_once( WP_PLUGIN_DIR . "/api-connection-manager/index.php");
//end 3rd parties

//construct objects
$wp_post_flowdock_dash = new WP_Post_Flowdock_Dash();
