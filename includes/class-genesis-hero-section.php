<?php
/**
 * This file contains the Hero Section class.
 *
 * @package SeoThemes/GenesisHero
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Hero Section.
 */
class Genesis_Hero_Section {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Add excerpts to page edit screen.
		$this->excerpts();

		// Output custom CSS.
		add_action( 'wp_head', array( $this, 'css' ), 99 );

		// Output backstretch JS.
		add_action( 'wp_head', array( $this, 'backstretch' ), 99 );

		// Add hero section to hook.
		add_action( 'genesis_setup', array( $this, 'hooks' ) );

		// Display hero section.
		add_action( 'genesis_after_header', array( $this, 'hero_section' ), 99 );

	}

	/**
	 * Add support for page excerpts.
	 */
	public function excerpts() {
		add_post_type_support( 'page', 'excerpt' );
	}

	/**
	 * Output CSS.
	 */
	public function css() {

		$text_color = get_option( 'genesis_hero_text_color' );
		$text_align = get_option( 'genesis_hero_text_align' );
		$overlay = get_option( 'genesis_hero_overlay_color' );
		$opacity = get_option( 'genesis_hero_overlay_opacity' );

		printf( '
		<style type="text/css" id="genesis-hero">
		.hero-section h1,
		.hero-section p,
		.hero-section .breadcrumb {
			color: %1$s;
			text-align: %2$s;
			float: %2$s;
		}
		.hero-section button,
		.hero-section .button {
			text-align: %2$s;
			float: %2$s;
		}
		.hero-section .overlay {
			background-color: %3$s;
			opacity: %4$s;
		}
		</style>', $text_color, $text_align, $overlay, $opacity );
	}

	/**
	 * Output backstretch JS.
	 */
	public function backstretch() {

		$default = wp_get_attachment_url( get_option( 'genesis_hero_default_image' ) );
		$featured = get_option( 'genesis_hero_featured_image' );

		if ( true == $default ) {
			$backstretch = printf( '<script>jQuery( document ).ready( function($) { $( ".hero-section" ).backstretch( "%s" ); } );</script>', esc_html( $default ) );
		}

		if ( true == $featured && get_the_post_thumbnail_url() ) {
			$backstretch = printf( '<script>jQuery( document ).ready( function($) { $( ".hero-section" ).backstretch( "%s" ); } );</script>', esc_html( get_the_post_thumbnail_url() ) );
		}

		return $backstretch;
	}

	/**
	 * Remove all titles.
	 *
	 * @access public
	 * @since  0.1.0
	 * @return void
	 */
	public function remove_titles() {
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
		remove_action( 'genesis_before_loop', 'genesis_do_posts_page_heading' );
		remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
		remove_action( 'genesis_before_loop', 'genesis_do_blog_template_heading' );
		remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
		remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
		remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );

		// Hide WooCommerce Page Title.
		add_filter( 'woocommerce_show_page_title' , function() {
			return false;
		} );
	}

	/**
	 * Opening markup.
	 */
	public function markup_open() {
		echo '<section class="hero-section" role="banner">';
		echo '<div class="wrap">';
	}

	/**
	 * Get the correct title.
	 */
	public function title() {

		$title = '';

		if ( class_exists( 'WooCommerce' ) && is_shop() ) {
			$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );

		} elseif ( 'posts' === get_option( 'show_on_front' ) && is_home() ) {
			$title = __( 'Latest Posts', 'genesis-hero' );

		} elseif ( is_front_page() && ! is_home() ) {
			$title = get_the_title( get_option( 'page_on_front' ) );

		} elseif ( is_home() ) {
			$title = genesis_do_posts_page_heading();

		} elseif ( is_author() ) {
			$title = get_the_author_meta( 'headline', (int) get_query_var( 'author' ) );

			if ( '' == $title && genesis_a11y( 'headings' ) ) {
				$title = get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) );
			}

		} elseif ( is_archive() || is_category() || is_tag() || is_tax() ) {
			$title = single_term_title( false, false );

		} elseif ( is_date() ) {
			$title = genesis_do_date_archive_title();

		} elseif ( is_page_template( 'page_blog.php' ) ) {
			$title = genesis_do_blog_template_heading();

		} elseif ( is_404() ) {
			$title = __( 'Page not found!', 'genesis-hero' );

		} else {
			$title = get_the_title();

		} // End if().

		// Add post titles back inside posts loop.
		if ( is_home() || is_archive() || is_category() || is_tag() || is_tax() ) {
			add_action( 'genesis_entry_header', 'genesis_do_post_title', 2 );
		}

		// Output the title.
		if ( $title ) {
			printf( '<h1 itemprop="headline">%s</h1>', esc_html( $title ) );
		}

	}

	/**
	 * Get the subtitle.
	 */
	public function subtitle() {

		$subtitle = '';

		if ( class_exists( 'WooCommerce' ) && is_shop() ) {
			$subtitle = get_the_excerpt( get_option( 'woocommerce_shop_page_id' ) );

		} elseif ( 'posts' === get_option( 'show_on_front' ) && is_home() ) {
			$subtitle = __( 'Showing the latest posts', 'genesis-hero' );

		} elseif ( is_author() ) {
			$subtitle = get_the_author_meta( 'intro_text', (int) get_query_var( 'author' ) );
			$subtitle = $subtitle ? apply_filters( 'genesis_author_intro_text_output', $subtitle ) : '';

		} elseif ( is_post_type_archive() ) {
			$subtitle = genesis_do_cpt_archive_title_description();

		} elseif ( is_archive() || is_category() || is_tag() || is_tax() ) {
			$subtitle = category_description();

		} elseif ( ! is_null( get_option( 'page_for_posts' ) ) && is_home() ) {
			$subtitle = esc_html( get_the_excerpt( get_option( 'page_for_posts' ) ) );

		} elseif ( has_excerpt() ) {
			$subtitle = get_the_excerpt();

		} elseif ( genesis_is_root_page() ) {
			$subtitle = esc_html( get_the_excerpt( get_option( 'page_on_front' ) ) );

		} else {
			$subtitle = genesis_do_breadcrumbs();

		} // End if().

		// Output the subtitle.
		if ( $subtitle ) {
			printf( '<p itemprop="description">%s</p>', wp_kses_post( $subtitle ) );
		}

	}

	/**
	 * Closing markup.
	 */
	public function markup_close() {

		$overlay = '';

		if ( true == get_option( 'genesis_hero_enable_overlay' ) ) {
			$overlay = '<div class="overlay"></div>';
		}

		echo '</div>' . $overlay . '</section>';
	}

	/**
	 * Add actions to hook.
	 *
	 * To remove a function simply use remove_action with the $hero global.
	 * e.g. To remove subtitles you would use:
	 * remove_action( 'genesis_hero', array( $hero, 'subtitle' ) );
	 */
	public function hooks() {

		// Add actions to genesis_hero hook.
		add_action( 'genesis_hero', array( $this, 'remove_titles' ) );
		add_action( 'genesis_hero', array( $this, 'markup_open' ) );
		add_action( 'genesis_hero', array( $this, 'title' ) );
		add_action( 'genesis_hero', array( $this, 'subtitle' ) );
		add_action( 'genesis_hero', array( $this, 'markup_close' ) );
	}

	/**
	 * Display Hero.
	 */
	public function hero_section() {

		$enabled_on = get_option( 'genesis_hero_enable_hero_on' );

		if ( $enabled_on ) {

			foreach ( $enabled_on as $type => $value ) {
				$enabled[ $value ] = $value;
			}

			$show = false;

			if ( ! empty( $enabled['front_page'] ) && is_front_page() ) {
				$show = true;
			}

			if ( ! empty( $enabled['posts_page'] ) && is_home() ) {
				$show = true;
			}

			if ( ! empty( $enabled['single_posts'] ) && is_singular( 'post' ) ) {
				$show = true;
			}

			if ( ! empty( $enabled['pages'] ) && is_singular( 'page' ) && ! is_front_page() && ! is_page_template( 'page_blog.php' ) ) {
				$show = true;
			}

			if ( ! empty( $enabled['archives'] ) && is_archive() || is_category() || is_tag() || is_tax() ) {
				$show = true;
			}

			if ( ! empty( $enabled['author'] ) && is_author() ) {
				$show = true;
			}

			if ( ! empty( $enabled['404_page'] ) && is_404() ) {
				$show = true;
			}

			if ( ! empty( $enabled['attachment'] ) && is_attachment() ) {
				$show = true;
			}

			if ( true == $show ) {

				// Run hook.
			   	do_action( 'genesis_hero' );
			}
		}
	}
}

$hero = new Genesis_Hero_Section();
