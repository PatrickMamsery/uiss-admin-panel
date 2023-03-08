<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Orchid\Layouts\ChartsLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Get Started';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Welcome to your Orchid application.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {

        // Actual data gruping monthly example 
        
            // $vendor_monthly_groups=DB::table('vendors')->select(DB::raw('count(id) as `data`'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            //                         ->groupby('year','month')
            //                         ->get();

            // $values=[];
            // $count=0;
            // $months_raw=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            // $months=[];

            // foreach ($vendor_monthly_groups as $entry) {

            // if($entry->year == date("Y")){
            // array_push($values,$entry->data);
            // array_push($months,$months_raw[$count]);
            // $count++;
            // }

            // }

            // $charts = [
            // [
            // 'labels' => $months,
            // 'title'  => 'Year - ' . date("Y"),
            // 'values' => array_reverse($values),
            // ],

            // ];

            

        $charts = [
            [
                'labels' => ['january','february','march','april','may','june','july'],
                'title'  => 'Some Data',
                'values' => [25, 40, 30, 35, 8, 52, 17, -4],
            ],
        ];
    
        return [
            'charts' => $charts,
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
           
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            ChartsLayout::class
        ];
    }
}
