<?php
require_once 'config/database.php';
 
// TODO: Validasi ID dari GET
$id_kategori = $_GET['id'] ?? null;

if (!$id_kategori) {
    // Jika tidak ada parameter ID di URL
    header("Location: index.php?error=" . urlencode("ID Kategori tidak ditemukan!"));
    exit();
}
 
// TODO: Cek keberadaan data
$stmt_check = $conn->prepare("SELECT id_kategori FROM kategori WHERE id_kategori = ?");
$stmt_check->bind_param("i", $id_kategori);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows === 0) {
    // Jika ID ada di URL tapi datanya tidak ada di database
    $stmt_check->close();
    header("Location: index.php?error=" . urlencode("Data kategori tidak valid atau sudah terhapus!"));
    exit();
}
$stmt_check->close();
 
// TODO: Delete data
$stmt_delete = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$stmt_delete->bind_param("i", $id_kategori);
$stmt_delete->execute();
 
// Cek affected_rows untuk memastikan berhasil (mengecek apakah ada baris yang terpengaruh/terhapus)
if ($stmt_delete->affected_rows > 0) {
    $status = "success";
    $pesan = "Data kategori berhasil dihapus!";
} else {
    $status = "error";
    $pesan = "Gagal menghapus data kategori: " . $conn->error;
}
$stmt_delete->close();

// TODO: Redirect dengan pesan
header("Location: index.php?" . $status . "=" . urlencode($pesan));
exit();
?>