<?php

namespace Tests\Unit;

use App\Data\ReportData;
use App\Models\Order;
use App\Models\Local;
use Carbon\Carbon;
use Tests\TestCase;

class ReportDataTest extends TestCase
{
    protected $reportData;
    protected $local;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportData = new ReportData();
        $this->local = Local::factory()->create();
    }

    /**
     * Test CA1: Cantidad y porcentaje de pedidos
     */
    public function test_ca1_order_count_and_percentage()
    {
        // Crear datos de prueba
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // 70 pedidos en línea
        Order::factory()->count(70)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_WEB,
            'date' => $startDate,
        ]);

        // 30 pedidos presenciales
        Order::factory()->count(30)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_PRESENCIAL,
            'date' => $startDate,
        ]);

        $stats = $this->reportData->getOrdersByOrigin($this->local->local_id, $startDate, $endDate);

        $this->assertEquals(70, $stats['web']['count']);
        $this->assertEquals(30, $stats['presential']['count']);
        $this->assertEquals(100, $stats['total']);
        $this->assertAlmostEquals(70.0, $stats['web']['percentage'], 1);
        $this->assertAlmostEquals(30.0, $stats['presential']['percentage'], 1);
    }

    /**
     * Test CA3: Suma de porcentajes = 100%
     */
    public function test_ca3_percentages_sum_to_100()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        Order::factory()->count(45)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_WEB,
            'date' => $startDate,
        ]);

        Order::factory()->count(55)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_PRESENCIAL,
            'date' => $startDate,
        ]);

        $stats = $this->reportData->getOrdersByOrigin($this->local->local_id, $startDate, $endDate);
        $sum = $stats['web']['percentage'] + $stats['presential']['percentage'];

        $this->assertEquals(100.0, $sum);
    }

    /**
     * Test CA6: Período con solo un tipo de pedido
     */
    public function test_ca6_single_order_type_period()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Solo pedidos presenciales
        Order::factory()->count(50)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_PRESENCIAL,
            'date' => $startDate,
        ]);

        $stats = $this->reportData->getOrdersByOrigin($this->local->local_id, $startDate, $endDate);

        $this->assertEquals(0, $stats['web']['count']);
        $this->assertEquals(50, $stats['presential']['count']);
        $this->assertEquals(0.0, $stats['web']['percentage']);
        $this->assertEquals(100.0, $stats['presential']['percentage']);
    }

    /**
     * Test CA4: Validación de rango de fechas personalizado
     */
    public function test_ca4_custom_date_range_validation()
    {
        // Fechas válidas
        $validation = $this->reportData->validateDateRange('2024-04-01', '2024-04-30');
        $this->assertTrue($validation['valid']);

        // Start > End (inválido)
        $validation = $this->reportData->validateDateRange('2024-04-30', '2024-04-01');
        $this->assertFalse($validation['valid']);

        // Formato inválido
        $validation = $this->reportData->validateDateRange('31-04-2024', '30-04-2024');
        $this->assertFalse($validation['valid']);
    }

    /**
     * Test: Ingresos por origen
     */
    public function test_revenue_by_origin()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // 10 pedidos web de $100 cada uno = $1000
        Order::factory()->count(10)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_WEB,
            'date' => $startDate,
            'total_amount' => 100.00,
        ]);

        // 5 pedidos presenciales de $200 cada uno = $1000
        Order::factory()->count(5)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_PRESENCIAL,
            'date' => $startDate,
            'total_amount' => 200.00,
        ]);

        $revenue = $this->reportData->getRevenueByOrigin($this->local->local_id, $startDate, $endDate);

        $this->assertEquals(1000.00, $revenue['web']['revenue']);
        $this->assertEquals(1000.00, $revenue['presential']['revenue']);
        $this->assertEquals(2000.00, $revenue['total']);
        $this->assertEquals(50.0, $revenue['web']['percentage']);
        $this->assertEquals(50.0, $revenue['presential']['percentage']);
    }

    /**
     * Test: Tendencia diaria
     */
    public function test_daily_trend()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = $startDate->clone()->addDays(2);

        // Día 1: 5 web + 3 presencial
        Order::factory()->count(5)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_WEB,
            'date' => $startDate,
        ]);

        Order::factory()->count(3)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_PRESENCIAL,
            'date' => $startDate,
        ]);

        // Día 2: 2 web + 4 presencial
        $day2 = $startDate->clone()->addDay();
        Order::factory()->count(2)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_WEB,
            'date' => $day2,
        ]);

        Order::factory()->count(4)->create([
            'local_id' => $this->local->local_id,
            'origin' => Order::ORIGIN_PRESENCIAL,
            'date' => $day2,
        ]);

        $trend = $this->reportData->getDailyTrend($this->local->local_id, $startDate, $endDate);

        $this->assertCount(3, $trend);
        $this->assertEquals(5, $trend[0]['web']);
        $this->assertEquals(3, $trend[0]['presential']);
        $this->assertEquals(2, $trend[1]['web']);
        $this->assertEquals(4, $trend[1]['presential']);
    }

    /**
     * Test: Período predefinido - Este Mes
     */
    public function test_predefined_period_this_month()
    {
        $dates = $this->reportData->getPeriodDates('month');

        $this->assertTrue($dates['start']->isStartOfMonth());
        $this->assertTrue($dates['end']->isEndOfMonth());
        $this->assertEquals('Este Mes', $dates['label']);
    }

    /**
     * Test: Período predefinido - Esta Semana
     */
    public function test_predefined_period_this_week()
    {
        $dates = $this->reportData->getPeriodDates('week');

        $this->assertTrue($dates['start']->isStartOfWeek());
        $this->assertTrue($dates['end']->isEndOfWeek());
        $this->assertEquals('Esta Semana', $dates['label']);
    }
}
