<?php
/**
 * Bulk Delete Posts by Custom Fields.
 *
 * @since   5.5
 *
 * @author  Sudar
 *
 * @package BulkDelete\Posts\Modules
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Bulk Delete Posts by Custom Field.
 *
 * @since 5.7.0
 */
class Bulk_Delete_Posts_By_Custom_Field extends BD_Post_Meta_Box_Module {

	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'p_custom_field';
		$this->meta_box_slug = 'bd_posts_by_custom_field';
		$this->meta_box_hook = "bd_add_meta_box_for_{$this->item_type}";
		$this->delete_action = 'delete_posts_by_custom_field';
		$this->cron_hook     = 'do-bulk-delete-posts-by-custom-field';
		// TODO: Change the URL.
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-users-by-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-u-ma';
		$this->messages      = array(
			'box_label'      => __( 'By User Meta', 'bulk-delete' ),
			'scheduled'      => __( 'Users from with the selected user meta are scheduled for deletion.', 'bulk-delete' ),
			'deleted_single' => __( 'Deleted %d user with the selected user meta', 'bulk-delete' ),
			'deleted_plural' => __( 'Deleted %d users with the selected user meta', 'bulk-delete' ),
		);
	}

	public function render() {
		// TODO: Implement render() method.
	}

	public function process() {
		// TODO: Implement process() method.
	}

	public function delete( $delete_options ) {
		// TODO: Implement delete() method.
	}
}

new Bulk_Delete_Posts_By_Custom_Field();