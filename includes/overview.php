<div class="wrap">
    <h2><?php echo __('Clear comments', 'clear-comments') . " — " . __('Overview', 'clear-comments'); ?></h2>
    <?php self::admin_tabs($curr_tab); ?>
    
    <?php
    $count = EPV_ClearComments::get_clear_count();
    if (!$count) {
        print __('No cleared comments', 'clear-comments');
        return;
    }       
    $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
    $perpage = 100;
    $pager = self::get_pager($count, $page, $perpage, $url = '?page='.self::$plugin_slug);

    $last_comments = EPV_ClearComments::get_last_comments($page, $perpage);

    if (sizeof($last_comments) > 0) {
        ?>     
        <h3><?php print __('Last comments', 'clear-comments') ?></h3>

        <div class="pager">
            <?php print $pager ?>          
        </div>
        <table width="100%">
            <?php
            foreach ($last_comments as $comment) {
                $com_link = get_comment_link($comment->cid);
                ?>
                <tr>
                    <td width="20%"  style="padding:0 20px 0 0" >
                        <p><?php print $comment->created_on; ?>.                      
                            <a href="<?php print $com_link ?>" ><?php print $comment->comment_author; ?></a></p>
                    </td>
                    <td style="border: 1px solid red; padding:0 20px;" ><p><?php print $comment->comment ?></p></td>
                    <td style="border: 1px solid silver; padding:0 20px;"><p><?php print $comment->comment_content ?></p></td>
                </tr>
            <?php }
            ?> 
        </table>
        <br />
        <div class="pager">
            <?php print $pager ?>
        </div>
        <?php
    }
    ?>
</div>