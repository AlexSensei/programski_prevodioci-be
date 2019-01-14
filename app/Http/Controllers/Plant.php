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
    public function index()
    {
        $xmlFiles =array_filter(Storage::disk('public')->files(), function ($item) {return strpos($item, 'xml');});

        $plants = [];

        foreach ($xmlFiles as $file) {
            $content = Storage::disk('public')->get($file);

            $xml = XmlParser::extract($content);
            
            $plant = $xml->parse([
                'name' => ['uses' => 'plant::name'],
                'planting_month' => ['uses' => 'plant.month.planting'],
                'harvesting_month' => ['uses' => 'plant.month.harvest'],
                'plantingPlace' => ['uses' => 'plant.plantingPlace'],
                'plantingDepth' => ['uses' => 'plant.plantingDepth'],
                'light' => ['uses' => 'plant.light'],
                'water' => ['uses' => 'plant.water'],
                'tips' => ['uses' => 'plant.tips'],
            ]);
            array_push($plants,$plant);
        }

        return $plants;
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
        Storage::put('public/' . $request->name . '.xml', $plant);

        $content = Storage::disk('public')->get($request->name . '.xml');

        $xml = XmlParser::extract($content);
            
        $plant = $xml->parse([
            'name' => ['uses' => 'plant::name'],
            'planting_month' => ['uses' => 'plant.month.planting'],
            'harvesting_month' => ['uses' => 'plant.month.harvest'],
            'plantingPlace' => ['uses' => 'plant.plantingPlace'],
            'plantingDepth' => ['uses' => 'plant.plantingDepth'],
            'light' => ['uses' => 'plant.light'],
            'water' => ['uses' => 'plant.water'],
            'tips' => ['uses' => 'plant.tips'],
        ]);

        return $plant;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

        $user = $xml->parse([
            'id' => ['uses' => 'user.id'],
            'email' => ['uses' => 'user.email'],
            'followers' => ['uses' => 'user::followers'],
        ]);

        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
