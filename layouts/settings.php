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

if ($_POST && isset($_POST['savesettings']) && current_user_can('manage_options')) {
	// verify the nonce and the admin referer (dies in case of failure)
	check_admin_referer('settings.save');

	// update the plugin settings
	update_option('vikmailsmtp_host', sanitize_text_field($_POST['host']));
	update_option('vikmailsmtp_port', !empty($_POST['port']) ? intval($_POST['port']) : '');
	update_option('vikmailsmtp_security', sanitize_text_field($_POST['security']));
	update_option('vikmailsmtp_auth', isset($_POST['auth']) ? 1 : 0);
	update_option('vikmailsmtp_user', VikMailSMTPCypher::encode(sanitize_text_field($_POST['user'])));
	update_option('vikmailsmtp_pass', VikMailSMTPCypher::encode(sanitize_text_field($_POST['pass'])));
	update_option('vikmailsmtp_sender', sanitize_text_field($_POST['sender']));
	update_option('vikmailsmtp_logging', isset($_POST['logging']) ? 1 : 0);

	VikMailSMTPBuilder::displayMessage('settings_saved');
}

if ($_POST && isset($_POST['testmail']) && !isset($_POST['savesettings']) && current_user_can('manage_options')) {
	// verify the nonce and the admin referer (dies in case of failure)
	check_admin_referer('settings.save');

	// send configuration test email
	VikMailSMTPBuilder::sendTestEmail();
}

// check whether the params have been saved at least once
$empty_params = VikMailSMTPBuilder::paramsEmpty();

// some params that need to be checked multiple times
$security = get_option('vikmailsmtp_security', 'none');
$auth_enabled = (int)get_option('vikmailsmtp_auth', 0);
$logging_enabled = (int)get_option('vikmailsmtp_logging', 0);

// get current user email
$user_email = VikMailSMTPBuilder::getUserEmail();

// current smtp pwd
$current_smtppwd = VikMailSMTPCypher::decode(get_option('vikmailsmtp_pass', ''));
$current_smtpuser = VikMailSMTPCypher::decode(get_option('vikmailsmtp_user', ''));

