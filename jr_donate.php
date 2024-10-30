<?php
/*
Plugin Name: JR Donate
Plugin URI: http://www.jakeruston.co.uk/2009/10/wordpress-plugin-jr-donate/
Description: Displays a donate widget on your blog, which allows users to donate to you via PayPal.
Version: 1.7.5
Author: Jake Ruston
Author URI: http://www.jakeruston.co.uk
*/

/*  Copyright 2010 Jake Ruston - the.escapist22@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$pluginname="donate";

// Hook for adding admin menus
add_action('admin_menu', 'jr_donate_add_pages');

// action function for above hook
function jr_donate_add_pages() {
    add_options_page('JR Donate', 'JR Donate', 'administrator', 'jr_donate', 'jr_donate_options_page');
}

if (!function_exists("jr_show_notices")) {
function jr_show_notices() {
echo "<div id='warning' class='updated fade'><b>Ouch! You currently do not have cURL enabled on your server. This will affect the operations of your plugins.</b></div>";
}
}

if (!_iscurlinstalled()) {
add_action("admin_notices", "jr_show_notices");

} else {
if (!defined("ch"))
{
function setupch()
{
$ch = curl_init();
$c = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
return($ch);
}
define("ch", setupch());
}

if (!function_exists("curl_get_contents")) {
function curl_get_contents($url)
{
$c = curl_setopt(ch, CURLOPT_URL, $url);
return(curl_exec(ch));
}
}
}

if (!function_exists("jr_donate_refresh")) {
function jr_donate_refresh() {
update_option("jr_submitted_donate", "0");
}
}

register_activation_hook(__FILE__,'donate_choice');

function donate_choice () {
if (get_option("jr_donate_links_choice")=="") {

if (_iscurlinstalled()) {
$pname="jr_donate";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_donate", "1");
wp_schedule_single_event(time()+172800, 'jr_donate_refresh'); 
} else {
$content = "Powered by <a href='http://arcade.xeromi.com'>Free Online Games</a> and <a href='http://directory.xeromi.com'>General Web Directory</a>.";
}

if ($content!="") {
$content=utf8_encode($content);
update_option("jr_donate_links_choice", $content);
}
}

if (get_option("jr_donate_link_personal")=="") {
$content = curl_get_contents("http://www.jakeruston.co.uk/p_pluginslink4.php");

update_option("jr_donate_link_personal", $content);
}
}

// jr_donate_options_page() displays the page content for the Test Options submenu
function jr_donate_options_page() {

    // variables for the field and option names 
    $opt_name = 'mt_donate_header';
    $opt_name_2 = 'mt_donate_address';
    $opt_name_4 = 'mt_donate_total';
    $opt_name_5 = 'mt_donate_currency';
    $opt_name_6 = 'mt_donate_plugin_support';
    $opt_name_7 = 'mt_donate_javascript';
    $opt_name_10 = 'mt_donate_message';
	$opt_name_9 = 'mt_donate_message2';
    $hidden_field_name = 'mt_donate_submit_hidden';
    $data_field_name = 'mt_donate_header';
    $data_field_name_2 = 'mt_donate_address';
    $data_field_name_4 = 'mt_donate_total';
    $data_field_name_5 = 'mt_donate_currency';
    $data_field_name_6 = 'mt_donate_plugin_support';
    $data_field_name_7 = 'mt_donate_javascript';
    $data_field_name_10 = 'mt_donate_message';
	$data_field_name_9 = 'mt_donate_message2';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );
    $opt_val_2 = get_option( $opt_name_2 );
    $opt_val_4 = get_option( $opt_name_4 );
    $opt_val_5 = get_option( $opt_name_5 );
    $opt_val_6 = get_option( $opt_name_6 );
    $opt_val_7 = get_option( $opt_name_7 );
    $opt_val_8 = get_option( $opt_name_8 );
	$opt_val_9 = get_option( $opt_name_9 );
    
if (!$_POST['feedback']=='') {
$my_email1="the.escapist22@gmail.com";
$plugin_name="JR Donate";
$blog_url_feedback=get_bloginfo('url');
$user_email=$_POST['email'];
$user_email=stripslashes($user_email);
$subject=$_POST['subject'];
$subject=stripslashes($subject);
$name=$_POST['name'];
$name=stripslashes($name);
$response=$_POST['response'];
$response=stripslashes($response);
$category=$_POST['category'];
$category=stripslashes($category);
if ($response=="Yes") {
$response="REQUIRED: ";
}
$feedback_feedback=$_POST['feedback'];
$feedback_feedback=stripslashes($feedback_feedback);
if ($user_email=="") {
$headers1 = "From: feedback@jakeruston.co.uk";
} else {
$headers1 = "From: $user_email";
}
$emailsubject1=$response.$plugin_name." - ".$category." - ".$subject;
$emailmessage1="Blog: $blog_url_feedback\n\nUser Name: $name\n\nUser E-Mail: $user_email\n\nMessage: $feedback_feedback";
mail($my_email1,$emailsubject1,$emailmessage1,$headers1);
?>
<div class="updated"><p><strong><?php _e('Feedback Sent!', 'mt_trans_domain' ); ?></strong></p></div>
<?php
}

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];
        $opt_val_2 = $_POST[ $data_field_name_2 ];
        $opt_val_5 = $_POST[ $data_field_name_5 ];
        $opt_val_6 = $_POST[$data_field_name_6];
        $opt_val_7 = $_POST[$data_field_name_7];
        $opt_val_8 = $_POST[$data_field_name_8];
		$opt_val_9 = $_POST[$data_field_name_9];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );
        update_option( $opt_name_2, $opt_val_2 );
        update_option( $opt_name_5, $opt_val_5 );
        update_option( $opt_name_6, $opt_val_6 );  
        update_option( $opt_name_7, $opt_val_7 ); 
        update_option( $opt_name_8, $opt_val_8 );
		update_option( $opt_name_9, $opt_val_9 );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Currency & Donating settings saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'JR Donate Plugin Options', 'mt_trans_domain' ) . "</h2>";
$blog_url_feedback=get_bloginfo('url');
	$donated=curl_get_contents("http://www.jakeruston.co.uk/p-donation/index.php?url=".$blog_url_feedback);
	if ($donated=="1") {
	?>
		<div class="updated"><p><strong><?php _e('Thank you for donating!', 'mt_trans_domain' ); ?></strong></p></div>
	<?php
	} else {
	if ($_POST['mtdonationjr']!="") {
	update_option("mtdonationjr", "444");
	}
	
	if (get_option("mtdonationjr")=="") {
	?>
	<div class="updated"><p><strong><?php _e('Please consider donating to help support the development of my plugins!', 'mt_trans_domain' ); ?></strong><br /><br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ULRRFEPGZ6PSJ">
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form></p><br /><form action="" method="post"><input type="hidden" name="mtdonationjr" value="444" /><input type="submit" value="Don't Show This Again" /></form></div>
<?php
}
}

    // options form
    
    $change4 = get_option("mt_donate_plugin_support");
    $change5 = get_option("mt_donate_javascript");
    $change6 = get_option("mt_donate_message");
	$change7 = get_option("mt_donate_message2");

if ($change4=="Yes" || $change4=="") {
$change4="checked";
$change41="";
} else {
$change4="";
$change41="checked";
}

if ($change5=="Yes" || $change5=="") {
$change5="checked";
$change51="";
} else {
$change5="";
$change51="checked";
}

if ($change6=="Yes" || $change6=="") {
$change6="checked";
$change61="";
} else {
$change6="";
$change61="checked";
}
    ?>
	<iframe src="http://www.jakeruston.co.uk/plugins/index.php" width="100%" height="20%">iframe support is required to see this.</iframe>
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Donate Widget Title", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="50">
</p><hr />

<p><?php _e("Widget Message (Appears below Title):", 'mt_trans_domain' ); ?> 
<textarea name="<?php echo $data_field_name_9; ?>"><?php echo $change7; ?></textarea>
</p><hr />

<p><?php _e("PayPal E-Mail Address", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_2; ?>" value="<?php echo $opt_val_2; ?>" size="50">
</p><hr />

<p><?php _e("Currency", 'mt_trans_domain' ); ?> 
<select name="<?php echo $data_field_name_5; ?>">
<option value="USD" selected>USD - US Dollars</option>
<option value="GBP">GBP - British Pounds</option>
<option value="AUD">AUD - Australian Dollars</option>
<option value="BRL">BRL - Brazilian Real</option>
<option value="CAD">CAD - Canadian Dollars</option>
<option value="CZK">CZK - Czech Koruny</option>
<option value="DKK">DKK - Danish Kroner</option>
<option value="EUR">EUR - Euros</option>
<option value="HKD">HKD - Hong Kong Dollars</option>
<option value="HUF">HUF - Hungarian Forints</option>
<option value="ILS">ILS - Israeli New Shekels</option>
<option value="JPY">JPY - Japanese Yen</option>
<option value="MYR">MYR - Malaysian Ringgits</option>
<option value="MXN">MXN - Mexican Pesos</option>
<option value="NZD">NZD - New Zealand Dollars</option>
<option value="NOK">NOK - Norwegian Krone</option>
<option value="PHP">PHP - Philippine Pesos</option>
<option value="PLN">PLN - Polish Zlotych</option>
<option value="SGD">SGD - Singapore Dollars</option>
<option value="SEK">SEK - Swedish Kronor</option>
<option value="CHF">CHF - Swiss Francs</option>
<option value="TWD">TWD - Taiwan New Dollars</option>
<option value="THB">THB - Thai Baht</option></select>
</p><hr />

<p><?php _e("Enable Javascript Dropdown?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_7; ?>" value="Yes" <?php echo $change5; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_7; ?>" value="No" <?php echo $change51; ?>>No
</p><hr />

<p><?php _e("When donated, go to this URL:", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_10; ?>" value="<?php echo $opt_val_10; ?>" size="50" />
</p><hr />

<p><?php _e("Show Plugin Support?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_6; ?>" value="Yes" <?php echo $change4; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_6; ?>" value="No" <?php echo $change41; ?> id="Please do not disable plugin support - This is the only thing I get from creating this free plugin!" onClick="alert(id)">No
</p><hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p><hr />

</form>
<script type="text/javascript">
function validate_required(field,alerttxt)
{
with (field)
  {
  if (value==null||value=="")
    {
    alert(alerttxt);return false;
    }
  else
    {
    return true;
    }
  }
}

function validateEmail(ctrl){

var strMail = ctrl.value
        var regMail =  /^\w+([-.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;

        if (regMail.test(strMail))
        {
            return true;
        }
        else
        {

            return false;

        }
					
	}

function validate_form(thisform)
{
with (thisform)
  {
  if (validate_required(subject,"Subject must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(email,"E-Mail must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(feedback,"Feedback must be filled out!")==false)
  {email.focus();return false;}
  if (validateEmail(email)==false)
  {
  alert("E-Mail Address not valid!");
  email.focus();
  return false;
  }
 }
}
</script>
<h3>Submit Feedback about my Plugin!</h3>
<p><b>Note: Only send feedback in english, I cannot understand other languages!</b><br /><b>Please do not send spam messages!</b></p>
<form name="form2" method="post" action="" onsubmit="return validate_form(this)">
<p><?php _e("Your Name:", 'mt_trans_domain' ); ?> 
<input type="text" name="name" /></p>
<p><?php _e("E-Mail Address (Required):", 'mt_trans_domain' ); ?> 
<input type="text" name="email" /></p>
<p><?php _e("Message Category:", 'mt_trans_domain'); ?>
<select name="category">
<option value="General">General</option>
<option value="Feedback">Feedback</option>
<option value="Bug Report">Bug Report</option>
<option value="Feature Request">Feature Request</option>
<option value="Other">Other</option>
</select>
<p><?php _e("Message Subject (Required):", 'mt_trans_domain' ); ?>
<input type="text" name="subject" /></p>
<input type="checkbox" name="response" value="Yes" /> I want e-mailing back about this feedback</p>
<p><?php _e("Message Comment (Required):", 'mt_trans_domain' ); ?> 
<textarea name="feedback"></textarea>
</p>
<p class="submit">
<input type="submit" name="Send" value="<?php _e('Send', 'mt_trans_domain' ); ?>" />
</p><hr /></form>
</div>
<?php
}

if (get_option("jr_donate_links_choice")=="") {
donate_choice();
}

function show_donations($args) {

extract($args);

$option_header=get_option("mt_donate_header");
$option_address=get_option("mt_donate_address");
$option_currency=get_option("mt_donate_currency");
$option_total=get_option("mt_donate_total");
$option_message=get_option("mt_donate_message");
$option_javascript=get_option("mt_donate_javascript");
$plugin_support=get_option("mt_donate_plugin_support");
$message2=get_option("mt_donate_message2");

if ($option_header=="") {
$option_header="Donate to Me!";
}

if ($option_address=="") {
$option_address="paypal@jakeruston.co.uk";
}

if ($option_currency=="") {
$option_currency="USD";
}

if ($option_target=="") {
$option_target="0";
}

if ($option_total=="") {
$option_total="0";
}

if ($plugin_support=="") {
$plugin_support="Yes";
}

if ($option_javascript=="") {
$option_javascript="No";
}

$blog_url=get_bloginfo('url');

if ($option_javascript=="Yes") {
echo '<script type="text/javascript" src="'.$blog_url.'/wp-content/plugins/jr-donate/javascript.js"></script>';
}

echo $before_title.$option_header.$after_title.$before_widget;

echo $message2 . "<br /><br />";

if ($option_javascript=="Yes") {
?>
<script type="text/javascript">
animatedcollapse.ontoggle=function($, divobj, state){ //fires each time a DIV is expanded/contracted
}
animatedcollapse.init()
animatedcollapse.addDiv('donate', 'fade=1')
</script>
<div id="donate" align="center">
<?php
}
?>
<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post" /><input type="text" name="amount" /><?php echo $option_currency; ?><br /><input type="hidden" name="cmd" value="_xclick"><input type="hidden" name="business" value="<?php echo $option_address; ?>"><input type="hidden" name="item_name" value="Donation"><input type="hidden" name="return" value="<?php echo $data_field_name_8; ?>" />
<input type="hidden" name="cancel_return" value="<?php echo $blog_url; ?>" /><input type="hidden" name="currency_code" value="<?php echo $option_currency; ?>"><input type="submit" value="Donate" /></form>

<?php if ($option_javascript=="Yes") { echo "</div>"; } ?>

<?php if ($option_javascript=="Yes") { ?><p><a href="javascript:animatedcollapse.toggle('donate')">Show/Hide</a></p><?php } ?>

<?php
if ($plugin_support=="Yes" || $plugin_support=="") {
$linkper=utf8_decode(get_option('jr_donate_link_personal'));

if (get_option("jr_donate_link_newcheck")=="") {
$pieces=explode("</a>", get_option('jr_donate_links_choice'));
$pieces[0]=str_replace(" ", "%20", $pieces[0]);
$pieces[0]=curl_get_contents("http://www.jakeruston.co.uk/newcheck.php?q=".$pieces[0]."");
$new=implode("</a>", $pieces);
update_option("jr_donate_links_choice", $new);
update_option("jr_donate_link_newcheck", "444");
}

if (get_option("jr_submitted_donate")=="0") {
$pname="jr_donate";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_donate", "1");
update_option("jr_donate_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_donate_refresh'); 
} else if (get_option("jr_submitted_donate")=="") {
$pname="jr_donate";
$url=get_bloginfo('url');
$current=get_option("jr_donate_links_choice");
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname."&override=".$current);
update_option("jr_submitted_donate", "1");
update_option("jr_donate_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_donate_refresh'); 
}

echo "<br /><p style='font-size:x-small'>Donate Plugin created by ".$linkper." - ".stripslashes(get_option('jr_donate_links_choice'))."</p>";
}

echo $after_widget;
?>

<?php
}

function init_donates_widget() {
register_sidebar_widget("JR Donate", "show_donations");
}

add_action("plugins_loaded", "init_donates_widget");

?>
