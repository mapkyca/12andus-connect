<?php

namespace TwelveAndUs\API\Connect\Data;

class Birthdata extends Data {

    public function __construct(
        string $name,
        string $location,
        int $year,
        int $month,
        int $day,
        int $hour,
        int $minute
    )
    {
        $this->setParams([
            'name' => $name,
            'location' => $location,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'hour' => $hour,
            'minute' => $minute
        ]);
    }
}
