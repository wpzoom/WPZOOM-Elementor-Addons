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
	 * Check if Inspiro Premium theme is active.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return bool
	 */
	public static function is_inspiro_theme() {
		$current_theme = get_template();
		// Check for Inspiro Premium only (not Inspiro Pro)
		return 'inspiro' === $current_theme && class_exists( 'WPZOOM' );
	}

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
					'selectVideoLabel' => esc_html__( 'Select Video', 'wpzoom-elementor-addons' ),
					'chooseVideoLabel' => esc_html__( 'Choose Video', 'wpzoom-elementor-addons' ),
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
			<input name="wpz_category_cover_image_pos" id="wpz_category_cover_image_pos" type="hidden" value="50% 50%" />
			<span id="wpz_category_cover_image_preview">
				<span class="wpz-focal-point-picker"></span>
				<span class="wpz-focal-point-hint"><?php esc_html_e( 'Click to set focal point', 'wpzoom-elementor-addons' ); ?></span>
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
			<p><?php
				if ( self::is_inspiro_theme() ) {
					esc_html_e( 'A cover image that represents the category. Click on the image to set the focal point. This will also serve as a poster image for video backgrounds.', 'wpzoom-elementor-addons' );
				} else {
					esc_html_e( 'A cover image that represents the category. Click on the image to set the focal point.', 'wpzoom-elementor-addons' );
				}
			?></p>

			<?php if ( self::is_inspiro_theme() ) : ?>
			<div class="wpz-cat-video-section">
				<h4><?php esc_html_e( 'Video Background in Header', 'wpzoom-elementor-addons' ); ?></h4>
				<p class="description"><?php esc_html_e( 'Configure a video which will play in the background of the header area on this category archive page.', 'wpzoom-elementor-addons' ); ?></p>

				<div class="wpz-cat-video-radio-group">
					<strong><?php esc_html_e( 'Video Source:', 'wpzoom-elementor-addons' ); ?></strong><br>
					<label><input type="radio" name="wpz_category_video_type" value="" checked> <?php esc_html_e( 'None', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="radio" name="wpz_category_video_type" value="self_hosted"> <?php esc_html_e( 'MP4 File', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="radio" name="wpz_category_video_type" value="external_hosted"> <?php esc_html_e( 'YouTube', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="radio" name="wpz_category_video_type" value="vimeo_pro"> <?php esc_html_e( 'Vimeo', 'wpzoom-elementor-addons' ); ?></label>
				</div>

				<div class="wpz-cat-video-field wpz-cat-video-mp4-field hidden">
					<label><strong><?php esc_html_e( 'MP4 Video URL:', 'wpzoom-elementor-addons' ); ?></strong></label><br>
					<button type="button" class="button wpz-cat-video-upload-btn"><?php esc_html_e( 'Upload Video', 'wpzoom-elementor-addons' ); ?></button>
					<input type="text" name="wpz_category_video_mp4" value="" />
					<p class="description"><?php esc_html_e( 'H.264 video encoding required.', 'wpzoom-elementor-addons' ); ?></p>
				</div>

				<div class="wpz-cat-video-field wpz-cat-video-youtube-field hidden">
					<label><strong><?php esc_html_e( 'YouTube Video URL:', 'wpzoom-elementor-addons' ); ?></strong></label><br>
					<input type="text" name="wpz_category_video_youtube" value="" placeholder="https://www.youtube.com/watch?v=..." />
					<p class="description"><?php esc_html_e( 'Full YouTube URL only, no shortlinks. YouTube videos are not supported on mobile devices.', 'wpzoom-elementor-addons' ); ?></p>
				</div>

				<div class="wpz-cat-video-field wpz-cat-video-vimeo-field hidden">
					<label><strong><?php esc_html_e( 'Vimeo Video URL:', 'wpzoom-elementor-addons' ); ?></strong></label><br>
					<input type="text" name="wpz_category_video_vimeo" value="" placeholder="https://vimeo.com/..." />
					<p class="description"><?php esc_html_e( 'Works best with Vimeo PLUS, PRO or Business accounts. Only public videos supported.', 'wpzoom-elementor-addons' ); ?></p>
				</div>

				<div class="wpz-cat-video-checkboxes">
					<strong><?php esc_html_e( 'Video Options:', 'wpzoom-elementor-addons' ); ?></strong>
					<label><input type="checkbox" name="wpz_category_video_autoplay" value="1" checked> <?php esc_html_e( 'Autoplay Video', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="checkbox" name="wpz_category_video_mute" value="1" checked> <?php esc_html_e( 'Mute Video', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="checkbox" name="wpz_category_video_loop" value="1" checked> <?php esc_html_e( 'Loop Video', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="checkbox" name="wpz_category_video_play_button" value="1" checked> <?php esc_html_e( 'Show Play/Pause Button', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="checkbox" name="wpz_category_video_mute_button" value="1" checked> <?php esc_html_e( 'Show Mute/Unmute Button', 'wpzoom-elementor-addons' ); ?></label>
				</div>
			</div>
			<?php endif; ?>
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
		$image_url = '';

		if ( $image_id > 0 ) {
			$attachment = wp_get_attachment_image_src( $image_id, 'full' );

			if ( false !== $attachment && is_array( $attachment ) && count( $attachment ) > 2 ) {
				$image_url = $attachment[0];
			}
		}

		$term_pos = get_term_meta( $tag->term_id, 'wpz_cover_image_pos', true );
		$imgpos   = false !== $term_pos ? trim( $term_pos ) : '50% 50%';

		// Parse focal point position for display
		$focal_x = 50;
		$focal_y = 50;
		if ( ! empty( $imgpos ) && strpos( $imgpos, '%' ) !== false ) {
			$parts = preg_split( '/\s+/', trim( $imgpos ) );
			if ( count( $parts ) >= 2 ) {
				$focal_x = floatval( $parts[0] );
				$focal_y = floatval( $parts[1] );
			}
		}
		$focal_point_style = 'left:' . $focal_x . '%;top:' . $focal_y . '%';
		$bg_pos_style      = ! empty( $imgpos ) ? 'background-position:' . esc_attr( $imgpos ) . ';' : '';

		$attrs = $image_id > 0 ? ' class="has-image" style="background-image:url(\'' . esc_url( $image_url ) . '\');' . $bg_pos_style . '"' : '';

		// Video background settings
		$video_type        = get_term_meta( $tag->term_id, 'wpz_cover_video_type', true );
		$video_mp4         = get_term_meta( $tag->term_id, 'wpz_cover_video_mp4', true );
		$video_youtube     = get_term_meta( $tag->term_id, 'wpz_cover_video_youtube', true );
		$video_vimeo       = get_term_meta( $tag->term_id, 'wpz_cover_video_vimeo', true );
		$video_autoplay    = get_term_meta( $tag->term_id, 'wpz_cover_video_autoplay', true );
		$video_mute        = get_term_meta( $tag->term_id, 'wpz_cover_video_mute', true );
		$video_loop        = get_term_meta( $tag->term_id, 'wpz_cover_video_loop', true );
		$video_play_button = get_term_meta( $tag->term_id, 'wpz_cover_video_play_button', true );
		$video_mute_button = get_term_meta( $tag->term_id, 'wpz_cover_video_mute_button', true );

		// Default values for checkboxes (checked by default if not set)
		$video_autoplay    = ( '' === $video_autoplay || false === $video_autoplay ) ? '1' : $video_autoplay;
		$video_mute        = ( '' === $video_mute || false === $video_mute ) ? '1' : $video_mute;
		$video_loop        = ( '' === $video_loop || false === $video_loop ) ? '1' : $video_loop;
		$video_play_button = ( '' === $video_play_button || false === $video_play_button ) ? '1' : $video_play_button;
		$video_mute_button = ( '' === $video_mute_button || false === $video_mute_button ) ? '1' : $video_mute_button;

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
				<input
					name="wpz_category_cover_image_pos"
					id="wpz_category_cover_image_pos"
					type="hidden"
					value="<?php echo esc_attr( ! empty( $imgpos ) ? $imgpos : '50% 50%' ); ?>"
				/>

				<span id="wpz_category_cover_image_preview"<?php echo $attrs; ?>>
					<span class="wpz-focal-point-picker" style="<?php echo esc_attr( $focal_point_style ); ?>"></span>
					<span class="wpz-focal-point-hint"><?php esc_html_e( 'Click to set focal point', 'wpzoom-elementor-addons' ); ?></span>
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

				<p class="description"><?php
					if ( self::is_inspiro_theme() ) {
						esc_html_e( 'A cover image that represents the category. Click on the image to set the focal point. This will also serve as a poster image for video backgrounds.', 'wpzoom-elementor-addons' );
					} else {
						esc_html_e( 'A cover image that represents the category. Click on the image to set the focal point.', 'wpzoom-elementor-addons' );
					}
				?></p>
			</td>
		</tr>
		<?php if ( self::is_inspiro_theme() ) : ?>
		<tr class="form-field term-video-wrap">
			<th scope="row"><label><?php esc_html_e( 'Video Background in Header', 'wpzoom-elementor-addons' ); ?></label></th>
			<td>
				<p class="description"><?php esc_html_e( 'Configure a video which will play in the background of the header area on this category archive page.', 'wpzoom-elementor-addons' ); ?></p>

				<div class="wpz-cat-video-radio-group" style="margin: 15px 0;">
					<strong><?php esc_html_e( 'Video Source:', 'wpzoom-elementor-addons' ); ?></strong><br>
					<label><input type="radio" name="wpz_category_video_type" value="" <?php checked( $video_type, '' ); ?>> <?php esc_html_e( 'None', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="radio" name="wpz_category_video_type" value="self_hosted" <?php checked( $video_type, 'self_hosted' ); ?>> <?php esc_html_e( 'MP4 File', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="radio" name="wpz_category_video_type" value="external_hosted" <?php checked( $video_type, 'external_hosted' ); ?>> <?php esc_html_e( 'YouTube', 'wpzoom-elementor-addons' ); ?></label>
					<label><input type="radio" name="wpz_category_video_type" value="vimeo_pro" <?php checked( $video_type, 'vimeo_pro' ); ?>> <?php esc_html_e( 'Vimeo', 'wpzoom-elementor-addons' ); ?></label>
				</div>

				<div class="wpz-cat-video-field wpz-cat-video-mp4-field hidden" style="margin-bottom: 15px;">
					<label><strong><?php esc_html_e( 'MP4 Video URL:', 'wpzoom-elementor-addons' ); ?></strong></label><br>
					<button type="button" class="button wpz-cat-video-upload-btn"><?php esc_html_e( 'Upload Video', 'wpzoom-elementor-addons' ); ?></button>
					<input type="text" name="wpz_category_video_mp4" value="<?php echo esc_attr( $video_mp4 ); ?>" style="width: 100%; max-width: 400px;" />
					<p class="description"><?php esc_html_e( 'H.264 video encoding required.', 'wpzoom-elementor-addons' ); ?></p>
				</div>

				<div class="wpz-cat-video-field wpz-cat-video-youtube-field hidden" style="margin-bottom: 15px;">
					<label><strong><?php esc_html_e( 'YouTube Video URL:', 'wpzoom-elementor-addons' ); ?></strong></label><br>
					<input type="text" name="wpz_category_video_youtube" value="<?php echo esc_attr( $video_youtube ); ?>" placeholder="https://www.youtube.com/watch?v=..." style="width: 100%; max-width: 400px;" />
					<p class="description"><?php esc_html_e( 'Full YouTube URL only, no shortlinks. YouTube videos are not supported on mobile devices.', 'wpzoom-elementor-addons' ); ?></p>
				</div>

				<div class="wpz-cat-video-field wpz-cat-video-vimeo-field hidden" style="margin-bottom: 15px;">
					<label><strong><?php esc_html_e( 'Vimeo Video URL:', 'wpzoom-elementor-addons' ); ?></strong></label><br>
					<input type="text" name="wpz_category_video_vimeo" value="<?php echo esc_attr( $video_vimeo ); ?>" placeholder="https://vimeo.com/..." style="width: 100%; max-width: 400px;" />
					<p class="description"><?php esc_html_e( 'Works best with Vimeo PLUS, PRO or Business accounts. Only public videos supported.', 'wpzoom-elementor-addons' ); ?></p>
				</div>

				<div class="wpz-cat-video-checkboxes" style="margin-top: 15px;">
					<strong><?php esc_html_e( 'Video Options:', 'wpzoom-elementor-addons' ); ?></strong>
					<label style="display: block; margin: 5px 0;"><input type="hidden" name="wpz_category_video_autoplay" value="0"><input type="checkbox" name="wpz_category_video_autoplay" value="1" <?php checked( $video_autoplay, '1' ); ?>> <?php esc_html_e( 'Autoplay Video', 'wpzoom-elementor-addons' ); ?></label>
					<label style="display: block; margin: 5px 0;"><input type="hidden" name="wpz_category_video_mute" value="0"><input type="checkbox" name="wpz_category_video_mute" value="1" <?php checked( $video_mute, '1' ); ?>> <?php esc_html_e( 'Mute Video (recommended for autoplay)', 'wpzoom-elementor-addons' ); ?></label>
					<label style="display: block; margin: 5px 0;"><input type="hidden" name="wpz_category_video_loop" value="0"><input type="checkbox" name="wpz_category_video_loop" value="1" <?php checked( $video_loop, '1' ); ?>> <?php esc_html_e( 'Loop Video', 'wpzoom-elementor-addons' ); ?></label>
					<label style="display: block; margin: 5px 0;"><input type="hidden" name="wpz_category_video_play_button" value="0"><input type="checkbox" name="wpz_category_video_play_button" value="1" <?php checked( $video_play_button, '1' ); ?>> <?php esc_html_e( 'Show Play/Pause Button', 'wpzoom-elementor-addons' ); ?></label>
					<label style="display: block; margin: 5px 0;"><input type="hidden" name="wpz_category_video_mute_button" value="0"><input type="checkbox" name="wpz_category_video_mute_button" value="1" <?php checked( $video_mute_button, '1' ); ?>> <?php esc_html_e( 'Show Mute/Unmute Button', 'wpzoom-elementor-addons' ); ?></label>
				</div>
			</td>
		</tr>
		<?php endif; ?>
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

		// Save video background settings (only for Inspiro theme)
		if ( self::is_inspiro_theme() ) {
			if ( isset( $_POST['wpz_category_video_type'] ) ) {
				update_term_meta( $term_id, 'wpz_cover_video_type', sanitize_text_field( wp_unslash( $_POST['wpz_category_video_type'] ) ) );
			}
			if ( isset( $_POST['wpz_category_video_mp4'] ) ) {
				update_term_meta( $term_id, 'wpz_cover_video_mp4', esc_url_raw( wp_unslash( $_POST['wpz_category_video_mp4'] ) ) );
			}
			if ( isset( $_POST['wpz_category_video_youtube'] ) ) {
				update_term_meta( $term_id, 'wpz_cover_video_youtube', esc_url_raw( wp_unslash( $_POST['wpz_category_video_youtube'] ) ) );
			}
			if ( isset( $_POST['wpz_category_video_vimeo'] ) ) {
				$vimeo_url = esc_url_raw( wp_unslash( $_POST['wpz_category_video_vimeo'] ) );
				update_term_meta( $term_id, 'wpz_cover_video_vimeo', $vimeo_url );
				// Extract and store Vimeo video ID
				$vimeo_id = '';
				if ( ! empty( $vimeo_url ) && preg_match( '/vimeo\.com\/(\d+)/', $vimeo_url, $matches ) ) {
					$vimeo_id = $matches[1];
				}
				update_term_meta( $term_id, 'wpz_cover_vimeo_id', $vimeo_id );
			}
			if ( isset( $_POST['wpz_category_video_autoplay'] ) ) {
				update_term_meta( $term_id, 'wpz_cover_video_autoplay', sanitize_text_field( wp_unslash( $_POST['wpz_category_video_autoplay'] ) ) );
			}
			if ( isset( $_POST['wpz_category_video_mute'] ) ) {
				update_term_meta( $term_id, 'wpz_cover_video_mute', sanitize_text_field( wp_unslash( $_POST['wpz_category_video_mute'] ) ) );
			}
			if ( isset( $_POST['wpz_category_video_loop'] ) ) {
				update_term_meta( $term_id, 'wpz_cover_video_loop', sanitize_text_field( wp_unslash( $_POST['wpz_category_video_loop'] ) ) );
			}
			if ( isset( $_POST['wpz_category_video_play_button'] ) ) {
				update_term_meta( $term_id, 'wpz_cover_video_play_button', sanitize_text_field( wp_unslash( $_POST['wpz_category_video_play_button'] ) ) );
			}
			if ( isset( $_POST['wpz_category_video_mute_button'] ) ) {
				update_term_meta( $term_id, 'wpz_cover_video_mute_button', sanitize_text_field( wp_unslash( $_POST['wpz_category_video_mute_button'] ) ) );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification
	}
}
