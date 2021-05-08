<?php
/**
 * Admin View: Product import form
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="tista_message content wc-progress-form-content woocommerce-importer woocommerce-importer__importing" style="display:none;">
			
	<header>
		<span class="spinner is-active"></span>
		<h2><?php esc_html_e( 'Importing', 'tista' ); ?></h2>
		<p><?php esc_html_e( 'Demo Contents are now being imported...', 'tista' ); ?></p>
	</header>			
</div>

		<div class="tista_message success" style="display:none;">
		<?php
				include( TISTA_IMPORTER_PATH.'inc/views/html-import-done.php' );
		?>
		</div>
<form id="import_demo_data_form" class="tista_importer wc-progress-form-content woocommerce-importer" enctype="multipart/form-data" method="post">
	<header>
		<h2><?php esc_html_e( 'One Click Demo Uploader', 'tista' ); ?></h2>
	</header>
	<section>
		<table class="form-table woocommerce-importer-options">
			<tbody>
				<tr>
					<td>
						<label for="woocommerce-importer-update-existing">
						<img src="<?php echo TISTA_IMPORTER_URI; ?>/assets/images/demo.png" width ="100%" />
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<label for="woocommerce-importer-update-existing">
						<textarea style="display:none;"  name="tista_import_data" id="tista-import-data" cols="100%" rows="6">{"first_footer_column":{"tista_footer_widget-2":{"title":"Featured ","posts_number":"3 ","category_select":"Hotel"}},"second_footer_column":{"tista_footer_widget-3":{"title":"Featured ","posts_number":"3 ","category_select":"Hotel"}},"third_footer_column":{"tista_footer_widget-4":{"title":"Featured ","posts_number":"3 ","category_select":""}},"page_sidebar_tista":{"tista_search_box_widget-2":{"title":""},"tista_sidebar_widget_cat-2":{"title":"Category","posts_number":"10"},"tista_sidebar_widget_post-2":{"title":"Featured ","posts_number":"10 ","category_select":"Hotel"},"tista_tags_cloud-1":{"title":"Tag Cloud","number":"10 "}},"contact_sidebar_tista":{"tista_address_name-2":{"title":"Address","address":"No.28 - xxxxxx Country","phone_1":"+ 1 (xxxx) xxx 8901","phone_2":"+ 1 (xxxx) 567 xxxx","fax":"+ 1 (234) xxxx xxxxx","email":"support@yoursite.com "}},"tista_footer_fisrt_col_one":{"tista_address_name-3":{"title":"Address","address":"No.28 - xxxxxx Country ","phone_1":"+ 1 (xxxx) xxx 8901","phone_2":"+ 1 (xxxx) 567 xxxx ","fax":"+ 1 (234) xxxx xxxxx ","email":"support@yoursite.com "}},"tista_footer_fisrt_col_two":{"tista_usefull_links-4":{"title":"Usefull Links","link_title":"Home","url":"#","link_1":"Placerat bibendum","url_1":"#","link_2":"Placerat bibendum","url_2":"#","link_3":"Placerat bibendum","url_3":"#","link_4":"Placerat bibendum","url_4":"#"}},"tista_footer_fisrt_col_third":{"tista_usefull_links-5":{"title":"Usefull Links","link_1":"Placerat bibendum","url_1":"#","link_2":"Placerat bibendum","url_2":"#","link_3":"Placerat bibendum","url_3":"#","link_4":"Placerat bibendum","url_4":"#"}}}
						</textarea>
						</label>
					</td>
				</tr>
			</tbody>
		</table>
	</section>
	<div class="wc-actions">
		<input type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Upload Demo', 'tista' ); ?>" name="save_step" />
		<?php wp_nonce_field('tista_import', 'tista_import_nonce'); ?>
	</div>
</form>

<script type="text/javascript">
		jQuery(document).ready(function() {
				jQuery('#import_demo_data_form').on('submit', function() {
								jQuery("html, body").animate({
									scrollTop: 0
								}, {
									duration: 300
								});
								jQuery('.tista_importer').slideUp(null, function(){
									jQuery('.tista_message.content').slideDown();
								});
								
								// Importing Content
								jQuery.ajax({
									type: 'POST',
									url: '<?php echo admin_url('admin-ajax.php'); ?>',
									data: jQuery(this).serialize()+'&action=tista_do_ajax_import',
									success: function(){
										jQuery('.tista_message.content').slideUp();
										jQuery('.tista_message.success').slideDown();

									}
								});
								return false;
				});
		});
</script>
