<?php

namespace App\Entity;

use App\Repository\FeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeatureRepository::class)]
class Feature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, SubscriptionPlan>
     */
    #[ORM\ManyToMany(targetEntity: SubscriptionPlan::class, mappedBy: 'features')]
    private Collection $subscriptionPlans;

    public function __construct()
    {
        $this->subscriptionPlans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, SubscriptionPlan>
     */
    public function getSubscriptionPlans(): Collection
    {
        return $this->subscriptionPlans;
    }

    public function addSubscriptionPlan(SubscriptionPlan $subscriptionPlan): static
    {
        if (!$this->subscriptionPlans->contains($subscriptionPlan)) {
            $this->subscriptionPlans->add($subscriptionPlan);
            $subscriptionPlan->addFeature($this);
        }

        return $this;
    }

    public function removeSubscriptionPlan(SubscriptionPlan $subscriptionPlan): static
    {
        if ($this->subscriptionPlans->removeElement($subscriptionPlan)) {
            $subscriptionPlan->removeFeature($this);
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->name ?? 'Feature';
    }
}
