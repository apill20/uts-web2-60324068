<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    // TODO: Ambil ID dari GET
    $id_kategori = $_GET['id'] ?? null;

    if (!$id_kategori) {
        header("Location: index.php?error=" . urlencode("ID Kategori tidak valid!"));
        exit();
    }
    
    // TODO: Retrieve data berdasarkan ID
    $stmt_get = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
    $stmt_get->bind_param("i", $id_kategori);
    $stmt_get->execute();
    $result = $stmt_get->get_result();

    if ($result->num_rows === 0) {
        // Jika data tidak ada di database, redirect
        header("Location: index.php?error=" . urlencode("Data kategori tidak ditemukan!"));
        exit();
    }

    $kategori = $result->fetch_assoc();
    $stmt_get->close();

    // Inisialisasi variabel dengan data dari database untuk pre-fill form
    $errors = [];
    $kode = $kategori['kode_kategori'];
    $nama = $kategori['nama_kategori'];
    $deskripsi = $kategori['deskripsi'];
    $status = $kategori['status'];
    
    // TODO: Jika POST, proses update
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Ambil dan bersihkan data dari form
        $kode = trim($_POST['kode_kategori'] ?? '');
        $nama = trim($_POST['nama_kategori'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $status = $_POST['status'] ?? 'Aktif';

        // Validasi kode kategori
        if (empty($kode)) {
            $errors[] = "Kode Kategori wajib diisi.";
        } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
            $errors[] = "Panjang Kode Kategori harus antara 4 hingga 10 karakter.";
        } elseif (substr($kode, 0, 4) !== "KAT-") {
            $errors[] = "Kode Kategori harus diawali dengan 'KAT-'.";
        }
        
        // Validasi nama kategori
        if (empty($nama)) {
            $errors[] = "Nama Kategori wajib diisi.";
        } elseif (strlen($nama) < 3 || strlen($nama) > 50) {
            $errors[] = "Panjang Nama Kategori harus antara 3 hingga 50 karakter.";
        }
        
        // Validasi deskripsi
        if (!empty($deskripsi) && strlen($deskripsi) > 200) {
            $errors[] = "Deskripsi maksimal 200 karakter.";
        }

        // Validasi Status
        if (!in_array($status, ['Aktif', 'Nonaktif'])) {
            $errors[] = "Pilihan status tidak valid.";
        }

        // Cek duplikasi kode (exclude record yang sedang diedit)
        if (empty($errors)) {
            $stmt_check = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?");
            $stmt_check->bind_param("si", $kode, $id_kategori);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                $errors[] = "Kode Kategori '$kode' sudah digunakan oleh kategori lain.";
            }
            $stmt_check->close();
        }

        // Proses Update Data
        if (empty($errors)) {
            $kode_clean = htmlspecialchars($kode);
            $nama_clean = htmlspecialchars($nama);
            $deskripsi_clean = htmlspecialchars($deskripsi);
            $status_clean = htmlspecialchars($status);

            $stmt_update = $conn->prepare("UPDATE kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ? WHERE id_kategori = ?");
            $stmt_update->bind_param("ssssi", $kode_clean, $nama_clean, $deskripsi_clean, $status_clean, $id_kategori);
            
            if ($stmt_update->execute()) {
                header("Location: index.php?success=" . urlencode("Data kategori berhasil diupdate!"));
                exit();
            } else {
                $errors[] = "Terjadi kesalahan saat mengupdate data: " . $conn->error;
            }
            $stmt_update->close();
        }
    }
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Kategori</h4>
                    </div>
                    <div class="card-body">
                        <!-- TODO: Form dengan data pre-filled -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="kode_kategori" class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode_kategori" name="kode_kategori" value="<?= htmlspecialchars($kode) ?>" required>
                                <div class="form-text">Contoh: KAT-004. Harus diawali 'KAT-' (4-10 karakter).</div>
                            </div>

                            <div class="mb-3">
                                <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?= htmlspecialchars($nama) ?>" required>
                                <div class="form-text">Minimal 3 dan maksimal 50 karakter.</div>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($deskripsi) ?></textarea>
                                <div class="form-text">Opsional. Maksimal 200 karakter.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusAktif" value="Aktif" <?= ($status == 'Aktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="statusAktif">Aktif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusNonaktif" value="Nonaktif" <?= ($status == 'Nonaktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="statusNonaktif">Nonaktif</label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
