<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.presstigers.com/
 * @since      1.0.0
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/admin
 * @author     PressTigers <support@presstigers.com>
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Zipcode_BCP_Admin_User_Requested_Zipcodes extends WP_List_Table {


	private $counter = 1;

	/** Class constructor */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => __( 'Requested Zipcode', 'zipcode-bcp' ), // singular name of the listed records
				'plural'   => __( 'Requested Zipcodes', 'zipcode-bcp' ), // plural name of the listed records
				'ajax'     => false, // does this table support ajax?
			)
		);
	}

	/**
	 * Retrieve zipcodes data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_zipcodes( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}zipcode_requested_users";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . sanitize_text_field( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . sanitize_text_field( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Retrieve zipcodes data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_zipcode_users_count( $zipcode ) {

		global $wpdb;
		$table = "{$wpdb->prefix}zipcode_requested_users";
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE zipcode = '$zipcode'" );

		return $count;
	}

	/**
	 * Delete a zipcode record.
	 *
	 * @param int $id zipcode ID
	 */
	public static function delete_zipcode( $id ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}zipcode_requested_users",
			array( 'id' => $id ),
			array( '%d' )
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}zipcode_requested_users";

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no request data is available */
	public function no_items() {
		esc_html_e( 'No user requested zipcodes avaliable.', 'zipcode-bcp' );
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array  $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
			case 'zipcode':
				return $item[ $column_name ];
			case 'user_email':
				return $item[ $column_name ];
			case 'post_id':
				return $item[ $column_name ];
			case 'post_type':
				return $item[ $column_name ];
			case 'post_title':
				$output = "<a href='" . esc_url( get_the_permalink( $item['post_id'] ) ) . "' target='_blank'>" . esc_html( get_the_title( $item['post_id'] ) ) . '</a>';
				return $output;
			default:
				return print_r( $item, true );
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />',
			esc_attr( $item['id'] )
		);
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	public function column_id( $item ) {

		$delete_nonce = wp_create_nonce( 'delete_zipcode' );

		$title = '<strong>' . $this->counter . '</strong>';

		$actions = array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&zipcode_id=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
		);
		$this->counter++;
		return $title . $this->row_actions( $actions );
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'id'         => __( 'ID', 'zipcode-bcp' ),
			'zipcode'    => __( 'Zip Code', 'zipcode-bcp' ),
			'user_email' => __( 'User Email', 'zipcode-bcp' ),
			'post_id'    => __( 'Post Type ID', 'zipcode-bcp' ),
			'post_type'  => __( 'Post Type Name', 'zipcode-bcp' ),
			'post_title' => __( 'Post Type Title', 'zipcode-bcp' ),
		);

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'zipcode' => array( __( 'zipcode', 'zipcode-bcp' ), false ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => __( 'Delete', 'zipcode-bcp' ),
		);

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'zipcode_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // WE have to calculate the total number of items
				'per_page'    => $per_page, // WE have to determine how many items to show on a page
			)
		);

		$this->items = self::get_zipcodes( $per_page, $current_page );
	}

	public function process_bulk_action() {
		// Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = sanitize_text_field( $_REQUEST['_wpnonce'] );
            
			if ( ! wp_verify_nonce( $nonce, 'delete_zipcode' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete_zipcode( sanitize_text_field( $_GET['zipcode_id'] ) );
			}
		}
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_zipcode( $id );
			}
		}
	}

}

class SZC_User_Requested_Zipcode {


	// class instance
	public static $instance;
	// customer WP_List_Table object
	public $req_zipcodes_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'zbcp_user_req_lists_menu' ) );
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function zbcp_user_req_lists_menu() {
		$hook = add_submenu_page( 'zbcp', 'User Requests', 'User Requests', 'manage_options', 'zbcp-users-requests', array( $this, 'cd_users_requested_zip_code_callback' ) );
		add_action( "load-$hook", array( $this, 'screen_option' ) );
	}

	/**
	 * Lists settings page
	 */
	public function cd_users_requested_zip_code_callback() {
		?>
		<div class="wrap">
			<h2>
				<?php esc_html_e( 'User Requests', 'zipcode-bcp' ); ?>
			</h2>
			<p style="margin-top: 0px;"><?php esc_html_e( 'See users detail that is submitted against a ZIP Code.', 'zipcode-bcp' ); ?></p>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->req_zipcodes_obj->prepare_items();
								$this->req_zipcodes_obj->display();
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = array(
			'label'   => 'Requested Zipcodes',
			'default' => 20,
			'option'  => 'zipcode_per_page',
		);

		add_screen_option( $option, $args );

		$this->req_zipcodes_obj = new Zipcode_BCP_Admin_User_Requested_Zipcodes();
	}

	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

add_action(
	'plugins_loaded',
	function () {
		SZC_User_Requested_Zipcode::get_instance();
	}
);
