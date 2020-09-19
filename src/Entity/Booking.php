<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifeCycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     *
     *@Assert\GreaterThan("today",message = "La date d'arrivé doit être ulterieur à la date d'aujourd'hui",groups={"front"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     *
     *@Assert\GreaterThan(propertyPath = "startDate",message ="La date de sortie doit être superieur à la date d'arrivée")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * calcle du prix avant d'envoyer
     *@ORM\PrePersist
     *@ORM\PreUpdate
     * 
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }
        if(empty($this->amount)){
            $this->amount = $this->ad->getPrice()*$this->getDuration();
        }
    }

    public function getDuration(){
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    } 

    public function isBooKableDate(){
        //1-il faut trouver les date impossible
        $notAvailable = $this->ad->getNotAvailableDays();
        //2-comparer les date choisies avec les date impossible
        $bookingDays = $this->getDays();
        //tableau contenant les jour en chaine de caractères (date)
        $days = array_map(function($day){
            return $day->format('Y-m-d');
        },$bookingDays);
        $notAvailable = array_map(function($day){
            return $day->format('Y-m-d');
        },$notAvailable);

        foreach($days as $day){
            if(array_search($day,$notAvailable) !== false) return false;
        }

        return true;

    }

    /**
     * permet de recuperer un tableau des journée de la reservation
     *
     * @return array
     */
    public function getDays(){
        $resultat = range(
            $this->getStartDate()->getTimestamp(),
            $this->getEndDate()->getTimestamp(),
            24*60*60
        );
        $days = array_map(function($daytimestamp){
            return new \DateTime(date('Y-m-d',$daytimestamp));
        },$resultat);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
