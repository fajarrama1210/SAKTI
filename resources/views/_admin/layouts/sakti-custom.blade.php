{{-- CSS Custom SAKTI --}}
<style>
    /* Card Utama dengan Shadow Custom */
    .sakti-card {
        background: #ffffff;
        border-radius: 15px;
        border: none;
        box-shadow:
            0 20px 45px rgb(73, 106, 77, 0.15), /* Opacity disesuaikan sedikit agar teks dalam card tetap fokus */
            0 10px 25px rgba(45, 206, 137, .18),
            inset 0 1px 1px rgba(255, 255, 255, .08);
        overflow: hidden;
    }

    /* Tombol & Elemen dengan Gradasi Hijau SAKTI */
    .btn-sakti-primary {
        background: linear-gradient(135deg, #07814e 45%, #1e905f 100%);
        color: #ffffff !important;
        border: none;
        box-shadow: 0 4px 6px rgba(7, 129, 78, 0.2);
        transition: all 0.3s ease;
    }

    .btn-sakti-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(7, 129, 78, 0.3);
    }

    .text-sakti-green {
        color: #07814e !important;
    }

    /* Styling Khusus Keterangan Status (Legend) */
    .legend-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem; /* Jarak antar kapsul keterangan */
        align-items: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem; /* Jarak pas antara ikon dan teks di dalam kapsul */
        background: #f8fafc;
        padding: 0.35rem 0.8rem 0.35rem 0.35rem;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .legend-item:hover {
        background: #ffffff;
        border-color: #07814e;
        box-shadow: 0 2px 8px rgba(7, 129, 78, 0.1);
    }

    .legend-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        font-size: 11px;
        color: #ffffff;
    }

    .legend-text {
        font-size: 0.8rem;
        font-weight: 600;
        color: #334155;
        line-height: 1;
    }

    /* Warna spesifik ikon legend */
    .icon-lunas { background-color: #2dce89; }
    .icon-sebagian { background-color: #fb6340; }
    .icon-menunggak { background-color: #f5365c; }
    .icon-belum {
        background-color: transparent;
        border: 1.5px solid #8898aa;
        color: #8898aa;
    }
    .icon-batal { background-color: #8898aa; }
</style>
