<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tista_Admin_Importers Class.
 */
class Tista_Admin_Importer {	
	
	/**
	 * Array of importer IDs.
	 *
	 * @var string[]
	 */
	protected $importers = array();
	
	/**
	 * The single class instance.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var object
	 */
	private static $_instance = null;
	
	/**
	 * Path to the theme-options file.
	 *
	 * @access private
	 * @since 5.2
	 * @var string
	 */
	private $theme_options_file;

	/**
	 * Plugin data.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var object
	 */
	private $data;

	/**
	 * The slug.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * The version number.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $version;

	/**
	 * The web URL to the plugin directory.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * The server path to the plugin directory.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * The web URL to the plugin admin page.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $page_url;

	/**
	 * The setting option name.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * Main Tista_Assistant Instance
	 *
	 * Ensures only one instance of this class exists in memory at any one time.
	 *
	 * @see Tista_Assistant()
	 * @uses Tista_Assistant::init_globals() Setup class globals.
	 * @uses Tista_Assistant::init_includes() Include required files.
	 * @uses Tista_Assistant::init_actions() Setup hooks and actions.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Tista_Assistant.
	 * @codeCoverageIgnore
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			self::$_instance->init_globals();
			self::$_instance->init_includes();
			self::$_instance->init_actions();
		}
		return self::$_instance;
	}

	/**
	 * A dummy constructor to prevent this class from being loaded more than once.
	 *
	 * @see Tista_Assistant::instance()
	 *
	 * @since 1.0.0
	 * @access private
	 * @codeCoverageIgnore
	 */
	private function __construct() {
		/* We do nothing here! */
	}

	/**
	 * You cannot clone this class.
	 *
	 * @since 1.0.0
	 * @codeCoverageIgnore
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'tista-importer' ), '1.0.0' );
	}

	/**
	 * You cannot unserialize instances of this class.
	 *
	 * @since 1.0.0
	 * @codeCoverageIgnore
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'tista-importer' ), '1.0.0' );
	}
	

	/**
	 * Setup the class globals.
	 *
	 * @since 1.0.0
	 * @access private
	 * @codeCoverageIgnore
	 */
	private function init_globals() {
		$this->data        = new stdClass();
		$this->version     = TISTA_IMPORTER_VERSION;
		$this->slug        = 'tista-importer';
		$this->option_name = self::sanitize_key( $this->slug );
		$this->plugin_url  = TISTA_IMPORTER_URI;
		$this->plugin_path = TISTA_IMPORTER_PATH;
		$this->page_url    = TISTA_IMPORTER_NETWORK_ACTIVATED ? network_admin_url( 'admin.php?page=' . $this->slug ) : admin_url( 'admin.php?page=' . $this->slug );
		$this->data->admin = true;

	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @access private
	 * @codeCoverageIgnore
	 */
	private function init_includes() {
		$this->theme_options_file =  $this->plugin_url . '/data/dummy-content/theme_options.json';
	}

	/**
	 * Setup the hooks, actions and filters.
	 *
	 * @uses add_action() To add actions.
	 * @uses add_filter() To add filters.
	 *
	 * @since 1.0.0
	 * @access private
	 * @codeCoverageIgnore
	 */
	private function init_actions() {
		// Activate plugin.
		register_activation_hook( TISTA_IMPORTER_CORE_FILE, array( $this, 'activate' ) );

		// Deactivate plugin.
		register_deactivation_hook( TISTA_IMPORTER_CORE_FILE, array( $this, 'deactivate' ) );

		// Load the textdomain.
		//add_action( 'init', array( $this, 'load_textdomain' ) );

		// Load OAuth.
		add_action( 'admin_menu', array( $this, 'add_to_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_ajax_tista_do_ajax_import', array( $this, 'tista_do_ajax_import' ) );

		// Register Tista importers.
		$this->importers['tista_importer'] = array(
			'menu'       => 'edit_theme_options',
			'name'       => __( 'Tista Demo Import', 'tista' ),
			'capability' => 'tista-importer',
			'callback'   => array( $this, 'tista_importer' ),
		);
	}
	
	
	/**
	 * Add menu items for our custom importers.
	 */
	public function add_to_menus() {
		foreach ( $this->importers as $id => $importer ) {
			add_theme_page( $importer['name'], $importer['name'], $importer['menu'], $importer['capability'],$importer['callback'],13 );
		}
	}

	/**
	 * Hide menu items from view so the pages exist, but the menu items do not.
	 */
	public function hide_from_menus() {
		global $submenu;

		foreach ( $this->importers as $id => $importer ) {
			if ( isset( $submenu[ $importer['menu'] ] ) ) {
				foreach ( $submenu[ $importer['menu'] ] as $key => $menu ) {
					if ( $id === $menu[2] ) {
						unset( $submenu[ $importer['menu'] ][ $key ] );
					}
				}
			}
		}
	}
	/**
	 * Register importer scripts.
	 */
	public function admin_scripts() {
		wp_enqueue_style( 'tista-admin', TISTA_IMPORTER_URI.'/assets/css/admin.css', '', '','screen' );
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * The product importer.
	 *
	 * This has a custom screen - the Tools > Import item is a placeholder.
	 * If we're on that screen, redirect to the custom one.
	 */
	public function tista_importer() {
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=post&page=tista_importer' ) );
			exit;
		}

		include_once( TISTA_IMPORTER_PATH . 'inc/class-tista-importer-controller.php' );

		$importer = new Tista_Importer_Controller();
		$importer->dispatch();
	}
	
	/**
	 * Content import
	 */
	public function tista_do_ajax_import() {
		check_admin_referer('tista_import', 'tista_import_nonce');
		update_option('tista_chosen_template', 'dummy-content');		

		set_time_limit( 0 );

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true );
		}	
		
		include_once( TISTA_IMPORTER_PATH . 'wordpress-importer/wordpress-importer.php' );
		
		$wp_import                    = new WP_Import();
		$wp_import->fetch_attachments = true;

		ob_start();
		$wp_import->import( TISTA_IMPORTER_PATH. 'data/dummy-content/content.xml' );
		ob_end_clean();
		       
        $locations = get_theme_mod( 'nav_menu_locations' );
        $menus = wp_get_nav_menus();

        if ($menus) {
            foreach ($menus as $menu) {
                if ($menu->name == 'Main menu') {
                    $locations['primary'] = $menu->term_id;
                }
            }
        }
        set_theme_mod( 'nav_menu_locations', $locations );
        update_option( 'show_on_front', 'page' );		
		/*Widgets*/		
               tista_submit_import_data(  );            	
              $this->import_theme_options(  );            	
		
		echo 'done';
		die();
	}
	
