<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="container">
    <h2>Tambah Mahasiswa</h2>
    <form action="index.php?controller=mahasiswa&action=store" method="POST">
        <label>NIM:</label><br>
        <input type="text" name="nim" required><br><br>

        <label>Nama:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Kelas:</label><br>
        <input type="text" name="kelas" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email"><br><br>

        <label>Praktikum:</label><br>
        <select name="praktikum_id" required>
            <?php foreach ($praktikumList as $p): ?>
                <option value="<?= $p['id']; ?>"><?= $p['nama_praktikum']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Simpan</button>
    </form>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
