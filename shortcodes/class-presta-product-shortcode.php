<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/PSWebServiceLibrary.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/PSProduct.php';

class PrestaProductShortcode {
	/** @var  PrestaShopWebservice */
	private $prestashop;

	function __construct() {
		add_shortcode('prestashop-products', array(&$this, 'handle_shortcode'));
		$this->prestashop = new PrestaShopWebservice(PRESTASHOP_URL, PRESTASHOP_WEBSERVICE_KEY, false);
	}

	function handle_shortcode($atts) {
		if(!defined('PRESTASHOP_URL') || !defined('PRESTASHOP_WEBSERVICE_KEY')) {
			return '<pre style="color: red;">' . __('You must define PRESTASHOP_URL and PRESTASHOP_WEBSERVICE_KEY env to use this plugin.', 'pps') . '</pre>';
		}

		$atts = shortcode_atts(array(
			'id' => '',
			'lang' => defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : 'en'
		), $atts);


		if($atts['id']) {
			echo '<pre>';
			try {
				$product = new PSProduct( $atts['id'], $this->prestashop, $atts['lang'] );
				var_dump( $product->toArray() );
			} catch(PrestaShopWebserviceException $e) {
				return $e->getMessage();
			}
			echo '</pre>';
			exit;
		}

		return 'super';
	}
}
