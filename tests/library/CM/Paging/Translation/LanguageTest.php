<?php

class CM_Paging_Translation_LanguageTest extends CMTest_TestCase {

    public function testRemove() {
        $languagePagingFoo = CM_Model_Language::create('Foo', 'foo', true)->getTranslations();
        $languagePagingBar = CM_Model_Language::create('Bar', 'bar', true)->getTranslations();

        $languagePagingFoo->set('phrase', 'foo');
        $languagePagingBar->set('phrase', 'bar');
        $this->assertSame('foo', $languagePagingFoo->get('phrase', null, true));
        $this->assertSame('bar', $languagePagingBar->get('phrase', null, true));

        $languagePagingFoo->remove('phrase');
        $this->assertNull($languagePagingFoo->get('phrase', null, true));
        $this->assertSame('bar', $languagePagingBar->get('phrase', null, true));
    }
}
