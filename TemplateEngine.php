<?php 
 class html_generator{
 	public $buffer;
 	protected $arr_foreach;
 
 public function load_template($name){
 	$this->buffer = file_get_contents($name);
 }
 
 public function mount_vars($arr){
 	foreach ($arr as $parameter=>$value){
 		if(!is_array($value)) {
 			$this->buffer = str_replace('[% '.$parameter.' %]', $value, $this->buffer);
 		}
 	}
 	$this->arr_foreach = $arr;
 }

 public function if_compiler(){
 	$this->buffer = preg_replace('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}i(?!f)|[^if]\{[\s]*\/if[\s]*\}/','{ ***identification_if*** }', $this->buffer, 1);
 	if (preg_match('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}[\S\s]*\{ \*\*\*identification_if\*\*\* \}/', $this->buffer, $matches)){
 		$short_if = preg_replace('/\{[\s]*if[\s]*\([\s]*/','', $matches[0]);
 		$short_if = preg_replace('/[\s]*\)[\s]*\}[\S\s]*\{ \*\*\*identification_if\*\*\* \}/', '', $short_if);
		if(!preg_match('/\[\%[\S\s]*\%\]/',$short_if)){
		 	$value_end= '<br>';
		 	if (preg_match('/\{[\s]*else[\s]*\}/',$matches[0],$else_sidetrack)){
		 		if (eval("return $short_if ? true : false;")){
		 			$link_true = preg_replace ('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}/','', $matches[0]);
		 			$link_true = preg_replace ('/{[\s]*else[\s]*\}[.\S\s]*\{ \*\*\*identification_if\*\*\* \}/', $value_end, $link_true);
		 			$this->buffer = preg_replace('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}[\S\s]*\{ \*\*\*identification_if\*\*\* \}/', $link_true, $this->buffer);
		 		}else{
		 			$link_false = preg_replace ('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}[.\S\s]*{[\s]*else[\s]*\}/', '', $matches[0]);
		 			$link_false = preg_replace ('/\{ \*\*\*identification_if\*\*\* \}/', $value_end, $link_false);
		 			$this->buffer = preg_replace('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}[\S\s]*\{ \*\*\*identification_if\*\*\* \}/', $link_false, $this->buffer);
		 		}
		 	}else{
		 		if (eval("return $short_if ? true : false;")){
		 			$link_true = preg_replace ('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}/','', $matches[0]);
		 			$link_true = preg_replace ('/\{ \*\*\*identification_if\*\*\* \}/', $value_end, $link_true);
		 			$this->buffer = preg_replace('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}[\S\s]*\{ \*\*\*identification_if\*\*\* \}/', $link_true, $this->buffer);
		 		}else{
		 			$link_false = '<br>*** ! Negative result ! ***<br>';
					$this->buffer = preg_replace('/\{[\s]*if[\s]*\([\S\s]*\)[\s]*\}[\S\s]*\{ \*\*\*identification_if\*\*\* \}/', $link_false, $this->buffer);
	 			}
	 		}
		}else{
			$error_if = preg_replace('/[\S\s]*\[\%/', '[%', $short_if);
			$error_ifEnd = preg_replace('/\%\][\S\s]*/', '%]', $error_if);
			exit($error_ifEnd.'<br>Переменная не задана или заданна не верно!<br>Variable is not set or the job is not true!');
		}
	}
 	if (preg_match('/{[\s]*\/if[\s]*\}/',$this->buffer, $ind)){
 		$this->if_compiler();
 	}
 }

 
 public function foreach_compiler(){
 	$this->buffer = preg_replace('/{ \/foreach \}/','{ ***identification_foreach*** }', $this->buffer, 1);
 	if (preg_match('/\{[\s]*foreach[\s]*\([\S\s]*\)[\s]*\}[\S\s]*\{ \*\*\*identification_foreach\*\*\* \}/', $this->buffer, $matches)){
 		$foreach_mod = preg_replace('/\{ \*\*\*identification_foreach\*\*\* \}/', '\'; }', $matches[0]);
 		$foreach_mod = preg_replace('/[\s]*\{/', '', $foreach_mod);
 		$foreach_mod = preg_replace('/\) \}/',') { $tmp .= \'', $foreach_mod);
 		$search_array = preg_replace('/[\s]*foreach[\s]*\([\s]*\[\%[\s]*/','',$foreach_mod);
 		$search_array = preg_replace('/[\s]*\%\][\s]*as[\S\s]*/','',$search_array);
 		$arr_foreach = 'array(';
 		
 		foreach($this->arr_foreach[$search_array] as $k => $v) {
 			$arr_foreach .= $k . ' => ' . $v . ', ';
 		}
 		
 		$arr_foreach .= ')';
 		$foreach_mod = preg_replace('/foreach[\s]*\([\s]*\[\%[\S\s]*\%\][\s]*as/', 'foreach ('.$arr_foreach.' as', $foreach_mod);
 		$foreach_mod = preg_replace('/\[\%/', '\' .', $foreach_mod);
 		$foreach_mod = preg_replace('/\%\]/', '. \'', $foreach_mod);
 		$foreach_eval = '$tmp = \'\';' . $foreach_mod . 'return $tmp;';
 		//echo '<br>***<br>';print_r($foreach_eval);
 		$this->buffer = preg_replace('/\{[\s]*foreach[\s]*\([\S\s]*\)[\s]*\}[\S\s]*\{ \*\*\*identification_foreach\*\*\* \}/',eval($foreach_eval),$this->buffer);
 	}
 	if (preg_match('/{ \/foreach \}/',$this->buffer, $ind)){
 		$this->foreach_compiler($arr);
 	}
 }
 
 public function print_out() {
 	print_r($this->buffer);
 }
 
}

?>