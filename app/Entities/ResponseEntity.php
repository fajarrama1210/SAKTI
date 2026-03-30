<?php

namespace App\Entities;

class ResponseEntity
{
    const MSG_SUCCESS_CREATE = 'Data berhasil ditambahkan!';
    const MSG_SUCCESS_UPDATE = 'Data berhasil diperbarui!';
    const MSG_SUCCESS_DELETE = 'Data berhasil dihapus!';
    const MSG_ERROR_SERVER = 'Terjadi kesalahan sistem, silakan coba lagi.';
    const MSG_ERROR_NOT_FOUND = 'Data tidak ditemukan.';
    const MSG_ERR_CONSTRAINT = 'Gagal menghapus! Data ini masih digunakan oleh pencatatan lain dan tidak dapat dihapus.';
    const MSG_ERR_ACADEMIC_YEAR_HAS_SEMESTER = 'Gagal menghapus! Tahun ajaran ini masih digunakan oleh data Semester.';
    const MSG_ERR_ACADEMIC_YEAR_HAS_RATE = 'Gagal menghapus! Tahun ajaran ini masih digunakan oleh data Tarif Pembayaran.';
    const MSG_ERR_ACADEMIC_YEAR_HAS_BILL = 'Gagal menghapus! Tahun ajaran ini masih memiliki data Tagihan.';
    const MSG_ERR_SEMESTER_HAS_BILL = 'Gagal menghapus! Semester ini sudah memiliki data tagihan aktif yang terkait dengan rentang bulannya.';
}
