<?php

namespace Waddle\Parsers;

use Exception;
use SimpleXMLElement;
use Waddle\Activity;
use Waddle\Lap;
use Waddle\Parser;
use Waddle\TrackPoint;

class TCXParser extends Parser {
    const NS_ACTIVITY_EXTENSION_V2 = 'http://www.garmin.com/xmlschemas/ActivityExtension/v2';

    /** @var string */
    private $nameNSActivityExtensionV2;

    /**
     * Parse the TCX file
     * @param string $pathname
     * @return Activity
     * @throws Exception
     */
    public function parse($pathname) {
        // Check that the file exists
        $this->checkForFile($pathname);

        // Create a new activity instance
        $activity = new Activity();

        // Load the XML in the TCX file
        $data = simplexml_load_file($pathname);
        if (!isset($data->Activities->Activity)) {
            throw new Exception('Unable to find valid activity in file contents');
        }
        $this->detectsNamespace($data);

        // Parse the first activity
        $activityNode = $data->Activities->Activity[0];
        $activity->setStartTime(new \DateTime((string) $activityNode->Id));
        $activity->setType((string) $activityNode['Sport']);

        // Now parse the laps
        // There should only be 1 lap, but they are stored in an array just in case this ever changes
        foreach ($activityNode->Lap as $lapNode) {
            $activity->addLap($this->parseLap($lapNode));
        }

        // Finally return the activity object
        return $activity;
    }

    /**
     *
     * @var SimpleXMLElement $xml
     */
    private function detectsNamespace(SimpleXMLElement $xml) {
        $this->nameNSActivityExtensionV2 = null;

        $namespaces = $xml->getDocNamespaces(true);
        foreach ($namespaces as $name => $ns) {
            if ($ns === self::NS_ACTIVITY_EXTENSION_V2) {
                $this->nameNSActivityExtensionV2 = $name;
            }
        }
    }

    /**
     * Parse the lap XML
     * @param SimpleXMLElement $lapNode
     * @return Lap
     * @throws Exception
     */
    protected function parseLap($lapNode) {
        $lap = new Lap();
        $lap->setTotalTime((float) $lapNode->TotalTimeSeconds);
        $lap->setTotalDistance((float) $lapNode->DistanceMeters);
        $lap->setMaxSpeed((float) $lapNode->MaximumSpeed);
        $lap->setTotalCalories((float) $lapNode->Calories);

        // Loop through the track points
        foreach ($lapNode->Track->Trackpoint as $trackPointNode) {
            $lap->addTrackPoint($this->parseTrackPoint($trackPointNode));
        }

        return $lap;
    }

    /**
     * Parse the XML of a track point
     * @param SimpleXMLElement $trackPointNode
     * @return TrackPoint
     * @throws Exception
     */
    protected function parseTrackPoint($trackPointNode) {
        $point = new TrackPoint();
        $point->setTime(new \DateTime((string) $trackPointNode->Time));
        $point->setPosition([
            'lat' => (float) $trackPointNode->Position->LatitudeDegrees,
            'lon' => (float) $trackPointNode->Position->LongitudeDegrees
        ]);
        $point->setAltitude((float) $trackPointNode->AltitudeMeters);
        $point->setDistance((float) $trackPointNode->DistanceMeters);

        if (isset($trackPointNode->HeartRateBpm)) {
            $point->setHeartRate((float) $trackPointNode->HeartRateBpm->Value);
        }

        if (isset($trackPointNode->Cadence)) {
            $point->setCadence((float) $trackPointNode->Cadence);
        }

        if ($this->nameNSActivityExtensionV2) {
            if (isset($trackPointNode->Extensions->TPX->Speed)) {
                $point->setSpeed((float) $trackPointNode->Extensions->TPX->Speed);
            }

            if (isset($trackPointNode->Extensions->TPX->Watts)) {
                $point->setWatts((float) $trackPointNode->Extensions->TPX->Watts);
            }
        }

        return $point;
    }
}
