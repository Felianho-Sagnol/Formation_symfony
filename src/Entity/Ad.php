<?php

namespace App\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use App\Repository\AdRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AdRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 * fields = {"title"},
 * message="Une autre annonce possede le meme titre"
 * )
 */
class Ad
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10,max=255,minMessage="il faut au-moins 10 caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="ad", orphanRemoval=true,cascade={"persist"})
     * @Assert\Valid()
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="ad")
     */
    private $startDate;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="ad")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->startDate = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    /**
     * Undocumented function
     *@ORM\PrePersist
     *@ORM\PreUpdate
     * @return void
     */
    public function initializeSlug(){
        $slugify = new Slugify();
        if(empty($this->slug)){
            $this->slug = $slugify->slugify($this->title);
        }
    }

    /**
     * cette fonction permt d'o btenir un tanbleau des jours qui ne sont pas disponibles pour cette annonce
     *
     * @return array
     */
    public function getNotAvailableDays(){
        $notAvailableDays = [];

        foreach($this->bookings as $booking){
            //calculer les jours qui se trouve entre la date d'arivé et de sortie
            $resultat = range(
                $booking->getStartDate()->getTimestamp(),
                $booking->getEndDate()->getTimestamp(),
                24*60*60
            );

            $days = array_map(function($daytimestamp){
                return new \DateTime(date('Y-m-d',$daytimestamp));
            },$resultat);

            $notAvailableDays = array_merge($notAvailableDays,$days);
        }

        return $notAvailableDays;
    }

    /**
     * permet de les commentaire d'un user par rapport a une annonce
     *
     * @param User $author
     * 
     */
    public function getCommentFromAuthor(User $author){
        foreach($this->comments as $comment){
            if($comment->getAuthor() === $author) return $comment;
        }
        return null;
    }

    /**
     * permet d'obtenir la note moyenne d'une annnonce
     */
    public function getAvgRating(){
        //1-calculer la somme des rating 
        $sum = array_reduce($this->comments->toArray(),function($total,$comment){
            return $total + $comment->getRating();
        },0);
        //2-diviser par le nombre total d'étoile
        if(count($this->comments) > 0) return $sum/count($this->comments);
        return 0; 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getStartDate(): Collection
    {
        return $this->startDate;
    }

    public function addStartDate(Booking $startDate): self
    {
        if (!$this->startDate->contains($startDate)) {
            $this->startDate[] = $startDate;
            $startDate->setAd($this);
        }

        return $this;
    }

    public function removeStartDate(Booking $startDate): self
    {
        if ($this->startDate->contains($startDate)) {
            $this->startDate->removeElement($startDate);
            // set the owning side to null (unless already changed)
            if ($startDate->getAd() === $this) {
                $startDate->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }
}
