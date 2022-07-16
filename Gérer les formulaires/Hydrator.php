<?php
namespace OCFram;
trait Hydrator {
	public function hydrate($data) {
		foreach($data as $attr => $value) {
			$method = 'set' . ucfirst($attr);
			if(is_callable([$this], $method))
				$this>$method($value);
		}
	}
}