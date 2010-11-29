<?php

class oo_table {
		
		public $table;
		
		function __construct($columns){
				$this->cols=$columns;
				$this->row=0;
				$this->cel=0;
				$this->table='<table:table table:name="oo_tbl_c" table:style-name="oo_tbl1">';
				for ($i=0;$i<$columns;$i++){
						$this->table.='<table:table-column table:style-name="oo_tbl1.A"/>';
				}
		}
		
		function add_row(){
				$this->table.=' <table:table-row>';
		}
		function end_row(){
				$this->table.='</table:table-row>';
				$this->row++;
				$this->cel=0;
		}
		
		function add_cell($cell_data,$bold=false,$lastline=false,$span=false,$datatype="string"){
				if ($this->row==0){
						if ($this->cel==0 || $this->cols==$this->cel+1){
							$style="oo_tbl1.E1";
						} else {
							$style="oo_tbl1.E2";
						}
				} elseif ($lastline) {
						if ($this->cel==0 || $this->cols==$this->cel+1){
							$style="oo_tbl1.U1";
						} else {
							$style="oo_tbl1.U2";
						}
				} else {
						if ($this->cel==0){
							$style="oo_tbl1.R1";
						} elseif ($this->cols==$this->cel+1){
							$style="oo_tbl1.R2";
						} else {
							$style="oo_tbl1.M1";
						}
				}
				
				if ($bold){
					$tx_style="P7";
				} else {
					$tx_style="P8";
				}
				if ($span){
						$sp = ' table:number-columns-spanned="'.$span.'" ';
						$sp2= '<table:covered-table-cell/>';
						$this->cel = ($this->cel-1)+$span;
				} else {
						$sp = '';
						$sp2= '';
				}
				$this->table.=	'<table:table-cell table:style-name="'.$style.'" '.$sp.'office:value-type="'.$datatype.'">' .
                        		'<text:p text:style-name="'.$tx_style.'">'.$cell_data.'</text:p></table:table-cell>';
                if ($span){
                		for ($i=1;$i<$span;$i++){
                				$this->table.='<table:covered-table-cell/>';
                		}
                }
				$this->cel++;
		}
		
		function end_table(){
				$this->table.="</table:table>";
		}
}

?>
