<?php
/**
 * Responsible for showing admin notices related to the Premium plugin.
 *
 * @link       https://bootstrapped.ventures
 * @since      9.3.1
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/admin
 */

/**
 * Responsible for showing admin notices related to the Premium plugin.
 *
 * @since      9.3.1
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/admin
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_Notices {

	/**
	 * Register actions and filters.
	 *
	 * @since    9.3.1
	 */
	public static function init() {
		add_filter( 'wprm_admin_notices', array( __CLASS__, 'user_ratings_notice' ) );
	}

	/**
	 * Show the user ratings notice.
	 *
	 * @since	9.3.1
	 * @param	array $notices Existing notices.
	 */
	public static function user_ratings_notice( $notices ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		// Only load on manage page.
		if ( $screen && 'wp-recipe-maker_page_wprm_manage' === $screen->id ) {
			if ( WPRM_Settings::get( 'features_user_ratings' ) ) {
				$notices[] = array(
					'id' => 'user_ratings_forced_comment',
					'title' => __( 'All ratings require a comment now', 'wp-recipe-maker' ),
					'text' => '<p>As warned in our previous updates, Google does not consider anonymous ratings to be trustworthy anymore. That is why WP Recipe Maker is now going to <strong>require a comment, name and email alongside any rating by default</strong>. These ratings can be given directly in the comment form or through the <a href="https://help.bootstrapped.ventures/article/27-user-ratings">User Ratings Modal</a>.</p><p>While getting a lot of anonymous ratings was definitely fun, having hundreds of 5 stars without knowing who gave these stars or what they actually thought of the recipe was not useful to visitors, Google or site owners. It also made it too easy for spammy sites to inflate their numbers, leading to unfair advantages.</p><p>While you might get fewer votes now, the ones you do get will provide you with actual feedback and you\'ll know that there is a real person on the other end providing it. It also allows us to <a href="https://bootstrapped.ventures/wp-recipe-maker-9-4-0/">improve the recipe metadata by including the actual review</a>.</p><p>Settings to change the required fields can be found on the <em>WP Recipe Maker > Settings > Star Ratings</em> page.</p>',
				);
			}
		}

		return $notices;
	}
}

WPRMP_Notices::init();
