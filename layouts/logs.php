<?php
/**
 * @package     VikMailSMTP
 * @subpackage  layouts.logs
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

if (isset($_REQUEST['action']) && current_user_can('manage_options')) {
	// validate nonce with the action in query string
	check_admin_referer('logs.'.$_REQUEST['action']);
	
	if ($_REQUEST['action'] == 'flush') {
		VikMailSMTPLogger::flushLogs();
	}
	if ($_REQUEST['action'] == 'reset') {
		VikMailSMTPInstaller::reset();
		VikMailSMTPBuilder::displayMessage('settings_reset');
	}
}

$logs = VikMailSMTPLogger::getLogsList();

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e('SMTP Mailer Logs', 'vikmailsmtp'); ?></h1>
<?php
if (count($logs)) {
?>
	<a href="<?php echo wp_nonce_url(admin_url('options-general.php?page=vikmailsmtp&p=logs&action=flush'), 'logs.flush'); ?>" class="page-title-action" onclick="return confirm('<?php _e('Do you want to empty the logs?', 'vikmailsmtp'); ?>');"><?php _e('Flush Logs', 'vikmailsmtp'); ?></a>
<?php
}
?>
	<a href="<?php echo wp_nonce_url(admin_url('options-general.php?page=vikmailsmtp&p=logs&action=reset'), 'logs.reset'); ?>" class="page-title-action" onclick="return confirm('<?php _e('The plugin will stop working until you reconfigure it.', 'vikmailsmtp'); ?>');"><?php _e('Reset Settings', 'vikmailsmtp'); ?></a>
	<hr class="wp-header-end" />
	<br /><br />
	<table class="wp-list-table widefat fixed striped" width="100%" cellspacing="0" cellpadding="4" border="0">
		<thead>
			<tr>
				<th class="manage-column left" style="text-align: left;" width="10%"><?php _e('Identifier', 'vikmailsmtp'); ?></th>
				<th class="manage-column left" style="text-align: left;" width="10%"><?php _e('Date', 'vikmailsmtp'); ?></th>
				<th class="manage-column left" style="text-align: left;" width="20%"><?php _e('Subject', 'vikmailsmtp'); ?></th>
				<th class="manage-column left" style="text-align: left;" width="20%"><?php _e('Recipients', 'vikmailsmtp'); ?></th>
				<th class="manage-column left" style="text-align: left;" width="20%"><?php _e('Sender', 'vikmailsmtp'); ?></th>
				<th class="manage-column center" style="text-align: center;" width="20%"><?php _e('Result', 'vikmailsmtp'); ?></th>
			</tr>
		</thead>		
		<tbody>
		<?php
		foreach ($logs as $log) {
			$subject = '';
			$recipient = '';
			$sender = '';
			if (is_object($log->log[0]->content)) {
				$subject = $log->log[0]->content->subject;
				// subject could be UTF8-encoded
				if (preg_match("/=\?UTF-8\?B\?(.*?)\?=/", $subject, $matches)) {
					$subject = base64_decode($matches[1]);
				}
				//
				$recipient = $log->log[0]->content->recipient;
				$sender = $log->log[0]->content->sender;
			}
			$result = '<span class="dashicons dashicons-yes" style="color: green;"></span>';
			if (isset($log->log[1]) && is_string($log->log[1]->content)) {
				$result = '<span class="vms-log-'.$log->log[1]->type.'">'.$log->log[1]->content.'</span>';
			}
			?>
			<tr class="row">
				<td><?php echo $log->id; ?></td>
				<td><?php echo $log->date; ?></td>
				<td><?php echo $subject; ?></td>
				<td><?php echo $recipient; ?></td>
				<td><?php echo $sender; ?></td>
				<td style="text-align: center;"><?php echo $result; ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
</div>