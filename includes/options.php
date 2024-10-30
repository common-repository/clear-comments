<div class="wrap">    
    <h2><?php echo __('Clear comments', 'clear-comments') . " — " . __('Options', 'clear-comments'); ?></h2>
    <?php self::admin_tabs($curr_tab); ?>

    <?php
    if (isset($_POST['clear_comm_keywords']) && isset($_POST['clear-comments-nonce'])) {
        $nonce = wp_verify_nonce($_POST['clear-comments-nonce'], 'clear-comments-options');
        $result = '';

        if ($nonce) {
            $keys = sanitize_text_field(stripslashes($_POST['clear_comm_keywords']));
            $white = '';
            if (isset($_POST['clear_comm_keywords_white'])) {
                $white = sanitize_text_field(stripslashes($_POST['clear_comm_keywords_white']));
            }

            $result = self::options_submit($keys, $white);
        }
        if ($result) {
            print "<div class=\"updated\"><p><strong>$result</strong></p></div>";
        }
    }
    ?>

    <form accept-charset="UTF-8" method="post" id="options">

        <h3><?php print __('Black list', 'clear-comments') ?></h3>

        <div class="form-field">	
            <textarea name="clear_comm_keywords" id="clear_comm_keywords" cols="50" rows="10"><?php print get_option('clear_comm_keywords', '') ?></textarea> 
        </div>	    
        <div class="form-field">
            <label for="clear_comm_keywords"><?php print __('Еnter keywords, one word per line', 'clear-comments') ?></label>
        </div>

        <h3><?php print __('White list', 'clear-comments') ?></h3>
        <div class="form-field">
            <textarea name="clear_comm_keywords_white" id="clear_comm_keywords_white" cols="50" rows="10"><?php print get_option('clear_comm_keywords_white', '') ?></textarea>
        </div>	    
        <div class="form-field">
            <label for="clear_comm_keywords_white"><?php print __('Еnter exclusion keywords, one word per line', 'clear-comments') ?></label>
        </div>
        <br />
        <?php wp_nonce_field('clear-comments-options', 'clear-comments-nonce'); ?>

        <input type="submit" name="options" id="edit-submit" value="<?php echo __('Save', 'clear-comments') ?>" class="button-primary">           

    </form>
</div>