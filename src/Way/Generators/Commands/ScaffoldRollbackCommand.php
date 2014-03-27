<?php

namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ScaffoldRollbackCommand extends GeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'rollback:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback Scaffold resource (with boilerplate)';
    protected $modelName = '';
    protected $collection = '';
    protected $controllerName = '';
    protected $resource = '';

    public function fire() {
        $this->resource = $this->argument('resource');

        $this->modelName = $this->getModelName();
        $this->collection = $this->getTableName();
        $this->controllerName = $this->getControllerName();

        $list = $this->getFileGenerationPath();

        $this->rollbackView($list['view']);
        $this->rollbackModel($list['model']);
        $this->rollbackController($list['controller']);
        $this->rollbackSeeder($list['seeder']);
        $this->rollbackMigration();
        $this->rollbackRoutes();
    }

    protected function file_remove($file) {
        if (File::exists($file)) {
            File::delete($file);
            $this->info(sprintf("Sukses menghapus file %s", $file));
            return true;
        }
        return false;
    }

    protected function directory_remove($file) {
        if (File::isDirectory($file)) {
            File::deleteDirectory($file);
            $this->info(sprintf("Sukses menghapus direktori %s", $file));
        }
    }

    protected function rollbackSeeder($file) {
        $this->file_remove($file);
    }

    protected function rollbackController($file) {
        $this->file_remove($file);
    }

    protected function rollbackModel($file) {
        $this->file_remove($file);
    }

    protected function rollbackView($file) {
        $this->directory_remove($file);
    }

    protected function rollbackMigration() {
        $path = $this->getPathByOptionOrConfig('path', 'migration_target_path');
        $name = $this->getMigrationName();
        $fileName = $name . '.php';
        if (File::isDirectory($path)) {
            foreach (File::files($path) as $value) {
                if (Str::endsWith($value, $fileName)) {
                    if($this->file_remove($value)){
                        Schema::dropIfExists($this->collection);
                        DB::table('eb_modules')->where('kode', '=', $this->collection)->delete();
                        DB::table('migrations')->where('migration', '=', str_replace('.php', '', last(explode('/', $value))))->delete();
                    }
                } else {
                    $pattern = "/$name(\d{14}).php\z/";
                    preg_match($pattern, $value, $results);
                    if (!empty($results)){
                        $this->file_remove($value);
                        DB::table('migrations')->where('migration', '=', str_replace('.php', '', last(explode('/', $value))))->delete();
                    } 
                }
            }
            $this->call('dump-autoload');
            
        }
    }

    protected function rollbackRoutes() {
        $path = $this->getPathByOptionOrConfig('path', 'routes_target_path').'/routes.php';        
        $this->generator->make_or_update(
            $this->getTemplatePath(),
            $this->getTemplateDataRoutes($path),
            $path
        );
    }

    protected function getModelName() {
        return ucwords(str_singular(camel_case($this->resource)));
    }

    protected function getControllerName() {
        return ucwords(str_plural(camel_case($this->resource))) . 'Controller';
    }

    protected function getTableName() {
        return str_plural($this->resource);
    }

    protected function getMigrationName() {
        return "create_" . str_plural($this->resource) . "_table";
    }

    protected function getTemplatePath() {
        return $this->getPathByOptionOrConfig('templatePath', 'view_routes_path');
    }

    protected function getTemplateDataRoutes($path) {
        $tag = '<?php'.PHP_EOL.PHP_EOL;
        $contents = '';
        $string = "Route::resource('{$this->collection}', '{$this->controllerName}');";
        if (File::isFile($path)){
            $contents = str_replace($tag, '', File::get($path));
        }
        $routes = $tag.str_replace(PHP_EOL.$string, '', $contents);
        
        return compact('routes');
    }

    protected function getTemplateData() {        
    }

    protected function getFileGenerationPath() {
        $path_model = $this->getPathByOptionOrConfig('path', 'model_target_path');
        $model = $path_model . '/' . ucwords($this->modelName) . '.php';

        $path_view = $this->getPathByOptionOrConfig('path', 'view_target_path');
        $view = "{$path_view}/{$this->collection}/";

        $path_ctrl = $this->getPathByOptionOrConfig('path', 'controller_target_path');
        $controller = $path_ctrl . '/' . $this->controllerName . '.php';

        $path_seeder = $this->getPathByOptionOrConfig('path', 'seed_target_path');
        $tableName = ucwords(str_plural($this->getModelName($this->resource)));
        $seeder = "{$path_seeder}/{$tableName}TableSeeder.php";

        return compact('model', 'view', 'controller', 'seeder');
    }

    protected function getArguments() {
        return [
            ['resource', InputArgument::REQUIRED, 'Singular resource name']
        ];
    }

}
