<?php

function pdfEscapeText($text) {
    $text = (string) $text;
    $text = str_replace('\\', '\\\\', $text);
    $text = str_replace('(', '\\(', $text);
    $text = str_replace(')', '\\)', $text);
    $text = str_replace(["\r", "\n"], ' ', $text);
    return $text;
}

function buildSimplePdf(array $lines) {
    $stream = "BT\n/F1 11 Tf\n50 800 Td\n";
    foreach ($lines as $i => $line) {
        if ($i > 0) {
            $stream .= "0 -16 Td\n";
        }
        $stream .= '(' . pdfEscapeText($line) . ") Tj\n";
    }
    $stream .= "ET";

    $objects = [];
    $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
    $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>";
    $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
    $objects[] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $index => $obj) {
        $offsets[] = strlen($pdf);
        $objNum = $index + 1;
        $pdf .= $objNum . " 0 obj\n" . $obj . "\nendobj\n";
    }

    $xrefOffset = strlen($pdf);
    $count = count($objects) + 1;
    $pdf .= "xref\n0 " . $count . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($i = 1; $i < $count; $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }

    $pdf .= "trailer\n<< /Size " . $count . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

    return $pdf;
}

function Crear_Ticket($items, $PagoTotal) {
    $fecha = date('d/m/Y H:i:s');
    $lines = [];
    $lines[] = 'AUTOHUB - TICKET DE COMPRA';
    $lines[] = 'Fecha: ' . $fecha;
    $lines[] = '----------------------------------------------';

    foreach ($items as $item) {
        $name = $item['name'] ?? 'Producto';
        $qty  = intval($item['quantity'] ?? 1);
        $pr   = floatval($item['price'] ?? 0);
        $sub  = $qty * $pr;
        $lines[] = $name . ' x' . $qty . '  $' . number_format($pr, 2) . '  Subtotal: $' . number_format($sub, 2);
    }

    $lines[] = '----------------------------------------------';
    $lines[] = 'TOTAL: $' . number_format((float)$PagoTotal, 2);
    $lines[] = 'Gracias por tu compra en AutoHub.';

    if (!file_exists('tickets')) {
        mkdir('tickets', 0777, true);
    }

    $fecha_arch = date('Y-m-d_H-i-s');
    $nombre_archivo = __DIR__ . "/tickets/ticket_{$fecha_arch}.pdf";
    $pdfBytes = buildSimplePdf($lines);

    return file_put_contents($nombre_archivo, $pdfBytes) ? $nombre_archivo : false;
}