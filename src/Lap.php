<?php

namespace Waddle;

class Lap {
    /** @var int Seconds */
    protected $totalTime;
    /** @var float Meters */
    protected $totalDistance;
    /** @var float Meters per seconds */
    protected $maxSpeed;
    /** @var float */
    protected $totalCalories;
    /** @var TrackPoint[] */
    protected $trackPoints = [];

    /**
     * Get the total lap time
     * @return int
     */
    public function getTotalTime() {
        return $this->totalTime;
    }

    /**
     * Get the total lap distance
     * @return float
     */
    public function getTotalDistance() {
        return $this->totalDistance;
    }

    /**
     * Get the max speed achieved during the lap
     * @return float
     */
    public function getMaxSpeed() {
        return $this->maxSpeed;
    }

    /**
     * Get the calories burnt during the lap
     * @return float
     */
    public function getTotalCalories() {
        return $this->totalCalories;
    }

    /**
     * Get the array of track points
     * @return TrackPoint[]
     */
    public function getTrackPoints() {
        return $this->trackPoints;
    }

    /**
     * Get a specific track point, by its number
     * @param int $num
     * @return TrackPoint|bool
     */
    public function getTrackPoint($num) {
        return array_key_exists($num, $this->trackPoints) ? $this->trackPoints[$num] : false;
    }

    /**
     * @param array $trackPoints
     * @return $this
     */
    public function setTrackPoints(array $trackPoints) {
        $this->trackPoints = $trackPoints;
        return $this;
    }

    /**
     * Set the total lap time (seconds)
     * @param int $val
     * @return $this
     */
    public function setTotalTime($val) {
        $this->totalTime = $val;
        return $this;
    }

    /**
     * Set the total lap distance (metres)
     * @param float $val
     * @return $this
     */
    public function setTotalDistance($val) {
        $this->totalDistance = $val;
        return $this;
    }

    /**
     * Set the max lap speed (metres per second)
     * @param float $val
     * @return $this
     */
    public function setMaxSpeed($val) {
        $this->maxSpeed = $val;
        return $this;
    }

    /**
     * Set the total calories burnt
     * @param int $val
     * @return $this
     */
    public function setTotalCalories($val) {
        $this->totalCalories = $val;
        return $this;
    }

    /**
     * Add a track point to the lap
     * @param TrackPoint $point
     * @return TrackPoint
     */
    public function addTrackPoint(TrackPoint $point) {
        $this->trackPoints[] = $point;
        return $point;
    }
}
