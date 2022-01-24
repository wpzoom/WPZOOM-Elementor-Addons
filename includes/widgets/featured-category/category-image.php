<?php
/**
 * Featured Category Image.
 *
 * Add an image field to categories for use in the Featured Category widget.
 *
 * @package WPZOOM_Elementor_Addons
 * @since   1.2.0
 */

namespace WPZOOMElementorWidgets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Category Image Class.
 *
 * Add an image field to categories for use in the Featured Category widget.
 *
 * @since 1.2.0
 */
class Featured_Category_Image {
	/**
	 * Constructor.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'category_term_new_form_tag', array( $this, 'category_term_form_tag' ) );
		add_action( 'category_term_edit_form_tag', array( $this, 'category_term_form_tag' ) );
		add_action( 'category_add_form_fields', array( $this, 'category_add_form_fields' ) );
		add_action( 'category_edit_form_fields', array( $this, 'category_edit_form_fields' ), 10, 2 );
		add_action( 'created_category', array( $this, 'category_form_custom_field_save' ), 10, 2 );
		add_action( 'edited_category', array( $this, 'category_form_custom_field_save' ), 10, 2 );
	}

	/**
	 * Enqueue scripts in the admin area.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string $hook The hook of the currently displayed admin screen.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'edit-tags.php' === $hook || 'term.php' === $hook ) {
			wp_enqueue_media();

			wp_enqueue_style(
				'wpzoom-elementor-addons-css-backend-featured-category-image',
				plugins_url( 'category-image.css', __FILE__ ),
				array(),
				WPZOOM_EL_ADDONS_VER
			);

			wp_enqueue_script(
				'wpzoom-elementor-addons-js-backend-featured-category-image',
				plugins_url( 'category-image.js', __FILE__ ),
				array( 'jquery' ),
				WPZOOM_EL_ADDONS_VER,
				true
			);

			wp_localize_script(
				'wpzoom-elementor-addons-js-backend-featured-category-image',
				'WPZoomElementorAddons',
				array(
					'selectImageLabel' => esc_html__( 'Select Image', 'wpzoom-elementor-addons' ),
					'chooseImageLabel' => esc_html__( 'Choose Image', 'wpzoom-elementor-addons' ),
				)
			);
		}
	}

	/**
	 * Output custom attributes on form tag of the category edit form.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function category_term_form_tag() {
		echo ' enctype="multipart/form-data" encoding="multipart/form-data"';
	}

	/**
	 * Add some extra fields to the category add form.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function category_add_form_fields() {
		?>
		<div class="form-field term-image-wrap">
			<label for="wpz_category_cover_image_selbtn"><?php esc_html_e( 'Cover Image', 'wpzoom-elementor-addons' ); ?></label>
			<input name="wpz_category_cover_image_id" id="wpz_category_cover_image_id" type="hidden" value="" />
			<span id="wpz_category_cover_image_preview">
				<span id="wpz_category_cover_image_btnwrap">
					<input
						type="button"
						id="wpz_category_cover_image_selbtn"
						class="button"
						value="<?php esc_html_e( 'Choose Image', 'wpzoom-elementor-addons' ); ?>"
					/>

					<input
						type="button"
						id="wpz_category_cover_image_clrbtn"
						class="button disabled"
						value="<?php esc_html_e( 'Clear Image', 'wpzoom-elementor-addons' ); ?>"
					/>
				</span>
			</span>
			<p><?php esc_html_e( 'A cover image that represents the category.', 'wpzoom-elementor-addons' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Add some extra fields to the category edit form.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  WP_Term $tag      Current taxonomy term object.
	 * @param  string  $taxonomy Current taxonomy slug.
	 * @return void
	 */
	public function category_edit_form_fields( $tag, $taxonomy ) {
		$term_meta = get_term_meta( $tag->term_id, 'wpz_cover_image_id', true );
		$image_id  = false !== $term_meta ? absint( $term_meta ) : 0;

		if ( $image_id > 0 ) {
			$attachment = wp_get_attachment_image_src( $image_id, 'full' );

			if ( false !== $attachment && is_array( $attachment ) && count( $attachment ) > 2 ) {
				$image_url    = $attachment[0];
				$image_height = $attachment[2];
			} else {
				$image_url    = '';
				$image_height = '';
			}
		}

		$term_pos = get_term_meta( $tag->term_id, 'wpz_cover_image_pos', true );
		$imgpos   = false !== $term_pos ? trim( $term_pos ) : '';
		$attrs    = $image_id > 0 ? ' class="has-image" style="background-image:url(\'' . esc_url( $image_url ) . '\');height:' . esc_attr( $image_height ) . 'px"' : '';

		// phpcs:disable WordPress.Security.EscapeOutput
		?>
		<tr class="form-field term-image-wrap">
			<th scope="row"><label for="wpz_category_cover_image_selbtn"><?php esc_html_e( 'Cover Image', 'wpzoom-elementor-addons' ); ?></label></th>
			<td>
				<input
					name="wpz_category_cover_image_id"
					id="wpz_category_cover_image_id"
					type="hidden"
					value="<?php echo esc_attr( $image_id > 0 ? $image_id : '' ); ?>"
				/>

				<span id="wpz_category_cover_image_preview"<?php echo $attrs; ?>>
					<span id="wpz_category_cover_image_btnwrap">
						<input
							type="button"
							id="wpz_category_cover_image_selbtn"
							class="button"
							value="<?php esc_html_e( 'Choose Image', 'wpzoom-elementor-addons' ); ?>"
						/>

						<input
							type="button"
							id="wpz_category_cover_image_clrbtn"
							class="button<?php echo esc_attr( $image_id > 0 ? '' : ' disabled' ); ?>"
							value="<?php esc_html_e( 'Clear Image', 'wpzoom-elementor-addons' ); ?>"
						/>
					</span>
				</span>

				<label id="wpz_category_cover_image_pos_label">
					<strong><?php esc_html_e( 'Position:', 'wpzoom-elementor-addons' ); ?></strong>

					<input
						type="text"
						name="wpz_category_cover_image_pos"
						id="wpz_category_cover_image_pos"
						value="<?php echo esc_attr( ! empty( $imgpos ) ? $imgpos : 'center' ); ?>"
					/>

					<small class="howto">
						<em>
							<?php
							printf(
								// translators: URL to documention on valid values for background position.
								__( 'See <a href="%s" rel="noopener noreferrer" target="_blank">here</a> for valid values.', 'wpzoom-elementor-addons' ),
								esc_url( 'https://developer.mozilla.org/docs/Web/CSS/background-position' )
							);
							?>
						</em>
					</small>
				</label>

				<p class="description"><?php esc_html_e( 'A cover image that represents the category.', 'wpzoom-elementor-addons' ); ?></p>
			</td>
		</tr>
		<?php
		// phpcs:enable WordPress.Security.EscapeOutput
	}

	/**
	 * Save custom category image fields.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  int $term_id Term ID.
	 * @param  int $tt_id   Term taxonomy ID.
	 * @return void
	 */
	public function category_form_custom_field_save( $term_id, $tt_id ) {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_POST['wpz_category_cover_image_id'] ) ) {
			update_term_meta(
				$term_id,
				'wpz_cover_image_id',
				( ! empty( $_POST['wpz_category_cover_image_id'] ) ? absint( $_POST['wpz_category_cover_image_id'] ) : '' )
			);
		}

		if ( isset( $_POST['wpz_category_cover_image_pos'] ) ) {
			update_term_meta(
				$term_id,
				'wpz_cover_image_pos',
				( ! empty( $_POST['wpz_category_cover_image_pos'] ) ? sanitize_text_field( wp_unslash( $_POST['wpz_category_cover_image_pos'] ) ) : '' )
			);
		}
		// phpcs:enable WordPress.Security.NonceVerification
	}
}
