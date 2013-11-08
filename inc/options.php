<?php

add_action( 'admin_init', 'vitally_responsible_options_init' );
add_action( 'admin_menu', 'hammy_add_page' );


/**
 * Create a new page in the settings menu
 */
function hammy_add_page() {

    add_options_page( 'Vitally Responsible - Responsive Images', 'Responsive Images', 'manage_options', 'vitally-responsible', 'vitally_responsible_options_page' );

}

/**
 * Establish all of the settings to go on that page
 *
 * http://ottopress.com/2009/wordpress-settings-api-tutorial/
 * http://codex.wordpress.org/Function_Reference/add_settings_section
 * http://codex.wordpress.org/Function_Reference/add_settings_field
 */
function vitally_responsible_options_init() {

    register_setting( 'vitally_responsible_options', 'vitally_responsible_options', 'vitally_responsible_options_updated' );
    add_settings_section( 'vitally_responsible_section', '', 'vitally_responsible_options_content', 'vitally-responsible' );
    add_settings_field( 'vital_breakpoints', '', 'vital_option_breakpoints', 'vitally-responsible', 'vitally_responsible_section' );
    add_settings_field( 'vital_crops', '', 'vital_option_crops', 'vitally-responsible', 'vitally_responsible_section' );
    add_settings_field( 'vital_ignores', '', 'vital_option_ignores', 'vitally-responsible', 'vitally_responsible_section' );
    add_settings_field( 'vital_enqueue', '', 'vital_option_enqueue', 'vitally-responsible', 'vitally_responsible_section' );
    add_settings_field( 'vital_filter_content', '', 'vital_option_filter_content', 'vitally-responsible', 'vitally_responsible_section' );

}


/**
 * callback to register_settings (meant for validation) - used to empty caches when options change!
 */
function vitally_responsible_options_updated($input){
	delete_post_meta_by_key( 'vitally_filtered_responsibly_less' );
    delete_post_meta_by_key( 'vitally_filtered_responsibly_more' );
    return $input;
}


/**
 * Additional content in the header of the 'section'
 *
 * This is the callback from add_settings_section above.
 */
function vitally_responsible_options_content(){
	?>
	<h2 class="vr-header">Vitally Responsible - Responsive Images</h2>

	<p>Automatic responsive images for post and page content using the <a href="https://github.com/scottjehl/picturefill" target="_blank">Picturefill</a> syntax.</p>

	<div id="code-warning">
		<p><strong>For best results with this plugin:</strong></p>
		<p>For best results replace calls to <code>the_content();</code> in your theme files with this code:</p>
		<pre>if( class_exists('Vitally_Responsible') ){
	 do_action( 'vitally_responsible_content');
}else{
	the_content();
}</pre>
	<p>This extra code won't break if you choose to disable the plugin and will revert to using the default <code>the_content</code> function.<br>Whilst the plugin is active it allows us to cache the filtered content, improving performance by preventing complex search/replaces running on every page load.<br>It also helps prevents potential conflicts with your theme and other plugins.</p>
	</div>

	<!--<pre><span class="nt">&lt;span</span> <span class="na">data-picture</span> <span class="na">data-alt=</span><span class="s">"A giant stone face at The Bayon temple in Angkor Thom, Cambodia"</span><span class="nt">&gt;</span>
    <span class="nt">&lt;span</span> <span class="na">data-src=</span><span class="s">"small.jpg"</span><span class="nt">&gt;&lt;/span&gt;</span>
    <span class="nt">&lt;span</span> <span class="na">data-src=</span><span class="s">"medium.jpg"</span>     <span class="na">data-media=</span><span class="s">"(min-width: 400px)"</span><span class="nt">&gt;&lt;/span&gt;</span>
    <span class="nt">&lt;span</span> <span class="na">data-src=</span><span class="s">"large.jpg"</span>      <span class="na">data-media=</span><span class="s">"(min-width: 800px)"</span><span class="nt">&gt;&lt;/span&gt;</span>
    <span class="nt">&lt;span</span> <span class="na">data-src=</span><span class="s">"extralarge.jpg"</span> <span class="na">data-media=</span><span class="s">"(min-width: 1000px)"</span><span class="nt">&gt;&lt;/span&gt;</span>

    <span class="c">&lt;!-- Fallback content for non-JS browsers. Same img src as the initial, unqualified source element. --&gt;</span>
    <span class="nt">&lt;noscript&gt;</span>
        <span class="nt">&lt;img</span> <span class="na">src=</span><span class="s">"external/imgs/small.jpg"</span> <span class="na">alt=</span><span class="s">"A giant stone face at The Bayon temple in Angkor Thom, Cambodia"</span><span class="nt">&gt;</span>
    <span class="nt">&lt;/noscript&gt;</span>
<span class="nt">&lt;/span&gt;</span></pre>-->
	<?php
}




