<?php

class PSProduct {
	private $productId;
	/**
	 * @var PrestaShopWebservice
	 */
	private $webservice;
	private $productXML;

	private $psCurrentLanguageId = -1;
	private $psCurrentLanguageCode = '';
	private $wp_language;
	private $category_link_rewrite;

	private $id;
	private $name;
	private $url;
	private $description_short;
	private $description;
	private $default_image_url;
	private $slug;

	function __construct($productId, PrestaShopWebservice $webservice, $default_language) {
		$this->productId = $productId;
		$this->webservice = $webservice;
		$this->wp_language = $default_language;

		$xml = $this->webservice->get(array(
			'resource' => 'products',
			'id' => $productId
		));

		$this->productXML = $xml->product[0];
		$this->psCurrentLanguageId = $this->_getPsCurrentLanguageId( $this->productXML );
	}

	public function getId() {
		if(!$this->id) {
			$this->id = (int)  $this->productXML->id;
		}

		return $this->id;
	}

	public function getName() {
		if(!$this->name) {
			$this->name = (string) $this->productXML->xpath('//name/language[@id="' . $this->psCurrentLanguageId . '"]')[0];
		}

		return $this->name;
	}

	public function getUrl() {
		if(!$this->url) {
//			$this->url = PRESTASHOP_URL
//				. '/' . $this->psCurrentLanguageCode
//				. '/' . $this->_getCategoryLinkRewrite()
//				. '/' . $this->getId()
//				. '-' . $this->getSlug()
//				. '.html';

			$this->url = PRESTASHOP_URL . '/index.php?controller=product&id_product=' . $this->getId();
		}

		return $this->url;
	}

	public function getDescription() {
		if(!$this->description) {
			$this->description = $this->productXML->xpath('//description/language[@id="' . $this->psCurrentLanguageId . '"]')[0]->asXML();
		}

		return $this->description;
	}

	public function getDescriptionShort() {
		if(!$this->description_short) {
			$this->description_short = (string) $this->productXML->xpath('//description_short/language[@id="' . $this->psCurrentLanguageId . '"]')[0];
		}

		return $this->description_short;
	}

	public function getDefaultImageUrl() {
		if(!$this->default_image_url) {
			$url = $this->productXML->id_default_image->attributes('xlink', TRUE)->href;
			$this->default_image_url = $url . '&ws_key=' . PRESTASHOP_WEBSERVICE_KEY;
		}

		return $this->default_image_url;
	}

	public function getSlug() {
		if(!$this->slug) {
			$this->slug = $this->productXML->xpath('//link_rewrite/language[@id="' . $this->psCurrentLanguageId . '"]')[0]->asXML();
		}

		return $this->slug;
	}


	private function _getPsCurrentLanguageId($productXML) {
		if($this->psCurrentLanguageId < 0) {
			$xmlLanguages = $productXML->meta_keywords->children();

			foreach ( $xmlLanguages as $key => $lang ) {
				$langId = $lang['id'];

				$xml = $this->webservice->get(array(
					'resource' => 'languages',
					'id'       => $langId
				));
				$iso_code = $xml->language[0]->iso_code;

				// if we find no correct language and no english, use first language found
				if($key == 0) {
					$this->psCurrentLanguageCode = $iso_code;
				}

				// if iso code is english and no correct language is found, set it as default.
				// it will be overriden by correct language id after
				if($iso_code == 'en' && $this->psCurrentLanguageId < 0) {
					$this->psCurrentLanguageId = $langId;
					$this->psCurrentLanguageCode = $iso_code;
				}
				// if we find a language that fits
				if($iso_code == $this->wp_language) {
					$this->psCurrentLanguageId = $langId;
					$this->psCurrentLanguageCode = $iso_code;
					return $this->psCurrentLanguageId;
				}
			}

			// if no correct language is found and there's no english,
			// set psCurrentLanguageId to first language id found in API.
			if($this->psCurrentLanguageId < 0) {
				$this->psCurrentLanguageId = $xmlLanguages[0]['id'];
			}
		}

		return $this->psCurrentLanguageId;
	}

	private function _getCategoryLinkRewrite() {
		if(!$this->category_link_rewrite) {
			$categoryId = $this->productXML->associations[0]->categories->category->id;
			if($categoryId) {
				$xml = $this->webservice->get(array(
					'resource' => 'categories',
					'id' => $categoryId
				));

				$this->category_link_rewrite = $xml->xpath('//link_rewrite/language[@id="' . (int) $this->psCurrentLanguageId . '"]')[0]->asXML();
			}
		}

		return $this->category_link_rewrite;
	}


	public function toArray() {
		return array(
			'id' => $this->getId(),
			'name' => $this->getName(),
			'url' => $this->getUrl(),
			'description_short' => $this->getDescriptionShort(),
			'description' => $this->getDescription(),
			'default_image_url' => $this->getDefaultImageUrl()
		);
	}
}