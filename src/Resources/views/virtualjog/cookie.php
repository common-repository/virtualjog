<?php \Netjog\Utils\Security::enforceWPKernel(); ?>
<?php
    /** @var bool $cookieModuleAllowed */
    /** @var bool $cookieModuleEnabled */
    /** @var string $currentDomain */
    /** @var array $domains */
?>
<div class="vjog-wrapper">
    <img class="vjog-header" src="<?php echo esc_html(\Netjog\Base\Asset::getImage('header-bg')); ?>" alt="Header background">

    <div class="vjog-content">

        <div class="vjog-content-box">
            <h1>Süti modul</h1>

            <div class="vjog-card">

                <?php if ($cookieModuleEnabled): ?>
                    <div class="vjog-card-header">
                        <h2>Süti modul státusz</h2>
                        <span class="vjog-version-badge">engedélyezve</span>
                    </div>
                <?php else: ?>
                    <div class="vjog-card-header">
                        <h2>Süti modul domain ellenőrzés</h2>
                        <span class="vjog-version-badge">ellenőrizve</span>
                    </div>
                    <p><b>Domain: </b><?php echo esc_html($currentDomain) ?></p>
                    <p><b>Kliens által meghatározótt domainek </b><?php echo esc_html(implode(', ', $domains)) ?></p>
                <?php endif; ?>

                <?php if ($cookieModuleAllowed): ?>

                    <?php if ($cookieModuleEnabled): ?>
                        <div class="alert alert-success">
                            Süti modul engedélyezve az oldalon
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            Az oldal felismerve a kliens által meghatározott domainek között
                        </div>
                    <?php endif; ?>


                    <?php if ($cookieModuleEnabled): ?>
                        <a class="btn btn-danger" href="/virtualjog/disable-cookie-module">Süti modul kikapcsolása az oldalon</a>
                    <?php else: ?>
                        <a class="btn btn-success" href="/virtualjog/enable-cookie-module">Süti modul engedélyezése az oldalon</a>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-danger">
                        Az oldal nem található a kliens által meghatározott domainek között
                    </div>
                <?php endif; ?>


            </div>
        </div>
    </div>
</div>

