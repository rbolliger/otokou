<?php
//header("Content-type: text/xml"); 

$version = '1.0';
$encoding = 'UTF-8';
$rootName = 'root';

$writer = new XMLWriter();
$writer->openURI('php://output');
$writer->startDocument($version,$encoding);

$writer->setIndent(true);

// declare root element
$writer->startElement($rootName);

// declare otokou element
$writer->startElement('otokou');
$writer->writeAttribute('version', '1.0');

//header element
$writer->startElement("header");
$writer->writeElement('error', '000');
$writer->writeElement('request', 'get_user');
$writer->endElement();

//body element
$writer->startElement("body");
$writer->writeElement('firstname', 'asdrubale');
$writer->writeElement('lastname', 'arnaldo');
$writer->endElement(); 

// End otokou
$writer->endElement();

// End root
$writer->endElement();

$writer->endDocument();

$writer->flush();
?> 