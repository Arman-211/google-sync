<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Services\GoogleSheetsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecordController extends Controller
{
    /**
     * @return View|Factory
     */
    public function index(): View|Factory
    {
        return view('records.index', ['records' => Record::query()->paginate(10)]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Allowed,Prohibited',
        ]);

        Record::query()->create($request->all());
        return redirect()->route('records.index');
    }

    /**
     * @return RedirectResponse
     */
    public function generate(): RedirectResponse
    {
        $records = [];
        for ($i = 0; $i < 1000; $i++) {
            $records[] = [
                'name' => 'Record ' . ($i + 1),
                'status' => $i % 2 == 0 ? 'Allowed' : 'Prohibited',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Record::query()->insert($records);
        return redirect()->route('records.index');
    }

    /**
     * @param GoogleSheetsService $googleSheetsService
     * @return RedirectResponse
     */
    public function sync(GoogleSheetsService $googleSheetsService): RedirectResponse
    {
        try {
            $googleSheetsService->syncRecords();
            return redirect()->route('records.index')->with('success', 'Синхронизация завершена!');
        } catch (\Exception $e) {
            Log::error('Ошибка синхронизации с Google Sheets: ' . $e->getMessage());

            return redirect()->route('records.index')->with('error', $e->getMessage());
        }
    }

    /**
     * @return RedirectResponse
     */
    public function clear(): RedirectResponse
    {
        Record::query()->truncate();
        return redirect()->route('records.index');
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $record = Record::query()->findOrFail($id);
        $record->status = $request->status;
        $record->save();

        app(GoogleSheetsService::class)->syncRecords();

        return redirect()->route('records.index')->with('success', 'Статус обновлён!');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $record = Record::query()->findOrFail($id);
        $record->delete();

        app(GoogleSheetsService::class)->syncRecords();

        return redirect()->route('records.index')->with('success', 'Запись удалена!');
    }
}
