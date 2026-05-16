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
    const MSG_VAL_REQUIRED = ':attribute wajib diisi.';
    const MSG_VAL_STRING = ':attribute harus berupa teks.';
    const MSG_VAL_MAX = ':attribute maksimal :max karakter.';
    const MSG_VAL_MIN = ':attribute minimal :min karakter.';
    const MSG_VAL_SIZE = ':attribute harus tepat :size karakter.';
    const MSG_VAL_DATE = ':attribute harus berupa tanggal yang valid.';
    const MSG_VAL_AFTER = ':attribute harus setelah :date.';
    const MSG_VAL_BOOLEAN = ':attribute harus berupa nilai benar atau salah.';
    const MSG_VAL_EXISTS = ':attribute yang dipilih tidak valid atau tidak ditemukan.';
    const MSG_VAL_INTEGER = ':attribute harus berupa angka bulat.';
    const MSG_VAL_NUMERIC = ':attribute harus berupa angka.';
    const MSG_VAL_BETWEEN = ':attribute harus antara :min dan :max.';
    const MSG_VAL_IN = ':attribute yang dipilih tidak valid.';
    const MSG_VAL_UNIQUE = ':attribute sudah digunakan, silakan gunakan yang lain.';
}
