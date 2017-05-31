<?php
/**
 * Fantastico Related Functionality
 * Last Changed: $LastChangedDate: 2017-05-25 09:04:25 -0400 (Thu, 25 May 2017) $
 * @author detain
 * @version $Revision: 24748 $
 * @copyright 2017
 * @package MyAdmin
 * @category Licenses
 */

use Detain\Fantastico\Fantastico;

/**
 * reusable_fantastico()
 * @return void
 */
function reusable_fantastico() {
	page_title('ReUsable Fantastico Licenses');
	//ini_set('display_errors', 'on');
	//echo '<pre>';
	if ($GLOBALS['tf']->ima == 'admin') {
		$module = 'licenses';
		$service_types = run_event('get_service_types', false, $module);
		$db = get_module_db($module);
		$settings = get_module_settings($module);
		$fantastico = new Fantastico(FANTASTICO_USERNAME, FANTASTICO_PASSWORD);
		$ipdetails = get_fantastico_licenses();
		$ips = $fantastico->getIpList(Fantastico::ALL_TYPES);
		$frequency = 1;
		if (isset($GLOBALS['tf']->variables->request['add']) && $GLOBALS['tf']->variables->request['add'] == 1) {
			$ip = $db->real_escape($GLOBALS['tf']->variables->request['ip']);
			if (in_array($ip, $ips)) {
				$db->query("select * from {$settings['TABLE']} left join services on {$settings['PREFIX']}_type=services_id where services_module='{$module}' and services_category=".SERVICE_TYPES_FANTASTICO." and license_ip='{$ip}'", __LINE__, __FILE__);
				if ($db->num_rows() == 0) {
					$result = $fantastico->getIpDetails($ip);
					if ($result['isVPS'] == 'Yes')
						$type = 5013;
					else
						$type = 5003;
					$service_cost = $service_types[$type]['services_cost'];
					$db->query(make_insert_query($settings['TABLE'], array(
						$settings['PREFIX'] . '_id' => null,
						$settings['PREFIX'] . '_type' => $type,
						$settings['PREFIX'] . '_custid' => 8,
						$settings['PREFIX'] . '_cost' => $service_cost,
						$settings['PREFIX'] . '_frequency' => $frequency,
						$settings['PREFIX'] . '_order_date' => mysql_now(),
						$settings['PREFIX'] . '_ip' => $ip,
						$settings['PREFIX'] . '_status' => 'canceled',
						$settings['PREFIX'] . '_invoice' => 0,
						$settings['PREFIX'] . '_coupon' => 0,
						$settings['PREFIX'] . '_extra' => '',
						$settings['PREFIX'] . '_hostname' => '',
					)), __LINE__, __FILE__);
				} else {
					//var_dump($fantastico->getIpDetails($ip));
					dialog('Error', 'IP Already Licensed For Fantastico In Our DB');
				}
			} else {
				dialog('Error', 'Fantastico does not report this as an IP licensed by you');
			}
		}
		add_output('<h3>Re-Usable Fantastico Licenses</h3>');
		$table = new TFTable;
		$table->add_hidden('add', 1);
		$table->set_title('Add ReUsable Fantastico IP');
		$table->add_field('Re-Usable IP');
		$table->add_field($table->make_input('ip', '', 20));
		$table->add_field($table->make_submit('Add'));
		$table->add_row();
		add_output($table->get_table());
		add_output(render_form('reusable_fantastico'));
	}
}