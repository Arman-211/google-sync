<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Google_Client;
use Google_Service_Sheets;
use Illuminate\Console\Command;

class FetchGoogleCommentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:google-comments {count?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение комментариев из Google Sheets';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('google-credentials.json'));
        $client->addScope(Google_Service_Sheets::SPREADSHEETS);
        $service = new Google_Service_Sheets($client);
        $setting = Setting::query()->first();

        if (!$setting || empty($setting->google_sheet_url)) {
            $this->error("❌ Ошибка: Google Sheet URL не установлен!");
            return;
        }

        $spreadsheetId = $setting->extractSpreadsheetId();
        $range = 'MyDataSheet!A1:D';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            $this->error("❌ Нет данных в Google Sheets!");
            return;
        }

        $count = $this->argument('count') ?? count($values);
        $this->info("📡 Синхронизация с Google Sheets...");
        $this->info("Будет загружено строк: $count\n");

        if (php_sapi_name() === 'cli') {
            $this->output->progressStart($count);
            $tableData = [];

            foreach (array_slice($values, 1, $count) as $row) {
                $tableData[] = [
                    'ID' => $row[0] ?? 'N/A',
                    'Name' => $row[1] ?? 'N/A',
                    'Status' => $row[2] ?? 'N/A',
                    'Comment' => $row[3] ?? '—'
                ];
                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
            $this->table(['ID', 'Name', 'Status', 'Comment'], $tableData);
            $this->info("✅ Завершено! Данные успешно загружены.");
        } else {
            echo "<h2>📡 Синхронизация с Google Sheets...</h2>";
            echo "<p style='font-size: 18px;'>Будет загружено строк: <strong>$count</strong></p>";
            echo "<table border='1' cellpadding='5' cellspacing='0' style='width:100%;border-collapse: collapse;'>";
            echo "<tr style='background-color: #007bff; color: white;'>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Comment</th>
                  </tr>";

            foreach (array_slice($values, 1, $count) as $row) {
                $id = $row[0] ?? 'N/A';
                $name = $row[1] ?? 'N/A';
                $status = $row[2] ?? 'N/A';
                $comment = $row[3] ?? '—';

                $statusColor = ($status === 'Allowed') ? 'green' : 'red';

                echo "<tr>
            <td>{$id}</td>
            <td>{$name}</td>
            <td style='color: $statusColor;'>{$status}</td>
            <td>{$comment}</td>
          </tr>";
            }

            echo "</table>";
            echo "<p style='color: green; font-weight: bold;'>✅ Завершено! Данные успешно загружены.</p>";
        }
    }
}
