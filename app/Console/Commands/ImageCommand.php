<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use OpenAI\Laravel\Facades\OpenAI;

class ImageCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image {prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = OpenAI::images()->create([
            'model' => 'dall-e-3',
            'prompt' => $this->argument('prompt'),
            'size' => '1024x1024',
            'quality' => 'hd',
            'response_format' => 'url',
        ]);

        $this->info($response->data[0]['url']);
    }
}
