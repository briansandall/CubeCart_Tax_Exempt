<?php
if(!defined('CC_INI_SET')) die('Access Denied');
$settings = $GLOBALS['config']->get('TaxExempt');
if ($settings['status'] && !empty($memberships)) {
	$is_tax_exempt = false;
	if (is_array($memberships)) {
		foreach ($memberships as $membership) {
			if (in_array($membership['group_id'], $settings['groups'])) {
				$is_tax_exempt = true;
				break;
			}
		}
	}
	if ($is_tax_exempt) {
		$GLOBALS['main']->addTabControl($GLOBALS['language']->tax_exempt['title_customer_tab'], 'tax_exempt-tab');
		$GLOBALS['hook_tab_content'][] = 'modules/plugins/TaxExempt/skin/admin/admin.customer.tabs.tpl';
		$GLOBALS['smarty']->assign('HOOK_TAB_CONTENT', $GLOBALS['hook_tab_content']);
		// CubeCart stores DATE fields as '0000-00-00' instead of NULL when a form gets saved with an empty date input
		if (!empty($customer['tax_exempt_expiration'])) {
			$customer['tax_exempt_expiration'] = ((int)(str_replace('-', '', $customer['tax_exempt_expiration'])) > 0) ? $customer['tax_exempt_expiration'] : "";
			$GLOBALS['smarty']->assign('CUSTOMER', $customer); // update smarty data
		}
	}
}
