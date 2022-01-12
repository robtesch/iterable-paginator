<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Robtesch\IterablePaginator\Paginator;

class PaginatesArrayTest extends TestCase
{
    public function testPaginates100Items(): void
    {
        $array = range(1, 100);

        $pagination = (new Paginator($array, 15))->paginate();

        $this->assertCount(15, $pagination);
    }

    public function testPaginates100ItemsPage2(): void
    {
        $array = range(1, 100);

        $pagination = (new Paginator($array,15))->paginate(2);

        $this->assertCount(15, $pagination);

        for ($i = 0; $i < 15; $i++) {
            $this->assertEquals($i + 16, $pagination[$i]);
        }
    }

    public function testReturnsSaneResultsForNegativePageNumber(): void
    {
        $array = range(1, 100);

        $pagination = (new Paginator($array,  15))->paginate(-1);

        $this->assertCount(15, $pagination);
    }

    public function testReturnsSaneResultsForMassivePageNumber(): void
    {
        $array = range(1, 100);

        $pagination = (new Paginator($array,  15))->paginate(100);

        $this->assertGreaterThan(0, $pagination);
    }

    public function testReturnsCorrectArrayRepresentation(): void
    {
        $all = ['chimp', 'turtle', 'chicken', 'pig', 'dog', 'frog', 'ostrich', 'cow', 'elephant', 'giraffe'];

        $pagination = (new Paginator($all, 3, 2));

        $expected = [
            "current_page" => 2,
            "data"         => ['pig', 'dog', 'frog'],
            "from"         => 4,
            "to"           => 6,
            "last_page"    => 4,
            "per_page"     => 3,
            "total"        => 10,
        ];

        $this->assertEquals($expected, $pagination->toArray());
    }

    public function testWorksWhenPerPageIsMoreThanItems(): void
    {
        $array = range(1, 100);

        $pagination = (new Paginator($array, 200, 1));

        $this->assertCount(100, $pagination->paginate());
    }
}
