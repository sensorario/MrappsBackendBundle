<?php

namespace Mrapps\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Mrapps\BackendBundle\Repository\PermissionRepository")
 * @ORM\Table(name="mrapps_backend_permissions")
 */
class Permission extends Base
{
    /**
     * @ORM\Column(name="object", type="string", length=255)
     * @Assert\Length(max=255)
     */
    protected $object;

    /**
     * @ORM\Column(name="role", type="string", length=255)
     * @Assert\Length(max=255)
     */
    protected $role;

    /**
     * @ORM\Column(name="can_view", type="boolean", nullable=true)
     */
    protected $canView;

    /**
     * @ORM\Column(name="can_create", type="boolean", nullable=true)
     */
    protected $canCreate;

    /**
     * @ORM\Column(name="can_edit", type="boolean", nullable=true)
     */
    protected $canEdit;

    /**
     * @ORM\Column(name="can_delete", type="boolean", nullable=true)
     */
    protected $canDelete;

    /**
     * Set object
     *
     * @param string $object
     *
     * @return Permission
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Permission
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set canView
     *
     * @param boolean $canView
     *
     * @return Permission
     */
    public function setCanView($canView)
    {
        $this->canView = $canView;

        return $this;
    }

    /**
     * Get canView
     *
     * @return boolean
     */
    public function getCanView()
    {
        return $this->canView;
    }

    /**
     * Set canCreate
     *
     * @param boolean $canCreate
     *
     * @return Permission
     */
    public function setCanCreate($canCreate)
    {
        $this->canCreate = $canCreate;

        return $this;
    }

    /**
     * Get canCreate
     *
     * @return boolean
     */
    public function getCanCreate()
    {
        return $this->canCreate;
    }

    /**
     * Set canEdit
     *
     * @param boolean $canEdit
     *
     * @return Permission
     */
    public function setCanEdit($canEdit)
    {
        $this->canEdit = $canEdit;

        return $this;
    }

    /**
     * Get canEdit
     *
     * @return boolean
     */
    public function getCanEdit()
    {
        return $this->canEdit;
    }

    /**
     * Set canDelete
     *
     * @param boolean $canDelete
     *
     * @return Permission
     */
    public function setCanDelete($canDelete)
    {
        $this->canDelete = $canDelete;

        return $this;
    }

    /**
     * Get canDelete
     *
     * @return boolean
     */
    public function getCanDelete()
    {
        return $this->canDelete;
    }
}
