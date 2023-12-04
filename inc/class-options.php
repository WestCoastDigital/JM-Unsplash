<?php

class jmUnsplashSettingsPage
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'jm_create_settings'));
        add_action('admin_init', array($this, 'jm_setup_sections'));
        add_action('admin_init', array($this, 'jm_setup_fields'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin'));
    }

    public function jm_create_settings()
    {
        $page_title = 'Unsplash Settings';
        $menu_title = 'Unsplash Settings';
        $capability = 'manage_options';
        $slug = 'unsplashsettings';
        $callback = array($this, 'jm_settings_content');
        add_media_page($page_title, $menu_title, $capability, $slug, $callback);
    }



    public function jm_settings_content()
    {
?>
        <div class="wrap">
            <h1 class="nav-tab-wrapper">
                <a href="?page=unsplashsettings&tab=image-search" class="nav-tab <?php echo empty($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] === 'image-search') ? 'nav-tab-active' : ''; ?>">Image Search</a>
                <a href="?page=unsplashsettings&tab=settings" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] === 'settings' ? 'nav-tab-active' : ''; ?>">API Settings</a>
                <a href="?page=unsplashsettings&tab=delete-images" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] === 'delete-images' ? 'nav-tab-active' : ''; ?>">Delete Images</a>
            </h1>

            <?php
            if (isset($_GET['tab']) && $_GET['tab'] === 'settings') {
                $this->jm_api_settings_content();
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'delete-images') {
                $this->jm_delete_images_content();
            } else {
                $this->jm_image_search_content();
            }
            ?>
        </div>
    <?php
    }


    public function jm_api_settings_content()
    {
    ?>
        <div class="wrap">
            <h2>API Settings</h2>
            <?php settings_errors(); ?>
            <form method="POST" action="options.php">
                <?php
                settings_fields('unsplashsettings');
                do_settings_sections('unsplashsettings');
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    public function jm_image_search_content()
    {
    ?>
        <div class="wrap">
            <h2>Image Search</h2>
            <form id="image-search-form">
                <input type="text" id="image_search" name="image_search" placeholder="Search images..." size="100" />
                <input type="submit" value="Search" class="button button-primary" />
            </form>
            <div class="image-search-container">
                <div id="image-results"></div>
            </div>
        </div>
    <?php
    }

    public function jm_delete_images_content()
    {
    ?>
        <div class="wrap">
            <h2>What is the meaning of life?</h2>
            <p>This will delete all images added via this plugin and cannot be undone.</p>
            <form id="image-delete-form">
                <input type="text" id="image_delete" name="image_delete" placeholder="Type 42 to confirm deletion" size="25" />
                <input type="submit" value="Delete" id="deleteImagesBtn" class="button button-primary" disabled="true" />
            </form>
        </div>
<?php
    }

    public function jm_setup_sections()
    {
        add_settings_section('unsplashsettings_section', '', array(), 'unsplashsettings');
    }

    public function jm_setup_fields()
    {
        $fields = array(
            array(
                'label' => 'Access Key',
                'id' => 'jm_unsplash_pubkey',
                'type' => 'text',
                'section' => 'unsplashsettings_section',
            ),
            array(
                'label' => 'Qty Per Page',
                'id' => 'jm_unsplash_perpay',
                'type' => 'number',
                'section' => 'unsplashsettings_section',
                'placeholder' => '50',
            ),
        );
        foreach ($fields as $field) {
            add_settings_field($field['id'], $field['label'], array($this, 'jm_field_callback'), 'unsplashsettings', $field['section'], $field);
            register_setting('unsplashsettings', $field['id']);
        }
    }

    public function jm_field_callback($field)
    {
        $value = get_option($field['id']);
        $placeholder = '';
        if (isset($field['placeholder'])) {
            $placeholder = $field['placeholder'];
        }
        switch ($field['type']) {
            case 'key':
                printf(
                    '<input name="%1$s" id="%1$s" type="text" placeholder="%3$s" value="%4$s" />',
                    $field['id'],
                    $field['type'],
                    $placeholder,
                    $value
                );
                break;
            default:
                printf(
                    '<input size="50" name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
                    $field['id'],
                    $field['type'],
                    $placeholder,
                    $value
                );
        }
        if (isset($field['desc'])) {
            if ($desc = $field['desc']) {
                printf('<p class="description">%s </p>', $desc);
            }
        }
    }

    public function enqueue_admin()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jm-unsplash-admin', JM_UNSPLASH_IMAGERY_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), JM_UNSPLASH_IMAGERY_VERSION);
        wp_add_inline_script('jm-unsplash-admin', 'var jmUnsplashApi = "' . get_option('jm_unsplash_pubkey') . '"');
        $perpage = get_option('jm_unsplash_perpay') ?? '';
        if (!$perpage || $perpage == '') {
            $perpage = 50;
        }
        wp_add_inline_script('jm-unsplash-admin', 'var jmUnsplashPerPay = "' . $perpage . '"');
        wp_enqueue_style('jm-unsplash-admin', JM_UNSPLASH_IMAGERY_PLUGIN_URL . 'assets/css/admin-style.css', array(), JM_UNSPLASH_IMAGERY_VERSION);

        wp_localize_script('jm-unsplash-admin', 'jmUnsplashAjax', array('ajax_url' => admin_url('admin-ajax.php')));
    }
}

new jmUnsplashSettingsPage();
