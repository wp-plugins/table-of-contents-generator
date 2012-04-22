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