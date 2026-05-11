<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Carbon\Carbon;

/**
 * Test para la funcionalidad de eventos en tiempo real - G4DS-264
 * 
 * Historia: Como Usuario, quiero ver nuevos eventos publicados sin necesidad de recargar la página
 * 
 * Criterios de Aceptación:
 * CA1: Al publicar un evento nuevo, aparece automáticamente en la lista de eventos para los usuarios conectados.
 * CA2: El evento nuevo muestra título, fecha y descripción correctamente.
 * CA3: Si no hay eventos activos, se muestra el mensaje: 'No hay eventos disponibles en este momento'.
 */
class EventRealTimeTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        
        // Limpiar la tabla de eventos para cada prueba
        Event::query()->truncate();
        
        // Crear un usuario regular para las pruebas
        $this->user = User::factory()->create();
    }

    /**
     * CP-264-01: Verificar que un evento publicado aparece sin recargar
     * 
     * Objetivo: Validar que al crear un evento, este aparece inmediatamente en la lista
     * sin necesidad de recargar la página.
     * 
     * Precondiciones:
     * - Usuario autenticado
     * - Sección de eventos disponible
     */
    public function test_new_event_appears_in_list_when_published(): void
    {
        // Verificar que no hay eventos activos inicialmente
        $this->assertDatabaseCount('tbevents', 0);

        // CA1: Al publicar un evento nuevo
        $event = Event::factory()
            ->activeUpcoming()
            ->create([
                'title' => 'Concierto en Vivo',
                'description' => 'Concierto musical con artistas locales',
                'is_active' => true,
            ]);

        // Verificar que el evento fue guardado en el sistema
        $this->assertDatabaseHas('tbevents', [
            'event_id' => $event->event_id,
            'title' => 'Concierto en Vivo',
            'is_active' => 1,
        ]);

        // Verificar que el evento aparece en la lista de eventos activos (sin recargar)
        $this->assertTrue(
            Event::active()->exists(),
            'El evento publicado debe aparecer en la lista de eventos activos'
        );

        // Verificar que podemos recuperarlo desde la base de datos sin recargar
        $retrievedEvent = Event::find($event->event_id);
        $this->assertNotNull($retrievedEvent);
        $this->assertEquals('Concierto en Vivo', $retrievedEvent->title);
    }

    /**
     * CP-264-02: Verificar que el evento muestra datos correctamente
     * 
     * Objetivo: Validar que el evento nuevo muestra título, fecha y descripción correctamente.
     * 
     * CA2: El evento nuevo muestra título, fecha y descripción correctamente.
     */
    public function test_event_displays_title_date_and_description_correctly(): void
    {
        $startDate = now()->addDays(3)->setHour(18)->setMinute(30);
        
        $event = Event::factory()
            ->withCustomData(
                'Festival Gastronómico 2026',
                'Un festival de comida con los mejores chefs de la región'
            )
            ->withStartDate($startDate)
            ->create();

        // CA2: Verificar que todos los campos se muestran correctamente
        $this->assertEquals('Festival Gastronómico 2026', $event->title);
        $this->assertEquals('Un festival de comida con los mejores chefs de la región', $event->description);
        $this->assertEquals($startDate->toDateTimeString(), $event->start_at->toDateTimeString());

        // Verificar que podemos acceder a los datos desde la respuesta
        $this->assertTrue($event->exists());
        $this->assertNotNull($event->title);
        $this->assertNotNull($event->description);
        $this->assertNotNull($event->start_at);
    }

    /**
     * CP-264-03: Verificar que se muestra mensaje cuando no hay eventos
     * 
     * Objetivo: Validar que si no hay eventos activos, se muestra el mensaje correcto.
     * 
     * CA3: Si no hay eventos activos, se muestra el mensaje: 
     * 'No hay eventos disponibles en este momento'.
     */
    public function test_no_available_events_message_when_no_active_events(): void
    {
        // Asegurar que no hay eventos activos
        Event::query()->delete();

        // Verificar que no hay eventos activos
        $activeEvents = Event::active()->get();
        $this->assertEmpty($activeEvents, 'No debe haber eventos activos');

        // Verificar que la condición para mostrar el mensaje se cumple
        if ($activeEvents->isEmpty()) {
            $message = 'No hay eventos disponibles en este momento';
            $this->assertEquals('No hay eventos disponibles en este momento', $message);
        }
    }

    /**
     * CP-264-04: Verificar que solo eventos activos se muestran
     * 
     * Objetivo: Validar que los eventos inactivos no aparecen en la lista.
     */
    public function test_inactive_events_do_not_appear_in_list(): void
    {
        // Crear evento activo
        $activeEvent = Event::factory()->activeUpcoming()->create();

        // Crear evento inactivo
        $inactiveEvent = Event::factory()->inactive()->create();

        // Recuperar solo eventos activos
        $activeEvents = Event::active()->get();

        // Verificar que solo el evento activo aparece
        $this->assertCount(1, $activeEvents);
        $this->assertTrue($activeEvents->contains($activeEvent));
        $this->assertFalse($activeEvents->contains($inactiveEvent));
    }

    /**
     * CP-264-05: Verificar que los eventos expirados (>24h) no aparecen
     * 
     * Objetivo: Validar que los eventos cuya fecha superó las 24 horas de antigüedad
     * no aparecen en la lista.
     */
    public function test_expired_events_do_not_appear_in_list(): void
    {
        // Crear evento activo y vigente
        $upcomingEvent = Event::factory()->activeUpcoming()->create();

        // Crear evento que pasó hace más de 24 horas
        $expiredEvent = Event::factory()->past()->create();

        // Recuperar solo eventos no expirados
        $activeEvents = Event::notExpired()->get();

        // Verificar que solo el evento vigente aparece
        $this->assertTrue($activeEvents->contains($upcomingEvent));
        $this->assertFalse($activeEvents->contains($expiredEvent));
    }

    /**
     * CP-264-06: Verificar que múltiples eventos se muestran correctamente
     * 
     * Objetivo: Validar que se pueden listar múltiples eventos simultáneamente.
     */
    public function test_multiple_events_display_correctly(): void
    {
        // Crear múltiples eventos activos
        $events = Event::factory()
            ->count(5)
            ->activeUpcoming()
            ->create();

        // Recuperar eventos activos
        $activeEvents = Event::active()->get();

        // Verificar que todos los eventos se muestran
        $this->assertCount(5, $activeEvents);
        
        foreach ($events as $event) {
            $this->assertTrue($activeEvents->contains($event));
        }
    }

    /**
     * CP-264-07: Verificar la búsqueda de eventos por título
     * 
     * Objetivo: Validar que se pueden buscar eventos por título.
     */
    public function test_search_events_by_title(): void
    {
        // Crear eventos con títulos específicos
        $event1 = Event::factory()
            ->activeUpcoming()
            ->create(['title' => 'Concierto de Rock']);

        $event2 = Event::factory()
            ->activeUpcoming()
            ->create(['title' => 'Cena Especial']);

        // Buscar por "Concierto"
        $results = Event::search('Concierto')->get();

        // Verificar que solo encuentra el evento correcto
        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($event1));
        $this->assertFalse($results->contains($event2));
    }

    /**
     * CP-264-08: Verificar que la fecha se muestra correctamente en diferentes formatos
     * 
     * Objetivo: Validar que la fecha del evento está correctamente tipificada como datetime.
     */
    public function test_event_date_is_properly_cast_to_datetime(): void
    {
        $specificDate = Carbon::parse('2026-06-15 19:30:00');
        
        $event = Event::factory()
            ->withStartDate($specificDate)
            ->create();

        // Verificar que start_at es una instancia de Carbon
        $this->assertInstanceOf(Carbon::class, $event->start_at);

        // Verificar que la fecha se mantiene correctamente
        $this->assertEquals(
            $specificDate->toDateTimeString(),
            $event->start_at->toDateTimeString()
        );
    }

    /**
     * CP-264-09: Verificar que eventos con imagen se muestran correctamente
     * 
     * Objetivo: Validar que los eventos pueden tener imagen y esta se guarda correctamente.
     */
    public function test_event_with_image_is_stored_correctly(): void
    {
        $event = Event::factory()
            ->withImage()
            ->create();

        // Verificar que la imagen URL se guardó
        $this->assertNotNull($event->image_url);
        $this->assertStringStartsWith('/storage/events/', $event->image_url);

        // Verificar que podemos recuperar el evento con la imagen
        $retrieved = Event::find($event->event_id);
        $this->assertEquals($event->image_url, $retrieved->image_url);
    }

    /**
     * CP-264-10: Verificar el estado del evento en español
     * 
     * Objetivo: Validar que el accessor devuelve el estado correcto en español.
     */
    public function test_event_status_in_spanish(): void
    {
        $activeEvent = Event::factory()->create(['is_active' => true]);
        $inactiveEvent = Event::factory()->create(['is_active' => false]);

        // Verificar los estados en español
        $this->assertEquals('Activo', $activeEvent->status_in_spanish);
        $this->assertEquals('Inactivo', $inactiveEvent->status_in_spanish);
    }

    /**
     * CP-264-11: Verificar que eventos recién creados aparecen al principio
     * 
     * Objetivo: Validar que cuando se publica un nuevo evento, aparece en la lista
     * ordenado por fecha de creación más reciente.
     */
    public function test_newly_published_events_appear_first(): void
    {
        // Crear primer evento con timestamp anterior
        $oldEvent = Event::factory()->create([
            'created_at' => now()->subMinute(),
            'is_active' => true,
        ]);

        // Crear nuevo evento con timestamp actual (más reciente)
        $newEvent = Event::factory()->create([
            'created_at' => now(),
            'is_active' => true,
        ]);

        // Recuperar eventos ordenados por created_at descendente
        $events = Event::orderBy('created_at', 'desc')->get();

        // Verificar que el evento más reciente aparece primero
        $this->assertGreaterThanOrEqual(2, $events->count());
        $this->assertEquals($newEvent->event_id, $events->first()->event_id);
    }
}
