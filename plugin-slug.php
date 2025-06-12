<?php
/**
 * Initialize the main plugin file.
 *
 * @category Class
 * @package  PluginClass
 * @author   <Author Name>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

/**
 * Plugin Name:       <PLUGIN_NAME>
 * Description:       <PLUGIN_DESC>
 * Version:           0.1
 * Author:
 * Requires PHP:      5.6.20
 * Requires at least: 5.0
 * License:           GNU General Public License, v2 ( or newer )
 * License URI:       http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Update URI:
 * Domain Path:       /lang
 * Text Domain:       plugin-slug
 *
 * @package     PluginClass
 *
 * Based on MVC Starter Plugin v3.0 by UseStrict Consulting
 *
 * Copyright (C) <YEAR> <COPY_TEXT>, released under the GNU General Public License.
 */

define( 'PLUGINCLASS_VERSION', '0.1' );
define( 'PLUGINCLASS_FILE', __FILE__ );

require_once __DIR__ . '/includes/class-pluginclass.php';

/**
 * Kick off the plugin and provides an easy way to get the object.
 */
function pluginclass() {
    return PluginClass::instance();
}
pluginclass();

/**
 * End of file plugin-slug.php
 * Location: plugin-slug/plugin-slug.php
 */
