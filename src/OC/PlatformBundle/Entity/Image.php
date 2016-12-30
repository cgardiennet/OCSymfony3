<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    private $oldFilePath;


    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/img';
    }

    protected function getRootDir()
    {
        return realpath(__DIR__ . '/../../../../web/');
    }

    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return $this->getRootDir() . '/' . $this->getUploadDir();
    }

    public function getWebPath()
    {
        return $this->getUploadDir() . '/' . $this->getFileName();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {

        // Si jamais il n'y a pas de fichier (champ facultatif), et que l'url n'est pas nulle
        // alors on prépare la suppression de l'ancien fichier pour prendre en compte l'url
        if (null === $this->file) {
            if (!is_null($this->url)) {
                $this->oldFilePath = $this->getUploadRootDir() . '/' . $this->getFileName();
                $this->extension = null;
            }
            return;
        }

        $this->extension = $this->file->guessClientExtension();
        $this->url = null;

        // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $this->alt = $this->file->getClientOriginalName();

        return $this;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {

        // Si jamais il n'y a pas de fichier (champ facultatif), et que l'url n'est pas nulle
        // après le succès du persist on supprime effectivement l'ancien fichier
        if (null === $this->file) {
            if (!is_null($this->url)) {
                $this->removeUpload();
            }

            return;
        }

        // Si on avait un ancien fichier, on le supprime
        if (null !== $this->oldFilePath) {
            $this->removeUpload();
        }

        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
            $this->getUploadRootDir(), // Le répertoire de destination
            $this->getFileName()   // Le nom du fichier à créer
        );

        return $this;
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $this->oldFilePath = $this->getUploadRootDir() . '/' . $this->getFileName();

        return $this;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
        if (file_exists($this->oldFilePath)) {
            // On supprime le fichier
            unlink($this->oldFilePath);
        }

        return $this;
    }


    /**
     * Getters/Setters
     */

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Get file
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file)
    {

        $this->file = $file;

        // On vérifie si on avait déjà un fichier uploadé pour cette entité
        if (null !== $this->extension) {
            // On sauvegarde l'extension du fichier pour le supprimer plus tard
            $this->oldFilePath = $this->getUploadRootDir() . '/' . $this->getFileName();

            // On réinitialise les valeurs des attributs extension et alt
            $this->extension = null;
            $this->alt = null;
            $this->url = null;
        }
    }

    public function getFileName()
    {
        return $this->id . '.' . $this->extension;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Image
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
