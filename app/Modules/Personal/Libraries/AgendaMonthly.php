<?php
/**
 * This file is part of the NEO ERP Application.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */

namespace App\Modules\Personal\Libraries;

use O2System\Html\Element;
use O2System\Spl\Datastructures\SplArrayObject;

class AgendaMonthly
{

    public function __construct($data, $year = null, $month = null)
    {
        $this->data = $data;
        $this->year = $year;
        $this->month = $month;
    }

    public function __toString()
    {
        $build = $this->build();
        return !is_null($build) ? $build : '';
    }

    public function build()
    {
        $data = $this->data;
        $data = $this->filterData($data, $this->year, $this->month);

        $row = '';
        foreach ($data as $key => $item) {
            $row .= $this->generateRow($item)->render();
        }

        return $row;
    }

    public function generateRow($data, $date = 0)
    {
        $element = new Element('tr');
        $element->attributes->addAttributeClass(['tr-agenda']);

        foreach ($data as $date => $item) {
            $node = $this->generateColumns($item);
            $element->childNodes->push($node);
        }

        return $element;
    }

    public function generateColumns($data)
    {
        $table_data = new Element('td');
        $table_data->attributes->addAttributeClass(['calendar-square']);

        $element_day = new Element('p');
        $element_day->textContent->prepend($data[0]->day);
        $table_data->childNodes->push($element_day);

        foreach ($data as $item) {
            $node = $this->generateColumn($item);

            if ($node) {
                $table_data->childNodes->push($node);
            }
        }

        return $table_data;
    }

    public function generateColumn($item)
    {
        if ($item->title) {
            $element = new Element('div');
            $element->attributes->addAttributeClass(['agenda', 'bg-danger', 'mt-3']);

            $element->textContent->prepend($item->title);
            return $element;
        }

        return false;
    }

    protected function filterData($data, $year = null, $month = null)
    {
        $dayStart = 'MONDAY';

        $month = sprintf('%1$02s', $month);
        $dateFirst = $year.'-'.$month.'-01';

        $results = [];

        // Add blank date for previous month
        if (date('N', strtotime($dateFirst)) > 1) {
            $dateCurrent = $dateFirst;
            for ($a=1; $a < date('N', strtotime($dateFirst)); $a++) { 
                $dateCurrent = date_format(date_sub(date_create($dateCurrent), date_interval_create_from_date_string('1 days')), 'Y-m-d');
                $results[1][$dateCurrent][] = new SplArrayObject([
                    'day' => ''
                ]);
            }
        }

        // Start generate data
        for ($i=1; $i <= date('t', strtotime($year.'-'.$month.'-01')); $i++) { 
            $dateCurrent = $year.'-'.$month.'-'.sprintf('%1$02s', $i);

            $weekFirst = date('W', strtotime($dateFirst));
            $weekCurrent = date('W', strtotime($dateCurrent));

            $weekNum = 1 + (intval($weekCurrent) - intval($weekFirst));

            foreach ($data as $item) {
                if (substr($item->time_start, 0, 10) == $dateCurrent) {
                    $item->day = $i;
                    $results[$weekNum][$dateCurrent][] = $item;
                }
            }

            $results[$weekNum][$dateCurrent][] = new SplArrayObject([
                'day' => $i
            ]);

        }

        // print_out ($results);

        return $results;
    }

}