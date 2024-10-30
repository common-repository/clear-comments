<?php

class EPV_ClearComments {

    private static $initiated = false;
    private static $last_keywords = array();
    private static $last_comment_bold = '';

    public static function init() {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    private static function init_hooks() {
        self::$initiated = true;

        // New comment
        add_action('preprocess_comment', array('EPV_ClearComments', 'auto_check_comment'), 2);

        // Edit comment
        add_action('edit_comment', array('EPV_ClearComments', 'auto_check_comment_edit'));
    }

    public static function auto_check_comment_edit($comment_id) {
        $comment = (array) get_comment($comment_id);

        $comment['comment_content'] = self::validate_content($comment['comment_content']);

        if (self::$last_keywords) {
            remove_action('edit_comment', array('EPV_ClearComments', 'auto_check_comment_edit'));

            self::add_comment($comment['comment_ID']);
            wp_update_comment($comment);
        }
    }

    public static function auto_check_comment($commentdata) {

        $commentdata['comment_content'] = self::validate_content($commentdata['comment_content']);
        if (self::$last_keywords) {
            add_action('comment_post', array('EPV_ClearComments', 'add_new_comment_log'));
        }
        return $commentdata;
    }

    public static function add_new_comment_log($comment_id) {
        remove_action('comment_post', array('EPV_ClearComments', 'add_new_comment_log'));
        self::add_comment($comment_id);
    }

    public static function validate_content($content) {
        self::$last_keywords = array();
        self::$last_comment_bold = '';
        $content_ret = $content;

        $replace_simbol = '*';

        //for test
        $spamlist_data = get_option('clear_comm_keywords', '');

        if ($spamlist_data) {
            $spamlist = explode("\n", $spamlist_data);


            $keys_found = array();
            foreach ($spamlist as $keyword) {
                if (preg_match_all('|([\p{L}0-9]*' . $keyword . '[\p{L}0-9]*)|ui', $content, $match)) {
                    foreach ($match[1] as $value) {
                        $keys_found[$value] = $keyword;
                    }
                }
            }

            $white_list_data = get_option('clear_comm_keywords_white', '');

            if ($white_list_data && $keys_found) {
                $white_list = explode("\n", $white_list_data);

                foreach ($keys_found as $phrase => $keyword) {
                    foreach ($white_list as $white_key) {
                        if (preg_match('|' . $white_key . '|ui', $phrase)) {
                            unset($keys_found[$phrase]);
                        }
                    }
                }
            }

            if ($keys_found) {
                foreach ($keys_found as $phrase => $keyword) {
                    if ($keyword) {
                        if (preg_match_all('|[\p{L}0-9]|ui', $phrase, $match)) {
                            $len = sizeof($match[0]);
                            $keyString = '';
                            for ($i = 0; $i < $len; $i++) {
                                $keyString .= $replace_simbol;
                            }
                            $content_ret = preg_replace('/([^\p{L}]+|^)' . $phrase . '([^\p{L}]+|$)/ui', "$1" . $keyString . "$2", $content_ret);
                        }
                    }
                }

                self::$last_keywords = $keys_found;
                self::$last_comment_bold = self::comment_bold($content, $keys_found);
            }
        }

        return $content_ret;
    }

    public static function get_last_keywords() {
        return self::$last_keywords;
    }

    public static function get_last_comment_bold() {
        return self::$last_comment_bold;
    }

    public static function comment_bold($comment, $keywords) {
        $comment_bold = $comment;
        if (sizeof($keywords) > 0) {
            foreach ($keywords as $key => $value) {
                $comment_bold = preg_replace('/([^\p{L}]+|^)' . $key . '([^\p{L}]+|$)/ui', "$1<b>" . $key . "</b>$2", $comment_bold);
            }
        }
        return $comment_bold;
    }

    public static function add_comment($comment_id) {
        if (isset($comment_id)) {
            global $wpdb, $table_prefix;
            $time = gmdate('Y-m-d H:i:s', ( time() + ( get_option('gmt_offset') * HOUR_IN_SECONDS )));
            $comment = self::$last_comment_bold;
            $wpdb->query(sprintf("INSERT INTO " . $table_prefix . "clear_comments_log (cid, comment, created_on) VALUES ('%d', '%s', '%s')", $comment_id, $comment, $time));
        }
    }

    public static function get_last_comments($page = 1, $perpage = 10) {
        $page -= 1;
        $start = $page * $perpage;

        $limit = '';
        if ($perpage > 0) {
            $limit = "LIMIT $start, " . $perpage;
        }
        global $wpdb, $table_prefix;
        $sql = sprintf("SELECT log.cid, log.comment, log.created_on, 
            comm.user_id, comm.comment_author, comm.comment_author_email, comm.comment_content 
            FROM " . $table_prefix . "clear_comments_log log
            INNER JOIN $wpdb->comments comm ON log.cid = comm.comment_ID            
            ORDER BY log.created_on DESC $limit");

        $result = $wpdb->get_results($sql);
        return $result;
    }

    public function get_clear_count() {
        global $wpdb, $table_prefix;
        $query = "SELECT COUNT(*) FROM " . $table_prefix . "clear_comments_log";
        $result = $wpdb->get_var($query);
        return $result;
    }

    public static function log($clear_comments_debug) {
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG)
            error_log(print_r(compact('clear_comments_debug'), 1)); //send message to debug.log when in debug mode
    }

}
