<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>


<script type="text/javascript">
	jQuery( document ).ready( function() {
		um_add_upgrade_log( '<?php echo esc_js( __( 'Updated social URLs fields in the UM Forms fields...', 'ultimate-member' ) ) ?>' );

		jQuery.ajax({
			url: wp.ajax.settings.url,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'um_social_fields260',
				nonce: um_admin_scripts.nonce
			},
			success: function( response ) {
				if ( typeof response.data.message != 'undefined' ) {
					um_add_upgrade_log( response.data.message );
					//switch to the next package
					um_run_upgrade();
				} else {
					um_wrong_ajax();
				}
			},
			error: function() {
				um_something_wrong();
			}
		});
	});
</script>
