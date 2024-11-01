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

$page = self::getVar('page');

?>
<div class="vikmailsmtp-menu-container">
	<div class="vikmailsmtp-menu-logo">
		<a href="https://vikwp.com" target="_blank"></a>
	</div>
	<div class="vikmailsmtp-menu-entry<?php echo $page == 'settings' ? ' vikmailsmtp-menu-entry-active' : ''; ?>">
		<a href="options-general.php?page=vikmailsmtp&p=settings"><i class="dashicons dashicons-admin-tools"></i> <?php _e('Settings', 'vikmailsmtp'); ?></a>
	</div>
	<div class="vikmailsmtp-menu-entry<?php echo $page == 'logs' ? ' vikmailsmtp-menu-entry-active' : ''; ?>">
		<a href="options-general.php?page=vikmailsmtp&p=logs"><i class="dashicons dashicons-email"></i> <?php _e('Logs', 'vikmailsmtp'); ?></a>
	</div>
	<div class="vikmailsmtp-menu-entry<?php echo $page == 'wizard' ? ' vikmailsmtp-menu-entry-active' : ''; ?>">
		<a href="options-general.php?page=vikmailsmtp&p=wizard"><i class="dashicons dashicons-sos"></i> <?php _e('Configuration Wizard', 'vikmailsmtp'); ?></a>
	</div>
</div>