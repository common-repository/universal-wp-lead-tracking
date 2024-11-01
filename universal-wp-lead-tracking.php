<?php
/*
	Plugin Name: Universal WP Lead Tracking
	Description: Adds tracking info to outgoing emails from contact forms by using the [tracking-info] shortcode. The tracking info includes the Form Page URL, Original Referrer, Landing Page, User IP, Browser. Compatible with Contact Form 7, Gravity Forms, Ninja Forms, and Elementor PRO forms! 
	Version: 1.0.5
	Author: Inbound Horizons
	Author URI: https://www.inboundhorizons.com/
	Requires at least: 3.3
	Requires PHP: 5.4
*/

	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly. No script kiddy attacks!
	}

	$UWPLT_COOKIE = 'uwplt_cookie';
	$UWPLT_SHORTCODE = 'tracking-info';


	
	class UWPLT {	// Universal WP Lead Tracking
		
		private static $_instance = null;
		

		public static function Instantiate() {
			if (is_null(self::$_instance)) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
		public function __construct() {
			
			// Backend menu
				add_action('admin_menu', function() {
					$this->BackendMenu();
				});	// Add a side-bar menu item
			
			
			// AJAX save
				add_action('wp_ajax_UWPLT_SAVE', function() {
					$this->SaveAJAX();
				});
		}
		
		private function BackendMenu() {
			
			add_options_page('Universal WP Lead Tracking', 'Universal WP Lead Tracking', 'manage_options', 'uwplt', function() {
				$this->BackendHTML();
			});
			
			
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), function($links) {
				return $this->SettingsLink($links);
			});
		}
		
		private function SettingsLink($links) {
			$url = get_admin_url() . "options-general.php?page=uwplt";
			$settings_link = '<a href="' . $url . '">' . __('Settings', 'Universal WP Lead Tracking') . '</a>';
			array_unshift($links, $settings_link);
			return $links;
		}
		
		private function BackendHTML() {
			global $UWPLT_COOKIE;
		
		
		
			
			$uwplt_ga = get_option('uwplt_ga', false);
			$uwplt_gtag = get_option('uwplt_gtag', false);
			
			$uwplt_ga_checked = ($uwplt_ga) ? 'checked' : '';
			$uwplt_gtag_checked = ($uwplt_gtag) ? 'checked' : '';
			
			
			// ga("send", "event", "Contact Form", "submit");
			// gtag("event", "submit", {"event_category": "Contact Form"});
			
			
			$img_src = plugin_dir_url(__FILE__).'assets/UWPLT-Plugin-Internal-Header.jpg';
			$plugin_data = get_plugin_data(__FILE__);
			
			$plugin_version = $plugin_data['Version'];
			
			
			
			
			
			
			// For the demo HTML
			
				$original_referrer = '';
				$landing_page = '';
				$contact_form_page_url = '';
				$user_IP = '';
				$user_country = '';
				$proxy_server_IP = '';
				$proxy_country = '';
				$browser = '';
			
				if (isset($_COOKIE[$UWPLT_COOKIE])) {	// If the session cookie is set...
				
					$session_key = sanitize_text_field($_COOKIE[$UWPLT_COOKIE]);	// Sanitize the cookie before using it
					$session_record = UWPLT_GetSessionRecord($session_key);
					
					if (is_array($session_record) && (isset($session_record['session_value']))) {	// If the record was actually set...
						
						$session = unserialize($session_record['session_value']);
						
						if (isset($session['OriginalRef']) && ($session['OriginalRef'] !== '')) {
							$original_referrer = sanitize_text_field($session['OriginalRef']);
						}
						
						if (isset($session['LandingPage']) && ($session['LandingPage'] !== '')) {
							$landing_page = sanitize_text_field($session['LandingPage']);
						}
					}
					
				}
		
				if (isset($_SERVER["HTTP_REFERER"]) && ($_SERVER['HTTP_REFERER'] !== '')) {
					$contact_form_page_url = sanitize_text_field($_SERVER["HTTP_REFERER"]);
				}
				
				if (isset($_SERVER["REMOTE_ADDR"]) && ($_SERVER['REMOTE_ADDR'] !== '')) {
					$user_IP = sanitize_text_field($_SERVER["REMOTE_ADDR"]);
				}
		
				if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && ($_SERVER['HTTP_X_FORWARDED_FOR'] !== '')) {
					$proxy_server_IP = sanitize_text_field($_SERVER["HTTP_X_FORWARDED_FOR"]);
				}

				if (isset($_SERVER["HTTP_USER_AGENT"]) && ($_SERVER['HTTP_USER_AGENT'] !== '')) {
					$browser = sanitize_text_field($_SERVER["HTTP_USER_AGENT"]);
				}
		
				$user_ip_data = $this->GetIPData($user_IP);
				$user_country = $user_ip_data['country_name'];
				
				if ($user_IP === $proxy_server_IP) {
					$proxy_country = $user_country;
				}
				else {
					$proxy_ip_data = $this->GetIPData($proxy_server_IP);
					$proxy_country = $proxy_ip_data['country_name'];
				}
			
		
			//<li>
			//	<b>User Country/City:</b> Includes general country and city location for the user.
			//</li>

			echo '
				<div style="text-align:center;">
					<div class="panel" style="margin:0px;">
						<img alt="Universal WP Lead Tracking" src="'.esc_url($img_src).'" />
					</div>
					<div class="wrap panel panel-margin">
						<div class="panel-body">
							<span style="float:left;">
								<b>Version:</b> '.esc_html($plugin_version).'
							</span>
							
							<span>
								<b>Developed By: </b>
								<a href="https://www.inboundhorizons.com/" target="_blank">
									Inbound Horizons
								</a>
							</span>
							
							<span style="float:right;">
								<a href="https://www.inboundhorizons.com/wordpress-plugin-feedback/" target="_blank">
									Get Support
								</a>
							</span>
							
						</div>
					</div>
				</div>
				
				
				<div class="wrap">			
					<h1>
						Universal WP Lead Tracking
					</h1>
					
					<div id="container">
					
						
						<div>
							Compatible with 
							<a href="https://contactform7.com/" target="_blank">Contact Form 7</a>, 
							<a href="https://www.gravityforms.com/" target="_blank">Gravity Forms</a>, 
							<a href="https://ninjaforms.com/" target="_blank">Ninja Forms</a>, 
							and <a href="https://elementor.com/" target="_blank">Elementor PRO forms</a>!
							
							<i>
								Don\'t see your form listed? Contact <a href="https://www.inboundhorizons.com/" target="_blank">Inbound Horizons</a>
								to add compatibility with your form.
							</i>
						</div>
						
						
						
						<table class="form-table" role="presentation">
							<tbody>
								<tr>
									<th scope="row">
										How To Use This Plugin
									</th>
									<td>
										<p class="description">		
											Insert the <b>[tracking-info]</b> shortcode into your contact form\'s notification 
											email to include the following information every time a user submits a form:
										</p>

										<ul> 
											<li>
												<b>Original Referrer:</b> This is where the user came to your website from such as Google, Facebook, or another website.
											</li>
											<li>
												<b>Landing Page:</b> This is the page on your website that the user first came to from an outside source.
											</li>
											<li>
												<b>Contact Form Page URL:</b> This is the page on your website where the user filled out your form.
											</li>
											<li>
												<b>User IP:</b> This is the IP address of the user that sent a form submission.
											</li>
											<li>
												<b>User Country:</b> Includes country for the user.
											</li>
											<li>
												<b>Browser:</b> This tells you what browser a user was on when they sent a form submission.
											</li>
										</ul>

										<p class="description">
											To connect your form submissions with Google Analytics (Universal or GA4) 
											simply check the ‘events’ box for whichever GA service you are using. Every 
											time a user submits a form, the event will send from your website to your 
											Google Analytics account where you can then set it up as a trackable 
											conversion action.
										</p>
									
									</td>
								</tr>
								<tr>
									<th scope="row">
										Tracking Info Shortcode
									</th>
									<td>		
										<span class="shortcode"><input type="text" onfocus="this.select();" readonly="readonly" value="[tracking-info]" class="regular-text code"></span>
										<p class="description">Shortcode for plaintext emails.</p>
									</td>
								</tr>
								<tr>
									<th scope="row">
									</th>
									<td>	
										<span class="shortcode"><input type="text" onfocus="this.select();" readonly="readonly" value="[tracking-info html=&quot;true&quot;]" class="regular-text code"></span>
										<p class="description">Shortcode for HTML formatted emails.</p>
										
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										Google Analytics Form Submission Event
									</th>
									<td>
										<label>
											<input id="uwplt_gtag" type="checkbox" value="1" '.esc_attr($uwplt_gtag_checked).' />
											Trigger a GTAG form submission event when your form is submitted. (GA4)
											<p class="description">gtag("event", "submit", {"event_category": "Contact Form"});</p>							
										</label>										
									</td>
								</tr>
								
								<tr>
									<th scope="row">
									</th>
									<td>
										<label>
											<input id="uwplt_ga" type="checkbox" value="1" '.esc_attr($uwplt_ga_checked).' />
											Trigger a GA form submission event when your form is submitted. (Universal)
											<p class="description">ga("send", "event", "Contact Form", "submit");</p>
										</label>										
									</td>
								</tr>
								<tr>
									<th scope="row">
										
									</th>
									<td>	
										<button type="button" class="button button-primary" id="save_uwplt_settings_btn">
											Save Changes
										</button>
										
										<span id="save_uwplt_settings_btn_spinner" class="spinner" style="float:none;"></span>
									</td>
								</tr>
								<tr>
									<th scope="row"></th>
									<td>
										<p id="uwplt_notices"></p>
									</td>
								</tr>
							</tbody>
						</table>
						
						<table class="form-table" role="presentation">
							<tbody>
								<tr>
									<th colspan="2" scope="row">
										<h1>Example Tracking Info</h1>
									</th>
								</tr>
								<tr>
									<th scope="row">
										Original Referrer:
									</th>
									<td>
										'.esc_html($original_referrer).'
									</td>
								</tr>
								<tr>
									<th scope="row">
										Landing Page:
									</th>
									<td>
										'.esc_html($landing_page).'
									</td>
								</tr>
								<tr>
									<th scope="row">
										Contact Form Page URL:
									</th>
									<td>
										'.esc_html($contact_form_page_url).'
									</td>
								</tr>
								<tr>
									<th scope="row">
										User IP:
									</th>
									<td>
										'.esc_html($user_IP).'
									</td>
								</tr>
								<tr>
									<th scope="row">
										User Country:
									</th>
									<td>
										'.esc_html($user_country).'
									</td>
								</tr>
								<tr>
									<th scope="row">
										Proxy IP:
									</th>
									<td>
										'.esc_html($proxy_server_IP).'
									</td>
								</tr>
								<tr>
									<th scope="row">
										Proxy Country:
									</th>
									<td>
										'.esc_html($proxy_country).'
									</td>
								</tr>
								<tr>
									<th scope="row">
										Browser:
									</th>
									<td>
										'.esc_html($browser).'
									</td>
								</tr>
								<tr>
									<th scope="row">
										<i>Credits:</i>
									</th>
									<td>
										<i>This site or product includes IP2Location LITE data available from http://www.ip2location.com.</i>
									</td>
								</tr>
								
							</tbody>
						</table>
						
						
						<div>
							
							<hr>
							Do you have questions about or feedback on making our plugins even better? 
							We want to hear from you. 
							Please contact us on our site here: 
							<a href="https://www.inboundhorizons.com/wordpress-plugin-feedback/" target="_blank">
								Plugin Feedback Form
							</a>
							
						</div>
						
						
					</div>
					
					<script>
					
						var uwplt_success = "";
						uwplt_success += "<div class=\'notice notice-success is-dismissible\'>";
						uwplt_success += "<button onclick=\'jQuery(this).parent().remove();\' type=\'button\' class=\'notice-dismiss\'>";
						uwplt_success += "<span class=\'screen-reader-text\'>Dismiss this notice.</span>";
						uwplt_success += "</button>";
						uwplt_success += "<p><b>Success!</b> Saved changes.</p>";
						uwplt_success += "</div>";
					
						var uwplt_error = "";
						uwplt_error += "<div class=\'notice notice-error is-dismissible\'>";
						uwplt_error += "<button onclick=\'jQuery(this).parent().remove();\' type=\'button\' class=\'notice-dismiss\'>";
						uwplt_error += "<span class=\'screen-reader-text\'>Dismiss this notice.</span>";
						uwplt_error += "</button>";
						uwplt_error += "<p><b>Error.</b> Unable to save changes.</p>";
						uwplt_error += "</div>";
						
						jQuery(document).ready(function() {
							jQuery(document).on("click", "#save_uwplt_settings_btn", UWPLT_SaveJS);
						});
					
						function UWPLT_SaveJS() {
							
							jQuery("#uwplt_notices").html("");
							
							jQuery("#save_uwplt_settings_btn").addClass("hidden");
							jQuery("#save_uwplt_settings_btn_spinner").addClass("is-active");
							
							jQuery.post(
								"'.admin_url('admin-ajax.php').'", 
								{
									"action": "UWPLT_SAVE",
									"uwplt_ga": (jQuery("#uwplt_ga").is(":checked") ? 1 : 0),
									"uwplt_gtag": (jQuery("#uwplt_gtag").is(":checked") ? 1 : 0),
								}, 
								function(response) {
									if (response && response.ok) {
										jQuery("#uwplt_notices").html(uwplt_success);
									}
									else {
										jQuery("#uwplt_notices").html(uwplt_error);
									}
							
									jQuery("#save_uwplt_settings_btn").removeClass("hidden");
									jQuery("#save_uwplt_settings_btn_spinner").removeClass("is-active");
								}
							);
						}
					</script>
					
					<style>
					
						#wpcontent {
							padding-left: 0;
						}
					
						#wpcontent img {
							max-width:100%; 
							vertical-align: middle; 
							box-shadow: 0px 0px 10px #003165;
						}
						
						.wrap {
							margin: auto;
							max-width: 60rem;
						}
						
						.panel {
							background-color: #fff;
							box-sizing: border-box;
							white-space: normal;
						}
						
						.panel-margin {
							margin-bottom: 20px;
						}
						
						.panel-body {
							padding: 10px;
						}
					</style>
					
				</div>
			';
		}
		
		private function SaveAJAX() {
			
			$uwplt_ga = isset($_POST['uwplt_ga']) ? boolval($_POST['uwplt_ga']) : false;
			$uwplt_gtag = isset($_POST['uwplt_gtag']) ? boolval($_POST['uwplt_gtag']) : false;
			
			update_option('uwplt_ga', $uwplt_ga);
			update_option('uwplt_gtag', $uwplt_gtag);
			
			header('Content-Type: application/json');
			echo json_encode(array(
				'ok' => true,
			));
			wp_die(); // This is required to terminate immediately and return a proper response
		}


		public static function IPv4AddressToNumber($ipv4_address) {
			if ($ipv4_address == "") {
				return 0;
			} 
			else {
				$ips = explode(".", $ipv4_address);
				$ip_number = ($ips[0] * 16777216) + ($ips[1] * 65536) + ($ips[2] * 256) + $ips[3];
				return ($ip_number);
			}
		}
		
		public static function IPv6AddressToNumber($ipv6_address) {
			if (function_exists('gmp_import')) {
				return (string) gmp_import(inet_pton($ipv6_address));
			}
			else {
				return (0);
			}
		}
		
		public static function NumberToIPv6Address($integer) {
			return inet_ntop(str_pad(gmp_export($integer), 16, "\0", STR_PAD_LEFT));
		}
		
		public static function GetVersionOfIP($ip_address) {
			$version = false;
			
			if (filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {    
				$version = 'IPv4';
			}
			else if (filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {    
				$version = 'IPv6';
			}
			
			return ($version);
		}
		
		public static function GetIPData($ip_address) {
			$ip_data = array(
				'country_code' => '',
				'country_name' => '',
			);
		
			$ip_version = UWPLT::GetVersionOfIP($ip_address);
			if ($ip_version === 'IPv4') {
				$raw_data = UWPLT::GetIPv4Data($ip_address);
				
				$ip_data = array(
					'country_code' => $raw_data[2],
					'country_name' => $raw_data[3],
				);
			}
			else if ($ip_version === 'IPv6') {
				$raw_data = UWPLT::GetIPv6Data($ip_address);
				
				$ip_data = array(
					'country_code' => $raw_data[2],
					'country_name' => $raw_data[3],
				);
			}
			
			return ($ip_data);
		}
		
		public static function GetIPv4Data($ip_address) {
			$return_data = array();
			
			$ip_number = UWPLT::IPv4AddressToNumber($ip_address);
			$csv = plugin_dir_path(__FILE__).'ip-lookup/IP2LOCATION-LITE-DB1.CSV';
			$return_data = UWPLT::GetRawIPData($ip_number, $csv);
			
			return ($return_data);
		}
		
		public static function GetIPv6Data($ip_address) {
			
			$ip_number = UWPLT::IPv6AddressToNumber($ip_address);
			$csv = plugin_dir_path(__FILE__).'ip-lookup/IP2LOCATION-LITE-DB1.IPV6.CSV';
			$return_data = UWPLT::GetRawIPData($ip_number, $csv);
			
			return ($return_data);
		}
		
		public static function GetRawIPData($ip_number, $csv) {
			$return_data = array();
			
			// IP Number Start
			// IP Number End
			// Nation Code
			// Nation
			
			$ip_data = array();
			$file = fopen($csv, 'r');
			while (($line = fgetcsv($file)) !== FALSE) {
				$return_data = $line;
				
				if (($ip_number >= $line[0]) && ($ip_number <= $line[1])) {
					break;
				}
			}
			fclose($file);
			
			return ($return_data);
		}
		
	}


	UWPLT::Instantiate();	// Instantiate an instance of the class
	




// -----------------------------------------------------------------------------
//	Google Analytics
// -----------------------------------------------------------------------------


	// Ninja Forms
		// /wp-content/plugins/ninja-forms/includes/Actions/Email.php
		
		add_filter('ninja_forms_action_email_message', 'UWPLT_AddTrackingToNinjaFormsEmail', 10, 3);
		function UWPLT_AddTrackingToNinjaFormsEmail($message, $data, $action_settings) {
			$message = do_shortcode($message);
			return ($message);
		}
		

	// Gravity Forms
		// https://docs.gravityforms.com/gform_pre_send_email/
		
		add_filter('gform_pre_send_email', 'UWPLT_AddTrackingToGravityFormsEmail', 10, 4);
		function UWPLT_AddTrackingToGravityFormsEmail($email, $message_format, $notification, $entry) {
			$email['message'] = do_shortcode($email['message']);
			return ($email);
		}


	// Elementor Forms (Elementor PRO)
		// /wp-content/plugins/elementor-pro/modules/forms/actions/email.php
		// Uses the shortcode


	// Contact Form 7
		// https://orbisius.com/blog/hook-contact-form-7-wordpress-plugin-sending-email-p2200
		
		add_filter('wpcf7_mail_components', 'UWPLT_AddTrackingToCF7Email');
		function UWPLT_AddTrackingToCF7Email($email) {
			$email['body'] = do_shortcode($email['body']);
			return ($email);
		}





	// Google Analytics tracking
		add_action('wp_head', 'UWPLT_Script');
		function UWPLT_Script() {
			
			$js = '';
			
			$js_code = '';
			
			
			$uwplt_ga = get_option('uwplt_ga', false);		// ga("send", "event", "Contact Form", "submit");
			$uwplt_gtag = get_option('uwplt_gtag', false);	// gtag("event", "submit", {"event_category": "Contact Form"});
			
			if ($uwplt_ga) {
				$js_code .= '
					if (typeof ga === "function") {	// Prevent errors if ga() not loaded...
						ga("send", "event", "Contact Form", "submit");
					}
				';
			}
			
			if ($uwplt_gtag) {
				$js_code .= '
					if (typeof gtag === "function") {	// Prevent errors if gtag() not loaded...
						gtag("event", "submit", {"event_category": "Contact Form"});
					}
				';
			}
			
			
			
			
			
			// Global function
				$js .= '
					function uwplt_form_submit() {
						'.$js_code.'
					}
				';
				
			
			// Contact Form 7 
			// 	(https://contactform7.com/tracking-form-submissions-with-google-analytics/)
				$js .= '
					document.addEventListener("wpcf7mailsent", uwplt_form_submit, false);
				';
				
			
			// Elementor PRO form
			//	https://elementor.com/help/form-widget-faq/
			//	https://trackingchef.com/google-tag-manager/bulletproof-elementor-form-tracking-with-gtm/
			// 	NOTE: This MUST use a jQuery listener and NOT a vanilla JS listener because the event is triggered with jQuery. 
			//	https://stackoverflow.com/questions/25256173/can-i-use-jquery-trigger-with-event-listeners-added-with-addeventlistener
				$js .= '
					jQuery(document).on("submit_success", uwplt_form_submit);
				';
				
			
			// Gravity Forms (this assumes the form is AJAX submitted and not redirected)
			// 	https://docs.gravityforms.com/gform_confirmation_loaded/
			//	https://stackoverflow.com/questions/31565566/gravity-forms-fire-js-event-on-successfull-form-submission
			// 	NOTE: This MUST use a jQuery listener and NOT a vanilla JS listener because the event is triggered with jQuery. 
			//	https://stackoverflow.com/questions/25256173/can-i-use-jquery-trigger-with-event-listeners-added-with-addeventlistener
				$js .= '
					jQuery(document).on("gform_confirmation_loaded", uwplt_form_submit);
				';
				
				
			// Ninja Forms
			//	https://ninjaforms.com/blog/event-tracking-for-ninja-forms-no-plugin/
			// 	https://www.chrisains.com/seo/tracking-ninja-form-submissions-with-google-analytics-jquery/
			// 	NOTE: This MUST use a jQuery listener and NOT a vanilla JS listener because the event is triggered with jQuery. 
			//	https://stackoverflow.com/questions/25256173/can-i-use-jquery-trigger-with-event-listeners-added-with-addeventlistener
				$js .= '
					jQuery(document).on("nfFormReady", function() {
						nfRadio.channel("forms").on("submit:response", function(form) {
							uwplt_form_submit();
						});
					});
				';
		
			
			if ($js_code != '') {
				$script = $js;
				
				wp_register_script('universal-wp-lead-tracking-script', '', [], '', true);
				wp_enqueue_script('universal-wp-lead-tracking-script');
				wp_add_inline_script('universal-wp-lead-tracking-script', $script);
			}
		}
		
		
	// Gravity Forms (on hard reload)
		add_filter('gform_confirmation', 'UWPLT_GravityFormsScript', 10, 4);
		function UWPLT_GravityFormsScript($confirmation, $form, $entry, $ajax) {
			$confirmation .= '<script>if(typeof uwplt_form_submit === "function"){uwplt_form_submit();}</script>';
		 
			return ($confirmation);
		}





// Global hooks

add_action('init', 'UWPLT_SetLandingInfo');
function UWPLT_SetLandingInfo() {
	global $UWPLT_COOKIE;
	global $UWPLT_SHORTCODE;

	if (!isset($_COOKIE[$UWPLT_COOKIE])) {
		
		// Check if the user landed on an HTTP or HTTPS page URL
			$protocol = 'http://';
			if (
					isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) 
				|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
			) {
				$protocol = 'https://';
			}
		
		// Get the values to save
			$original_ref = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : ''; 
			$landing_page = $protocol . sanitize_text_field($_SERVER["SERVER_NAME"]) . sanitize_text_field($_SERVER["REQUEST_URI"]); 
		
		
		// Package the values into an array
			$session = array();
			$session['OriginalRef'] = $original_ref;
			$session['LandingPage'] = $landing_page;
		
		
		// Save the values
			$session_key = UWPLT_InsertSession($session);
		
		
		// Set the session cookie with the DB session key
			if (!headers_sent()) {	// Only if no headers sent to avoid an error
				if ($UWPLT_COOKIE != '') {
					$_COOKIE[$UWPLT_COOKIE] = $session_key;
					setcookie($UWPLT_COOKIE, $session_key, time() + (21600), "/"); // 21600 = 6 hours
				}
			}
		
	}
	
	
	// Elementor form
	
	add_shortcode($UWPLT_SHORTCODE, 'UWPLT_Shortcode');
}

