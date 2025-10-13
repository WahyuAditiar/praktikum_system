<?php
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=template_mata_kuliah.csv");

// Header kolom
echo "kode_mk,nama_mk,sks,semester,jurusan,deskripsi,status\n";

// Contoh baris data
echo "TI101,Pemrograman Web,3,3,Teknik Informatika,Dasar-dasar pemrograman web,active\n";
echo "SI201,Basis Data,3,2,Sistem Informasi,Pengenalan basis data,active\n";
?>
