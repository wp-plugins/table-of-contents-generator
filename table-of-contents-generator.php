<?php
/**
* Plugin Name: Table of Contents Generator
* Plugin URI: http://www.n7studios.co.uk/2012/04/22/wordpress-table-of-contents-generator-plugin
* Version: 1.02
* Author: <a href="http://www.n7studios.co.uk/">n7 Studios</a>
* Description: Generates an ordered list by scanning a Page's content's headings. Placed within a Page using [TOC].
*/

/**
* TOC Generator Class
* 
* @package WordPress
* @subpackage Table of Contents Generator
* @author Tim Carr
* @version 1.02
* @copyright n7 Studios
*/
class TableOfContentsGenerator {
    /**
    * Constructor.
    */
    function TableOfContentsGenerator() {
        // Plugin Details
        $this->plugin->name = 'table-of-contents-generator';
        $this->plugin->displayName = 'Table of Contents Generator';
        $this->plugin->url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));          

        if (is_admin()) {
            add_action('init', array(&$this, 'InitPlugin'), 99);
            add_action('admin_menu', array(&$this, 'AddAdminMenu'));
        } else {
        	add_filter('wp_head', array(&$this, 'FrontendHeader'));
	        add_filter('the_content', array(&$this, 'GenerateTOC'));
        }
    }
    
    /**
    * Initialises the plugin within the WordPress Administration
    *
    * Registers a button for the TinyMCE rich text editor and sets up a new shortcode
    */
    function InitPlugin() {
    	// CSS
    	wp_register_style($this->plugin->name.'-admin-css', $this->plugin->url.'css/admin.css');
    	wp_enqueue_style($this->plugin->name.'-admin-css');
    
    	// TinyMCE
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
		if (get_user_option('rich_editing') == 'true') {
			add_filter('mce_external_plugins', array(&$this, 'AddTinyMCEPlugin'));
        	add_filter('mce_buttons', array(&$this, 'AddTinyMCEButton'));
    	}
    }
    
    /**
    * Adds a single option panel to Wordpress Administration
    */
    function AddAdminMenu() {
        add_menu_page($this->plugin->displayName, $this->plugin->displayName, 9, $this->plugin->name, array(&$this, 'AdminPanel'), $this->plugin->url.'images/icons/small.png');
    }
    
	/**
    * Adds a button to the TinyMCE Editor for shortcode inserts
    */
	function AddTinyMCEButton($buttons) {
	    array_push($buttons, "|", 'tocgenerator');
	    return $buttons;
	}
	
	/**
    * Adds a plugin to the TinyMCE Editor for shortcode inserts
    */
	function AddTinyMCEPlugin($plugin_array) {
	    $plugin_array['tocgenerator'] = $this->plugin->url.'js/editor_plugin.js';
	    return $plugin_array;
	}
	
	/**
    * Outputs the plugin Admin Panel in Wordpress Admin
    */
    function AdminPanel() {
        // Save Settings
        if (isset($_POST['submit'])) {
            update_option($this->plugin->name, $_POST[$this->plugin->name]);
            $this->message = __('Settings Updated.'); 
        }
        
        // Load form
        $this->settings = get_option($this->plugin->name); 
        include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/admin/settings.php');  
    }
    
    /**
    * Outputs CSS in the frontend header if required
    */
    function FrontendHeader() {
    	$settings = get_option($this->plugin->name);
    	if (is_array($settings) AND $settings['customCSS'] != '') {
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
    function GenerateTOC($content) {
    	if (strpos($content, '[TOC]') === false) return $content; // [TOC] does not exist
    	$content = preg_replace_callback('#<h([1-6])(.*?)>(.*?)</h\1>|<!--nextpage-->#', array(&$this, 'ReplaceHeadings'), $content); // ID all headings
    	$toc = $this->BuildTOC(); // Build TOC HTML
    	return str_replace('[TOC]', $toc, $content); // Return content with [TOC] replaced
    }
    
    /**
    * Called by GenerateTOC, provides an array of the next heading within the content
    *
    * @param array $match Match (0 => HTML text, 1 => heading type (1-6), 2 => additional attributes, 3 => text)
    * @return Edited Heading
    */
    function ReplaceHeadings($match) {
    	if ($match[3] == '<!--nextpage-->') return $match[0]; // Skip nextpage  	
    	$tocID = $this->GenerateUniqueTOCID($match[3]);
    	return '<h'.$match[1].' id="'.$tocID.'"'.$match[2].'>'.$match[3].'</h'.$match[1].'>';
    }
    
    /**
    * Generates a unique table of contents heading ID
    *
    * @param string $heading Heading Text
    * @return string Unique Heading ID
    */
    function GenerateUniqueTOCID($heading) {
        // Make heading name ID safe / compatible
        $newHeading = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', sanitize_title_with_dashes($heading));
        
        // Check if we have already used this one
        $count = 0;
        if ($this->headings AND is_array($this->headings)) {
        	foreach ($this->headings as $existingHeading=>$headingTitle) {
        		if ($existingHeading == $newHeading) $newHeading = $newHeading.($count++);
        	}
        }
        
        // We now have a unique heading ID
        $newHeading = 'toc-'.$newHeading;
        $this->headings[$newHeading] = strip_tags($heading); // Removes anchor and other tags that might be within the header.
        return $newHeading;
    }
    
    /**
    * Generates the table of contents based on the headings array defined when scanning
    * for headings in GenerateUniqueTOCID
    *
    * @return string Table of Contents HTML
    */
    function BuildTOC() {
    	if (!$this->headings OR !is_array($this->headings)) return ''; // No headings, so no TOC HTML required
    	
    	$html = '<ol class="toc-generator">';
    	foreach ($this->headings as $key=>$heading) {
    		$html .= '<li><a href="#'.$key.'" title="Jump to '.$heading.'">'.$heading.'</a></li>';
    	}
    	$html .= '</ol>';
    	
    	return $html;
    }
}
$tocGenerator = new TableOfContentsGenerator(); // Invoke class
?>
