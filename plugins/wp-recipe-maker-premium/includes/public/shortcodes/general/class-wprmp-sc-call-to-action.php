<?php
/**
 * Handle the Premium call to action shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/general
 */

/**
 * Handle the Premium call to action shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/general
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Call_to_Action {
	public static function init() {
		add_filter( 'wprm_call_to_action_shortcode', array( __CLASS__, 'shortcode' ), 10, 2 );
	}

	/**
	 * Add the call to action.
	 *
	 * @since	5.6.0	 
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 */
	public static function shortcode( $output, $atts ) {
		$output = '';

		// Get optional icon.
		$icon = '';
		if ( $atts['icon'] ) {
			$icon = WPRM_Icon::get( $atts['icon'], $atts['icon_color'] );

			if ( $icon ) {
				$icon = '<span class="wprm-recipe-icon wprm-call-to-action-icon">' . $icon . '</span> ';
			}
		}

		// Custom container style.
		$style = '';
		$style .= 'color: ' . $atts['text_color'] . ';';
		$style .= $atts['background_color'] ? 'background-color: ' . $atts['background_color'] . ';' : '';
		$style .= 'margin: ' . $atts['margin'] . ';';
		$style .= 'padding-top: ' . $atts['padding'] . ';';
		$style .= 'padding-bottom: ' . $atts['padding'] . ';';

		if ( $atts['border_radius'] && $atts['border_radius'] !== '0px' && $atts['border_radius'] !== '0em' ) {
			$style .= 'border-radius: ' . $atts['border_radius'] . ';';
		}

		// Output.
		$classes = array(
			'wprm-call-to-action',
			'wprm-call-to-action-' . $atts['style'],
		);

		// Add custom class if set.
		if ( $atts['class'] ) { $classes[] = esc_attr( $atts['class'] ); }

		$output .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" style="' . esc_attr( $style ) . '">';

		if ( 'left' === $atts['icon_position'] ) {
			$output .= $icon;
		}
		$output .= '<span class="wprm-call-to-action-text-container">';

		// Optional Header.
		if ( $atts['header'] ) {
			$style = 'color: ' . $atts['header_color'] . ';';
			$tag = WPRM_Shortcode_Helper::sanitize_html_element( $atts['header_tag'] );
			$output .= '<' . $tag . ' class="wprm-call-to-action-header" style="' . esc_attr( $style ) . '">' . WPRM_Shortcode_Helper::sanitize_html( __( $atts['header'], 'wp-recipe-maker' ) ) . '</' . $tag . '>';
		}

		// Social URLs
		$social_urls = array(
			'instagram' => array(
				'handle' => 'https://www.instagram.com/',
				'tag' => 'https://www.instagram.com/explore/tags/',
			),
			'twitter' => array(
				'handle' => 'https://twitter.com/',
				'tag' => 'https://twitter.com/hashtag/',
			),
			'facebook' => array(
				'handle' => 'https://www.facebook.com/',
				'tag' => 'https://www.facebook.com/hashtag/',
			),
			'pinterest' => array(
				'handle' => 'https://www.pinterest.com/',
				'tag' => 'https://www.pinterest.com/search/pins/?rs=hashtag_closeup&q=%23',
			),
		);

		// Main CTA text.
		$output .= '<span class="wprm-call-to-action-text">';
		switch ( $atts['action'] ) {
			case 'instagram':
			case 'twitter':
			case 'facebook':
			case 'pinterest':
				$handle = $atts['social_handle'] ? '<a href="' . $social_urls[ $atts['action'] ]['handle'] . urlencode( $atts['social_handle'] ) . '" target="_blank" rel="noreferrer noopener" style="color: ' . esc_attr( $atts['link_color'] ) . '">@' . $atts['social_handle'] . '</a>' : '';
				$tag = $atts['social_tag'] ? '<a href="' . $social_urls[ $atts['action'] ]['tag'] . urlencode( $atts['social_tag'] ) . '" target="_blank" rel="noreferrer noopener" style="color: ' . esc_attr( $atts['link_color'] ) . '">#' . $atts['social_tag'] . '</a>' : '';

				$text = __( $atts['social_text'], 'wp-recipe-maker' );
				$text = str_ireplace( '%handle%', $handle, $text );
				$text = str_ireplace( '%tag%', $tag, $text );

				$output .= $text;
				break;
			case 'custom':
				$url = $atts['custom_link_url'] ? esc_url_raw( $atts['custom_link_url'] ) : '#';
				$nofollow = 'nofollow' === $atts['custom_link_nofollow'] ? ' rel="nofollow"' : '';
				$link = $atts['custom_link_text'] ? '<a href="' . esc_attr( $url ) . '" target="' . esc_attr( $atts['custom_link_target'] ) . '" style="color: ' . esc_attr( $atts['link_color'] ) . '"' . $nofollow . '>' . __( $atts['custom_link_text'], 'wp-recipe-maker' ) . '</a>' : '';

				$text = __( $atts['custom_text'], 'wp-recipe-maker' );
				$text = str_ireplace( '%link%', $link, $text );

				$output .= $text;
				break;
			case 'rating':
				$modal_uid = WPRMP_User_Rating::get_modal_uid();
				$button = $atts['rating_link_text'] ? '<a href="#" role="button" class="wprm-cta-rating-modal" data-modal-uid="' . esc_attr( $modal_uid ) . '" data-recipe="%recipe_id%" style="color: ' . esc_attr( $atts['link_color'] ) . '">' . __( $atts['rating_link_text'], 'wp-recipe-maker' ) . '</a>' : '';

				$text = __( $atts['rating_text'], 'wp-recipe-maker' );
				$text = str_ireplace( '%link%', $button, $text );

				$output .= $text;
				break;
		}
		$output .= '</span>';

		$output .= '</span>';
		if ( 'right' === $atts['icon_position'] ) {
			$output .= $icon;
		}
		$output .= '</div>';

		// If inside of a recipe card, replace placeholders.
		$recipe = WPRM_Template_Shortcodes::get_recipe( 0 );

		if ( $recipe ) {
			$output = $recipe->replace_placeholders( $output );
		}

		return $output;
	}
}

WPRMP_SC_Call_to_Action::init();