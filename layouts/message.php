<?php
/**
 * @package     VikMailSMTP
 * @subpackage  layouts.message
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

$message = self::getVar('message');
$type = self::getVar('type');

switch ($message) {
	case 'settings_saved':
		$mess = __('Settings saved.', 'vikmailsmtp');
		break;
	case 'settings_reset':
		$mess = __('Settings reset.', 'vikmailsmtp');
		break;
	case 'settings_calculated':
		$mess = __('SMTP Settings saved successfully. Please review them and send a test email.', 'vikmailsmtp');
		break;
	case 'missing_data_calc':
		$mess = __('Missing data for calculating the settings. Please enter them manually.', 'vikmailsmtp');
		break;
	case 'invalid_email':
		$mess = __('Invalid email address.', 'vikmailsmtp');
		break;
	default:
		$mess = $message;
		break;
}

?>
<div class="notice is-dismissible notice-<?php echo $type; ?>"> 
	<p><strong><?php echo $mess; ?></strong></p>
</div>