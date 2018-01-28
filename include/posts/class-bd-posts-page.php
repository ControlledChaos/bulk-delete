<?php

/**
 * Bulk Delete Posts Page class.
 *
 * @since 5.7.0
 */
class BD_Posts_Page extends BD_Page {

    protected $menu_action = 'admin_menu';

	/**
	 * Initialize and setup variables.
	 *
	 * @since 5.7.0
	 *
	 * @return void
	 */
	protected function initialize() {
		$this->page_slug  = 'bulk-delete-posts';
		$this->item_type  = 'posts';
		$this->capability = 'delete_users';

		$this->label = array(
			'page_title' => __( 'Bulk Delete Posts', 'bulk-delete' ),
			'menu_title' => __( 'Bulk Delete Posts', 'bulk-delete' ),
		);

		$this->messages = array(
			'warning_message'      => __( 'WARNING: Posts deleted once cannot be retrieved back. Use with caution.', 'bulk-delete' ),
		);
	}

	/**
	 * Override parents `add_menu()` as this is the first menu item.
	 *
	 * @since 5.7.0
	 */
	public function add_menu() {
		if ( ! $this->is_bulk_delete_menu_registered() ) {
			$this->register_bulk_delete_menu();
		}

		parent::add_menu();
	}

	/**
	 * Add menu.
	 *
	 * @since 5.7.0
	 */
	public function register_bulk_delete_menu() {
		add_menu_page(
			__( 'Bulk WP', 'bulk-delete' ),
			__( 'Bulk WP', 'bulk-delete' ),
			'manage_options',
			$this->page_slug,
			array( $this, 'render_page' ),
			'dashicons-trash'
		);
	}

	/**
	 * Show the delete posts page.
	 *
     * @since 5.7.0
	 */
	public function render_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Bulk Delete Posts', 'bulk-delete' );?></h2>
			<?php settings_errors(); ?>

			<form method = "post">
				<?php
				// nonce for bulk delete
				wp_nonce_field( 'sm-bulk-delete-posts', 'sm-bulk-delete-posts-nonce' );

				/* Used to save closed meta boxes and their order */
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
				?>
				<div id = "poststuff">
					<div id="post-body" class="metabox-holder columns-1">

						<div class="notice notice-warning">
							<p><strong><?php _e( 'WARNING: Posts deleted once cannot be retrieved back. Use with caution.', 'bulk-delete' ); ?></strong></p>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php do_meta_boxes( '', 'advanced', null ); ?>
						</div> <!-- #postbox-container-2 -->

					</div> <!-- #post-body -->
				</div><!-- #poststuff -->
			</form>
		</div><!-- .wrap -->

		<?php
		/**
		 * Runs just before displaying the footer text in the "Bulk Delete Posts" admin page.
		 *
		 * This action is primarily for adding extra content in the footer of "Bulk Delete Posts" admin page.
		 *
		 * @since 5.0.0
		 */
		do_action( 'bd_admin_footer_posts_page' );
	}
}

new BD_Posts_Page();
