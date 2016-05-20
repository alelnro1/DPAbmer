DPAbmer

Instrucciones de Uso (Si ya tenés un proyecto Laravel >5 creado, pasá al paso 2):

1) Crear un proyecto de Laravel
composer create-project --prefer-dist laravel/laravel NOMBRE-PROYECTO

2) Dentro de composer.json, en require-dev agregar:
"doublepoint/dp-abmer": "1.*"

3) Actualizar dependencias ejecutando el comando "composer update"

4) Abrir config/app.php y agregar el ServiceProvider
DoublePoint\DPAbmer\DPAbmerServiceProvider::class,

5) Ejecutar el comando "php artisan" para verificar que aparezca 
php artisan abm:create

6) Ejecutar el comando "php artisan abm:create" y seguir los pasos

7) Crear la base de datos y vincularla en el archivo .env

8) Migrar las tablas ejecutando el comando "php artisan migrate"

9) Para que aparezca "lindo" el diseño, entrar a resources/layouts/app.blade.php y modificar donde dice @yield('content') por:

    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
	
10) Para una correcta ejecucion de los css y javascripts, agregar en donde corresponde (css arriba y javascript abajo)

	@yield('styles')
	
	@yield('javascript')

10) Ejecutar laravel con el comando "php artisan serve"

11) Acceder a la ruta del abm creado. Ej: localhost:8000/personas
