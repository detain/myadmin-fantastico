<?php

namespace Detain\MyAdminFantastico;

use Symfony\Component\EventDispatcher\GenericEvent;

class Settings {

	public function __construct() {
	}

	public static function update(GenericEvent $event) {
		// will be executed when the licenses.settings event is dispatched
		$settings = $event->getSubject();
		$settings->add_text_setting('apisettings', 'fantastico_username', 'Fantastico Username:', 'Fantastico Username', $settings->get_setting('FANTASTICO_USERNAME'));
		$settings->add_text_setting('apisettings', 'fantastico_password', 'Fantastico Password:', 'Fantastico Password', $settings->get_setting('FANTASTICO_PASSWORD'));
		$settings->add_dropdown_setting('stock', 'outofstock_licenses_fantastico', 'Out Of Stock Fantastico Licenses', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_LICENSES_FANTASTICO'), array('0', '1'), array('No', 'Yes', ));
	}

}