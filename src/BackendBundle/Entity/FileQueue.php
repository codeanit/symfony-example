<?php

namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileQueue
 *
 * @ORM\Table(name="file_queue")
 * @ORM\Entity
 */
class FileQueue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="service_id", type="integer", nullable=true)
     */
    private $serviceId;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=45, nullable=true)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=45, nullable=true)
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="service_name", type="string", length=45, nullable=true)
     */
    private $serviceName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=true)
     */
    private $creationDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_executed", type="integer", nullable=true)
     */
    private $isExecuted;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set serviceId
     *
     * @param integer $serviceId
     * @return FileQueue
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get serviceId
     *
     * @return integer 
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return FileQueue
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return FileQueue
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set serviceName
     *
     * @param string $serviceName
     * @return FileQueue
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get serviceName
     *
     * @return string 
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return FileQueue
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set isExecuted
     *
     * @param integer $isExecuted
     * @return FileQueue
     */
    public function setIsExecuted($isExecuted)
    {
        $this->isExecuted = $isExecuted;

        return $this;
    }

    /**
     * Get isExecuted
     *
     * @return integer 
     */
    public function getIsExecuted()
    {
        return $this->isExecuted;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
