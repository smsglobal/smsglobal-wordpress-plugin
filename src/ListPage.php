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
        $title = Smsglobal_Utils::_('Subscribers');
        $browser_header = Smsglobal_Utils::_('SMSGlobal Subscribers');
        add_submenu_page('smsglobal', $browser_header, $title, 'manage_options', 'smsglobal_subscribers', array($this, 'getPage'));
    }

    public function getPage()
    {
        $subscribers = smsglobal_get_subscription(null, null);
        ?>
        <div class="wrap">
            <?php screen_icon() ?>
            <h2><?php echo Smsglobal_Utils::_('SMS Subscribers') ?></h2>
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
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo count($subscribers); ?> subscribers</span>
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
                <th scope="row"><?php echo Smsglobal_Utils::_('Name') ?></th>
                <th scope="row"><?php echo Smsglobal_Utils::_('Mobile') ?></th>
                <th scope="row"><?php echo Smsglobal_Utils::_('Email') ?></th>
                <th scope="row"><?php echo Smsglobal_Utils::_('URL') ?></th>
                <th scope="row"><?php echo Smsglobal_Utils::_('Verified') ?></th>
            </tr>
            </thead>
            <?php foreach ($subscribers as $subscriber): ?>
                <tr valign="top">
                    <td class="manage-column"><?php echo $subscriber->name; ?></td>
                    <td class="manage-column"><?php echo $subscriber->mobile; ?></td>
                    <td class="manage-column"><?php echo $subscriber->email; ?></td>
                    <td class="manage-column"><?php echo $subscriber->url; ?></td>
                    <td class="manage-column"><?php if($subscriber->verified): echo "<img src='".get_option('siteurl').'/wp-content/plugins/smsglobal/assets/tick.png'."' width='20' />"; endif;?></td>
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
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo count($subscribers); ?> subscribers</span>
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
            <div>You haven't had any subscriber via SMS yet.</div>
        <?php
        endif;
        ?>
        </div>
        <?php
    }
}
