<?php
declare(strict_types=1);

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GenericEntityImport implements ToCollection, WithHeadingRow
{
    private int $inserted = 0;

    /**
     * @param  class-string  $modelClass
     * @param  array<string, string>  $fieldMap  ['Header Label' => 'column_name']
     * @param  array<string, mixed>  $defaults  Default values (e.g. tenant_id)
     * @param  array<string, mixed>  $rules  Validation rules from entity config
     */
    public function __construct(
        private readonly string $modelClass,
        private readonly array $fieldMap,
        private readonly array $defaults = [],
        private readonly array $rules = [],
        private array $errors = [],
    ) {}

    /**
     * @return array<int, array{row: int, message: string}>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function inserted(): int
    {
        return $this->inserted;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $data = [];

            if (!empty($this->fieldMap)) {
                foreach ($this->fieldMap as $header => $column) {
                    $data[$column] = $row[$header] ?? null;
                }
            } else {
                $data = $row->toArray();
            }

            $data = array_merge($this->defaults, $data);

            // Validate against entity rules
            $validator = Validator::make($data, $this->rules);
            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'message' => implode('; ', $validator->errors()->all()),
                ];
                continue;
            }

            try {
                $this->modelClass::create($data);
                $this->inserted++;
            } catch (\Throwable $e) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'message' => $e->getMessage(),
                ];
            }
        }
    }

}
