<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Robtesch\IterablePaginator\Paginator;

class PaginatesCallableTest extends TestCase
{
    public function testPaginates100Items(): void
    {
        $callable = function($offset, $perPage) {
          return range($offset + 1, $offset + $perPage);
        };

        $pagination = (new Paginator($callable,  15, 1, 100))->paginate();

        $this->assertCount(15, $pagination);
    }

    public function testThrowsErrorWhenNotPassingTotal(): void
    {
        $callable = function($offset, $perPage) {
            return range($offset + 1, $offset + $perPage);
        };

        try {
            (new Paginator($callable,  15, 1))->paginate();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertEquals('you must pass a total when passing a callable for items', $e->getMessage());
        }
    }

    public function testPaginates100ItemsPage2(): void
    {
        $callable = function($offset, $perPage) {
            return range($offset + 1, $offset + $perPage);
        };

        $pagination = (new Paginator($callable,  15, 2, 100))->paginate(2);

        $this->assertCount(15, $pagination);

        for ($i = 0; $i < 15; $i++) {
            $this->assertEquals($i + 16, $pagination[$i]);
        }
    }

    public function testReturnsSaneResultsForNegativePageNumber(): void
    {
        $callable = function($offset, $perPage) {
            return range($offset + 1, $offset + $perPage);
        };

        $pagination = (new Paginator($callable,  15, -1, 100))->paginate(-1);

        $this->assertCount(15, $pagination);
    }

    public function testReturnsSaneResultsForMassivePageNumber(): void
    {
        $callable = function($offset, $perPage) {
            return range($offset + 1, $offset + $perPage);
        };

        $pagination = (new Paginator($callable,  15, 100, 100))->paginate(100);

        $this->assertGreaterThan(0, $pagination);
    }

    public function testReturnsCorrectArrayRepresentation(): void
    {
        $callable = function($offset, $perPage) {
            $all = ['chimp', 'turtle', 'chicken', 'pig', 'dog', 'frog', 'ostrich', 'cow', 'elephant', 'giraffe'];
            return array_slice($all, $offset, $perPage);
        };

        $pagination = (new Paginator($callable, 3, 2, 10));

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
        $callable = function($offset, $perPage) {
            return range($offset + 1, $offset + $perPage);
        };

        $pagination = (new Paginator($callable, 200, 1, 100))->paginate();

        $this->assertCount(100, $pagination);
    }
}
