<?php

namespace Atlantis;

class Pagination
{
    public int $total = 0;
    public int $page = 0;
    public int $limit = 0;
    public int $first = 0;
    public int $prev = 0;
    public int $next = 0;
    public int $last = 0;
    public int $offset = 0;

    function __construct(int $total = 0, int $page = 0, int $limit = 0)
    {
        $this->total = abs($total);
        $this->page = abs($page) ?: 1;
        $this->limit = abs($limit) ?: $this->total;
        return $this->calculate();
    }

    public function isset(): bool
    {
        return (bool) $this->page;
    }

    function total(int $total)
    {
        $this->total = abs($total);
        return $this->calculate();
    }

    function page(int $page)
    {
        $this->page = abs($page) ?: 1;
        return $this->calculate();
    }

    function limit(int $limit)
    {
        $this->limit = abs($limit) ?: $this->total;
        return $this->calculate();
    }

    protected function calculate()
    {
        $this->last = $this->limit ? ceil($this->total / $this->limit) : 1;
        $this->page = $this->page > $this->last ? $this->last : $this->page;
        $this->first = $this->page ? 1 : 0;
        $this->prev = $this->page - 1 < 1 ? 1 : $this->page - 1;
        $this->next = $this->page + 1 > $this->last ? $this->last : $this->page + 1;
        $offset = ($this->page - 1) * $this->limit;
        $this->offset = $offset > 0 ? $offset : 0;
        return $this;
    }
}