function vital_option_breakpoints(){

    $options = get_option( 'vitally_responsible_options' );
    $value = $options['vital_breaks'];

    ?>

    <hr>

    <h3>Set Breakpoint Sizes (min-widths)</h3>
    <p><strong>A list of all breakpoint sizes (minimum widths) for which you'd like a different image crop to load</strong></p>
    <ol>
    	<li>A 0 breakpoint will automatically be included so your first value should be the first breakpoint above 0</li> 
    	<li>For now all values will be treated as px widths so <strong>there's no need to specify a unit</strong></li>
    	<li>Breakpoint values should be separated by a comma</li>
    </ol>
    <p>An example of expected input could be <code>400,800,1000</code></p>

    <label for="vitally_responsible_options[vital_breaks]">List Responsive Breakpoint Widths</label>
    <input id='vital-breakpoints' name='vitally_responsible_options[vital_breaks]' value='<?php echo $value; ?>' />

    <?php

}

function vital_option_crops(){

    $options = get_option( 'vitally_responsible_options' );
    $value = $options['vital_crops'];

    ?>

    <hr>

   	<h3>Set Image Crop Sizes</h3>
    <p><strong>A list of all image sizes to be used at the different breakpoints above.</strong></p>
    <ol>
    	<li>The first value is used as the default image size, until the width of the site is greater than the first breakpoint</li>
    	<li>So there should be one more value in this list than the <strong>Breakpoint Sizes</strong> list above</li> 
    	<li>For now all values will be treated as px widths so <strong>there's no need to specify a unit</strong></li>
    	<li>Width values should be separated by a comma</li>
    </ol>
    <p>An example of expected input could be <code>380,700,1000,1300</code></p>

    <label for="vitally_responsible_options[vital_crops]">List Image Crop Widths</label>
    <input id='vital-crops' name='vitally_responsible_options[vital_crops]' value='<?php echo $value; ?>' />

    <?php

}

function vital_option_ignores(){

    $options = get_option( 'vitally_responsible_options' );
    $value = $options['vital_ignore'];

    ?>

    <hr>

    <h3>Add Classes to Ignore</h3>
    <p><strong>A list of classes (separated by commas) that will be left as <code>&lt;img&gt;</code> tags.</strong></p>
    <p>An example of expected input could be <code>thumbnail,non-responsive</code></p>

    <input id='vital-ignores' name='vitally_responsible_options[vital_ignore]' value='<?php echo $value; ?>' />
   
    <?php

}

function vital_option_enqueue(){

    $options = get_option( 'vitally_responsible_options' );
    $value = $options['vital_enqueue'];

    ?>

    <hr>

    <h3>Automatically Enqueue Picturefill Javascript</h3>
    <p><strong>Turn this setting on if you do not want to manually include the js for picturefill in your theme files.</strong><p>
	<p>This option is turned off by default so that you can add the very short js from <a href="https://github.com/scottjehl/picturefill" target="_blank">https://github.com/scottjehl/picturefill</a> with your other js and save an http:// request, or include a version customised for your theme.</p>
	<p>If turned on this setting will automatically enqueue the javascript needed for loading responsive images</p>
    <select id="vital-enqueue" name="vitally_responsible_options[vital_enqueue]">
      <option value="true" <?php if ( $value == 'true' ) echo 'selected'; ?>>On</option>
      <option value="false" <?php if ( $value == 'false') echo 'selected'; ?>>Off</option>
    </select>

    <?php

}

function vital_option_filter_content(){

    $options = get_option( 'vitally_responsible_options' );
    $value = $options['vital_filter_content'];

    ?>

    <hr>

    <h3>Automatically Filter <code>the_content</code></h3>
    <p><strong>Turn this setting on if you do not want to manually edit your theme with the code at the top of this page.</strong><p>
	<p>This option is turned off by default as performance may be slightly better (and there is less risk of conflict with other plugins) if you can use the <a href="#code-warning">code</a> mentioned at the top of this page in your theme files.</p>
	<p>If turned on this setting will automatically filter the_content so will work without requiring any changes to theme files</p>
    <select id="vital-filter-content" name="vitally_responsible_options[vital_filter_content]">
      <option value="true" <?php if ( $value == 'true' ) echo 'selected'; ?>>On</option>
      <option value="false" <?php if ( $value == 'false') echo 'selected'; ?>>Off</option>
    </select>

    <?php

}

/**
 * Actually adding all of this to the page!
 */
function vitally_responsible_options_page(){
    ?>

	<div class="wrap vitally-responsible">

        <form action="options.php" method="post">
			<?php settings_fields( 'vitally_responsible_options' ); ?>
			<?php do_settings_sections( 'vitally-responsible' ); ?>
			<?php submit_button(); ?>
		</form>

        <p style="color:#777;font-size:11px;margin-top:20px">Many thanks to Noel Tock and <a href="https://github.com/noeltock/hammy" target="_blank">Hammy</a> for the inspiration for this plugin. Cropped images are generated using<a href="https://github.com/humanmade/WPThumb"  target="_blank">WPThumb</a></p>
   
	</div>

<?php } 

?>