<?php
/**
 * This file adds the Genesis Hero admin settings.
 *
 * @package  SeoThemes/GenesisHero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Genesis Hero Settings class.
 */
class Genesis_Hero_Settings {

	/**
	 * The single instance of Genesis_Hero_Settings.
	 *
	 * @var 	object
	 * @access  private
	 * @since 	0.1.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 *
	 * @var 	object
	 * @access  public
	 * @since 	0.1.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   0.1.0
	 */
	public $settings = array();

	/**
	 * Constructor.
	 *
	 * @param object $parent Main plugin object.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = 'genesis_hero_';

		// Initialise settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings.
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu.
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ), 11 );

		// Add settings link to plugins page.
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings.
	 *
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu.
	 *
	 * @return void
	 */
	public function add_menu_item() {

		$page = add_submenu_page(
			'genesis',
			__( 'Genesis Hero', 'genesis-hero' ),
			__( 'Genesis Hero', 'genesis-hero' ),
			'manage_options',
			$this->parent->_token . '_settings',
			array( $this, 'settings_page' )
		);

		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS.
	 *
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker.
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );

		// We're including the WP media scripts here because they're needed for the image upload field.
		wp_enqueue_media();

		wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '0.1.0' );
		wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table.
	 *
	 * @param  array $links Existing links.
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'genesis-hero' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		$settings['standard'] = array(
			'title'					=> __( 'Settings', 'genesis-hero' ),
			'description'			=> __( 'Customize the default behaviour of the Genesis Hero plugin.', 'genesis-hero' ),
			'fields'				=> array(
				array(
					'id' 			=> 'output_css',
					'label'			=> __( 'Output CSS', 'genesis-hero' ),
					'description'	=> __( 'Set this to No if you would like to disable the plugin from outputting CSS.', 'genesis-hero' ),
					'type'			=> 'radio',
					'options'		=> array(
						true 	=> 'Yes',
						false	=> 'No',
					),
					'default'		=> '1',
				),
				array(
					'id' 			=> 'output_js',
					'label'			=> __( 'Output JS', 'genesis-hero' ),
					'description'	=> __( 'Set this to No if you would like to disable the plugin from outputting JavaScript.', 'genesis-hero' ),
					'type'			=> 'radio',
					'options'		=> array(
						true 	=> 'Yes',
						false	=> 'No',
					),
					'default'		=> '1',
				),
				array(
					'id' 			=> 'enable_hero_on',
					'label'			=> __( 'Enable Hero on', 'genesis-hero' ),
					'description'	=> __( 'Select which pages and post types to enable the hero section on.', 'genesis-hero' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array(
						'front_page'	=> 'Front Page',
						'posts_page'	=> 'Posts Page',
						'single_posts'	=> 'Single Posts',
						'pages'			=> 'Pages',
						'archives'		=> 'Archives',
						'author'		=> 'Author',
						'404_page'		=> '404 Page',
						'attachment'	=> 'attachment/Media',
					),
					'default'		=> array(
						'front_page',
						'posts_page',
						'single_posts',
						'pages',
						'archives',
						'author',
						'404_page',
						'attachment',
					),
				),
				array(
					'id' 			=> 'featured_image',
					'label'			=> __( 'Use Featured Image', 'genesis-hero' ),
					'description'	=> __( 'Check this box to use the Featured Image of a page as the background image.', 'genesis-hero' ),
					'type'			=> 'checkbox',
					'default'		=> true,
				),
				array(
					'id' 			=> 'default_image',
					'label'			=> __( 'Default Image' , 'genesis-hero' ),
					'description'	=> __( 'Set a default image to use if there is no featured image set.' ),
					'type'			=> 'image',
					'default'		=> '1',
					'placeholder'	=> '',
				),
				array(
					'id' 			=> 'text_align',
					'label'			=> __( 'Text Alignment', 'genesis-hero' ),
					'description'	=> __( 'Select the text alignment of the hero section.', 'genesis-hero' ),
					'type'			=> 'radio',
					'options'		=> array(
						'left' 	 => 'Left',
						'center' => 'Center',
						'right'  => 'Right',
					),
					'default'		=> 'left',
				),
				array(
					'id' 			=> 'text_color',
					'label'			=> __( 'Text Color', 'genesis-hero' ),
					'description'	=> __( 'Select a color for the hero text.', 'genesis-hero' ),
					'type'			=> 'color',
					'default'		=> '#ffffff',
				),
				array(
					'id' 			=> 'enable_overlay',
					'label'			=> __( 'Enable Overlay', 'genesis-hero' ),
					'description'	=> __( 'Check this box to enable a color overlay.', 'genesis-hero' ),
					'type'			=> 'checkbox',
					'default'		=> true,
				),
				array(
					'id' 			=> 'overlay_color',
					'label'			=> __( 'Overlay Color', 'genesis-hero' ),
					'description'	=> __( 'Select a color for the hero overlay.', 'genesis-hero' ),
					'type'			=> 'color',
					'default'		=> '#333333',
				),
				array(
					'id' 			=> 'overlay_opacity',
					'label'			=> __( 'Overlay Opacity' , 'genesis-hero' ),
					'description'	=> __( 'This controls the opacity level for the hero overlay.', 'genesis-hero' ),
					'type'			=> 'number',
					'default'		=> '0.82',
					'placeholder'	=> __( '0.82', 'genesis-hero' ),
					'min'			=> '0.0',
					'max'			=> '1.0',
					'step'			=> '0.01',
				),
			),
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this->parent->admin, 'display_field' ),
						$this->parent->_token . '_settings', $section,
						array(
							'field' => $field,
							'prefix' => $this->base,
							'step' => '0.1',
						)
					);
				}

				if ( ! $current_section ) {
					break;
				}
			} // End foreach().
		} // End if().
	}

	/**
	 * Settings Section.
	 *
	 * @param  array $section Available settings.
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML.
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Genesis Hero' , 'genesis-hero' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields.
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'genesis-hero' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main Genesis_Hero_Settings Instance.
	 *
	 * Ensures only one instance of Genesis_Hero_Settings is loaded or can be loaded.
	 *
	 * @since 0.1.0
	 * @static
	 * @see Genesis_Hero()
	 * @return Main Genesis_Hero_Settings instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}
