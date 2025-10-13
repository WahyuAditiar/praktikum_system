<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="container">
    <h2>Edit Mahasiswa</h2>
    <form action="index.php?controller=mahasiswa&action=update" method="POST">
        <input type="hidden" name="id" value="<?= $mahasiswa['id']; ?>">

        <label>NIM:</label><br>
        <input type="text" name="nim" value="<?= $mahasiswa['nim']; ?>" required><br><br>

        <label>Nama:</label><br>
        <input type="text" name="nama" value="<?= $mahasiswa['nama']; ?>" required><br><br>

        <label>Kelas:</label><br>
        <input type="text" name="kelas" value="<?= $mahasiswa['kelas']; ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= $mahasiswa['email']; ?>"><br><br>

        <label>Praktikum:</label><br>
        <select name="praktikum_id" required>
            <?php foreach ($praktikumList as $p): ?>
                <option value="<?= $p['id']; ?>" 
                    <?= $mahasiswa['praktikum_id'] == $p['id'] ? 'selected' : ''; ?>>
                    <?= $p['nama_praktikum']; ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Update</button>
    </form>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
                