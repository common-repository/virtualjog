<?php \Netjog\Utils\Security::enforceWPKernel(); ?>
<?php /** @var array $documents */ ?>
<div class="vjog-wrapper">
    <img class="vjog-header" src="<?php echo esc_html(\Netjog\Base\Asset::getImage('header-bg')); ?>" alt="Header background">

    <div class="vjog-content">

        <div class="vjog-content-box">
            <h1>Dokumentumok</h1>

            <?php foreach ($documents as $document): ?>
                <div class="vjog-card">
                    <div class="vjog-card-header">
                        <h2><?php echo esc_html($document['name']) ?></h2>
                        <span class="vjog-version-badge">
                           Verzió: <?php echo esc_html($document['lastVersion']) ?>
                        </span>
                    </div>
                    <p><b>Utolsó módosítás leírása: </b><?php echo esc_html($document['lastVersionMessage']) ?></p>
                    <?php if ($document['isInserted']): ?>
                        <a href="<?php echo esc_url(home_url()) . '/' . esc_html($document['slug']) ?>" class="btn btn-success" target="_blank">Megtekintés</a>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            A dokumentum még nincs beillesztve az oldalára
                        </div>
                        <form method="post" action="/virtualjog/document-insert?nonce=<?php echo esc_html(wp_create_nonce('insert_document')) ?>">
                            <?php wp_nonce_field('document_insert'); ?>
                            <input type="hidden" name="documentName" value="<?php echo esc_html($document['name']) ?>">
                            <input type="hidden" name="documentSlug" value="<?php echo esc_html($document['slug']) ?>">
                            <input type="hidden" name="embedUrl" value="<?php echo esc_html($document['embedUrl']) ?>">
                            <button class="btn btn-success" type="submit">Beillesztés</button>
                        </form>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

