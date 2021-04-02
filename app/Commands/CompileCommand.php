<?php

namespace App\Commands;

use Mni\FrontYAML\Parser;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;

class CompileCommand extends Command
{
    protected $signature = 'compile {site}';

    protected $description = 'Gonna Compile The Files!';

    private string $inputPath;
    private string $outputPath;
    private string $templatePath;
    private object $bagEnd;

    public function handle(): mixed
    {
        $this->bagEnd = $this->findBagEnd();

        $this->inputPath = "{$this->bagEnd->path}{$this->bagEnd->input}";
        $this->outputPath = "{$this->bagEnd->path}{$this->bagEnd->output}";
        $this->templatePath = "{$this->bagEnd->path}{$this->bagEnd->template}";

        $parser = new Parser(null, new CommonMarkParser());

        $files =  collect(Storage::disk('root')->allFiles($this->inputPath))
                        ->reject(function($file) {
                            return ! Str::endsWith($file, $this->bagEnd->input_extensions);
                        })
                        ->reject(function($file) {
                            return Str::endsWith($file, $this->bagEnd->exclude_files);
                        });

        $files->each(function($file) use($parser) {
            $document = $parser->parse(Storage::disk('root')->get($file));
            $yaml = $document->getYAML();

            if (!$yaml) {
                $this->error("File needs a YAML section: ".$file);
                exit();
            }

            $content = $document->getContent();
            $templateFile = $yaml['template'] ?? $this->bagEnd->default_template;

            $template = Storage::disk('root')->get("{$this->templatePath}/{$templateFile}");

            foreach($yaml as $key => $value) {
                $template = str_replace("{{".$key."}}", $value, $template);
            }

            $template = str_replace("{{content}}", $content, $template);

            $newFilename = str_replace($this->inputPath, $this->outputPath, $file);
            foreach($this->bagEnd->input_extensions as $extension) {
                $newFilename = str_replace($extension, $this->bagEnd->output_extension, $newFilename);
            }

            Storage::disk('root')->put($newFilename, $template);
            $this->info("Compiling: {$newFilename}");
        });

        return $this->info("done");
    }

    private function findBagEnd()
    {
        $hobbiton = json_decode(Storage::get('.hobbiton'))->sites;

        return  collect($hobbiton)->firstWhere('name', $this->argument('site'));
    }
}
