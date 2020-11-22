<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CsvService;
use App\Http\Requests\UploadFileRequest as Request;

class CsvController extends Controller
{
    /**
     * @var CsvService $csvService
     */
    private CsvService $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getStatistics(Request $request) : array
    {
        return $this->csvService->getStatistics($request);
    }
}
