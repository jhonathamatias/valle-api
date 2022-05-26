<?php namespace Valle\Services;

class Mapper
{
    public static function collectionMap(string $collectionName, array $collection, int $total, int $offset = 0, int $limit = 100): array
    {
        return [
            $collectionName => $collection,  
            "offset" => $offset, 
            "limit" => $limit, 
            "total" => $total
        ];
    }
}