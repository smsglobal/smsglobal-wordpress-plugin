<?php
class Smsglobal_GroupPage
{
    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'addMenu'));
            add_action('admin_enqueue_scripts', array($this, 'addScript'));
        }
    }

    public function addMenu()
    {
        $title = __('Phone Book', SMSGLOBAL_TEXT_DOMAIN);
        $header = __('SMSGlobal Phone Book', SMSGLOBAL_TEXT_DOMAIN);
        add_submenu_page('smsglobal', $header, $title, 'manage_options', 'smsglobal_groups', array($this, 'getPage'));
    }

    public function addScript($page)
    {
        if ($page === 'toplevel_page_smsglobal') {
            $url = plugins_url(
                'assets/admin.js',
                dirname(__FILE__)
            );
            wp_enqueue_script('smsglobal-admin', $url, array('jquery'));
        }
    }


    public function getPage()
    {
        $apiClient = Smsglobal_Utils::getRestClient();
        $groups = $apiClient->getList('Group')->objects;
        ?>
    <div class="wrap">
        <h2><?php _e('SMS Group', SMSGLOBAL_TEXT_DOMAIN) ?></h2>
        <div>&nbsp;</div>
        <?PHP
        if ($groups):
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
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php printf(__('%d groups', SMSGLOBAL_TEXT_DOMAIN), count($groups)); ?></span>
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
                    <th scope="row"><?php _e('ID', SMSGLOBAL_TEXT_DOMAIN) ?></th>
                    <th scope="row"><?php _e('Name', SMSGLOBAL_TEXT_DOMAIN) ?></th>
                    <th scope="row"><?php _e('Keyword', SMSGLOBAL_TEXT_DOMAIN) ?></th>
                    <th scope="row"><?php _e('Default Origin', SMSGLOBAL_TEXT_DOMAIN) ?></th>
                </tr>
                </thead>
                <?php foreach ($groups as $group): ?>
                <tr valign="top">
                    <td class="manage-column"><?php echo $group->getId(); ?></td>
                    <td class="manage-column"><?php echo $group->getName(); ?></td>
                    <td class="manage-column"><?php echo $group->getKeyword(); ?></td>
                    <td class="manage-column"><?php echo $group->getDefaultOrigin(); ?></td>
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
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php printf(__('%d contact groups', SMSGLOBAL_TEXT_DOMAIN), count($groups)) ?></span>
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
            <div><?php printf(__('You do not have any contact group in <a href="%s" target="_blank">MXT</a> yet.', SMSGLOBAL_TEXT_DOMAIN), 'http://mxt.smsglobal.com'); ?></div>
            <?php
        endif;
        ?>
    </div>
    <?php
    }
}