register_activation_hook(__FILE__, 'UWPLT_CreateSessionTable');
function UWPLT_CreateSessionTable() {
	// https://codex.wordpress.org/Creating_Tables_with_Plugins
	global $wpdb;

	$table = 'track_sessions';
	$table_name = $wpdb->prefix . $table;
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "
		CREATE TABLE ".$table_name." (
			session_key char(32) NOT NULL,
			session_value LONGTEXT NOT NULL,
			session_expiry BIGINT(20) UNSIGNED NOT NULL,
			PRIMARY KEY (session_key)
		) $charset_collate;
	";
	

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}




function UWPLT_Shortcode($atts) {
	global $UWPLT_COOKIE;
	
	$user_IP = '';
	$user_country = '';
	$proxy_server_IP = '';
	$proxy_country = '';

	$args = shortcode_atts(array(
		'html' => false,
	), $atts);

	$lineBreak = PHP_EOL;	// Default PHP line break for plaintext emails. 
	
	if ($args['html']) {
		$lineBreak = '<br>';	// HTML line break
	}
	
	$trackingInfo = $lineBreak . $lineBreak . "-- Tracking Info --" . $lineBreak;
	$trackingInfo .= "The user filled the form on: " . sanitize_text_field($_SERVER['HTTP_REFERER']) . $lineBreak;


	
	if (isset($_COOKIE[$UWPLT_COOKIE])) {	// If the session cookie is set...
	
	
		$session_key = sanitize_text_field($_COOKIE[$UWPLT_COOKIE]);	// Sanitize the cookie before using it
		$session_record = UWPLT_GetSessionRecord($session_key);
		
		if (is_array($session_record) && (isset($session_record['session_value']))) {	// If the record was actually set...
			
			$session = unserialize($session_record['session_value']);
			

		
			if (isset($session['OriginalRef']) && ($session['OriginalRef'] !== '')) {
				$trackingInfo .= "The user came to your website from: " . sanitize_text_field($session['OriginalRef']) . $lineBreak;
			}
			
			if (isset($session['LandingPage']) && ($session['LandingPage'] !== '')) {
				$trackingInfo .= "The user's landing page on your website: " . sanitize_text_field($session['LandingPage']) . $lineBreak;
			}
		}
		
	
	}
	
	

	if (isset($_SERVER["REMOTE_ADDR"]) && ($_SERVER['REMOTE_ADDR'] !== '')) {
		$user_IP = sanitize_text_field($_SERVER["REMOTE_ADDR"]);
		
		$user_ip_data = UWPLT::GetIPData($user_IP);
		$user_country = $user_ip_data['country_name'];
		
		$trackingInfo .= "User's IP: " . sanitize_text_field($user_IP) . $lineBreak;
		$trackingInfo .= "User's Country: " . sanitize_text_field($user_country) . $lineBreak;
	}
	
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && ($_SERVER['HTTP_X_FORWARDED_FOR'] !== '')) {
		$proxy_server_IP = sanitize_text_field($_SERVER["HTTP_X_FORWARDED_FOR"]);
		
		if ($proxy_server_IP === $user_IP) {
			$proxy_country = $user_country;
		}
		else {
			$proxy_ip_data = UWPLT::GetIPData($proxy_server_IP);
			$proxy_country = $proxy_ip_data['country_name'];
		}
			
		$trackingInfo .= "User's Proxy Server IP: " . sanitize_text_field($proxy_server_IP) . $lineBreak;
		$trackingInfo .= "User's Proxy Server Country: " . sanitize_text_field($proxy_country) . $lineBreak;
	}

	if (isset($_SERVER["HTTP_USER_AGENT"]) && ($_SERVER['HTTP_USER_AGENT'] !== '')) {
		$trackingInfo .= "User's browser is: " . sanitize_text_field($_SERVER["HTTP_USER_AGENT"]) . $lineBreak;
	}
	
	return ($trackingInfo);
}


