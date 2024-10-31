<?php
/*
	Plugin Name: Post Tags and Archives
	Plugin URI: http://www.oxpal.com/main.php?o=dev_wordpress_pta
	Description: Allows you to add the tag cloud and archives into posts and pages. Simply write [POSTTAGS] or [POSTARCHIVES] in the text.  

	Author: Thomas Schmall
	Author URI: http://www.oxpal.com
  Text Domain: post-tags-and-archives
	Version: 1.1.1
  License: GPL2
	
	Copyright 2011-2021, Thomas Schmall 

  Licenced under the GNU GPL: 
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

  This program is distributed without any warranty. See the 
  GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// check for WP context
if ( !defined('ABSPATH') ){ die(); }

//initially set the options
function pta_posttagsandarchives_install () {
	$newoptions = get_option('pta-posttagsandarchives_options');

  $newoptions = pta_getDefaults($newoptions);

  add_option('pta-posttagsandarchives_options', $newoptions);
  return;
}//install

function pta_getDefaults ($optionsArray) {
  /* Reference list of arguments for cloud:
$args = array(
    'smallest'                  => 8, 
    'largest'                   => 22,
    'unit'                      => 'pt', / pt, px, em, %
    'number'                    => 45,  number: If its 0 then all are shown`
    'format'                    => 'flat', ('list' (UL) or 'array'(for use in php))
    'separator'                 => \"\n\", between text /default: '\n' (whitespace)
    'separator'                 => "\n/\n"
    'orderby'                   => 'name', (count)
    'order'                     => 'ASC',    * 'DESC' * 'RAND' tags are in a random order. 
    'exclude'                   => null, 
    'include'                   => null, 
    'topic_count_text_callback' => default_topic_count_text,
    'link'                      => 'view', 
    'taxonomy'                  => 'post_tag', 
    'echo'                      => true 
  ); 
   */
	$optionsArray['smallest'] = '10';
	$optionsArray['largest'] = '29';
	$optionsArray['number'] = null;
	$optionsArray['orderby'] = 'name';
	$optionsArray['order'] = 'ASC';
	$optionsArray['exclude'] = null;
	$optionsArray['include'] = null;
	$optionsArray['format'] = 'flat';
	$optionsArray['separator'] = "<span style='color: #efefef;'>&bull;</span>";

/* Reference list of arguments for archive:
$args = array(
 <?php $args = array(
    'type'            => 'monthly', yearly, daily, weekly, postbypost, alpha (same as psotbypost but ordered by title)
    'limit'           => null, (number of archives to get)
    'format'          => 'html', option, link (only for head, so doesn't apply), custom (uses before and after)
    'before'          => null, string (text before link)
    'after'           => null, string (text after link)
    'show_post_count' => false, bool (show number of posts/archive)
    'echo'            => true, false (return out put or echo)
    'order'           => 'DESC' 'ASC'
); ?>
  */

	$optionsArray['archives_type'] = 'monthly';
	$optionsArray['archives_limit'] = null;
	$optionsArray['archives_format'] = 'html';
	$optionsArray['archives_before'] = null;
	$optionsArray['archives_after'] = null;
	$optionsArray['archives_show_post_count'] = false;
	$optionsArray['archives_order'] = 'DESC';

  return $optionsArray;
}

// add the admin page
function pta_posttagsandarchives_add_pages() {
  add_options_page('Post Tags and Archives', 'Post Tags & Archives', 'manage_options', __FILE__, 'pta_posttagsandarchives_options');
}

function pta_posttagsandarchives_uninstall () {
	delete_option('pta-posttagsandarchives_options');
	//delete_option('pta-posttagsandarchives_widget'); 
  if( function_exists('add_shortcode') )
  {
    remove_shortcode('POSTARCHIVES');
    remove_shortcode('postarchives');
    remove_shortcode('postarchive');
    remove_shortcode('POSTARCHIVE');
    remove_shortcode('posttags');
    remove_shortcode('POSTTAGS');
   }
  return;
}//uninstall

// shortcode function
function pta_posttags_shortcode( $atts=NULL )
{
  return pta_GetPostTags();
}
function pta_postarchives_shortcode( $atts=NULL )
{
  return pta_GetPostArchives();
}

// php function for integration
function pta_posttags( $atts=NULL )
{
  echo pta_GetPostTags();
}
function pta_postarchives( $atts=NULL )
{
  echo pta_GetPostArchives();
}

