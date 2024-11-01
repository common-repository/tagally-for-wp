=== TagALLY-for-WP ===
Contributors: yier
Donate link: 
Tags: tag cloud, widget, share, ajax, related content, sidebar, plugin
Requires at least: 2.5
Tested up to: 2.8
Stable tag: 0.5

It is a very distinctive tag cloud plugin, it can shine, pop up related-tip, and link you and others through tags.

== Description ==

It will display a distinctive tag cloud in sidebar, each tag in the tag cloud will pop up a tooltip that lists the hottest and latest posts' excerpts and url links related with the tag.  These posts come from not only your blog but also other blogs. Through sharing publish-status posts' excerpts among blogs, more people who access other blogs will have a chance to know your blog, and, you'll be able to know more information of others also. Furthermore, Whenever a new post adding tags is published in any of these wordpress blogs, the same tags in your tag cloud will glitter to inform you of the updates. If you have any question, please mail to <a href="mailto:tagally@gmail.com">tagally@gmail.com</a>.

== Installation ==

<ol>
	<li>Copy the whole tagally-for-wp folder to <code>'/wp-content/plugins/'</code> directory.</li>
	<li>Deactivate All UltimateTagWarrior plugins to avoid potential conflict.</li>
	<li>Activate the Plugin: Go to the WordPress Administration <b>Plugins</b> Panel and activate <b>TagALLY-for-WP</b> plugin.</li>
	<li>Set Options: From the WordPress Administration Panels, choose <b>Dashboard > TagALLY > Options</b>. Set the options to meet your needs.</li>
  <li>Make sure that your theme supports widgets and the code "&lt;?php wp_head(); ?&gt;" is written  before "&lt;/head&gt;" in the header.php file and the code "&lt;?php wp_footer(); ?&gt;" is written before "&lt;/body&gt;" in the footer.php file of your theme.</li>
</ol>

== Setting options ==

<p>From the WordPress Administration Panels, choose <b>Dashboard > TagALLY > Options</b>. Set the options to meet your needs.</p>
<ul>
	<li><b>Pop up tooltips in tagcloud:</b> selecting yes to pop up tooltips when moving mouse cursor to a tag displayed in tagcloud.</li>
	<li><b>Most popular Font:</b> most popular tag will be displayed in the font in the tag cloud.</li>
	<li><b>Least popular Font:</b> least popular tag will be displayed in the font in the tag cloud.</li>
	<li><b>TagCloud Title:</b> the title of tagcloud.</li>
	<li><b>Showing how many tags in tagcloud most:</b> the most number of tags displayed in tagcloud.</li>
	<li><b>TagCloud color type:</b> selecting any one of three types to configure tag cloud color.
		<ul>
			<li><b>Custom Colorful TagCloud:</b> display a colorful tag cloud according to your custom color scheme which consists of a series of colors.</li>
			<li><b>Progressive Color TagCloud:</b> according to "Most popular color" and "Least popular color", display tag cloud in progressive colors.
				<ul>
					<li><b>Most popular color:</b>  most popular tag will be displayed in the color in the tag cloud.</li>
					<li><b>Least popular color:</b> lease popular tag will be displayed in the color in the tag cloud.</li>
				</ul>
			</li>
			<li><b>Standard Colorful TagCloud:</b> display a colorful tag cloud accroding to your selection from standard color schemes the plugin supplies.</li>
	  </ul>
	</li>
	<li><b>TagCloud sample background color:</b> set tagcloud preview sample background color.</li>
</ul>

== Export posts and related tags ==
<p>From the WordPress Administration Panels, choose <b>Dashboard > TagALLY > Manage</b>, you'll be able to use one-click method to export your posts and related tags.</p>
<p>We'll only collect those publish-status posts's links and excerpts grouped by their tags in <a href="http://www.tagally.com">TagALLY</a> website, then make them sharable among wordpress blogs and later even anyone who accesses TagALLY website.</p>

== Displaying a Tag Cloud in sidebar ==
<p>If your blog version is lower than wordpress 2.7, Go to <b>Design > Widgets</b> Panel, find out 'TagAlly: Tag Cloud' and press 'Add' beside it. Finally, <b>Don't forget to press 'Save Changes' button to save your setting.</b>. For wordpress 2.7 or higher, Go to <b>Appearance > Widgets</b> Panel, drag the <b>"TagAlly-for-WP:TagCloud"</b> to your sidebar.
</p>

