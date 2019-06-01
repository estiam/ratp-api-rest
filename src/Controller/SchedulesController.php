<?php

declare(strict_types=1);

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

use Swagger\Annotations as SWG;

class SchedulesController extends AppController
{
    /**
     * @SWG\Get(
     *     produces={"application/json", "application/xml"},
     *     description="Get schedules at a specific station on a specific line."
     * )
     * @SWG\Parameter(
     *     name="type",
     *     in="path",
     *     type="string",
     *     description="The type of transport (metros, rers, tramways, bus or noctiliens)",
     *     enum={"metros", "rers", "tramways", "bus", "noctiliens"}
     * )
     * @SWG\Parameter(
     *     name="code",
     *     in="path",
     *     type="string",
     *     description="The code of transport line (e.g. 8)"
     * )
     * @SWG\Parameter(
     *     name="station",
     *     in="path",
     *     type="string",
     *     description="Slug of the station name (e.g. bonne+nouvelle)"
     * )
     * @SWG\Parameter(
     *     name="way",
     *     in="path",
     *     type="string",
     *     description="Way on the line",
     *     enum={"A", "R", "A+R"}
     * )
     * @SWG\Tag(
     *   name="Schedules",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="OK"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request"
     * )
     *
     * @Rest\View()
     * @Rest\Get("/schedules/{type}/{code}/{station}/{way}")
     *
     * @param string $type
     * @param string $code
     * @param string $station
     * @param string $way
     *
     * @return View
     */
    public function stations(string $type, string $code, string $station, string $way): View
    {
        /** @todo */
    }
}
