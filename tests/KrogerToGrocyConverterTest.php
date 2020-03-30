<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Grocy\Helpers\KrogerToGrocyConverter;

final class KrogerToGrocyConverterTest extends TestCase
{
	public function testConvertUpcToBarcode(): void
	{
		$this->assertEquals(
			"0722252601421",
			KrogerToGrocyConverter::ConvertUpcToBarcode("0072225260142")
		);
		$this->assertEquals(
			"0072220005165",
			KrogerToGrocyConverter::ConvertUpcToBarcode("07222000516")
		);
		$this->assertEquals(
			"0072220005165",
			KrogerToGrocyConverter::ConvertUpcToBarcode("0000007222000516")
        );
        
        $this->assertEquals(
            "0000000041300",
            KrogerToGrocyConverter::ConvertUpcToBarcode("0000000004130")
        );
    }
    
    public function testConvertJson(): void
    {
        $testjson = file_get_contents(dirname(__FILE__) . "/testdata/receipts.json");
        $default_quantity_units = 3;
        $default_location_id = 2;

        $products = KrogerToGrocyConverter::ConvertJson(json_decode($testjson, true), $default_quantity_units, $default_location_id);

        $expectedjson = file_get_contents(dirname(__FILE__) . "/testdata/receipts_expected.json");
        $expected = json_decode($expectedjson, true);

        $this->assertEquals(count($expected), count($products));

        foreach ($expected as $index => $product)
        {
            foreach ($product as $key => $value)
            {
                $this->assertEquals($value, $products[$index][$key], "Failed matching key " . $key);
            }
        }
    }
}

?>