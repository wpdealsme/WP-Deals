<?php
/*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/* Make sure we don't expose any info if called directly */

if (!function_exists('add_action')) {
    echo "Hi there!  I'm just a little plugin, don't mind me.";
    exit;
}

add_action('init', 'deals_rolescheck');

function deals_rolescheck() {
    if (current_user_can('manage_options')) {
        add_action('admin_menu', 'deals_add_page');
        add_action('admin_init', 'deals_init');
        add_action('admin_init', 'deals_mlu_init');
    }
}

/* Loads the file for option sanitization */

add_action('init', 'deals_load_sanitization');

function deals_load_sanitization() {
    require_once dirname(__FILE__) . '/deals_sanitize.php';
}

/*
 * Creates the settings in the database by looping through the array
 * we supplied in options.php.  This is a neat way to do it since
 * we won't have to save settings for headers, descriptions, or arguments.
 *
 * Read more about the Settings API in the WordPress codex:
 * http://codex.wordpress.org/Settings_API
 *
 */

function deals_init() {

    // Include the required files
    require_once dirname(__FILE__) . '/deals_interface.php';
    require_once dirname(__FILE__) . '/deals_medialibrary_uploader.php';

    // Loads the options array from the theme
    require_once dirname(__FILE__) . '/deals_options.php';

    $deals_settings = get_option('dealoptions');

    // Updates the unique option id in the database if it has changed
    deals_option_name();

    // Gets the unique id, returning a default if it isn't defined
    if (isset($deals_settings['id'])) {
        $option_name = $deals_settings['id'];
    } else {
        $option_name = 'dealoptions';
    }

    // If the option has no saved data, load the defaults
    if (!get_option($option_name)) {
        deals_setdefaults();
    }

    // Registers the settings fields and callback
    register_setting('dealoptions', $option_name, 'deals_validate');
}

function deals_setdefaults() {

    $deals_settings = get_option('dealoptions');

    // Gets the unique option id
    $option_name = $deals_settings['id'];

    if (isset($deals_settings['knownoptions'])) {
        $knownoptions = $deals_settings['knownoptions'];
        if (!in_array($option_name, $knownoptions)) {
            array_push($knownoptions, $option_name);
            $deals_settings['knownoptions'] = $knownoptions;
            update_option('dealoptions', $deals_settings);
        }
    } else {
        $newoptionname = array($option_name);
        $deals_settings['knownoptions'] = $newoptionname;
        update_option('dealoptions', $deals_settings);
    }

    // Gets the default options data from the array in options.php
    $options = deals_options();

    // If the options haven't been added to the database yet, they are added now
    $values = deals_get_default_values();

    if (isset($values)) {
        add_option($option_name, $values); // Add option with default settings
    }
}

if (!function_exists('deals_add_page')) {

    function deals_add_page() {

        //$deals_page = add_submenu_page('edit.php?post_type=daily-deals', 'Deal Options', 'Deals Settings', 'manage_options', 'daily-options', 'deals_page');
        $deals_page = add_options_page('Deal Options', 'Deal Options', 'manage_options', 'deal-options','deals_page');
        
        // Adds actions to hook in the required css and javascript
        add_action("admin_print_styles-$deals_page", 'deals_load_styles');
        add_action("admin_print_scripts-$deals_page", 'deals_load_scripts');
    }

}

/* Loads the CSS */

function deals_load_styles() {
    wp_enqueue_style('admin-style', DEALS_FRAMEWORK_DIRECTORY . 'css/admin-style.css');
    wp_enqueue_style('color-picker', DEALS_FRAMEWORK_DIRECTORY . 'css/colorpicker.css');
}

/* Loads the javascript */

function deals_load_scripts() {

    add_action('admin_head', 'deals_admin_head');

    // Enqueued scripts
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('color-picker', DEALS_FRAMEWORK_DIRECTORY . 'js/colorpicker.js', array('jquery'));
    wp_enqueue_script('options-custom', DEALS_FRAMEWORK_DIRECTORY . 'js/options-custom.js', array('jquery'));
}

function deals_admin_head() {

    // Hook to add custom scripts
    do_action('deals_settings_custom_scripts');
}

