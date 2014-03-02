<?php
namespace Event\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Event\Entity\User;
use Event\Entity\Event;
use Event\Entity\Inventory;
use DateTime;

/** 
	* @ORM\Entity
	* @ORM\Table(name="inventorylog")	
*/
class InventoryLog {
    /**
	 * @ORM\Id
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;
	
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
	 * @ORM\Column(type="datetime")
	 */
	protected $date;
	
	/**
     * @ORM\Column(type="integer")
     */
    protected $stock = 0;
	
	/**
     * @ORM\Column(type="integer")
     */
    protected $currentstock = 0;
	
	
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
	
	public function getStock(){
		return $this->stock;		
	}
	
	public function setStock($stock){
		$this->stock = $stock;
	}
	
	public function getCurrentstock(){
		return $this->currentstock;		
	}
	
	public function setCurrentstock($stock){
		$this->currentstock = $stock;
	}
	
	public function getInventory(){
		return $this->inventory;
	}
	
	public function setInventory($inventory){
		$this->inventory = $inventory;
	}
	
	public function getDate(){
		return $this->date;
	}
	
	public function setDate(DateTime $date){
		$this->date = $date;
	}
	
	public function exchangeArray($data)
    {
		$this->user_id     = (isset($data['user_id'])) ? $data['user_id'] : 0;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->id  = (isset($data['id'])) ? $data['id'] : 0;
		$this->stock  = (isset($data['stock'])) ? $data['stock'] : 0;
		$this->date  = (isset($data['date'])) ? $data['date'] : 0;
    }
	
	public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}