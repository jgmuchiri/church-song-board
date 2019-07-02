<?php

if(!class_exists('WP_List_Table')) {
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class SongsList extends WP_List_Table
{

    /** Class constructor */
    public
    function __construct()
    {

        parent::__construct([
            'singular' => __('Song', 'sp'),
            //singular name of the listed records
            'plural'   => __('Songs', 'sp'),
            //plural name of the listed records
            'ajax'     => FALSE
            //does this table support ajax?
        ]);

    }

    public static
    function get_songs($per_page = 5, $page_number = 1)
    {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}".CMB_TABLE;

        if(!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY '.esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' '.esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET '.($page_number - 1) * $per_page;

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    /**
     * @param $id
     */
    public static
    function delete_song($id)
    {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}".CMB_TABLE,
            ['ID' => $id],
            ['%d']
        );
    }

    /**
     * @return string|null
     */
    public static
    function record_count()
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}".CMB_TABLE;

        return $wpdb->get_var($sql);
    }

    /** Text displayed when no data is available */
    public
    function no_items()
    {
        _e('No songs avaliable.', 'sp');
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array  $item
     * @param string $column_name
     *
     * @return mixed
     */
    public
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'event_date':
                return date('d M, Y @ h:ia', strtotime($item[$column_name]));
                break;
            case 'title':
            case 'author':
                return $item[$column_name];
            case 'actions':
                $edit = '&nbsp;<a class="pull-right" href="?page=New_church_song_board&song='.$item['id'].'"><i class="fa fa-pencil"></i></a>';

                $video = '';
                $lyrics = '';

                if(!empty($item['video'])) {
                    $video = '<a target="_blank" href="'.$item['video'].'">Video</a> | ';
                }
                if(!empty($item['lyrics'])) {
                    $lyrics = '<a target="_blank" href="'.$item['lyrics'].'">Lyrics</a>';
                }
                return $video.$lyrics. ' '.$edit;
            default:
//                return print_r($item, TRUE);
                break;
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name($item)
    {

        $delete_nonce = wp_create_nonce('sp_delete_song');

        $title = '<strong>'.$item['title'].'</strong>';

        $actions = [
            'delete' => sprintf('<a href="?page=%s&action=%s&song=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce),
        ];

        return $title.$this->row_actions($actions);
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns()
    {
        $columns = [
            'cb'         => '<input type="checkbox" />',
            'event_date' => __('Event Date', 'sp'),
            'title'      => __('Title', 'sp'),
            'author'     => __('Author', 'sp'),
            'actions'    => __('Actions', 'sp'),
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public
    function get_sortable_columns()
    {
        $sortable_columns = [
            'event_date' => [
                'Date',
                TRUE,
            ],
            'title'      => [
                'Title',
                FALSE,
            ],
            'author'     => [
                'Author',
                FALSE,
            ],
        ];

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public
    function get_bulk_actions()
    {
        $actions = [
            'bulk-delete' => 'Delete',
        ];

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public
    function prepare_items()
    {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('songs_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items,
            //WE have to calculate the total number of items
            'per_page'    => $per_page
            //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_songs($per_page, $current_page);
    }


    public
    function process_bulk_action()
    {
        if('delete' == $this->current_action()) {
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if(!wp_verify_nonce($nonce, 'sp_delete_song')) {
                die('Not permitted');
            }
            else {
                self::delete_song(absint($_GET['song']));
//                wp_redirect(esc_url_raw(add_query_arg()));
//                exit;
            }

        }

        if((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
            || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);
            foreach ($delete_ids as $id) {
                self::delete_song($id);
            }
//            wp_redirect(esc_url_raw(add_query_arg()));
//            exit;
        }
    }
}