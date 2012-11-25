<?php
/*
Plugin Name: Shopp + Ology
Description: Generate information about your hosting environment to assist with troubleshooting WordPress and Shopp.
Version: 1.0.3
Plugin URI: http://optimizemyshopp.com
Author: Lorenzo Orlando Caum, Enzo12 LLC
Author URI: http://enzo12.com
License: GPLv2
*/
/* 
(CC BY 3.0) 2012  Lorenzo Orlando Caum  (email : hello@enzo12.com)

	This plugin is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This plugin is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this plugin.  If not, see <http://www.gnu.org/licenses/>. 
*/

Shopp_Ology::smartLoad();

class Shopp_Ology {
	public static $yourname;
	
	public static function smartLoad() {
		$instantiate = apply_filters('shoppOlogyLoadBasic', true);
		if ($instantiate) { new Shopp_Ology; }
	}

	public function __construct() {
		add_action('shopp_init', array($this, 'init'));
		
		$this->yourname = get_option("shopp_ology_yourname");
	}

	public function init() {
		wp_enqueue_style( 'shopp-ology-stylesheet', plugins_url( "css/shopp-ology.css", __FILE__ ), array(), '20121101' );
		
		add_action('admin_menu', array($this, 'admin_menu'));
	}

	public function admin_menu() {
		global $Shopp;
		$ShoppMenu = $Shopp->Flow->Admin->MainMenu;
		$page = add_submenu_page($ShoppMenu,__('Shopp + Ology', 'page title'), __('+ Ology','menu title'), defined('SHOPP_USERLEVEL') ? SHOPP_USERLEVEL : 'manage_options', 'shopp-ology', array($this, 'render_display_settings'));

		add_action( 'admin_print_styles-' . $page, 'admin_styles' );
	}
	
 	public function admin_styles() {
       	wp_enqueue_style( 'shopp-ology-stylesheet' );
  	}

