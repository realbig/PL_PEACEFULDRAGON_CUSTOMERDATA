<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class PD_CustomerData_Page
 *
 * Creates the admin page.
 *
 * @since   0.1.0
 */
class PD_CustomerData_Page {

	function __construct() {

		$this->add_actions();
	}

	private function add_actions() {

		add_action( 'admin_menu', array( $this, '_admin_menu' ) );
		add_action( 'admin_init', array( $this, '_do_export' ) );
	}

	function _admin_menu() {

		add_management_page(
			'Customer Export',
			'Customer Export',
			'manage_options',
			'pd-customer-export',
			array( $this, '_page_output' )
		);
	}

	private function get_customer_list() {

		$customers  = array();
		$columns    = array();
		$categories = isset( $_POST['pd-categories'] ) ? $_POST['pd-categories'] : false;

		$users = pd_cd_get_customer_users();

		if ( ! empty( $users ) ) {

			$columns = array(
				'User ID',
				'User Name',
				'User Registered',
				'Classes',
			);

			/** @var WP_User $user */
			foreach ( $users as $user ) {

				$classes = $this->get_user_products( $user->ID, $categories );

				$customers[] = array(
					$user->ID,
					$user->display_name,
					$user->user_registered,
					$classes,
				);
			}
		}

		return array(
			'columns' => $columns,
			'rows'    => $customers,
		);
	}

	private function get_user_products( $user_ID, $categories ) {

		$classes = '';

		$customer_orders = get_posts( array(
			'meta_key'    => '_customer_user',
			'meta_value'  => $user_ID,
			'post_type'   => 'shop_order',
			'numberposts' => - 1
		) );

		foreach ( $customer_orders as $order ) {

			$order = new WC_Order( $order->ID );
			$items = $order->get_items();

			foreach ( $items as $item ) {

				$product = get_post( $item['item_meta']['_product_id'][0] );

				if ( ! $product ) {
					continue;
				}

				$terms = wp_get_post_terms( $product->ID, 'product_cat' );
				$terms = wp_list_pluck( $terms, 'term_id' );

				if ( $categories && count( array_intersect( $terms, $categories ) ) === 0 ) {
					continue;
				}

				$classes .= ( $classes !== '' ? ', ' : '' ) . $product->post_title;
			}
		}

		return $classes;
	}

	private function get_class_list() {

		global $wpdb;

		$classes = array();
		$columns = array();

		if ( $categories = isset( $_POST['pd-categories'] ) ? (array) $_POST['pd-categories'] : false ) {

			$posts = get_posts( array(
				'post_type'   => 'product',
				'numberposts' => - 1,
				'tax_query'   => array(
//					'relation' => 'OR',
					array(
						'taxonomy' => 'product_cat',
						'field' => 'term_id',
						'terms' => $categories,
					),
				),
			) );

			if ( $posts ) {

				$columns = array(
					'Class',
					'Students',
				);

				foreach ( $posts as $post ) {

					$orders = $wpdb->get_results(
						"
						SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id in (SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key='_product_id' and meta_value={$post->ID})
						"
					);

					$students = array();

					if ( $orders ) {
						foreach ( $orders as $order ) {
							if ( $order = new WC_Order( $order->order_id ) ) {
								if ( $customer_ID = get_post_meta( $order->id, '_customer_user', true ) ) {
									$user = new WP_User( $customer_ID );
									$students[ $customer_ID ] = $user->display_name;
								}
							}
						}
					}

					$classes[] = array(
						$post->post_title,
						implode( ',', $students ),
					);
				}
			}
		}

		return array(
			'columns' => $columns,
			'rows' => $classes,
		);
	}

	private function get_export_data() {

		switch ( $_POST['pd-customerdata-which'] ) {

			case 'customer-list':
				$data = $this->get_customer_list();
				break;

			case 'class-list':
				$data = $this->get_class_list();
				break;

			default:
				return false;
		}

		return $data;
	}

	function _do_export() {

		if ( isset( $_POST['pd-customer-data-export'] ) &&
		     wp_verify_nonce( $_POST['pd-customer-data-export'], 'do-export' )
		) {

			$data = $this->get_export_data();

			if ( empty( $data['rows'] ) ) {
				wp_redirect( $_SERVER['REQUEST_URI'] );
				exit();
			}

			$which = isset( $_POST['pd-customerdata-which'] ) ? $_POST['pd-customerdata-which'] : false;
			$filename = $which ? "pd-$which-" . date( 'm-d-Y' ) : false;

			require_once __DIR__ . '/class-pd-customerdata-export.php';
			new PD_CustomerData_Export( $data['columns'], $data['rows'], $filename );
		}
	}

	function _page_output() {
		?>
		<div class="wrap">

			<h2>Customer Export</h2>

			<form method="post">
				<?php wp_nonce_field( 'do-export', 'pd-customer-data-export' ); ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row">List Type</th>
						<td>
							<select name="pd-customerdata-which">
								<option value="customer-list">Customer List</option>
								<option value="class-list">Class List</option>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">Choose Class Categories</th>
						<td>
							<?php
							$product_categories = get_categories( array(
								'taxonomy'     => 'product_cat',
								'orderby'      => 'name',
								'show_count'   => false,
								'pad_counts'   => false,
								'hierarchical' => true,
								'title_li'     => '',
								'hide_empty'   => false,
							) );

							if ( $product_categories ) : ?>
								<select name="pd-categories[]" multiple style="min-height: 300px;">

									<?php foreach ( $product_categories as $category ) : ?>
										<option value="<?php echo $category->term_id; ?>">
											<?php echo $category->name; ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
						</td>
					</tr>
				</table>

				<input type="submit" name="pd-customerdata-export" class="button" value="Export"/>
			</form>
		</div>
	<?php
	}
}