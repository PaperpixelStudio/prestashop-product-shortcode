<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/PSWebServiceLibrary.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/PSProduct.php';

class PrestaProductShortcode {
	/** @var  PrestaShopWebservice */
	private $webservice;

	function __construct() {
		add_shortcode('prestashop-products', array(&$this, 'handle_shortcode'));
		$this->webservice = new PrestaShopWebservice(PRESTASHOP_URL, PRESTASHOP_WEBSERVICE_KEY, false);
	}

	function handle_shortcode($atts) {
		if(!defined('PRESTASHOP_URL') || !defined('PRESTASHOP_WEBSERVICE_KEY')) {
			return $this->_formatError(__('You must define PRESTASHOP_URL and PRESTASHOP_WEBSERVICE_KEY env to use this plugin.', 'pps'));
		}

		$html= '';

		$atts = shortcode_atts(array(
			'id' => '',
			'ids' => '',
			'language' => defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : 'en',
			'limit' => -1,
			'sort' => 'DESC'
		), $atts);


		if($atts['ids']) {
			try {
				$ids = explode(',', $atts['ids']);
				if(count($ids) == 1) {
					$product = new PSProduct( $atts['ids'][0], $this->webservice, $atts['language'] );
					$html = "<div class='prestashop-product-single'>" . $this->_formatProductItem($product) . "</div>";
					return apply_filters('pps_product_single_html', $html, $product);
				} elseif(count($ids) > 1) {
					$products = array();
					foreach($ids as $id) {
						$products[] = new PSProduct($id, $this->webservice, $atts['language']);
					}
					$html = $this->_formatProductList($products);
					return apply_filters('pps_product_list_html', $html, $products);
				}
			} catch(PrestaShopWebserviceException $e) {
				return $this->_formatError($e->getMessage());
			}
		} else {
			$products = array();

			try {
				$params = array(
					'resource' => 'products',
					'sort' => 'id_' . $atts['sort']
				);

				if($atts['limit'] > -1) {
					$params['limit'] = $atts['limit'];
				}

				$xml = $this->webservice->get($params);

				foreach($xml->products[0]->children() as $product) {
					$productId = (int) $product['id'];
					$products[] = new PSProduct($productId, $this->webservice, $atts['language']);
				}

				$html = $this->_formatProductList( $products );
				return apply_filters('pps_product_list_html', $html, $products);
			} catch(PrestaShopWebserviceException $e) {
				return $this->_formatError($e->getMessage());
			}
		}
	}


	private function _formatProductList(array $products) {
		$html = "<ul class='prestashop-product-list'>";
		foreach($products as $product) {
			$html .= "<li>".$this->_formatProductItem($product)."</li>";
		}
		$html .= "</ul>";

		return $html;
	}

	private function _formatProductItem(PSProduct $product) {
		$html  = "<h4>".strip_tags($product->getName())."</h4>";
		$html .= "<p>".strip_tags($product->getDescriptionShort())."</p>";

		return $html;
	}

	private function _formatError($errorMessage) {
		return "<pre>" . $errorMessage . "</pre>";
	}
}
