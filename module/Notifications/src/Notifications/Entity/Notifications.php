<?php
namespace Notifications\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


use DateTime;

/** 
	* @ORM\Entity
	* @ORM\Table(name="notifications")	
*/
class Notifications {
    /**
	 * @ORM\Id
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $message;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notifywho = null;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $linkroute = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Event\Entity\User")
	 * @ORM\JoinColumn(referencedColumnName="user_id")
	 */
	protected $notifyuser = null;
	
	
	public function getId(){
		return $this->id;
	}
	
	public function getNotifyuser(){		
		return $this->notifyuser;
	}
	
	public function setNotifyuser(\Event\Entity\User $user){
		$this->notifyuser = $user;
	}
	
	public function getLinkroute(){
		return $this->linkroute;
	}
	
	public function setLinkroute($linkroute){
		$this->linkroute = $linkroute;
	}
	
	public function setMessage($message){
		$this->message = $message;
	}
	
	public function getMessage(){
		return $this->message;
	}
	
	public function getNotifywho(){
		return $this->notifywho;
	}
	
	public function setNotifywho($notifywho){
		$this->notifywho = $notifywho;
	}
	
	
	
	
	public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}