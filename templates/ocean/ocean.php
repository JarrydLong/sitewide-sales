<?php
namespace Sitewide_Sales\templates\ocean;

/**
 * Ocean Template for Sitewide Sales
 *
 */

/**
 * Add template to list.
 */
function swsale_templates( $templates ) {
	$templates['ocean'] = 'Ocean';

	return $templates;
}
add_filter( 'swsale_templates', __NAMESPACE__ . '\swsale_templates' );

/**
 * Load our landing page and banner CSS/JS if needed.
 */
function wp_enqueue_scripts() {
	// Load landing page CSS if needed.
	if ( swsales_landing_page_template() == 'ocean' ) {
		wp_register_style( 'swsales_ocean_landing_page', plugins_url( 'templates/ocean/landing-page.css', SWSALES_BASENAME ), null, SWSALES_VERSION );
		wp_enqueue_style( 'swsales_ocean_landing_page' ); 
	}

	// Load banner CSS if needed.
	if ( swsales_banner_template() == 'ocean' ) {
		wp_register_style( 'swsales_ocean_banner', plugins_url( 'templates/ocean/banner.css', SWSALES_BASENAME ), null, SWSALES_VERSION );
		wp_enqueue_style( 'swsales_ocean_banner' );
	} 
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\wp_enqueue_scripts' );

/**
 * Filter to add the ocean template wrapper for this banner template.
 *
 */
function swsales_banner_content_ocean( $content ) {
	$content_before = '<div id="swsales-banner-wrap-ocean" class="swsales-banner-wrap">';
	$content_after = '</div>';

	$content = $content_before . $content . $content_after;

	return $content;
}
add_action( 'swsales_banner_content_ocean', __NAMESPACE__ . '\swsales_banner_content_ocean' );

/**
 * Filter to add the ocean template wrapper for this landing page template.
 *
 */
function swsales_landing_page_content_ocean( $content ) {
	$content_before = '<div id="swsales-landing-page-wrap-ocean" class="swsales-landing-page-wrap">';
	$content_after = '</div>';

	$content = $content_before . $content . $content_after;

	return $content;
}
add_action( 'swsales_landing_page_content_ocean', __NAMESPACE__ . '\swsales_landing_page_content_ocean' );
