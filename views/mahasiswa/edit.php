<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mahasiswa</title>
</head>
<body>
    <h2>Edit Data Mahasiswa</h2>
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

        <label>Pilih Praktikum:</label><br>
        <select name="praktikum_id" required>
            <?php foreach ($praktikumList as $p): ?>
                <option value="<?= $p['id']; ?>" 
                    <?= ($mahasiswa['praktikum_id'] == $p['id']) ? 'selected' : ''; ?>>
                    <?= $p['nama_praktikum']; ?> (<?= $p['semester']; ?> - <?= $p['tahun_ajaran']; ?>)
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>
