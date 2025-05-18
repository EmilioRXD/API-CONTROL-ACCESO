<?php
require_once __DIR__ . '/lib/tcpdf/tcpdf.php';

// Crear un archivo de log para depuración
file_put_contents(__DIR__ . '/debug_tcpdf.txt', "Iniciando prueba TCPDF\n");

try {
    // Crear nueva instancia de TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    file_put_contents(__DIR__ . '/debug_tcpdf.txt', "TCPDF instanciado correctamente\n", FILE_APPEND);

    // Configurar PDF
    $pdf->SetCreator('Test');
    $pdf->SetAuthor('Sistema');
    $pdf->SetTitle('Prueba PDF');
    $pdf->SetSubject('Prueba PDF');
    $pdf->SetKeywords('prueba, pdf');
    
    // Eliminar cabecera y pie de página
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Añadir una página
    $pdf->AddPage();
    
    // Establecer fuente
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Añadir contenido
    $pdf->Cell(0, 10, 'Prueba de PDF', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Esta es una prueba para verificar si TCPDF funciona correctamente.', 0, 1);
    
    // Datos de prueba
    $data = array(
        array('1', 'Fila 1', 'Dato 1'),
        array('2', 'Fila 2', 'Dato 2'),
        array('3', 'Fila 3', 'Dato 3')
    );
    
    // Crear tabla
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(20, 7, 'ID', 1, 0, 'C');
    $pdf->Cell(40, 7, 'Nombre', 1, 0, 'C');
    $pdf->Cell(40, 7, 'Valor', 1, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    foreach($data as $row) {
        $pdf->Cell(20, 6, $row[0], 1, 0);
        $pdf->Cell(40, 6, $row[1], 1, 0);
        $pdf->Cell(40, 6, $row[2], 1, 1);
    }
    
    // Generar PDF y guardarlo
    $pdf_content = $pdf->Output('test.pdf', 'S');
    file_put_contents(__DIR__ . '/test_output.pdf', $pdf_content);
    
    file_put_contents(__DIR__ . '/debug_tcpdf.txt', "PDF generado correctamente\n", FILE_APPEND);
    echo "PDF generado correctamente. Revisa el archivo test_output.pdf";
    
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/debug_tcpdf.txt', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo "Error al generar PDF: " . $e->getMessage();
}
?>
