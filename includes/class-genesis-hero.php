<?php
/**
 * This file contains the main Genesis Hero class.
 *
 * @package  SeoThemes/GenesisHero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Genesis Hero class.
 */
class Genesis_Hero {

	/**
	 * The single instance of Genesis_Hero.
	 *
	 * @var 	object
	 * @access  private
	 * @since 	0.1.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   0.1.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $_version;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '0.1.0' ) {
		$this->_version = $version;
		$this->_token = 'genesis_hero';

		// Load plugin environment variables.
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );

		// Load frontend JS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new Genesis_Hero_Admin_API();
		}

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

	} // End __construct()

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return void
	 */
	public function enqueue_styles() {

		if ( true == get_option( 'genesis_hero_output_css' ) ) {
			wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
			wp_enqueue_style( $this->_token . '-frontend' );
		}
	} // End enqueue_styles()

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function enqueue_scripts() {

		if ( true == get_option( 'genesis_hero_output_js' ) ) {
			wp_register_script( $this->_token . '-backstretch', esc_url( $this->assets_url ) . 'js/backstretch' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
			wp_enqueue_script( $this->_token . '-backstretch' );
		}

	} // End enqueue_scripts()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'genesis-hero', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
	    $domain = 'genesis-hero';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Main Genesis_Hero Instance
	 *
	 * Ensures only one instance of Genesis_Hero is loaded or can be loaded.
	 *
	 * @since 0.1.0
	 * @static
	 * @see Genesis_Hero()
	 * @return Main Genesis_Hero instance
	 */
	public static function instance ( $file = '', $version = '0.1.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	private function _log_version_number() {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number()

}
