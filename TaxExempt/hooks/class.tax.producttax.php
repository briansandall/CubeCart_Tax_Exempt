<?php
if(!defined('CC_INI_SET')) die('Access Denied');
$is_tax_exempt = false;
if (isset($GLOBALS['tmp']['tax_exempt']['is_tax_exempt'])) {
	// Hook has already been called during current process - use previously determined value
	$is_tax_exempt = ($GLOBALS['tmp']['tax_exempt']['is_tax_exempt']);
} else {
	// Check tax exempt group membership
	$settings = $GLOBALS['config']->get('TaxExempt');
	if ($settings['status'] && $GLOBALS['user']->is()) {
		$memberships = $GLOBALS['user']->getMemberships($GLOBALS['user']->getId());
		if (is_array($memberships)) {
			foreach ($memberships as $membership) {
				if (in_array($membership['group_id'], $settings['groups'])) {
					$is_tax_exempt = true;
					break;
				}
			}
		}
	}
	// Check certificate expiration date
	if ($is_tax_exempt) {
		$expiration = $GLOBALS['db']->select('CubeCart_customer', 'tax_exempt_expiration', array('customer_id'=>$GLOBALS['user']->getId()), false);
		// CubeCart stores DATE fields as '0000-00-00' instead of NULL when a form gets saved with an empty date input
		if (!empty($expiration)) {
			$expiration = $expiration[0]['tax_exempt_expiration'];
			$expiration = ((int)(str_replace('-', '', $expiration)) > 0) ? $expiration : null;
		}
		if (!empty($expiration)) {
			$expiration = (new \DateTime($expiration))->setTime(0,0,0);
			$today = (new \DateTime())->setTime(0,0,0);
			$interval = $today->diff($expiration);
			if ($interval->invert == 1 || $interval->days < 1) {
				$is_tax_exempt = false;
				// Notify customer that their certificate has expired
				if (empty($_GET['a']) || in_array(filter_input(INPUT_GET, '_a'), array('basket','checkout','confirm'))) {
					$GLOBALS['gui']->setInfo($GLOBALS['language']->tax_exempt['certificate_expired']);
				}
				if (in_array(filter_input(INPUT_GET, '_a'), array('basket','checkout','confirm'))) {
					$GLOBALS['gui']->setInfo($GLOBALS['language']->tax_exempt['certificate_expired_proceed']);
				}
			}
		}
	}
	// Set global variable to skip processing for subsequent calls
	$GLOBALS['tmp']['tax_exempt']['is_tax_exempt'] = $is_tax_exempt;
}
// Set $state to invalid tax zone to prevent addition of taxes
if ($is_tax_exempt) {
	$state = -1;
}
