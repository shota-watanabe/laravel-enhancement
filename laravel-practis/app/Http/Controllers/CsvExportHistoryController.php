<?php

namespace App\Http\Controllers;

use App\Models\CsvExportHistory;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportHistoryController extends Controller
{
    public function index(): View
    {
        $csv_export_histories = CsvExportHistory::with('download_user')->orderBy('created_at', 'desc')->paginate();
        return view('csv_export_histories.index', compact('csv_export_histories'));
    }

    public function store(Request $request): StreamedResponse
    {
        $searchType = $request->search_type;
        $searchKeyword = $request->search_keyword;
        $user = New User();
        $users = $user->keywordSearch($searchType, $searchKeyword);

        $file_name = sprintf('users-%s.csv', now()->format('YmdHis'));
        $stream = $this->createCsv($users);
        Storage::put($file_name, $stream);
        Auth::user()->csv_export_histories()->create([
            'file_name' => $file_name,
        ]);
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
