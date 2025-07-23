<?php
/**
 * Template for Amazon converting HTML page.
 *
 * @link       http://bootstrapped.ventures
 * @since      9.1.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/menu/tools
 */

?>

<div class="wrap wprm-tools">
	<h2><?php esc_html_e( 'Amazon Conversion', 'wp-recipe-maker' ); ?></h2>
	<?php printf( esc_html( _n( 'Searching %d equipment', 'Searching %d equipment', count( $posts ), 'wp-recipe-maker-premium' ) ), count( $posts ) ); ?>.
	<div id="wprm-tools-progress-container">
		<div id="wprm-tools-progress-bar"></div>
	</div>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wprm_manage#equipment' ) ); ?>" id="wprm-tools-finished"><?php esc_html_e( 'Finished succesfully. Click here to continue.', 'wp-recipe-maker' ); ?></a>
</div>
