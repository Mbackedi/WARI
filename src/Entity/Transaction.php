<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Envoyeur;
use App\Entity\Beneficiaire;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @ORM\Column(type="bigint")
     */
    private $montant;

    /**
     * @ORM\Column(type="bigint")
     */
    private $frais;

    /**
     * @ORM\Column(type="bigint")
     */
    private $total;

    /**
     * @ORM\Column(type="bigint")
     */
    private $commissionsup;

    /**
     * @ORM\Column(type="bigint")
     */
    private $commissionparte;

    /**
     * @ORM\Column(type="bigint")
     */
    private $commissionetat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datedenvoie;

    /**
     * @ORM\Column(type="datetime" ,nullable=true)
     */
    private $dateretrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typedoperation;


    /**
     * @ORM\Column(type="bigint")
     */
    private $numerotransacion;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $caissier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomExp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomExp;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephonExp;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $adresseExp;

    /**
     * @ORM\Column(type="bigint")
     */
    private $numeropieceEXp;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $typepieceExp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomBen;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomBen;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephonBen;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $adresseBen;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $numeropieceBen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typepieceBen;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactionsBen")
     */
    private $caissierBen;



    public function getId(): ?int
    {
        return $this->id;
    }



    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getCommissionsup(): ?int
    {
        return $this->commissionsup;
    }

    public function setCommissionsup(int $commissionsup): self
    {
        $this->commissionsup = $commissionsup;

        return $this;
    }

    public function getCommissionparte(): ?int
    {
        return $this->commissionparte;
    }

    public function setCommissionparte(int $commissionparte): self
    {
        $this->commissionparte = $commissionparte;

        return $this;
    }

    public function getCommissionetat(): ?int
    {
        return $this->commissionetat;
    }

    public function setCommissionetat(int $commissionetat): self
    {
        $this->commissionetat = $commissionetat;

        return $this;
    }

    public function getDatedenvoie(): ?\DateTimeInterface
    {
        return $this->datedenvoie;
    }

    public function setDatedenvoie(\DateTimeInterface $datedenvoie): self
    {
        $this->datedenvoie = $datedenvoie;

        return $this;
    }

    public function getDateretrait(): ?\DateTimeInterface
    {
        return $this->dateretrait;
    }

    public function setDateretrait(\DateTimeInterface $dateretrait): self
    {
        $this->dateretrait = $dateretrait;

        return $this;
    }

    public function getTypedoperation(): ?string
    {
        return $this->typedoperation;
    }

    public function setTypedoperation(string $typedoperation): self
    {
        $this->typedoperation = $typedoperation;

        return $this;
    }

    public function getEnvoyeur(): ?Envoyeur
    {
        return $this->envoyeur;
    }

    public function setEnvoyeur(?Envoyeur $envoyeur): self
    {
        $this->envoyeur = $envoyeur;

        return $this;
    }



    public function getNumerotransacion(): ?int
    {
        return $this->numerotransacion;
    }

    public function setNumerotransacion(int $numerotransacion): self
    {
        $this->numerotransacion = $numerotransacion;

        return $this;
    }

    public function getBeneficiaire(): ?Beneficiaire
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(?Beneficiaire $beneficiaire): self
    {
        $this->beneficiaire = $beneficiaire;

        return $this;
    }

    public function getCaissier(): ?User
    {
        return $this->caissier;
    }

    public function setCaissier(?User $caissier): self
    {
        $this->caissier = $caissier;

        return $this;
    }

    public function getNomExp(): ?string
    {
        return $this->nomExp;
    }

    public function setNomExp(string $nomExp): self
    {
        $this->nomExp = $nomExp;

        return $this;
    }

    public function getPrenomExp(): ?string
    {
        return $this->prenomExp;
    }

    public function setPrenomExp(string $prenomExp): self
    {
        $this->prenomExp = $prenomExp;

        return $this;
    }

    public function getTelephonExp(): ?int
    {
        return $this->telephonExp;
    }

    public function setTelephonExp(int $telephonExp): self
    {
        $this->telephonExp = $telephonExp;

        return $this;
    }

    public function getAdresseExp(): ?string
    {
        return $this->adresseExp;
    }

    public function setAdresseExp(string $adresseExp): self
    {
        $this->adresseExp = $adresseExp;

        return $this;
    }

    public function getNumeropieceEXp(): ?int
    {
        return $this->numeropieceEXp;
    }

    public function setNumeropieceEXp(int $numeropieceEXp): self
    {
        $this->numeropieceEXp = $numeropieceEXp;

        return $this;
    }

    public function getTypepieceExp(): ?string
    {
        return $this->typepieceExp;
    }

    public function setTypepieceExp(string $typepieceExp): self
    {
        $this->typepieceExp = $typepieceExp;

        return $this;
    }

    public function getNomBen(): ?string
    {
        return $this->nomBen;
    }

    public function setNomBen(string $nomBen): self
    {
        $this->nomBen = $nomBen;

        return $this;
    }

    public function getPrenomBen(): ?string
    {
        return $this->prenomBen;
    }

    public function setPrenomBen(string $prenomBen): self
    {
        $this->prenomBen = $prenomBen;

        return $this;
    }

    public function getTelephonBen(): ?int
    {
        return $this->telephonBen;
    }

    public function setTelephonBen(int $telephonBen): self
    {
        $this->telephonBen = $telephonBen;

        return $this;
    }

    public function getAdresseBen(): ?string
    {
        return $this->adresseBen;
    }

    public function setAdresseBen(string $adresseBen): self
    {
        $this->adresseBen = $adresseBen;

        return $this;
    }

    public function getNumeropieceBen(): ?int
    {
        return $this->numeropieceBen;
    }

    public function setNumeropieceBen(int $numeropieceBen): self
    {
        $this->numeropieceBen = $numeropieceBen;

        return $this;
    }

    public function getTypepieceBen(): ?string
    {
        return $this->typepieceBen;
    }

    public function setTypepieceBen(?string $typepieceBen): self
    {
        $this->typepieceBen = $typepieceBen;

        return $this;
    }

    public function getCaissierBen(): ?User
    {
        return $this->caissierBen;
    }

    public function setCaissierBen(?User $caissierBen): self
    {
        $this->caissierBen = $caissierBen;

        return $this;
    }
}
