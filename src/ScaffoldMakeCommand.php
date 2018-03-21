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
        //make request.
        $request_name = $this->argument('class') . 'Request';
        $this->call('make:request', ['name' => $request_name]);

        //make model, factory, migration
        $this->call('make:model', [
            'name' => $this->argument('class'),
        ]);

        $this->call('make:factory', [
            'name' => $this->argument('class') . 'Factory',
        ]);

        $table = Str::plural(Str::snake(class_basename($this->argument('class'))));

        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);

        $this->call('make:controller', [
            'name' => $this->argument('class') . 'Controller',
            '-r' => true,
        ]);

        //make controller.
        $source = file_get_contents(__DIR__.'/stubs/controller.scaffold.stub');

        $step1 = str_replace("DummyProp", mb_strtolower($this->argument('class')), $source);
        $step2 = str_replace("DummyLowerClass", mb_strtolower($this->argument('class')), $step1);
        $step3 = str_replace("DummyClass", $this->argument('class'), $step2);

        file_put_contents(base_path($this->config['path']['controller'] . '/' . $this->argument('class') . 'Controller.php'), $step3);
        //make views.

        $this->makeViews('create');
        $this->makeViews('show');
        $this->makeViews('edit');
        $this->makeViews('index');

        //append to api or web route file.
        $rroute_stub = file_get_contents(__DIR__.'/stubs/routes.stub');
        $rstep2 = str_replace("DummyLowerClass", mb_strtolower($this->argument('class')), $rroute_stub);
        $route_stub = str_replace("DummyClass", $this->argument('class'), $rstep2);

        file_put_contents(
            base_path('routes/' . ($this->option('route') ?: 'web') . '.php'),
            $route_stub,
            FILE_APPEND
        );
    }

    protected function makeViews($view)
    {
        $creating = file_get_contents(__DIR__.'/stubs/' . $view . '.blade.stub');

        if (! is_dir(base_path('resources/views/' . mb_strtolower($this->argument('class')) . 's'))) {
            mkdir(base_path('resources/views/' . mb_strtolower($this->argument('class')) . 's'));
        }

        touch(base_path('resources/views/' . mb_strtolower($this->argument('class')) . 's' . '/' . $view . '.blade.php'));

        $cstep2 = str_replace("DummyLowerClass", mb_strtolower($this->argument('class')), $creating);
        $created = str_replace("DummyClass", $this->argument('class'), $cstep2);

        file_put_contents(base_path('resources/views/' . mb_strtolower($this->argument('class')) . 's' . '/' . $view . '.blade.php'), $created);
    }
}
