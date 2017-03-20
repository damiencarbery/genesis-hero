<?php
/**
 * Plugin Name: Genesis Hero
 * Version: 0.1.0
 * Plugin URI: http://www.seothemes.net/
 * Description: Adds a hero section to Genesis child themes.
 * Author: Seo Themes
 * Author URI: http://www.seothemes.net/
 * Requires at least: 4.0
 * Tested up to: 4.7.3
 *
 * Text Domain: genesis-hero
 * Domain Path: /lang/
 *
 * @package SeoThemes/GenesisHero
 * @author Seo Themes
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once( 'includes/class-genesis-hero.php' );
require_once( 'includes/class-genesis-hero-settings.php' );
require_once( 'includes/class-genesis-hero-section.php' );

// Load plugin libraries.
require_once( 'includes/lib/class-genesis-hero-admin-api.php' );

/**
 * Returns the main instance of Genesis_Hero to prevent the need to use globals.
 *
 * @since  0.1.0
 * @return object Genesis_Hero
 */
function genesis_hero () {
	$instance = Genesis_Hero::instance( __FILE__, '0.1.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Genesis_Hero_Settings::instance( $instance );
	}

	return $instance;
}

genesis_hero();
