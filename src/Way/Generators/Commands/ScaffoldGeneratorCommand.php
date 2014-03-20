<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\DB;
use Config;
use stdClass;

class ScaffoldGeneratorCommand extends ResourceGeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a new resource (with boilerplate)';
    
    
    public function fire(){
        parent::fire();  
        $resource = $this->argument('resource');
        $this->migration_modules($resource);
        $this->call('migrate');
    }
    
    protected function migration_modules($resource){
        $migrationName = $this->getMigrationName($resource);
        $name = $this->getTableName($resource);
        $this->call('generate:migration', [
            'migrationName' => $migrationName.date('Ymdhis'),
            '--fromScaffold' => $name
        ]);
    }
    
    protected function insert_modules(){
        $name = $this->getTableName($this->argument('resource'));
        $show_name = ucwords(str_replace('_', ' ', snake_case($name)));
        $obj = new stdClass();
        $obj->privileges = array('create', 'index', 'edit', 'store', 'show', 'update', 'destroy'); 
        $obj->name = $show_name;
        $obj->icon = 'fa-bars';
        DB::insert('insert into modules (kode, body) values (?, ?)', array($name, json_encode($obj)));
        
        $menus = DB::table('settings')->where('id', '=', 1)->first();
        $new_body = substr($menus->body, 0, -3).',{"title":"'.$show_name.'","routes":"'.$name.'"}]}]';
        DB::table('settings')->where('id', '=', 1)->update(array('body' => $new_body));
    }

    /**
     * Call model generator if user confirms
     *
     * @param $resource
     */
    protected function callView($resource)
    {        
        $collection = $this->getTableName($resource);
        $modelName = $this->getModelName($resource);

        if ($this->confirm("Do you want me to create views for this $modelName resource? [yes|no]"))
        {

            foreach(['index', 'show', 'create', 'edit', '_form'] as $viewName)
            {
                $viewName = "{$collection}.{$viewName}";

                $this->call('generate:view', [
                    'viewName' => $viewName,
                    '--templatePath' => Config::get("generators::config.scaffold_view_template_path"),
                    '--fields' => $this->option('fields')
                ]);
            }
        }
    }
    
    protected function callModel($resource)
    {
        $modelName = $this->getModelName($resource);

        if ($this->confirm("Do you want me to create a $modelName model? [yes|no]"))
        {
            $this->call('generate:model', [
                'modelName' => $modelName,
                '--templatePath' => Config::get("generators::config.scaffold_model_template_path"),
                '--fields' => $this->option('fields')
            ]);
        }
    }

    /**
     * Call controller generator if user confirms
     *
     * @param $resource
     */
    protected function callController($resource)
    {
        $controllerName = $this->getControllerName($resource);

        if ($this->confirm("Do you want me to create a $controllerName controller? [yes|no]"))
        {
            $this->call('generate:controller', [
                'controllerName' => $controllerName,
                '--templatePath' => Config::get("generators::config.scaffold_controller_template_path")
            ]);
        }
    }

}
