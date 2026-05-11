<?php

namespace Tests\Unit;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Pruebas unitarias para el modelo Event
 * Complementan los tests de Feature con validaciones de métodos y scopos
 */
class EventModelTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        
        // Limpiar la tabla de eventos para cada prueba
        Event::query()->truncate();
    }

    /**
     * Verificar que el modelo Event puede ser instanciado correctamente
     */
    public function test_event_model_can_be_created(): void
    {
        $event = Event::factory()->create([
            'title' => 'Test Event',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Test Event', $event->title);
        $this->assertTrue($event->is_active);
    }

    /**
     * Verificar que el modelo usa correctamente la tabla tbevents
     */
    public function test_event_uses_correct_table_name(): void
    {
        $event = new Event();
        $this->assertEquals('tbevents', $event->getTable());
    }

    /**
     * Verificar que el modelo usa la clave primaria correcta
     */
    public function test_event_uses_correct_primary_key(): void
    {
        $event = new Event();
        $this->assertEquals('event_id', $event->getKeyName());
    }

    /**
     * Verificar que los atributos fillable están correctamente definidos
     */
    public function test_event_fillable_attributes(): void
    {
        $event = new Event();
        $fillable = $event->getFillable();

        $this->assertContains('title', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('start_at', $fillable);
        $this->assertContains('location', $fillable);
        $this->assertContains('is_active', $fillable);
        $this->assertContains('image_url', $fillable);
    }

    /**
     * Verificar que los casts se aplican correctamente
     */
    public function test_event_casts_applied_correctly(): void
    {
        $event = Event::factory()->create([
            'is_active' => 1,
        ]);

        // Verificar que is_active se castea a boolean
        $this->assertIsBool($event->is_active);
        $this->assertTrue($event->is_active);

        // Verificar que start_at se castea a datetime
        $this->assertInstanceOf(Carbon::class, $event->start_at);
    }

    /**
     * Verificar el scope 'active' filtra correctamente
     */
    public function test_active_scope_filters_correctly(): void
    {
        // Crear eventos de prueba
        Event::factory()->activeUpcoming()->create();
        Event::factory()->inactive()->create();
        Event::factory()->past()->create();

        // Aplicar scope active
        $activeEvents = Event::active()->get();

        // Verificar que solo hay un evento (el activo y vigente)
        $this->assertCount(1, $activeEvents);
        $this->assertTrue($activeEvents->first()->is_active);
    }

    /**
     * Verificar el scope 'notExpired' filtra correctamente
     */
    public function test_not_expired_scope_filters_correctly(): void
    {
        // Evento dentro de 24 horas
        $upcoming = Event::factory()->activeUpcoming()->create();

        // Evento hace más de 24 horas
        $expired = Event::factory()->past()->create();

        // Aplicar scope notExpired
        $notExpired = Event::notExpired()->get();

        // Verificar que filtra correctamente
        $this->assertTrue($notExpired->contains($upcoming));
        $this->assertFalse($notExpired->contains($expired));
    }

    /**
     * Verificar el scope 'search' busca por título
     */
    public function test_search_scope_finds_by_title(): void
    {
        $event1 = Event::factory()->create(['title' => 'Concierto de Jazz']);
        $event2 = Event::factory()->create(['title' => 'Festival Gastronómico']);
        $event3 = Event::factory()->create(['title' => 'Concierto de Salsa']);

        // Buscar "Concierto"
        $results = Event::search('Concierto')->get();

        // Verificar resultados
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($event1));
        $this->assertTrue($results->contains($event3));
        $this->assertFalse($results->contains($event2));
    }

    /**
     * Verificar que el accessor statusInSpanish funciona correctamente
     */
    public function test_status_in_spanish_accessor(): void
    {
        $activeEvent = Event::factory()->create(['is_active' => true]);
        $inactiveEvent = Event::factory()->create(['is_active' => false]);

        $this->assertEquals('Activo', $activeEvent->status_in_spanish);
        $this->assertEquals('Inactivo', $inactiveEvent->status_in_spanish);
    }

    /**
     * Verificar que se pueden crear eventos con todos los campos
     */
    public function test_create_event_with_all_fields(): void
    {
        $startDate = now()->addDays(5);
        
        $event = Event::create([
            'title' => 'Evento Completo',
            'description' => 'Descripción detallada del evento',
            'start_at' => $startDate,
            'location' => 'Calle Principal 123',
            'is_active' => true,
            'image_url' => '/storage/events/evento.jpg',
        ]);

        $this->assertDatabaseHas('tbevents', [
            'event_id' => $event->event_id,
            'title' => 'Evento Completo',
            'description' => 'Descripción detallada del evento',
            'location' => 'Calle Principal 123',
            'is_active' => 1,
            'image_url' => '/storage/events/evento.jpg',
        ]);
    }

    /**
     * Verificar que se pueden actualizar eventos
     */
    public function test_update_event(): void
    {
        $event = Event::factory()->create(['title' => 'Título Original']);

        $event->update(['title' => 'Título Actualizado']);

        $this->assertEquals('Título Actualizado', $event->fresh()->title);
        $this->assertDatabaseHas('tbevents', [
            'event_id' => $event->event_id,
            'title' => 'Título Actualizado',
        ]);
    }

    /**
     * Verificar que se pueden eliminar eventos
     */
    public function test_delete_event(): void
    {
        $event = Event::factory()->create();
        $eventId = $event->event_id;

        $event->delete();

        $this->assertDatabaseMissing('tbevents', [
            'event_id' => $eventId,
        ]);
    }

    /**
     * Verificar que timestamp se actualizan automáticamente
     */
    public function test_timestamps_are_automatically_updated(): void
    {
        $event = Event::factory()->create();
        $originalUpdatedAt = $event->updated_at;

        sleep(1);
        $event->update(['title' => 'Nuevo Título']);

        $this->assertNotEquals(
            $originalUpdatedAt->toDateTimeString(),
            $event->fresh()->updated_at->toDateTimeString()
        );
    }

    /**
     * Verificar que el factory crea eventos con datos válidos
     */
    public function test_factory_creates_valid_event(): void
    {
        $event = Event::factory()->create();

        $this->assertNotNull($event->title);
        $this->assertNotNull($event->description);
        $this->assertNotNull($event->start_at);
        $this->assertNotNull($event->location);
        $this->assertNotNull($event->is_active);
        $this->assertInstanceOf(Carbon::class, $event->created_at);
        $this->assertInstanceOf(Carbon::class, $event->updated_at);
    }

    /**
     * Verificar los diferentes estados del factory
     */
    public function test_factory_states(): void
    {
        // Estado activeUpcoming
        $upcoming = Event::factory()->activeUpcoming()->make();
        $this->assertTrue($upcoming->is_active);
        $this->assertTrue($upcoming->start_at->isFuture());

        // Estado inactive
        $inactive = Event::factory()->inactive()->make();
        $this->assertFalse($inactive->is_active);

        // Estado future
        $future = Event::factory()->future()->make();
        $this->assertTrue($future->is_active);
        $this->assertTrue($future->start_at->diffInDays(now()) > 7);

        // Estado past
        $past = Event::factory()->past()->make();
        $this->assertTrue($past->is_active);
        $this->assertTrue($past->start_at->isPast());
    }

    /**
     * Verificar que se pueden crear eventos con datos personalizados
     */
    public function test_factory_with_custom_data(): void
    {
        $event = Event::factory()
            ->withCustomData('Mi Evento', 'Descripción personalizada')
            ->create();

        $this->assertEquals('Mi Evento', $event->title);
        $this->assertEquals('Descripción personalizada', $event->description);
    }

    /**
     * Verificar que se pueden crear eventos con ubicación personalizada
     */
    public function test_factory_with_custom_location(): void
    {
        $event = Event::factory()
            ->withLocation('Plaza Mayor')
            ->create();

        $this->assertEquals('Plaza Mayor', $event->location);
    }

    /**
     * Verificar que se pueden crear eventos con imagen
     */
    public function test_factory_with_image(): void
    {
        $event = Event::factory()->withImage()->create();

        $this->assertNotNull($event->image_url);
        $this->assertStringStartsWith('/storage/events/', $event->image_url);
    }
}
