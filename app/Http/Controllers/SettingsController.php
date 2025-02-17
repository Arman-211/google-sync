<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Google_Client;
use Google_Service_Sheets;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{


    /**
     * @return Factory|View|Application|object
     */
    public function edit()
    {
        $settings = Setting::query()->first();
        return view('settings', compact('settings'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate(['google_sheet_url' => 'required|url']);
        $spreadsheetId = $this->extractSpreadsheetId($request->google_sheet_url);
        $serviceAccountEmail = env('GOOGLE_SERVICE_ACCOUNT_EMAIL');

        if (!$this->hasEditPermission($spreadsheetId)) {
            return redirect()->route('settings.edit')->with('errors', nl2br("
                        ❌ У сервисного аккаунта **$serviceAccountEmail** нет прав на редактирование этой таблицы.

                        Пожалуйста, добавьте его как **Редактора** в Google Sheets:

                        1️⃣ Откройте таблицу
                        2️⃣ Нажмите **\"Поделиться\" (Share)**
                        3️⃣ Вставьте email: **$serviceAccountEmail**
                        4️⃣ Выберите **Редактор (Editor)** → **Отправить**
                        "));
        }
        Setting::query()->updateOrCreate([], $request->all());

        return redirect()->route('settings.edit')->with('success', 'Ссылка на Google Sheet сохранена!');
    }

    /**
     * @param $url
     * @return string|null
     */
    private function extractSpreadsheetId($url): ?string
    {
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * @param $spreadsheetId
     * @return bool
     */
    private function hasEditPermission($spreadsheetId): bool
    {
        try {
            $client = new Google_Client();
            $client->setAuthConfig(storage_path('google-credentials.json'));
            $client->addScope(Google_Service_Sheets::SPREADSHEETS);
            $service = new Google_Service_Sheets($client);

            $requests = [
                new \Google_Service_Sheets_Request([
                    'updateSpreadsheetProperties' => [
                        'properties' => ['title' => 'Access Test'],
                        'fields' => 'title',
                    ]
                ])
            ];

            $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => $requests
            ]);

            $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
