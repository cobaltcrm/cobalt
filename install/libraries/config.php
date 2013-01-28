<?php

class crmConfig{

	public function set($var,$val){
		$this->$var = $val;
	}

	public function get($var){
		return $this->$var;
	}

	public function toArray(){
		$arr = array();
		foreach (get_object_vars($this) as $k => $v)
		{
			$arr[$k]=$v;
		}
		return $arr;
	}

	public function toString()
	{
		// Build the object variables string
		$vars = '';
		foreach (get_object_vars($this) as $k => $v)
		{
			if (is_scalar($v))
			{
				$vars .= "\tpublic $" . $k . " = '" . addcslashes($v, '\\\'') . "';\n";
			}
			elseif (is_array($v) || is_object($v))
			{
				$vars .= "\tpublic $" . $k . " = " . $this->getArrayString((array) $v) . ";\n";
			}
		}

		$str = "<?php\nclass JConfig {\n";
		$str .= $vars;
		$str .= "}";

		return $str;
	}

	protected function getArrayString($a)
	{
		$s = 'array(';
		$i = 0;
		foreach ($a as $k => $v)
		{
			$s .= ($i) ? ', ' : '';
			$s .= '"' . $k . '" => ';
			if (is_array($v) || is_object($v))
			{
				$s .= $this->getArrayString((array) $v);
			}
			else
			{
				$s .= '"' . addslashes($v) . '"';
			}
			$i++;
		}
		$s .= ')';
		return $s;
	}

}