<?php

class SWSales_Banner_Module_Blocks extends SWSales_Banner_Module {
	/**
	 * Set up the module.
	 */
	public static function init() {
		parent::init();

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_swsales_create_reusable_block_banner', array( __CLASS__, 'create_reusable_block_banner_ajax' ) );

		// Set up showing banner on frontend.
		add_action( 'wp', array( __CLASS__, 'choose_banner' ) );
	}

	/**
	 * Enqueues /modules/banners/blocks/swsales-banner-module-blocks-settings.js
	 */
	public static function admin_enqueue_scripts() {
		global $wpdb, $typenow;
		if ( 'sitewide_sale' === $typenow ) {
			wp_register_script( 'swsales_banner_module_blocks_settings', plugins_url( 'modules/banner/blocks/swsales-banner-module-blocks-settings.js', SWSALES_BASENAME ), array( 'jquery' ), SWSALES_VERSION );
			wp_enqueue_script( 'swsales_banner_module_blocks_settings' );

			wp_localize_script(
				'swsales_banner_module_blocks_settings',
				'swsales_blocks',
				array(
					'create_reusable_block_banner_nonce'  => wp_create_nonce( 'swsales_create_reusable_block_banner' ),
					'home_url'                   => home_url(),
					'admin_url'                  => admin_url(),
				)
			);
		}
	}

	/**
	 * Logic for when to show banners/which banner to show
	 */
	public static function choose_banner() {
		// are we previewing?
		$preview = false;
		if ( current_user_can( 'administrator' ) && isset( $_REQUEST['swsales_preview_sale_banner'] ) ) {
			$active_sitewide_sale = Sitewide_Sales\classes\SWSales_Sitewide_Sale::get_sitewide_sale( intval( $_REQUEST['swsales_preview_sale_banner'] ) );
			$preview              = true;
		} else {
			$active_sitewide_sale = self::is_used_by_active_sitewide_sale();
		}
		// Return nothing if the sale isn't active.
		if ( empty( $active_sitewide_sale ) ) {
			return;
		}

		// Get the banner info and linked block.
		$banner_info = self::get_banner_info( $active_sitewide_sale );
		$banner_block = get_post( $banner_info['block_id'] );

		// Unless we are previewing, don't show the banner on certain pages.
		$show_banner = true;
		if ( ! $preview ) {
			$show_banner = self::banner_should_be_shown( $active_sitewide_sale );

			// Don't show if the block is an error or not published.
			if ( is_wp_error( $banner_block ) || get_post_status( $banner_block ) != 'publish' ) {
				$show_banner = false;
			}

			// Don't show on login page.
			if ( Sitewide_Sales\classes\SWSales_Setup::is_login_page() ) {
				$show_banner = false;
			}
		}

		// If the banner module isn't blocks, don't show the banner.
		if ( isset( $banner_info['module'] ) && $banner_info['module'] != 'SWSales_Banner_Module_Blocks' ) {
			$show_banner = false;
		}

		// Return nothing if we shouldn't show the banner.
		if ( empty( $show_banner ) ) {
			return;
		}
		
		// Display the appropriate banner
		$registered_banners = self::get_registered_banners();

		if ( array_key_exists( $banner_info['location'], $registered_banners ) && array_key_exists( 'callback', $registered_banners[ $banner_info['location'] ] ) ) {
			$callback_func = $registered_banners[ $banner_info['location'] ]['callback'];
			if ( is_array( $callback_func ) ) {
				if ( 2 >= count( $callback_func ) ) {
					call_user_func( $callback_func[0] . '::' . $callback_func[1] );
				}
			} elseif ( is_string( $callback_func ) ) {
				if ( is_callable( $callback_func ) ) {
					call_user_func( $callback_func );
				}
			}
		}
	}

