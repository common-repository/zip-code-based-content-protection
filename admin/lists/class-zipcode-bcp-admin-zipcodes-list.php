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

class Zipcode_BCP_Admin_Zipcodes_List extends WP_List_Table {


	private $counter = 1;

	/** Class constructor */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => __( 'Zipcode', 'zipcode-bcp' ), // singular name of the listed records
				'plural'   => __( 'Zipcodes', 'zipcode-bcp' ), // plural name of the listed records
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
	public static function get_zipcodes( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}zipcode_serving";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Delete a zipcode record.
	 *
	 * @param int $id zipcode ID
	 */
	public static function delete_zipcode( $id ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}zipcode_serving",
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

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}zipcode_serving";

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no zipcode data is available */
	public function no_items() {
		esc_html_e( 'No zipcodes avaliable.', 'zipcode-bcp' );
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
			case 'services':
				$res    = $item[ $column_name ];
				$output = '';
				if ( empty( $res ) ) {
					$output = 'N\A';
				} else {
						$output = $res;
				}
				return $output;
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes
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
			'cb'       => '<input type="checkbox" />',
			'id'       => __( 'ID', 'zipcode-bcp' ),
			'zipcode'  => __( 'Zip Code', 'zipcode-bcp' ),
			'services' => __( 'City & County (only US)', 'zipcode-bcp' ),
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

class SZC_Zipcode_List {


	// class instance
	public static $instance;
	// customer WP_List_Table object
	public $zipcodes_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'zbcp_lists_menu' ) );
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function zbcp_lists_menu() {

		$hook = add_menu_page(
			'ZBCP',
			'ZBCP',
			'manage_options',
			'zbcp',
			array( $this, 'zipcode_bcp_callback' ),
			'dashicons-admin-site-alt2',
			31
		);
		add_action( "load-$hook", array( $this, 'screen_option' ) );
	}

	/**
	 * Lists settings page
	 */
	public function zipcode_bcp_callback() {
		?>
		<div class="wrap">
			<h2>
				<?php  esc_html_e( 'ZIP Code Based Content Protection', 'zipcode-bcp' ); ?>
				<a href="#TB_inline?width=200&height=500&inlineId=my-content-id" class="button action thickbox"><?php esc_html_e( 'Add USA ZIP Code', 'zipcode-bcp' ); ?></a>
				<a href="#TB_inline?width=200&height=200&inlineId=other-content-id" class="button action thickbox"><?php esc_html_e( 'Add Other Countries ZIP Code', 'zipcode-bcp' ); ?></a>
				<p style="margin-top: 10px;"><?php esc_html_e( 'Add the ZIP Code(s) that you want to display on custom post type while restricting the content', 'zipcode-bcp' ); ?></p>
				<?php add_thickbox(); ?>
				<div id="other-content-id" style="display:none;">
					<div class="form_container">
						<h3 style="text-align: center;margin-top: 35px;"><?php esc_html_e( 'Add New Zip Code', 'zipcode-bcp' ); ?></h3>
						<form method="post" action="#" id="add_new_zipcode" class="add_new_zipcode">
							<input type="text" id="zipcode_field" name="zipcode" required placeholder="Minimum 4 Maximum 8 Characters">
							<input type="button" id="zipcode_button" class="zipcode_field" value="<?php echo esc_attr( 'Add' ); ?>" class="button" /> <div class="zbcp_loader"></div>
							<div class="success-msg-zipcode display-success-msg"><?php esc_html_e( 'Zipcode has been saved successfully!', 'zipcode-bcp' ); ?></div>
						</form>
					</div>
				</div>
				<div id="my-content-id" style="display:none;">
					<div class="form_container">
						<h3 style="text-align: center;margin-top: 35px;"><?php esc_html_e( 'Add New Zip Code', 'zipcode-bcp' ); ?></h3>
						<div id="svg_wrap"></div>
						<section>
							<p><?php esc_html_e( 'Select the State', 'zipcode-bcp' ); ?> </p>
							<select name="usa_state" id="usa_state">
								<option value="<?php echo esc_attr( 'AL' ); ?>"> <?php esc_html_e( 'Alabama', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'AK' ); ?>"> <?php esc_html_e( 'Alaska', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'AZ' ); ?>"> <?php esc_html_e( 'Arizona', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'AR' ); ?>"> <?php esc_html_e( 'Arkansas', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'CA' ); ?>"> <?php esc_html_e( 'California', 'zipcode-bcp' ); ?></option>
								
								<option value="<?php echo esc_attr( 'CO' ); ?>"> <?php esc_html_e( 'Colorado', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'CT' ); ?>"> <?php esc_html_e( 'Connecticut', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'DE' ); ?>"> <?php esc_html_e( 'Delaware', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'DC' ); ?>"> <?php esc_html_e( 'District Of Columbia', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'Fl' ); ?>"> <?php esc_html_e( 'Florida', 'zipcode-bcp' ); ?></option>

								
								<option value="<?php echo esc_attr( 'GA' ); ?>"> <?php esc_html_e( 'Georgia', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'HI' ); ?>"> <?php esc_html_e( 'Hawaii', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'ID' ); ?>"> <?php esc_html_e( 'Idaho', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'IL' ); ?>"> <?php esc_html_e( 'Illinois', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'IN' ); ?>"> <?php esc_html_e( 'Indiana', 'zipcode-bcp' ); ?></option>


								<option value="<?php echo esc_attr( 'IA' ); ?>"> <?php esc_html_e( 'Iowa', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'KS' ); ?>"> <?php esc_html_e( 'Kansas', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'LA' ); ?>"> <?php esc_html_e( 'Louisiana', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'ME' ); ?>"> <?php esc_html_e( 'Maine', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'MD' ); ?>"> <?php esc_html_e( 'Maryland', 'zipcode-bcp' ); ?></option>



								<option value="<?php echo esc_attr( 'MA' ); ?>"> <?php esc_html_e( 'Massachusetts', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'MI' ); ?>"> <?php esc_html_e( 'Michigan', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'MN' ); ?>"> <?php esc_html_e( 'Minnesota', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'MS' ); ?>"> <?php esc_html_e( 'Mississippi', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'MO' ); ?>"> <?php esc_html_e( 'Missouri', 'zipcode-bcp' ); ?></option>


								<option value="<?php echo esc_attr( 'MT' ); ?>"> <?php esc_html_e( 'Montana', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'NE' ); ?>"> <?php esc_html_e( 'Nebraska', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'NV' ); ?>"> <?php esc_html_e( 'Nevada', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'NH' ); ?>"> <?php esc_html_e( 'New Hampshire', 'zipcode-bcp' ); ?></option>
								<option value="<?php echo esc_attr( 'NJ' ); ?>"> <?php esc_html_e( 'New Jersey', 'zipcode-bcp' ); ?></option>

							</select>
						</section>

						<section>
							<div style="margin: 20px auto;" class="zbcp_loader"></div>
							<p style="text-align: center;font-weight: bold"><?php esc_html_e( 'State:', 'zipcode-bcp' ); ?> <span id="state_full_name"></span></p>
							<p><?php esc_html_e( 'Select the ZIP Code', 'zipcode-bcp' ); ?></p>
							<select multiple="yes" name="zipcode" id="zipcode_field_usa" class="zipcode_field_usa"></select>
						</section>
						<div class="button" id="prev"><?php esc_html_e( '&larr; Previous', 'zipcode-bcp' ); ?></div>
						<div class="button" id="next"><?php esc_html_e( 'Next &rarr;', 'zipcode-bcp' ); ?></div>
						<div class="button" id="submit_s"><?php esc_html_e( 'Submit', 'zipcode-bcp' ); ?></div>
					<div class="success-msg-zipcode display-success-msg"><?php esc_html_e( 'Zipcode has been saved successfully!', 'imple-zipcode-checker' ); ?></div>
					</div>
				</div>
			</h2>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->zipcodes_obj->prepare_items();
								$this->zipcodes_obj->display();
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
			'label'   => 'Zipcodes',
			'default' => 20,
			'option'  => 'zipcode_per_page',
		);

		add_screen_option( $option, $args );

		$this->zipcodes_obj = new Zipcode_BCP_Admin_Zipcodes_List();
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
		SZC_Zipcode_List::get_instance();
	}
);
