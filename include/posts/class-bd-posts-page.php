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


}

new BD_Posts_Page();
