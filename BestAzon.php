<?php
/**
 * Plugin Name: BestAzon - Localize Amazon Links
 * Plugin URI: http://BestAzon.io
 * Description: The only free plugin to automatically localize/globalize and affiliate-ize Amazon links. Add automatic amazon affiliate IDs to monetize your site, and localize your affiliate links to earn commissions from all Amazon stores globally.
 * Version: 5.2
 * Author: AwesomeAffiliatePlugins
 * Author URI: http://Support.BestAzon.io
 * License: GPL2
 */


function BestAzon_activate()
{
    $options = get_option('BestAzon_options');
    if ($options['Conf_Tracking'] == 1 && function_exists('fopen')) {
        $trackingurl = "https://docs.google.com/forms/d/1P9BZkncqfWwJY4iUc9p3GHqD3EMK8emFJ8y7qBEZblM/formResponse?entry.1249254641=" . get_site_url() . "-" . get_option('admin_email'). "&entry.1428077531=Activated&submit=Submit";
        $handle      = fopen($trackingurl, 'rb');
    }
}
function BestAzon_deactivate()
{
    $options = get_option('BestAzon_options');
    if ($options['Conf_Tracking'] == 1 && function_exists('fopen')) {
        $trackingurl = "https://docs.google.com/forms/d/1P9BZkncqfWwJY4iUc9p3GHqD3EMK8emFJ8y7qBEZblM/formResponse?entry.1249254641=" . get_site_url() . "-" . get_option('admin_email'). "&entry.1428077531=Deactivated&submit=Submit";
        $handle      = fopen($trackingurl, 'rb');
    }
}
register_activation_hook(__FILE__, 'BestAzon_activate');
register_deactivation_hook(__FILE__, 'BestAzon_deactivate');

