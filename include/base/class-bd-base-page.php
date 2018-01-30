<?php
/**
 * Base class for all Pages.
 *
 * @since   5.5.4
 *
 * @author  Sudar
 *
 * @package BulkDelete\Base\Page
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Base class for Pages.
 *
 * @abstract
 *
 * @since 5.5.4
 */
abstract class BD_Base_Page {
	/**
	 * @var string Page Slug.
	 */
	protected $page_slug;

	protected $item_type;

	/**
	 * @var string Menu action.
	 */
	protected $menu_action = 'bd_after_primary_menus';

	/**
	 * @var string Minimum capability needed for viewing this page.
	 */
	protected $capability = 'manage_options';

	// TODO: Remove property after confirming on `class-bd-settings-page.php` file.
	/**
	 * @var bool Whether sidebar is needed or not.
	 */
	protected $render_sidebar = false;

	/**
	 * @var string The screen variable for this page.
	 */
	protected $screen;

	/**
	 * @var array Labels used in this page.
	 */
	protected $label = array();

	/**
	 * @var array Messages shown to the user.
	 */
	protected $messages = array();

	/**
	 * @var array Actions used in this page.
	 */
	protected $actions = array();

	/**
	 * Initialize and setup variables.
	 *
	 * @since 5.5.4
	 * @abstract
	 *
	 * @return void
	 */
	abstract protected function initialize();

	/**
	 * Render body content.
	 *
	 * @since 5.5.4
	 * @abstract
	 *
	 * @return void
	 */
	abstract protected function render_body();

	/**
	 * Use `factory()` method to create instance of this class.
	 * Don't create instances directly.
	 *
	 * @since 5.5.4
	 * @see factory()
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 * Setup the module.
	 *
	 * @since 5.5.4
	 */
	protected function setup() {
		$this->initialize();
		$this->setup_hooks();
	}

	/**
	 * Setup hooks.
	 *
	 * @since 5.5.4
	 */
	protected function setup_hooks() {
		add_action( $this->menu_action, array( $this, 'add_menu' ) );
		add_action( "bd_admin_footer_for_{$this->page_slug}", array( $this, 'modify_admin_footer' ) );
		add_action( 'admin_print_scripts-' . $this->page_slug, array( $this, 'add_script' ) );

		add_filter( 'bd_action_nonce_check', array( $this, 'nonce_check' ), 10, 2 );
		add_filter( 'bd_admin_help_tabs', array( $this, 'render_help_tab' ), 10, 2 );
	}

	/**
	 * Add menu.
	 *
	 * @since 5.5.4
	 */
	public function add_menu() {
		$this->screen = add_submenu_page(
			$this->get_base_page_slug(),
			$this->label['page_title'],
			$this->label['menu_title'],
			$this->capability,
			$this->page_slug,
			array( $this, 'render_page' )
		);

		add_action( "load-{$this->screen}", array( $this, "add_delete_settings_panel" ) );
	}

	/**
	 * Check for nonce before executing the action.
	 *
	 * @since 5.5.4
	 *
	 * @param bool   $result The current result.
	 * @param string $action Action name.
	 */
	public function nonce_check( $result, $action ) {
		if ( in_array( $action, $this->actions ) ) {
			if ( check_admin_referer( "bd-{$this->page_slug}", "bd-{$this->page_slug}-nonce" ) ) {
				return true;
			}
		}

		return $result;
	}

	/**
	 * Modify help tabs for the current page.
	 *
	 * @since 5.5.4
	 *
	 * @param array  $help_tabs Current list of help tabs.
	 * @param string $screen    Current screen name.
	 *
	 * @return array Modified list of help tabs.
	 */
	public function render_help_tab( $help_tabs, $screen ) {
		if ( $this->screen == $screen ) {
			$help_tabs = $this->add_help_tab( $help_tabs );
		}

		return $help_tabs;
	}

	/**
	 * Add help tabs.
	 * Help tabs can be added by overriding this function in the child class.
	 *
	 * @since 5.5.4
	 *
	 * @param array $help_tabs Current list of help tabs.
	 *
	 * @return array List of help tabs.
	 */
	protected function add_help_tab( $help_tabs ) {
		return $help_tabs;
	}

	/**
	 * Render the page.
	 *
	 * @since 5.5.4
	 */
	public function render_page() {
?>
		<div class="wrap">
			<h2><?php echo $this->label['page_title'];?></h2>
			<?php settings_errors(); ?>

			<form method = "post">
			<?php $this->render_nonce_fields(); ?>

			<div id = "poststuff">
				<div id="post-body" class="metabox-holder columns-1">

					<?php $this->render_header(); ?>

					<div id="postbox-container-2" class="postbox-container">
						<?php $this->render_body(); ?>
					</div> <!-- #postbox-container-2 -->

				</div> <!-- #post-body -->
			</div><!-- #poststuff -->
			</form>
		</div><!-- .wrap -->
<?php
		$this->render_footer();
	}

	/**
	 * Print nonce fields.
	 *
	 * @since 5.5.4
	 */
	protected function render_nonce_fields() {
		wp_nonce_field( "bd-{$this->page_slug}", "bd-{$this->page_slug}-nonce" );
	}

