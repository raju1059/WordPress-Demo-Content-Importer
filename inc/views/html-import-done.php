<?php
/**
 * Admin View: Importer - Done!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wc-progress-form-content woocommerce-importer">
	<header>
		<h2><?php _e( 'Congratulations! Demo Contents Successfully Imported', 'tista' ); ?></h2>
	</header>
	<section>
		<table class="form-table woocommerce-importer-options">
			<tbody>
			<?php	
			if ( tista_have_import_results() ) :
				$results = $tista_import_results;
				foreach ($results as $sidebar) :
			?>
				<tr>
					<td><?php echo esc_html($sidebar['name']); ?></td>
					<td><?php echo esc_html($sidebar['message']); ?></td>
				</tr>
			<?php			
				foreach ($sidebar['widgets'] as $widget) :
			?>
				<tr>
					<td><?php echo esc_html($widget['title']); ?></td>
					<td><?php echo esc_html($widget['message']); ?></td>
				</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</section>
	<div class="wc-actions">
		<a class="button button-primary" href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'View site', 'tista' ); ?></a>
	</div>
</div>