<?php namespace Way\Generators\Commands;

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Way\Generators\Parsers\MigrationFieldsParser;

class ViewGeneratorCommand extends GeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a view';

    /**
     * Create directory tree for views,
     * and fire generator
     */
    public function fire()
    {
        $directoryPath = dirname($this->getFileGenerationPath());

        if ( ! File::exists($directoryPath))
        {
            File::makeDirectory($directoryPath, 0777, true);
        }

        parent::fire();
    }

    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        $path = $this->getPathByOptionOrConfig('path', 'view_target_path');
        $viewName = str_replace('.', '/', $this->argument('viewName'));

        return sprintf('%s/%s.blade.php', $path, $viewName);
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        $arr_action = explode('.', $this->argument('viewName'));
        $name = ucwords($arr_action[0]);
        $collection = strtolower($name);
        $resource = str_singular($collection);
        $model = ucwords($resource);
        $parser = new MigrationFieldsParser();
        $fields = $parser->parse($this->option('fields'));
        list($headers, $body, $items, $colspan) = $this->parseFields($fields);
        
        return compact('name', 'collection', 'resource', 'model', 'headers', 'body', 'colspan', 'items');
    }
    
    protected function parseFields($fields){
        $headers = $body = $field = array();
        foreach ($fields as $key => $value) {
            $headers[] = '<th>'.ucwords(str_replace('_', ' ', $key)).'</th>';
            $body[] = '<td>{{ $data->'.$key.' }}</td>';
            $field[] = '{{ Helper::'.$this->parse_helper_field($value['type']).'(\''.$key.'\', $errors)}}';
        }
        $headers[] = "<th class=\"action2\">#</th>";
        $colspan = count($fields) + 1;
        $tab = "\t\t\t\t\t\t\t";
        return array(implode(PHP_EOL.$tab, $headers), implode(PHP_EOL.$tab, $body), implode(PHP_EOL, $field), $colspan);
    }
    
    protected function parse_helper_field($type){
        switch (strtolower($type)){
            case 'text' : return 'TextArea'; 
            default : return 'Textfield'; 
        }
    }

    /**
     * Get path to the template for the generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        $arr_action = explode('.', $this->argument('viewName'));
        return $this->getPathByOptionOrConfig('templatePath', 'view_template_path').'/'.end($arr_action).'.txt';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['viewName', InputArgument::REQUIRED, 'The name of the desired view']
        ];
    }
    
    protected function getOptions()
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Fields for the migration'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'Where should the file be created?', app_path('views')],
            ['templatePath', null, InputOption::VALUE_OPTIONAL, 'The location of the template for this generator']
        ];
    }

}
