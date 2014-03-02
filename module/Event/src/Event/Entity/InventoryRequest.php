<?php
namespace Event\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Event\Entity\User;
use Event\Entity\Event;
use Event\Entity\Inventory;
use DateTime;

/** 
	* @ORM\Entity
	* @ORM\Table(name="inventoryrequest")	
*/
class InventoryRequest {
    /**
	 * @ORM\Id
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;
	
	/**
     * @ORM\ManyToOne(targetEntity="Event\Entity\Event", inversedBy="borrows")
     */
    protected $event;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $description = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Inventory")
	 * @ORM\JoinColumn(referencedColumnName="id")
	 */
	protected $inventory;
	
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
     * @ORM\Column(type="integer")
     */
    protected $amount = 0;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status = 'Pending';
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $note = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $dateofreturn;
	
	/**
     * Allow null to remove association
     *
     * @param BlogPost $blogPost
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;
    }

    /**
     * @return BlogPost
     */
    public function getEvent()
    {
        return $this->event;
    }
	
	public function getId(){
		return $this->id;
	}
	
	public function getDescription(){
		return $this->description;
	}
	
	public function setDescription($description){
		$this->description = $description;
	}
	
    public function getUser(){		
		return $this->user;
	}
	
	public function setUser(User $user){
		$this->user = $user;
	}
	
	public function getAmount(){
		return (int)$this->amount;		
	}
	
	public function setAmount($amount){
		$this->amount = $amount;
	}
	
	public function getInventory(){
		return $this->inventory;
	}
	
	public function setInventory(Inventory $inventory){
		$this->inventory = $inventory;
	}
	
	public function getDateofreturn(){
		return $this->dateofreturn;
	}
	
	public function setDateofreturn($date){
		$this->dateofreturn = $date;
	}
	
	public function getReviewedBy(){
		return $this->reviewedby;
	}
	
	public function setReviewedBy($reviewedby){
		$this->reviewedby = $reviewedby;
	}
	
	public function getStatus(){
		return $this->status;
	}
	
	public function setStatus($status){
		$this->status = $status;
	}
	
	public function getNote(){
		return $this->note;
	}
	
	public function setNote($note){
		$this->note = $note;	
	}
	
	public function exchangeArray($data)
    {
		$this->user_id     = (isset($data['user_id'])) ? $data['user_id'] : 0;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->id  = (isset($data['id'])) ? $data['id'] : 0;
		$this->stock  = (isset($data['stock'])) ? $data['stock'] : 0;
		$this->date  = (isset($data['date'])) ? $data['date'] : 0;
		$this->reviewedby  = (isset($data['reviewedby'])) ? $data['reviewedby'] : 0;
		$this->note  = (isset($data['note'])) ? $data['note'] : 0;
		$this->dateofreturn  = (isset($data['dateofreturn'])) ? $data['dateofreturn'] : null;
    }
	
	public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}