// replace tag in content with tag cloud (non-shortcode version for WP 2.3.x)
function pta_posttagsandarchives_init($content){
  $postTags = true;
  $postArchives = true;

	if( strpos($content, '[POSTTAGS]') === false ){
    $postTags = false;
  };

	if( strpos($content, '[POSTARCHIVES]') === false ){
    $postArchives = false;
  };

  if($postTags)
  {
    $code = pta_GetPostTags();
		$content = str_replace( '[POSTTAGS]', $code, $content );
	}

  if($postArchives)
  { 
    $code = pta_GetPostArchive();
    $content = str_replace( '[POSTARCHIVES]', $code, $content );
	}

  return $content;
}//init

function pta_GetPostTags()
{
  $args = pta_posttagsandarchives_getargs();
  $encloseBegin = ' <div class="pta-posttags">';
  $encloseEnd = '</div>';
  //echo $encloseBegin;
  $code = $encloseBegin.wp_tag_cloud($args).$encloseEnd;
  return $code;
}
function pta_GetPostArchives()
{ 
  $args = pta_posttagsandarchives_getargs_archives();
  $tEncloseBegin = "";
  $tEncloseEnd = "";
  if ($args['format'] == 'html') 
  {
    $tEncloseBegin = "<ul>";
    $tEncloseEnd = "</ul>";
  }

  $encloseBegin = ' <div class="pta-postarchives">'.$tEncloseBegin;
  $encloseEnd = $tEncloseEnd.'</div>';
  //echo $encloseBegin;
  $code = $encloseBegin.wp_get_archives($args).$encloseEnd;
  return $code;
}

function pta_posttagsandarchives_getargs(){
	$tempoptions = get_option('pta-posttagsandarchives_options');

  $args = array(
    'smallest'                  => $tempoptions['smallest'], 
    'largest'                   => $tempoptions['largest'],
    'number'                    => $tempoptions['number'],
    'orderby'                   => $tempoptions['orderby'],
    'order'                     => $tempoptions['order'],
    'exclude'                   => $tempoptions['exclude'],
    'include'                   => $tempoptions['include'],
    'format'                    => $tempoptions['format'],
    'separator'                 => "\n".$tempoptions['separator'],
    'taxonomy'                  => 'post_tag', 
    'link'                      => 'view', 
    'echo'                      => false

  );

  return $args; 
}

function pta_posttagsandarchives_getargs_archives(){
	$tempoptions = get_option('pta-posttagsandarchives_options');

  $args = array(
    'type'                  => $tempoptions['archives_type'], 
    'limit'                 => $tempoptions['archives_limit'],
    'format'                => $tempoptions['archives_format'],
    'before'                => $tempoptions['archives_before'],
    'after'                 => $tempoptions['archives_after'],
    'show_post_count'       => $tempoptions['archives_show_post_count'],
    'echo'                  => false,
    //'echo'                => $tempoptions['archives_echo'],
    'order'                 => $tempoptions['archives_order']
  );

  return $args; 
}



