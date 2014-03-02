<?php
namespace Event\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Event\Entity\User;
use Event\Entity\InventoryRequest;
use DateTime;

/** 
	* @ORM\Entity
	* @ORM\Table(name="event")	
*/
class Event {
    /**
	 * @ORM\Id
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fullname = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(referencedColumnName="user_id")
	 */
	protected $user;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(referencedColumnName="user_id")
	 */
	protected $reviewedby;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $eventtitle = null;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $company = null;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $position = null;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $address = null;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $contact = null;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $purpose = null;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $venue = null;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $notes = null;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $concerns = null;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status = 'Pending';
	
	/**
     * @ORM\OneToMany(targetEntity="Event\Entity\InventoryRequest", mappedBy="event", cascade={"persist"})
     */
    protected $borrows;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $startofevent;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $endofevent;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $datecreated;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $report = null;
	
	public function __construct()
    {
        $this->borrows = new ArrayCollection();
    }
	
	public function getId(){
		return $this->id;
	}
	
	public function getFullName(){
		return $this->fullname;
	}
	
	public function setFullName($fullname){
		$this->fullname = $fullname;
	}
	
	public function getReport(){
		return $this->report;
	}
	
	public function setReport($report){
		$this->report = $report;
	}
	
    public function getUser(){		
		return $this->user;
	}
	
	public function setUser(User $user){
		$this->user = $user;
	}
	
	public function getEventTitle(){
		return $this->eventtitle;
	}
	
	public function setEventTitle($eventtitle){
		$this->eventtitle = $eventtitle;
	}
	
	public function getCompany(){
		return $this->company;
	}
	
	public function setCompany($company){
		$this->company = $company;
	}
	
	public function getPosition(){
		return $this->position;
	}
	
	public function setPosition($position){
		$this->position = $position;
	}
	
	public function getAddress(){
		return $this->address;
	}
	
	public function setAddress($address){
		$this->address = $address;
	}
	
	public function getContact(){
		return $this->contact;
	}
	
	public function setContact($contact){
		$this->contact = $contact;
	}
	
	public function getPurpose(){
		return $this->purpose;
	}
	
	public function setPurpose($purpose){
		$this->purpose = $purpose;
	}
	
	public function getVenue(){
		return $this->venue;
	}
	
	public function setVenue($venue){
		$this->venue = $venue;
	}
	
	public function getNotes(){
		return $this->notes;
	}
	
	public function setNotes($notes){
		$this->notes = $notes;
	}
	
	public function getStatus(){
		return $this->status;
	}
	
	public function setStatus($status){
		$this->status = $status;
	}
	
	public function getConcerns(){
		return $this->concerns;
	}
	
	public function setConcerns($concerns){
		$this->concerns = $concerns;
	}
	
	public function getReviewedBy(){
		return $this->reviewedby;
	}
	
	public function setReviewedBy($reviewedby){
		$this->reviewedby = $reviewedby;
	}
	
	/**
     * @param Collection $borrows
     */
    public function addBorrows(Collection $borrows)
    {
        foreach ($borrows as $borrow) {
			$borrow->setEvent($this);
            $this->borrows->add($borrow);
        }
    }

    /**
     * @param Collection $borrows
     */
    public function removeBorrows(Collection $borrows)
    {
        foreach ($borrows as $borrow) {
            $borrow->setEvent(null);
            $this->borrows->removeElement($borrow);
        }
    }

    /**
     * @return Collection
     */
    public function getBorrows()
    {
        return $this->borrows;
    }
	
	public function removeBorrow(InventoryRequest $borrow){
		$borrow->setEvent(null);
        $this->borrows->removeElement($borrow);
	}
	
	public function getStartOfEvent(){
		return $this->startofevent;
	}
	
	public function setStartOfEvent(DateTime $date){
		$this->startofevent = $date;
	}
	
	public function getEndOfEvent(){
		return $this->endofevent;
	}
	
	public function setEndOfEvent(DateTime $date){
		$this->endofevent = $date;
	}
	
	public function getDateCreated(){
		return $this->datecreated;
	}
	
	public function setDateCreated(DateTime $date){
		$this->datecreated = $date;
	}
	
	public function exchangeArray($data)
    {
		$this->user_id     = (isset($data['user_id'])) ? $data['user_id'] : 0;
        $this->eventtitle = (isset($data['eventtitle'])) ? $data['eventtitle'] : null;
        $this->id  = (isset($data['id'])) ? $data['id'] : 0;
		$this->fullname  = (isset($data['fullname'])) ? $data['fullname'] : null;
		$this->company  = (isset($data['company'])) ? $data['company'] : null;
		$this->position  = (isset($data['position'])) ? $data['position'] : null;
		$this->address  = (isset($data['address'])) ? $data['address'] : null;
		$this->contact  = (isset($data['contact'])) ? $data['contact'] : null;
		$this->purpose  = (isset($data['purpose'])) ? $data['purpose'] : null;
		$this->venue  = (isset($data['venue'])) ? $data['venue'] : null;
		$this->notes  = (isset($data['notes'])) ? $data['notes'] : null;
		$this->concerns  = (isset($data['concerns'])) ? $data['concerns'] : null;
		$this->status  = (isset($data['status'])) ? $data['status'] : null;
		$this->startofevent  = (isset($data['startofevent'])) ? $data['startofevent'] : null;
		$this->endofevent  = (isset($data['endofevent'])) ? $data['endofevent'] : null;
		$this->datecreated  = (isset($data['datecreated'])) ? $data['datecreated'] : null;
		$this->report  = (isset($data['report'])) ? $data['report'] : null;
    }
	
	public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}