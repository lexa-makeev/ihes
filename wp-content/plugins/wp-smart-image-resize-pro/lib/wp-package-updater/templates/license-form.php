<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
$has_license_key = !empty( $license_key );
$has_valid_license_key = empty( $license_error ) && $has_license_key;
?>


<form id="<?php echo esc_attr( 'wrap_license_' . $package_slug ); ?>" >

	
	<p>
		<label><?php esc_html_e( 'License key', 'wp-package-updater' ); ?></label> <input placeholder="Enter license key to activate" class="regular-text license" type="text" id="<?php echo esc_attr( 'license_key_' . $package_id); ?>" value="<?php echo $license_key ?>" >
	
		<button type="button"  class="button-primary deactivate-license" <?php echo $has_license_key ? '' : 'style="display:none"'   ?>
		data-pending-text="Deactivating..."
		value="deactivate">Deactivate license</button>

	<button type="button"  class="button-primary activate-license" <?php echo $has_license_key ?  'style="display:none"' : ''  ?>
		data-pending-text="Activating..."
		value="activate" >
	Activate license
	</button>
	
</p>
</form>

<p class="description" style="font-style: italic;">Проблемы с активацией лицензии? <a href="https://sirplugin.com/contact.html" target="_blank">Contact us</a>.</p>