== Frequently Asked Questions ==
<ol>
	<li>Q: Working in WP 2.5, installed, turned on plug-in and configured it. When I go to my 'widgets' menu, there is no "TagAlly: Tag Cloud" available as a widget to add to the sidebar. </li>
	<p>A: We've fixed the bug in TagALLY-for-WP 0.31.
	</p>
	<li>Q: Why does my blog not display the color tag cloud like your blog("<a href='http://blog.tagally.com'>blog.tagally.com</a>") after I activate "TagAlly-for-WP:TagCloud"? </li>
	<p>A: Firstly, please make sure you've dragged the "TagAlly-for-WP:TagCloud" to your sidebar on your WordPress Administration <b>Presentation > Widgets</b> Panel. Secondly, please make sure your theme supports widget. That is to say that your theme will change the sidebar content according to your arrangement after you drag and drop the various elements of your sidebar content and click "Save changes" button on WordPress Administration <b>Presentation > Widgets</b> Panel.
	</p>
	<li>Q: Why couldn't the tooltip pop up when I move mouse cursor onto a tag displayed in my blog tag cloud? </li>
	<p>A: Please make sure that the code "&lt;?php wp_head(); ?&gt;" is written  before "&lt;/head&gt;" in the header.php file and the code "&lt;?php wp_footer(); ?&gt;" is written before "&lt;/body&gt;" in the footer.php file of your theme.
	</p>
	<li>Q: Why couldn't my blog web page be loaded successfully when I set "Pop up tooltips in tagcloud" option to "yes"? </li>
	<p>A: This may be a bug, we have fixed it in new version. You may upgrade your TagALLY-for-WP plugin to the newest version.
	</p>
</ol>

== Upgrading to version 0.5 from lower version ==
<p>Copy the new files to your '/wp-content/plugins/tagally-for-wp' directory, overwriting old files. You also make use of 'Plugin Update Notification' of wordpress 2.5 or higher to upgrade your plugin.
</p>

== Change Log ==
<ul>
	<li>version 0.5</li>
	<ol>
		<li>This version is only compatible with wordpress 2.5 and higher. If your wordpress is version 2.2 - 2.3.3, please download TagALLY-for-WP v0.31.</li>
		<li>Add a progress bar to show the progress of exporting posts.</li>
		<li>Make some small updates to makes the plugin compatible with WordPress 2.8</li>
	</ol>
	<li>version 0.31</li>
	<ol>
		<li>fix the bug that there is no "TagAlly: Tag Cloud" available in 'widgets' menu.</li>
	</ol>
	<li>version 0.3</li>
	<ol>
		<li>for more convenient use, integrate all TagALLY menus into <b>Dashboard > TagALLY </b>.</li>
		<li>add two options 'TagCloud title' and 'Showing how many tags in tagcloud most' in WordPress Administration Panels <b>Dashboard > TagALLY > Options</b> to config the tagcloud title and the the most number of tags displayed in tagcloud.</li>
		<li>enhance tagcloud color config function. support three ways to configure tagcloud color which are <b>Custom Colorful TagCloud</b> and <b>Progressive Color TagCloud</b> and <b>Standard Colorful TagCloud</b>.</li>
		<li>in pop-up tooltip of sidebar tagcloud, not only show visitors the latest and hottest posts from all blogs, but also show them the latest and hottest posts of only your blog.</li>
	</ol>
	<li>version 0.25</li>
	<ol>
		<li>fix a bug that would cause web page not to be loaded successfully when there are a lot of tags in tag cloud and "Pop up tooltips in tagcloud" option is setted to "yes"</li>
		<li>add tag cloud color config option in the WordPress Administration Panels "Options > TagALLY".</li>
	</ol>
	<li>version 0.2</li>
	<ol>
		<li>fix some bugs</li>
		<li>add a field named 'slug' in table 'wp_tags' for blogs lower than wordpress 2.3</li>
		<li>improve the way the tag cloud displays in various fonts</li>
		<li>add blog name which the latest and hottest posts come from in pop-up tooltip of tag cloud</li>
		<li>add a function that enable visitors to browse all posts of your blog related with the tag throuth clicking "tag: tag name" in pop-up tooltip of tag cloud</li>
	</ol>
	<li>version 0.1</li>
	<ol>
		<li>Initial version</li>
	</ol>
</ul>