	/**
	 * Handles the process of showing a banner.
	 */
	public static function __callStatic( $name, $arguments ) {
		switch ( $name ) {
			case 'hook_top_banner':
				add_action( 'wp_body_open', array( __CLASS__, 'show_top_banner' ) );
				break;
			case 'hook_bottom_banner':
				add_action( 'wp_body_open', array( __CLASS__, 'show_bottom_banner' ) );
				break;
			case 'hook_bottom_right_banner':
				add_action( 'wp_body_open', array( __CLASS__, 'show_bottom_right_banner' ) );
				break;
			case 'show_top_banner':
			case 'show_bottom_banner':
			case 'show_bottom_right_banner':
				if ( current_user_can( 'administrator' ) && isset( $_REQUEST['swsales_preview_sale_banner'] ) ) {
					$active_sitewide_sale = Sitewide_Sales\classes\SWSales_Sitewide_Sale::get_sitewide_sale( intval( $_REQUEST['swsales_preview_sale_banner'] ) );
				} else {
					$active_sitewide_sale = Sitewide_Sales\classes\SWSales_Sitewide_Sale::get_active_sitewide_sale();
				}
				// Get the banner info.
				$banner_info = self::get_banner_info( $active_sitewide_sale );

				// Get the banner content.
				$banner_block = get_post( $banner_info['block_id'] );
				$banner_content = do_blocks( $banner_block->post_content );

				ob_start();
				?>
				<div id="swsales-banner-block-<?php esc_html_e( str_replace( '_', '-', $banner_info['location'] ) ); ?>" class="swsales-banner swsales-banner-block" style="display: none;">
					<?php
						switch ( $name ) {
							case 'show_top_banner':
								echo $banner_content;
								break;
							case 'show_bottom_banner':
								?>
								<a href="javascript:void(0);" onclick="document.getElementById('swsales-banner-block-bottom').style.display = 'none';" class="swsales-dismiss" title="Dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'sitewide-sales' ); ?></a>
								<?php echo $banner_content; ?>
								<?php
								break;
							case 'show_bottom_right_banner':
								?>
								<a href="javascript:void(0);" onclick="document.getElementById('swsales-banner-block-bottom-right').style.display = 'none';" class="swsales-dismiss" title="Dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'sitewide-sales' ); ?></a>
								<?php echo $banner_content; ?>								
								<?php
								break;
						}
					?>
				</div> <!-- end swsales-banner -->
				<?php

				$content = ob_get_contents();
				ob_end_clean();

				// Filter for themes and plugins to modify the banner content.
				$content = apply_filters( 'swsales_banner_content', $content, $banner_info['template'], 'top' );

				// Echo the banner content.	
				echo $content;
				break;
			default:
				// Throw exception if method not supported.
				throw new Exception('The ' . $name . ' method is not supported.');
		}
	}

	/**
	 * Returns a human-readable name for this module.
	 *
	 * @return string
	 */
	protected static function get_module_label() {
		return __( 'Reusable Block', 'sitewide-sales' );
	}

	/**
	 * Returns whether the plugin associaited with this module is active.
	 *
	 * @return bool
	 */
	protected static function is_module_active() {
		return true;
	}

