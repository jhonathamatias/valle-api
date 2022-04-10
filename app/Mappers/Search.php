<?php

namespace Valle\Mappers;

use Valle\Interfaces\MapperSearchInterface;

class Search implements MapperSearchInterface
{
    public function __construct(protected array $data, protected int $limit, protected int $offset, protected int $total)
    {
    }

    public function total(): int 
    {
        return $this->total;
    }

    public function limit(): int 
    {
        return $this->limit;

    }

    public function data(): array 
    {
        return $this->data;

    }

    public function offset(): int 
    {
        return $this->offset;

    }
}