<?php
	include 'dump_r.php';

	class lib {
		public static function myFn() {}
	}

	class myObject {
		public		$cow_publ = 999;
		protected	$moo_prot = 666;
		private		$cat_priv = 555;
		static		$dog_stat = 444;

		protected	$self;

		public function  __construct() {
			$this->self = $this;
		}
	}

	$obj = new myObject;

	$obj->id = 12345;
	$obj->name = 'test string';
	$obj->name2 = '';
	$obj->name3 = '0';
	$obj->name4 = 'false';
	$obj->name5 = 'null';
	$obj->price = 69.95;
	$obj->address = new stdClass;
	$obj->address->street = '111 Any Sreet';
	$obj->address->zip = 60657;
	$obj->address->city = 'Chicago';
	$obj->ref_to_addr = $obj->address;
	$obj->kids = array(
		0		=> 'blah',
		'a'		=> null,
		'xxx'	=> new myObject,
	);
	$obj->date_str = '2011-12-13 15:25:03';
	$obj->not_date = '123456';
	$obj->otherSet = array();
	$obj->isFull = false;
	$obj->food = null;
	$obj->dom = new DOMDocument;
	$obj->xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><root><moo attr="myAttribute">f</moo><moo2>g</moo2><sss>55.9</sss></root>');
	$obj->afile = fopen(__FILE__, 'r');

	$obj->call_anon = function($a) {};
	$obj->call_static = 'lib::myFn';
	$obj->call_array = array('lib' ,'myFn');
	$obj->notfn = 'pi';		// global functions are excluded in favor of not mis-interpreting strings

	$sub = new stdClass;
	$sub->a = 'moo';
	$sub->b = false;
	$sub->c = array('hello', 'world', 2.98, null);
	$sub->d = "75";

	$obj->json_arr_str = json_encode(array(true,false,null,$sub));
	$obj->json_obj_str = json_encode($sub);
	$obj->xml_str = '<?xml version="1.0" encoding="utf-8"?><root><moo attr="myAttribute">f</moo><moo2>g</moo2><sss>55.9</sss></root>';
?>
<h1>dump_r()</h1>

<h2>no depth restriction</h2>
<?php dump_r($obj); ?>

<h2>with depth restriction + custom marking</h2>
<style>
	.dump_r .myVals > * {
		background: lightyellow !important;
	}
</style>
<?php dump_r($obj, 1, function($inp) {return $inp == '999' || $inp == '123456' ? 'myVals' : '';}); ?>