<?php
if(!defined('CC_INI_SET')) die('Access Denied');
$tax_exempt_db = array(
	array('table'=>'CubeCart_customer','column'=>'tax_exempt_expiration','data_type'=>'DATE','definition'=>'DATE NULL DEFAULT NULL','comment'=>'Added by TaxExempt plugin'),
);
$GLOBALS['smarty']->assign('TAXEXEMPT_DB', $tax_exempt_db);

// Delay module->fetch() to allow assigning additional SMARTY variables
$module = new Module(__FILE__, $_GET['module'], 'admin/index.tpl', true, false);

// Check `CubeCart_customer` table for compatibility; add column if missing
if (!empty($_POST['tax_exempt_uninstall'])) {
	if (!empty($module->_settings['db_install'])) {
		foreach ($tax_exempt_db as $data) {
			$table = $GLOBALS['db']->sqlSafe($GLOBALS['config']->get('config', 'dbprefix').$data['table']);
			$GLOBALS['db']->misc("ALTER TABLE `$table` DROP COLUMN `$data[column]`");
		}
		$module->_settings['db_install'] = 0;
		$GLOBALS['main']->setACPNotify($GLOBALS['language']->tax_exempt['db_uninstall']);
	}
	$module->_settings['status'] = 0;
	$module->module_settings_save($module->_settings);
	$GLOBALS['smarty']->assign('MODULE', $module->_settings); // update smarty data
} elseif (empty($module->_settings['db_install']) && !empty($_POST['module']['status'])) {
	$error = false;
	foreach ($tax_exempt_db as $key => $data) {
		$table = $GLOBALS['db']->sqlSafe($GLOBALS['config']->get('config', 'dbprefix').$data['table']);
		$result = $GLOBALS['db']->misc("SELECT COLUMN_COMMENT, DATA_TYPE FROM INFORMATION_SCHEMA.columns WHERE table_name='$table' AND COLUMN_NAME='$data[column]'");
		if (empty($result)) {
			$tax_exempt_db[$key]['add'] = true; // flag to indicate column needs to be added
		} elseif (strcasecmp($result[0]['DATA_TYPE'], $data['data_type']) !== 0 || strcasecmp($result[0]['COLUMN_COMMENT'], $data['comment']) !== 0) {
			$error = true;
			break;
		}
	}
	if ($error) {
		$GLOBALS['main']->setACPWarning($GLOBALS['language']->tax_exempt['db_error']);
		$GLOBALS['main']->setACPWarning($GLOBALS['language']->tax_exempt['db_error_instructions']);
	} else {
		foreach ($tax_exempt_db as $data) {
			if (!empty($data['add'])) {
				$table = $GLOBALS['db']->sqlSafe($GLOBALS['config']->get('config', 'dbprefix').$data['table']);
				$GLOBALS['db']->misc("ALTER TABLE `$table` ADD COLUMN `$data[column]` $data[definition] COMMENT '$data[comment]'");
			}
		}
		$module->_settings['db_install'] = 1;
		$module->module_settings_save($module->_settings);
		$GLOBALS['smarty']->assign('MODULE', $module->_settings); // update smarty data
		$GLOBALS['main']->setACPNotify($GLOBALS['language']->tax_exempt['db_install']);
	}
}
// Fetch customer groups
if (($groups = $GLOBALS['db']->select('CubeCart_customer_group', false, false, array('group_name' => 'ASC'))) !== false) {
	foreach ($GLOBALS['hooks']->load('admin.customer.group_list') as $hook) include $hook;
	$GLOBALS['smarty']->assign('CUSTOMER_GROUPS', $groups);
	$enabled_groups = (isset($module->_settings['groups']) ? $module->_settings['groups'] : array());
	if (!empty($enabled_groups)) {
		$enabled = array();
		foreach ($groups as $group) {
			if (in_array($group['group_id'], $enabled_groups)) {
				$enabled[] = $group;
			}
		}
		$GLOBALS['smarty']->assign('ENABLED_GROUPS', $enabled);
	}
}
$module->fetch();
$page_content = $module->display();
