<?php

class doc_table {
		
		public $table;
		
		function __construct($columns){
				$this->cols=$columns;
				$this->row=0;
				$this->cel=0;
				$this->table="<table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0 
									 style='width:100%; border-collapse:collapse;mso-yfti-tbllook:1184;mso-padding-alt:0cm 0cm 0cm 0cm;
									 mso-border-insideh:.5pt solid windowtext;mso-border-insidev:.5pt solid windowtext'>";
				for ($i=0;$i<$columns;$i++){
						//$this->table.='<td>';
				}
		}
		
		function add_row(){
				$this->table.=' <tr>';
		}
		function end_row(){
				$this->table.='</tr>';
				$this->row++;
				$this->cel=0;
		}
		
		function add_cell($cell_data,$bold=false,$lastline=false,$span=false,$datatype="string"){
				
				if ($bold){
					$b1="<b>";
					$b2="</b>";
				} else {
					$b1="";
					$b2="";
				}
				
				if ($span){
						$cs="colspan={$span}";
						$this->cel = ($this->cel-1)+$span;
				} else {
						$cs='';
				}
				
				$this->table.=	"<td {$cs} style='padding:3px;'><p class=MsoNormal>".
								"<span style='font-family:\"Arial\",\"sans-serif\";mso-fareast-font-family:Arial'>{$b1}{$cell_data}{$b2}</span></p></td>\n";
				$this->cel++;
		}
		
		function end_table(){
				$this->table.="</table>";
		}
}

?>
