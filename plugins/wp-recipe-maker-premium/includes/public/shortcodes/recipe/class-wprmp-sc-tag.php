<?php
/**
 * Handle the Premium recipe tag shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      6.4.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium recipe tag shortcode.
 *
 * @since      6.4.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Tag {
	public static function init() {
		add_filter( 'wprm_recipe_tag_shortcode_term', array( __CLASS__, 'display' ), 10, 3 );
		add_filter( 'wprm_recipe_tag_shortcode_link', array( __CLASS__, 'link' ), 10, 2 );
	}

	/**
	 * Alter term output display style.
	 *
	 * @since	6.7.0
	 * @param	mixed $output 		Current output.
	 * @param	array $term	 		Term we're outputting.
	 * @param	array $atts	 		Shortcode attributes.
	 */
	public static function display( $output, $term, $atts ) {
		if ( 'images' === $atts['display_style'] || 'text_images' === $atts['display_style'] ) {
			$classes = array(
				'wprm-recipe-tag-term',
				'wprm-recipe-tag-' . $atts['display_style'],
				'wprm-recipe-tag-image-align-' . esc_attr( $atts['image_position'] ),
			);
			$data = '';

			$term_id = is_object( $term ) && isset( $term->term_id ) ? intval( $term->term_id ) : 0;

			if ( $term_id ) {
				// Equipment Image.
				$image_id = intval( get_term_meta( $term_id, 'wprmp_term_image_id', true ) );
				$classes[] = $image_id ? 'wprm-recipe-tag-term-has-image' : 'wprm-recipe-tag-term-no-image';

				$image_output = '';
				if ( $image_id ) {
					$size = $atts['image_size'];
					$force_size = false;

					preg_match( '/^(\d+)x(\d+)(\!?)$/i', $atts['image_size'], $match );
					if ( ! empty( $match ) ) {
						$size = array( intval( $match[1] ), intval( $match[2] ) );
						$force_size = isset( $match[3] ) && '!' === $match[3];
					}
	
					$thumbnail_size = WPRM_Shortcode_Helper::get_thumbnail_image_size( $image_id, $size, $force_size );
					$img = wp_get_attachment_image( $image_id, $thumbnail_size );
	
					// Disable term image pinning.
					if ( WPRM_Settings::get( 'pinterest_nopin_term_image' ) ) {
						$img = str_ireplace( '<img ', '<img data-pin-nopin="true" ', $img );
					}

					// Maybe force image size.
					if ( $force_size ) {
						$style = WPRM_Shortcode_Helper::get_force_image_size_style( $size );
						$img = WPRM_Shortcode_Helper::add_inline_style( $img, $style );
					}
	
					$image_output = '<div class="wprm-recipe-tag-term-image">' . $img . '</div>';

					// Check if a tooltip should be added.
					if ( 'none' !== $atts['image_tooltip'] ) {
						$tooltip = '';

						switch ( $atts['image_tooltip'] ) {
							case 'term':
								$tooltip = $term->name;
								break;
							case 'title':
								$tooltip = get_the_title( $image_id );
								break;
							case 'caption':
								$tooltip = wp_get_attachment_caption( $image_id );
								break;
							case 'description':
								$attachment = get_post( $image_id );
								$tooltip = $attachment->post_content;
								break;
						}

						if ( $tooltip ) {
							$classes[] = 'wprm-tooltip';
							$data = WPRM_Tooltip::get_tooltip_data( $tooltip );
						}
					}
				}

				$text_output = ! $image_output || 'text_images' === $atts['display_style'] ? '<div class="wprm-recipe-tag-term-text">' . $output . '</div>' : '';

				$output = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $data . '>' . $image_output . $text_output . '</div>';
			}
		}

		return $output;
	}

	/**
	 * Add ingredient links.
	 *
	 * @since	6.4.0
	 * @param	mixed $output 		Current output.
	 * @param	array $term	 		Term we're outputting.
	 */
	public static function link( $output, $term ) {
		$term_id = is_object( $term ) && isset( $term->term_id ) ? intval( $term->term_id ) : 0;

		if ( $term_id ) {
			// Easy Affiliate Links integration.
			if ( class_exists( 'EAFL_Link_Manager' ) ) {
				$eafl = get_term_meta( $term_id, 'wprmp_term_eafl', true );

				if ( $eafl ) {
					$eafl_link = EAFL_Link_Manager::get_link( $eafl );

					if ( $eafl_link ) {
						return do_shortcode( '[eafl id="' .  $eafl . '"]' . $output . '[/eafl]' );
					}
				}
			}

			// Regular link.
			$link = get_term_meta( $term_id, 'wprmp_term_link', true );
			$link_nofollow = get_term_meta( $term_id, 'wprmp_term_link_nofollow', true );

			if ( $link ) {
				$link_output = WPRMP_Links::get( $link, $link_nofollow, $output, 'term' );

				if ( $link_output ) {
					return $link_output;
				}
			}
		}

		return $output;
	}
}

WPRMP_SC_Tag::init();