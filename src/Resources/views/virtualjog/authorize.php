<?php \Netjog\Utils\Security::enforceWPKernel(); ?>
<?php
/** @var array $virtualjogClientData */

$alerts = \Netjog\Utils\Alert::obtain();

?>
<div class="vjog-wrapper">
    <img class="vjog-header" src="<?php echo esc_html(\Netjog\Base\Asset::getImage('header-bg')); ?>" alt="Header background">

    <div class="vjog-content">
        <div class="vjog-content-box">
            <h1>Virtualjog</h1>
            <p>10+ éves adatvédelmi- és internetjogi tapasztalat után megalkottuk a folyamatos jogvédelmet biztosító szolgáltatásunkat, mely megkönnyíti a weboldal üzemeltetők életét. Naprakész ÁSZF-et, Adatkezelési tájékoztatót, cookie panelt, cookie szabálytatot, ÁSZF generátort és szerződés-generátort készítünk legaltech alkalmazásunkkal. A VirtualJog a Net-jog.hu csoport tagja. </p>
        </div>

        <div class="vjog-content-box">
            <h2>Nincs még fiókja? Regisztráljon most!</h2>
            <p>Ha még nem regisztrált, kérjük válasszon csomagot a virtualjog.hu oldalon, és regisztráljon a rendszerbe.</p>
            <a class="btn btn-primary" target="_blank" href="https://virtualjog.hu/">Irány a virtualjog.hu</a>
        </div>

        <div class="vjog-content-box">
            <h2>Bejelentkezés a Virtualjog rendszerbe</h2>
            <p>Ha már regisztrált, kérjük írja be a hozzáférési kódját az alábbi mezőbe, és kattintson a hitelesítés gombra</p>
            <?php if (!empty($alerts)): ?>
                <?php foreach ($alerts as $alert): ?>
                    <div class="alert alert-<?php echo esc_html($alert['type']) ?>">
                        <?php echo esc_html($alert['message']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <form class="vjog-form-box" action="/virtualjog/authorize" method="post">
                <?php wp_nonce_field('security_authorize'); ?>
                <input class="form-control" type="text" name="access_token" placeholder="Hozzáférési kód">
                <button class="btn btn-primary" type="submit">Hitelesítés</button>
            </form>
        </div>

    </div>
</div>