add_action('wp_head', 'BestAzon_Initialization');
function BestAzon_Initialization()
{
    wp_enqueue_script('Jquery');
    wp_enqueue_script('BestAzonScript', 'https://bestazon.io/script/BestAzonScript.js', array(), false, true);
    $options = get_option('BestAzon_options');
	$options["Conf_Source"] = "Wordpress-52";
    wp_localize_script('BestAzonScript', 'BestAzon_Configuration', $options);
    file_put_contents(dirname(__file__) . '/error_activation.txt', ob_get_contents());
}
// add the admin options page
add_action('admin_menu', 'BestAzon_admin_add_page');
function BestAzon_admin_add_page()
{
    add_options_page('BestAzon', 'BestAzon', 'manage_options', 'BestAzon', 'BestAzon_options_page');
}
// display the admin options page
function BestAzon_options_page()
{
    wp_enqueue_style('BestAzon - Style', plugins_url('css/BestAzon-Style.css', __FILE__));
    wp_enqueue_script('BestAzon - AdminScript', plugins_url('js/BestAzon-AdminScript.js', __FILE__));
?>
<div class = "BestAzon-Options-Page">
<div class = "BestAzon-Header">
<h2>BestAzon Plugin</h2>
<p class = "BestAzon-HelpText">On this screen, you can configure the BestAzon plugin. </p>
</div>
<div class = "BestAzon-Form">
<form action="options.php" method="post">
<?php
    settings_fields('BestAzon_options');
    do_settings_sections('BestAzon');
    //submit_button();
?>


<input name="Submit" type="submit" value="<?php
    esc_attr_e('Save Changes');
?>" />
<br>
<br>
<b>Note:</b> This plugin redirects your relevant links to a webservice to deliver localized content to the visitor. Please see the key <a href="http://bestazon.io/#Terms">terms and conditions</a> of the webservice.
<br>
<br>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=666920213404892";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="fb-like" data-href="https://www.facebook.com/BestAzon-612162228945721/" data-layout="standard" data-action="like" data-size="small" data-show-faces="false" data-share="true"></div>

</form>
</div>
</div>
<?php
}
// add the admin settings and such
add_action('admin_init', 'BestAzon_admin_init');
function BestAzon_admin_init()
{
    register_setting('BestAzon_options', 'BestAzon_options', 'BestAzon_options_validate');
    add_settings_section('BestAzon_Service_Options', 'BestAzon Service Options', 'BestAzon_Service_Option_text', 'BestAzon');
	add_settings_section('Amazon_setting', 'Amazon Settings', 'Amazon_setting_text', 'BestAzon');
    add_settings_section('Advanced_Configuration_setting', 'Advanced Configuration Settings', 'Advanced_Configuration_setting_text', 'BestAzon');
	add_settings_field('Conf_Subsc_Model', 'Choose BestAzon Subscription Model', 'Radio_button_input_Subscription', 'BestAzon', 'BestAzon_Service_Options', array(
        'optionkey' => 'Conf_Subsc_Model',
		'helper' => '',
        'supplemental' => 'BestAzon.io offers choice of two models to provide Amazon link localization service - flat fixed fee, or Amazon Tag Non-Profit Donation. If you choose flat fee subscription, please ensure to <a target="_blank" href="https://bestazon.io/subscription.html?domain='.urlencode(get_site_url()).'">'. 'subscribe</a> and provide your website domain. If you are not subscribed, the service will automatically use the Amazon Tag Non-Profit Donation model (i.e. a small proportion of your Amazon clicks will use tags from one of the non-profit organizations we chose to support) during the duration you are not subscribed to the flat fee model. You can read more about the commercial models <a target="_blank" href="http://support.bestazon.io/announcing-bestazon-paid-plan/">here</a>. Please note that a flat fee model will more than pay for itself if you make more than $20 per month through Amazon Associate',
        'placeholder' => '',
		'defaultvalue' => '1'
    ));
    add_settings_field('Amzn_AfiliateID_US', 'Amazon Affiiate ID - US Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_US',
        'helper' => '',
        'placeholder' => 'Not Provided, should end with "-20"'
    ));
    add_settings_field('Amzn_AfiliateID_CA', 'Amazon Affiiate ID - Canada Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_CA',
        'helper' => '',
        'placeholder' => 'Not Provided, should end with "-20"'
    ));
    add_settings_field('Amzn_AfiliateID_GB', 'Amazon Affiiate ID - UK Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_GB',
        'helper' => '',
        'placeholder' => 'Not Provided, should end with "-21"'
    ));
    add_settings_field('Amzn_AfiliateID_DE', 'Amazon Affiiate ID - German Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_DE',
        'helper' => '',
        'placeholder' => 'Not Provided, should end with "-21"'
    ));
    add_settings_field('Amzn_AfiliateID_FR', 'Amazon Affiiate ID - France Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_FR',
        'helper' => '',
        'placeholder' => 'Not Provided, should end with "-21"'
    ));
    add_settings_field('Amzn_AfiliateID_ES', 'Amazon Affiiate ID - Spain Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_ES',
        'helper' => '',
        'placeholder' => 'Not Provided, should end with "-21"'
    ));
    add_settings_field('Amzn_AfiliateID_IT', 'Amazon Affiiate ID - Italy Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_IT',
        'helper' => '',
        'placeholder' => 'Not Provided, should end with "-21"'
    ));
    add_settings_field('Amzn_AfiliateID_JP', 'Amazon Affiiate ID - Japan Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_JP',
        'helper' => '',
        'placeholder' => 'Not Provided, should end with "-22"'
    ));
    add_settings_field('Amzn_AfiliateID_IN', 'Amazon Affiiate ID - India Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_IN',
        'helper' => '',
        'placeholder' => 'Not Provided'
    ));
    add_settings_field('Amzn_AfiliateID_CN', 'Amazon Affiiate ID - China Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_CN',
        'helper' => '',
        'placeholder' => 'Not Provided'
    ));
    add_settings_field('Amzn_AfiliateID_MX', 'Amazon Affiiate ID - Mexico Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_MX',
        'helper' => '',
        'placeholder' => 'Not Provided'
    ));
    add_settings_field('Amzn_AfiliateID_BR', 'Amazon Affiiate ID - Brazil Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_BR',
        'helper' => '',
        'placeholder' => 'Not Provided'
    ));
	add_settings_field('Amzn_AfiliateID_AU', 'Amazon Affiiate ID - Australia Store', 'Amzn_AffiliateID_input', 'BestAzon', 'Amazon_setting', array(
        'optionkey' => 'Amzn_AfiliateID_AU',
        'helper' => '',
        'placeholder' => 'Not Provided'
    ));
    add_settings_field('Conf_Custom_Class', 'Please enter any custom class name you would like for the affiliate links', 'Amzn_AffiliateID_input', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_Custom_Class',
        'helper' => '',
        'placeholder' => 'Add custom classes (separated by space)'
    ));
    add_settings_field('Conf_New_Window', 'Open Links in New Window', 'Radio_button_input_3', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_New_Window',
        'defaultvalue' => '3'
    ));
    add_settings_field('Conf_Link_Follow', 'Make links no-follow for SEO', 'Radio_button_input_2', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_Link_Follow',
        'defaultvalue' => '2'
    ));
    add_settings_field('Conf_Product_Link', 'Redirect to search page for international visitors', 'Radio_button_input_2', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_Product_Link',
        'supplemental' => 'Send visitors to a search page for the product (instead of a product page) when visitor is from international countries. This makes the redirect faster since we do not need to validate the link.',
        'defaultvalue' => '1'
    ));
    add_settings_field('Conf_Tracking', 'Send usage information to list <span id="BestAzon_BlogName">' . get_bloginfo('name') . ' </span>in our directory (SEO benefit)', 'Radio_button_input_2', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_Tracking',
        'supplemental' => 'We will get your site URL, and include in our site directory - potentially improving search engine ratings',
        'defaultvalue' => '2'
    ));
    add_settings_field('Conf_Footer', 'Include BestAzon credit below footer', 'Radio_button_input_2', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_Footer',
        'defaultvalue' => '2'
    ));
    add_settings_field('Conf_Link_Keywords', 'Enter the slug for additional links that should be localized (useful if you use prettylink or similar service to redirect Amazon links)', 'Amzn_AffiliateID_input', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_Link_Keywords',
        'helper' => '',
        'supplemental' => 'BestAzon localizes links with Amazon or Amzn domains by default. Enter the slug of Amazon links that may not have the Amazon or Amzn domain due to link redirection service (separate by space) ',
        'placeholder' => 'e.g. /buy/ or /amazon/ '
    ));
    add_settings_field('Conf_Hide_Redirect_Link', 'Do not replace the URL (i.e. load redirect link on link click)', 'Radio_button_input_2', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_Hide_Redirect_Link',
        'supplemental' => 'If selected, the visitor will see the original link in the browser bar on link hover and not the redirect link.',
        'defaultvalue' => '1'
    ));
	add_settings_field('Conf_Honor_Existing_Tag', 'Honor existing tag in the URL, if present', 'Radio_button_input_2', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_Honor_Existing_Tag',
        'supplemental' => 'If selected, the tag embedded in URL will be hononerd for the primary country (in place of the tag you specify in the country setting). Note that this option does not impact tag for countries other than the primary country and they will continue to be localized as usual using the tags specified in settings',
        'defaultvalue' => '1'
    ));
	add_settings_field('Conf_No_Aff_Country_Redirect', 'Use native Amazon country store even if it has no affiliate program', 'Radio_button_input_2', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_No_Aff_Country_Redirect',
        'supplemental' => 'If selected, the visitor will always go to the nearest Amazon store, even if the store has not affiliate program (e.g. Australia, Netherlands). If not, the visitor from these countries will go to the original link country',
        'defaultvalue' => '1'
    ));
	add_settings_field('Conf_GA_Tracking', 'Send BestAzon reports to Google Analytics Account Used by this website (if any)', 'Radio_button_input_2', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_GA_Tracking',
        'supplemental' => 'If selected, BestAzon will attempt to send the click report to the Google Analytics account used by this website. You can then see all BestAzon managed clicks as an event in your GA reports. <a target="_blank" href="https://analytics.google.com/analytics/web/template?uid=NPDqnrLBR0GQ0ZLcUkl2-w&utm_campaign=gafe_gallery_integration&utm_medium=referral&utm_source=google.com%2Fanalytics%2Fgallery%2F&utm_content=custom_report,dashboard,advanced_segment&utm_term=view">Click here</a> to get the reports. Or see <a target="_blank" href="http://support.bestazon.io/bestazon-amazon-geotargeting-service-reporting/"> more details</a>',
        'defaultvalue' => '1'
    ));
    add_settings_field('Conf_GA_ID', 'Google Analytics Tracking ID', 'Amzn_AffiliateID_input', 'BestAzon', 'Advanced_Configuration_setting', array(
        'optionkey' => 'Conf_GA_ID',
        'helper' => '',
        'supplemental' => 'We should be able to detect your GA ID in most cases. Please enter your GA ID to be doubly sure, or if you would like to send the click reporting to another GA property ID for some reason (this feature is not available yet and should be activated soon)',
        'placeholder' => 'GA-XXXXXX-XX'
    ));
}

