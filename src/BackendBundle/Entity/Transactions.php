<?php

namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="BackendBundle\Entity\Repository\TransactionRepository")
 */
class Transactions
{
    const QUEUE_OPERATION_CREATE = 'CREATE';
    const QUEUE_OPERATION_CHANGE = 'CHANGE';
    const QUEUE_OPERATION_CANCEL = 'CANCEL';
    const QUEUE_OPERATION_ENQUEUE = 'ENQUEUE';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @ORM\Column(name="source_transaction_id", type="string", length=11, nullable=true)
     */
    private $sourceTransactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_code", type="string", length=50, nullable=false)
     */
    private $transactionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_number", type="string", length=50, nullable=true)
     */
    private $trackingNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", length=10, nullable=false)
     */
    private $transactionType;

    /**
     * @var string
     *
     * @ORM\Column(name="remitting_currency", type="string", length=3, nullable=true)
     */
    private $remittingCurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="payout_currency", type="string", length=3, nullable=true)
     */
    private $payoutCurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="remitting_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $remittingAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="payout_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $payoutAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $exchangeRate;

    /**
     * @var string
     *
     * @ORM\Column(name="fee", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $fee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="remittance_date", type="datetime", nullable=true)
     */
    private $remittanceDate;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_first_name", type="string", length=45, nullable=true)
     */
    private $beneficiaryFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_middle_name", type="string", length=45, nullable=true)
     */
    private $beneficiaryMiddleName;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_last_name", type="string", length=45, nullable=true)
     */
    private $beneficiaryLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_email", type="string", length=45, nullable=true)
     */
    private $beneficiaryEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_city", type="string", length=45, nullable=true)
     */
    private $beneficiaryCity;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_state", type="string", length=45, nullable=true)
     */
    private $beneficiaryState;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_country", type="string", length=20, nullable=true)
     */
    private $beneficiaryCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_address", type="string", length=100, nullable=true)
     */
    private $beneficiaryAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_phone_mobile", type="string", length=15, nullable=true)
     */
    private $beneficiaryPhoneMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_phone_landline", type="string", length=15, nullable=true)
     */
    private $beneficiaryPhoneLandline;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_postal_code", type="string", length=10, nullable=true)
     */
    private $beneficiaryPostalCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="beneficiary_account_number", type="integer", nullable=true)
     */
    private $beneficiaryAccountNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="beneficiary_bank_routing_no", type="integer", nullable=true)
     */
    private $beneficiaryBankRoutingNo;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_bank_branch", type="string", length=45, nullable=true)
     */
    private $beneficiaryBankBranch;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_bank_name", type="string", length=45, nullable=true)
     */
    private $beneficiaryBankName;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_bank_account_type", type="string", length=6, nullable=true)
     */
    private $beneficiaryBankAccountType;

    /**
     * @var integer
     *
     * @ORM\Column(name="beneficiary_id_number", type="integer", nullable=true)
     */
    private $beneficiaryIdNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_id_type", type="string", length=45, nullable=true)
     */
    private $beneficiaryIdType;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_id_issued_country", type="string", length=20, nullable=true)
     */
    private $beneficiaryIdIssuedCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_id_issued_city", type="string", length=45, nullable=true)
     */
    private $beneficiaryIdIssuedCity;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_id_issued_state", type="string", length=45, nullable=true)
     */
    private $beneficiaryIdIssuedState;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="beneficiary_id_issued_date", type="datetime", nullable=true)
     */
    private $beneficiaryIdIssuedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="beneficiary_id_expiry_date", type="datetime", nullable=true)
     */
    private $beneficiaryIdExpiryDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="payout_agent_id", type="integer", nullable=true)
     */
    private $payoutAgentId;

    /**
     * @var string
     *
     * @ORM\Column(name="payout_agent_name", type="string", length=25, nullable=true)
     */
    private $payoutAgentName;

    /**
     * @var string
     *
     * @ORM\Column(name="payout_agent_city", type="string", length=45, nullable=true)
     */
    private $payoutAgentCity;

    /**
     * @var string
     *
     * @ORM\Column(name="payout_agent_state", type="string", length=45, nullable=true)
     */
    private $payoutAgentState;

    /**
     * @var string
     *
     * @ORM\Column(name="payout_agent_country", type="string", length=20, nullable=true)
     */
    private $payoutAgentCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_first_name", type="string", length=45, nullable=true)
     */
    private $remitterFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_middle_name", type="string", length=45, nullable=true)
     */
    private $remitterMiddleName;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_last_name", type="string", length=45, nullable=true)
     */
    private $remitterLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_email", type="string", length=45, nullable=true)
     */
    private $remitterEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_city", type="string", length=45, nullable=true)
     */
    private $remitterCity;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_country", type="string", length=20, nullable=true)
     */
    private $remitterCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_state", type="string", length=45, nullable=true)
     */
    private $remitterState;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_address", type="string", length=100, nullable=true)
     */
    private $remitterAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_phone_mobile", type="string", length=15, nullable=true)
     */
    private $remitterPhoneMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_phone_landline", type="string", length=15, nullable=true)
     */
    private $remitterPhoneLandline;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_postal_code", type="string", length=10, nullable=true)
     */
    private $remitterPostalCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="remitter_account_number", type="integer", nullable=true)
     */
    private $remitterAccountNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="remitter_bank_routing_no", type="integer", nullable=true)
     */
    private $remitterBankRoutingNo;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_bank_branch", type="string", length=45, nullable=true)
     */
    private $remitterBankBranch;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_bank_name", type="string", length=45, nullable=true)
     */
    private $remitterBankName;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_bank_account_type", type="string", length=6, nullable=true)
     */
    private $remitterBankAccountType;

    /**
     * @var integer
     *
     * @ORM\Column(name="remitter_id_number", type="integer", nullable=true)
     */
    private $remitterIdNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_id_type", type="string", length=45, nullable=true)
     */
    private $remitterIdType;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_id_issued_country", type="string", length=20, nullable=true)
     */
    private $remitterIdIssuedCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_id_issued_city", type="string", length=45, nullable=true)
     */
    private $remitterIdIssuedCity;

    /**
     * @var string
     *
     * @ORM\Column(name="remitter_id_issued_state", type="string", length=45, nullable=true)
     */
    private $remitterIdIssuedState;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="remitter_id_issued_date", type="datetime", nullable=true)
     */
    private $remitterIdIssuedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="remitter_id_expiry_date", type="datetime", nullable=true)
     */
    private $remitterIdExpiryDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="remitting_agent_id", type="integer", nullable=true)
     */
    private $remittingAgentId;

    /**
     * @var string
     *
     * @ORM\Column(name="remitting_agent_name", type="string", length=25, nullable=true)
     */
    private $remittingAgentName;

    /**
     * @var string
     *
     * @ORM\Column(name="remitting_agent_city", type="string", length=45, nullable=true)
     */
    private $remittingAgentCity;

    /**
     * @var string
     *
     * @ORM\Column(name="remitting_agent_state", type="string", length=45, nullable=true)
     */
    private $remittingAgentState;

    /**
     * @var string
     *
     * @ORM\Column(name="remitting_agent_country", type="string", length=20, nullable=true)
     */
    private $remittingAgentCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="processing_status", type="string", length=25, nullable=false)
     */
    private $processingStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;



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
     * Set transactionSource
     *
     * @param string $transactionSource
     * @return Transactions
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
     * @return Transactions
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
     * Set sourceTransactionId
     *
     * @param string $sourceTransactionId
     * @return Transactions
     */
    public function setSourceTransactionId($sourceTransactionId)
    {
        $this->sourceTransactionId = $sourceTransactionId;

        return $this;
    }

    /**
     * Get sourceTransactionId
     *
     * @return string 
     */
    public function getSourceTransactionId()
    {
        return $this->sourceTransactionId;
    }

    /**
     * Set transactionCode
     *
     * @param string $transactionCode
     * @return Transactions
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
     * Set trackingNumber
     *
     * @param string $trackingNumber
     * @return Transactions
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
    }

    /**
     * Get trackingNumber
     *
     * @return string 
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * Set transactionType
     *
     * @param string $transactionType
     * @return Transactions
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
     * Set remittingCurrency
     *
     * @param string $remittingCurrency
     * @return Transactions
     */
    public function setRemittingCurrency($remittingCurrency)
    {
        $this->remittingCurrency = $remittingCurrency;

        return $this;
    }

    /**
     * Get remittingCurrency
     *
     * @return string 
     */
    public function getRemittingCurrency()
    {
        return $this->remittingCurrency;
    }

    /**
     * Set payoutCurrency
     *
     * @param string $payoutCurrency
     * @return Transactions
     */
    public function setPayoutCurrency($payoutCurrency)
    {
        $this->payoutCurrency = $payoutCurrency;

        return $this;
    }

    /**
     * Get payoutCurrency
     *
     * @return string 
     */
    public function getPayoutCurrency()
    {
        return $this->payoutCurrency;
    }

    /**
     * Set remittingAmount
     *
     * @param string $remittingAmount
     * @return Transactions
     */
    public function setRemittingAmount($remittingAmount)
    {
        $this->remittingAmount = $remittingAmount;

        return $this;
    }

    /**
     * Get remittingAmount
     *
     * @return string 
     */
    public function getRemittingAmount()
    {
        return $this->remittingAmount;
    }

    /**
     * Set payoutAmount
     *
     * @param string $payoutAmount
     * @return Transactions
     */
    public function setPayoutAmount($payoutAmount)
    {
        $this->payoutAmount = $payoutAmount;

        return $this;
    }

    /**
     * Get payoutAmount
     *
     * @return string 
     */
    public function getPayoutAmount()
    {
        return $this->payoutAmount;
    }

    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate
     * @return Transactions
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
     * Set fee
     *
     * @param string $fee
     * @return Transactions
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return string 
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set remittanceDate
     *
     * @param \DateTime $remittanceDate
     * @return Transactions
     */
    public function setRemittanceDate($remittanceDate)
    {
        $this->remittanceDate = $remittanceDate;

        return $this;
    }

    /**
     * Get remittanceDate
     *
     * @return \DateTime 
     */
    public function getRemittanceDate()
    {
        return $this->remittanceDate;
    }

    /**
     * Set beneficiaryFirstName
     *
     * @param string $beneficiaryFirstName
     * @return Transactions
     */
    public function setBeneficiaryFirstName($beneficiaryFirstName)
    {
        $this->beneficiaryFirstName = $beneficiaryFirstName;

        return $this;
    }

    /**
     * Get beneficiaryFirstName
     *
     * @return string 
     */
    public function getBeneficiaryFirstName()
    {
        return $this->beneficiaryFirstName;
    }

    /**
     * Set beneficiaryMiddleName
     *
     * @param string $beneficiaryMiddleName
     * @return Transactions
     */
    public function setBeneficiaryMiddleName($beneficiaryMiddleName)
    {
        $this->beneficiaryMiddleName = $beneficiaryMiddleName;

        return $this;
    }

    /**
     * Get beneficiaryMiddleName
     *
     * @return string 
     */
    public function getBeneficiaryMiddleName()
    {
        return $this->beneficiaryMiddleName;
    }

    /**
     * Set beneficiaryLastName
     *
     * @param string $beneficiaryLastName
     * @return Transactions
     */
    public function setBeneficiaryLastName($beneficiaryLastName)
    {
        $this->beneficiaryLastName = $beneficiaryLastName;

        return $this;
    }

    /**
     * Get beneficiaryLastName
     *
     * @return string 
     */
    public function getBeneficiaryLastName()
    {
        return $this->beneficiaryLastName;
    }

    /**
     * Set beneficiaryEmail
     *
     * @param string $beneficiaryEmail
     * @return Transactions
     */
    public function setBeneficiaryEmail($beneficiaryEmail)
    {
        $this->beneficiaryEmail = $beneficiaryEmail;

        return $this;
    }

    /**
     * Get beneficiaryEmail
     *
     * @return string 
     */
    public function getBeneficiaryEmail()
    {
        return $this->beneficiaryEmail;
    }

    /**
     * Set beneficiaryCity
     *
     * @param string $beneficiaryCity
     * @return Transactions
     */
    public function setBeneficiaryCity($beneficiaryCity)
    {
        $this->beneficiaryCity = $beneficiaryCity;

        return $this;
    }

    /**
     * Get beneficiaryCity
     *
     * @return string 
     */
    public function getBeneficiaryCity()
    {
        return $this->beneficiaryCity;
    }

    /**
     * Set beneficiaryState
     *
     * @param string $beneficiaryState
     * @return Transactions
     */
    public function setBeneficiaryState($beneficiaryState)
    {
        $this->beneficiaryState = $beneficiaryState;

        return $this;
    }

    /**
     * Get beneficiaryState
     *
     * @return string 
     */
    public function getBeneficiaryState()
    {
        return $this->beneficiaryState;
    }

    /**
     * Set beneficiaryCountry
     *
     * @param string $beneficiaryCountry
     * @return Transactions
     */
    public function setBeneficiaryCountry($beneficiaryCountry)
    {
        $this->beneficiaryCountry = $beneficiaryCountry;

        return $this;
    }

    /**
     * Get beneficiaryCountry
     *
     * @return string 
     */
    public function getBeneficiaryCountry()
    {
        return $this->beneficiaryCountry;
    }

    /**
     * Set beneficiaryAddress
     *
     * @param string $beneficiaryAddress
     * @return Transactions
     */
    public function setBeneficiaryAddress($beneficiaryAddress)
    {
        $this->beneficiaryAddress = $beneficiaryAddress;

        return $this;
    }

    /**
     * Get beneficiaryAddress
     *
     * @return string 
     */
    public function getBeneficiaryAddress()
    {
        return $this->beneficiaryAddress;
    }

    /**
     * Set beneficiaryPhoneMobile
     *
     * @param string $beneficiaryPhoneMobile
     * @return Transactions
     */
    public function setBeneficiaryPhoneMobile($beneficiaryPhoneMobile)
    {
        $this->beneficiaryPhoneMobile = $beneficiaryPhoneMobile;

        return $this;
    }

    /**
     * Get beneficiaryPhoneMobile
     *
     * @return string 
     */
    public function getBeneficiaryPhoneMobile()
    {
        return $this->beneficiaryPhoneMobile;
    }

    /**
     * Set beneficiaryPhoneLandline
     *
     * @param string $beneficiaryPhoneLandline
     * @return Transactions
     */
    public function setBeneficiaryPhoneLandline($beneficiaryPhoneLandline)
    {
        $this->beneficiaryPhoneLandline = $beneficiaryPhoneLandline;

        return $this;
    }

    /**
     * Get beneficiaryPhoneLandline
     *
     * @return string 
     */
    public function getBeneficiaryPhoneLandline()
    {
        return $this->beneficiaryPhoneLandline;
    }

    /**
     * Set beneficiaryPostalCode
     *
     * @param string $beneficiaryPostalCode
     * @return Transactions
     */
    public function setBeneficiaryPostalCode($beneficiaryPostalCode)
    {
        $this->beneficiaryPostalCode = $beneficiaryPostalCode;

        return $this;
    }

    /**
     * Get beneficiaryPostalCode
     *
     * @return string 
     */
    public function getBeneficiaryPostalCode()
    {
        return $this->beneficiaryPostalCode;
    }

    /**
     * Set beneficiaryAccountNumber
     *
     * @param integer $beneficiaryAccountNumber
     * @return Transactions
     */
    public function setBeneficiaryAccountNumber($beneficiaryAccountNumber)
    {
        $this->beneficiaryAccountNumber = $beneficiaryAccountNumber;

        return $this;
    }

    /**
     * Get beneficiaryAccountNumber
     *
     * @return integer 
     */
    public function getBeneficiaryAccountNumber()
    {
        return $this->beneficiaryAccountNumber;
    }

    /**
     * Set beneficiaryBankRoutingNo
     *
     * @param integer $beneficiaryBankRoutingNo
     * @return Transactions
     */
    public function setBeneficiaryBankRoutingNo($beneficiaryBankRoutingNo)
    {
        $this->beneficiaryBankRoutingNo = $beneficiaryBankRoutingNo;

        return $this;
    }

    /**
     * Get beneficiaryBankRoutingNo
     *
     * @return integer 
     */
    public function getBeneficiaryBankRoutingNo()
    {
        return $this->beneficiaryBankRoutingNo;
    }

    /**
     * Set beneficiaryBankBranch
     *
     * @param string $beneficiaryBankBranch
     * @return Transactions
     */
    public function setBeneficiaryBankBranch($beneficiaryBankBranch)
    {
        $this->beneficiaryBankBranch = $beneficiaryBankBranch;

        return $this;
    }

    /**
     * Get beneficiaryBankBranch
     *
     * @return string 
     */
    public function getBeneficiaryBankBranch()
    {
        return $this->beneficiaryBankBranch;
    }

    /**
     * Set beneficiaryBankName
     *
     * @param string $beneficiaryBankName
     * @return Transactions
     */
    public function setBeneficiaryBankName($beneficiaryBankName)
    {
        $this->beneficiaryBankName = $beneficiaryBankName;

        return $this;
    }

    /**
     * Get beneficiaryBankName
     *
     * @return string 
     */
    public function getBeneficiaryBankName()
    {
        return $this->beneficiaryBankName;
    }

    /**
     * Set beneficiaryBankAccountType
     *
     * @param string $beneficiaryBankAccountType
     * @return Transactions
     */
    public function setBeneficiaryBankAccountType($beneficiaryBankAccountType)
    {
        $this->beneficiaryBankAccountType = $beneficiaryBankAccountType;

        return $this;
    }

    /**
     * Get beneficiaryBankAccountType
     *
     * @return string 
     */
    public function getBeneficiaryBankAccountType()
    {
        return $this->beneficiaryBankAccountType;
    }

    /**
     * Set beneficiaryIdNumber
     *
     * @param integer $beneficiaryIdNumber
     * @return Transactions
     */
    public function setBeneficiaryIdNumber($beneficiaryIdNumber)
    {
        $this->beneficiaryIdNumber = $beneficiaryIdNumber;

        return $this;
    }

    /**
     * Get beneficiaryIdNumber
     *
     * @return integer 
     */
    public function getBeneficiaryIdNumber()
    {
        return $this->beneficiaryIdNumber;
    }

    /**
     * Set beneficiaryIdType
     *
     * @param string $beneficiaryIdType
     * @return Transactions
     */
    public function setBeneficiaryIdType($beneficiaryIdType)
    {
        $this->beneficiaryIdType = $beneficiaryIdType;

        return $this;
    }

    /**
     * Get beneficiaryIdType
     *
     * @return string 
     */
    public function getBeneficiaryIdType()
    {
        return $this->beneficiaryIdType;
    }

    /**
     * Set beneficiaryIdIssuedCountry
     *
     * @param string $beneficiaryIdIssuedCountry
     * @return Transactions
     */
    public function setBeneficiaryIdIssuedCountry($beneficiaryIdIssuedCountry)
    {
        $this->beneficiaryIdIssuedCountry = $beneficiaryIdIssuedCountry;

        return $this;
    }

    /**
     * Get beneficiaryIdIssuedCountry
     *
     * @return string 
     */
    public function getBeneficiaryIdIssuedCountry()
    {
        return $this->beneficiaryIdIssuedCountry;
    }

    /**
     * Set beneficiaryIdIssuedCity
     *
     * @param string $beneficiaryIdIssuedCity
     * @return Transactions
     */
    public function setBeneficiaryIdIssuedCity($beneficiaryIdIssuedCity)
    {
        $this->beneficiaryIdIssuedCity = $beneficiaryIdIssuedCity;

        return $this;
    }

    /**
     * Get beneficiaryIdIssuedCity
     *
     * @return string 
     */
    public function getBeneficiaryIdIssuedCity()
    {
        return $this->beneficiaryIdIssuedCity;
    }

    /**
     * Set beneficiaryIdIssuedState
     *
     * @param string $beneficiaryIdIssuedState
     * @return Transactions
     */
    public function setBeneficiaryIdIssuedState($beneficiaryIdIssuedState)
    {
        $this->beneficiaryIdIssuedState = $beneficiaryIdIssuedState;

        return $this;
    }

    /**
     * Get beneficiaryIdIssuedState
     *
     * @return string 
     */
    public function getBeneficiaryIdIssuedState()
    {
        return $this->beneficiaryIdIssuedState;
    }

    /**
     * Set beneficiaryIdIssuedDate
     *
     * @param \DateTime $beneficiaryIdIssuedDate
     * @return Transactions
     */
    public function setBeneficiaryIdIssuedDate($beneficiaryIdIssuedDate)
    {
        $this->beneficiaryIdIssuedDate = $beneficiaryIdIssuedDate;

        return $this;
    }

    /**
     * Get beneficiaryIdIssuedDate
     *
     * @return \DateTime 
     */
    public function getBeneficiaryIdIssuedDate()
    {
        return $this->beneficiaryIdIssuedDate;
    }

    /**
     * Set beneficiaryIdExpiryDate
     *
     * @param \DateTime $beneficiaryIdExpiryDate
     * @return Transactions
     */
    public function setBeneficiaryIdExpiryDate($beneficiaryIdExpiryDate)
    {
        $this->beneficiaryIdExpiryDate = $beneficiaryIdExpiryDate;

        return $this;
    }

    /**
     * Get beneficiaryIdExpiryDate
     *
     * @return \DateTime 
     */
    public function getBeneficiaryIdExpiryDate()
    {
        return $this->beneficiaryIdExpiryDate;
    }

    /**
     * Set payoutAgentId
     *
     * @param integer $payoutAgentId
     * @return Transactions
     */
    public function setPayoutAgentId($payoutAgentId)
    {
        $this->payoutAgentId = $payoutAgentId;

        return $this;
    }

    /**
     * Get payoutAgentId
     *
     * @return integer 
     */
    public function getPayoutAgentId()
    {
        return $this->payoutAgentId;
    }

    /**
     * Set payoutAgentName
     *
     * @param string $payoutAgentName
     * @return Transactions
     */
    public function setPayoutAgentName($payoutAgentName)
    {
        $this->payoutAgentName = $payoutAgentName;

        return $this;
    }

    /**
     * Get payoutAgentName
     *
     * @return string 
     */
    public function getPayoutAgentName()
    {
        return $this->payoutAgentName;
    }

    /**
     * Set payoutAgentCity
     *
     * @param string $payoutAgentCity
     * @return Transactions
     */
    public function setPayoutAgentCity($payoutAgentCity)
    {
        $this->payoutAgentCity = $payoutAgentCity;

        return $this;
    }

    /**
     * Get payoutAgentCity
     *
     * @return string 
     */
    public function getPayoutAgentCity()
    {
        return $this->payoutAgentCity;
    }

    /**
     * Set payoutAgentState
     *
     * @param string $payoutAgentState
     * @return Transactions
     */
    public function setPayoutAgentState($payoutAgentState)
    {
        $this->payoutAgentState = $payoutAgentState;

        return $this;
    }

    /**
     * Get payoutAgentState
     *
     * @return string 
     */
    public function getPayoutAgentState()
    {
        return $this->payoutAgentState;
    }

    /**
     * Set payoutAgentCountry
     *
     * @param string $payoutAgentCountry
     * @return Transactions
     */
    public function setPayoutAgentCountry($payoutAgentCountry)
    {
        $this->payoutAgentCountry = $payoutAgentCountry;

        return $this;
    }

    /**
     * Get payoutAgentCountry
     *
     * @return string 
     */
    public function getPayoutAgentCountry()
    {
        return $this->payoutAgentCountry;
    }

    /**
     * Set remitterFirstName
     *
     * @param string $remitterFirstName
     * @return Transactions
     */
    public function setRemitterFirstName($remitterFirstName)
    {
        $this->remitterFirstName = $remitterFirstName;

        return $this;
    }

    /**
     * Get remitterFirstName
     *
     * @return string 
     */
    public function getRemitterFirstName()
    {
        return $this->remitterFirstName;
    }

    /**
     * Set remitterMiddleName
     *
     * @param string $remitterMiddleName
     * @return Transactions
     */
    public function setRemitterMiddleName($remitterMiddleName)
    {
        $this->remitterMiddleName = $remitterMiddleName;

        return $this;
    }

    /**
     * Get remitterMiddleName
     *
     * @return string 
     */
    public function getRemitterMiddleName()
    {
        return $this->remitterMiddleName;
    }

    /**
     * Set remitterLastName
     *
     * @param string $remitterLastName
     * @return Transactions
     */
    public function setRemitterLastName($remitterLastName)
    {
        $this->remitterLastName = $remitterLastName;

        return $this;
    }

    /**
     * Get remitterLastName
     *
     * @return string 
     */
    public function getRemitterLastName()
    {
        return $this->remitterLastName;
    }

    /**
     * Set remitterEmail
     *
     * @param string $remitterEmail
     * @return Transactions
     */
    public function setRemitterEmail($remitterEmail)
    {
        $this->remitterEmail = $remitterEmail;

        return $this;
    }

    /**
     * Get remitterEmail
     *
     * @return string 
     */
    public function getRemitterEmail()
    {
        return $this->remitterEmail;
    }

    /**
     * Set remitterCity
     *
     * @param string $remitterCity
     * @return Transactions
     */
    public function setRemitterCity($remitterCity)
    {
        $this->remitterCity = $remitterCity;

        return $this;
    }

    /**
     * Get remitterCity
     *
     * @return string 
     */
    public function getRemitterCity()
    {
        return $this->remitterCity;
    }

    /**
     * Set remitterCountry
     *
     * @param string $remitterCountry
     * @return Transactions
     */
    public function setRemitterCountry($remitterCountry)
    {
        $this->remitterCountry = $remitterCountry;

        return $this;
    }

    /**
     * Get remitterCountry
     *
     * @return string 
     */
    public function getRemitterCountry()
    {
        return $this->remitterCountry;
    }

    /**
     * Set remitterState
     *
     * @param string $remitterState
     * @return Transactions
     */
    public function setRemitterState($remitterState)
    {
        $this->remitterState = $remitterState;

        return $this;
    }

    /**
     * Get remitterState
     *
     * @return string 
     */
    public function getRemitterState()
    {
        return $this->remitterState;
    }

    /**
     * Set remitterAddress
     *
     * @param string $remitterAddress
     * @return Transactions
     */
    public function setRemitterAddress($remitterAddress)
    {
        $this->remitterAddress = $remitterAddress;

        return $this;
    }

    /**
     * Get remitterAddress
     *
     * @return string 
     */
    public function getRemitterAddress()
    {
        return $this->remitterAddress;
    }

    /**
     * Set remitterPhoneMobile
     *
     * @param string $remitterPhoneMobile
     * @return Transactions
     */
    public function setRemitterPhoneMobile($remitterPhoneMobile)
    {
        $this->remitterPhoneMobile = $remitterPhoneMobile;

        return $this;
    }

    /**
     * Get remitterPhoneMobile
     *
     * @return string 
     */
    public function getRemitterPhoneMobile()
    {
        return $this->remitterPhoneMobile;
    }

    /**
     * Set remitterPhoneLandline
     *
     * @param string $remitterPhoneLandline
     * @return Transactions
     */
    public function setRemitterPhoneLandline($remitterPhoneLandline)
    {
        $this->remitterPhoneLandline = $remitterPhoneLandline;

        return $this;
    }

    /**
     * Get remitterPhoneLandline
     *
     * @return string 
     */
    public function getRemitterPhoneLandline()
    {
        return $this->remitterPhoneLandline;
    }

    /**
     * Set remitterPostalCode
     *
     * @param string $remitterPostalCode
     * @return Transactions
     */
    public function setRemitterPostalCode($remitterPostalCode)
    {
        $this->remitterPostalCode = $remitterPostalCode;

        return $this;
    }

    /**
     * Get remitterPostalCode
     *
     * @return string 
     */
    public function getRemitterPostalCode()
    {
        return $this->remitterPostalCode;
    }

    /**
     * Set remitterAccountNumber
     *
     * @param integer $remitterAccountNumber
     * @return Transactions
     */
    public function setRemitterAccountNumber($remitterAccountNumber)
    {
        $this->remitterAccountNumber = $remitterAccountNumber;

        return $this;
    }

    /**
     * Get remitterAccountNumber
     *
     * @return integer 
     */
    public function getRemitterAccountNumber()
    {
        return $this->remitterAccountNumber;
    }

    /**
     * Set remitterBankRoutingNo
     *
     * @param integer $remitterBankRoutingNo
     * @return Transactions
     */
    public function setRemitterBankRoutingNo($remitterBankRoutingNo)
    {
        $this->remitterBankRoutingNo = $remitterBankRoutingNo;

        return $this;
    }

    /**
     * Get remitterBankRoutingNo
     *
     * @return integer 
     */
    public function getRemitterBankRoutingNo()
    {
        return $this->remitterBankRoutingNo;
    }

    /**
     * Set remitterBankBranch
     *
     * @param string $remitterBankBranch
     * @return Transactions
     */
    public function setRemitterBankBranch($remitterBankBranch)
    {
        $this->remitterBankBranch = $remitterBankBranch;

        return $this;
    }

    /**
     * Get remitterBankBranch
     *
     * @return string 
     */
    public function getRemitterBankBranch()
    {
        return $this->remitterBankBranch;
    }

    /**
     * Set remitterBankName
     *
     * @param string $remitterBankName
     * @return Transactions
     */
    public function setRemitterBankName($remitterBankName)
    {
        $this->remitterBankName = $remitterBankName;

        return $this;
    }

    /**
     * Get remitterBankName
     *
     * @return string 
     */
    public function getRemitterBankName()
    {
        return $this->remitterBankName;
    }

    /**
     * Set remitterBankAccountType
     *
     * @param string $remitterBankAccountType
     * @return Transactions
     */
    public function setRemitterBankAccountType($remitterBankAccountType)
    {
        $this->remitterBankAccountType = $remitterBankAccountType;

        return $this;
    }

    /**
     * Get remitterBankAccountType
     *
     * @return string 
     */
    public function getRemitterBankAccountType()
    {
        return $this->remitterBankAccountType;
    }

    /**
     * Set remitterIdNumber
     *
     * @param integer $remitterIdNumber
     * @return Transactions
     */
    public function setRemitterIdNumber($remitterIdNumber)
    {
        $this->remitterIdNumber = $remitterIdNumber;

        return $this;
    }

    /**
     * Get remitterIdNumber
     *
     * @return integer 
     */
    public function getRemitterIdNumber()
    {
        return $this->remitterIdNumber;
    }

    /**
     * Set remitterIdType
     *
     * @param string $remitterIdType
     * @return Transactions
     */
    public function setRemitterIdType($remitterIdType)
    {
        $this->remitterIdType = $remitterIdType;

        return $this;
    }

    /**
     * Get remitterIdType
     *
     * @return string 
     */
    public function getRemitterIdType()
    {
        return $this->remitterIdType;
    }

    /**
     * Set remitterIdIssuedCountry
     *
     * @param string $remitterIdIssuedCountry
     * @return Transactions
     */
    public function setRemitterIdIssuedCountry($remitterIdIssuedCountry)
    {
        $this->remitterIdIssuedCountry = $remitterIdIssuedCountry;

        return $this;
    }

    /**
     * Get remitterIdIssuedCountry
     *
     * @return string 
     */
    public function getRemitterIdIssuedCountry()
    {
        return $this->remitterIdIssuedCountry;
    }

    /**
     * Set remitterIdIssuedCity
     *
     * @param string $remitterIdIssuedCity
     * @return Transactions
     */
    public function setRemitterIdIssuedCity($remitterIdIssuedCity)
    {
        $this->remitterIdIssuedCity = $remitterIdIssuedCity;

        return $this;
    }

    /**
     * Get remitterIdIssuedCity
     *
     * @return string 
     */
    public function getRemitterIdIssuedCity()
    {
        return $this->remitterIdIssuedCity;
    }

    /**
     * Set remitterIdIssuedState
     *
     * @param string $remitterIdIssuedState
     * @return Transactions
     */
    public function setRemitterIdIssuedState($remitterIdIssuedState)
    {
        $this->remitterIdIssuedState = $remitterIdIssuedState;

        return $this;
    }

    /**
     * Get remitterIdIssuedState
     *
     * @return string 
     */
    public function getRemitterIdIssuedState()
    {
        return $this->remitterIdIssuedState;
    }

    /**
     * Set remitterIdIssuedDate
     *
     * @param \DateTime $remitterIdIssuedDate
     * @return Transactions
     */
    public function setRemitterIdIssuedDate($remitterIdIssuedDate)
    {
        $this->remitterIdIssuedDate = $remitterIdIssuedDate;

        return $this;
    }

    /**
     * Get remitterIdIssuedDate
     *
     * @return \DateTime 
     */
    public function getRemitterIdIssuedDate()
    {
        return $this->remitterIdIssuedDate;
    }

    /**
     * Set remitterIdExpiryDate
     *
     * @param \DateTime $remitterIdExpiryDate
     * @return Transactions
     */
    public function setRemitterIdExpiryDate($remitterIdExpiryDate)
    {
        $this->remitterIdExpiryDate = $remitterIdExpiryDate;

        return $this;
    }

    /**
     * Get remitterIdExpiryDate
     *
     * @return \DateTime 
     */
    public function getRemitterIdExpiryDate()
    {
        return $this->remitterIdExpiryDate;
    }

    /**
     * Set remittingAgentId
     *
     * @param integer $remittingAgentId
     * @return Transactions
     */
    public function setRemittingAgentId($remittingAgentId)
    {
        $this->remittingAgentId = $remittingAgentId;

        return $this;
    }

    /**
     * Get remittingAgentId
     *
     * @return integer 
     */
    public function getRemittingAgentId()
    {
        return $this->remittingAgentId;
    }

    /**
     * Set remittingAgentName
     *
     * @param string $remittingAgentName
     * @return Transactions
     */
    public function setRemittingAgentName($remittingAgentName)
    {
        $this->remittingAgentName = $remittingAgentName;

        return $this;
    }

    /**
     * Get remittingAgentName
     *
     * @return string 
     */
    public function getRemittingAgentName()
    {
        return $this->remittingAgentName;
    }

    /**
     * Set remittingAgentCity
     *
     * @param string $remittingAgentCity
     * @return Transactions
     */
    public function setRemittingAgentCity($remittingAgentCity)
    {
        $this->remittingAgentCity = $remittingAgentCity;

        return $this;
    }

    /**
     * Get remittingAgentCity
     *
     * @return string 
     */
    public function getRemittingAgentCity()
    {
        return $this->remittingAgentCity;
    }

    /**
     * Set remittingAgentState
     *
     * @param string $remittingAgentState
     * @return Transactions
     */
    public function setRemittingAgentState($remittingAgentState)
    {
        $this->remittingAgentState = $remittingAgentState;

        return $this;
    }

    /**
     * Get remittingAgentState
     *
     * @return string 
     */
    public function getRemittingAgentState()
    {
        return $this->remittingAgentState;
    }

    /**
     * Set remittingAgentCountry
     *
     * @param string $remittingAgentCountry
     * @return Transactions
     */
    public function setRemittingAgentCountry($remittingAgentCountry)
    {
        $this->remittingAgentCountry = $remittingAgentCountry;

        return $this;
    }

    /**
     * Get remittingAgentCountry
     *
     * @return string 
     */
    public function getRemittingAgentCountry()
    {
        return $this->remittingAgentCountry;
    }

    /**
     * Set processingStatus
     *
     * @param string $processingStatus
     * @return Transactions
     */
    public function setProcessingStatus($processingStatus)
    {
        $this->processingStatus = $processingStatus;

        return $this;
    }

    /**
     * Get processingStatus
     *
     * @return string 
     */
    public function getProcessingStatus()
    {
        return $this->processingStatus;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Transactions
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
     * @return Transactions
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
     * @var string
     */
    private $uuid;

    /**
     * @var \DateTime
     */
    private $beneficiaryDob;

    /**
     * @var string
     */
    private $beneficiarySsn;

    /**
     * @var string
     */
    private $beneficiaryGender;

    /**
     * @var string
     */
    private $beneficiaryOccupation;

    /**
     * @var \DateTime
     */
    private $remitterDob;

    /**
     * @var string
     */
    private $remitterSsn;

    /**
     * @var string
     */
    private $remitterGender;

    /**
     * @var string
     */
    private $remitterOccupation;

    /**
     * @var string
     */
    private $queueOperation;


    /**
     * Set uuid
     *
     * @param string $uuid
     * @return Transactions
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string 
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set beneficiaryDob
     *
     * @param \DateTime $beneficiaryDob
     * @return Transactions
     */
    public function setBeneficiaryDob($beneficiaryDob)
    {
        $this->beneficiaryDob = $beneficiaryDob;

        return $this;
    }

    /**
     * Get beneficiaryDob
     *
     * @return \DateTime 
     */
    public function getBeneficiaryDob()
    {
        return $this->beneficiaryDob;
    }

    /**
     * Set beneficiarySsn
     *
     * @param string $beneficiarySsn
     * @return Transactions
     */
    public function setBeneficiarySsn($beneficiarySsn)
    {
        $this->beneficiarySsn = $beneficiarySsn;

        return $this;
    }

    /**
     * Get beneficiarySsn
     *
     * @return string 
     */
    public function getBeneficiarySsn()
    {
        return $this->beneficiarySsn;
    }

    /**
     * Set beneficiaryGender
     *
     * @param string $beneficiaryGender
     * @return Transactions
     */
    public function setBeneficiaryGender($beneficiaryGender)
    {
        $this->beneficiaryGender = $beneficiaryGender;

        return $this;
    }

    /**
     * Get beneficiaryGender
     *
     * @return string 
     */
    public function getBeneficiaryGender()
    {
        return $this->beneficiaryGender;
    }

    /**
     * Set beneficiaryOccupation
     *
     * @param string $beneficiaryOccupation
     * @return Transactions
     */
    public function setBeneficiaryOccupation($beneficiaryOccupation)
    {
        $this->beneficiaryOccupation = $beneficiaryOccupation;

        return $this;
    }

    /**
     * Get beneficiaryOccupation
     *
     * @return string 
     */
    public function getBeneficiaryOccupation()
    {
        return $this->beneficiaryOccupation;
    }

    /**
     * Set remitterDob
     *
     * @param \DateTime $remitterDob
     * @return Transactions
     */
    public function setRemitterDob($remitterDob)
    {
        $this->remitterDob = $remitterDob;

        return $this;
    }

    /**
     * Get remitterDob
     *
     * @return \DateTime 
     */
    public function getRemitterDob()
    {
        return $this->remitterDob;
    }

    /**
     * Set remitterSsn
     *
     * @param string $remitterSsn
     * @return Transactions
     */
    public function setRemitterSsn($remitterSsn)
    {
        $this->remitterSsn = $remitterSsn;

        return $this;
    }

    /**
     * Get remitterSsn
     *
     * @return string 
     */
    public function getRemitterSsn()
    {
        return $this->remitterSsn;
    }

    /**
     * Set remitterGender
     *
     * @param string $remitterGender
     * @return Transactions
     */
    public function setRemitterGender($remitterGender)
    {
        $this->remitterGender = $remitterGender;

        return $this;
    }

    /**
     * Get remitterGender
     *
     * @return string 
     */
    public function getRemitterGender()
    {
        return $this->remitterGender;
    }

    /**
     * Set remitterOccupation
     *
     * @param string $remitterOccupation
     * @return Transactions
     */
    public function setRemitterOccupation($remitterOccupation)
    {
        $this->remitterOccupation = $remitterOccupation;

        return $this;
    }

    /**
     * Get remitterOccupation
     *
     * @return string 
     */
    public function getRemitterOccupation()
    {
        return $this->remitterOccupation;
    }

    /**
     * Set queueOperation
     *
     * @param string $queueOperation
     * @return Transactions
     */
    public function setQueueOperation($queueOperation)
    {
        $this->queueOperation = $queueOperation;

        return $this;
    }

    /**
     * Get queueOperation
     *
     * @return string 
     */
    public function getQueueOperation()
    {
        return $this->queueOperation;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $queues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->queues = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add queues
     *
     * @param \BackendBundle\Entity\OperationsQueue $queues
     * @return Transactions
     */
    public function addQueue(\BackendBundle\Entity\OperationsQueue $queues)
    {
        $this->queues[] = $queues;

        return $this;
    }

    /**
     * Remove queues
     *
     * @param \BackendBundle\Entity\OperationsQueue $queues
     */
    public function removeQueue(\BackendBundle\Entity\OperationsQueue $queues)
    {
        $this->queues->removeElement($queues);
    }

    /**
     * Get queues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQueues()
    {
        return $this->queues;
    }
    /**
     * @var \BackendBundle\Entity\Transactions
     */
    private $parentTransaction;


    /**
     * Set parentTransaction
     *
     * @param \BackendBundle\Entity\Transactions $parentTransaction
     * @return Transactions
     */
    public function setParentTransaction(\BackendBundle\Entity\Transactions $parentTransaction = null)
    {
        $this->parentTransaction = $parentTransaction;

        return $this;
    }

    /**
     * Get parentTransaction
     *
     * @return \BackendBundle\Entity\Transactions 
     */
    public function getParentTransaction()
    {
        return $this->parentTransaction;
    }
    /**
     * @var string
     */
    private $payoutPayerName;

    /**
     * @var string
     */
    private $payoutPayerCode;


    /**
     * Set payoutPayerName
     *
     * @param string $payoutPayerName
     * @return Transactions
     */
    public function setPayoutPayerName($payoutPayerName)
    {
        $this->payoutPayerName = $payoutPayerName;

        return $this;
    }

    /**
     * Get payoutPayerName
     *
     * @return string 
     */
    public function getPayoutPayerName()
    {
        return $this->payoutPayerName;
    }

    /**
     * Set payoutPayerCode
     *
     * @param string $payoutPayerCode
     * @return Transactions
     */
    public function setPayoutPayerCode($payoutPayerCode)
    {
        $this->payoutPayerCode = $payoutPayerCode;

        return $this;
    }

    /**
     * Get payoutPayerCode
     *
     * @return string 
     */
    public function getPayoutPayerCode()
    {
        return $this->payoutPayerCode;
    }
}
