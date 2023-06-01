<?php

namespace App\Http\Controllers;

use App\Models\CsvExportHistory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportHistoryController extends Controller
{
    public function index(): View
    {
        $csv_export_histories = CsvExportHistory::orderBy('created_at', 'desc')->paginate();
        return view('csv_export_histories.index', compact('csv_export_histories'));
    }

    public function store(Request $request): StreamedResponse
    {
        $users = session()->get('users');
        $file_name = sprintf('users-%s.csv', now()->format('YmdHis'));
        $stream = $this->createCsv($users);
        Storage::put($file_name, $stream);
        CsvExportHistory::create([
            'download_user_id' => Auth::user()->id,
            'file_name' => $file_name,
        ]);
        redirect()->route('login');
        return Storage::download($file_name);
    }

    public function show(CsvExportHistory $csv_export_history): StreamedResponse
    {
        if(Storage::exists($csv_export_history->file_name)) {
            return Storage::download($csv_export_history->file_name);
        }
        throw new \Exception('ファイルが存在しません。');
    }

    private function createCsv($users)
    {
        $stream = fopen('php://temp', 'r+b');

        fputcsv($stream, ['id', 'name', 'company_name', 'section']);

        foreach ($users as $user) {
            fputcsv($stream, [
                $user->id,
                $user->name,
                $user->company->name,
                $user->sections->implode('name', ', '),
            ]);
        }

        rewind($stream);

        return $stream;
    }
}