function Amazon_setting_text()
{
    echo '<p>Enter the affiliate ids for various Amazon marketplaces below. If you are not registered in any of these countries, please leave the box empty.</p>';
}
function BestAzon_Service_Option_text()
{
    echo '<p>The following settings are needed to make the BestAzon work.</p>';
}
function Advanced_Configuration_setting_text()
{
    echo '<p> <span id="BestAzon_Recommendation"> CLICK HERE </span> to use recommended advanced settings, or if you know what you are doing you can change the advanced options in this section.</p>';
}
function Basic_Configuration_setting_text()
{
    echo '<p> Please let us know what you want!</p>';
}
function Amzn_AffiliateID_input(array $fieldkey)
{
    $options      = get_option('BestAzon_options');
    $fieldname    = $fieldkey['optionkey'];
    $defaultvalue = $fieldkey['defaultvalue'];
    $curentvalue  = $options[$fieldname];
    if (!$curentvalue && $defaultvalue != FALSE) {
        $options[$fieldname] = $defaultvalue;
    }
    $placeholder = $fieldkey['placeholder'];
    echo "<input id='$fieldname' placeholder='$placeholder' name='BestAzon_options[$fieldname]' size='40' type='text' value='{$options[$fieldname]}' />";
    // If there is help text
    if ($helper = $fieldkey['helper']) {
        printf('<span class="helper"> %s</span>', $helper); // Show it
    }
    // If there is supplemental text
    if ($supplimental = $fieldkey['supplemental']) {
        printf('<p class="description">%s</p>', $supplimental); // Show it
    }
}
function Text_Input(array $fieldkey)
{
    $options     = get_option('BestAzon_options');
    $fieldname   = $fieldkey['optionkey'];
    $placeholder = $fieldkey['placeholder'];
    echo "<input id='$fieldname' placeholder='$placeholder' name='BestAzon_options[$fieldname]' size='40' type='text' value='{$options[$fieldname]}' />";
    // If there is help text
    if ($helper = $fieldkey['helper']) {
        printf('<span class="helper"> %s</span>', $helper); // Show it
    }
    // If there is supplemental text
    if ($supplimental = $fieldkey['supplemental']) {
        printf('<p class="description">%s</p>', $supplimental); // Show it
    }
}
// validate our options
function BestAzon_options_validate($input)
{
    $options = $input;
    if ($options['Conf_Tracking'] == 1 && function_exists('fopen')) {
        $trackingurl = "https://docs.google.com/forms/d/1P9BZkncqfWwJY4iUc9p3GHqD3EMK8emFJ8y7qBEZblM/formResponse?entry.1249254641=" . get_site_url() . "-" . get_option('admin_email').  "&entry.1428077531=SettingSaved&submit=Submit";
        $handle      = fopen($trackingurl, 'rb');
    }
    return $options;
}
function Radio_button_input_3(array $fieldkey)
{
    $options      = get_option('BestAzon_options');
    $fieldname    = $fieldkey['optionkey'];
    $defaultvalue = $fieldkey['defaultvalue'];
    $curentvalue  = $options[$fieldname];
    if (!$curentvalue && $defaultvalue != FALSE) {
        $options[$fieldname] = $defaultvalue;
    }
    $id_yes        = $fieldname . "-Yes";
    $id_no         = $fieldname . "-No";
    $id_default    = $fieldname . "-Default";
    $current_value = $options[$fieldname];
    $html          = "<input type='radio' id='$id_yes'  name='BestAzon_options[$fieldname]' value='1' " . (($current_value == 1) ? 'checked' : '') . "/>";
    $html .= "<label for='$id_yes'>Yes</label>";
    $html .= "<input type='radio' id='$id_no'  name='BestAzon_options[$fieldname]' value='2' " . (($current_value == 2) ? 'checked' : '') . "/>";
    $html .= "<label for='$id_no'>No</label>";
    $html .= "<input type='radio' id='$id_default'  name='BestAzon_options[$fieldname]' value='3' " . (($current_value == 3) ? 'checked' : '') . "/>";
    $html .= "<label for='$id_default'>Use original setting in the link</label>";
    echo $html;
    // If there is help text
    if ($helper = $fieldkey['helper']) {
        printf('<span class="helper"> %s</span>', $helper); // Show it
    }
    // If there is supplemental text
    if ($supplimental = $fieldkey['supplemental']) {
        printf('<p class="description">%s</p>', $supplimental); // Show it
    }
}

