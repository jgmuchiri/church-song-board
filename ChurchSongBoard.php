<?php

/*
Plugin Name: Church Song Board
Plugin URI: https://johnmuchiri.com
Description: Church song board
Author: John Muchiri
Version: 1.3
Author URI: https://johnmuchiri.com
*/

define('CMB_TABLE', 'church_song_board');
include plugin_dir_path(__FILE__).'lib/SongsList.php';

class ChurchSongBoard
{

    public $songs_obj;

    static $instance;

    public
    function __construct()
    {
        add_action('admin_menu', [
            &$this,
            'menu_items',
        ]);

        add_action('init', [
            &$this,
            'updater',
        ]);
        wp_enqueue_style('style-main', plugin_dir_url(__FILE__).'css/style.css', FALSE);

        add_shortcode('mboard', [
            &$this,
            'church_song_board',
        ]);
    }

    /**
     * Screen options
     */
    public
    function screen_option()
    {

        $option = 'per_page';
        $args = [
            'label'   => 'Songs',
            'default' => 10,
            'option'  => 'songs_per_page',
        ];

        add_screen_option($option, $args);

        $this->songs_obj = new SongsList();
    }


    /** Singleton instance */
    public static
    function get_instance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    function admin()
    {
        global $wpdb;
        include plugin_dir_path(__FILE__).'views/admin.php';
    }

    function menu_items()
    {
        $hook = add_menu_page(
            'Song Board',
            'Song Board',
            'manage_options',
            'ChurchSongBoard',
            [
                $this,
                'admin',
            ],
            'dashicons-format-audio',
            2
        );
        add_action("load-$hook", [
            $this,
            'screen_option',
        ]);

        add_submenu_page(
            'ChurchSongBoard',
            'New Song',
            'New Song',
            'manage_options',
            'New_church_song_board',
            [
                &$this,
                'form_creation',
            ]
        );
    }

    function church_song_board()
    {
        global $wpdb;

        $today = date('Y-m-d').' 00:00:00';

        $songs = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.CMB_TABLE.' WHERE event_date > \''.$today.'\'  ORDER BY sort_order ASC, event_date ASC');

        $content = '';

        if(!empty($songs)) {
            ob_start();
            include plugin_dir_path(__FILE__).'views/board.php';
            $content = ob_get_clean();
        }
        return $content;
    }

    function form_creation()
    {
        global $wpdb;
        $done = FALSE;
        if(isset($_POST['newsong'])) {
            global $wpdb;
            $wpdb->insert($wpdb->prefix.CMB_TABLE, [
                'event_date' => sanitize_text_field($_POST['event_date'].' '.$_POST['event_date_t']),
                'title'      => sanitize_text_field($_POST['title']),
                'author'     => sanitize_text_field($_POST['author']),
                'video'      => sanitize_text_field($_POST['video']),
                'lyrics'     => sanitize_text_field($_POST['lyrics']),
                'sort_order' => sanitize_text_field($_POST['sort_order']),
            ]);

            if($wpdb->rows_affected > 0) {
                $done = TRUE;
            }
        }

        if(isset($_POST['updatesong'])) {

            $wpdb->update($wpdb->prefix.CMB_TABLE, [
                'event_date' => sanitize_text_field($_POST['event_date'].' '.$_POST['event_date_t']),
                'title'      => sanitize_text_field($_POST['title']),
                'author'     => sanitize_text_field($_POST['author']),
                'video'      => sanitize_text_field($_POST['video']),
                'lyrics'     => sanitize_text_field($_POST['lyrics']),
                'sort_order' => sanitize_text_field($_POST['sort_order']),
            ], ['id' => sanitize_text_field($_GET['song'])]);
            if($wpdb->rows_affected > 0) {
                $done = TRUE;
            }
        }

        $song = [];
        if(isset($_GET['song'])) {
            $song = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.CMB_TABLE.' WHERE id='.$_GET['song']);
        }
        include plugin_dir_path(__FILE__).'views/form.php';
    }

    function activate()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = CMB_TABLE;
        $query = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}{$table} (
            id INT auto_increment PRIMARY KEY,
            event_date DATETIME NULL,
            title VARCHAR(255) NOT NULL,
            author VARCHAR (255) NULL,
            video VARCHAR(255) NULL,
            lyrics VARCHAR (255) NULL,
            
            background VARCHAR (255) NULL,
            created_at DATETIME NULL,
            sort_order INT NULL,
            created_by INT NULL) $charset_collate;";
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        dbDelta($query);
        //check table created
        if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'") != $wpdb->prefix.$table) {
            die('Unable to create database. Check plugin.');
        }
    }

    function deactivate()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix.CMB_TABLE);
    }

    function updater()
    {
        include_once plugin_dir_path(__FILE__).'lib/updater.php';

        define('WP_GITHUB_FORCE_UPDATE', TRUE);

        if(is_admin()) {
            $config = [
                'slug'               => plugin_basename(__FILE__),
                'proper_folder_name' => 'church-song-board',
                'api_url'            => 'https://api.github.com/repos/jgmuchiri/church-song-board',
                'raw_url'            => 'https://raw.github.com/jgmuchiri/church-song-board/master',
                'github_url'         => 'https://github.com/jgmuchiri/church-song-board',
                'zip_url'            => 'https://github.com/jgmuchiri/church-song-board/archive/master.zip',
                'sslverify'          => TRUE,
                'requires'           => '1.0',
                'tested'             => '1.2',
                'readme'             => 'README.md',
                'access_token'       => '',
            ];

            new WP_GitHub_Updater($config);
        }
    }
}


if(class_exists('ChurchSongBoard')) {
    //install
    register_activation_hook(__FILE__, [
        'ChurchSongBoard',
        'activate',
    ]);
    //uninstall
    register_deactivation_hook(__FILE__, [
        'ChurchSongBoard',
        'deactivate',
    ]);
    // Instantiate the plugin class
    $cmb = new ChurchSongBoard();
    // Add a link to the settings page onto the plugin page
    if(isset($cmb)) {
        // Add the settings link to the plugins page
        function cmb_listing_settings_link($links)
        {
            $settings_link = '<a href="?page=ChurchSongBoard">Song Board</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", 'cmb_listing_settings_link');
    }
}