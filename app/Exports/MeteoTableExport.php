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
            ['Ամսաթիվ (%)', 'Խոնավություն (տոկոս %)', 'Ջերմաստիճան (ցելսիուս)', 'Քամու արգ. 09 (մ/վրկ)', 'Քամու ուղղ. 09 (աստիճան)', 'Քամու արգ. 27 (մ/վրկ)', 'Քամու ուղղ. 27 (աստիճան)', 'Ճնշում (հպա)', 'Տեսանելիություն 09 (մետր)', 'Տեսանելիություն сер. (մետր)', 'Տեսանելիություն 27 (մետր)', 'Ցողի կետ (ջերմաստիճան)', 'Շփման գործակից', 'Ամպերի ներքին սահմանի բարձր. (մետր)', 'Ամպամածության տեսակը և քանակը'],
            ['Date', 'Humidity (percent %)', 'Temperature (celsius)', 'Wind speed 09 (m/s)', 'Wind direction 09 (degree)', 'Wind speed 27 (m/s)', 'Wind direction 27 (degree)', 'Pressure (GPA)', 'Visibility 09 (metres)', 'Visibility сер. (metres)', 'Visibility 27 (metres)', 'Dew point (temperature)', 'Contact coefficient', 'Inner border of clouds (metres)', 'Cloudiness type and amount'],
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at,
            $row->wet > 0 ? $row->wet : '0',
            $row->temperature > 0 ? $row->temperature : '0',
            $row->wind_speed_09 > 0 ? $row->wind_speed_09 : '0',
            $row->wind_direction_09 > 0 ? $row->wind_direction_09 : '0',
            $row->wind_speed_27 > 0 ? $row->wind_speed_27 : '0',
            $row->wind_direction_27 > 0 ? $row->wind_direction_27 : '0',
            $row->bar > 0 ? $row->bar : '0',
            $row->visibility_09 > 0 ? $row->visibility_09 : '0',
            $row->visibility_mid > 0 ? $row->visibility_mid : '0',
            $row->visibility_27 > 0 ? $row->visibility_27 : '0',
            $row->start_point,
            $row->contact_coefficient,
            $row->cloud_height,
            $row->cloudy,
        ];
    }
}
