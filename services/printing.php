<?php

use Fpdf\Fpdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\PhpWord;

class Printing
{
  function __construct()
  {
    $this->db = new Database();
  }

  function pdf()
  {
    $pdf = new Fpdf('P', 'mm', 'A5');

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(100, 7, 'Laporan Data Pegawai', 0, 1, 'C');

    $pdf->Cell(10, 7, '', 0, 1);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 6, 'No', 1, 0);
    $pdf->Cell(15, 6, 'NIP', 1, 0);
    $pdf->Cell(25, 6, 'Nama', 1, 0);
    $pdf->Cell(25, 6, 'Tempat Lahir', 1, 0);
    $pdf->Cell(25, 6, 'Tanggal Lahir', 1, 0);
    $pdf->Cell(30, 6, 'Foto', 1, 1);

    $pdf->SetFont('Arial', '', 10);

    $data = $this->db->query('SELECT * FROM pegawai');
    $i = 1;

    foreach ($data as $row) {
      $pdf->Cell(10, 20, $i++, 1, 0);
      $pdf->Cell(15, 20, $row['nip'], 1, 0);
      $pdf->Cell(25, 20, $row['nama_pegawai'], 1, 0);
      $pdf->Cell(25, 20, $row['tempat_lahir'], 1, 0);
      $pdf->Cell(25, 20, $row['tanggal_lahir'], 1, 0);

      $img = 'img/' . $row['foto'];
      $pdf->Cell(30, 20, $pdf->Image($img, $pdf->GetX(), $pdf->GetY(), 17), 1, 1);
    }

    $pdf->Output();
  }

  function excel()
  {
    $spreadsheet = new Spreadsheet();

    $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'LAPORAN DATA PEGAWAI');
    $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(13);
    $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
    $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');

    $spreadsheet->getActiveSheet()
      ->setCellValue('A3', 'NO')
      ->setCellValue('B3', 'NIP')
      ->setCellValue('C3', 'NAMA PEGAWAI')
      ->setCellValue('D3', 'TEMPAT LAHIR')
      ->setCellValue('E3', 'TANGGAL LAHIR')
      ->setCellValue('F3', 'FOTO');

    $spreadsheet->getActiveSheet()->getStyle('A1:F3')->getFont()->setBold(true);

    $data = $this->db->query('SELECT * FROM pegawai');
    $rowID = 4;
    $i = 1;

    foreach ($data as $d) {

      $spreadsheet->getActiveSheet()
        ->setCellValue('A' . $rowID, $i++)
        ->setCellValue('B' . $rowID, $d['nip'])
        ->setCellValue('C' . $rowID, $d['nama_pegawai'])
        ->setCellValue('D' . $rowID, $d['tempat_lahir'])
        ->setCellValue('E' . $rowID, $d['tanggal_lahir']);

      $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
      $objDrawing->setPath('img/' . $d['foto']);
      $objDrawing->setCoordinates('F' . $rowID);
      $objDrawing->setOffsetX(5);
      $objDrawing->setOffsetY(5);
      $objDrawing->setWidth(50);
      $objDrawing->setHeight(50);
      $objDrawing->setWorksheet($spreadsheet->getActiveSheet());

      $spreadsheet->getActiveSheet()->getRowDimension($rowID)->setRowHeight(50);
      $rowID++;
    }

    foreach (range('A', 'E') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $border = array(
      'allBorders' => array(
        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
      )
    );

    $spreadsheet->getActiveSheet()->getStyle('A3' . ':F' . ($rowID - 1))
      ->getBorders()->applyFromArray($border);

    $alignment = array(
      'alignment' => array(
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
      )
    );

    $spreadsheet->getActiveSheet()->getStyle('A3' . ':F' . ($rowID - 1))->applyFromArray($alignment);

    $filename = 'datapgw-excel.xlsx';
    ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $objWriter->save('php://output');
    ob_end_clean();
    exit;
  }

  function word()
  {
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    $title = array('size' => 16, 'bold' => true);
    $section->addText("Laporan Data Pegawai", $title);
    $section->addTextBreak(1);

    $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
    $styleCell = array('valign' => 'center');
    $fontHeader = array('bold' => true);
    $noSpace = array('spaceAfter' => 0);
    $imgStyle = array('width' => 50, 'height' => 50);

    $phpWord->addTableStyle('mytable', $styleTable);

    $table = $section->addTable('mytable');
    $table->addRow();
    $table->addCell(500, $styleCell)->addText('NO', $fontHeader, $noSpace);
    $table->addCell(750, $styleCell)->addText('NIP', $fontHeader, $noSpace);
    $table->addCell(1250, $styleCell)->addText('NAMA', $fontHeader, $noSpace);
    $table->addCell(1250, $styleCell)->addText('TPT LAHIR', $fontHeader, $noSpace);
    $table->addCell(1250, $styleCell)->addText('TGL LAHIR', $fontHeader, $noSpace);
    $table->addCell(1500, $styleCell)->addText('FOTO', $fontHeader, $noSpace);

    $data = $this->db->query('SELECT * FROM pegawai');
    $i = 1;

    foreach ($data as $d) {
      $table->addRow();
      $table->addCell(500, $styleCell)->addText($i++, array(), $noSpace);
      $table->addCell(750, $styleCell)->addText($d['nip'], array(), $noSpace);
      $table->addCell(1250, $styleCell)->addText($d['nama_pegawai'], array(), $noSpace);
      $table->addCell(1250, $styleCell)->addText($d['tempat_lahir'], array(), $noSpace);
      $table->addCell(1250, $styleCell)->addText($d['tanggal_lahir'], array(), $noSpace);
      $table->addCell(1500, $styleCell)->addImage('img/' . $d['foto'], $imgStyle);
    }

    $filename = "datapgw-word.docx";
    header('Content-Type: application/msword');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('php://output');
    exit;
  }
}
