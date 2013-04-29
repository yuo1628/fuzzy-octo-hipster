<?php
	require './state_track.php'; // ignore some search paths
	/* 倒水問題中的瓶子
	*  
	 * 瓶子可作三種操作：
	 *   1. 裝滿水
	 *   2. 將水倒光
	 *   3. 將水倒入另一個瓶子
	*/
    class Bottle extends StateTrack {
    	
		 
		// 表示瓶子最多能裝多少水
    	private $capacity;
		// 表示目前裝了多少水
		private $currentHold = 0;
		// 表示到目前為止的倒水動作
		// 一個state的格式為array(currentHold, "actionDescription")
		private $states = array();
		
		public function __construct($capacity) {
			$this->capacity = $capacity;
		}
		
    	// 把水倒光
    	public function drain() {
    		$this->setCurrentHold(0, "把水倒光");
    	}
		
		// 把水裝滿
		public function fill() {
			$this->setCurrentHold($this->getCapacity(), "把水裝滿");
		}
		
		// 取得瓶子最大能裝多少水
		public function getCapacity() {
			return $this->capacity;
		}
		
		// 取得瓶子還能裝多少水
		public function getRemainCapacity() {
			return $this->getCapacity() - $this->getCurrentHold();
		}
		
		// 設定目前裝了多少水
		private function setCurrentHold($currentHold, $description) {
			$this->currentHold = $currentHold;
			$this->writeState(array($currentHold, $description));
		}
		
		// 取得瓶子目前裝了多少水
		public function getCurrentHold() {
			return $this->currentHold;
		}
		
		// 取得最後一個動作紀錄
		public function getLastState() {
			return $this->states[count($this->states) - 1];
		}
		
		// 把水倒至另一個瓶子
		public function pour($otherBottle) {
			$otherBottleRemainCapacity = $otherBottle->getRemainCapacity();
			$otherBottleCurrentHold = $otherBottle->getCurrentHold();
			// 剩餘倒水倒不滿另一個瓶子
			if ($this->currentHold < $otherBottleRemainCapacity) {
				$otherBottle->setCurrentHold($otherBottleCurrentHold + $this->getCurrentHold(),
											 "另一個瓶子倒{$this->currentHold}單位的水至此瓶子");
				$this->setCurrentHold(0, "倒剩餘單位的水至另一個瓶子");
			}
			// 別的瓶子會被倒滿，自己的瓶子有可能會剩下水
			elseif ($this->currentHold >= $otherBottleRemainCapacity) {
				$pouringVolume = $this->currentHold - $otherBottleRemainCapacity;
				$otherBottle->setCurrentHold($otherBottleCurrentHold + $pouringVolume,
											 "另一個瓶子倒{$pouringVolume}單位的水至此瓶子");
				$this->setCurrentHold($this->getCurrentHold() - $pouringVolume, "倒滿另一個瓶子");
			}
		}
		
		// 測試最後的狀態與指定的瓶子是否相同
		// 不比較動作說明
		public function isSameLastState($otherBottle) {
			$otherBottleLastState = $otherBottle->getLastState();
			$lastState = $this->getLastState();
			return $otherBottleLastState[0] == $lastState[0];
		}
    }

	/*
	 * 類別測試
	 */
	$bottle1 = new Bottle(9);
	$bottle2 = new Bottle(6);
	//$autoTrack2 = new AutoStateTrach($bottle2);
	$bottle2->fill();
	$bottle2->pour($bottle1);
	$bottle2->fill();
	$bottle2->pour($bottle1);
	$bottle1->drain();
	echo '<pre>';
	print_r($bottle2->getStates());
	print_r($bottle1->getStates());
	//print_r($autoTrack2->getStates());
	echo '</pre>';
	//$bottle2->setCurrentHold(2);
?>