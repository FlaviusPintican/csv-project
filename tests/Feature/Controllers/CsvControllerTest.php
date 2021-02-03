<?php declare(strict_types=1);

namespace Tests\Feature\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Mockery;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CsvControllerTest extends TestCase
{
    /**
     * @var string
     */
    private const ROUTE = '/csv/statistics';

    /**
     * @var string[]
     */
    private const HEADERS = [
        'Accept' => 'application/json; charset=utf-8',
    ];

    /**
     * @test
     *
     * @param array       $expectedData
     * @param string|File $file
     *
     * @dataProvider getInvalidResponse
     * @return void
     */
    public function itReturnsInvalidResponse(array $expectedData, $file) : void
    {
        $response = $this->getResponse($file);
        $this->assertEquals($expectedData, $response->json());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     *
     * @param File  $file
     * @param array $expectedData
     * @param int   $nrRecords
     * @param int   $age
     *
     * @dataProvider getStatistics
     *
     * @return void
     */
    public function itReturnsStatistics(File $file, array $expectedData, int $nrRecords, int $age) : void
    {
        $this->mockCsv($nrRecords, $age);
        $response = $this->getResponse($file);
        $this->assertEquals($expectedData, $response->json());
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itReturnsInvalidNumberofRows() : void
    {
        $this->mockCsv();
        $response = $this->getResponse(UploadedFile::fake()->createWithContent('test.csv', ''));
        $this->assertEquals('Csv file should have maximum 10 records', $response->json('message'));
        $this->assertEquals(BadRequestHttpException::class, $response->json('exception'));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @return array
     */
    public function getInvalidResponse() : array
    {
        return [
            'missing file' => [
                [
                    'message' => 'The given data was invalid.',
                    'errors' => ['file' => ['The file field is required.']],
                ],
                '',
            ],
            'invalid file' => [
                [
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'file' => [
                            'The file must be a file.',
                            'The file must be a file of type: csv, txt.',
                        ]
                    ],
                ],
                'test',
            ],
            'invalid file extension' => [
                [
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'file' => [
                            'The file must be a file of type: csv, txt.',
                        ]
                    ],
                ],
                UploadedFile::fake()->create('test.json')
            ],
        ];
    }

    /**
     * @return array
     */
    public function getStatistics() : array
    {
        return [
            'empty csv statistics' => [
                UploadedFile::fake()->createWithContent('test.csv', ''),
                [],
                0,
                0,
            ],
            'csv statistics' => [
                UploadedFile::fake()->createWithContent('test.csv', ''),
                [
                    'ages' => [
                        25 => [
                            'percentage' => '100%'
                        ]
                    ]
                ],
                5,
                25
            ],
        ];
    }

    /**
     * @param int $nrRecords
     * @param int $age
     *
     * @return Spreadsheet
     */
    private function buildSheet(int $nrRecords, int $age) : Spreadsheet
    {
        $spreadSheet = new Spreadsheet();
        $spreadSheet->getActiveSheet()->setCellValue('A1', 'Name');
        $spreadSheet->getActiveSheet()->setCellValue('B1', 'Age');

        for ($i = 2; $i <= $nrRecords; ++$i) {
            $spreadSheet->getActiveSheet()->setCellValue("A$i", Str::random($i));
            $spreadSheet->getActiveSheet()->setCellValue("B$i", $age ?: $i);
        }

        return $spreadSheet;
    }

    /**
     * @param string|File $file
     *
     * @return TestResponse
     */
    private function getResponse($file) : TestResponse
    {
        return $this->post(self::ROUTE, ['file' => $file], self::HEADERS);
    }

    /**
     * @param int $nrRecords
     * @param int $age
     *
     * @return void
     */
    private function mockCsv(int $nrRecords = 12, int $age = 0) : void
    {
        $csv = Mockery::mock(Csv::class);
        $csv->shouldReceive('load')
            ->andReturn($this->buildSheet($nrRecords, $age));
        $this->app->instance(Csv::class, $csv);
    }
}
