<?php

namespace DoublePoint\DPAbmer\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class ABMCreateCommand extends Command
{
    /**
     * The filesystem handler.
     * @var object
     */
    protected $files;

    /***
     * El nombre del ABM en singular
     * @var string
     */
    protected $abm_singular;

    /***
     * El nombre del ABM en plural
     * @var string
     */
    protected $abm_plural;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abm:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creador de ABM generico';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->abm_singular = $this->ask('Como se va a llamar el ABM en singular? (Ej: producto, persona)');
        $this->abm_plural   = $this->ask('Como se va a llamar el ABM en plural? (Ej: productos, personas)');
        $hay_subida_de_archivos = $this->choice('Queres que el ABM pueda subir archivos? (Ej: imagenes, adjuntos)', ['Si', 'No']);

        // Si ya existe auth, ni preguntar
        $abm_users = "no";

        if (! is_dir(base_path('resources/views/auth'))) {
            $abm_users = $this->choice('Queres instalar el ABM de usuarios? (Incluye diseño)', ['Si', 'No']);
        }

        // Comienzo la barra de progreso
        $bar = $this->output->createProgressBar();
        $bar->start();

        if (strtolower(trim($abm_users)) == "si")
        {
            $this->line(' Instalando ABM Usuarios de Laravel...');
            Artisan::call('make:auth');
            $bar->advance();
        }

        $this->line(' Creando directorios...');
            $this->crearDirectorios();
        $bar->advance();

        $this->info(' Copiando vistas...');
            $this->copiarVistas();
            $this->copiarAssets();
        $bar->advance();

        $this->info(' Copiando el modelo...');
            $this->copiarModelo();
        $bar->advance();

        $this->info(' Copiando el controlador...');
            $this->copiarControlador();
        $bar->advance();

        if (strtolower(trim($hay_subida_de_archivos)))
        {
            $this->line(' Agregando subida de archivos...');
                $this->agregarSubidaArchivos();
            $bar->advance();
        }

        $this->info(' Copiando migracion...');
            $this->copiarMigracion();
        $bar->advance();

        $this->info(' Instalando rutas...');
            $this->copiarRutas();

