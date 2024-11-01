<?php
/**
 * @package     VikMailSMTP
 * @subpackage  layouts.settings
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

// whether to display the calculated SMTP settings
$proceed_button = false;

if ($_POST && isset($_POST['calcsettings']) && current_user_can('manage_options')) {
	// verify the nonce and the admin referer (dies in case of failure)
	check_admin_referer('wizard.calc');

	// calculate the SMTP settings
	if (VikMailSMTPSettings::calculate(sanitize_text_field($_POST['provider']), sanitize_text_field($_POST['whatemail']), sanitize_text_field($_POST['whatpwd']), true)) {
		// display success message and show the settings calculated
		VikMailSMTPBuilder::displayMessage('settings_calculated');
		$proceed_button = true;
	}
}

?>

<form method="post" action="" novalidate="novalidate" id="vikmailsmtp_form">
	<div class="vikmailsmtp-wizard-container">
		<div class="vikmailsmtp-wizard-inner">
			<div class="vikmailsmtp-wizard-providers<?php echo $proceed_button ? ' vms-hide' : ''; ?>">
				<h3><?php _e('Please select an SMTP provider', 'vikmailsmtp'); ?></h3>
				<ul>
					<li>
						<div class="vikmailsmtp-provider">
							<div class="vikmailsmtp-provider-radio">
								<input type="radio" name="provider" value="gmail" data-provname="<?php _e('Gmail', 'vikmailsmtp'); ?>" />
							</div>
							<div class="vikmailsmtp-provider-title">
								<img src="<?php echo plugin_dir_url(VIKMAILSMTP_BASEFILE); ?>assets/images/gmail.png" />
							</div>
						</div>
					</li>
					<li>
						<div class="vikmailsmtp-provider">
							<div class="vikmailsmtp-provider-radio">
								<input type="radio" name="provider" value="hotmail" data-provname="<?php _e('Windows Live Hotmail', 'vikmailsmtp'); ?>" />
							</div>
							<div class="vikmailsmtp-provider-title">
								<img src="<?php echo plugin_dir_url(VIKMAILSMTP_BASEFILE); ?>assets/images/hotmail.png" />
							</div>
						</div>
					</li>
					<li>
						<div class="vikmailsmtp-provider">
							<div class="vikmailsmtp-provider-radio">
								<input type="radio" name="provider" value="yahoo" data-provname="<?php _e('Yahoo', 'vikmailsmtp'); ?>" />
							</div>
							<div class="vikmailsmtp-provider-title">
								<img src="<?php echo plugin_dir_url(VIKMAILSMTP_BASEFILE); ?>assets/images/yahoo.jpg" />
							</div>
						</div>
					</li>
					<li>
						<div class="vikmailsmtp-provider">
							<div class="vikmailsmtp-provider-radio">
								<input type="radio" name="provider" value="custom" data-provname="<?php _e('Other SMTP Provider', 'vikmailsmtp'); ?>" />
							</div>
							<div class="vikmailsmtp-provider-title">
								<span><?php _e('Other SMTP provider...', 'vikmailsmtp'); ?></span>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="vikmailsmtp-wizard-email vms-hide">
				<h3 id="vms-provname"></h3>
				<div class="vikmailsmtp-wizard-whatemail">
					<label for="whatemail"><?php _e('What\'s your email address?', 'vikmailsmtp'); ?></label>
					<input type="text" name="whatemail" id="whatemail" value="" autocomplete="off" pattern="/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/" required />
				</div>
				<div class="vikmailsmtp-wizard-whatpwd">
					<label for="whatpwd"><?php _e('Please enter your password', 'vikmailsmtp'); ?></label>
					<input type="password" name="whatpwd" id="whatpwd" value="" autocomplete="off" />
				</div>
				<p class="vms-submit">
					<input name="calcsettings" id="submit" class="button button-large button-primary" value="<?php _e('Save', 'vikmailsmtp'); ?>" type="submit">
				</p>
			</div>
		<?php
		if ($proceed_button) {
			?>
			<div class="vikmailsmtp-wizard-complete">
				<h3><?php _e('Your SMTP Settings', 'vikmailsmtp'); ?></h3>
				<div class="vikmailsmtp-wizard-complete-data">
					<span class="vikmailsmtp-param-title"><?php _e('SMTP Host', 'vikmailsmtp'); ?></span>
					<span class="vikmailsmtp-param-value"><?php echo get_option('vikmailsmtp_host', 'localhost'); ?></span>
				</div>
				<div class="vikmailsmtp-wizard-complete-data">
					<span class="vikmailsmtp-param-title"><?php _e('SMTP Port', 'vikmailsmtp'); ?></span>
					<span class="vikmailsmtp-param-value"><?php echo get_option('vikmailsmtp_port', '25'); ?></span>
				</div>
				<div class="vikmailsmtp-wizard-complete-data">
					<span class="vikmailsmtp-param-title"><?php _e('SMTP Username', 'vikmailsmtp'); ?></span>
					<span class="vikmailsmtp-param-value"><?php echo VikMailSMTPCypher::decode(get_option('vikmailsmtp_user', '')); ?></span>
				</div>
				<p class="vms-submit">
					<a class="button button-large" href="options-general.php?page=vikmailsmtp"><?php _e('Click here to continue', 'vikmailsmtp'); ?></a>
				</p>
			</div>
			<?php
		}
		?>
		</div>
	</div>
	<?php
	// print nonce fields for later validation when POST data is submitted
	wp_nonce_field('wizard.calc');
	?>
</form>

<script type="text/javascript">
jQuery(document).ready(function() {
	/** Show the wizard window /*/
	setTimeout(function() {
		jQuery('.vikmailsmtp-wizard-inner').addClass('vms-enterpage');
	}, 500);

	/** Listener for the provider selection /*/
	jQuery('input[name="provider"]').click(function() {
		jQuery('#vms-provname').text(jQuery(this).attr('data-provname'));
		setTimeout(function() {
			jQuery('.vikmailsmtp-wizard-providers').hide();
			jQuery('.vikmailsmtp-wizard-email').show();
		}, 300);
	});

	/** Trigger the click of the radio when clicking on the provider's icon **/
	jQuery('.vikmailsmtp-provider-title').click(function() {
		jQuery(this).closest('.vikmailsmtp-provider').find('input[type="radio"]').trigger('click');
	});
});
</script>