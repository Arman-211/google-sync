<?php

namespace App\Console\Commands;

use App\Services\GoogleSheetsService;
use Google\Exception;
use Illuminate\Console\Command;

class SyncGoogleSheetCommand extends Command
{

    protected $signature = 'sync:google-sheet';
    protected $description = 'Синхронизация данных с Google Sheets';

    /**
     * @param GoogleSheetsService $googleSheetsService
     * @return void
     * @throws Exception
     * @throws \Google\Service\Exception
     */
    public function handle(GoogleSheetsService $googleSheetsService): void
    {
        $this->info('Запуск синхронизации...');
        $googleSheetsService->syncRecords();
        $this->info('Синхронизация завершена!');
    }
}
