<?php

  namespace App\Entity;

  use DateTime;
  use DateTimeImmutable;
  use Doctrine\ORM\Mapping as ORM;
  use Symfony\Component\HttpFoundation\File\File;
  use Symfony\Component\HttpFoundation\File\UploadedFile;
  use Vich\UploaderBundle\Mapping\Annotation as Vich;

  /**
   * @ORM\Entity(repositoryClass="App\Repository\ImagesRepository")
   * @Vich\Uploadable
   * @ORM\HasLifecycleCallbacks()
   */
  class Images
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    // ... other fields


    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $imageName;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="images", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $imageFile;


    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $imageSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $updatedAt;

    /**
     * permet de mettre en place la date de création
     *
     * @ORM\PrePersist()
     *
     * @return void
     */
    public function prepersist() {
      if (empty($this->updatedAt)) {
        $this->updatedAt = new \DateTime();
      }
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
      return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return Images
     * @throws \Exception
     */
    public function setUpdatedAt(DateTime $updatedAt): Images
    {
      $this->updatedAt = new DateTimeImmutable();
      return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Recette")
     */
    private $recette;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $imageFile
     * @throws \Exception
     */
    public function setImageFile(?File $imageFile = null)
    {
      $this->imageFile = $imageFile;

      if (null !== $imageFile) {
        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        $this->updatedAt = new DateTimeImmutable();
      }
    }

    public function getImageFile(): ?File
    {
      return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
      $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
      return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
      $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
      return $this->imageSize;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): self
    {
        $this->recette = $recette;

        return $this;
    }
  }