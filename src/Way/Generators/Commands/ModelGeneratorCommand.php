<?php namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Way\Generators\Parsers\MigrationFieldsParser;

class ModelGeneratorCommand extends GeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a model';

    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        $path = $this->getPathByOptionOrConfig('path', 'model_target_path');

        return $path. '/' . ucwords($this->argument('modelName')) . '.php';
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        $parser = new MigrationFieldsParser();
        $fields = $parser->parse($this->option('fields'));
        $field = $this->parseFields($fields);
        $name = ucwords($this->argument('modelName'));
        return compact('name', 'field');
        
    }
    
    protected function parseFields($fields){
        $field = array();
        foreach ($fields as $key => $value) {
            $field[] = "'$key'";
        }
        return implode(', ', $field);
    }

    /**
     * Get path to the template for the generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return $this->getPathByOptionOrConfig('templatePath', 'model_template_path');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['modelName', InputArgument::REQUIRED, 'The name of the desired Eloquent model']
        ];
    }
    
    
    protected function getOptions()
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Fields for the migration'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'Where should the file be created?', app_path('models')],
            ['templatePath', null, InputOption::VALUE_OPTIONAL, 'The location of the template for this generator']
        ];
    }

}
