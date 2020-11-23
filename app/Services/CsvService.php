<?php declare(strict_types=1);

namespace App\Services;

use App\Dto\Person;
use App\Http\Requests\UploadFileRequest as Request;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CsvService
{
    /**
     * Set limit of csv rows
     */
    private const LIMIT_ROWS = 10;

    /**
     * @param Request $request
     *
     * @return array
     */
    public function generateStatistics(Request $request) : array
    {
        /** @var Csv $csv */
        $csv = resolve(Csv::class);
        $csvData = $csv->load($request->file('file')->getRealPath())->getActiveSheet()->toArray();
        array_shift($csvData);
        $this->validateNumberOfRows($csvData);
        $ages = array_map(fn(array $person) => (new Person($person))->getAge(), $csvData);
        $duplicateAges = array_filter(array_count_values($ages), fn(int $frequency) => $frequency > 1);
        $statistics = [];
        $nrAges = count($ages);

        foreach ($duplicateAges as $age => $frequency) {
            $statistics['ages'][$age] = [
                'percentage' => (number_format($frequency / $nrAges, 4) * 100) . '%',
            ];
        }

        return $statistics;
    }

    /**
     * @param array $csvData
     *
     * @return void
     */
    private function validateNumberOfRows(array $csvData) : void
    {
        if (count($csvData) > self::LIMIT_ROWS) {
            throw new BadRequestHttpException(sprintf('Csv file should have maximum %s records', self::LIMIT_ROWS));
        }
    }
}
