<div class="wrap">
    <div id="<?php echo $this->plugin->name; ?>-title" class="icon32"></div> 
    <h2><?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Settings'); ?></h2>
           
    <?php    
    if ($this->message != '') {
        ?>
        <div class="updated"><p><?php echo $this->message; ?></p></div>  
        <?php
    }
    if ($this->errorMessage != '') {
        ?>
        <div class="error"><p><?php echo $this->errorMessage; ?></p></div>  
        <?php
    }
    ?>        
        
    <form id="post" name="post" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <div id="poststuff" class="metabox-holder">
            <!-- Content -->
            <div id="post-body">
                <div id="post-body-content">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable" style="position: relative;">                        
                        <!-- Settings -->
                        <div class="postbox">
                            <h3 class="hndle"><?php _e('Settings'); ?></h3>
                            <div class="inside">
                                <p>
                                    <strong><?php _e('Table of Contents CSS'); ?></strong>
                                    <textarea name="<?php echo $this->plugin->name; ?>[customCSS]" class="widefat"><?php echo $this->settings['customCSS']; ?></textarea>  
                                </p>
                                <p class="description"><?php _e('Enter your own custom CSS to style any table of content lists.  Structure is:'); ?></p>
                                <p class="description">
                                	&lt;ol class="toc-generator"&gt;<br />
                                	&lt;li&gt;&lt;a href="#anchor" title="Heading Title">Heading Title&lt;/a&gt;&lt;/li&gt;<br />
                                	&lt;/ol&gt;
                                </p>
							</div>
                        </div>
                        
                        <!-- Go Pro -->
                        <div class="postbox">
                            <h3 class="hndle"><?php _e('Pro Settings and Support'); ?></h3>
                            <div class="inside">
                            	<p><?php echo __('Upgrade to '.$this->plugin->displayName.' Pro to configure additional options, including:'); ?></p>
                            	<ul>
                            		<li><strong><?php _e('Site Wide Display Options'); ?>: </strong><?php _e('Define site wide TOC settings for title, alignment, border, background color, font, font size and font color.'); ?></li>
									<li><strong><?php _e('Always Display TOC'); ?>: </strong><?php _e('Choose to have the table of contents static in the top left or right corner of the Page or Post as the user scrolls down.'); ?></li>
									<li><strong><?php _e('Expandable TOC'); ?>: </strong><?php _e('Allow site visitors to show / hide your table of contents.'); ?></li>
									<li><strong><?php _e('Exclude Headings from TOC'); ?>: </strong><?php _e('Choose to exclude specific heading tags (H1, H2 etc) from the Table of Contents listings.'); ?></li>
									<li><strong><?php _e('Back to Top'); ?>: </strong><?php _e('Choose to display a Back to Top anchor below each heading.'); ?></li>
									<li><strong><?php _e('Support'); ?>: </strong><?php _e('Access to support ticket system and knowledgebase.'); ?></li>
									<li><strong><?php _e('Documentation'); ?>: </strong><?php _e('Detailed documentation on how to install and configure the plugin.'); ?></li>
									<li><strong><?php _e('Updates'); ?>: </strong><?php _e('Receive one click update notifications, right within your WordPress Adminstration panel.'); ?></li>
                            		<li><strong><?php _e('Seamless Upgrade'); ?>: </strong><?php _e('Retain all current settings when upgrading to Pro.'); ?></li>
                            	</ul>
                            	<p><a href="http://www.wptoc.co.uk/" target="_blank" class="button">Upgrade Now</a></p>
                            </div>
                        </div>
                        
                        <!-- Save -->
                        <div class="submit">
                            <input type="submit" name="submit" value="<?php _e('Save'); ?>" /> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>