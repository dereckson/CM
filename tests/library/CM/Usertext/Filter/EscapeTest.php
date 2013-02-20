<?php

class CM_Usertext_Filter_EscapeTest extends CMTest_TestCase {

	public static function tearDownAfterClass() {
		CMTest_TH::clearEnv();
	}

	public function testProcess() {
		$text = '<b>foo</b> <script></script> <strong>bar</strong>';
		$expected = '&lt;b&gt;foo&lt;/b&gt; &lt;script&gt;&lt;/script&gt; &lt;strong&gt;bar&lt;/strong&gt;';
		$filter = new CM_Usertext_Filter_Escape();
		$actual = $filter->transform($text);

		$this->assertEquals($expected, $actual);
	}

}