	/**
	 * Render header for the page.
	 *
	 * If sidebar is enabled, then it is rendered as well.
	 *
	 * @since 5.5.4
	 */
	protected function render_header() {
?>
		<div class="notice notice-warning">
			<p><strong><?php echo $this->messages['warning_message']; ?></strong></p>
		</div>
<?php
	}

	/**
	 * Render footer.
	 *
	 * @since 5.5.4
	 */
	protected function render_footer() {
		/**
		 * Runs just before displaying the footer text in the admin page.
		 *
		 * This action is primarily for adding extra content in the footer of admin page.
		 *
		 * @since 5.5.4
		 */
		do_action( "bd_admin_footer_for_{$this->page_slug}" );
	}

	/**
	 * Modify admin footer in Bulk Delete plugin pages.
	 */
	public function modify_admin_footer() {
		add_filter( 'admin_footer_text', 'bd_add_rating_link' );
	}

	/**
	 * Enqueue Scripts and Styles.
	 */
	public function add_script() {
		global $wp_scripts;
		$bd = BULK_DELETE();

		/**
		 * Runs just before enqueuing scripts and styles in all Bulk WP admin pages.
		 *
		 * This action is primarily for registering or deregistering additional scripts or styles.
		 *
		 * @since 5.5.1
		 */
		do_action( 'bd_before_admin_enqueue_scripts' );

		wp_enqueue_script( 'jquery-ui-timepicker', plugins_url( '/assets/js/jquery-ui-timepicker-addon.min.js', $bd::$PLUGIN_FILE ), array( 'jquery-ui-slider', 'jquery-ui-datepicker' ), '1.5.4', true );
		wp_enqueue_style( 'jquery-ui-timepicker', plugins_url( '/assets/css/jquery-ui-timepicker-addon.min.css', $bd::$PLUGIN_FILE ), array(), '1.5.4' );

		wp_enqueue_script( 'select2', plugins_url( '/assets/js/select2.min.js', $bd::$PLUGIN_FILE ), array( 'jquery' ), '4.0.0', true );
		wp_enqueue_style( 'select2', plugins_url( '/assets/css/select2.min.css', $bd::$PLUGIN_FILE ), array(), '4.0.0' );

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( $bd::JS_HANDLE, plugins_url( '/assets/js/bulk-delete' . $postfix . '.js', $bd::$PLUGIN_FILE ), array( 'jquery-ui-timepicker', 'jquery-ui-tooltip' ), $bd::VERSION, true );
		wp_enqueue_style( $bd::CSS_HANDLE, plugins_url( '/assets/css/bulk-delete' . $postfix . '.css', $bd::$PLUGIN_FILE ), array( 'select2' ), $bd::VERSION );

		$ui  = $wp_scripts->query( 'jquery-ui-core' );
		$url = "//ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
		wp_enqueue_style( 'jquery-ui-smoothness', $url, false, $ui->ver );

		/**
		 * Filter JavaScript array.
		 *
		 * This filter can be used to extend the array that is passed to JavaScript
		 *
		 * @since 5.4
		 */
		$translation_array = apply_filters( 'bd_javascript_array', array(
			'msg'            => array(),
			'validators'     => array(),
			'dt_iterators'   => array(),
			'pre_action_msg' => array(),
			'error_msg'      => array(),
			'pro_iterators'  => array(),
		) );
		wp_localize_script( $bd::JS_HANDLE, $bd::JS_VARIABLE, $translation_array );

		/**
		 * Runs just after enqueuing scripts and styles in all Bulk WP admin pages.
		 *
		 * This action is primarily for registering additional scripts or styles.
		 *
		 * @since 5.5.1
		 */
		do_action( 'bd_after_admin_enqueue_scripts' );
	}

	/**
	 * Getter for screen.
	 *
	 * @return string Current value of screen
	 */
	public function get_screen() {
		return $this->screen;
	}

	/**
	 * Getter for page_slug.
	 *
	 * @return string Current value of page_slug
	 */
	public function get_page_slug() {
		return $this->page_slug;
	}

	/**
	 * Get the page slug of base page.
	 *
	 * @return string Page slug.
	 */
	public function get_base_page_slug() {
		$bd = BULK_DELETE();
	    if ( $this->is_bulk_delete_menu_registered() ) {
			return $bd::POSTS_PAGE_SLUG;
		}

		return 'bulk-delete-posts';
	}

	/**
	 * Is the bulk delete menu already registered?
	 *
	 * @return bool True if registered, False otherwise.
	 */
	protected function is_bulk_delete_menu_registered() {
		global $admin_page_hooks;

		$bd = BULK_DELETE();

		return ! empty( $admin_page_hooks[ $bd::POSTS_PAGE_SLUG ] );
	}

	/**
	 * Add settings Panel for delete posts page.
	 */
	public function add_delete_settings_panel() {
		/**
		 * Add contextual help for admin screens.
		 *
		 * @since 5.1
		 */
		do_action( 'bd_add_contextual_help', $this->item_type );

		/* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
		do_action( 'add_meta_boxes_' . $this->screen, null );

		/* Enqueue WordPress' script for handling the meta boxes */
		wp_enqueue_script( 'postbox' );
	}
}
?>
