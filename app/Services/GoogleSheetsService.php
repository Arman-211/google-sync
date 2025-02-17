<?php

namespace App\Services;

use App\Models\Setting;
use Google\Exception;
use Google_Client;
use Google_Service_Sheets;
use App\Models\Record;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_ClearValuesRequest;
use Google_Service_Sheets_ValueRange;

class GoogleSheetsService
{
    /**
     * @throws Exception
     * @throws \Google\Service\Exception
     * @throws \Exception
     */
    public function syncRecords(): void
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('google-credentials.json'));
        $client->addScope(Google_Service_Sheets::SPREADSHEETS);

        $service = new Google_Service_Sheets($client);
        $setting = Setting::query()->first();

        if (!$setting || empty($setting->google_sheet_url)) {
            throw new \Exception('Ошибка: Google Sheet URL не установлен! Пожалуйста, добавьте его в настройках.');
        }

        $spreadsheetId = $setting->extractSpreadsheetId();
        $sheetTitle = 'MyDataSheet';
        $range = "{$sheetTitle}!A1:D";

        $sheets = array_map(fn($s) => $s->getProperties()->getTitle(), $service->spreadsheets->get($spreadsheetId)->getSheets());
        if (!in_array($sheetTitle, $sheets)) {
            $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [['addSheet' => ['properties' => ['title' => $sheetTitle]]]]
            ]);
            $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
        }

        $existingData = $service->spreadsheets_values->get($spreadsheetId, $range)->getValues();
        $commentsMap = [];
        $existingIds = [];

        if (!empty($existingData)) {
            foreach ($existingData as $index => $row) {
                if ($index === 0) continue;
                if (!empty($row[0]) && is_numeric($row[0])) {
                    $existingIds[] = (int)$row[0];
                    $commentsMap[$row[0]] = $row[3] ?? '';
                }
            }
        }

        $allowedRecords = Record::query()->where('status', 'Allowed')->get();
        $allowedIds = $allowedRecords->pluck('id')->toArray();

        $recordsToDelete = array_diff($existingIds, $allowedIds);

        // Создаём новые данные, сохраняя комментарии
        $values = [['ID', 'Name', 'Status', 'Comment']];
        foreach ($allowedRecords as $record) {
            $comment = $commentsMap[$record->id] ?? '';
            $values[] = [$record->id, $record->name, $record->status, $comment];
        }

        if (!empty($recordsToDelete)) {
            foreach ($recordsToDelete as $id) {
                $deleteRange = "{$sheetTitle}!A2:D";
                $service->spreadsheets_values
                    ->clear($spreadsheetId, $deleteRange, new Google_Service_Sheets_ClearValuesRequest());
            }
        }

        $body = new Google_Service_Sheets_ValueRange(['values' => $values]);
        $params = ['valueInputOption' => 'RAW'];
        $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
    }


}
