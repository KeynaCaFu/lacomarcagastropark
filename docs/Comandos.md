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