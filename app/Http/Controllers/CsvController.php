<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoadCsvRequest;
use App\Models\Map;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CsvController extends Controller
{
    /**
     * @param String $resource
     * @param String $type
     * @return Collection
     */
    private function csvToArray(String $resource, String $type)
    {
        $filename = time().".csv";
        $path = "csv/{$type}/{$filename}";
        try {
            copy($resource, public_path($path));
            $csv= file_get_contents($path);
        } catch (\RuntimeException $e) {
            throw new NotFoundResourceException(sprintf('Error opening file "%s".', $resource), 0, $e);
        }

        $csvCollection = collect(array_map("str_getcsv", explode("\n", $csv)));
        $fields = $csvCollection->shift();

        $csvCollection = $csvCollection->map(function ($item) use ($fields) {
            $keyed = collect();
            $aux = 0;
            foreach ($item as $field) {
                if($field === null) {
                    break;
                }
                $keyed->put($fields[$aux], $field);
                $aux++;
            }
            return $keyed;
        });

        File::delete(public_path($path));
        return $csvCollection;
    }

    public function load(LoadCsvRequest $request)
    {
        $csvCollection = $this->csvToArray($request->get('link'), $request->get('type'));
        $map = Map::where(['bank' => $request->get('bank'), 'type' => $request->get('type')])->first();

        foreach ($csvCollection as $transaction) {
            if($transaction->get($map->date)) {
                $request->user()->transactions()->create([
                    'date' => Carbon::createFromFormat($map->date_format, $transaction->get($map->date))->format('Y-m-d'),
                    'amount' => $transaction->get($map->amount),
                    'description' => $transaction->get($map->description),
                    'file' => $request->get('filename'),
                    'type' => $request->get('type'),
                    'bank' => $request->get('bank')
                ]);
            }
        }

        return $request->user()->transactions()->latest()
            ->where('file', $request->get('filename'))
            ->paginate(18)
            ->withQueryString();
    }
}