function UWPLT_InsertSession($value = array(), $expires = 86400) {	// (86400 = 1 day)
	global $wpdb;
	
	
	UWPLT_CleanupSessionTable();	// Clean up the table by removing old records
	
	// Get a unique session key that is not in the DB
	$key = '';
	$duplicate_key = true;
	while ($duplicate_key) {
		$key = UWPLT_GenerateSessionKey();
		$record = UWPLT_GetSessionRecord($key);
		
		if (!is_array($record) || empty($record)) {
			$duplicate_key = false;
		}
	}
	
	
	
	
	$table = 'track_sessions';
	$table_name = $wpdb->prefix . $table;
	
	$wpdb->insert($table_name, 
		array(
			'session_key' => $key,
			'session_value' => serialize($value),		// Serialize the data
			'session_expiry' => (time() + $expires),	// Set the expiration time as right NOW + expiration seconds
		),
		array(
			'%s',
			'%s',
			'%d',
		)
	);
	
	return ($key);
}

function UWPLT_GetSessionRecord($key) {
	global $wpdb;
	
	$table = 'track_sessions';
	$table_name = $wpdb->prefix . $table;
	
	$record = $wpdb->get_row(
		$wpdb->prepare("SELECT * FROM ".$table_name." WHERE session_key = %s", $key),
		ARRAY_A
	);
	
	return ($record);
}

function UWPLT_GenerateSessionKey($count = 32) {
	$chars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));	// Get an array of all letters and numbers
	
	$key = '';
	for ($i = 0; $i < $count; $i++) {
		$key .= $chars[array_rand($chars)];
	}
	
	return ($key);
}

function UWPLT_CleanupSessionTable() {
	global $wpdb;
	
	$table = 'track_sessions';
	$table_name = $wpdb->prefix . $table;
	
	// Delete all records that have expired
	$current_time = time();
	$sql = $wpdb->prepare("DELETE FROM ".$table_name." WHERE session_expiry < %d", $current_time);
	$wpdb->query($sql);
	
}



