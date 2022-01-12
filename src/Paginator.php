<?php

namespace Robtesch\IterablePaginator;

class Paginator
{
    private array $items;
    private int $total;
    private int $perPage;
    private int $currentPage;

    public function __construct(iterable $items, int $perPage, int $currentPage = 1)
    {
        if (is_array($items)) {
            $this->items = $items;
        } elseif ($items instanceof \Traversable) {
            $this->items = iterator_to_array($items);
        } else {
            throw new \InvalidArgumentException('expected array or Traversable for items.');
        }
        $this->total = count($this->items);
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    public function items(): iterable
    {
        return $this->items;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function pages(): int
    {
        return (int)ceil($this->total / $this->perPage);
    }

    public function total(): int
    {
        return $this->total;
    }

    public function totalElementsOnCurrentPage(): int
    {
        return count($this->paginate());
    }

    private function getOffset(): int
    {
        return max(0, ($this->currentPage - 1) * $this->perPage);
    }

    public function paginate(?int $page = null)
    {
        if (is_int($page)) {
            $this->currentPage = $page;
        }

        if($this->currentPage > $this->pages()) {
            $this->currentPage = $this->pages();
        }

        return array_slice($this->items, $this->getOffset(), $this->perPage, false);
    }

    public function toArray(): array
    {
        return [
            "current_page" => $this->currentPage,
            "data"         => $this->paginate(),
            "from"         => $this->first(),
            "to"           => $this->last(),
            "last_page"    => $this->pages(),
            "per_page"     => $this->perPage,
            "total"        => $this->total,
        ];
    }

    private function first(): ?int
    {
        return count($this->items) > 0 ? ($this->currentPage - 1) * $this->perPage + 1 : null;
    }

    public function last(): ?int
    {
        return count($this->items) > 0 ? $this->first() + count($this->paginate()) - 1 : null;
    }
}
