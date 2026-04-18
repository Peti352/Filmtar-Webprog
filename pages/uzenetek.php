<h1>Üzenetek</h1>

<?php if (!isset($_SESSION['user'])): ?>
    <p class="error-message">Az üzenetek megtekintéséhez bejelentkezés szükséges!</p>
<?php else: ?>

    <?php
    try {
        $stmt = $dbh->prepare("SELECT u.id, u.kuldo_id, u.nev, u.email, u.targy, u.uzenet, u.kuldve FROM uzenetek u ORDER BY u.kuldve DESC");
        $stmt->execute();
        $uzenetek = $stmt->fetchAll();
    } catch (PDOException $e) {
        $uzenetek = [];
    }
    ?>

    <?php if (!empty($uzenetek)): ?>
        <section class="messages-section">
            <div class="table-responsive">
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Küldő neve</th>
                            <th>E-mail</th>
                            <th>Tárgy</th>
                            <th>Üzenet</th>
                            <th>Küldés ideje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sorszam = 1; foreach ($uzenetek as $uzenet): ?>
                            <tr>
                                <td><?= $sorszam++ ?></td>
                                <td><?php if ($uzenet['kuldo_id'] === null): ?><span class="guest-sender">Vendég</span><?php else: ?><?= htmlspecialchars($uzenet['nev']) ?><?php endif; ?></td>
                                <td><?= htmlspecialchars($uzenet['email']) ?></td>
                                <td><?= htmlspecialchars($uzenet['targy']) ?></td>
                                <td class="message-text"><?= nl2br(htmlspecialchars($uzenet['uzenet'])) ?></td>
                                <td><?= date('Y. m. d. H:i', strtotime($uzenet['kuldve'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php else: ?>
        <p class="info-message">Még nincsenek üzenetek.</p>
    <?php endif; ?>

<?php endif; ?>
