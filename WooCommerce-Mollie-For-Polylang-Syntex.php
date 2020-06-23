<?php
/**
 * Plugin Name:     WooCommerce Mollie For Polylang Syntex
 * Plugin URI:      https://www.mollie.com
 * Description:     Integration between Mollie Payments for WooCommerce and Polylang by Syntex
 * Version:         0.1.0
 * Author:          Mollie
 * Author URI:      https://www.mollie.com
 * Requires at least: 3.8
 * Tested up to: 5.3
 * Text Domain:     WooCommerce-Mollie-For-Polylang-Syntex
 * Domain Path:     /languages
 * License: GPLv2 or later
 * WC requires at least: 2.2.0
 * WC tested up to: 4.0
 *
 * @package         WooCommerce_Mollie_For_Polylang_Syntex
 */


add_filter(
	'mollie_for_polylang_get_correct_url',
	'mollieForPolylangGetSiteUrlWithLanguage',
	10,
	1
);

/**
 * Check if any multi language plugins are enabled and return the correct site url.
 *
 * @return string
 */
function mollieForPolylangGetSiteUrlWithLanguage($returnUrl)
{
	/**
	 * function is_plugin_active() is not available. Lets include it to use it.
	 */
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');

	$langUrl = get_home_url();
	$polylang_fallback = false;
	if (is_plugin_active('polylang/polylang.php')) {
		$lang = PLL()->model->get_language(pll_current_language());

		if (empty ($lang->search_url)) {
			$polylang_fallback = true;
		} else {
			$polylang_url = $lang->search_url;
			$langUrl = str_replace($langUrl, $polylang_url, $returnUrl);
		}
	}

	if ($polylang_fallback == true || is_plugin_active('mlang/mlang.php')
		|| is_plugin_active('mlanguage/mlanguage.php')
	) {
		$slug = get_bloginfo('language');
		$pos = strpos($slug, '-');
		if ($pos !== false) {
			$slug = substr($slug, 0, $pos);
		}
		$slug = '/' . $slug;
		$langUrl = str_replace($langUrl, $langUrl . $slug, $langUrl);
	}

	$returnUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $langUrl);

	return $returnUrl;
}
