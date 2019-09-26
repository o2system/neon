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

use DateInterval;
use DateTime;
use O2System\Framework\Libraries\Ui\Element;

class Timeline
{
    public function __construct($data)
    {
        $this->data = $data;

        $this->boardStart = new DateTime();
        if (!is_null($data->dateStart) && !$data->dateStart == '0000-00-00 00:00:00') {
            $this->boardStart = DateTime::createFromFormat('Y-m-d H:i:s', $data->dateStart);
        }

        $this->boardStart = $this->boardStart->setDate($this->boardStart->format('Y'), $this->boardStart->format('m'), '01');

        $this->boardEnd = clone $this->boardStart;
        $this->boardEnd = $this->boardEnd->sub(new DateInterval('P1M'))->add(new DateInterval('P1Y'));
        if (!is_null($data->dateEnd)) {
            $this->boardEnd = DateTime::createFromFormat('Y-m-d H:i:s', $data->dateEnd);
        }

        $this->weekTotal = ((new DateTime())->setISODate($this->boardStart->format('Y'), 53)->format('W') == 53 ? 53 : 52);
    }

    public function __toString()
    {
        return $this->build();
    }

    public function build()
    {
        $results = '';
        foreach ($this->data->cards as $key => $item) {
            $results .= $this->generateRows($item, ($key + 1))->render();
        }

        return $results;
    }

    public function generateRows($data, $phase)
    {
        $rootElement = new Element('div');

        if ($data->tasks) {
            $totalRow = count($data->tasks);

            foreach ($data->tasks as $key => $task) {
                if ($task) {
                    $element = new Element('tr');

                    if ($totalRow == count($data->tasks)) {
                        $node = new Element('td');
                        $node->attributes->addAttribute('rowspan', $totalRow);
                        $node->textContent->prepend($phase);
                        $element->childNodes->push($node);

                        $node = new Element('td');
                        $node->attributes->addAttribute('rowspan', $totalRow);
                        $node->textContent->prepend($task->card->name);
                        $element->childNodes->push($node);
                    }

                    $node = new Element('td');
                    $node->textContent->prepend($task->title);
                    $element->childNodes->push($node);

                    $node = $this->generateMonthTimeline($task->dateStart, $task->dateEnd);
                    $element->childNodes->push($node);

                    $rootElement->childNodes->push($element);

                    --$totalRow;
                }
            }
        }

        return $rootElement;
    }

    public function generateMonthTimeline($dateStart = null, $dateEnd = null)
    {
        if (!is_null($dateStart)) {
            $dateStart = DateTime::createFromFormat('Y-m-d H:i:s', $dateStart);
        }
        if (!is_null($dateEnd)) {
            $dateEnd = DateTime::createFromFormat('Y-m-d H:i:s', $dateEnd);
        }

        $element = new Element('div');

        $boardStart = clone $this->boardStart;
        $weekStart = $boardStart->format('W');

        for ($i = 1; $i <= $this->weekTotal; ++$i) {
            $node = new Element('td');

            if ($dateStart) {
                if ($dateStart->format('Y-m') == $boardStart->format('Y-m') && $dateStart->format('W') == $boardStart->format('W')) {
                    $node->attributes->addAttributeClass(['bg-danger']);
                }
            }

            if ($dateEnd) {
                // if ($dateStart->format('W') >= $boardStart->format('W') && $dateEnd->format('W') <= $boardStart->format('W')) {
                if ($dateStart->format('Y-m') <= $boardStart->format('Y-m')
                    && $dateEnd->format('Y-m') >= $boardStart->format('Y-m')
                    && $dateStart->format('W') <= $boardStart->format('W')
                    && $dateEnd->format('W') >= $boardStart->format('W')
                ) {
                    $node->attributes->addAttributeClass(['bg-danger']);

                    $node->attributes->addAttribute('colspan', ($dateEnd->format('W') - $dateEnd->format('W')));
                }
            }

            $element->childNodes->push($node);

            $boardStart->add(new DateInterval('P1W'));
        }

        return $element;
    }

    public function getHeader()
    {
        $element = '';
        $boardStart = clone $this->boardStart;
        for ($i = 1; $i < 12; ++$i) {
            if ($boardStart->format('m') == 12) {
                $colspan = $this->weekTotal - date('W', strtotime($boardStart->format('Y-m').'-01'));
            } else {
                $colspan = weeks_in_month($boardStart->format('m'), $boardStart->format('Y'));
            }

            $node = new Element('td');
            $node->attributes->addAttribute('colspan', $colspan);
            $node->textContent->prepend($boardStart->format('F'));

            $element .= $node->render();
            $boardStart->add(new DateInterval('P1M'));
        }

        return $element;
    }
}
