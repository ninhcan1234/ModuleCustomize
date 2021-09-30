<?php

namespace AHT\Post\Api\Data;

interface PostInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS = 'status';
    const IMAGE = 'image';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get identities
     *
     * @return string
     */
    public function getIdentities();

    /**
     * Set ID
     *
     * @param int $id
     * @return PostInterface
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return PostInterface
     */
    public function setName($name);

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();


    /**
     * Set description
     *
     * @param string $description
     * @return PostInterface
     */
    public function setDescription($description);

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return PostInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return PostInterface
     */
    public function setUpdatedAt($updatedAt);

        /**
     * get Image
     *
     * @return string
     */
    public function getImage();

       /**
     * Set image
     *
     * @param string $image
     * @return PostInterface
     */
    public function setImage($image);

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getStatus();

    /**
     * Set is active
     *
     * @param bool|int $status
     * @return PostInterface
     */
    public function setStatus($status);
}
