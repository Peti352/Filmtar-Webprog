<?php
/**
 * kepek.php - Képgaléria oldal
 *
 * Képek megjelenítése galéria nézetben.
 * Bejelentkezett felhasználók új képet tölthetnek fel.
 *
 * Elérhető változók:
 *   $dbh      - PDO adatbázis-kapcsolat
 *   $_SESSION - Munkamenet adatok
 *
 * Adatbázis tábla: kepek
 *   - id, felhasznalo_id, fajlnev, feltoltve
 * Kapcsolódó tábla: felhasznalok
 *   - id, csaladi_nev, utonev
 */
?>

<h1>Képgaléria</h1>

<!-- Feltöltési űrlap - csak bejelentkezett felhasználóknak -->
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

<!-- Galéria szekció - Feltöltött képek megjelenítése -->
<section class="gallery-section">
    <h2>Feltöltött képek</h2>

    <?php
    // Képek lekérdezése az adatbázisból, feltöltő nevével együtt
    try {
        $stmt = $dbh->prepare("
            SELECT k.id, k.fajlnev, k.feltoltve,
                   f.csaladi_nev, f.utonev
            FROM kepek k
            LEFT JOIN felhasznalok f ON k.feltolto_id = f.id
            ORDER BY k.feltoltve DESC
        ");
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
                    <!-- Kép megjelenítése - kattintható a lightbox-hoz -->
                    <a href="uploads/<?= htmlspecialchars($kep['fajlnev']) ?>" class="gallery-img">
                        <img
                            src="uploads/<?= htmlspecialchars($kep['fajlnev']) ?>"
                            alt="Feltöltött kép - <?= htmlspecialchars($kep['csaladi_nev'] . ' ' . $kep['utonev']) ?>">
                    </a>
                    <!-- Kép adatai -->
                    <div class="gallery-info">
                        <p class="gallery-uploader">
                            Feltöltötte: <?= htmlspecialchars($kep['csaladi_nev'] . ' ' . $kep['utonev']) ?>
                        </p>
                        <p class="gallery-date">
                            <?= date('Y. m. d. H:i', strtotime($kep['feltoltve'])) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="info-message">Még nincsenek feltöltött képek.</p>
    <?php endif; ?>
</section>
