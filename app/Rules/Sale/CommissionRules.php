<?php 

namespace App\Rules\Sale;

use InvalidArgumentException;

class CommissionRules
{
    public static function calculate(string $tipoVenda, float $valorTotal): array
    {
        if (!in_array($tipoVenda, ['direta', 'afiliada'])) {
            throw new \InvalidArgumentException("Tipo de venda inválido: {$tipoVenda}");
        }

        $plataforma = round($valorTotal * 0.10, 2);

        return match ($tipoVenda) {
            'direta' => [
                'plataforma' => $plataforma,
                'produtor' => round($valorTotal * 0.90, 2),
                'afiliado' => 0.00,
            ],
            'afiliada' => [
                'plataforma' => $plataforma,
                'produtor' => round($valorTotal * 0.60, 2),
                'afiliado' => round($valorTotal * 0.30, 2),
            ],
        };
    }
}