<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agregar índices para optimizar queries de Plaza
     * Versión robusta: solo agrega índices que no existen
     */
    public function up(): void
    {
        // Helper para verificar si un índice existe
        $hasIndex = function($table, $indexName) {
            try {
                $columns = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
                return !empty($columns);
            } catch (\Exception $e) {
                return false;
            }
        };

        // Índices en tabla tbproduct
        Schema::table('tbproduct', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('tbproduct', 'tbproduct_status_index')) {
                try { $table->index('status'); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tbproduct', 'tbproduct_category_index')) {
                try { $table->index('category'); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tbproduct', 'tbproduct_status_category_index')) {
                try { $table->index(['status', 'category']); } catch (\Exception $e) {}
            }
        });

        // Índices en tabla tbschedule
        Schema::table('tbschedule', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('tbschedule', 'tbschedule_local_id_index')) {
                try { $table->index('local_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tbschedule', 'tbschedule_local_id_day_of_week_index')) {
                try { $table->index(['local_id', 'day_of_week']); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tbschedule', 'tbschedule_status_index')) {
                try { $table->index('status'); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tbschedule', 'tbschedule_local_id_day_of_week_status_index')) {
                try { $table->index(['local_id', 'day_of_week', 'status']); } catch (\Exception $e) {}
            }
        });

        // Índices en tabla tblocal
        Schema::table('tblocal', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('tblocal', 'tblocal_status_index')) {
                try { $table->index('status'); } catch (\Exception $e) {}
            }
        });

        // Índices en tabla tblocal_product
        Schema::table('tblocal_product', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('tblocal_product', 'tblocal_product_local_id_index')) {
                try { $table->index('local_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tblocal_product', 'tblocal_product_product_id_index')) {
                try { $table->index('product_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tblocal_product', 'tblocal_product_local_id_product_id_index')) {
                try { $table->index(['local_id', 'product_id']); } catch (\Exception $e) {}
            }
        });

        // Índices en tabla tbproduct_review
        Schema::table('tbproduct_review', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('tbproduct_review', 'tbproduct_review_product_id_index')) {
                try { $table->index('product_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tbproduct_review', 'tbproduct_review_review_id_index')) {
                try { $table->index('review_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndex('tbproduct_review', 'tbproduct_review_product_id_review_id_index')) {
                try { $table->index(['product_id', 'review_id']); } catch (\Exception $e) {}
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada al revertir - los índices son útiles mantener
    }
};
