<?php \Netjog\Utils\Security::enforceWPKernel(); ?>
<?php
    /** @var array $virtualjogClientData */
?>
<div class="vjog-wrapper">
    <img class="vjog-header" src="<?php echo esc_html(\Netjog\Base\Asset::getImage('header-bg')); ?>" alt="Header background">

    <div class="vjog-content">

        <?php if ($virtualjogClientData): ?>

            <div class="vjog-content-box">
                <h1>Fiók adatok</h1>
                <p>Üdvözöljük, <?php echo esc_html($virtualjogClientData['name']) ?>!</p>
                <div class="vjog-card">
                    <h2>Csomagok</h2>
                    <ul>
                        <?php foreach ($virtualjogClientData['packages'] as $package): ?>
                            <li><?php echo esc_html($package['name']) ?> - <?php echo esc_html($package['subPackage']) ?> - <?php echo esc_html($package['subscriptionEndDate']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p>Ön már be van jelentkezve a Virtualjog rendszerbe.</p>
                    <p>Ha szeretne kijelentkezni, kattintson az alábbi gombra</p>
                    <form class="vjog-form-box" action="/virtualjog/logout" method="post">
                        <button class="btn btn-primary" type="submit">Kijelentkezés</button>
                    </form>
                </div>

            </div>

            <div class="vjog-content-box">
                <?php foreach ($virtualjogClientData['packages'] as $package): ?>
                    <?php $subsrciptionEndDate = new DateTime($package['subscriptionEndDate']); ?>
                    <?php $now = new DateTime(); ?>
                    <?php if ($package['slug'] === 'cookie-panel' && $subsrciptionEndDate > $now): ?>
                        <?php if (!get_option('virtualjog_cookie_module_enabled',false)): ?>
                            <div class="alert alert-success" role="alert">
                                <h3>Aktív süti modul előfizetés észlelve!</h3>
                                <p>A süti modul telepítve van a weboldalára. Az előfizetése <?php echo esc_html($package['subscriptionEndDate']) ?>-ig érvényes.</p>
                                <p>Amennyiben szeretné aktíválni a süti modult, kattintson az alábbi gombra.</p>
                                <a class="btn btn-primary" href="/wp-admin/admin.php?page=virtualjog/cookie">Süti modul beállítások</a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success" role="alert">
                                <h3>Süti panel aktív!</h3>
                                <p>Az ön süti panel előfizetése és az oldalon található bővítménye aktíválva van.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>

        <?php endif; ?>

    </div>
</div>

