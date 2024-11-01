<?php
/*
Plugin Name: TagALLY-for-WP
version: 0.5
Plugin URI: http://blog.tagally.com/category/download
Description: Want to show more in your tag cloud? Want more people to know your blog? The plugin can help you to realize these ideas. If you are the first time to activate the plugin, please be sure to read the tagally-for-wp-help.html file , especially "Export posts and related tags" section in the file. If you have any question, please mail to <a href="mailto:tagally@gmail.com">tagally@gmail.com</a>.
Author: yier
Author URI: http://blog.tagally.com/
*/

require_once('tagally-for-wp-function.php');
require_once('tagally-for-wp-tagcloud-widget.php');

$tafwp = new TagAllyForWPFunction;

if (!$tafwp->tagsys)
{
	// Add or edit tags
	add_action('simple_edit_form', array(&$tafwp,'edit_post_tags'));
	add_action('edit_form_advanced', array(&$tafwp,'edit_post_tags'));
	add_action('edit_page_form', array(&$tafwp,'edit_post_tags'));

	
	// Show Tags to Browsers
	add_filter('the_content', array(&$tafwp, 'the_content_tags'));

	add_filter('query_vars', array(&$tafwp, 'tag_query_vars'));

	
	/*
	template_redirect
		No parameter. Executes before the determination of the template file to be used to
		display the requested page. Useful for providing additional templates based on request criteria.
	*/
	add_action('template_redirect', array(&$tafwp,'template_redirect_tag'));
	
	/*
	posts_join
	  Allows a plugin to modify the JOIN clauses of the query that returns the post array.
	  This is typically used to add a table to the JOIN, in combination with the posts_where trigger.
	*/
	add_filter('posts_join', array(&$tafwp,'posts_join'));
	add_filter('posts_where', array(&$tafwp,'posts_where'));
	add_filter('posts_orderby', array(&$tafwp,'posts_orderby'));
}

//add_action('edit_tag_form_pre', array(&$tafwp,'edit_tag_form_pre'));
//add_action('edit_post_tag', array(&$tafwp,'edit_tag'),10,2);
//add_action('delete_post_tag', array(&$tafwp,'delete_tag'),10,2);

// Save changes to tags
add_action('save_post', array(&$tafwp,'save_post_tags'));

// Delete post
add_action('delete_post', array(&$tafwp, 'delete_post_tags'));


add_filter('wp_head', array(&$tafwp, 'wp_head_add_javascript'));
add_filter('wp_footer', array(&$tafwp, 'wp_footer_add_dropmenu'));

add_filter('admin_head', array(&$tafwp, 'admin_head_add_javascript'));

// Admin menu items
add_action('admin_menu', array(&$tafwp, 'admin_menu_tag_option'));

// Admin ajax
add_action('wp_ajax_tafwp_ajax_export',array(&$tafwp, 'wp_ajax_export'));
add_action('wp_ajax_tafwp_ajax_export_status',array(&$tafwp, 'wp_ajax_export_status'));


function TAFWPShowTagCloud($count = 50)
{
	global $tafwp;
	$tafwp->ShowPopularTags($count);
}

?>