if (!function_exists('deals_page')) {

    function deals_page() {
        $return = deals_fields();
        settings_errors();
        ?>

        <div class="wrap">
            <?php screen_icon('themes'); ?>
            <h2 class="nav-tab-wrapper">
                <?php echo $return[1]; ?>
            </h2>

            <div class="metabox-holder">
                <div id="dealsframework">
                    <form action="options.php" method="post">
                        <?php settings_fields('dealoptions'); ?>

                        <?php echo $return[0]; /* Settings */ ?>

                        <div id="dealsframework-submit">
                            <input type="submit" class="button-primary" name="update" value="<?php esc_attr_e('Save Options'); ?>" />
                            <input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e('Restore Defaults'); ?>" onclick="return confirm( '<?php print esc_js(__('Click OK to reset. Any theme settings will be lost!')); ?>' );" />
                            <div class="clear"></div>
                        </div>
                    </form>
                </div> <!-- / #container -->
            </div>
        </div> <!-- / .wrap -->

        <?php
    }

}

/**
 * Validate Options.
 *
 * This runs after the submit/reset button has been clicked and
 * validates the inputs.
 *
 * @uses $_POST['reset']
 * @uses $_POST['update']
 */
function deals_validate($input) {

    /*
     * Restore Defaults.
     *
     * In the event that the user clicked the "Restore Defaults"
     * button, the options defined in the theme's options.php
     * file will be added to the option for the active theme.
     */

    if (isset($_POST['reset'])) {
        add_settings_error('deal-options', 'restore_defaults', __('Default options restored.', 'wpdeals'), 'updated fade');
        return deals_get_default_values();
    }

    /*
     * Udpdate Settings.
     */

    if (isset($_POST['update'])) {
        $clean = array();
        $options = deals_options();

        foreach ($options as $option) {

            if (!isset($option['id'])) {
                continue;
            }

            if (!isset($option['type'])) {
                continue;
            }

            $id = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($option['id']));

            // Set checkbox to false if it wasn't sent in the $_POST
            if ('checkbox' == $option['type'] && !isset($input[$id])) {
                $input[$id] = '0';
            }

            // Set each item in the multicheck to false if it wasn't sent in the $_POST
            if ('multicheck' == $option['type'] && !isset($input[$id])) {
                foreach ($option['options'] as $key => $value) {
                    $input[$id][$key] = '0';
                }
            }

            // For a value to be submitted to database it must pass through a sanitization filter
            if (has_filter('deals_sanitize_' . $option['type'])) {
                $clean[$id] = apply_filters('deals_sanitize_' . $option['type'], $input[$id], $option);
            }
        }
//                echo '<pre>';
//                print_r($clean);
//                echo '</pre>';
//                exit();
        add_settings_error('deal-options', 'save_options', __('Options saved.', 'wpdeals'), 'updated fade');

        return $clean;
    }

    /*
     * Request Not Recognized.
     */

    return deals_get_default_values();
}

function deals_get_default_values() {
    $output = array();
    $config = deals_options();
    foreach ((array) $config as $option) {
        if (!isset($option['id'])) {
            continue;
        }
        if (!isset($option['std'])) {
            continue;
        }
        if (!isset($option['type'])) {
            continue;
        }
        if (has_filter('deals_sanitize_' . $option['type'])) {
            $output[$option['id']] = apply_filters('deals_sanitize_' . $option['type'], $option['std'], $option);
        }
    }
    return $output;
}

/**
 * Add Theme Options menu item to Admin Bar.
 */
add_action('wp_before_admin_bar_render', 'deals_adminbar');

function deals_adminbar() {

    global $wp_admin_bar;

    $wp_admin_bar->add_menu(array(
        'parent' => 'appearance',
        'id' => 'deals_plugin_options',
        'title' => __('Deal Options'),
        'href' => admin_url('options-general.php?page=deal-options')
    ));
}

if (!function_exists('deals_get_option')) {

    function deals_get_option($name, $default = false) {
        $config = get_option('dealoptions');

        if (!isset($config['id'])) {
            return $default;
        }

        $options = get_option($config['id']);

        if (isset($options[$name])) {
            return $options[$name];
        }
        return $default;
    }

}