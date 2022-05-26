<?php

namespace Valle\Interfaces;

interface MapperSearchInterface 
{
    public function __construct(array $data, int $limit, int $offset, int $total);
    public function total(): int;
    public function limit(): int;
    public function data(): array;
    public function offset(): int;
}