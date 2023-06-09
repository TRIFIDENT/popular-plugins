<?php
/**
 * Feed List View
 *
 * @link       https://webappick.com/
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/admin/partial
 * @author     Ohidul Islam <wahid@webappick.com>
 */
$myListTable = new Woo_Feed_Manage_list();
$myListTable->prepare_items();
$limit       = woo_feed_get_options( 'per_batch' );
$fileName    = '';
$message     = array();
global $regenerating, $regeneratingName, $plugin_page;
$regenerating = false;

$feed_created   = apply_filters('CTXFEED_filter_securing_input', "GET",    @$_GET['feed_created'],    "text");
$feed_updated   = apply_filters('CTXFEED_filter_securing_input', "GET",    @$_GET['feed_updated'],    "text");
$feed_imported   = apply_filters('CTXFEED_filter_securing_input', "GET",    @$_GET['feed_imported'],    "text");
$feed_regenerate   = apply_filters('CTXFEED_filter_securing_input', "GET",    @$_GET['feed_regenerate'],    "text");
$feed_name   = apply_filters('CTXFEED_filter_securing_input', "GET",    @$_GET['feed_name'],    "file_name");

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( ( $feed_created || $feed_updated || $feed_imported ) &&  1 == $feed_regenerate ) {
	// filename must be wf_config+XXX format for js to work.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$fileName = $feed_name ?  wp_unslash($feed_name)  : ''; // trigger feed regenerate...
	if ( ! empty( $fileName ) ) {
		$fileName         = woo_feed_extract_feed_option_name( $fileName );
		$regeneratingName = $fileName;
		$fileName         = 'wf_config' . $fileName; // to be safe...
		$regenerating     = true;
	}
}
?>
<div class="wrap wapk-admin">
	<div class="wapk-section">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Manage Feed', 'woo-feed' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=webappick-new-feed' ) ); ?>" class="page-title-action woo-feed-btn-bg-gradient-blue"><?php esc_html_e( 'New Feed', 'woo-feed' ); ?></a>
		<a href="#TB_inline?&width=300&height=152&inlineId=wpf_importer" name="Import Feed Config" class="thickbox page-title-action woo-feed-btn-bg-gradient-blue"><?php esc_html_e( 'Import Feed', 'woo-feed' ); ?></a>
		<div id="wpf_importer" style="display: none;">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php?action=wpf_import' ) ); ?>" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'wpf_import' ); ?>
				<!-- <input type="file" name="wpf_import_file" id="wpf_import_file" accept=".wpf" onchange="this.form.submit()">-->
				<table class="fixed widefat">
					<tr>
						<td colspan="2">
							<label for="wpf_import_file" class="screen-reader-text"><?php esc_html_e( 'Import Feed File', 'woo-feed' ); ?></label>
							<input type="file" name="wpf_import_file" id="wpf_import_file" accept=".wpf" required>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="wpf_import_feed_name" class="screen-reader-text"><?php esc_html_e( 'Feed File Name', 'woo-feed' ); ?></label>
							<input type="text" class="regular-text" name="wpf_import_feed_name" id="wpf_import_feed_name" placeholder="<?php esc_attr_e( 'Feed File Name', 'woo-feed' ); ?>" required>
						</td>
					</tr>
					<tr class="text-center">
						<td colspan="2">
							<input type="submit" class="button button-primary" id="wpf_import_submit" value="<?php esc_attr_e( 'Import Now', 'woo-feed' ); ?>">
						</td>
					</tr>
				</table>
			</form>
		</div>
		<hr class="wp-header-end">
		<?php WPFFWMessage()->displayMessages(); ?>
        <?php woo_feed_progress_bar(); ?>
		<table class="woo_feed_manage_list_table widefat fixed">
			<thead>
			<tr>
				<th>
                    <b><?php esc_html_e( 'Auto Update Feed Interval', 'woo-feed' ); ?></b>
                    <?php echo sprintf('<span style="float: right;color: #30336b;font-weight: 600;">%s</span>', esc_html__('Version '. WOO_FEED_FREE_VERSION, 'woo-feed')); //phpcs:ignore ?>
                </th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>
					<form action="" method="post" style="display: flex;align-items: center;">
						<?php wp_nonce_field( 'wf_schedule', 'wf_schedule_nonce' ); ?>
						<label for="wf_schedule"><b><?php esc_html_e( 'Interval', 'woo-feed' ); ?></b></label>
						<select name="wf_schedule" id="wf_schedule" style="margin: 0 5px;">
						<?php
						$interval = get_option( 'wf_schedule' );
						foreach ( woo_feed_get_schedule_interval_options() as $k => $v ) {
							printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $interval, $k, false ), esc_html( $v ) );
						}
						?>
						</select>
						<button type="submit" class="button button-primary wf_update_interval_btn woo-feed-btn-bg-gradient-blue"><?php esc_html_e( 'Update Interval', 'woo-feed' ); ?></button>
                        <?php woo_feed_clear_cache_button(); ?>
					</form>
				</td>
			</tr>
			</tbody>
		</table>
		<form id="contact-filter" method="post">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo esc_attr( $plugin_page ); ?>"/>
			<?php // $myListTable->search_box('search', 'search_id'); ?>
			<!-- Now we can render the completed list table -->
			<?php $myListTable->display(); ?>
		</form>
	</div>
</div>
