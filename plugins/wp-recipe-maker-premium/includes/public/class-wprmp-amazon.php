<?php
/**
 * Responsible for handling anything Amazon related.
 *
 * @link       https://bootstrapped.ventures
 * @since      9.1.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 */

/**
 * Responsible for handling anything Amazon related.
 *
 * @since      9.1.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ItemsResult;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\ApiException;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\Configuration;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResponse;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use BootstrappedVentures\WPRecipeMaker\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResponse;

class WPRMP_Amazon {

	/**
	 * Get affiliate link to a specific product without going through the API.
	 * https://affiliate-program.amazon.com/help/node/topic/GP38PJ6EUR6PFBEC
	 *
	 * @since    9.1.0
	 */
	public static function get_noapi_affiliate_link( $asin ) {
		$link = '';

		if ( $asin ) {
			$tag = trim( WPRM_Settings::get( 'amazon_partner_tag' ) );

			if ( $tag ) {
				$store = self::get_store();
				$domain = str_ireplace( 'webservices.', 'www.', $store['host'] );

				$link = 'https://' . $domain . '/dp/' . $asin . '/ref=nosim?tag=' . $tag;
			}
		}

		return $link;
	}

	/**
	 * Search for products through the Amazon API.
	 *
	 * @since    9.1.0
	 */
	public static function search_products( $search ) {
		$result = array(
			'error' => false,
			'products' => array(),
		);

		$products = WPRMP_Amazon_Api::search_products( $search );

		// Add error to result or parse products.
		if ( is_wp_error( $products ) ) {
			if ( 'NoResults' !== $products->get_error_code() ) {
				$result['error'] = $products->get_error_data();
			}
		} else {
			$result['products'] = self::get_data_from_products( $products );
		}

		return $result;
	}

	/**
	 * Get products through the Amazon API.
	 *
	 * @since    9.1.0
	 */
	public static function get_products( $asins ) {
		$result = array(
			'error' => false,
			'products' => array(),
		);

		$products = WPRMP_Amazon_Api::get_products( $asins );

		// Add error to result or parse products.
		if ( is_wp_error( $products ) ) {
			if ( 'NoResults' !== $products->get_error_code() ) {
				$result['error'] = $products->get_error_data();
			}
		} else {
			$data = self::get_data_from_products( $products );

			$result['products'] = array();

			// Make sure all ASINs are set, even if product wasn't actually found.
			foreach ( $asins as $asin ) {
				// Default to false.
				$result['products'][ $asin ] = false;

				// Check if ASIN is set in data.
				$data_key = array_search( $asin, array_column( $data, 'asin' ) );

				if ( false !== $data_key ) {
					$result['products'][ $asin ] = $data[ $data_key ];
				}
			}
		}

		return $result;
	}

	/**
	 * Extract the data we want from the items Amazon returns.
	 *
	 * @since    9.1.0
	 */
	public static function get_data_from_products( $products ) {
		$data = array();

		if ( is_array( $products ) && ! is_wp_error( $products ) ) {
			foreach ( $products as $product ) {
				if ( $product ) {
					$asin = $product->getASIN();

					if ( $asin ) {
						$name = null !== $product->getItemInfo() && null !== $product->getItemInfo()->getTitle() && null !== $product->getItemInfo()->getTitle()->getDisplayValue() ? $product->getItemInfo()->getTitle()->getDisplayValue() : '';
						$link = $product->getDetailPageURL();
						$image = null !== $product->getImages() && null !== $product->getImages()->getPrimary() && null !== $product->getImages()->getPrimary()->getLarge() ? $product->getImages()->getPrimary()->getLarge()->getURL() : '';
						$price = null !== $product->getOffers() && null !== $product->getOffers()->getListings() && null !== $product->getOffers()->getListings()[0]->getPrice() ? $product->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() : '';

						$data[] = array(
							'asin' => $asin,
							'name' => $name,
							'link' => $link,
							'image' => $image,
							'price' => $price,
						);
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Get selected Amazon store.
	 *
	 * @since    9.1.0
	 */
	public static function get_store() {
		$all_stores = self::get_stores();
		$store = WPRM_Settings::get( 'amazon_store' );

		if ( ! isset( $all_stores[ $store ] ) ) {
			$store = 'united_states';
		}

		return $all_stores[ $store ];
	}

	/**
	 * Get all available Amazon stores.
	 *
	 * @since    9.1.0
	 */
	public static function get_stores() {
		include( WPRM_DIR . 'templates/settings/group-amazon.php' );

		if ( is_array( $amazon_stores ) ) {
			return $amazon_stores;
		}

		// Make sure default is always there.
		return array(
			'united_states' => array(
				'label' => 'United States',
				'host' => 'webservices.amazon.com',
				'region' => 'us-east-1',
			),
		);
	}
}
