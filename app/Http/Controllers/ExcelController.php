<?php

namespace App\Http\Controllers;

use App\Entities\BookCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Excel;

class ExcelController extends Controller
{
    public function bookCodeExport() {
        $cellData = BookCode::query()->whereBetween('created_at', ['2018-05-08 00:00:00', '2018-05-08 23:59:59'])->get();

        $data = [];
        array_push($data, ['二维码内容']);
        foreach ($cellData as $datum) {
            $da = [];
            $da[] = $datum->code;
            array_push($data, $da);
        }

        $date = date('Y-m-d', time());
        Excel::create('图书二维码链接'. $date,function($excel) use ($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->store('xlsx');
    }
}
