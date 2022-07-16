<?php
class SuperIterator implements ArrayAccess, SeekableIterator, Countable {
	private $_pos = 0,
			$_indexes = [],
			$_reflect;
	
	public function __construct(array $data = array()) {
		$this->_reflect = new ReflectionClass(static::class);
		$properties = $this->_reflect->getProperties();
		foreach($properties as $property) {
			$name = $property->getName();
			if($name != '_pos' && $name != '_indexes' && $name != '_reflect') {
				$method = 'get' . ucfirst($name);
				if($this->_reflect->hasMethod($name)) 
					$reflectMethod = $this->_reflect->getMethod($name);
				elseif($this->_reflect->hasMethod($method))
					$reflectMethod = $this->_reflect->getMethod($method);
				if(($this->_reflect->hasMethod($name) || $this->_reflect->hasMethod($method)) && $reflectMethod->isPublic()) 
					$this->_indexes[] = $name;
			}
		}
		
		if(!empty($data)) {
			foreach($data as $key => $value) {
				if($this->offsetExists($key))
					$this->offsetSet($key, $value);
			}
		}
		
	}
	
	public function offsetExists($cle) {
		return in_array($cle, $this->_indexes);
	}
	
	public function offsetGet($offset) {
		if(is_int($offset)) {
			if(array_key_exists($offset, $this->_indexes)) {
				$name = $this->_indexes[$offset];
				return $this->$name;
			}
		}
		return in_array($offset, $this->_indexes) ? $this->$offset : null;
	}
	
	public function offsetSet($offset, $value) {
		if(is_int($offset) && array_key_exists($offset, $this->_indexes)) 
			$method = 'set' . ucfirst($this->_indexes[$offset]);
		elseif(in_array($offset, $this->_indexes)) 
			$method = 'set' . ucfirst($offset);
		if($this->_reflect->hasMethod($method) && $this->_reflect->getMethod($method)->isPublic())
			$this->$method($value);
	}
	
	public function offsetUnset($offset) {
		throw new \RuntimeException('Impossible de supprimer une quelconque valeur');
	}
	
	public function current() {
		return $this->valid() ? $this[$this->_indexes[$this->_pos]] : null;
	}
	
	public function key() {
		return $this->_indexes[$this->_pos];
	}
	
	public function next() {
		$this->_pos++;
	}
	
	public function rewind() {
		$this->_pos = 0;
	}
	
	public function valid() {
		$bool = isset($this->_indexes[$this->_pos]);
		if($bool === false)
			$this->_pos = 0;
		return $bool;
	}
	
	public function count() {
		return count($this->_indexes);
	}
	
	public function seek($pos) {
		$oldPos = $this->_pos;
		$this->_pos = is_int($pos) ? $pos : array_search($pos, $this->_indexes);
		if(!$this->valid()) {
			trigger_error('La position spécifiée n\'est pas valide', E_USER_WARNING);
			$this->_pos = $oldPos;
		}
	}
	
	public function &iterator() {
		foreach($this->_indexes as $key => &$offset) {
			yield $key => $this->$offset;
		}
	}
}

class Test extends SuperIterator {
	protected $a = 1,
			  $b = 2,
			  $c = 3,
			  $d = 4,
			  $e = 5;
	public function setA($a) {
		$this->a = $a;
	}
	
	public function setB($b) { $this->b = $b; }
	
	public function setC($c) { $this->c = $c; }
	
	public function setD($d) { $this->d = $d; }
	
	public function setE($e) { $this->e = $e; }
	
	public function a() { return $this->a; }
	
	public function b() { return $this->b; }
	
	public function c() { return $this->c; }
	
	public function d() { return $this->d; }
	
	public function e() { return $this->e; }
}

$a = new Test(['a' => 'un', 'b' => 'deux', 'c' => 'trois', 'd' => 'quatre']);


foreach($a as $key => $value)
	echo  'a[' . $key .'] = ' . $value , '<br />';

echo $a['d'] . '<hr>';
//$a->rewind();
while($a->valid()) {
	echo $a->current() . '<br />';
	$a->next();
}

//$a->rewind();
echo '<hr>';
$a->seek(2);
echo 'after seek(2) : ' . $a->key() , '<hr>';
echo 'count : ' . $a->count() , '<br />' , '<hr>';
$a->rewind();
for($i = $a->key(); $a->valid(); $a->next()) {
	$i = $a->key();
	echo 'a[' . $i . '] = ' . $a[$i] , '<br />';
}

//$a->rewind();
echo '<hr>';
for($i = 0; $i < $a->count(); $i++) {
	$a[$i] = $i * 3;
	echo 'a[' . $i . '] = ' . $a[$i] , '<br />'; 
}

echo '<hr>';
$a['e'] = 'cinque';
echo $a['e'] , ' : moment de vérité'; // ma super classe sait distinguer les méthodes protected et public, contrairement à ArrayIterator

echo '<hr>';

foreach($a->iterator() as &$value) {
	$value = 'voilà voilà';
}


foreach($a as $value)
	echo $value , '<br>';