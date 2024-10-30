<?php

class EPV_ClearComments_Admin {

    private static $initiated = false;
    private static $plugin_slug = 'clear_comments_options';
    private static $pages = array(
        'overview' => 'overview', //slug tab => page tpl name
        'options' => 'options',
        'test' => 'test',
    );

    public static function init() {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks() {
        self::$initiated = true;
        add_action('admin_menu', array('EPV_ClearComments_Admin', 'admin_menu'), 10);
    }

    public static function admin_menu() {
        add_options_page(__('Clear comments', 'clear-comments'), __('Clear comments', 'clear-comments'), 'manage_options', self::$plugin_slug, array('EPV_ClearComments_Admin', 'plugin_overview'));
    }

    public static function options_submit($keys, $white) {
        $result = '';

        $keywords = self::validate_keywords($keys);
        update_option('clear_comm_keywords', $keywords);
        $result = __('Success', 'clear-comments');


        if (isset($white)) {
            $keywords_w = self::validate_keywords($white);
            update_option('clear_comm_keywords_white', $keywords_w);
            $result = __('Success', 'clear-comments');
        }

        return $result;
    }

    public static function test_submit($text) {
        $result = '';
        
        update_option('clear_comm_test', $text);
        
        return $result;
    }

    public function plugin_overview() {
        $curr_tab = !empty($_GET['tab']) ? sanitize_text_field(stripslashes($_GET['tab'])) : ''; // sanitization ok.
        if (empty($curr_tab)) {
            $curr_tab = 'overview';
        }

        if (isset(self::$pages[$curr_tab])) {
            $curr_page = self::$pages[$curr_tab];
        } else {
            $curr_page = self::$pages['overview'];
        }

        include(EPV_CLEAR_COMMENTS__PLUGIN_DIR . 'includes/' . $curr_page . '.php');
    }

    public static function admin_tabs($current = '') {
        if ('' === $current) {
            $current = !empty($_GET['tab']) ? stripslashes($_GET['tab']) : ''; // CSRF ok, sanitization ok.
        }

        $admin_url = admin_url('options-general.php?page=' . self::$plugin_slug);
        $admin_tabs = array(
            'overview' => __('Overview', 'clear-comments'),
            'options' => __('Options', 'clear-comments'),
            'test' => __('Test', 'clear-comments'),
        );
        ?>
        <div id="nav">
            <h3 class="themes-php">
                <?php
                foreach ($admin_tabs as $tab => $name) {
                    printf('<a class="%s" href="%s">%s</a>', esc_attr($tab === $current ? 'nav-tab nav-tab-active' : 'nav-tab' ), esc_url_raw(add_query_arg('tab', $tab, $admin_url)), esc_html($name));
                }
                ?>
            </h3>
        </div>
        <?php
    }

    public static function validate_keywords($keywords) {
        $new_arr = array();

        // Only words and numbers separated by space or comma
        if (preg_match_all('/(?:[ ,]*)([\p{L}0-9]+)(?:[ ,]*)/ui', $keywords, $match)) {
            foreach ($match[1] as $key) {
                $new_arr[$key] = $key;
            }
        }

        if (sizeof($new_arr) > 0) {
            ksort($new_arr);
            $keywords = implode("\n", $new_arr);
        }
        return $keywords;
    }

    public function get_pager($count, $page = 1, $perpage = 0, $url = '/', $pg = 'p', $active_class = 'current') {
        $paged = $page;
        $max_page = ceil($count / $perpage);
        $pages_to_show = 10;
        $pages_to_show_minus_1 = $pages_to_show - 1;
        $half_page_start = floor($pages_to_show_minus_1 / 2);
        $half_page_end = ceil($pages_to_show_minus_1 / 2);
        $start_page = $paged - $half_page_start;
        if ($start_page <= 0) {
            $start_page = 1;
        }
        $end_page = $paged + $half_page_end;
        if (($end_page - $start_page) != $pages_to_show_minus_1) {
            $end_page = $start_page + $pages_to_show_minus_1;
        }
        if ($end_page > $max_page) {
            $start_page = $max_page - $pages_to_show_minus_1;
            $end_page = $max_page;
        }
        if ($start_page <= 0) {
            $start_page = 1;
        }

        $ret = '';

        $first_page_text = '«';
        $last_page_text = '»';

        if ($max_page > 1) {

            if ($start_page >= 2 && $pages_to_show < $max_page) {
                $ret .= '<span class="pg"><a href="' . $url . '&' . $pg . '=1' . '" title="' . $first_page_text . '">' . $first_page_text . '</a></span>';
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                $active = '';
                if ($i == $paged) {
                    $active = $active_class;
                }
                $page_text = $i;
                $ret .= '<span class="pg ' . $active . '"><a href="' . $url . '&' . $pg . '=' . $i . '" class="' . $active . '" title="' . $page_text . '">' . $page_text . '</a></span>';
            }

            if ($end_page < $max_page) {

                $ret .= '<span class="pg"><a href="' . $url . '&' . $pg . '=' . $max_page . '" title="' . $last_page_text . '">' . $last_page_text . '</a></span>';
            }
        }
        return $ret;
    }

}