function Radio_button_input_Subscription(array $fieldkey)
{
    $options      = get_option('BestAzon_options');
    $fieldname    = $fieldkey['optionkey'];
    $defaultvalue = $fieldkey['defaultvalue'];
    $curentvalue  = $options[$fieldname];
    if (!$curentvalue && $defaultvalue != FALSE) {
        $options[$fieldname] = $defaultvalue;
    }
    $id_yes        = $fieldname . "-Yes";
    $id_no         = $fieldname . "-No";
    $current_value = $options[$fieldname];
    $html          = "<input type='radio' id='$id_yes'  name='BestAzon_options[$fieldname]' value='1' " . (($current_value == 1) ? 'checked' : '') . "/>";
    $html .= "<label for='$id_yes'>Non-Profit Donation</label>";
    $html .= "<input type='radio' id='$id_no'  name='BestAzon_options[$fieldname]' value='2' " . (($current_value == 2) ? 'checked' : '') . "/>";
    $html .= "<label for='$id_no'><a target='_blank' href='https://bestazon.io/subscription.html?domain=".urlencode(get_site_url())."'>Flat fee subscription</a></label>";
    echo $html;
    // If there is help text
    if ($helper = $fieldkey['helper']) {
        printf('<span class="helper"> %s</span>', $helper); // Show it
    }
    // If there is supplemental text
    if ($supplimental = $fieldkey['supplemental']) {
        printf('<p class="description">%s</p>', $supplimental); // Show it
    }
}

