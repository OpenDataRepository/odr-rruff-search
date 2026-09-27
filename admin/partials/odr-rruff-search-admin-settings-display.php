<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://opendatarepository.org
 * @since      1.0.0
 *
 * @package    Odr_Rruff_Search
 * @subpackage Odr_Rruff_Search/admin/partials
 */

?>
<h2>ODR Search Plugin Settings</h2>
<?php settings_errors( 'odr_rruff_search_plugin_options' ); ?>
<form action="options.php" method="post">
    <?php
        settings_fields( 'odr_rruff_search_plugin_options' );
        do_settings_sections( $this->plugin_name );
    ?>
    <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
</form>