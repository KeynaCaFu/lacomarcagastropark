# Extenciones para poder utilizar php/Laravel

## Revisar el php init que este de esta manera estas 3 extenciones
extension=gd
;extension=gd2
extension=zip

## Luego iniciar la instalacion 
composer install

## Ocupamos mas extenciones
composer install --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip --ignore-platform-req=ext-dom --ignore-platform-req=ext-xml --ignore-platform-req=ext-xmlreader


## Ahoras las dependencias de Node.js (VITE)
npm install
npm run build
npm run dev

## EXTENCIONES PARA PDF / EXCEL
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf

# Instala la librería de Pusher para PHP
composer require pusher/pusher-php-server

# Instala las librerías de JavaScript (Laravel Echo y Pusher JS)
npm install --save laravel-echo pusher-js

# Compila los assets (Vite o Mix)
npm run build

# COMANDOS PARA LAS PRUEBAS QA

1. PR PROBADA CON PHPUNIT
php artisan test tests/Feature/ReviewDisplayTest.php

2. Pruebas QA 07-05-2026
para probar la prueba de que un evento fue publicado aparezca en el sitio web el tiempo real con webSocket
php artisan test --filter=Event --verbose

3.  Ejecutar todas las pruebas de cancelación de pedidos
php artisan test --filter=OrderCancellationTest

4. PRUEBA QA para validar cambio en horario tiempo real
comando php artisan test tests/Feature/LocalScheduleUpdateTest.php --no-coverge

5. PRUEBA QA PARA CAMBIAR EL ESTADO DE UN PRODUCTO TIEMPO REAL 
para ejecutar php artisan test tests/Feacture/ProductDeactivationTest.php --no-coverage

6. PR REVISADA Y PROBADA CON PHPUNIT
para probar: php artisan test tests/Feature/ReviewDeletionTest.php

7. para probar prueba sobre listado de reseñas de un cliente 
php artisan test --filter=ClientReviewsVisualizationTest --verbose

8. PRUEBA QA para validar cambio en horario tiempo real
comando php artisan test tests/Feature/LocalScheduleUpdateTest.php --no-coverge

