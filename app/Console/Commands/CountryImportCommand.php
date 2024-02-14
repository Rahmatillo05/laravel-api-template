<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CountryImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'country:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import countries from a JSON file.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get('https://api.biztv.media/api/v1/countries?per_page=400&include=icon');
        if ($response->failed()) {
            $this->error('Failed to fetch countries from the API.');
            return;
        } else {
            $countries = $response->json()['data'];
            try {
                DB::beginTransaction();
                foreach ($countries as $country) {
                    $flag_id = $this->saveFile($country['icon']['url'], $country['icon']['title']);
                    Country::updateOrCreate(
                        ['code' => $country['code']],
                        [
                            'name' => $country['name_uz'],
                            'flag_symbol' => $country['flag_symbol'],
                            'flag_id' => $flag_id,
                            'code' => $country['code']
                        ]
                    );
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
                return;
            }
            $this->info('Countries imported successfully.');
        }
    }

    public function saveFile($url, $file_name)
    {
        $response = Http::get($url);
        if ($response->failed()) {
            throw new \Exception('Failed to fetch file from the API.');
        } else {
            $file = $response->body();
            $response = Http::attach('files', $file, $file_name)
                ->post('http://127.0.0.1:8000/api/files/upload');
            if ($response->failed()) {
                throw new \Exception($response->json()['message']);
            }

            return $response->json()['data'][0]['id'];
        }
    }
}
