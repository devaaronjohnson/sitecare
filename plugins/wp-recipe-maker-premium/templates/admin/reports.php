<?php
/**
 * Template for Premium reports page.
 *
 * @link       https://bootstrapped.ventures
 * @since      9.5.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/templates/admin
 */

?>
<?php if ( WPRM_Addons::is_active( 'elite' ) ) : ?>
<tr>
	<th scope="row">
		<?php esc_html_e( 'Recipe Collections', 'wp-recipe-maker-premium' ); ?>
	</th>
	<td>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wprm_report_recipe_collections' ) ); ?>" class="button" id="report_recipe_collections"><?php esc_html_e( 'Generate Recipe Collections Usage Report', 'wp-recipe-maker-premium' ); ?></a>
		<p class="description" id="tagline-report_recipe_collections">
			<?php esc_html_e( 'Take note that this can only take collections created by logged in users into account.', 'wp-recipe-maker-premium' ); ?>
		</p>
	</td>
</tr>
<?php endif; ?>