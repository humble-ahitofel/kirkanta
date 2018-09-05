<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PhoneNumber extends ContactInfo
{
    use PhoneNumberTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Library", inversedBy="phone_numbers")
     */
    protected $parent;

    /**
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="phone_numbers")
     */
    protected $department;

    public function setDepartment(?Department $department) : void
    {
        $this->department = $department;

        if ($department) {
            $this->setLibrary($department->getLibrary());
        }
    }

    public function getDepartment() : ?Department
    {
        return $this->department;
    }

    public function setLibrary(Library $library) : void
    {
        $this->setParent($library);
    }

    public function getLibrary() : Library
    {
        return $this->getParent();
    }
}
