<?php declare(strict_types=1);

namespace App\Dto;

use Webmozart\Assert\Assert;

class Person
{
    /**
     * @var string $name
     */
    private string $name;

    /**
     * @var int $age
     */
    private int $age;

    /**
     * @param array $personData
     */
    public function __construct(array $personData)
    {
        [$this->name, $this->age] = $personData;
        Assert::stringNotEmpty($this->name);
        Assert::integer($this->age);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getAge() : int
    {
        return $this->age;
    }
}
