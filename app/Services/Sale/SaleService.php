<?php

namespace App\Services\Sale;

use App\DTOs\SaleDTO;
use App\Repositories\Sale\SaleRepository;
use App\Rules\Sale\CommissionRules;
use Illuminate\Support\Str;

class SaleService
{
    protected $commissionRules;
    protected $repository;

    public function __construct(CommissionRules $commissionRules, SaleRepository $repository)
    {
        $this->commissionRules = $commissionRules;
        $this->repository = $repository;
    }

    public function simulateAndSave(array $data)
    {
        $comissoes = $this->commissionRules->calculate(
            $data['tipo_venda'],
            $data['valor_total']
        );

        $payload = [
            'id' => Str::uuid(),
            'valor_total' => $data['valor_total'],
            'tipo_venda' => $data['tipo_venda'],
            'comissoes' => $comissoes,
        ];

        return $this->repository->create($payload);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function findById(string $id)
    {
        return $this->repository->find($id);
    }

    public function update(string $id, array $data)
    {
        if (!$this->repository->exists($id)) {
            return false;
        }

        $commissions = $this->commissionRules->calculate(
            $data['tipo_venda'],
            $data['valor_total']
        );

        $payload = [
            'id' => $id,
            'valor_total' => $data['valor_total'],
            'tipo_venda' => $data['tipo_venda'],
            'comissoes' => $commissions,
        ];

        return $this->repository->update($id, $payload);
    }

    public function delete(string $id)
    {
        if (!$this->repository->exists($id)) return false;

        return $this->repository->delete($id);
    }
}
