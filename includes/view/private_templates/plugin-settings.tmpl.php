<?php
/**
 * Starter file for the plugin settings screen.
 *
 * @package plugin-slug
 */

?>
<form method="POST" action="options.php" class="">
	<h1><?php esc_html_e( '<PLUGIN_NAME> Settings', 'plugin-slug' ); ?></h1>


	<div>
		<?php settings_fields( $this->settings_name ); ?>
		<?php do_settings_sections( $this->settings_name ); ?>
		<?php submit_button(); ?>
	</div>
</form>
