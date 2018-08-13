<?php

namespace App\Entity;

use App\Entity\Feature\Translatable;
use App\I18n\Translations;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="addresses")
 */
class Address extends EntityBase implements Translatable
{
    use Feature\TranslatableTrait;

    /**
     * @ORM\Column(type="string")
     */
    private $zipcode;

    /**
     * @ORM\Column(type="integer")
     */
    private $box_number;

    /**
     * @ORM\Column(type="geography", options={"geometry_type"="POINT", "srid"=4326})
     */
    private $coordinates;

    private $parsedCoordinates;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="AddressData", mappedBy="entity", orphanRemoval=true, cascade={"persist", "remove"}, fetch="EXTRA_LAZY", indexBy="langcode")
     */
    private $translations;

    /**
     * @ORM\OneToOne(targetEntity="Library")
     */
    private $library;

    public function isNull() : bool
    {
        if (!empty($this->street)) {
            return false;
        }

        if (!empty($this->zipcode)) {
            return false;
        }

        if (!empty($this->box_number)) {
            return false;
        }

        if ($this->city) {
            return false;
        }

        return true;
    }

    public function getStreet() : ?string
    {
        return $this->translations[$this->langcode]->getStreet();
    }

    public function setStreet(?string $street) : void
    {
        $this->translations[$this->langcode]->setStreet($street);
    }

    public function getArea() : ?string
    {
        return $this->translations[$this->langcode]->getArea();
    }

    public function setArea(?string $area) : void
    {
        $this->translations[$this->langcode]->setArea($area);
    }

    public function getZipcode() : ?string
    {
        return $this->zipcode;
    }

    public function setZipcode($zipcode) : void
    {
        $this->zipcode = $zipcode;
    }

    public function getBoxNumber() : ?int
    {
        return $this->box_number;
    }

    public function setBoxNumber($number) : void
    {
        $this->box_number = $number;
    }

    public function getLibrary() : Library
    {
        return $this->library;
    }

    public function setLibrary(Library $organisation) : void
    {
        $this->library = $organisation;
    }

    public function getCity() : ?City
    {
        return $this->city;
    }

    public function setCity(City $city) : void
    {
        $this->city = $city;
    }

    public function getCoordinates() : ?string
    {
        if (!$this->parsedCoordinates && $this->coordinates) {
            preg_match('/POINT\((\d+|\d+\.\d+) (\d+|\d+\.\d+)\)/', $this->coordinates, $matches);
            list($_, $lat, $lon) = $matches;
            $this->parsedCoordinates = sprintf('%s, %s', $lat, $lon);
        }
        return $this->parsedCoordinates;
    }

    public function setCoordinates(string $coordinates) : void
    {
        $this->parsedCoordinates = null;

        if (!empty($coordinates)) {
            $old_coords = $this->coordinates;
            list($lat, $lon) = explode(',', $coordinates);

                // NOTE: Six decimals are enough for precision of one meter.
                $this->coordinates = sprintf('SRID=4326;POINT(%2.6F %3.6F)', $lat, $lon);
            } else {
                $this->coordinates = null;
            }
        }
}