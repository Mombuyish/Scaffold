<?php

namespace Yish\Scaffold;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ScaffoldMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scaffold {class}
    {--route= : Append to specific route file, default is web.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make scaffolding resource.';

    private $config;

    public function __construct($config)
    {
        $this->config = $config;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createRequest();
        $this->createModel();
        $this->createFactory();
        $this->createMigration();
        $this->createController();

        $this->updateDummyNameController();

        $this->createViews();

        $this->appendRouteFile();
    }

    protected function makeViews($view)
    {
        $name = Str::plural(Str::snake(class_basename($this->argument('class'))));

        $creating = file_get_contents(__DIR__ . '/stubs/' . $view . '.blade.stub');

        if (! is_dir(base_path('resources/views/' . $name))) {
            mkdir(base_path('resources/views/' . $name));
        }

        touch(base_path('resources/views/' . $name . '/' . $view . '.blade.php'));

        $cstep2 = str_replace("PluralSnakeClass", $name, $creating);
        $cstep3 = str_replace("SnakeClass", Str::snake(class_basename($this->argument('class'))), $cstep2);
        $created = str_replace("DummyClass", $this->argument('class'), $cstep3);

        file_put_contents(base_path('resources/views/' . $name . '/' . $view . '.blade.php'), $created);
    }

    protected function createFactory()
    {
        $this->call('make:factory', [
            'name' => $this->argument('class').'Factory',
            '--model' => $this->argument('class'),
        ]);
    }

    protected function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('class'))));

        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    protected function createController()
    {
        $controller = Str::studly(class_basename($this->argument('class')));

        $this->call('make:controller', [
            'name' => "{$controller}Controller",
            '-r' => true,
        ]);
    }

    protected function createModel()
    {
        $this->call('make:model', [
            'name' => $this->argument('class'),
        ]);
    }

    protected function createRequest()
    {
        $request = Str::studly(class_basename($this->argument('class')));

        $this->call('make:request', [
            'name' => "{$request}Request"
        ]);
    }

    protected function updateDummyNameController()
    {
        $name = Str::plural(Str::snake(class_basename($this->argument('class'))));
        // taking scaffold stub.
        $source = file_get_contents(__DIR__ . '/stubs/controller.scaffold.stub');

        // replace dummy name.
        $step1 = str_replace("DummyProp", Str::snake(class_basename($this->argument('class'))), $source);
        $step2 = str_replace("DummyLowerClass", $name, $step1);
        $step3 = str_replace("DummyClass", $this->argument('class'), $step2);

        // put in controller
        $controller = Str::studly(class_basename($this->argument('class')));
        file_put_contents(base_path($this->config['path']['controller'] . '/' . "{$controller}Controller.php"), $step3);
    }

    protected function createViews()
    {
        $this->makeViews('create');
        $this->makeViews('show');
        $this->makeViews('edit');
        $this->makeViews('index');
    }

    protected function appendRouteFile()
    {
        $name = Str::plural(Str::snake(class_basename($this->argument('class'))));
        $controller = Str::studly(class_basename($this->argument('class')));

        $rroute_stub = file_get_contents(__DIR__ . '/stubs/routes.stub');
        $rstep2 = str_replace("PluralSnakeClass", $name, $rroute_stub);
        $route_stub = str_replace("ControllerClass", "{$controller}Controller", $rstep2);

        file_put_contents(
            base_path('routes/' . ($this->option('route') ?: 'web') . '.php'),
            $route_stub,
            FILE_APPEND
        );
    }
}
