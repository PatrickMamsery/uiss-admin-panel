<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Orchid\Layouts\ChartsLayout;
use App\Orchid\Layouts\OverviewMetrics;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

use App\Models\User;
use App\Models\CustomRole;
use App\Models\Project;
use App\Models\Program;
use App\Models\Event as CustomEvent;

class PlatformScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'UISS - Admin Panel';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Welcome to UISS Admin Panel.';

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

            // get values
            $members = User::where('role_id', CustomRole::where('name', 'member')->first()->id)->count();
            $projects = Project::count();
            $programs = Program::count();
            $events = CustomEvent::count();

            return [
                'metrics' => [
                    ['keyValue' => number_format($members, 0), 'keyDiff' => 0],
                    ['keyValue' => number_format($projects, 0), 'keyDiff' => 0],
                    ['keyValue' => number_format($programs, 0), 'keyDiff' => 0],
                    ['keyValue' => number_format($events, 0), 'keyDiff' => 0],
                ],
            ];

        // $charts = [
        //     [
        //         'labels' => ['january','february','march','april','may','june','july'],
        //         'title'  => 'Some Data',
        //         'values' => [25, 40, 30, 35, 8, 52, 17, -4],
        //     ],
        // ];
    
        // return [
        //     'charts' => $charts,
        // ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Go to site')
                ->href('https://admin.uiss.patrickmamsery.works')
                ->target('_blank')
                ->icon('globe-alt'),
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
            // ChartsLayout::class
            OverviewMetrics::class,

            Layout::view('home')
        ];
    }
}
