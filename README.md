# Sales Intelligence Agent

Sales Intelligence Agent adalah aplikasi kasir pintar berbasis web yang tidak hanya mencatat transaksi penjualan, tetapi juga secara otomatis menganalisis pergerakan produk untuk mendeteksi *deadstock*. Dengan memanfaatkan kecerdasan buatan, sistem ini memberikan rekomendasi strategis seperti pemberian diskon yang tepat agar produk yang lambat terjual dapat segera berputar.

## Stack Teknologi
- **Bahasa Pemrograman & Framework**: PHP (Laravel) untuk backend, JavaScript (Vue.js + Inertia.js) untuk frontend, dan TailwindCSS untuk styling.
- **AI Recommendation**: Terintegrasi dengan **AI** untuk memproses data analisis *deadstock* dan menghasilkan rekomendasi diskon persentase serta strategi penjualan yang optimal.
- **Database**: Menggunakan **Database API** (PostgreSQL/SQLite) untuk penyimpanan dan pengelolaan data penjualan serta inventaris barang secara terpusat.

## Flow Aplikasi (Garis Besar)
1. **Input Transaksi**: Kasir memasukkan data pesanan atau penjualan harian secara *real-time* melalui antarmuka sistem kasir.
2. **Pemantauan & Analisis Stok**: Sistem secara berkala memantau jumlah stok barang dan menghitung durasi hari barang tidak terjual (tidak ada transaksi).
3. **Proses Rekomendasi (AI)**: Untuk barang yang terdeteksi sebagai *deadstock* atau *slow-moving*, sistem akan mengirimkan data riwayatnya ke OpenAI API guna mendapatkan analisis dan rekomendasi persentase diskon yang sesuai.
4. **Dashboard Keputusan**: Pemilik atau manajer toko dapat melihat ringkasan performa penjualan, grafik pendapatan bulanan, peringatan stok menipis, serta hasil saran diskon dari AI di halaman *Dashboard* untuk dieksekusi lebih lanjut.

Demo Web nya : https://swapartid.projectqin.my.id/login
