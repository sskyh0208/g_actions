<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function testDatabase()
    {
        // ログ出力
        Log::debug('This is a debug message.');

        // 例えば、users テーブルから全データを取得
        $users = DB::table('users')->get();

        // データをJSONとして返す
        return response()->json($users);
    }
}