        $bar->finish();
    }

    private function crearDirectorios()
    {
        if (! is_dir(base_path('resources/views/' . $this->abm_plural))) {
            mkdir(base_path('resources/views/' . $this->abm_plural), 0755, true);
            mkdir(base_path('resources/assets/js/' . $this->abm_plural), 0755, true);
            mkdir(base_path('public/js/' . $this->abm_plural), 0755, true);
        } else {
            abort('500', 'El directorio ' . $this->abm_plural . ' ya existe');
        }
    }

    private function copiarVistas()
    {
        $vistas = [
            'create.stub' => 'create.blade.php',
            'edit.stub'   => 'edit.blade.php',
            'listar.stub' => 'listar.blade.php',
            'show.stub'   => 'show.blade.php'
        ];

        foreach ($vistas as $key => $value) {
            $new_path = base_path('resources/views/' . $this->abm_plural . '/' . $value);
            $old_path = __DIR__.'/../resources/views/generic/' . $key;

            $this->replaceAndSave($old_path, ['{{ABM-SINGULAR}}', '{{ABM-PLURAL}}', '{{ABM-SINGULAR-MAYUSCULA-PRIMERA-LETRA}}', '{{ABM-PLURAL-MAYUSCULA-PRIMERA-LETRA}}'], [$this->abm_singular, $this->abm_plural, ucfirst($this->abm_singular), ucfirst($this->abm_plural)], $new_path);
        }
    }

    /**
     * Copiar los js en /assets y /public
     */
    private function copiarAssets()
    {
        $assets = [
            'create.stub' => 'create.js',
            'edit.stub'   => 'edit.js',
            'delete-link.stub' => 'delete-link.js',
            'listar.stub'   => 'listar.js'
        ];

        foreach ($assets as $key => $value) {
            $new_path_assets = base_path('resources/assets/js/' . $this->abm_plural . '/' . $value);
            $new_path_public = base_path('public/js/' . $this->abm_plural . '/' . $value);
            $old_path = __DIR__.'/../resources/assets/' . $key;

            $this->replaceAndSave($old_path, ['{{ABM-SINGULAR}}', '{{ABM-PLURAL}}', '{{ABM-SINGULAR-MAYUSCULA-PRIMERA-LETRA}}', '{{ABM-PLURAL-MAYUSCULA-PRIMERA-LETRA}}'], [$this->abm_singular, $this->abm_plural, ucfirst($this->abm_singular), ucfirst($this->abm_plural)], $new_path_assets);
            $this->replaceAndSave($old_path, ['{{ABM-SINGULAR}}', '{{ABM-PLURAL}}', '{{ABM-SINGULAR-MAYUSCULA-PRIMERA-LETRA}}', '{{ABM-PLURAL-MAYUSCULA-PRIMERA-LETRA}}'], [$this->abm_singular, $this->abm_plural, ucfirst($this->abm_singular), ucfirst($this->abm_plural)], $new_path_public);
        }
    }

    private function copiarControlador()
    {
        $new_path = $this->getNombreControlador();
        $old_path = $this->getRutaControladorStub();

        $this->replaceAndSave($old_path, ['{{ABM-SINGULAR}}', '{{ABM-PLURAL}}', '{{ABM-SINGULAR-MAYUSCULA-PRIMERA-LETRA}}', '{{ABM-PLURAL-MAYUSCULA-PRIMERA-LETRA}}'], [$this->abm_singular, $this->abm_plural, ucfirst($this->abm_singular), ucfirst($this->abm_plural)], $new_path);
    }

    private function getNombreControlador()
    {
        $nombre = ucfirst($this->abm_plural);
        $nuevo_nombre = $nombre . 'Controller.php';

        $new_path = base_path('app/Http/Controllers/' . $nuevo_nombre);

        return $new_path;
    }

    private function getRutaControladorStub()
    {
        return __DIR__.'/../Http/Controllers/Controller.stub';
    }

    private function copiarModelo()
    {
        $nombre = ucfirst($this->abm_singular);
        $nuevo_nombre = $nombre . '.php';

        $new_path = base_path('app/' . $nuevo_nombre);
        $old_path = __DIR__.'/../Model.stub';

        $this->replaceAndSave($old_path, ['{{ABM-SINGULAR}}', '{{ABM-PLURAL}}', '{{ABM-SINGULAR-MAYUSCULA-PRIMERA-LETRA}}', '{{ABM-PLURAL-MAYUSCULA-PRIMERA-LETRA}}'], [$this->abm_singular, $this->abm_plural, ucfirst($this->abm_singular), ucfirst($this->abm_plural)], $new_path);
    }

    private function copiarMigracion()
    {
        $new_path = base_path('database/migrations/' . date('Y_m_d_His_') . 'create_' . $this->abm_plural . '_table.php');
        $old_path = __DIR__.'/../database/migrations/Migration.stub';

        $this->replaceAndSave($old_path, ['{{ABM-SINGULAR}}', '{{ABM-PLURAL}}', '{{ABM-SINGULAR-MAYUSCULA-PRIMERA-LETRA}}', '{{ABM-PLURAL-MAYUSCULA-PRIMERA-LETRA}}'], [$this->abm_singular, $this->abm_plural, ucfirst($this->abm_singular), ucfirst($this->abm_plural)], $new_path);
    }

    private function copiarRutas()
    {
        $new_path = base_path('app/Http/routes.php');
        $old_path = __DIR__.'/../Http/routes.stub';

        $this->replaceAndSave($old_path, ['{{ABM-SINGULAR}}', '{{ABM-PLURAL}}', '{{ABM-SINGULAR-MAYUSCULA-PRIMERA-LETRA}}', '{{ABM-PLURAL-MAYUSCULA-PRIMERA-LETRA}}'], [$this->abm_singular, $this->abm_plural, ucfirst($this->abm_singular), ucfirst($this->abm_plural)], $new_path, true);
    }

    private function agregarSubidaArchivos()
    {
        $controller_path = $this->getNombreControlador();
        $subir_archivo_path = __DIR__.'/../Http/Controllers/SubirArchivo.stub';

        $subir_archivo_limpio = $this->getReplacedContent(['{{ABM-SINGULAR}}', '{{ABM-PLURAL}}', '{{ABM-SINGULAR-MAYUSCULA-PRIMERA-LETRA}}', '{{ABM-PLURAL-MAYUSCULA-PRIMERA-LETRA}}'], [$this->abm_singular, $this->abm_plural, ucfirst($this->abm_singular), ucfirst($this->abm_plural)], $subir_archivo_path);

        $this->replaceAndSave($controller_path, ['{{SUBIR_ARCHIVOS}}'], [$subir_archivo_limpio], $controller_path);
    }

    /**
     * Open haystack, find and replace needles, save haystack.
     *
     * @param  string $oldFile The haystack
     * @param  mixed  $search  String or array to look for (the needles)
     * @param  mixed  $replace What to replace the needles for?
     * @param  string $newFile Where to save, defaults to $oldFile
     *
     * @return void
     */
    private function replaceAndSave($oldFile, $search, $replace, $newFile = null, $append = false)
    {
        $newFile = ($newFile == null) ? $oldFile : $newFile;

        $replacing = $this->getReplacedContent($search, $replace, $oldFile);

        if ($append) {
            $this->files->append($newFile, $replacing);
        } else {
            $this->files->put($newFile, $replacing);
        }
    }

    private function getReplacedContent($search, $replace, $oldFile)
    {
        $file = $this->files->get($oldFile);
        $replacing = str_replace($search, $replace, $file);

        return $replacing;
    }
}
