<?php

namespace Grocy\Services;

use Exception;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class PrintService extends BaseService {

	/**
	 * Checks if a thermal printer has been configured
	 * @return bool
	 */
	private static function isThermalPrinterEnabled(): bool {
		//TODO
		return true;
	}

	/**
	 * Initialises the printer
	 * @return Printer handle
	 * @throws Exception if unable to connect to printer
	 */
	private static function getPrinterHandle() {
		$connector = new FilePrintConnector("php://stdout");
		return new Printer($connector);
	}


	/**
	 * Prints a grocy logo
	 * @param $printer
	 */
	private static function printHeader(Printer $printer) {
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		$printer->setTextSize(4, 4);
		$printer->setReverseColors(true);
		$printer->text("grocy");
		$printer->setJustification();
		$printer->setTextSize(1, 1);
		$printer->setReverseColors(false);
		$printer->selectPrintMode();
		$printer->feed(6);
	}

	/**
	 * @param bool $printHeader Printing of Grocy logo
	 * @param string[] $items Items to print
	 * @return string[]
	 * @throws Exception
	 */
	public function printShoppingList(bool $printHeader, array $items): array {
		if (!self::isThermalPrinterEnabled()) {
			throw new Exception("Printer is not setup enabled in configuration");
		}
		$printer = self::getPrinterHandle();
		if ($printer === false)
			throw new Exception("Unable to connect to printer");

		if ($printHeader) {
			self::printHeader($printer);
		}

		foreach ($items as $item) {
			$printer->text($item);
			$printer->feed();
		}

		$printer->feed(2);
		$printer->cut();
		$printer->close();
		return [
			'result' => "OK",
			'printed' => $items
		];
	}
}