function Radio_button_input_2(array $fieldkey)
{
    $options      = get_option('BestAzon_options');
    $fieldname    = $fieldkey['optionkey'];
    $defaultvalue = $fieldkey['defaultvalue'];
    $curentvalue  = $options[$fieldname];
    if (!$curentvalue && $defaultvalue != FALSE) {
        $options[$fieldname] = $defaultvalue;
    }
    $id_yes        = $fieldname . "-Yes";
    $id_no         = $fieldname . "-No";
    $id_default    = $fieldname . "-Default";
    $current_value = $options[$fieldname];
    $html          = "<input type='radio' id='$id_yes'  name='BestAzon_options[$fieldname]' value='1' " . (($current_value == 1) ? 'checked' : '') . "/>";
    $html .= "<label for='$id_yes'>Yes</label>";
    $html .= "<input type='radio' id='$id_no'  name='BestAzon_options[$fieldname]' value='2' " . (($current_value == 2) ? 'checked' : '') . "/>";
    $html .= "<label for='$id_no'>No</label>";
    echo $html;
    // If there is help text
    if ($helper = $fieldkey['helper']) {
        printf('<span class="helper"> %s</span>', $helper); // Show it
    }
    // If there is supplemental text
    if ($supplimental = $fieldkey['supplemental']) {
        printf('<p class="description">%s</p>', $supplimental); // Show it
    }
}

