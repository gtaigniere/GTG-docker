<?php
if (isset($photos)) {
?>
    <section id="section_galerie">

        <h1>Galerie</h1>

        <?php foreach ($photos as $photo) : ?>

        <figure>

            <img src="<?= $photo ?>" alt="<?= basename($photo) ?>" />

        </figure>

        <?php endforeach; ?>

    </section>
<?php
}
?>
