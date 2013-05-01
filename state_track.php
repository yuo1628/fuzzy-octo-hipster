<?php

    /*
	 * StateTrack
	 * 
	 * 負責紀錄類別的動作
	 * 
	 * State的格式為：array(狀態數字，動作說明)
	 * 
	 */
	 
	 
	 class StateTrack {
	 	
		// 紀錄類別動作的變數
		private $states = array();
		
		// 新增狀態紀錄
		protected function writeState($state) {
			$this->states[] = $state;
		}
		
		// 取得至目前為止的狀態紀錄
		public function getStates() {
			return $this->states;
		}
	 }
	 
	 /*
	  * AutoStateTrack
	  * 
	  * 自動紀錄類別的動作
	  * 當要追蹤的類別被操作時，此類別會自動紀錄該動作
	  * 
	  * 狀態數字說明：
	  *  - 1：方法呼叫
	  *  - 2：屬性值被指定
	  * 
	  */
	  class AutoStateTrack extends StateTrack {
	  	
		// 要追蹤的物件
		private $trackObject;
		
		public function __construct($trackObject) {
			$this->trackObject = $trackObject;	
		}
		
		public function __call($name, $args) {
			call_user_func_array(array($this->trackObject, $name), $args);
			// 建立args repr表示方式的陣列
			$varsArray = array();
			foreach ($args as $arg) {
				$varsArray[] = var_export($arg, True);
			}
			$this->writeState(array(1, "{$name}(" . implode(", ", $varsArray) . ')'));
		}
	  }
?>