	/**
	 * Echos the HTML for the settings that should be displayed
	 * if this module is active and selected while editing a
	 * sitewide sale.
	 *
	 * @param SWSales_Sitewide_Sale $sitewide_sale The sale being edited.
	 */
	public static function echo_banner_settings_html_inner( $sitewide_sale ) {
		// Gather information information needed to display settings.
		$banner_info          = self::get_banner_info( $sitewide_sale );
		$registered_locations = self::get_registered_banners();

		// Query to get all reusable blocks for dropdown.
		$args = array(
			'order' => 'ASC',
			'orderby' => 'title',
			'posts_per_page' => -1,
			'post_status' => array( 'draft', 'publish' ),
			'post_type' => 'wp_block'
		);
		$all_reusable_blocks = new WP_Query( $args );
		?>
		<tr>
			<th scope="row" valign="top"><label><?php esc_html_e( 'Reusable Block', 'sitewide-sales' ); ?></label></th>
			<td>
				<?php
					$block_found = false;
					if ( $all_reusable_blocks->have_posts() ) { ?>
						<select class="swsales_option" id="swsales_banner_block_id" name="swsales_banner_block_id">
							<option value="0"><?php esc_html_e( '- Choose One -', 'sitewide-sales' ); ?></option>
							<?php
								while ( $all_reusable_blocks->have_posts() ) {
									$all_reusable_blocks->the_post();
									if ( selected( $banner_info['block_id'], $all_reusable_blocks->post->ID, false ) ) {
										$block_found = true;
									}
									if ( $all_reusable_blocks->post->post_status == 'draft' ) {
										$status_part = ' (' . esc_html__( 'Draft', 'sitewide-sales' ) . ')';
									} else {
										$status_part = '';
									}
									echo '<option value="' . esc_attr( $all_reusable_blocks->post->ID ) . '"' . selected( $banner_info['block_id'], $all_reusable_blocks->post->ID ) . '>' . esc_html( $all_reusable_blocks->post->post_title ) . $status_part . '</option>';
								}
							?>
						</select>
					<?php
						wp_reset_postdata();
						} else { ?>
							<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
						<?php
						}
					?>
				<p>
					<span id="swsales_after_reusable_block_select" 
					<?php
					if ( ! $block_found ) {
						?>
style="display: none;"<?php } ?>>
					<?php
						$edit_block_url = admin_url( 'post.php?post=' . $banner_info['block_id'] . '&action=edit' );
					?>
					<a target="_blank" class="button button-secondary" id="swsales_edit_banner_block" href="<?php echo esc_url( $edit_block_url ); ?>"><?php esc_html_e( 'edit block', 'sitewide-sales' ); ?></a>
					<input type="submit" class="button button-secondary" id="swsales_preview" name="swsales_preview" value="<?php esc_attr_e( 'save and preview', 'sitewide-sales' ); ?>">
					<?php
						esc_html_e( ' or ', 'sitewide-sales' );
					?>
					</span>
					<button type="button" id="swsales_create_reusable_block_banner" class="button button-secondary"><?php esc_html_e( 'create a new reusable block', 'sitewide-sales' ); ?></button>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label><?php esc_html_e( 'Banner Location', 'sitewide-sales' ); ?></label></th>
			<td>
				<select class="swsales_option" name="swsales_banner_block_location">
					<?php
					foreach ( $registered_locations as $registered_location_slug => $registered_location_data ) {
						if ( is_string( $registered_location_slug ) && is_array( $registered_location_data ) && ! empty( $registered_location_data['option_title'] ) && is_string( $registered_location_data['option_title'] ) ) {
							echo '<option value="' . esc_attr( $registered_location_slug ) . '"' . selected( $banner_info['location'], $registered_location_slug ) . '>' . esc_html( $registered_location_data['option_title'] ) . '</option>';
						}
					}
					?>
				</select>
			</td>
		</tr>
		<?php
    }

	/**
	 * Saves settings shown by echo_banner_settings_html_inner().
	 *
	 * @param int     $post_id The ID of the post being saved.
	 * @param WP_Post $post The post being saved.
	 */
	protected static function save_banner_settings( $post_id, $post ) {
		if ( isset( $_POST['swsales_banner_block_location'] ) ) {
			update_post_meta( $post_id, 'swsales_banner_block_location', sanitize_text_field( $_POST['swsales_banner_block_location'] ) );
		}
		if ( isset( $_POST['swsales_banner_block_id'] ) ) {
			update_post_meta( $post_id, 'swsales_banner_block_id', sanitize_text_field( $_POST['swsales_banner_block_id'] ) );
		}
    }

