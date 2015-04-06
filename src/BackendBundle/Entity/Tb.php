<?php

namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tb
 *
 * @ORM\Table(name="TB")
 * @ORM\Entity
 */
class Tb
{
    /**
     * @var string
     *
     * @ORM\Column(name="transaction_key", type="string", length=45, nullable=false)
     */
    private $transactionKey;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_source", type="string", length=10, nullable=false)
     */
    private $transactionSource;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_service", type="string", length=20, nullable=false)
     */
    private $transactionService;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=25, nullable=false)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_code", type="string", length=255, nullable=false)
     */
    private $transactionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", length=10, nullable=false)
     */
    private $transactionType;

    /**
     * @var integer
     *
     * @ORM\Column(name="receiver_id_number", type="integer", nullable=true)
     */
    private $receiverIdNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_id_type", type="string", length=45, nullable=true)
     */
    private $receiverIdType;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_id_issued_country", type="string", length=45, nullable=true)
     */
    private $receiverIdIssuedCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_first_name", type="string", length=45, nullable=true)
     */
    private $receiverFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_middle_name", type="string", length=45, nullable=true)
     */
    private $receiverMiddleName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_last_name", type="string", length=45, nullable=true)
     */
    private $receiverLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_email", type="string", length=45, nullable=true)
     */
    private $receiverEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_account_type", type="string", length=45, nullable=true)
     */
    private $receiverAccountType;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_currency", type="string", length=45, nullable=true)
     */
    private $receiverCurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_city", type="string", length=45, nullable=true)
     */
    private $receiverCity;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_country", type="string", length=45, nullable=true)
     */
    private $receiverCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_state", type="string", length=45, nullable=true)
     */
    private $receiverState;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_phone_mobile", type="string", length=15, nullable=true)
     */
    private $receiverPhoneMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_phone_landline", type="string", length=15, nullable=true)
     */
    private $receiverPhoneLandline;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_postal_code", type="string", length=10, nullable=true)
     */
    private $receiverPostalCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="receiver_account_number", type="integer", nullable=true)
     */
    private $receiverAccountNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="receiver_bank_routing_no", type="integer", nullable=true)
     */
    private $receiverBankRoutingNo;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_bank_branch", type="string", length=45, nullable=true)
     */
    private $receiverBankBranch;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_bank_name", type="string", length=45, nullable=true)
     */
    private $receiverBankName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiving_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $receivingAmount;

    /**
     * @var integer
     *
     * @ORM\Column(name="sender_id_number", type="integer", nullable=true)
     */
    private $senderIdNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_id_type", type="string", length=45, nullable=true)
     */
    private $senderIdType;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_id_issued_country", type="string", length=45, nullable=true)
     */
    private $senderIdIssuedCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_first_name", type="string", length=45, nullable=true)
     */
    private $senderFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_middle_name", type="string", length=45, nullable=true)
     */
    private $senderMiddleName;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_last_name", type="string", length=45, nullable=true)
     */
    private $senderLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_email", type="string", length=45, nullable=true)
     */
    private $senderEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_account_type", type="string", length=45, nullable=true)
     */
    private $senderAccountType;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_currency", type="string", length=45, nullable=true)
     */
    private $senderCurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_city", type="string", length=45, nullable=true)
     */
    private $senderCity;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_country", type="string", length=45, nullable=true)
     */
    private $senderCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_state", type="string", length=45, nullable=true)
     */
    private $senderState;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_phone_mobile", type="string", length=15, nullable=true)
     */
    private $senderPhoneMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_phone_landline", type="string", length=15, nullable=true)
     */
    private $senderPhoneLandline;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_postal_code", type="string", length=10, nullable=true)
     */
    private $senderPostalCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="sender_account_number", type="integer", nullable=true)
     */
    private $senderAccountNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="sender_bank_routing_number", type="integer", nullable=true)
     */
    private $senderBankRoutingNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_bank_branch", type="string", length=45, nullable=true)
     */
    private $senderBankBranch;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_bank_name", type="string", length=45, nullable=true)
     */
    private $senderBankName;

    /**
     * @var string
     *
     * @ORM\Column(name="sending_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $sendingAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $exchangeRate;

    /**
     * @var string
     *
     * @ORM\Column(name="additional_informations", type="text", nullable=true)
     */
    private $additionalInformations;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_payment_type", type="string", length=10, nullable=true)
     */
    private $transactionPaymentType;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_payment_type_code", type="string", length=10, nullable=true)
     */
    private $transactionPaymentTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=10, nullable=false)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set transactionKey
     *
     * @param string $transactionKey
     * @return Tb
     */
    public function setTransactionKey($transactionKey)
    {
        $this->transactionKey = $transactionKey;

        return $this;
    }

    /**
     * Get transactionKey
     *
     * @return string 
     */
    public function getTransactionKey()
    {
        return $this->transactionKey;
    }

    /**
     * Set transactionSource
     *
     * @param string $transactionSource
     * @return Tb
     */
    public function setTransactionSource($transactionSource)
    {
        $this->transactionSource = $transactionSource;

        return $this;
    }

    /**
     * Get transactionSource
     *
     * @return string 
     */
    public function getTransactionSource()
    {
        return $this->transactionSource;
    }

    /**
     * Set transactionService
     *
     * @param string $transactionService
     * @return Tb
     */
    public function setTransactionService($transactionService)
    {
        $this->transactionService = $transactionService;

        return $this;
    }

    /**
     * Get transactionService
     *
     * @return string 
     */
    public function getTransactionService()
    {
        return $this->transactionService;
    }

    /**
     * Set transactionStatus
     *
     * @param string $transactionStatus
     * @return Tb
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }

    /**
     * Get transactionStatus
     *
     * @return string 
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * Set transactionCode
     *
     * @param string $transactionCode
     * @return Tb
     */
    public function setTransactionCode($transactionCode)
    {
        $this->transactionCode = $transactionCode;

        return $this;
    }

    /**
     * Get transactionCode
     *
     * @return string 
     */
    public function getTransactionCode()
    {
        return $this->transactionCode;
    }

    /**
     * Set transactionType
     *
     * @param string $transactionType
     * @return Tb
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * Get transactionType
     *
     * @return string 
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Set receiverIdNumber
     *
     * @param integer $receiverIdNumber
     * @return Tb
     */
    public function setReceiverIdNumber($receiverIdNumber)
    {
        $this->receiverIdNumber = $receiverIdNumber;

        return $this;
    }

    /**
     * Get receiverIdNumber
     *
     * @return integer 
     */
    public function getReceiverIdNumber()
    {
        return $this->receiverIdNumber;
    }

    /**
     * Set receiverIdType
     *
     * @param string $receiverIdType
     * @return Tb
     */
    public function setReceiverIdType($receiverIdType)
    {
        $this->receiverIdType = $receiverIdType;

        return $this;
    }

    /**
     * Get receiverIdType
     *
     * @return string 
     */
    public function getReceiverIdType()
    {
        return $this->receiverIdType;
    }

    /**
     * Set receiverIdIssuedCountry
     *
     * @param string $receiverIdIssuedCountry
     * @return Tb
     */
    public function setReceiverIdIssuedCountry($receiverIdIssuedCountry)
    {
        $this->receiverIdIssuedCountry = $receiverIdIssuedCountry;

        return $this;
    }

    /**
     * Get receiverIdIssuedCountry
     *
     * @return string 
     */
    public function getReceiverIdIssuedCountry()
    {
        return $this->receiverIdIssuedCountry;
    }

    /**
     * Set receiverFirstName
     *
     * @param string $receiverFirstName
     * @return Tb
     */
    public function setReceiverFirstName($receiverFirstName)
    {
        $this->receiverFirstName = $receiverFirstName;

        return $this;
    }

    /**
     * Get receiverFirstName
     *
     * @return string 
     */
    public function getReceiverFirstName()
    {
        return $this->receiverFirstName;
    }

    /**
     * Set receiverMiddleName
     *
     * @param string $receiverMiddleName
     * @return Tb
     */
    public function setReceiverMiddleName($receiverMiddleName)
    {
        $this->receiverMiddleName = $receiverMiddleName;

        return $this;
    }

    /**
     * Get receiverMiddleName
     *
     * @return string 
     */
    public function getReceiverMiddleName()
    {
        return $this->receiverMiddleName;
    }

    /**
     * Set receiverLastName
     *
     * @param string $receiverLastName
     * @return Tb
     */
    public function setReceiverLastName($receiverLastName)
    {
        $this->receiverLastName = $receiverLastName;

        return $this;
    }

    /**
     * Get receiverLastName
     *
     * @return string 
     */
    public function getReceiverLastName()
    {
        return $this->receiverLastName;
    }

    /**
     * Set receiverEmail
     *
     * @param string $receiverEmail
     * @return Tb
     */
    public function setReceiverEmail($receiverEmail)
    {
        $this->receiverEmail = $receiverEmail;

        return $this;
    }

    /**
     * Get receiverEmail
     *
     * @return string 
     */
    public function getReceiverEmail()
    {
        return $this->receiverEmail;
    }

    /**
     * Set receiverAccountType
     *
     * @param string $receiverAccountType
     * @return Tb
     */
    public function setReceiverAccountType($receiverAccountType)
    {
        $this->receiverAccountType = $receiverAccountType;

        return $this;
    }

    /**
     * Get receiverAccountType
     *
     * @return string 
     */
    public function getReceiverAccountType()
    {
        return $this->receiverAccountType;
    }

    /**
     * Set receiverCurrency
     *
     * @param string $receiverCurrency
     * @return Tb
     */
    public function setReceiverCurrency($receiverCurrency)
    {
        $this->receiverCurrency = $receiverCurrency;

        return $this;
    }

    /**
     * Get receiverCurrency
     *
     * @return string 
     */
    public function getReceiverCurrency()
    {
        return $this->receiverCurrency;
    }

    /**
     * Set receiverCity
     *
     * @param string $receiverCity
     * @return Tb
     */
    public function setReceiverCity($receiverCity)
    {
        $this->receiverCity = $receiverCity;

        return $this;
    }

    /**
     * Get receiverCity
     *
     * @return string 
     */
    public function getReceiverCity()
    {
        return $this->receiverCity;
    }

    /**
     * Set receiverCountry
     *
     * @param string $receiverCountry
     * @return Tb
     */
    public function setReceiverCountry($receiverCountry)
    {
        $this->receiverCountry = $receiverCountry;

        return $this;
    }

    /**
     * Get receiverCountry
     *
     * @return string 
     */
    public function getReceiverCountry()
    {
        return $this->receiverCountry;
    }

    /**
     * Set receiverState
     *
     * @param string $receiverState
     * @return Tb
     */
    public function setReceiverState($receiverState)
    {
        $this->receiverState = $receiverState;

        return $this;
    }

    /**
     * Get receiverState
     *
     * @return string 
     */
    public function getReceiverState()
    {
        return $this->receiverState;
    }

    /**
     * Set receiverPhoneMobile
     *
     * @param string $receiverPhoneMobile
     * @return Tb
     */
    public function setReceiverPhoneMobile($receiverPhoneMobile)
    {
        $this->receiverPhoneMobile = $receiverPhoneMobile;

        return $this;
    }

    /**
     * Get receiverPhoneMobile
     *
     * @return string 
     */
    public function getReceiverPhoneMobile()
    {
        return $this->receiverPhoneMobile;
    }

    /**
     * Set receiverPhoneLandline
     *
     * @param string $receiverPhoneLandline
     * @return Tb
     */
    public function setReceiverPhoneLandline($receiverPhoneLandline)
    {
        $this->receiverPhoneLandline = $receiverPhoneLandline;

        return $this;
    }

    /**
     * Get receiverPhoneLandline
     *
     * @return string 
     */
    public function getReceiverPhoneLandline()
    {
        return $this->receiverPhoneLandline;
    }

    /**
     * Set receiverPostalCode
     *
     * @param string $receiverPostalCode
     * @return Tb
     */
    public function setReceiverPostalCode($receiverPostalCode)
    {
        $this->receiverPostalCode = $receiverPostalCode;

        return $this;
    }

    /**
     * Get receiverPostalCode
     *
     * @return string 
     */
    public function getReceiverPostalCode()
    {
        return $this->receiverPostalCode;
    }

    /**
     * Set receiverAccountNumber
     *
     * @param integer $receiverAccountNumber
     * @return Tb
     */
    public function setReceiverAccountNumber($receiverAccountNumber)
    {
        $this->receiverAccountNumber = $receiverAccountNumber;

        return $this;
    }

    /**
     * Get receiverAccountNumber
     *
     * @return integer 
     */
    public function getReceiverAccountNumber()
    {
        return $this->receiverAccountNumber;
    }

    /**
     * Set receiverBankRoutingNo
     *
     * @param integer $receiverBankRoutingNo
     * @return Tb
     */
    public function setReceiverBankRoutingNo($receiverBankRoutingNo)
    {
        $this->receiverBankRoutingNo = $receiverBankRoutingNo;

        return $this;
    }

    /**
     * Get receiverBankRoutingNo
     *
     * @return integer 
     */
    public function getReceiverBankRoutingNo()
    {
        return $this->receiverBankRoutingNo;
    }

    /**
     * Set receiverBankBranch
     *
     * @param string $receiverBankBranch
     * @return Tb
     */
    public function setReceiverBankBranch($receiverBankBranch)
    {
        $this->receiverBankBranch = $receiverBankBranch;

        return $this;
    }

    /**
     * Get receiverBankBranch
     *
     * @return string 
     */
    public function getReceiverBankBranch()
    {
        return $this->receiverBankBranch;
    }

    /**
     * Set receiverBankName
     *
     * @param string $receiverBankName
     * @return Tb
     */
    public function setReceiverBankName($receiverBankName)
    {
        $this->receiverBankName = $receiverBankName;

        return $this;
    }

    /**
     * Get receiverBankName
     *
     * @return string 
     */
    public function getReceiverBankName()
    {
        return $this->receiverBankName;
    }

    /**
     * Set receivingAmount
     *
     * @param string $receivingAmount
     * @return Tb
     */
    public function setReceivingAmount($receivingAmount)
    {
        $this->receivingAmount = $receivingAmount;

        return $this;
    }

    /**
     * Get receivingAmount
     *
     * @return string 
     */
    public function getReceivingAmount()
    {
        return $this->receivingAmount;
    }

    /**
     * Set senderIdNumber
     *
     * @param integer $senderIdNumber
     * @return Tb
     */
    public function setSenderIdNumber($senderIdNumber)
    {
        $this->senderIdNumber = $senderIdNumber;

        return $this;
    }

    /**
     * Get senderIdNumber
     *
     * @return integer 
     */
    public function getSenderIdNumber()
    {
        return $this->senderIdNumber;
    }

    /**
     * Set senderIdType
     *
     * @param string $senderIdType
     * @return Tb
     */
    public function setSenderIdType($senderIdType)
    {
        $this->senderIdType = $senderIdType;

        return $this;
    }

    /**
     * Get senderIdType
     *
     * @return string 
     */
    public function getSenderIdType()
    {
        return $this->senderIdType;
    }

    /**
     * Set senderIdIssuedCountry
     *
     * @param string $senderIdIssuedCountry
     * @return Tb
     */
    public function setSenderIdIssuedCountry($senderIdIssuedCountry)
    {
        $this->senderIdIssuedCountry = $senderIdIssuedCountry;

        return $this;
    }

    /**
     * Get senderIdIssuedCountry
     *
     * @return string 
     */
    public function getSenderIdIssuedCountry()
    {
        return $this->senderIdIssuedCountry;
    }

    /**
     * Set senderFirstName
     *
     * @param string $senderFirstName
     * @return Tb
     */
    public function setSenderFirstName($senderFirstName)
    {
        $this->senderFirstName = $senderFirstName;

        return $this;
    }

    /**
     * Get senderFirstName
     *
     * @return string 
     */
    public function getSenderFirstName()
    {
        return $this->senderFirstName;
    }

    /**
     * Set senderMiddleName
     *
     * @param string $senderMiddleName
     * @return Tb
     */
    public function setSenderMiddleName($senderMiddleName)
    {
        $this->senderMiddleName = $senderMiddleName;

        return $this;
    }

    /**
     * Get senderMiddleName
     *
     * @return string 
     */
    public function getSenderMiddleName()
    {
        return $this->senderMiddleName;
    }

    /**
     * Set senderLastName
     *
     * @param string $senderLastName
     * @return Tb
     */
    public function setSenderLastName($senderLastName)
    {
        $this->senderLastName = $senderLastName;

        return $this;
    }

    /**
     * Get senderLastName
     *
     * @return string 
     */
    public function getSenderLastName()
    {
        return $this->senderLastName;
    }

    /**
     * Set senderEmail
     *
     * @param string $senderEmail
     * @return Tb
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    /**
     * Get senderEmail
     *
     * @return string 
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Set senderAccountType
     *
     * @param string $senderAccountType
     * @return Tb
     */
    public function setSenderAccountType($senderAccountType)
    {
        $this->senderAccountType = $senderAccountType;

        return $this;
    }

    /**
     * Get senderAccountType
     *
     * @return string 
     */
    public function getSenderAccountType()
    {
        return $this->senderAccountType;
    }

    /**
     * Set senderCurrency
     *
     * @param string $senderCurrency
     * @return Tb
     */
    public function setSenderCurrency($senderCurrency)
    {
        $this->senderCurrency = $senderCurrency;

        return $this;
    }

    /**
     * Get senderCurrency
     *
     * @return string 
     */
    public function getSenderCurrency()
    {
        return $this->senderCurrency;
    }

    /**
     * Set senderCity
     *
     * @param string $senderCity
     * @return Tb
     */
    public function setSenderCity($senderCity)
    {
        $this->senderCity = $senderCity;

        return $this;
    }

    /**
     * Get senderCity
     *
     * @return string 
     */
    public function getSenderCity()
    {
        return $this->senderCity;
    }

    /**
     * Set senderCountry
     *
     * @param string $senderCountry
     * @return Tb
     */
    public function setSenderCountry($senderCountry)
    {
        $this->senderCountry = $senderCountry;

        return $this;
    }

    /**
     * Get senderCountry
     *
     * @return string 
     */
    public function getSenderCountry()
    {
        return $this->senderCountry;
    }

    /**
     * Set senderState
     *
     * @param string $senderState
     * @return Tb
     */
    public function setSenderState($senderState)
    {
        $this->senderState = $senderState;

        return $this;
    }

    /**
     * Get senderState
     *
     * @return string 
     */
    public function getSenderState()
    {
        return $this->senderState;
    }

    /**
     * Set senderPhoneMobile
     *
     * @param string $senderPhoneMobile
     * @return Tb
     */
    public function setSenderPhoneMobile($senderPhoneMobile)
    {
        $this->senderPhoneMobile = $senderPhoneMobile;

        return $this;
    }

    /**
     * Get senderPhoneMobile
     *
     * @return string 
     */
    public function getSenderPhoneMobile()
    {
        return $this->senderPhoneMobile;
    }

    /**
     * Set senderPhoneLandline
     *
     * @param string $senderPhoneLandline
     * @return Tb
     */
    public function setSenderPhoneLandline($senderPhoneLandline)
    {
        $this->senderPhoneLandline = $senderPhoneLandline;

        return $this;
    }

    /**
     * Get senderPhoneLandline
     *
     * @return string 
     */
    public function getSenderPhoneLandline()
    {
        return $this->senderPhoneLandline;
    }

    /**
     * Set senderPostalCode
     *
     * @param string $senderPostalCode
     * @return Tb
     */
    public function setSenderPostalCode($senderPostalCode)
    {
        $this->senderPostalCode = $senderPostalCode;

        return $this;
    }

    /**
     * Get senderPostalCode
     *
     * @return string 
     */
    public function getSenderPostalCode()
    {
        return $this->senderPostalCode;
    }

    /**
     * Set senderAccountNumber
     *
     * @param integer $senderAccountNumber
     * @return Tb
     */
    public function setSenderAccountNumber($senderAccountNumber)
    {
        $this->senderAccountNumber = $senderAccountNumber;

        return $this;
    }

    /**
     * Get senderAccountNumber
     *
     * @return integer 
     */
    public function getSenderAccountNumber()
    {
        return $this->senderAccountNumber;
    }

    /**
     * Set senderBankRoutingNumber
     *
     * @param integer $senderBankRoutingNumber
     * @return Tb
     */
    public function setSenderBankRoutingNumber($senderBankRoutingNumber)
    {
        $this->senderBankRoutingNumber = $senderBankRoutingNumber;

        return $this;
    }

    /**
     * Get senderBankRoutingNumber
     *
     * @return integer 
     */
    public function getSenderBankRoutingNumber()
    {
        return $this->senderBankRoutingNumber;
    }

    /**
     * Set senderBankBranch
     *
     * @param string $senderBankBranch
     * @return Tb
     */
    public function setSenderBankBranch($senderBankBranch)
    {
        $this->senderBankBranch = $senderBankBranch;

        return $this;
    }

    /**
     * Get senderBankBranch
     *
     * @return string 
     */
    public function getSenderBankBranch()
    {
        return $this->senderBankBranch;
    }

    /**
     * Set senderBankName
     *
     * @param string $senderBankName
     * @return Tb
     */
    public function setSenderBankName($senderBankName)
    {
        $this->senderBankName = $senderBankName;

        return $this;
    }

    /**
     * Get senderBankName
     *
     * @return string 
     */
    public function getSenderBankName()
    {
        return $this->senderBankName;
    }

    /**
     * Set sendingAmount
     *
     * @param string $sendingAmount
     * @return Tb
     */
    public function setSendingAmount($sendingAmount)
    {
        $this->sendingAmount = $sendingAmount;

        return $this;
    }

    /**
     * Get sendingAmount
     *
     * @return string 
     */
    public function getSendingAmount()
    {
        return $this->sendingAmount;
    }

    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate
     * @return Tb
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * Get exchangeRate
     *
     * @return string 
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * Set additionalInformations
     *
     * @param string $additionalInformations
     * @return Tb
     */
    public function setAdditionalInformations($additionalInformations)
    {
        $this->additionalInformations = $additionalInformations;

        return $this;
    }

    /**
     * Get additionalInformations
     *
     * @return string 
     */
    public function getAdditionalInformations()
    {
        return $this->additionalInformations;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Tb
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Tb
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set transactionPaymentType
     *
     * @param string $transactionPaymentType
     * @return Tb
     */
    public function setTransactionPaymentType($transactionPaymentType)
    {
        $this->transactionPaymentType = $transactionPaymentType;

        return $this;
    }

    /**
     * Get transactionPaymentType
     *
     * @return string 
     */
    public function getTransactionPaymentType()
    {
        return $this->transactionPaymentType;
    }

    /**
     * Set transactionPaymentTypeCode
     *
     * @param string $transactionPaymentTypeCode
     * @return Tb
     */
    public function setTransactionPaymentTypeCode($transactionPaymentTypeCode)
    {
        $this->transactionPaymentTypeCode = $transactionPaymentTypeCode;

        return $this;
    }

    /**
     * Get transactionPaymentTypeCode
     *
     * @return string 
     */
    public function getTransactionPaymentTypeCode()
    {
        return $this->transactionPaymentTypeCode;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Tb
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
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
