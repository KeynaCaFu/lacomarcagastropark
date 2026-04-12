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
     * Versión mejorada: verifica si el índice ya existe antes de crearlo
     */
    public function up(): void
    {
        // Helper para verificar si un índice existe
        $hasIndexed = function($table, $indexName) {
            try {
                $columns = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
                return !empty($columns);
            } catch (\Exception $e) {
                return false;
            }
        };

        // Índices en tabla tbproduct
        Schema::table('tbproduct', function (Blueprint $table) use ($hasIndexed) {
            if (!$hasIndexed('tbproduct', 'tbproduct_status_index')) {
                try { $table->index('status'); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tbproduct', 'tbproduct_category_index')) {
                try { $table->index('category'); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tbproduct', 'tbproduct_status_category_index')) {
                try { $table->index(['status', 'category']); } catch (\Exception $e) {}
            }
        });

        // Índices en tabla tbschedule
        Schema::table('tbschedule', function (Blueprint $table) use ($hasIndexed) {
            if (!$hasIndexed('tbschedule', 'tbschedule_local_id_index')) {
                try { $table->index('local_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tbschedule', 'tbschedule_local_id_day_of_week_index')) {
                try { $table->index(['local_id', 'day_of_week']); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tbschedule', 'tbschedule_status_index')) {
                try { $table->index('status'); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tbschedule', 'tbschedule_local_id_day_of_week_status_index')) {
                try { $table->index(['local_id', 'day_of_week', 'status']); } catch (\Exception $e) {}
            }
        });

        // Índices en tabla tblocal
        Schema::table('tblocal', function (Blueprint $table) use ($hasIndexed) {
            if (!$hasIndexed('tblocal', 'tblocal_status_index')) {
                try { $table->index('status'); } catch (\Exception $e) {}
            }
        });

        // Índices en tabla tblocal_product
        Schema::table('tblocal_product', function (Blueprint $table) use ($hasIndexed) {
            if (!$hasIndexed('tblocal_product', 'tblocal_product_local_id_index')) {
                try { $table->index('local_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tblocal_product', 'tblocal_product_product_id_index')) {
                try { $table->index('product_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tblocal_product', 'tblocal_product_local_id_product_id_index')) {
                try { $table->index(['local_id', 'product_id']); } catch (\Exception $e) {}
            }
        });

        // Índices en tabla tbproduct_review
        Schema::table('tbproduct_review', function (Blueprint $table) use ($hasIndexed) {
            if (!$hasIndexed('tbproduct_review', 'tbproduct_review_product_id_index')) {
                try { $table->index('product_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tbproduct_review', 'tbproduct_review_review_id_index')) {
                try { $table->index('review_id'); } catch (\Exception $e) {}
            }
            if (!$hasIndexed('tbproduct_review', 'tbproduct_review_product_id_review_id_index')) {
                try { $table->index(['product_id', 'review_id']); } catch (\Exception $e) {}
            }
        });

        $this->command->info('Plaza indexes optimization: Complete!');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices de tbproduct
        Schema::table('tbproduct', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['category']);
            $table->dropIndex(['status', 'category']);
        });

        // Remover índices de tbschedule
        Schema::table('tbschedule', function (Blueprint $table) {
            $table->dropIndex(['local_id']);
            $table->dropIndex(['local_id', 'day_of_week']);
            $table->dropIndex(['status']);
            $table->dropIndex(['local_id', 'day_of_week', 'status']);
        });

        // Remover índices de tblocal
        Schema::table('tblocal', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        // Remover índices de tblocal_product
        Schema::table('tblocal_product', function (Blueprint $table) {
            $table->dropIndex(['local_id']);
            $table->dropIndex(['product_id']);
            try {
                $table->dropIndex(['local_id', 'product_id']);
            } catch (\Exception $e) {
                // El índice podría no existir
            }
        });

        // Remover índices de tbproduct_review
        Schema::table('tbproduct_review', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['review_id']);
            $table->dropIndex(['product_id', 'review_id']);
        });
    }
};
