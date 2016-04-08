<div class='wrap'>
    <h1><?= __('Composer Manager', 'wp-composer-manager') ?></h1>
    <hr>
    <h2><?= __('Installed Composer Packages', 'wp-composer-manager') ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?= __('Package', 'wp-composer-manager') ?></th>
                <th><?= __('Version', 'wp-composer-manager') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        /** @var \AaronSaray\WPComposerManager\Model\Package $package */
        foreach ($this->packages as $package) {
            echo '<tr>';
            echo '<td>' . esc_html($package->getName()) . '</td>';
            echo '<td>' . esc_html($package->getVersion()) . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th><?= __('Package', 'wp-composer-manager') ?></th>
                <th><?= __('Version', 'wp-composer-manager') ?></th>
            </tr>
        </tfoot>
    </table>
    <br>
    <hr>
    <h2><?= __('Plugins with composer.json files', 'wp-composer-manager') ?></h2>
    <p>These plugins have composer.json files in their root.</p>
    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th><?= __('Plugin Name', 'wp-composer-manager') ?></th>
            <th><?= __('Description', 'wp-composer-manager') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        /** @var \AaronSaray\WPComposerManager\Model\Plugin $plugin */
        foreach ($this->plugins as $plugin) {
            echo '<tr>';

            echo '<td>';
            echo esc_html($plugin->getName());
            if (!$plugin->isActive()) echo " <small><strong>(inactive)</strong>";
            echo '</td>';
            echo '<td>' . esc_html($plugin->getDescription()) . '</td>';
            echo '<td><a class="composer-update-link" href="plugins.php?page=composer-manager-composer-install&plugin=' . esc_html($plugin->getId()) . '">Composer Update</a></td>';
            echo '</tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <th><?= __('Plugin Name', 'wp-composer-manager') ?></th>
            <th><?= __('Description', 'wp-composer-manager') ?></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    (function($) {
        $(function() {
            $('.composer-update-link').on('click', function(e) {
                var l = $(this);
                l.html('Please wait...').css('cursor', 'wait').blur();
                var i = $('<img />').css('height', '10px').css('width', '10px').css('marginLeft', '3px').css('marginTop', '3px')
                    .attr('src', '<?= site_url('/wp-includes/images/wpspin-2x.gif') ?>');
                l.after(i);
            });
        })
    }(jQuery));

</script>