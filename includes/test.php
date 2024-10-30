<div class="wrap">
    <h2><?php echo __('Clear comments', 'clear-comments') . " — " . __('Test', 'clear-comments'); ?></h2>
    <?php self::admin_tabs($curr_tab); ?>

    <h3><?php print __('Comment text', 'clear-comments') ?></h3>

    <?php
    if (isset($_POST['clear_comm_test']) && isset($_POST['clear-comments-nonce'])) {

        $nonce = wp_verify_nonce($_POST['clear-comments-nonce'], 'clear-comments-test');
        $result = '';

        if ($nonce) {
            $test_text = sanitize_text_field(stripslashes($_POST['clear_comm_test']));
            if ($test_text) {
                $result = self::test_submit($test_text);
            }
        }
    }
    ?>

    <form accept-charset="UTF-8" method="post" id="options">

        <div class="form-field">	
            <label for="clear_comm_test"><?php print __('Write a test comment', 'clear-comments') ?></label>
            <br />
            <textarea name="clear_comm_test" id="clear_comm_test" cols="100" rows="5"><?php print get_option('clear_comm_test', '') ?></textarea>
        </div> 
        <?php wp_nonce_field('clear-comments-test', 'clear-comments-nonce'); ?>

        <input type="submit" name="options" id="edit-submit" value="<?php echo __('Submit', 'clear-comments') ?>" class="button-primary">           

    </form>


    <?php
    $comment = get_option('clear_comm_test', '');
    if ($comment) {
        $clear_comment = EPV_ClearComments::validate_content($comment);
        $last_keywords = EPV_ClearComments::get_last_keywords();
        $comment_bold = EPV_ClearComments::get_last_comment_bold();
        ?>
        <h3><?php print __('Found keywords', 'clear-comments') ?></h3>
        <p><blockquote><?php print $comment_bold ?></blockquote></p>   
    <h3><?php print __('Result text', 'clear-comments') ?></h3>
    <p><blockquote><?php print $clear_comment ?></blockquote></p>    
    <h3><?php print __('Keywords list', 'clear-comments') ?></h3>
    <?php
    echo "<pre>";
    print_r($last_keywords);
    echo "</pre>";
    ?>

    <?php
}
?>
</div>