<?php

class CM_Image_ImageTest extends CMTest_TestCase {

    public function testValidateImage() {
        $path = DIR_TEST_DATA . 'img/test.jpg';
        $image = new CM_Image_Image($path);
        $image->validateImage();
        $this->assertTrue(true);
    }

    public function testValidateImageCorruptContent() {
        $path = DIR_TEST_DATA . 'img/corrupt-content.jpg';
        $image = new CM_Image_Image($path);
        $image->validateImage();
        $this->assertTrue(true);
    }

    public function testValidateImageJpgNoExtension() {
        $path = DIR_TEST_DATA . 'img/jpg-no-extension';
        $image = new CM_Image_Image($path);
        $image->validateImage();
        $this->assertTrue(true);
    }

    /**
     * @expectedException CM_Exception
     * @expectedExceptionMessage Unsupported format
     */
    public function testValidateImageUnsupportedFormat() {
        $path = DIR_TEST_DATA . 'img/test.tiff';
        $file = new CM_Image_Image($path);
        $file->validateImage();
    }

    /**
     * @expectedException CM_Exception
     * @expectedExceptionMessage Cannot load Imagick instance
     */
    public function testValidateImageCorruptHeader() {
        $path = DIR_TEST_DATA . 'img/corrupt-header.jpg';
        $image = new CM_Image_Image($path);
        $image->validateImage();
    }

    /**
     * @expectedException CM_Exception
     * @expectedExceptionMessage Cannot load Imagick instance
     */
    public function testValidateImageEmptyFile() {
        $path = DIR_TEST_DATA . 'img/empty.jpg';
        $image = new CM_Image_Image($path);
        $image->validateImage();
    }

    /**
     * @expectedException CM_Exception
     * @expectedExceptionMessage Cannot load Imagick instance
     */
    public function testValidateImageNoImage() {
        $path = DIR_TEST_DATA . 'test.jpg.zip';
        $image = new CM_Image_Image($path);
        $image->validateImage();
    }

    public function testDimensions() {
        $path = DIR_TEST_DATA . 'img/test.jpg';
        list($width, $height) = getimagesize($path);

        $image = new CM_Image_Image($path);
        $this->assertSame($width, $image->getWidth());
        $this->assertSame($height, $image->getHeight());
    }

