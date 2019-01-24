<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Orchestra\Parser\Xml\Facade as XmlParser;

use Illuminate\Support\Facades\Storage;

class Plant extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      
        $filterParams = json_decode($request->filter, true);
    
        $xmlFiles = array_filter(Storage::disk('public')->files(), function ($item) {return strpos($item, 'xml');});

        $plants = [];

        foreach ($xmlFiles as $file) {
            $content = Storage::disk('public')->get($file);

            $xml = XmlParser::extract($content);
            
            $plant = $this->parse($xml);

            if($this->filter($plant, $filterParams)) {
                if($this->validatePlant($plant)) {
                    array_push($plants, $plant);
                }
            }
        }

        return $plants;
    }

    public function validatePlant($plant) 
    {
        if(!$plant['name']) {
            return false;
        }
        if($plant['planting_month'] < 1 || $plant['planting_month'] > 12 ) {
            return false;
        }
        if($plant['harvesting_month'] < 1 || $plant['harvesting_month'] > 12 ) {
            return false;
        }
        if($plant['plantingPlace'] < 1 || $plant['plantingPlace'] > 2 ) {
            return false;
        }
        if($plant['water'] < 1 || $plant['water'] > 3 ) {
            return false;
        }
        if($plant['light'] < 1 || $plant['light'] > 3 ) {
            return false;
        }

        if($plant['plantingDepth'] < 1 || $plant['plantingDepth'] > 25 ) {
            return false;
        }

        return true;
    }

    public function filter($plant, $filter)
    {
        if ($filter['name'] && \strpos($plant['name'], $filter['name']) === false) {
            return false;
        }
        
        if ($filter['plantingMonthFrom'] && $filter['plantingMonthFrom'] > $plant['planting_month']) {
            return false;
        }

        if ($filter['plantingMonthTo'] && $filter['plantingMonthTo'] < $plant['planting_month']) {
            return false;
        }

        if ($filter['harvestingMonthFrom'] && $filter['harvestingMonthFrom'] > $plant['harvesting_month']) {
            return false;
        }

        if ($filter['harvestingMonthTo'] && $filter['harvestingMonthTo'] < $plant['harvesting_month']) {
            return false;
        }

        if ($filter['plantingPlace'] && $filter['plantingPlace'] !== $plant['plantingPlace']) {
            return false;
        }

        if ($filter['waterNeeded'] && $filter['waterNeeded'] !== $plant['water']) {
            return false;
        }

        if ($filter['lightNeeded'] && $filter['lightNeeded'] !== $plant['light']) {
            return false;
        }

        if ($filter['plantingDepth'] && $filter['plantingDepth'] < $plant['plantingDepth']) {
            return false;
        }

        return $plant;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {



        
        $plant = '<api>
    <plant name="' . $request->name . '">
        <month>
            <planting>' . $request->plantingMonth . '</planting>
            <harvest>' . $request->harvestingMonth . '</harvest>
        </month>
        <plantingPlace>' . $request->plantingPlace . '</plantingPlace>
        <plantingDepth>' . $request->plantingDepth . '</plantingDepth>
        <light>' . $request->light . '</light>
        <water>' . $request->water . '</water>
        <tips>' . $request->tips . '</tips>
    </plant>
</api>';

        $name=str_random(20);

        Storage::put('public/' . $name . '.xml', $plant);

        $content = Storage::disk('public')->get($name . '.xml');

        $xml = XmlParser::extract($content);
            
        $plant = $this->parse($xml);

        return $plant;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        $content = Storage::disk('public')->get($name . '.xml');

        $xml = XmlParser::extract($content);

        $plant = $this->parse($xml);

        return $plant;
    }

    public function parse($xml)
    {
        return $xml->parse([
            'name' => ['uses' => 'plant::name'],
            'planting_month' => ['uses' => 'plant.month.planting'],
            'harvesting_month' => ['uses' => 'plant.month.harvest'],
            'plantingPlace' => ['uses' => 'plant.plantingPlace'],
            'plantingDepth' => ['uses' => 'plant.plantingDepth'],
            'light' => ['uses' => 'plant.light'],
            'water' => ['uses' => 'plant.water'],
            'tips' => ['uses' => 'plant.tips'],
        ]);
    }
}
