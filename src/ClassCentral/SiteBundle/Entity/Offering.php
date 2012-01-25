<?php

namespace ClassCentral\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ClassCentral\SiteBundle\Entity\Offering
 */
class Offering {

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var date $startDate
     */
    private $startDate;

    /**
     * @var date $endDate
     */
    private $endDate;

    /**
     * @var boolean $exactDatesKnow
     */
    private $exactDatesKnow;

    /**
     * @var datetime $created
     */
    private $created;

    /**
     * @var datetime $modified
     */
    private $modified;

    /**
     * @var ClassCentral\SiteBundle\Entity\Course
     */
    private $course;

    /**
     * @var ClassCentral\SiteBundle\Entity\Initiative
     */
    private $initiative;

    /**
     *
     * @var string $url
     */
    private $url;

    /**
     * If this field is null then the course name will be displayed
     * @var string $name
     */
    private $name;

    /**
     *
     * @var string $videoIntro
     */
    private $videoIntro;

    /**
     * 
     * @var integer length
     */
    private $length;

    public function __construct() {
        $this->instructors = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set startDate
     *
     * @param date $startDate
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    /**
     * Get startDate
     *
     * @return date 
     */
    public function getStartDate() {
        return $this->startDate;
    }

    public function getStartTimestamp() {
        return strval($this->startDate->getTimestamp());
    }

    /**
     * Set endDate
     *
     * @param date $endDate
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    /**
     * Get endDate
     *
     * @return date 
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * Set exactDatesKnow
     *
     * @param boolean $exactDatesKnow
     */
    public function setExactDatesKnow($exactDatesKnow) {
        $this->exactDatesKnow = $exactDatesKnow;
    }

    /**
     * Get exactDatesKnow
     *
     * @return boolean 
     */
    public function getExactDatesKnow() {
        return $this->exactDatesKnow;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created) {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param datetime $modified
     */
    public function setModified($modified) {
        $this->modified = $modified;
    }

    /**
     * Get modified
     *
     * @return datetime 
     */
    public function getModified() {
        return $this->modified;
    }

    /**
     * Set course
     *
     * @param ClassCentral\SiteBundle\Entity\Course $course
     */
    public function setCourse(\ClassCentral\SiteBundle\Entity\Course $course) {
        $this->course = $course;
    }

    /**
     * Get course
     *
     * @return ClassCentral\SiteBundle\Entity\Course 
     */
    public function getCourse() {
        return $this->course;
    }

    /**
     * Set initiative
     * 
     * @param ClassCEntral\SiteBundle\Entitiy\Offering $offering
     */
    public function setInitiative(\ClassCentral\SiteBundle\Entitiy\Initiative $initiative) {
        $this->initiative = $initiative;
    }

    /**
     * Get Initative
     * 
     * @return ClassCentral\SiteBundle\Entity\Initiative
     */
    public function getInitiative() {
        return $this->initiative;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        if (empty($this->name)) {
            $this->course->getName();
        }
        
        return $this->name;
    }

    public function getFormattedStartDate() {
        if ($this->getExactDatesKnow()) {
            $format = 'jS M, Y';
        } else {
            $format = 'M, Y';
        }

        return $this->getStartDate()->format($format);
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getVideoIntro() {
        return $this->videoIntro;
    }

    public function setVideoIntro($videoIntro) {
        $this->videoIntro = $videoIntro;
    }

    public function getLength() {
        return $this->length;
    }

    public function setLength($length) {
        $this->length = $length;
    }

}