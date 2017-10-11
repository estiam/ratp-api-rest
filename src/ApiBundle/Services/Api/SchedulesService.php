<?php
namespace ApiBundle\Services\Api;

use ApiBundle\Helper\NamesHelper;
use ApiBundle\Helper\NetworkHelper;
use FOS\RestBundle\Exception\InvalidParameterException;
use Ratp\Api;
use Ratp\Direction;
use Ratp\GeoPoint;
use Ratp\Line;
use Ratp\MissionsNext;
use Ratp\Station;

class SchedulesService extends ApiService implements ApiDataInterface
{
    /**
     * @param $method
     * @param array $parameters
     * @return mixed
     */
    public function get($method, $parameters = [])
    {
        return parent::getData($method, $parameters);
    }

    /**
     * @param $parameters
     * @return array|null
     */
    protected function getSchedules($parameters)
    {
        $schedules = [];

        $typesAllowed = [
            'rers',
            'metros',
            'tramways',
            'bus',
            'noctiliens'
        ];

        if (!in_array($parameters['type'], $typesAllowed)) {
            throw new InvalidParameterException(sprintf('Unknown type : %s', $parameters['type']));
        }

        $prefix = NetworkHelper::typeSlugSchedules($parameters['type']);

        $line = new Line();

        // prefix line name
        if (in_array($parameters['type'], ['bus', 'metros', 'tramways', 'noctiliens'])) {
            $line->setId($prefix . $parameters['code']);
        } elseif (in_array($parameters['type'], ['rers'])) {
            $line->setId($prefix . strtoupper($parameters['code']));
        }

        // lines with several destinations
        if ($parameters['id'] != '') {
            $line->setId($parameters['id']);
        }

        $station = new Station();
        $station->setLine($line);
        $station->setName(NamesHelper::clean($parameters['station']));

        // 2017-10-11 add "all ways" option
        $way = $parameters['way'] == 'A+R' ? '*' : $parameters['way'];

        $direction = new Direction();
        $direction->setSens($way);

        $mission = new MissionsNext($station, $direction);

        $api    = new Api();
        $return = $api->getMissionsNext($mission)->getReturn();

        $this->isAmbiguous($return);

        if ($return->getMissions()) {
            foreach ($return->getMissions() as $mission) {

                // 2017-10-11 fix destination name
                if (isset($mission->stations[1]) && ($mission->stations[1]->getGeoPointA() instanceof GeoPoint)) {
                    $destination = $mission->stations[1]->getGeoPointA()->getName();
                } else if (isset($mission->stations[1])) {
                    $destination = $mission->stations[1]->getName();
                } else {
                    $destination = $return->getArgumentDirection()->getName();
                }

                $schedules[] = [
                    'code'        => $mission->code,
                    'message'     => $mission->stationsMessages[0],
                    'destination' => $destination,
                ];
            }
        } else {
            $schedules[] = [
                'code'        => 'Schedules unavailable',
                'message'     => 'Schedules unavailable',
                'destination' => $return->getArgumentDirection()->getName(),
            ];
        }

        return [
            'schedules' => $schedules
        ];
    }
}
