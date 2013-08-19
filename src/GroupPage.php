<?php
class Smsglobal_GroupPage
{
    protected $toOptions;

    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'addMenu'));
            add_action('admin_enqueue_scripts', array($this, 'addScript'));
        }
    }

    public function addMenu()
    {
        $title = Smsglobal::_('Phone Book');
        $header = Smsglobal::_('SMSGlobal Phone Book');
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
        $apiClient = Smsglobal::getRestClient();
        $groups = $apiClient->getList('group')->objects;

        $isConfigured = $this->checkConfiguration();
        ?>
    <div class="wrap">
        <?php screen_icon() ?>
        <h2><?php echo Smsglobal::_('SMS Group') ?></h2>
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
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo count($groups); ?> groups</span>
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
                    <th scope="row">ID</th>
                    <th scope="row">name</th>
                    <th scope="row">keyword</th>
                    <th scope="row">defaultOrigin</th>
                </tr>
                </thead>
                <?php foreach ($groups as $group): ?>
                <tr valign="top">
                    <td class="manage-column"><?php echo $group->ID; ?></td>
                    <td class="manage-column"><?php echo $group->name; ?></td>
                    <td class="manage-column"><?php echo $group->keyword; ?></td>
                    <td class="manage-column"><?php echo $group->defaultOrigin; ?></td>
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
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo count($groups); ?> contact groups</span>
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
            <div>You do not have any contact group in <a href="http://mxt.smsglobal.com" target="_blank">MXT</a> yet.</div>
            <?php
        endif;
        ?>
    </div>
    <?php
    }

    protected function checkConfiguration()
    {
        $key = get_option('smsglobal_api_key');
        $secret = get_option('smsglobal_api_secret');

        return false !== $key && false !== $secret;
    }

    protected function getToOptions()
    {
        if (null === $this->toOptions) {
            $this->toOptions = Smsglobal::getRoles();
            $this->toOptions['number'] = Smsglobal::_('Number');
        }

        return $this->toOptions;
    }
}