	/**
	 * Imports Theme Options.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_theme_options() {
		$theme_options = array(
			'tista_general_sidebar' => 'true',
			'general_email' => 'example@gmail.com',
			'general_mobile' => '+880 1737 2905xx',
			'general_fb' => 'http://facebook.com',
			'general_twitter' => 'http://twitter.com',
			'general_linked' => 'http://linked.com',
			'general_google_plus' => 'http://googleplus.com ',
			'general_drible' => 'http://drible.com',
			'general_android' => 'http://android.com',
			'general_copy_right' => 'Powred by ',
			'blog_comment' => 'true',
			'general_404_page' => 'Not Found Go to Search',
			'general_tracking_code' => '',
		) ;
		update_option( 'chobi_framework_values', $theme_options );
	}
	/**
	 * Activate plugin.
	 *
	 * @since 1.0.0
	 * @codeCoverageIgnore
	 */
	public function activate() {
		self::set_plugin_state( true );
	}

	/**
	 * Deactivate plugin.
	 *
	 * @since 1.0.0
	 * @codeCoverageIgnore
	 */
	public function deactivate() {
		self::set_plugin_state( false );
	}

	/**
	 * Loads the plugin's translated strings.
	 *
	 * @since 1.0.0
	 * @codeCoverageIgnore
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'tista-assistant', false, TISTA_IMPORTER_PATH . 'languages/' );
	}

	/**
	 * Sanitize data key.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $key An alpha numeric string to sanitize.
	 * @return string
	 */
	private function sanitize_key( $key ) {
		return preg_replace( '/[^A-Za-z0-9\_]/i', '', str_replace( array( '-', ':' ), '_', $key ) );
	}

	/**
	 * Recursively converts data arrays to objects.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $array An array of data.
	 * @return object
	 */
	private function convert_data( $array ) {
		foreach ( (array) $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$array[ $key ] = self::convert_data( $value );
			}
		}
		return (object) $array;
	}

	/**
	 * Set the `is_plugin_active` option.
	 *
	 * This setting helps determine context. Since the plugin can be included in your theme root you
	 * might want to hide the admin UI when the plugin is not activated and implement your own.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param bool $value Whether or not the plugin is active.
	 */
	private function set_plugin_state( $value ) {
		self::set_option( 'is_plugin_active', $value );
	}

	/**
	 * Set option value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Option name.
	 * @param mixed  $option Option data.
	 */
	public function set_option( $name, $option ) {
		$options          = self::get_options();
		$name             = self::sanitize_key( $name );
		$options[ $name ] = esc_html( $option );
		$this->set_options( $options );
	}


	/**
	 * Set option.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $options Option data.
	 */
	public function set_options( $options ) {
		TISTA_IMPORTER_NETWORK_ACTIVATED ? update_site_option( $this->option_name, $options ) : update_option( $this->option_name, $options );
	}

	/**
	 * Return the option settings array.
	 *
	 * @since 1.0.0
	 */
	public function get_options() {
		return TISTA_IMPORTER_NETWORK_ACTIVATED ? get_site_option( $this->option_name, array() ) : get_option( $this->option_name, array() );
	}

	/**
	 * Return a value from the option settings array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Option name.
	 * @param mixed  $default The default value if nothing is set.
	 * @return mixed
	 */
	public function get_option( $name, $default = '' ) {
		$options = self::get_options();
		$name    = self::sanitize_key( $name );
		return isset( $options[ $name ] ) ? $options[ $name ] : $default;
	}

	/**
	 * Set data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Unique object key.
	 * @param mixed  $data Any kind of data.
	 */
	public function set_data( $key, $data ) {
		if ( ! empty( $key ) ) {
			if ( is_array( $data ) ) {
				$data = self::convert_data( $data );
			}
			$key = self::sanitize_key( $key );
			// @codingStandardsIgnoreStart
			$this->data->$key = $data;
			// @codingStandardsIgnoreEnd
		}
	}

	/**
	 * Get data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Unique object key.
	 * @return string|object
	 */
	public function get_data( $key ) {
		return isset( $this->data->$key ) ? $this->data->$key : '';
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Return the plugin version number.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Return the plugin URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return $this->plugin_url;
	}

	/**
	 * Return the plugin path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_path() {
		return $this->plugin_path;
	}

	/**
	 * Return the plugin page URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_page_url() {
		return $this->page_url;
	}

	/**
	 * Return the option settings name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_option_name() {
		return $this->option_name;
	}
}