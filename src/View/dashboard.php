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
    <hr>
    <h2><?= __('Plugins with composer.json files', 'wp-composer-manager') ?></h2>
    <p>These plugins have composer.json files in their root.</p>
    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th><?= __('Plugin Name', 'wp-composer-manager') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($this->plugins as $plugin) {

        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <th><?= __('Plugin Name', 'wp-composer-manager') ?></th>
        </tr>
        </tfoot>
    </table>
</div>
