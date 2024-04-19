<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ParsingService;

class ParsingController extends Controller
{
    public function __construct(private ParsingService $parsingService)
    {
    }

    public function parse(FormRequest $request)
    {
        $file = $request->file('csv_file');

        if (!$file) {
            return redirect('/');
        }

        $fileContents = file($file->getPathname());

        $parsedData = $this->parsingService->parseNames($fileContents);

        return view('index', ['parsedData' => $parsedData ?: null]);
    }
}
