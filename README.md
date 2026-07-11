# Sales Intelligence Agent

## Summary
Aplikasi ini dirancang untuk membantu pemilik toko atau tim operasional memantau performa penjualan, mengelola stok, dan mendapatkan rekomendasi diskon secara lebih cerdas. Sistem menggabungkan data transaksi, informasi produk, dan analisis stok dalam satu pengalaman yang mudah dipantau.

## Stack / Teknis
- Bahasa pemrograman: PHP dengan Laravel sebagai backend, JavaScript dengan Vue 3 untuk antarmuka, serta Tailwind CSS dan Vite untuk tampilan dan build frontend.
- AI Recommendation: sistem saat ini menggunakan logika rekomendasi internal untuk mendeteksi produk deadstock dan menyarankan diskon, dengan potensi integrasi ke API OpenAI untuk menghasilkan insight dan rekomendasi yang lebih natural.
- Database: data disimpan dan diolah melalui database relasional, dengan koneksi Laravel ke sumber data penjualan dan produk untuk mendukung transaksi, stok, dan analisis.

## Aspek Non-Teknis
- Target pengguna: pemilik toko, admin, dan tim operasional yang membutuhkan visibilitas cepat terhadap penjualan dan stok.
- Nilai bisnis: membantu mempercepat keputusan promosi, mengurangi risiko stok menumpuk, dan meningkatkan efisiensi pengelolaan produk.

## Flow Aplikasi (Garis Besar)
1. Pengguna membuka dashboard untuk melihat ringkasan penjualan, stok, dan status produk.
2. Data transaksi dan produk diproses dari aktivitas penjualan serta pengelolaan stok.
3. Sistem menganalisis produk yang lama tidak terjual atau cenderung deadstock.
4. Rekomendasi diskon dan informasi penjualan ditampilkan untuk membantu keputusan promosi.
5. Proses transaksi dapat dilakukan secara langsung, lalu data tersebut akan memengaruhi stok dan laporan yang tampil di dashboard.


Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
