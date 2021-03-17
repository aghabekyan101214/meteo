<?php

namespace App\Exports;

use App\Meteo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MeteoTableExport implements FromCollection, WithHeadings, WithMapping
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function headings() : array
    {
        return [
            ['Ամսաթիվ', 'Խոնավություն', 'Ջերմաստիճան', 'Քամու արգ. 09', 'Քամու ուղղ. 09', 'Քամու արգ. 27', 'Քամու ուղղ. 27', 'Ճնշում', 'Տեսանելիություն 09', 'Տեսանելիություն сер.', 'Տեսանելիություն 27', 'Ցողի կետ', 'Շփման գործակից', 'Ամպերի բրձր.', 'Ամպամածություն'],
            ['Date', 'Humidity', 'Temperature', 'Wind speed 09', 'Wind direction 09', 'Wind speed 27', 'Wind direction 27', 'Pressure', 'Visibility 09', 'Visibility сер.', 'Visibility 27', 'Dew point', 'Contact coefficient', 'Cloud height', 'Cloudiness'],
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at,
            $row->wet > 0 ? $row->wet : '-',
            $row->temperature > 0 ? $row->temperature : '-',
            $row->wind_speed_09 > 0 ? $row->wind_speed_09 : '-',
            $row->wind_direction_09 > 0 ? $row->wind_direction_09 : '-',
            $row->wind_speed_27 > 0 ? $row->wind_speed_27 : '-',
            $row->wind_direction_27 > 0 ? $row->wind_direction_27 : '-',
            $row->bar > 0 ? $row->bar : '-',
            $row->visibility_09 > 0 ? $row->visibility_09 : '-',
            $row->visibility_mid > 0 ? $row->visibility_mid : '-',
            $row->visibility_27 > 0 ? $row->visibility_27 : '-',
            $row->start_point,
            $row->contact_coefficient,
            $row->cloud_height,
            $row->cloudy,
        ];
    }
}
