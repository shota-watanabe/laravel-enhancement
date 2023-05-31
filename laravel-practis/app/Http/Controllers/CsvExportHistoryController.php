<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportHistoryController extends Controller
{
    public function store(Request $request)
    {
        $sessionUsers = session()->get('users');
        // $this->download($sessionUsers);
        $this->exportCsv($sessionUsers);
    }

    public function exportCsv($users)
    {
        // ①HTTPヘッダーの設定
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment;'
        ];

        $fileName = Carbon::now()->format('YmdHis').'_customerList.csv';

        $callback = function() use ($users)
        {
            // ②ストリームを作成してファイルに書き込めるようにする
            $stream = fopen('php://output', 'w');
            // ③CSVのヘッダ行の定義
            $head = [
                'ID',
                'NAME',
                'COMPANY',
                'SECTION',
            ];
            // ④UTF-8からSJISにフォーマットを変更してExcelの文字化け対策
            mb_convert_variables('SJIS', 'UTF-8', $head);
            fputcsv($stream, $head);
            // ⑤データを取得してCSVファイルのデータレコードに顧客情報を挿入

            foreach ($users as $user) {
                $data = [
                    $user->id,
                    $user->name,
                    $user->company->name,
                    $user->sections->implode(',')
                ];

                mb_convert_variables('SJIS', 'UTF-8', $data);
                fputcsv($stream, $data);
            }

            fclose($stream);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }
}
