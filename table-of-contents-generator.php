<?php
/**
* Plugin Name: Table of Contents Generator
* Plugin URI: http://www.wpcube.co.uk/plugins/table-of-contents-generator-pro
* Version: 1.5.4
* Author: WP Cube
* Author URI: http://www.wpcube.co.uk
* Description: Generates an ordered list by scanning a Page's content's headings. Placed within a Page using [TOC].
* License: GPL2
*/

/*  Copyright 2013 WP Cube (email : support@wpcube.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Table of Contents Generator Class
* 
* @package WP Cube
* @subpackage Table of Contents Generator
* @author Tim Carr
* @version 1.5.4
* @copyright WP Cube
*/
class TOCGenerator {
    /**
    * Constructor.
    */
    function __construct() {

        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name         = 'table-of-contents-generator'; // Plugin Folder
        $this->plugin->displayName  = 'Table of Contents Generator'; // Plugin Name
        $this->plugin->version      = '1.5.4';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );

        $this->plugin->upgradeReasons = array(
        	array(__('Site Wide Display Options'), __('Define site wide TOC settings for title, alignment, border, background color, font, font size and font color.')),
        	array(__('Always Display TOC'), __('Choose to have the table of contents static in the top left or right corner of the Page or Post as the user scrolls down.')),
        	array(__('Expandable TOC'), __('Allow site visitors to show / hide your table of contents.')),
        	array(__('Exclude Headings from TOC'), __('Choose to exclude specific heading tags (H1, H2 etc) from the Table of Contents listings.')),
        	array(__('Back to Top'), __('Choose to display a Back to Top anchor below each heading.')),
        );
        $this->plugin->upgradeURL = 'http://www.wpcube.co.uk/plugins/table-of-contents-generator-pro';
        
        // Dashboard Submodule
        if (!class_exists('WPCubeDashboardWidget')) {
			require_once($this->plugin->folder.'/_modules/dashboard/dashboard.php');
		}
		$dashboard = new WPCubeDashboardWidget($this->plugin); 
		
		// Hooks
        add_action('admin_menu', array(&$this, 'adminPanelsAndMetaBoxes'));
        add_action('plugins_loaded', array(&$this, 'loadLanguageFiles'));
        
        if (is_admin()) {
        	add_action('init', array(&$this, 'setupTinyMCEPlugins'));
        } else {
        	add_filter('wp_head', array(&$this, 'frontendHeader'));
        	add_filter('the_content', array(&$this, 'generateTOC'));
	    }
    }
    
    /**
    * Register the plugin settings panel
    */
    function adminPanelsAndMetaBoxes() {
        add_menu_page($this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array(&$this, 'adminPanel'), 'dashicons-list-view');
    }
    
	/**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function adminPanel() {
        // Save Settings
        if (isset($_POST['submit'])) {
        	if (isset($_POST[$this->plugin->name])) {
        		update_option($this->plugin->name, $_POST[$this->plugin->name]);
				$this->message = __('Settings Updated.', $this->plugin->name);
			}
        }
        
        // Get latest settings
        $this->settings = get_option($this->plugin->name);
        
		// Load Settings Form
        include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/views/settings.php');  
    }
    
    /**
	* Loads plugin textdomain
	*/
	function loadLanguageFiles() {
		load_plugin_textdomain($this->plugin->name, false, $this->plugin->name.'/languages/');
	}
    
    /**
    * Setup calls to add a button and plugin to the TinyMCE Rich Text Editors, except on the plugin's
    * own screens.
    */
    function setupTinyMCEPlugins() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
		if (get_user_option('rich_editing') == 'true') {
			add_filter('mce_external_plugins', array(&$this, 'addTinyMCEPlugin'));
        	add_filter('mce_buttons', array(&$this, 'addTinyMCEButton'));
    	}
    }
    
    /**
    * Adds a button to the TinyMCE Editor for shortcode inserts
    */
	function addTinyMCEButton($buttons) {
	    array_push($buttons, "|", 'tocgenerator');
	    return $buttons;
	}
	
	/**
    * Adds a plugin to the TinyMCE Editor for shortcode inserts
    */
	function addTinyMCEPlugin($plugin_array) {
	    $plugin_array['tocgenerator'] = $this->plugin->url.'/js/editor_plugin.js';
	    return $plugin_array;
	}
	
	/**
    * Outputs CSS in the frontend header if required
    */
    function frontendHeader() {
    	$settings = get_option($this->plugin->name);
    	if (is_array($settings) AND isset($settings['customCSS']) AND !empty($settings['customCSS'])) {
    		echo ('<style type="text/css"> '.$settings['customCSS'].' </style>');
    	} 	
    }

    /**
    * Scans the content for [TOC], replacing it with an ordered list as well as
    * headings with IDs
    *
    * @param string $content Content
    * @return string Content w/ TOC if required
    */
    function generateTOC($content) {
    	if (strpos($content, '[TOC]') === false) return $content; // [TOC] does not exist
    	$content = preg_replace_callback('#<h([1-6])(.*?)>(.*?)</h\1>|<!--nextpage-->#', array(&$this, 'ReplaceHeadings'), $content); // ID all headings
    	$toc = $this->buildTOC(); // Build TOC HTML
    	return str_replace('[TOC]', $toc, $content); // Return content with [TOC] replaced
    }
    
    /**
    * Called by GenerateTOC, provides an array of the next heading within the content
    *
    * @param array $match Match (0 => HTML text, 1 => heading type (1-6), 2 => additional attributes, 3 => text)
    * @return Edited Heading
    */
    function replaceHeadings($match) {
    	if ($match[3] == '<!--nextpage-->') return $match[0]; // Skip nextpage  	
    	$tocID = $this->generateUniqueTOCID($match[3], $match[1]);
    	return '<h'.$match[1].' id="'.$tocID.'"'.$match[2].'>'.$match[3].'</h'.$match[1].'>';
    }
    
    /**
    * Generates a unique table of contents heading ID
    *
    * @param string $heading Heading Text
    * @param int $headingType Heading Type (1,2,3,4,5,6)
    * @return string Unique Heading ID
    */
    function generateUniqueTOCID($heading, $headingType) {
        // Make heading name ID safe / compatible
        $newHeading = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', sanitize_title_with_dashes($heading));
        
        // Check if we have already used this one
        $count = 0;
        if (isset($this->headings) AND is_array($this->headings)) {
        	foreach ($this->headings as $existingHeading=>$headingTitle) {
        		if ($existingHeading == $newHeading) $newHeading = $newHeading.($count++);
        	}
        }
        
        // We now have a unique heading ID
        $newHeading = 'toc-'.$newHeading;
        $this->headings[$newHeading] = array(
        	'type' => $headingType,
        	'title' => strip_tags($heading),
        ); // Removes anchor and other tags that might be within the header.
        
        return $newHeading;
    }
    
    /**
    * Generates the table of contents based on the headings array defined when scanning
    * for headings in GenerateUniqueTOCID
    *
    * @return string Table of Contents HTML
    */
    function buildTOC() {
    	if (!$this->headings OR !is_array($this->headings)) return ''; // No headings, so no TOC HTML required
    	
    	$count = 0;
    	$html = '<ol class="toc-generator">';
    	foreach ($this->headings as $key=>$heading) {
    		$html .= '<li class="heading-'.$heading['type'].(($count == 0) ? ' selected' : '').'"><a href="#'.$key.'" title="Jump to '.$heading['title'].'">'.$heading['title'].'</a></li>';
    		$count++;
    	}
    	$html .= '</ol>';
    	
    	return $html;
    }

}
$tocGenerator = new TOCGenerator();
?>