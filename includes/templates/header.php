<div class="wrap">

    <?php
    $base_url = '?page=turn14-dashboard';
    ?>
    <div style="margin-bottom: 20px;">
        <img src="https://www.turn14.com/images/header/logo.png"> 
    </div>

    <h2 class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab) : ?>
            <?php
            $active = '';
            if (! empty($_GET['tab']) && $_GET['tab'] == $tab['tab_url']) {
                $active = 'nav-tab-active';
            }
            if (empty($_GET['tab']) && $tab['tab_url'] == '') {
                $active = 'nav-tab-active';
            }
            ?>
            <a href="<?php echo esc_url($base_url . ($tab['tab_url'] ? '&tab=' . $tab['tab_url'] : '')); ?>" class="nav-tab <?php echo esc_attr($active);?>"><?php echo esc_html($tab['name']); ?></a>
        <?php endforeach; ?>
    </h2>