?>
<div class="wrap">
	<h1><?php _e('SMTP Settings', 'vikmailsmtp'); ?></h1>

	<form method="post" action="" novalidate="novalidate" id="vikmailsmtp_form">

		<table class="form-table">

			<tbody>

				<tr>
					<th scope="row">
						<label for="host"><?php _e('SMTP Host', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<input name="host" id="host" value="<?php echo esc_attr(get_option('vikmailsmtp_host', 'localhost')); ?>" class="regular-text" type="text" aria-describedby="host-description">
						<p id="host-description" class="description"><?php _e('Enter the name of the SMTP Host.', 'vikmailsmtp'); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="port"><?php _e('SMTP Port', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<input name="port" id="port" value="<?php echo esc_attr(get_option('vikmailsmtp_port', '25')); ?>" class="regular-text" type="number" max="65535" step="1" min="1" aria-describedby="port-description">
						<p id="port-description" class="description"><?php _e('Enter the port number of the SMTP server. Usually: 25 when using an unsecure mail server - 465 when using a secure server with SMTPS - 25 or 587 when using a secure server with SMTP with STARTTLS extension.', 'vikmailsmtp'); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="security"><?php _e('SMTP Security', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<select name="security" id="security" aria-describedby="security-description">
							<option value="none" <?php selected($security, 'none'); ?>><?php _e('None', 'vikmailsmtp'); ?></option>
							<option value="ssl" <?php selected($security, 'ssl'); ?>><?php _e('SSL/TLS', 'vikmailsmtp'); ?></option>
							<option value="tls" <?php selected($security, 'tls'); ?>><?php _e('STARTTLS', 'vikmailsmtp'); ?></option>
						</select>
						<p id="security-description" class="description"><?php _e('Select the security model of the SMTP server. Usually: None for no encryption - SSL/TLS for SMTPS (usually on port 465) - STARTTLS for SMTP with STARTTLS extension (usually on port 25 or port 587).', 'vikmailsmtp'); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="auth"><?php _e('SMTP Authentication', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<input name="auth" id="auth" value="1" type="checkbox" onchange="vikwpToggleAuth(this.checked);" aria-describedby="auth-description" <?php checked($auth_enabled); ?>>
						<p id="auth-description" class="description"><?php _e('Enable this setting if your SMTP Host requires Authentication.', 'vikmailsmtp'); ?></p>
					</td>
				</tr>

				<tr class="vikwp-hidden" style="display: <?php echo $auth_enabled ? 'table-row' : 'none'; ?>;">
					<th scope="row">
						<label for="user"><?php _e('SMTP Username', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<input name="user" id="user" value="<?php echo esc_attr($current_smtpuser); ?>" class="regular-text" type="text" autocomplete="off" aria-describedby="user-description">
						<p id="user-description" class="description"><?php _e('Enter the username for access to the SMTP host.', 'vikmailsmtp'); ?></p>
					</td>
				</tr>

				<tr class="vikwp-hidden" style="display: <?php echo $auth_enabled ? 'table-row' : 'none'; ?>;">
					<th scope="row">
						<label for="pass"><?php _e('SMTP Password', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<div class="wp-pwd">
							<span class="password-input-wrapper">
								<input name="pass" id="pass" value="<?php echo str_repeat('&bull;', strlen(esc_attr($current_smtppwd))); ?>" class="regular-text" type="text" autocomplete="off" aria-describedby="pass-description">
							</span>
							<button type="button" class="button wp-hide-pw" id="togglepwd">
								<span class="dashicons dashicons-visibility"></span>
								<span class="text" data-showlbl="<?php _e('Show', 'vikmailsmtp'); ?>" data-hidelbl="<?php _e('Hide', 'vikmailsmtp'); ?>"><?php _e('Show', 'vikmailsmtp'); ?></span>
							</button>
						</div>
						<p id="pass-description" class="description"><?php _e('Enter the password for the SMTP host.', 'vikmailsmtp'); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="sender"><?php _e('Force Sender', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<input name="sender" id="sender" value="<?php echo esc_attr(get_option('vikmailsmtp_sender', '')); ?>" class="regular-text" type="text" autocomplete="off" aria-describedby="sender-description">
						<p id="sender-description" class="description"><?php _e('The From Name and From Address info can be forced to a specific value if your SMTP service requires so. Otherwise WP will use the default site information as sender. Correct syntax: Your Name &lt;your@email.com&gt;', 'vikmailsmtp'); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="logging"><?php _e('Enable Logs', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<input name="logging" id="logging" value="1" type="checkbox" aria-describedby="logging-description" <?php checked($logging_enabled); ?>>
						<p id="logging-description" class="description"><?php _e('Keep track of the results for the last email messages sent.', 'vikmailsmtp'); ?></p>
					</td>
				</tr>

				<tr class="vikwp-testmail" style="display: <?php echo !$empty_params ? 'table-row' : 'none'; ?>;">
					<th scope="row">
						<label for="pass"><?php _e('Test Configuration', 'vikmailsmtp'); ?></label>
					</th>
					<td>
						<input name="testmail" id="testmail" class="button button-secondary" value="<?php _e('Send Test Mail', 'vikmailsmtp'); ?>" type="submit" onclick="return vikwpCheckTestMail();" aria-describedby="testmail-description">
						<p id="testmail-description" class="description"><?php echo sprintf(__('Test the current configuration by sending a Test Mail to %s.', 'vikmailsmtp'), $user_email); ?></p>
					</td>
				</tr>

			</tbody>

		</table>

		<p class="submit">
			<input name="savesettings" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'vikmailsmtp'); ?>" type="submit">
		</p>

		<?php
		// print nonce fields for later validation when POST data is submitted
		wp_nonce_field('settings.save');
		?>

	</form>

</div>

<script type="text/javascript">
var testemail_listen = false;
function vikwpToggleAuth(is_on) {
	jQuery('.vikwp-hidden').toggle();
}
jQuery(document).ready(function() {
	/** disable test mail function when settings change **/
	jQuery('#vikmailsmtp_form input, #vikmailsmtp_form select').change(function() {
		if (!testemail_listen || jQuery(this).attr('name') == 'logging') {
			return;
		}
		jQuery('.vikwp-testmail').hide();
	});
	/** prevent form submission when hitting enter on an input field **/
	jQuery(window).keydown(function(event) {
		if (event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});
	/** Update password field to avoid Safari from auto-filling it **/
	setTimeout(function() {
		jQuery('#user').val('<?php echo $current_smtpuser; ?>');
		jQuery('#pass').attr('type', 'password').val('<?php echo esc_attr($current_smtppwd); ?>');
	}, 500);
	setTimeout(function() {
		testemail_listen = true;
	}, 750);
	/** Toggle password field **/
	jQuery('#togglepwd').click(function() {
		var showpwd = jQuery(this).find('.dashicons').hasClass('dashicons-visibility');
		if (showpwd) {
			jQuery('#pass').attr('type', 'text');
			jQuery(this).find('.dashicons').addClass('dashicons-hidden').removeClass('dashicons-visibility');
			jQuery(this).find('.text').text(jQuery(this).find('.text').attr('data-hidelbl'));
		} else {
			jQuery('#pass').attr('type', 'password');
			jQuery(this).find('.dashicons').addClass('dashicons-visibility').removeClass('dashicons-hidden');
			jQuery(this).find('.text').text(jQuery(this).find('.text').attr('data-showlbl'));
		}
	});
});
</script>
