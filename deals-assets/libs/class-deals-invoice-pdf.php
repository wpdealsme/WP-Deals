<?php

ini_set('display_errors','On');
require_once 'fpdf.php';

class Deals_Invoice_Pdf extends FPDF {
    
    private $_invoice_vars;
    
    public function __construct($vars) {
        $this->FPDF();
        $this->_invoice_vars = $vars;
    }
	
	public function test() {
		$this->AddPage();
		$this->SetFont('Arial','B',16);
		$this->Cell(40,10,'Hello World!');
		$this->Output();
	}	
    
    public function make($filename='invoice') {
        
        if(is_array($this->_invoice_vars) && !empty($this->_invoice_vars)) {
            extract($this->_invoice_vars);
        }
        
        $filepath = DEALS_ASSETS.'invoices/'.$filename.'-'.$checkVerify->transaction_id.'.pdf';        
        $buy_date_raw = date_create($checkVerify->buy_date);
		$buy_date = date_format($buy_date_raw, 'M d, y');
        
        if(!file_exists($filepath)) {
        
            $this->AddPage();
            $this->SetFont('Arial','B',16);
            $this->Cell(40,10,'INVOICE');
            $this->Ln(20);
            $this->SetFont('Arial','',12);
            $this->Cell(110);
            $this->MultiCell(80,5,strip_tags($invoice_options['info']),0,'R');
            $this->Ln(10);
            $this->Cell(143);
            if(empty($invoice_options['logo_url'])) {
                $this->Cell(40,20,'logo',0,0,'R');
            }else{
                $this->Image($invoice_options['logo_url']);
            }            
            $this->Ln(30);
            $this->Cell(190,10,$invoice_options['store_name'],'B');
            $this->Ln(13);
            $this->SetFont('Arial','',10);
            $this->SetFillColor(204,204,204);    
            $this->Cell(65);    
            $this->MultiCell(60,5,'Invoice'."\n".'#'.$checkVerify->transaction_id,0,'C',true);
            $this->SetY(119);
            $this->SetFillColor(173,173,173);
            $this->Cell(130);
            $this->SetTextColor(255,255,255);
            $this->SetFont('Arial','B');
            $this->MultiCell(60,5,'Date'."\n".$buy_date,0,'C',true);
            $this->SetTextColor(0);
            $this->SetFont('Arial');
            $this->SetFillColor(204,204,204);
            $this->Cell(130);
            $this->MultiCell(60,5,'Amount Due'."\n".'$'.$checkVerify->total_price,0,'C',true);
            $this->Ln(30);
            $this->SetTextColor(173,173,187);
            $this->SetFont('Arial','B',12);    
            $this->Cell(60,10,'ITEM NAME','B',0);
            $this->Cell(60,10,'USER NAME','B',0);
            $this->Cell(30,10,'PRICE','B',0);
            $this->Cell(40,10,'LINK','B',0);
            $this->Ln(20);
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial','B',10);			
			$this->MultiCell(50,3,$invoice_data['title']);			
            $this->Cell(60,10,'','B',0);			
            $this->Cell(60,10,$invoice_data['user_name'],'B',0);
            $this->Cell(30,10,'$'.$checkVerify->total_price,'B',0);    
            $this->SetTextColor(66,187,289);
            $this->Cell(40,10,'Click Here','B',0,'',false,$invoice_data['link']);
            $this->Ln(10);
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial');    
            $this->Cell(60);
            $this->Cell(60,10,'Total');
            $this->SetFont('Arial','B');
            $this->Cell(30,10,'$'.$checkVerify->total_price);
            $this->Ln(5);
            $this->SetFont('Arial');
            $this->Cell(60);
            $this->Cell(60,10,'Amount Paid');
            $this->SetFont('Arial','B');
            $this->Cell(30,10,'$'.$checkVerify->total_price);
            $this->Ln(5);
            $this->SetFont('Arial');
            $this->Cell(60);
            $this->Cell(60,10,'Balance Due');
            $this->SetFont('Arial','B');
            $this->Cell(30,10,'$0.00');
            $this->Ln(10);
            $this->Cell(60,10,'','T');
            $this->Cell(60,10,'','T');
            $this->Cell(70,10,'','T');			
            $this->Image($invoice_options['barcode'],140,230);
            
            $this->Output($filepath,'F');
        
        }        
        
    }
    
}