    /**
	 * Get banner info for the given sitewide sale.
	 *
	 * @param SWSales_Sitewide_Sale $sitewide_sale The sitewide sale to get the banner info for.
	 * @return array The banner info.
	 */
	private static function get_banner_info( $sitewide_sale ) {
		$banner_info = array();
		$banner_info['module'] = $sitewide_sale->get_meta_value( 'swsales_banner_module' );
		$banner_info['block_id'] = $sitewide_sale->get_meta_value( 'swsales_banner_block_id' );
		$banner_info['location'] = $sitewide_sale->get_meta_value( 'swsales_banner_block_location' );
		// Update location in case we are previewing.
		if ( ! is_admin() && current_user_can( 'administrator' ) && isset( $_REQUEST['swsales_preview_sale_banner_type'] ) ) {
			$banner_info['location'] = $_REQUEST['swsales_preview_sale_banner_type'];
		}

		// If the blocks module is used, there is no template.
		$banner_info['template'] = '';

		$banner_info['close_behavior'] = $sitewide_sale->get_meta_value( 'swsales_banner_close_behavior' );

		return $banner_info;
	}

	/**
	 * Gets info about available banners including name and available
	 * css selectors.
	 *
	 * @return array banner_name => array( option_title=>string, callback=>string )
	 */
	private static function get_registered_banners() {
		$registered_banners = array(
			'bottom_right' => array(
				'option_title'  => __( 'Bottom Right of Site', 'sitewide-sales' ),
				'callback'      => array( __CLASS__, 'hook_bottom_right_banner' ),
			),
			'bottom'       => array(
				'option_title'  => __( 'Bottom of Site', 'sitewide-sales' ),
				'callback'      => array( __CLASS__, 'hook_bottom_banner' ),
			),
			'top'          => array(
				'option_title'  => __( 'Top of Site', 'sitewide_Sales' ),
				'callback'      => array( __CLASS__, 'hook_top_banner' ),
			),
		);
		return $registered_banners;
	}

	/**
	 * AJAX callback to create a new reusable block banner for your sale
	 */
	public static function create_reusable_block_banner_ajax() {
		check_ajax_referer( 'swsales_create_reusable_block_banner', 'nonce' );

		$sitewide_sale_id = intval( $_REQUEST['swsales_id'] );
		if ( empty( $sitewide_sale_id ) ) {
			echo json_encode(
				array(
					'status' => 'error',
					'error'  => esc_html__(
						'No sitewide sale ID given. Try doing it manually.',
						'sitewide-sales'
					),
				)
			);
			exit;
		}

		$reusable_block_banner_title = sanitize_text_field( $_REQUEST['swsales_reusable_block_banner_title'] );
		if ( empty( $reusable_block_banner_title ) ) {
			$reusable_block_banner_title = esc_html__( 'Sitewide Sale Reusable Block Banner', 'sitewide-sales' );
		}

		$reusable_block_banner_post_id = wp_insert_post(
			array(
				'post_title'    => $reusable_block_banner_title,
				'post_content'  => '<!-- wp:group {\"backgroundColor\":\"black\",\"textColor\":\"white\",\"className\":\"swsales-padding\"} --><div class=\"wp-block-group swsales-padding has-white-color has-black-background-color has-text-color has-background\"><!-- wp:heading {\"level\":3,\"textColor\":\"white\"} --><h3 class=\"has-white-color has-text-color\">Limited Time Offer</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Save 50% on your first year of membership!</p><!-- /wp:paragraph --><!-- wp:buttons --><div class=\"wp-block-buttons\"><!-- wp:button {\"width\":100} --><div class=\"wp-block-button has-custom-width wp-block-button__width-100\"><a class=\"wp-block-button__link\">Buy Now</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->',
				'post_type'     => 'wp_block',
				'post_status'   => 'publish',
			)
		);

		if ( empty( $reusable_block_banner_post_id ) ) {
			$r = array(
				'status' => 'error',
				'error'  => esc_html__( 'Error inserting post. Try doing it manually.', 'sitewide-sales' ),
			);
		} else {
			$r = array(
				'status' => 'success',
				'post'   => get_post( $reusable_block_banner_post_id ),
			);
		}

		echo json_encode( $r );
		exit;
	}
}
SWSales_Banner_Module_Blocks::init();