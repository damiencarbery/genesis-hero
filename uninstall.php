<?php
/**
 * This file runs when the plugin is uninstalled (deleted).
 * This cleans up unused meta, options, etc. in the database.
 *
 * @package SeoThemes/GenesisHero
 */

// If plugin is not being uninstalled, exit (do nothing).
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
