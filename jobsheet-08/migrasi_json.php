<?php
require __DIR__ . '/includes/koneksi.php';

$jsonPath = __DIR__ . '/data/buku.json';   // sesuaikan lokasi file JSON-mu
$data = json_decode(file_get_contents($jsonPath), true);

if (!is_array($data)) {
    die("Gagal membaca JSON: " . json_last_error_msg());
}

$stmt = $pdo->prepare(
    "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori)
     VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)"
);

$pdo->beginTransaction();
try {
    foreach ($data as $b) {
        $stmt->execute([
            'judul'     => $b['judul'],
            'pengarang' => $b['pengarang'],
            'tahun'     => (int) $b['tahun'],
            'isbn'      => $b['isbn'] ?? null,
            'stok'      => (int) ($b['stok'] ?? 0),
            'kategori'  => $b['kategori'] ?? null,
        ]);
    }
    $pdo->commit();
    echo "Berhasil memigrasi " . count($data) . " buku.";
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Migrasi gagal, tidak ada data yang tersimpan: " . $e->getMessage());
}