	public function render_display_settings() {
		wp_nonce_field('shopp-ology');	
		if(!empty($_POST['submit'])){
			$this->yourname = stripslashes($_POST['yourname']);
			
			update_option("shopp_ology_yourname", $this->yourname);
		}
?>

<div class="wrap">
	<h2>Shopp + Ology</h2>
	<div class="postbox-container" style="width:65%;">
		<div class="metabox-holder">	

			<div id="shopp-ology-introduction" class="postbox">
				<h3 class="hndle"><span>Introduction</span></h3>
				<div class="inside">
					<p>This plugin adds an option for generating information about your <a href="http://optimizemyshopp.com/go/shopp/" title="Learn more about Shopp">Shopp</a> and its hosting environment. You can then share this information with your web developer, Shopp consultant, or responding Shopp team member on the Help Desk.</p>
					<strong>Acknowledgements</strong>
					<br />
					<p>Credit to <a href="http://optimizemyshopp.com/go/adamsewell/" title="Get in touch with Adam">Adam Sewell</a>, <a href="http://optimizemyshopp.com/go/chrisrunnells/" title="Get in touch with Chris">Chris Runnells</a>, and <a href="http://optimizemyshopp.com/go/jonathandavis/" title="Get in touch with Jonathan">Jonathan Davis</a> who answered my questions on how to retrieve certain data via PHP.</p>
				</div>
			</div>

			<div id="shopp-ology-information" class="postbox">
				<h3 class="hndle"><span>Information</span></h3>
				<div class="inside">
				<p><?php if ( '' === get_option( 'shopp_ology_yourname', '' ) ) {
    echo ""; 
	} else {	
	echo "Hello ". get_option("shopp_ology_name", $this->yourname). "!"; } ?></p> 
    			<p>Below is information on your hosting environment. The data will be helpful to the responding Shopp team member which will lead to a faster solution for your issue. WIN-WIN!</p>
				<p>If applicable, clear your "cache" once or twice. Then copy and paste the information below into your Shopp support ticket.</p>
				<p>Notes: Due to the infinite variations from different hosting environments, some information may be inaccurate. However, most of the information reported should be correct.</p> 
				<textarea style="width:100%; height:675px;">
**Server Information**

Type of Server: <?php echo $_SERVER['SERVER_SOFTWARE']. "\n"; ?>
IP Address: <?php echo $_SERVER['SERVER_ADDR']. "\n"; ?>
PHP Version: <?php echo phpversion(). "\n" ?>
MySQL Version: <?php echo mysql_get_server_info(). "\n" ?>
Maximum Upload Size: <?php echo ini_get('upload_max_filesize'). "\n" ?>
Maximum Post Size: <?php echo ini_get('post_max_size'). "\n" ?>
				
**WordPress Information**
				
Version: <?php echo $GLOBALS['wp_version']. "\n"; ?>
Maximum Upload Size: <?php echo wp_max_upload_size(). "\n" ?>
Site Memory: <?php echo ini_get('memory_limit'). "\n" ?>
Address: <?php echo get_bloginfo("url"). "\n"; ?>
Site Address: <?php echo get_bloginfo("wpurl"). "\n" ?>
Permalink Settings: <?php echo get_option('permalink_structure'). "\n" ?>
Theme: <? echo str_replace(get_bloginfo("wpurl"),"",get_bloginfo("template_directory")). "\n" ?>
Active Plugins: <?php $active_plugins = (array) get_option( 'active_plugins', array() );
						$active_plugins = array_map( 'strtolower', $active_plugins );
						$s_plugins = array();
						foreach ( $active_plugins as $plugin ) {
								$plugin_data = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
	    						if ( ! empty( $plugin_data['Name'] ) ) {							
	    							$s_plugins[] = $plugin_data['Name'] . ' ' . __('version', '') . ' ' . $plugin_data['Version'];
	    						}
						}
						if ( sizeof( $s_plugins ) == 0 ) echo '-'; else echo '' . implode( ', ', $s_plugins ) . ' ' . "\n"; ?>
WP-CACHE: <?php if(WP_CACHE == true) {
echo 'enabled'. "\n";
} else {
echo 'disabled'. "\n";
} ?>
Sessions Path: <?php echo ini_get('session.save_path'). "\n"; ?>
GD Library: <?php if(! function_exists('imagecreatefromstring')) {
echo 'not installed'. "\n";
} else {
echo 'installed'. "\n";
} ?>
cURL: <?php if(function_exists('_iscurlinstalled()')) {
echo 'not installed'. "\n";
} else {
echo 'installed'. "\n";
} ?>

**Shopp Information**
				
Version: <?php echo SHOPP_VERSION. "\n"; ?>
<?php
global $wpdb;
$tablename = $wpdb->prefix . "shopp_meta";
$updatekey = $wpdb->get_var( $wpdb->prepare( 
"SELECT value 
FROM $tablename 
WHERE `type` = 'setting' 
AND `name` = 'updatekey'" ) );
echo "Support Access Key: {$updatekey}". "\n"; ?>
<?php
global $wpdb;
$tablename = $wpdb->prefix . "shopp_meta";
$merchant_email = $wpdb->get_var( $wpdb->prepare( 
"SELECT value 
FROM $tablename 
WHERE `type` = 'setting' 
AND `name` = 'merchant_email'" ) );
echo "Merchant Email: {$merchant_email}". "\n"; ?>
<?php
$baseop = shopp_setting('base_operations');
$state = $baseop['zone'];
$country = $baseop['country'];
echo "Base of Operations: {$state}, {$country}". "\n"; ?>
<?php
global $wpdb;
$tablename = $wpdb->prefix . "shopp_meta";
$active_gateways = $wpdb->get_var( $wpdb->prepare( 
"SELECT value 
FROM $tablename 
WHERE `type` = 'setting' 
AND `name` = 'active_gateways'" ) );
echo "Payment Gateways: {$active_gateways}". "\n"; ?>
Pages: <?php
$storepages = shopp_setting('storefront_pages');
$catalog = $storepages['catalog'];
$account = $storepages['account'];
$cart = $storepages['cart'];
$checkout = $storepages['checkout'];
$confirmorder = $storepages['confirm'];
$thanks = $storepages['thanks'];
echo ($catalog['slug']). ', ' . ($account['slug']). ', ' . ($cart['slug']). ', ' . ($checkout['slug']). ', ' . ($confirmorder['slug']). ', ' . ($thanks['slug']). "\n"; ?>
<?php
global $wpdb;
$tablename = $wpdb->prefix . "shopp_meta";
$theme_templates = $wpdb->get_var( $wpdb->prepare( 
"SELECT value 
FROM $tablename 
WHERE `type` = 'setting' 
AND `name` = 'theme_templates'" ) );
echo "Theme Templates: {$theme_templates}". "\n"; ?>
<?php
global $wpdb;
$tablename = $wpdb->prefix . "shopp_meta";
$image_storage = $wpdb->get_var( $wpdb->prepare( 
"SELECT value 
FROM $tablename 
WHERE `type` = 'setting' 
AND `name` = 'image_storage'" ) );
echo "Image Storage: {$image_storage}". "\n"; ?>
<?php
global $wpdb;
$tablename = $wpdb->prefix . "shopp_meta";
$product_storage = $wpdb->get_var( $wpdb->prepare( 
"SELECT value 
FROM $tablename 
WHERE `type` = 'setting' 
AND `name` = 'product_storage'" ) );
echo "Product Storage: {$product_storage}". "\n"; ?>

**Your Information**
				
IP Address: <?php echo $_SERVER['REMOTE_ADDR']. "\n"; ?>
Computer: <?php echo $_SERVER['HTTP_USER_AGENT']. "\n"; ?>
				</textarea>
				</div>
			</div>
			
			<div id="shopp-ology-general-settings" class="postbox">
				<h3 class="hndle"><span>Complimentary Videos</span></h3>
				<div class="inside">
				<p>Pro-tip: After starting a video, click on the fullscreen button which appears to the right of the HD toggle.</p>
				<p><strong>Upgrading Shopp from versions 1.2.x</strong><br /><br /><iframe src="http://player.vimeo.com/video/44829218?title=0&amp;byline=0&amp;portrait=0" width="600" height="300" frameborder="0" webkitAllowFullScreen allowFullScreen></iframe></p>
				<br />
				<p><strong>Upgrading Shopp from version 1.1.x</strong><br /><br /><img src="<?php echo plugins_url( 'shopp-ology/images/video-tutorial-arriving-soon.gif' , dirname(__FILE__) ); ?>" width="600" height="300" /></p>
				</div>
			</div>
			
			<div id="shopp-ology-support-feedback" class="postbox">
				<h3 class="hndle"><span>Support & Feedback</span></h3>
				<div class="inside">
				<p>This is a 3rd-party integration.</p> 
				<p>This plugin is <strong>actively supported</strong>. Support is provided as a courtesy by Lorenzo Orlando Caum, Enzo12 LLC. If you have any questions or concerns, please open a <a href="http://optimizemyshopp.com/support/" title="Open a new support ticket with Optimize My Shopp">new support ticket</a> via our Help Desk.</p>
                <p>You can share feedback via this a <a href="http://optimizemyshopp.com/go/shopp-extensions-survey/" title="Take a super short survey">short survey</a>. Takes less 3 than minutes -- we promise!</p>
                <p>Feeling generous? Please consider <a href="http://optimizemyshopp.com/go/donate-shopp-ology/" title="Say thank you by purchasing Lorenzo a Redbull">buying me a Redbull</a>, <a href="http://optimizemyshopp.com/go/amazonwishlist/" title="Say thank you by gifting Lorenzo a book">ordering me a book</a> from my Amazon Wishlist, or <a href="http://optimizemyshopp.com/go/tip-shopp-help-desk/" title="Say thank you by tipping Lorenzo via the Shopp Help Desk">tipping me</a> through the Shopp Help Desk.</p>
				</div>
			</div>
			
			<div id="shopp-ology-settings" class="postbox">
				<h3 class="hndle"><span>Ology Settings</span></h3>
				<div class="inside">
                    <p>
					<form action="" method="post">
					<table>
                    <tr>
						<th>Name:</th>
						<td><input type="text" name="yourname" size="35" value="<?php echo $this->yourname; ?>" /></td>
					</tr>
					</table>
					<input type="submit" class="button-primary" value="Save Settings" name="submit" />
					</form>
                    </p>
				</div>
			</div>
			
			<div id="shopp-ology-about-the-author" class="postbox">
				<h3 class="hndle"><span>About the Author</span></h3>
				<div class="inside">
					<table border="0" width="100%">
   					 	<tr>
                            <td width="70%"><div><img style="padding: 0px 15px 0px 0px; float:left" src="<?php echo plugins_url( 'shopp-ology/images/lorenzo-orlando-caum-shopp-wordpress-150x150.jpg' , dirname(__FILE__) ); ?>" border="0" alt="Founder of Enzo12 LLC" width="150" height="150">
                            <p><a href="http://twitter.com/lorenzocaum" title="Follow @lorenzocaum">@lorenzocaum</a> is an entrepreneur and a marketer.</p>
                            <p>Lorenzo contributes to the <a href="http://optimizemyshopp.com/go/shopp/" title="Visit shopplugin.net">Shopp</a> project as a member of the support team. He has written several  <a href="http://optimizemyshopp.com/resources/#shopp-extensions" title="View free WordPress plugins for Shopp">WordPress extensions for Shopp</a>. His latest project is <a href="http://optimizemyshopp.com/go/shopp101/" title="Shopp 101 -- video tutorials for Shopp">video tutorials for Shopp</a>.</p>
                            <p>He is the founder of Enzo12 LLC, a <a href="http://enzo12.com" title="Enzo12 LLC">web engineering firm</a> in Tampa, FL. If you would like to know more about Lorenzo, you can <a href="http://twitter.com/lorenzocaum">follow him on Twitter</a> or <a href="http://lorenzocaum.com" title="Read Lorenzo's blog">check out his blog</a>.</p></div></td>
                            <td width="30%"></td>
   						</tr>
					</table>
				</div>
			</div>

		</div>
	</div>

	<div class="postbox-container" style="width:25%;">
		<div class="metabox-holder">
			
			<div id="shopp-ology-donate" class="postbox">
				<h3 class="hndle"><span><strong>Make a Donation!</strong></span></h3>
				<div class="inside">
                    <p>Hi friend!</p>
                    <p>If this plugin is helpful to you, then please <a href="http://optimizemyshopp.com/go/donate-shopp-ology/" title="Say thank you by purchasing Lorenzo a Redbull">buy me a Redbull</a>.</p>
                    <p>Why not <a href="http://optimizemyshopp.com/go/amazonwishlist/" title="Say thank you by gifting Lorenzo a book">order me a book</a> from my Amazon Wishlist.</p>
                    <p>You can also <a href="http://optimizemyshopp.com/go/tip-shopp-help-desk/" title="Say thank you by tipping Lorenzo via the Shopp Help Desk">tip me</a> through the Shopp Help Desk.</p>
					<p>Your kindness is appreciated and will go towards <em>continued development</em> of the Shopp + Ology plugin.</p>
				</div>
			</div>
			
			<div id="shopp-ology-subscribe" class="postbox">
				<h3 class="hndle"><span>Free Email Updates</span></h3>
				<div class="inside">
					<p>Get infrequent email updates delivered right to your inbox about getting the most from Shopp.</p>
					<div id="optin">
					<p>
					<form action="http://optimizemyshopp.us2.list-manage1.com/subscribe/post?u=5991854e8288cad7823e23d2e&amp;id=0719c3f096" method="post" target="_blank">
					<input type="text" name="EMAIL" class="email" value="Enter your email" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" />
					<input name="submit" class="button-primary" type="submit" value="Get Started!" />
					</form>
					</p>
					</div>
				</div>
			</div>
					
			<div id="shopp-ology-have-a-question" class="postbox">
				<h3 class="hndle"><span>Have a Question?</span></h3>
				<div class="inside">
                    <p>Open a <a href="http://optimizemyshopp.com/support/" title="Open a new support ticket with Optimize My Shopp">new support ticket</a> for Shopp + Ology</p>
                    <p>Learn about <a href="http://optimizemyshopp.com/resources/" title="Learn about extra Shopp resources">additional Shopp resources</a></p>
                    <p>Want awesome support from the Shopp Help Desk? <a title="How to Get Awesome Support on the Shopp Help Desk" href="http://optimizemyshopp.com/blog/how-to-get-awesome-support-from-the-shopp-help-desk/">Click here to read the post</a></p>
				</div>
			</div>

			<div id="shopp-ology-enjoy-this-plugin" class="postbox">
				<h3 class="hndle"><span>Enjoy this Plugin?</span></h3>
				<div class="inside">
					<p>
					<ol>
					<li><strong>Rate it </strong><a href="http://wordpress.org/extend/plugins/shopp-ology/">5 stars on WordPress.org</a></li>
					<li><strong>Spread social joy</strong> ;)<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://optimizemyshopp.com" data-text="Shopp + Ology for my #WordPress #ecommerce store" data-count="none" data-via="enzo12llc" data-related="lorenzocaum:entrepreneur">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script><br /><br /><div id="fb-root"></div>
					<script>(function(d, s, id) {
 					var js, fjs = d.getElementsByTagName(s)[0];
  					if (d.getElementById(id)) {return;}
  					js = d.createElement(s); js.id = id;
  					js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  					fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));</script>

					<div class="fb-like" data-href="http://optimizemyshopp.com" data-send="false" data-layout="button_count" data-width="5" data-show-faces="false" data-font="lucida grande">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><br /><br /> <!-- Place this tag where you want the +1 button to render -->
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="g-plusone" data-annotation="inline" data-width="120" data-href="http://optimizemyshopp.com"></div>

					<!-- Place this render call where appropriate -->
					<script type="text/javascript">
  					(function() {
   		 			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    				po.src = 'https://apis.google.com/js/plusone.js';
    				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  					})();
					</script></li>
                    <li><strong>Express your kindness</strong> with a <a href="http://optimizemyshopp.com/go/donate-shopp-ology/">donation</a></li>
					</ol>
					</p>		 
				</div>
			</div>

			<div id="shopp-ology-news-from-oms" class="postbox">
				<h3 class="hndle"><span>News from Optimize My Shopp</span></h3>
				<div class="inside">
				<p>Free eBook<br /> <a href="http://optimizemyshopp.com/the-list/" title="Receive your free eBook delivered instantly to your inbox">10 Steps to a More Secure WordPress</a></p>
				<p>White Papers<br /> <a href="http://optimizemyshopp.com/resources/white-papers/" title="Get your free white paper on creating a fast Shopp website">Speeding up your Shopp Ecommerce Website</a><br /><a href="http://optimizemyshopp.com/resources/white-papers/" title="Get your free white paper on using Shopp with caching plugins">Shopp + Caching Plugins</a></p>
				<?php _e('Recent posts from the blog:'); ?>
				<?php
				include_once(ABSPATH . WPINC . '/feed.php');
				$rss = fetch_feed('http://feeds.feedburner.com/optimizemyshopp');
				if (!is_wp_error( $rss ) ) : 
    			$maxitems = $rss->get_item_quantity(7); 
    			$rss_items = $rss->get_items(0, $maxitems); 
				endif;
				?>
				<ul>
    			<?php if ($maxitems == 0) echo '<li>No items.</li>';
    			else
    			foreach ( $rss_items as $item ) : ?>
    			<li>
        		<a href='<?php echo esc_url( $item->get_permalink() ); ?>'
        		title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
        		<?php echo esc_html( $item->get_title() ); ?></a>
    			</li>
    			<?php endforeach; ?>
				</ul>
				</div>
			</div>			
			
			<div id="shopp-ology-recommendations" class="postbox">
				<h3 class="hndle"><span>Recommended</span></h3>
				<div class="inside">
                    <p>Need a Shopp developer to help you with your online store? <br /><a href="http://optimizemyshopp.com/store/wordpress-consulting/" title="Hire a Shopp developer today">Get in touch today</a></p>
                    <p>What do you think about video tutorials for Shopp? <br /><a href="http://optimizemyshopp.com/go/shopp101/" title="Learn more about Shopp video tutorials">Request an invite</a></p>
				</div>
			</div>

		</div>
		<br /><br /><br />
	</div>
</div>
<?php	
	}
}