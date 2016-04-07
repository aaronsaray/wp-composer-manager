<div class='wrap'>
    <h1><?= __('Composer Manager', 'wp-composer-manager') ?></h1>
    <hr>
    <h2>
        <?php
        echo __('Run Composer Install on Plugin', 'wp-composer-manager');
        if (!empty($this->plugin)) {
            echo " " . esc_html($this->plugin->getName());
        }
        ?>
    </h2>
    <?php
    if (!empty($this->errors)) {
        echo '<p class="error-message">There were errors:</p>';
        echo '<ul>';
        foreach ($this->errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul>';
    }
    else {
        echo '<p class="success-message">The <code>composer update</code> function was successful.</p>';
        echo '<pre>' . implode("\n", $this->composerOutput) . '</pre>';
    }
    ?>
    <a href="plugins.php?page=composer-manager" class="button button-primary">Back to Plugin Dashboard</a>
</div>