// options page
function pta_posttagsandarchives_options() {	
	$options = $newoptions = get_option('pta-posttagsandarchives_options');
	// if submitted, process results
	if ( $_POST["pta-posttagsandarchives_submit"] ) {
    $arrayDefaults = array();
    $arrayDefaults = pta_getDefaults($arrayDefaults);

    $tVal=null;

    //Tags
    $tVal= strip_tags(stripslashes($_POST["smallest"])); 
		$newoptions['smallest'] = is_numeric($tVal) ? $tVal : $arrayDefaults["smallest"];

    $tVal= strip_tags(stripslashes($_POST["largest"]));
		$newoptions['largest'] = is_numeric($tVal) ? $tVal : $arrayDefaults["largest"];

    $tVal= strip_tags(stripslashes($_POST["number"]));
		$newoptions['number'] = is_numeric($tVal) ? $tVal : $arrayDefaults["number"];

		$newoptions['orderby'] = strip_tags(stripslashes($_POST["orderby"]));
		$newoptions['order'] = strip_tags(stripslashes($_POST["order"]));
		//$newoptions['exclude'] = strip_tags(stripslashes($_POST["exclude"]));
		//$newoptions['include'] = strip_tags(stripslashes($_POST["include"]));

    $tstring = html_entity_decode(stripslashes($_POST["separator"]));
    $tstring = str_replace("\\n", "\n", $tstring);
		$newoptions['separator'] = $tstring;

    //archive
		$newoptions['archives_type'] = strip_tags(stripslashes($_POST["archives_type"]));
    $tVal= strip_tags(stripslashes($_POST["archives_limit"]));
    if(!is_numeric($tVal)) $tVal = $arrayDefaults["archives_limit"];
    if ($tVal == 0) $tVal = null;
		$newoptions['archives_limit'] = $tVal ;
		$newoptions['archives_format'] = strip_tags(stripslashes($_POST["archives_format"]));

    $tstring = html_entity_decode(stripslashes($_POST["archives_before"]));
    $tstring = str_replace("\\n", "\n", $tstring);
		$newoptions['archives_before'] = $tstring;

    $tstring = html_entity_decode(stripslashes($_POST["archives_after"]));
    $tstring = str_replace("\\n", "\n", $tstring);
		$newoptions['archives_after'] = $tstring;

    $newoptions['archives_show_post_count'] = $_POST["archives_show_post_count"];

		$newoptions['archives_order'] = $_POST["archives_order"];

    echo '<div id="message" class="updated fade"><p>'
         . __('Options changed.', 'pta-posttagsandarchives')
         . '</p></div>';
	}

	// any changes? save!
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('pta-posttagsandarchives_options', $options);
	}

	// options form
	echo '<form method="post">';
	echo '<h3>Post Tags And Archives - Plugin Options</h3>';
	echo "<div class=\"wrap\"><h2>Tag Cloud Options</h2>";
	echo '<table class="form-table">';
	// smallest
	echo '<tr valign="top"><th scope="row">Text size of smallest tag</th>';
	echo '<td><input type="text" name="smallest" value="'.$options['smallest'].'" size="5"></input>&nbsp;&nbsp;Size of the tag with the smallest count value - unit is pixel (px).</td></tr>';
	// largest
	echo '<tr valign="top"><th scope="row">Text size of largest tag</th>';
	echo '<td><input type="text" name="largest" value="'.$options['largest'].'" size="5"></input>&nbsp;&nbsp;Size of the tag with the highest count value - unit is pixel (px).</td></tr>';
	// number
	echo '<tr valign="top"><th scope="row">The number of tags to display.</th>';
  $tVal = $options['number'];
  if ($tVal == null) $tVal = 0;
	echo '<td><input type="text" name="number" value="'.$tVal.'" size="8"></input>&nbsp;&nbsp;The value 0 means all tags are displayed.</td></tr>';
	// orderby
	echo '<tr valign="top"><th scope="row">Order by:</th>';
	echo '<td><input type="radio" name="orderby" value="name"';
	if( $options['orderby'] == 'name' ){ echo ' checked="checked" '; }
	echo '></input> Name<br /><input type="radio" name="orderby" value="count"';
	if( $options['orderby'] == 'count' ){ echo ' checked="checked" '; }
	echo '></input> Count</td></tr>';
	// order
	echo '<tr valign="top"><th scope="row">Order direction:</th>';
	echo '<td><select name="order">';
  echo '<option ';
	if( $options['order'] == 'ASC' ){ echo ' selected="selected" '; }
	echo 'value="ASC">Ascending</option> <option ';
	if( $options['order'] == 'DESC' ){ echo ' selected="selected" '; }
	echo 'value="DESC">Descending</option> <option ';
  if( $options['order'] == 'RAND' ){ echo ' selected="selected" '; }
	echo 'value="RAND">Random</option>';
	echo '></select> </td></tr>';

  /*
  //exclude
	echo '<tr valign="top"><th scope="row">Exclude tags</th>';
	echo '<td><input type="text" name="exclude" value="'.$options['exclude'].'" size="60"></input><br />List of excluded tags.</td></tr>';	

  //include
	echo '<tr valign="top"><th scope="row">Include tags</th>';
	echo '<td><input type="text" name="include" value="'.$options['include'].'" size="60"></input><br />List of included tags.</td></tr>';	
   */

  //Separator
  echo '<tr valign="top"><th scope="row">Separator between tags:
          <br><i>Advanced! Test before using.</i> </th>';
  $tstring=htmlentities($options['separator']);
  $tstring=str_replace("\n","\\n", $tstring);
	echo '<td><input type="text" name="separator" value="'.$tstring.'" size="60"></input><br />Default: <b>&lt;span style=&#39;color: #efefef;&#39;&gt;&amp;bull;&lt;/span&gt;</b>
         </td></tr>';	

	echo '</table>';
  echo '</br></br>';


	echo "<div class=\"wrap\"><h2>Post Archives Options</h2>";
	echo '<table class="form-table">';
	//type
  echo '<tr valign="top"><th scope="row">Type of archive list to show:</th>';
  echo '<td> <select name="archives_type">';
  echo '<option ';
  if( $options['archives_type'] == 'yearly' ){ echo ' selected="selected"'; }
  echo 'value="yearly">Yearly</option>';
  echo '<option ';
  if( $options['archives_type'] == 'monthly' ){ echo ' selected="selected"'; }
  echo 'value="monthly">Monthly</option>';
  echo '<option ';
  if( $options['archives_type'] == 'weekly' ){ echo ' selected="selected"'; }
  echo 'value="weekly">Weekly</option>';
  echo '<option ';
  if( $options['archives_type'] == 'daily' ){ echo ' selected="selected"'; }
  echo 'value="daily">Daily</option>';
  echo '<option ';
  if( $options['archives_type'] == 'postbypost' ){ echo ' selected="selected"'; }
  echo 'value="postbypost">All posts (sorted by date)</option>';
  echo '<option ';
  if( $options['archives_type'] == 'alpha' ){ echo ' selected="selected"'; }
  echo 'value="alpha">All posts (sorted by title)</option>';
  echo '</select>';
  // <label for="stats_order">Limit:</label>';
  echo '</td></tr>';

  //format
  echo '<tr valign="top"><th scope="row">Format as:</th>';
  echo '<td> <select name="archives_format">';
  echo '<option ';
  if( $options['archives_format'] == 'html' ){ echo ' selected="selected"'; }
  echo 'value="html">List (and custom strings) </option>';
  echo '<option ';
  if( $options['archives_format'] == 'option' ){ echo ' selected="selected"'; }
  echo 'value="option">Advanced: Options for dropdown</option>';
  echo '<option ';
  if( $options['archives_format'] == 'custom' ){ echo ' selected="selected"'; }
  echo 'value="custom">Advanced: Use custom strings</option>';
  // <label for="stats_order">Limit:</label>';
  echo '</select></td></tr>';

	//limit
  $tstring = $options['archives_limit'] == null ? 0 : $options['archives_limit'];
	echo '<tr valign="top"><th scope="row">Number of archives to show:</th>';
	echo '<td><input type="text" name="archives_limit" value="'.$tstring.'" size="5"></input>';
	echo '<label for="archives_limit">&nbsp;&nbsp;The value 0 means no limit.</label>';
  echo '</td></tr>';

  //show_post_count
	echo '<tr valign="top"><th scope="row">Show number of posts per archive? </th>';
  echo '<td><input id="archives_show_post_count" name="archives_show_post_count" type="checkbox" value="1"';
  if ($options['archives_show_post_count']==1) echo ' checked';
  echo '/>';
  echo '</input></td></tr>';

	// order
	echo '<tr valign="top"><th scope="row">Order direction:</th>';
	echo '<td><input type="radio" name="archives_order" value="ASC"';
	if( $options['archives_order'] == 'ASC' ){ echo ' checked="checked" '; }
	echo '></input> Ascending<br /><input type="radio" name="archives_order" value="DESC"';
	if( $options['archives_order'] == 'DESC' ){ echo ' checked="checked" '; }
  echo '></input> Descending<br />';
  echo '</td></tr>';

  //Custom Strings
  echo '<tr valign="top"><th scope="row">Custom strings:
          <br><i>Advanced! Test before using.</i> </th>';
  $tstring=htmlentities($options['archives_before']);
  $tstring=str_replace("\n","\\n", $tstring);
  echo '<td width=60px><input type="text" name="archives_before" value="'.$tstring.'" size="40"></input>';
  echo '<br />Html before item. (Default ist empty)';

  echo '</td>';
  $tstring=htmlentities($options['archives_after']);
  $tstring=str_replace("\n","\\n", $tstring);
  echo '<td><input type="text" name="archives_after" value="'.$tstring.'" size="40"></input><br />Html after item. (Default is empty)</b>'; 
  echo '</td></tr>';	


	echo '</table>';

	echo '<input type="hidden" name="pta-posttagsandarchives_submit" value="true"></input>';
	echo '</table>';
	echo '<p class="submit"><input type="submit" value="Update Options &raquo;"></input></p>';
	echo "</div>";
	echo '</form>';
	
}

// add the actions
add_action('admin_menu', 'pta_posttagsandarchives_add_pages');
register_activation_hook( __FILE__, 'pta_posttagsandarchives_install' );
register_deactivation_hook( __FILE__, 'pta_posttagsandarchives_uninstall' );

if( function_exists('add_shortcode') ){
	add_shortcode('POSTARCHIVES', 'pta_postarchives_shortcode');
	add_shortcode('postarchives', 'pta_postarchives_shortcode');
	add_shortcode('postarchive', 'pta_postarchives_shortcode');
	add_shortcode('POSTARCHIVE', 'pta_postarchives_shortcode');
	add_shortcode('posttags', 'pta_posttags_shortcode');
	add_shortcode('POSTTAGS', 'pta_posttags_shortcode');
} else {
  add_filter('the_content','pta_posttagsandarchives_init');
}

