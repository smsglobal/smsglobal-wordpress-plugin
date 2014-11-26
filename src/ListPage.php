<?php
class Smsglobal_ListPage
{
    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'addMenu'));
        }
    }

    public function addMenu()
    {
        $title = __('Subscribers', SMSGLOBAL_TEXT_DOMAIN);
        $browser_header = __('SMSGlobal Subscribers', SMSGLOBAL_TEXT_DOMAIN);
        add_submenu_page('smsglobal', $browser_header, $title, 'manage_options', 'smsglobal_subscribers', array($this, 'getPage'));
    }

    public function getPage()
    {
        $subscribers = smsglobal_get_subscription(null, null);
        $mediaUrl = plugin_dir_url(__FILE__).'../assets';
        ?>
        <div class="wrap">
            <h2><?php _e('SMS Subscribers', SMSGLOBAL_TEXT_DOMAIN) ?></h2>
            <div>&nbsp;</div>
        <?PHP
        if ($subscribers):
        ?>
            <div class="tablenav top">

<!--                <div class="alignleft actions">-->
<!--                    <select name="action">-->
<!--                        <option value="-1" selected="selected">Bulk Actions</option>-->
<!--                        <option value="activate-selected">Activate</option>-->
<!--                        <option value="deactivate-selected">Deactivate</option>-->
<!--                        <option value="update-selected">Update</option>-->
<!--                        <option value="delete-selected">Delete</option>-->
<!--                    </select>-->
<!--                    <input type="submit" name="" id="doaction" class="button action" value="Apply">-->
<!--                </div>-->
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php printf(__('%d subscribers', SMSGLOBAL_TEXT_DOMAIN), count($subscribers)); ?></span>
<!--<span class="pagination-links"><a class="first-page disabled" title="Go to the first page" href="http://wordpress.local/wp-admin/plugins.php">«</a>-->
<!--<a class="prev-page disabled" title="Go to the previous page" href="http://wordpress.local/wp-admin/plugins.php?paged=1">‹</a>-->
<!--<span class="paging-input"><input class="current-page" title="Current page" type="text" name="paged" value="1" size="1"> of <span class="total-pages">1</span></span>-->
<!--<a class="next-page disabled" title="Go to the next page" href="http://wordpress.local/wp-admin/plugins.php?paged=1">›</a>-->
<!--<a class="last-page disabled" title="Go to the last page" href="http://wordpress.local/wp-admin/plugins.php?paged=1">»</a></span>-->
                </div>
                <br class="clear">
            </div>
        <table class="wp-list-table widefat plugins">
            <thead>
            <tr valign="top">
                <th scope="row"><?php _e('Name', SMSGLOBAL_TEXT_DOMAIN) ?></th>
                <th scope="row"><?php _e('Mobile', SMSGLOBAL_TEXT_DOMAIN) ?></th>
                <th scope="row"><?php _e('Email', SMSGLOBAL_TEXT_DOMAIN) ?></th>
                <th scope="row"><?php _e('Verified', SMSGLOBAL_TEXT_DOMAIN) ?></th>
            </tr>
            </thead>
            <?php foreach ($subscribers as $subscriber): ?>
                <tr valign="top">
                    <td class="manage-column"><?php echo $subscriber->name; ?></td>
                    <td class="manage-column"><?php echo $subscriber->mobile; ?></td>
                    <td class="manage-column"><?php echo $subscriber->email; ?></td>
                    <td class="manage-column"><?php if($subscriber->verified): echo "<img src='{$mediaUrl}/tick.png' width='20' />"; endif;?></td>
                </tr>
            <?php endforeach ?>
        </table>
            <div class="tablenav bottom">

<!--                <div class="alignleft actions">-->
<!--                    <select name="action2">-->
<!--                        <option value="-1" selected="selected">Bulk Actions</option>-->
<!--                        <option value="activate-selected">Activate</option>-->
<!--                        <option value="deactivate-selected">Deactivate</option>-->
<!--                        <option value="update-selected">Update</option>-->
<!--                        <option value="delete-selected">Delete</option>-->
<!--                    </select>-->
<!--                    <input type="submit" name="" id="doaction2" class="button action" value="Apply">-->
<!--                </div>-->
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php printf(__('%d subscribers', SMSGLOBAL_TEXT_DOMAIN), count($subscribers)); ?></span>
<!--<span class="pagination-links"><a class="first-page disabled" title="Go to the first page" href="http://wordpress.local/wp-admin/plugins.php">«</a>-->
<!--<a class="prev-page disabled" title="Go to the previous page" href="http://wordpress.local/wp-admin/plugins.php?paged=1">‹</a>-->
<!--<span class="paging-input">1 of <span class="total-pages">1</span></span>-->
<!--<a class="next-page disabled" title="Go to the next page" href="http://wordpress.local/wp-admin/plugins.php?paged=1">›</a>-->
<!--<a class="last-page disabled" title="Go to the last page" href="http://wordpress.local/wp-admin/plugins.php?paged=1">»</a></span>-->
                </div>
                <br class="clear">
            </div>
        <?php
        else:
        ?>
            <div><?php _e('You haven\'t had any subscriber via SMS yet.', SMSGLOBAL_TEXT_DOMAIN); ?></div>
        <?php
        endif;
        ?>
        </div>
        <?php
    }
}
