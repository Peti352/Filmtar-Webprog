<?php
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
    default:
        ?>
        <h1>Filmek kezelése (CRUD)</h1>
        <a href="index.php?page=crud&action=uj" class="btn btn-success">+ Új film hozzáadása</a>

        <?php
        $stmt = $dbh->prepare('SELECT * FROM filmek ORDER BY id DESC');
        $stmt->execute();
        $filmek = $stmt->fetchAll();

        if (count($filmek) > 0):
        ?>
            <div class="table-responsive">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>
                            <th>Cím</th>
                            <th>Rendező</th>
                            <th>Év</th>
                            <th>Műfaj</th>
                            <th>Értékelés</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filmek as $film): ?>
                            <tr>
                                <td><?= (int)$film['id'] ?></td>
                                <td><?= htmlspecialchars($film['cim']) ?></td>
                                <td><?= htmlspecialchars($film['rendezo']) ?></td>
                                <td><?= (int)$film['ev'] ?></td>
                                <td><?= htmlspecialchars($film['mufaj']) ?></td>
                                <td><?= isset($film['ertekeles']) ? number_format((float)$film['ertekeles'], 1) : '–' ?></td>
                                <td class="actions">
                                    <a href="index.php?page=crud&action=szerkeszt&id=<?= (int)$film['id'] ?>" class="btn btn-primary btn-sm">Szerkesztés</a>
                                    <a href="index.php?page=crud&action=torol&id=<?= (int)$film['id'] ?>" class="btn btn-danger btn-sm">Törlés</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-message">Még nincsenek filmek az adatbázisban.</p>
        <?php endif; ?>
        <?php
        break;

    case 'uj':
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        ?>
        <h1>Új film hozzáadása</h1>

        <?php if (isset($_SESSION['form_errors'])): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($_SESSION['form_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['form_errors']); ?>
        <?php endif; ?>

        <form action="index.php?page=crud" method="POST" class="crud-form">
            <input type="hidden" name="action" value="crud_create">

            <div class="form-group">
                <label for="cim">Cím <span class="required">*</span></label>
                <input type="text" id="cim" name="cim" required value="<?= htmlspecialchars($formData['cim'] ?? '') ?>" placeholder="Pl. A keresztapa">
            </div>

            <div class="form-group">
                <label for="rendezo">Rendező <span class="required">*</span></label>
                <input type="text" id="rendezo" name="rendezo" required value="<?= htmlspecialchars($formData['rendezo'] ?? '') ?>" placeholder="Pl. Francis Ford Coppola">
            </div>

            <div class="form-group">
                <label for="ev">Év <span class="required">*</span></label>
                <input type="number" id="ev" name="ev" required min="1888" max="<?= date('Y') + 5 ?>" value="<?= htmlspecialchars($formData['ev'] ?? '') ?>" placeholder="Pl. 1972">
            </div>

            <div class="form-group">
                <label for="mufaj">Műfaj <span class="required">*</span></label>
                <input type="text" id="mufaj" name="mufaj" required value="<?= htmlspecialchars($formData['mufaj'] ?? '') ?>" placeholder="Pl. Dráma">
            </div>

            <div class="form-group">
                <label for="ertekeles">Értékelés (0–10)</label>
                <input type="number" id="ertekeles" name="ertekeles" step="0.1" min="0" max="10" value="<?= htmlspecialchars($formData['ertekeles'] ?? '') ?>" placeholder="Pl. 9.2">
            </div>

            <div class="form-group">
                <label for="leiras">Leírás</label>
                <textarea id="leiras" name="leiras" rows="5" placeholder="Rövid ismertető a filmről..."><?= htmlspecialchars($formData['leiras'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Mentés</button>
                <a href="index.php?page=crud" class="btn btn-secondary">Vissza a listához</a>
            </div>
        </form>
        <?php
        break;

    case 'szerkeszt':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $stmt = $dbh->prepare('SELECT * FROM filmek WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $film = $stmt->fetch();

        if (!$film):
        ?>
            <div class="alert alert-danger">
                <p>A keresett film nem található (ID: <?= $id ?>).</p>
                <a href="index.php?page=crud" class="btn btn-secondary">Vissza a listához</a>
            </div>
        <?php
            break;
        endif;

        $formData = $_SESSION['form_data'] ?? $film;
        unset($_SESSION['form_data']);
        ?>
        <h1>Film szerkesztése</h1>

        <?php if (isset($_SESSION['form_errors'])): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($_SESSION['form_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['form_errors']); ?>
        <?php endif; ?>

        <form action="index.php?page=crud" method="POST" class="crud-form">
            <input type="hidden" name="action" value="crud_update">
            <input type="hidden" name="id" value="<?= (int)$id ?>">

            <div class="form-group">
                <label for="cim">Cím <span class="required">*</span></label>
                <input type="text" id="cim" name="cim" required value="<?= htmlspecialchars($formData['cim'] ?? '') ?>" placeholder="Pl. A keresztapa">
            </div>

            <div class="form-group">
                <label for="rendezo">Rendező <span class="required">*</span></label>
                <input type="text" id="rendezo" name="rendezo" required value="<?= htmlspecialchars($formData['rendezo'] ?? '') ?>" placeholder="Pl. Francis Ford Coppola">
            </div>

            <div class="form-group">
                <label for="ev">Év <span class="required">*</span></label>
                <input type="number" id="ev" name="ev" required min="1888" max="<?= date('Y') + 5 ?>" value="<?= htmlspecialchars($formData['ev'] ?? '') ?>" placeholder="Pl. 1972">
            </div>

            <div class="form-group">
                <label for="mufaj">Műfaj <span class="required">*</span></label>
                <input type="text" id="mufaj" name="mufaj" required value="<?= htmlspecialchars($formData['mufaj'] ?? '') ?>" placeholder="Pl. Dráma">
            </div>

            <div class="form-group">
                <label for="ertekeles">Értékelés (0–10)</label>
                <input type="number" id="ertekeles" name="ertekeles" step="0.1" min="0" max="10" value="<?= htmlspecialchars($formData['ertekeles'] ?? '') ?>" placeholder="Pl. 9.2">
            </div>

            <div class="form-group">
                <label for="leiras">Leírás</label>
                <textarea id="leiras" name="leiras" rows="5" placeholder="Rövid ismertető a filmről..."><?= htmlspecialchars($formData['leiras'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Módosítás mentése</button>
                <a href="index.php?page=crud" class="btn btn-secondary">Vissza a listához</a>
            </div>
        </form>
        <?php
        break;

    case 'torol':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $stmt = $dbh->prepare('SELECT * FROM filmek WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $film = $stmt->fetch();

        if (!$film):
        ?>
            <div class="alert alert-danger">
                <p>A keresett film nem található (ID: <?= $id ?>).</p>
                <a href="index.php?page=crud" class="btn btn-secondary">Vissza a listához</a>
            </div>
        <?php
            break;
        endif;
        ?>
        <h1>Film törlése</h1>

        <div class="confirm-box">
            <p class="confirm-message">Biztosan törölni szeretné a következő filmet: <strong><?= htmlspecialchars($film['cim']) ?></strong>?</p>

            <div class="confirm-details">
                <p><strong>Rendező:</strong> <?= htmlspecialchars($film['rendezo']) ?></p>
                <p><strong>Év:</strong> <?= (int)$film['ev'] ?></p>
                <p><strong>Műfaj:</strong> <?= htmlspecialchars($film['mufaj']) ?></p>
                <?php if (!empty($film['ertekeles'])): ?>
                    <p><strong>Értékelés:</strong> <?= number_format((float)$film['ertekeles'], 1) ?>/10</p>
                <?php endif; ?>
            </div>

            <form action="index.php?page=crud" method="POST" class="confirm-actions">
                <input type="hidden" name="action" value="crud_delete">
                <input type="hidden" name="id" value="<?= (int)$id ?>">
                <button type="submit" class="btn btn-danger">Igen, törlés</button>
                <a href="index.php?page=crud" class="btn btn-secondary">Mégsem</a>
            </form>
        </div>
        <?php
        break;
}