    public function testRotate() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());
        $this->assertNotSame($imageOriginal->getWidth(), $imageOriginal->getHeight());

        $image->rotate(90);
        $this->assertSame($imageOriginal->getHeight(), $image->getWidth());
        $this->assertSame($imageOriginal->getWidth(), $image->getHeight());
    }

    public function testRotateAnimatedGif() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/animated.gif');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());
        $this->assertNotSame($imageOriginal->getWidth(), $imageOriginal->getHeight());

        $image->rotate(90, CM_Image_Image::FORMAT_GIF);
        $this->assertSame($imageOriginal->getHeight(), $image->getWidth());
        $this->assertSame($imageOriginal->getWidth(), $image->getHeight());
        $this->assertEquals(148987, $image->getSize(), '', 5000);
    }

    public function testRotateByExif() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test-rotated.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());
        $this->assertSame(6, $image->getOrientation());
        $image->rotateByExif();
        $this->assertSame(1, $image->getOrientation());
        $expectedWidth = $imageOriginal->getHeight();
        $this->assertSame($expectedWidth, $image->getWidth());
        $expectedHeight = $imageOriginal->getWidth();
        $this->assertSame($expectedHeight, $image->getHeight());
    }

    public function testStripProfileData() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test-rotated.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());
        $this->assertSame(6, $image->getOrientation());
        $image->stripProfileData();
        $image = new CM_Image_Image($image);
        $this->assertSame(0, $image->getOrientation());
    }

    public function testSetOrientation() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test-rotated.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());
        $this->assertSame(6, $image->getOrientation());
        $image->setOrientation(2);
        $image = new CM_Image_Image($image);
        $this->assertSame(2, $image->getOrientation());
    }

    public function testGetFormat() {
        $pathList = array(
            DIR_TEST_DATA . 'img/test.jpg'            => CM_Image_Image::FORMAT_JPEG,
            DIR_TEST_DATA . 'img/test.gif'            => CM_Image_Image::FORMAT_GIF,
            DIR_TEST_DATA . 'img/test.png'            => CM_Image_Image::FORMAT_PNG,
            DIR_TEST_DATA . 'img/jpg-no-extension'    => CM_Image_Image::FORMAT_JPEG,
            DIR_TEST_DATA . 'img/corrupt-content.jpg' => CM_Image_Image::FORMAT_JPEG,
        );

        foreach ($pathList as $path => $format) {
            $image = new CM_Image_Image($path);
            $this->assertSame($format, $image->getFormat());
        }
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Unsupported format
     */
    public function testGetFormatUnsupportedFormat() {
        $path = DIR_TEST_DATA . 'img/test.tiff';
        $image = new CM_Image_Image($path);
        $image->getFormat();
    }

    public function testIsAnimated() {
        $imageJpeg = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $this->assertFalse($imageJpeg->isAnimated());

        $imageAnimatedGif = new CM_Image_Image(DIR_TEST_DATA . 'img/animated.gif');
        $this->assertTrue($imageAnimatedGif->isAnimated());
    }

    public function testIsAnimatedConvertingToNonAnimated() {
        $file = new CM_File(DIR_TEST_DATA . 'img/animated.gif');
        $image = CM_Image_Image::createTmp('gif', $file->read());
        $this->assertTrue($image->isAnimated());

        $image->convert(CM_Image_Image::FORMAT_GIF);
        $this->assertTrue($image->isAnimated());

        $image->convert(CM_Image_Image::FORMAT_JPEG);
        $this->assertFalse($image->isAnimated());
    }

    public function testGetWidthHeight() {
        $pathList = array(
            DIR_TEST_DATA . 'img/test.jpg',
            DIR_TEST_DATA . 'img/test.gif',
            DIR_TEST_DATA . 'img/test.png',
        );
        foreach ($pathList as $path) {
            $image = new CM_Image_Image($path);
            $this->assertSame(363, $image->getWidth());
            $this->assertSame(214, $image->getHeight());
        }
    }

    public function testGetWidthHeightAnimatedGif() {
        $image = new CM_Image_Image(DIR_TEST_DATA . 'img/animated.gif');
        $this->assertSame(180, $image->getWidth());
        $this->assertSame(135, $image->getHeight());
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Invalid compression quality
     */
    public function testSetCompressionQualityInvalid() {
        $path = DIR_TEST_DATA . 'img/test.jpg';
        $image = new CM_Image_Image($path);
        $image->setCompressionQuality(-188);
    }

    public function testConvert() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $imageNew = CM_Image_Image::createTmp();
        $image->convert(CM_Image_Image::FORMAT_GIF, $imageNew);
        $this->assertSame($image->getWidth(), $imageNew->getWidth());
        $this->assertSame($image->getHeight(), $imageNew->getHeight());
        $this->assertNotSame($image->read(), $imageNew->read());
    }

    public function testConvertSameFile() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $image->convert(CM_Image_Image::FORMAT_GIF);
        $this->assertSame($imageOriginal->getWidth(), $image->getWidth());
        $this->assertSame($imageOriginal->getHeight(), $image->getHeight());
        $this->assertNotSame($imageOriginal->read(), $image->read());
    }

    public function testConvertSameFileSameFormat() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $image->convert(CM_Image_Image::FORMAT_JPEG);
        $this->assertSame($imageOriginal->getWidth(), $image->getWidth());
        $this->assertSame($imageOriginal->getHeight(), $image->getHeight());
        $this->assertSame($imageOriginal->read(), $image->read());
    }

    public function testConvertAllFormats() {
        $formatList = array(
            CM_Image_Image::FORMAT_JPEG,
            CM_Image_Image::FORMAT_GIF,
            CM_Image_Image::FORMAT_PNG,
        );
        $pathList = array(
            DIR_TEST_DATA . 'img/test.jpg',
            DIR_TEST_DATA . 'img/test.gif',
            DIR_TEST_DATA . 'img/test.png',
        );
        foreach ($pathList as $path) {
            foreach ($formatList as $format) {
                $imageOriginal = new CM_Image_Image($path);
                $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

                $image->convert($format);
                $this->assertSame($imageOriginal->getWidth(), $image->getWidth());
                $this->assertSame($imageOriginal->getHeight(), $image->getHeight());
                $this->assertGreaterThan(0, $image->getSize());
            }
        }
    }

    public function testConvertJpegCompression() {
        $qualityList = array(
            1   => 4056,
            30  => 6439,
            60  => 8011,
            90  => 14865,
            95  => 18854,
            100 => 37649,
        );
        $path = DIR_TEST_DATA . 'img/test.gif';
        foreach ($qualityList as $quality => $expectedFileSize) {
            $imageOriginal = new CM_Image_Image($path);
            $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

            $image->setCompressionQuality($quality);
            $image->convert(CM_Image_Image::FORMAT_JPEG);
            $fileSizeDelta = $expectedFileSize * 0.05;
            $this->assertEquals($expectedFileSize, $image->getSize(), 'File size mismatch for quality `' . $quality . '`', $fileSizeDelta);
        }
    }

    public function testResize() {
        $image = $this->mockClass('CM_Image_Image')->newInstanceWithoutConstructor();
        $image->mockMethod('getWidth')->set(250);
        $image->mockMethod('getHeight')->set(150);

        $targetFile = CM_File::createTmp();
        $format = 'jpg';

        $resizeSpecificMethod = $image->mockMethod('resizeSpecific')
            ->set(function ($width, $height, $offsetX, $offsetY, $formatNew, $fileNew) use ($format, $targetFile) {
                $this->assertSame(250, $width);
                $this->assertSame(150, $height);
                $this->assertSame(0, $offsetX);
                $this->assertSame(0, $offsetY);
                $this->assertSame($format, $formatNew);
                $this->assertSame($targetFile, $fileNew);
            });
        /** @var CM_Image_Image $image */
        $image->resize(500, 400, false, $format, $targetFile);
        $this->assertSame(1, $resizeSpecificMethod->getCallCount());
    }

    public function testResizeNoInvalidDimensions() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());
        $width = $image->getWidth();
        $height = $image->getHeight();
        $widthResize = ($width > $height) ? 1 : (int) round($width / $height);
        $heightResize = ($height > $width) ? 1 : (int) round($height / $width);
        $image->resize($widthResize, $heightResize);
        $this->assertSame($widthResize, $image->getWidth());
        $this->assertSame($heightResize, $image->getHeight());
    }

    public function testResizeSquare() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $image->resize(50, 50, true);
        $this->assertSame(50, $image->getWidth());
        $this->assertSame(50, $image->getHeight());
    }

    public function testResizeSquareNoBlowup() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $sizeExpected = min($image->getWidth(), $image->getHeight());
        $image->resize(5000, 5000, true);
        $this->assertSame($sizeExpected, $image->getWidth());
        $this->assertSame($sizeExpected, $image->getHeight());
    }

    public function testResizeFileSize() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());
        $this->assertEquals(17661, $image->getSize(), '', 300);

        $image->setCompressionQuality(90);
        $image->resize(100, 100);
        $this->assertEquals(4620, $image->getSize(), '', 300);
    }

    public function testResizeAnimatedGif() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/animated.gif');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $image->resize(50, 50, true, CM_Image_Image::FORMAT_GIF);
        $this->assertSame('image/gif', $image->getMimeType());
        $this->assertSame(50, $image->getWidth());
        $this->assertSame(50, $image->getHeight());
        $this->assertEquals(25697, $image->getSize(), '', 2000);
    }

    public function testResizeAnimatedGifToJpeg() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/animated.gif');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $image->resize(50, 50, true, CM_Image_Image::FORMAT_JPEG);
        $this->assertSame('image/jpeg', $image->getMimeType());
        $this->assertSame(50, $image->getWidth());
        $this->assertSame(50, $image->getHeight());
        $this->assertEquals(1682, $image->getSize(), '', 100);
    }

    public function testResizeSpecific() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $image->resizeSpecific(50, 50, 20, 20);
        $this->assertSame(50, $image->getWidth());
        $this->assertSame(50, $image->getHeight());
    }

    public function testResizeSpecificKeepExif() {
        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test-rotated.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());
        $imageNew = CM_Image_Image::createTmp(null);
        $image->resize($image->getWidth(), $image->getHeight(), null, null, $imageNew);
        $this->assertSame(6, $imageNew->getOrientation());
    }

    public function testGetExtensionByFormat() {
        $this->assertSame('jpg', CM_Image_Image::getExtensionByFormat(CM_Image_Image::FORMAT_JPEG));
        $this->assertSame('gif', CM_Image_Image::getExtensionByFormat(CM_Image_Image::FORMAT_GIF));
        $this->assertSame('png', CM_Image_Image::getExtensionByFormat(CM_Image_Image::FORMAT_PNG));
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Invalid format
     */
    public function testGetExtensionByFormatInvalid() {
        CM_Image_Image::getExtensionByFormat(-999);
    }

    public function testCreate() {
        $rawImageData = file_get_contents(DIR_TEST_DATA . 'img/test.jpg', 'r');
        $image = CM_Image_Image::create(CM_Bootloader::getInstance()->getDirTmp() . 'test.jpg', $rawImageData);
        $this->assertEquals('image/jpeg', $image->getMimeType());
        $image->delete();
    }

    public function testFreeMemory() {
        $getMemoryUsage = function () {
            $pid = CM_Process::getInstance()->getProcessId();
            $memory = CM_Util::exec('ps', ['-o', 'rss', '--no-headers', '--pid', $pid]);
            return (int) $memory;
        };

        $imageOriginal = new CM_Image_Image(DIR_TEST_DATA . 'img/test.jpg');
        $image = CM_Image_Image::createTmp(null, $imageOriginal->read());

        $memoryUsage = $getMemoryUsage();
        $memoryUsageMaxExpected = $memoryUsage + 20 * 1000;

        $image->resizeSpecific(3000, 3000);
        $this->assertGreaterThan($memoryUsageMaxExpected, $getMemoryUsage());

        $image->freeMemory();
        $this->assertLessThan($memoryUsageMaxExpected, $getMemoryUsage());

        $image->validateImage();
    }

    public function testCalculateDimensions() {
        $dimensions = CM_Image_Image::calculateDimensions( 2000, 1600, 500, 600, false );

        $this->assertEquals( 500, $dimensions['width']);
        $this->assertEquals( 400, $dimensions['height']);
        $this->assertEquals( 0, $dimensions['offsetX']);
        $this->assertEquals( 0, $dimensions['offsetY']);
    }

    public function testCalculateDimensionsSquare() {
        $dimensions = CM_Image_Image::calculateDimensions( 2000, 1600, 500, 500, true );

        $this->assertEquals( 500, $dimensions['width']);
        $this->assertEquals( 500, $dimensions['height']);
        $this->assertEquals( 200, $dimensions['offsetX']);
        $this->assertEquals( 0, $dimensions['offsetY']);
    }

    public function testCalculateDimensionsLower() {
        $dimensions = CM_Image_Image::calculateDimensions( 100, 200, 1000, 500, false );

        $this->assertEquals( 100, $dimensions['width']);
        $this->assertEquals( 200, $dimensions['height']);
        $this->assertEquals( 0, $dimensions['offsetX']);
        $this->assertEquals( 0, $dimensions['offsetY']);
    }
}