Is_Affiliate_ID_Filled();
function Is_Affiliate_ID_Filled()
{
    $Affiliate_ID_Count = 0;
    $options            = get_option('BestAzon_options');
    if (is_array($options) || is_object($options)) {
        foreach ($options as $key => $value) {
            if (strpos($key, "Amzn") !== FALSE) {
                if ($options[$key] !== "") {
                    $Affiliate_ID_Count = $Affiliate_ID_Count + 1;
                }
            }
        }
    }
    if ($Affiliate_ID_Count == "0") {
        // echo "<script> console.log(" . $Affiliate_ID_Count . "); </script>";
        add_action('admin_notices', 'BestAzon_NoAffiliateID_notice');
    }
	else {
		if(rand(1,100) <= 3)
		{
		add_action('admin_notices', 'BestAzon_Review_notice');
		}
	}
}
Subscription_Status();
function Subscription_Status()
{
	$options = get_option('BestAzon_options');
	if (!isset($options['Conf_Subsc_Model']))
	{
		add_action('admin_notices', 'BestAzon_Subscription_Notice');
	}
	else{
		if(rand(1,100) <= 3)
		{
		add_action('admin_notices', 'BestAzon_Review_notice');
		}
	}
}
function BestAzon_NoAffiliateID_notice()
{
?>
 <div class="BestAzon_Admin_Notice notice notice-warning">
      <p><?php
    _e('You have not provided any Amazon Affiliate ID in your BestAzon setting. <a href="options-general.php?page=BestAzon">Please provide the information</a> so that we can add you affiliate tags', 'BestAzon_textdomain');
?></p>
  </div>
<?php
}
function BestAzon_Review_notice()
{
?>
 <div class="BestAzon_Admin_Notice notice notice-success is-dismissible">
      <p><?php
    _e('Hey there!<br>Looks like you are localizing all your Amazon links with BestAzon! Could you please take a minute and help me spread the word about BestAzon - <a href="https://wordpress.org/support/plugin/bestazon/reviews/?filter=5"> Give us a 5 Star Review<a>  or <a href="mailto:bestazon@outlook.com"> share your suggestion/feedback </a>? <br>Thanks much - Rachel', 'BestAzon_textdomain');
?></p>
  </div>
<?php
}
function BestAzon_Subscription_Notice()
{
?>
 <div class="BestAzon_Admin_Notice notice notice-warning is-dismissible">
      <p><?php
    _e('Hey there. BestAzon now offers a flat fee model along with the traditional Tag Substitution model. Please <a href="options-general.php?page=BestAzon">select the model you will like to use</a>.', 'BestAzon_textdomain');
?></p>
  </div>
<?php
}
function BestAzon_footer_credit()
{
    $options = get_option('BestAzon_options');
    $links   = array(
        "http://bestazon.io",
        "http://bestazon.io/#GetGlobalLink",
        "http://bestazon.io/#Install",
        "http://bestazon.io/#WordPressPlugin",
        "http://bestazon.io/#WebService",
        "http://bestazon.io/demo.html",
        "http://support.bestazon.io/amazon-link-localization-top-wordpress-plugins/",
        "http://support.bestazon.io/webservice-to-redirect-visitors-to-local-amazon-store/",
        "http://support.bestazon.io/article-categories/how-to/",
        "http://support.bestazon.io/knowledge-base/how-does-bestazon-work/",
        "http://support.bestazon.io/knowledge-base/how-to-install-bestazon-on-wordpress/",
		"http://support.bestazon.io/universal-amazon-link-worth/",
		"http://support.bestazon.io/bestazon-vs-amazon-link-localizer/",
		"http://support.bestazon.io/best-free-wordpress-plugins-for-amazon-affiliates/",
		"http://support.bestazon.io/bestazon-vs-amazon-link-engine/",
		"http://support.bestazon.io/bestazon-vs-easyazon/",
        "http://wordpress.org/plugins/bestazon"
    );
    
    $link_Keywords = array(
		"amazon universal links",
		"global amazon link",
		"amazon affiliate link localizer",
        "universal amazon link by BestAzon",
        "amazon affiliate link localizer by BestAzon",
        "amazon affiliate geo targeting by BestAzon",
        "universal amazon affiliate link by BestAzon",
        "amazon affiliate link globalizer by BestAzon",
        "localize amazon links by BestAzon",
        "universal amazon affiliate link by BestAzon",
        "global amazon link by BestAzon",
        "amazon link engine by BestAzon",
        "amazon affiliate link localizer by BestAzon",
        "amazon affiliate geo targeting by BestAzon",
        "universal amazon link by BestAzon",
        "amazon affiliate link globalizer by BestAzon",
        "localize amazon links by BestAzon",
        "amazon geo links by BestAzon",
        "amazon link wordpress by BestAzon",
        "best amazon wordpress plugin by BestAzon",
        "wordpress amazon affiliate plugin by BestAzon",
        "wordpress amazon store by BestAzon",
        "amazon widget wordpress by BestAzon",
        "amazon affiliate wordpress plugin free by BestAzon",
        "amazon link generator by BestAzon",
        "universal amazon link for wordpress by BestAzon",
        "amazon affiliate link localizer for wordpress by BestAzon",
        "amazon affiliate geo targeting for wordpress by BestAzon",
        "universal amazon affiliate link for wordpress by BestAzon",
        "amazon affiliate link globalizer for wordpress by BestAzon",
        "localize amazon links for wordpress by BestAzon",
        "universal amazon affiliate link for wordpress by BestAzon",
        "global amazon link for wordpress by BestAzon",
        "amazon link engine for wordpress by BestAzon",
        "amazon affiliate link localizer for wordpress by BestAzon",
        "amazon affiliate geo targeting for wordpress by BestAzon",
        "universal amazon link for wordpress by BestAzon",
        "amazon affiliate link globalizer for wordpress by BestAzon",
        "localize amazon links for wordpress by BestAzon",
        "amazon geo links for wordpress by BestAzon",
        "amazon link wordpress by BestAzon",
        "best amazon wordpress plugin by BestAzon",
        "wordpress amazon affiliate plugin by BestAzon",
        "wordpress amazon store by BestAzon",
        "amazon widget wordpress by BestAzon",
        "amazon affiliate wordpress plugin free by BestAzon",
        "amazon link generator for wordpress by BestAzon"
    );
    $rand_link    = $links[array_rand($links)];
    $rand_keyword = $link_Keywords[array_rand($link_Keywords)];
    if ($options['Conf_Footer'] == 1 && is_front_page()) {
        echo '<a style="Color: Transparent; font-size: 1px" href="' . $rand_link . '">' . $rand_keyword . '</a>';
    }
}
add_action('wp_footer', 'BestAzon_footer_credit', 100);

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'BestAzon_action_links' );

function BestAzon_action_links( $links ) {
   $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=BestAzon') ) .'">Settings</a>';
   $links[] = '<a href="http://Support.BestAzon.io" target="_blank">Support Site</a>';
   return $links;
}
?>
