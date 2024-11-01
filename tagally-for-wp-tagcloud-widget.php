<?php

function widget_tafwptagcloud_init()
{
	if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control'))
		return;
	function widget_tafwptagcloud($args)
	{
		extract($args);
		$maxtagcount = get_option('TAFWPMaxTagCount');
		$cloudtitle = get_option('TAFWPCloudTitle');
		$title = empty($cloudtitle) ? __('Tag Cloud') : $cloudtitle;
		$maxcount = empty($maxtagcount) ? 50 : (int)$maxtagcount;
		echo $before_widget . $before_title . $title . $after_title;
		TAFWPShowTagCloud($maxcount);
		echo $after_widget;
	}

//	register_sidebar_widget('TagAlly-for-WP:TagCloud', 'widget_tafwptagcloud');
	$widget_ops = array('classname' => 'tagally-tag-cloud', 'description' => __( "Your most used tags in colorful cloud format with multiple color config ways and more information") );
	wp_register_sidebar_widget('tagally-tag-cloud',__('TagAlly: Tag Cloud'), 'widget_tafwptagcloud', $widget_ops);
}

add_action('plugins_loaded', 'widget_tafwptagcloud_init');
?>