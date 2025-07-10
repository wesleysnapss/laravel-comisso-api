<?php 

namespace App\Repositories\Sale;

use Illuminate\Support\Facades\Storage;

class SaleRepository
{
    private string $file = 'sales.json';

    public function exists(string $id): bool
    {
        $sales = $this->getData();

        foreach ($sales as $sale) {
            if ($sale['id'] === $id) return true;
        }
        
        return false;
    }

    private function getData()
    {   
        $file =  Storage::exists($this->file);

        if(!$file) return [];  

        $data = Storage::get($this->file);

        return json_decode($data, true);
    }

    private function saveData(array $data)
    {
        $save = Storage::put($this->file, json_encode($data, JSON_PRETTY_PRINT));

        if(!$save)  return false;

        return true;

    }

    public function all()
    {
        return $this->getData();
    }

    public function create(array $sale)
    {
        $sales = $this->getData();
        $sales[] = $sale;
        $this->saveData($sales);
        return $sale;
    }

    public function find(string $id)
    {
        $data = $this->all();
        
        foreach ($data as $sale) {
            if ($sale['id'] === $id) {
                return $sale;
            }
        }

        return false;
    }

    public function update(string $id, array $payload)
    {
        $data = $this->all();

        foreach ($data as $index => $item) {
            if ($item['id'] === $id) {
                $data[$index] = $payload;
                $this->saveData($data);
                return $payload;
            }
        }

        return false;
    }

    public function delete(string $id)
    {
        $sales = $this->getData();

        $filtered = array_filter($sales, fn ($s) => $s['id'] !== $id);

        if(count($sales) === count($filtered)) return false;

        $this->saveData(array_values($filtered));

        return true;
    }
}