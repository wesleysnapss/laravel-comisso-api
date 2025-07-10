<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SaleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::put('sales.json', json_encode([]));
    }

    public function test_can_create_sale()
    {
        $response = $this->postJson('/api/sales', [
            'valor_total' => 1000,
            'tipo_venda' => 'afiliada'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'valor_total',
                'tipo_venda',
                'comissoes' => ['plataforma', 'produtor', 'afiliado']
            ]);
    }

    public function test_validation_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/sales', [
            'valor_total' => -10,
            'tipo_venda' => 'invalido'
        ]);

        $response->assertStatus(422);
    }

    public function test_can_list_sales()
    {
        $this->postJson('/api/sales', ['valor_total' => 500, 'tipo_venda' => 'direta']);
        $response = $this->getJson('/api/sales');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'count',
                'data' => [
                    ['id', 'valor_total', 'tipo_venda', 'comissoes']
                ]
            ]);
    }

    public function test_can_update_sale_and_recalculate_commission()
    {
        $create = $this->postJson('/api/sales', [
            'valor_total' => 100,
            'tipo_venda' => 'direta'
        ]);

        $id = $create->json('id');

        $response = $this->putJson("/api/sales/{$id}", [
            'valor_total' => 200,
            'tipo_venda' => 'afiliada'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $id,
                'valor_total' => 200,
                'tipo_venda' => 'afiliada',
                'comissoes' => [
                    'plataforma' => 20.0,
                    'produtor' => 120.0,
                    'afiliado' => 60.0
                ]
            ]);
    }

    public function test_can_delete_sale()
    {
        $create = $this->postJson('/api/sales', ['valor_total' => 300, 'tipo_venda' => 'afiliada']);
        $id = $create->json('id');

        $response = $this->deleteJson("/api/sales/{$id}");
        $response->assertStatus(200);
    }
}
