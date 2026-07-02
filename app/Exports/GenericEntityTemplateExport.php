<?php
declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GenericEntityTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private readonly array $headers,
        private readonly string $title,
    ) {}

    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function title(): string
    {
        return $this->title;
    }
}
