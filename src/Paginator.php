<?php

namespace Robtesch\IterablePaginator;

class Paginator
{
    /**
     * @var array|callable
     */
    private $items;
    private int $total;
    private int $perPage;
    private int $currentPage;

    /**
     * @param  iterable|callable  $items
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  int|null  $total
     */
    public function __construct($items, int $perPage, int $currentPage = 1, ?int $total = null)
    {
        if (is_array($items)) {
            $this->items = $items;
            $this->total = count($this->items);
            if ($total !== null) {
                throw new \InvalidArgumentException('you must not pass a total unless passing a callable for items');
            }
        } elseif ($items instanceof \Traversable) {
            $this->items = iterator_to_array($items);
            $this->total = count($this->items);
            if ($total !== null) {
                throw new \InvalidArgumentException('you must not pass a total unless passing a callable for items');
            }
        } elseif (is_callable($items, true)) {
            $this->items = $items;
            if ($total === null) {
                throw new \InvalidArgumentException('you must pass a total when passing a callable for items');
            }
            $this->total = $total;
        } else {
            throw new \InvalidArgumentException('expected array or Traversable for items.');
        }
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    private function resolveItems()
    {
        if (is_callable($this->items, true)) {
            return call_user_func($this->items, $this->getOffset(), min($this->perPage, $this->total));
        }
        return $this->items;
    }

    public function getItems(): iterable
    {
        return $this->resolveItems();
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

        if ($this->currentPage > $this->pages()) {
            $this->currentPage = $this->pages();
        }

        if (is_callable($this->items, true)) {
            return $this->resolveItems();
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
        return count($this->resolveItems()) > 0 ? ($this->currentPage - 1) * $this->perPage + 1 : null;
    }

    public function last(): ?int
    {
        return count($this->resolveItems()) > 0 ? $this->first() + count($this->paginate()) - 1 : null;
    }
}
