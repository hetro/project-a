<?php
namespace Event\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/** 
	* @ORM\Entity
	* @ORM\Table(name="user")	
*/
class User {
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer", length=10, nullable=false)
    */
    protected $user_id;
	
	/**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    protected $username = null;
	
	/**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    protected $email = null;
	
	/**
     * @ORM\Column(name="display_name", type="string", length=50, nullable=true)
     */
	protected $display_name;
	
	/**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
	protected $password;
	
	/**
     * @ORM\Column(type="integer", nullable=true)
     */
	protected $state = 0;
	
	public function getDisplayName(){
		$displayName = $this->display_name;
        if (null === $displayName) {
            $displayName = $this->username;
        }
        if (null === $displayName) {
            $displayName = $this->email;
            $displayName = substr($displayName, 0, strpos($displayName, '@'));
        }

        return $displayName;
	}
	
	public function setDisplayName($displayName){
		$this->display_name = $displayName;
	}
	
    public function getUserId(){		
		return $this->user_id;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function setUsername($username){
		$this->username = $username;
	}
	
	public function getEmail(){
		
		return $this->email;
	}
	
	public function setEmail($email){
		$this->email = $email;
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function setPassword($password){
		$this->password = $password;
	}
	
	public function getState(){
		return $this->state;
	}
	
	public function setState($state){
		$this->state = $state;
	}
	
	public function exchangeArray($data)
    {
		$this->user_id     = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->email  = (isset($data['email'])) ? $data['email'] : null;
		$this->display_name  = (isset($data['display_name'])) ? $data['display_name'] : null;
		$this->password  = (isset($data['password'])) ? $data['password'] : null;
		$this->state  = (isset($data['state'])) ? $data['state'] : null;
		$this->total_credit  = (isset($data['total_credit'])) ? $data['total_credit'] : 0;
    }
	
	public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}