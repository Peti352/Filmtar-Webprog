<h1>Képgaléria</h1>

<?php if (isset($_SESSION['user'])): ?>
    <section class="upload-section">
        <h2>Új kép feltöltése</h2>
        <form action="index.php?page=kepek" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="image_upload">
            <div class="form-group">
                <label for="kep">Válasszon képet:</label>
                <input type="file" name="kep" id="kep" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Kép feltöltése</button>
        </form>
    </section>
<?php else: ?>
    <section class="upload-section">
        <p class="info-message">Képek feltöltéséhez jelentkezzen be!</p>
    </section>
<?php endif; ?>

<section class="gallery-section">
    <h2>Feltöltött képek</h2>

    <?php
    try {
        $stmt = $dbh->prepare("SELECT k.id, k.fajlnev, k.feltoltve, f.csaladi_nev, f.utonev FROM g_kepek k LEFT JOIN g_felhasznalok f ON k.feltolto_id = f.id ORDER BY k.feltoltve DESC");
        $stmt->execute();
        $kepek = $stmt->fetchAll();
    } catch (PDOException $e) {
        $kepek = [];
    }
    ?>

    <?php if (!empty($kepek)): ?>
        <div class="gallery-grid">
            <?php foreach ($kepek as $kep): ?>
                <div class="gallery-item">
                    <a href="uploads/<?= htmlspecialchars($kep['fajlnev']) ?>" class="gallery-img">
                        <img src="uploads/<?= htmlspecialchars($kep['fajlnev']) ?>" alt="Feltöltött kép">
                    </a>
                    <div class="gallery-info">
                        <p class="gallery-uploader">Feltöltötte: <?= htmlspecialchars($kep['csaladi_nev'] . ' ' . $kep['utonev']) ?></p>
                        <p class="gallery-date"><?= date('Y. m. d. H:i', strtotime($kep['feltoltve'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="info-message">Még nincsenek feltöltött képek.</p>
    <?php endif; ?>
</section>
