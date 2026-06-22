<?php

namespace App\Console\Commands;

use App\Services\SalesIntelligenceService;
use Illuminate\Console\Command;

class AnalyzeDeadstock extends Command
{
    protected $signature = 'sales:analyze-deadstock';

    protected $description = 'Analisis produk deadstock dan buat rekomendasi diskon AI';

    public function handle()
    {
        $this->info('🔍 Menganalisis produk deadstock...');

        $service = new SalesIntelligenceService();
        $results = $service->analyzeDeadstock();

        if (empty($results)) {
            $this->warn('Tidak ada produk deadstock ditemukan.');
            return 0;
        }

        $this->info('✅ Ditemukan ' . count($results) . ' produk deadstock:');
        $this->table(
            ['Produk', 'Hari Tidak Laku', 'Diskon'],
            $results
        );

        return 0;
    }
}
