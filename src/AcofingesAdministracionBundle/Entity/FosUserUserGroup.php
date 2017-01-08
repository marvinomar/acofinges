<?php

namespace AcofingesAdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FosUserUserGroup
 *
 * @ORM\Table(name="fos_user_user_group", indexes={@ORM\Index(name="IDX_B3C77447FE54D947", columns={"group_id"}), @ORM\Index(name="IDX_B3C77447A76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class FosUserUserGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="fos_user_user_group_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \FosUserGroup
     *
     * @ORM\ManyToOne(targetEntity="FosUserGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    private $group;

    /**
     * @var \FosUserUser
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set group
     *
     * @param \AcofingesAdministracionBundle\Entity\FosUserGroup $group
     *
     * @return FosUserUserGroup
     */
    public function setGroup(\AcofingesAdministracionBundle\Entity\FosUserGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \AcofingesAdministracionBundle\Entity\FosUserGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set user
     *
     * @param \AcofingesAdministracionBundle\Entity\FosUserUser $user
     *
     * @return FosUserUserGroup
     */
    public function setUser(\AcofingesAdministracionBundle\Entity\FosUserUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AcofingesAdministracionBundle\Entity\FosUserUser
     */
    public function getUser()
    {
        return $this->user;
    }
}
