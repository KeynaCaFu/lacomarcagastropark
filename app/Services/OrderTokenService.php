<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

/**
 * OrderTokenService
 * 
 * Servicio para generar y gestionar tokens únicos de verificación de órdenes.
 * Cumple con CA2: El QR contiene un token numérico único.
 * Cumple con CA5: El sistema registra la fecha y hora de generación del token.
 * 
 * Formato del token: LCGP-XXXX (ej: LCGP-4829)
 */
class OrderTokenService
{
    /**
     * Prefijo del token
     */
    const TOKEN_PREFIX = 'LCGP';

    /**
     * Generar un token único de verificación
     * 
     * CA2: Genera token en formato LCGP-XXXX con 4 dígitos aleatorios
     * Valida unicidad en la base de datos mediante do-while
     * 
     * @return string Token único generado (ej: LCGP-4829)
     */
    public function generateUniqueToken(): string
    {
        $token = null;
        $attempts = 0;
        $maxAttempts = 100; // Prevenir bucles infinitos

        // do-while para garantizar unicidad
        do {
            // Generar 4 dígitos aleatorios
            $randomDigits = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $token = self::TOKEN_PREFIX . '-' . $randomDigits;

            // Verificar que el token no exista en la BD
            $exists = Order::where('verification_token', $token)->exists();
            
            $attempts++;

            // Si no existe, salir del loop
            if (!$exists) {
                break;
            }

            // Si alcanzamos max intentos, lanzar excepción
            if ($attempts >= $maxAttempts) {
                throw new \Exception('No se pudo generar un token único después de ' . $maxAttempts . ' intentos');
            }

        } while (true); // Continuar hasta encontrar un token único

        return $token;
    }

    /**
     * Validar formato de token
     * 
     * @param string $token Token a validar
     * @return bool true si el formato es válido (LCGP-XXXX)
     */
    public function isValidTokenFormat(string $token): bool
    {
        return preg_match('/^' . self::TOKEN_PREFIX . '-\d{4}$/', $token) === 1;
    }

    /**
     * Buscar orden por token de verificación
     * 
     * @param string $token Token de verificación
     * @return Order|null Orden encontrada o null
     */
    public function findOrderByToken(string $token): ?Order
    {
        return Order::where('verification_token', $token)->first();
    }

    /**
     * Validar token y retornar orden
     * 
     * @param string $token Token de verificación
     * @return Order|null Orden si el token es válido y existe
     */
    public function validateToken(string $token): ?Order
    {
        // Validar formato
        if (!$this->isValidTokenFormat($token)) {
            return null;
        }

        // Buscar y retornar orden
        return $this->findOrderByToken($token);
    }

    /**
     * Preparación para validación futura con plaza_key
     * 
     * Esta función está preparada para recibir plaza_key en futuras versiones
     * NOTA: Implementar cuando se integre la validación de QR de plaza
     * 
     * @param string $token Token de verificación
     * @param string $plazaKey Clave de plaza (futura implementación)
     * @return Order|null Orden validada
     */
    public function validateTokenWithPlazaKey(string $token, string $plazaKey): ?Order
    {
        // TODO: Implementar lógica de validación de plaza_key
        // Por ahora, solo valida el token
        // 
        // Lógica futura:
        // 1. Verificar que plaza_key sea válida en tabla qr_settings
        // 2. Verificar que la orden pertenezca al local asociado con plaza_key
        // 3. Registrar intento de validación en qr_generation_logs
        
        $order = $this->validateToken($token);

        if ($order) {
            // TODO: Agregar lógica de validación con plaza_key aquí
            // Ejemplo:
            // $qrSetting = QrSetting::where('qr_key', $plazaKey)->first();
            // if (!$qrSetting || !$qrSetting->is_active) {
            //     return null; // Plaza key inválida
            // }
        }

        return $order;
    }

    /**
     * Obtener estadísticas de tokens generados
     * 
     * @return array Estadísticas
     */
    public function getTokenStats(): array
    {
        return [
            'total_tokens_generated' => Order::whereNotNull('verification_token')->count(),
            'unique_tokens' => Order::whereNotNull('verification_token')->distinct('verification_token')->count(),
            'confirmed_orders' => Order::whereNotNull('confirmed_at')->count(),
            'pending_confirmation' => Order::whereNull('confirmed_at')->whereNotNull('verification_token')->count(),
        ];
    }
}
