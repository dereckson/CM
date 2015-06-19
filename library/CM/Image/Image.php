<?php

class CM_Image_Image extends CM_File {

    const FORMAT_JPEG = 1;
    const FORMAT_GIF = 2;
    const FORMAT_PNG = 3;

    /** @var Imagick|null */
    private $_imagick;

    /** @var int */
    private $_compressionQuality = 90;

    /** @var bool|null */
    private $_animated;

    /**
     * @param string $imageBlob
     * @throws CM_Exception
     */
    public function __construct($imageBlob) {
        $imageBlob = (string) $imageBlob;
        try {
            $imagick = new Imagick();
            $imagick->readImageBlob($imageBlob);
            if ($imagick->getIteratorIndex() > 0) {
                $this->_animated = true;
                $imagick = $imagick->coalesceImages();
            } else {
                $this->_animated = false;
            }
        } catch (ImagickException $e) {
            throw new CM_Exception('Cannot load Imagick instance for `' . $this->getPath() . '`: ' . $e->getMessage());
        }
        $this->_imagick = $imagick;
    }

    /**
     * @return CM_Image_Image
     */
    public function getClone() {
        return clone $this;
    }

    /**
     * @return string
     * @throws CM_Exception
     */
    public function getBlob() {
        try {
            if ($this->_getAnimationRequired($this->getFormat())) {
                $imageBlob = $this->_imagick->getImagesBlob();
            } else {
                $imageBlob = $this->_imagick->getImageBlob();
            }
        } catch (ImagickException $e) {
            throw new CM_Exception('Cannot get image blob: ' . $e->getMessage());
        }
        return $imageBlob;
    }

    /**
     * @param int       $widthMax
     * @param int       $heightMax
     * @param bool|null $square
     * @return $this
     * @throws CM_Exception
     */
    public function resize($widthMax, $heightMax, $square = null) {
        $square = isset($square) ? (bool) $square : false;

        $dimensions = self::calculateDimensions($this->getWidth(), $this->getHeight(), $widthMax, $heightMax, $square);
        $this->resizeSpecific($dimensions['width'], $dimensions['height'], $dimensions['offsetX'], $dimensions['offsetY']);
        return $this;
    }

    /**
     * @param int      $widthResize
     * @param int      $heightResize
     * @param int|null $offsetX
     * @param int|null $offsetY
     * @return $this
     * @throws CM_Exception
     * @throws CM_Exception_Invalid
     */
    public function resizeSpecific($widthResize, $heightResize, $offsetX = null, $offsetY = null) {
        $width = $this->getWidth();
        $height = $this->getHeight();

        try {
            $this->_invokeOnEveryFrame(function () use ($offsetX, $offsetY, $width, $height, $widthResize, $heightResize) {
                if (null !== $offsetX && null !== $offsetY) {
                    $this->_imagick->cropImage($width, $height, $offsetX, $offsetY);
                }
                $this->_imagick->resizeImage($widthResize, $heightResize, Imagick::FILTER_CATROM, 1);
            });
        } catch (ImagickException $e) {
            throw new CM_Exception('Error when resizing image: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * @param int $angle
     * @return $this
     */
    public function rotate($angle) {
        $angle = (int) $angle;

        $this->_invokeOnEveryFrame(function () use ($angle) {
            if (true !== $this->_imagick->rotateImage(new ImagickPixel('#00000000'), $angle)) {
                throw new CM_Exception('Cannot rotate image by `' . $angle . '` degrees');
            }
        });
        return $this;
    }

    public function rotateByExif() {
        $orientation = $this->getOrientation();
        switch ($orientation) {
            case Imagick::ORIENTATION_TOPRIGHT: // flipped
            case Imagick::ORIENTATION_UNDEFINED: // undefined
            case Imagick::ORIENTATION_TOPLEFT: // normal
                break;
            case Imagick::ORIENTATION_BOTTOMLEFT: // 180° flipped
            case Imagick::ORIENTATION_BOTTOMRIGHT: // 180°
                $this->rotate(-180);
                $this->setOrientation(1);
                break;
            case Imagick::ORIENTATION_LEFTTOP: // 270° flipped
            case Imagick::ORIENTATION_RIGHTTOP: // 270°
                $this->rotate(-270);
                $this->setOrientation(1);
                break;
            case Imagick::ORIENTATION_RIGHTBOTTOM: // 90° flipped
            case Imagick::ORIENTATION_LEFTBOTTOM: // 90°
                $this->rotate(-90);
                $this->setOrientation(1);
                break;
        }
    }

    /**
     * @param int $format
     * @return $this
     * @throws CM_Exception
     */
    public function setFormat($format) {
        if (true !== $this->_imagick->setImageFormat($this->_getImagickFormat($format))) {
            throw new CM_Exception('Cannot set image format `' . $format . '`');
        }
        $this->_animated = $this->_getAnimationRequired($format);
        return $this;
    }

    public function stripProfileData() {
        $imagick = $this->_getImagick();
        $imagick->stripImage();
        $this->_writeImagick($imagick, $this->getFormat());
    }

    /**
     * @return int
     * @throws CM_Exception
     */
    public function getWidth() {
        try {
            return $this->_getImagick()->getImageWidth();
        } catch (ImagickException $e) {
            throw new CM_Exception('Cannot detect image width: ' . $e->getMessage());
        }
    }

    /**
     * @return int
     * @throws CM_Exception
     */
    public function getHeight() {
        try {
            return $this->_getImagick()->getImageHeight();
        } catch (ImagickException $e) {
            throw new CM_Exception('Cannot detect image height: ' . $e->getMessage());
        }
    }

    /**
     * @return int
     * @throws CM_Exception_Invalid
     */
    public function getFormat() {
        $imagickFormat = $this->_imagick->getImageFormat();
        switch ($imagickFormat) {
            case 'JPEG':
                return self::FORMAT_JPEG;
            case 'GIF':
                return self::FORMAT_GIF;
            case'PNG':
                return self::FORMAT_PNG;
            default:
                throw new CM_Exception_Invalid('Unsupported format `' . $imagickFormat . '`.');
        }
    }

    /**
     * @return int
     */
    public function getOrientation() {
        return $this->_getImagick()->getImageOrientation();
    }

    /**
     * @param int $orientation
     *
     * Works only on images where the meta-data already contains orientation-information
     */
    public function setOrientation($orientation) {
        $imagick = $this->_getImagick();
        $imagick->setImageOrientation((int) $orientation);
        $this->_writeImagick($imagick, $this->getFormat());
    }

    public function validateImage() {
        $this->getFormat();
    }

    /**
     * @return bool
     */
    public function isAnimated() {
        return $this->_animated;
    }

    /**
     * @param int $quality 1-100
     * @return $this
     * @throws CM_Exception
     * @throws CM_Exception_Invalid
     */
    public function setCompressionQuality($quality) {
        $quality = (int) $quality;
        if ($quality < 1 || $quality > 100) {
            throw new CM_Exception_Invalid('Invalid compression quality `' . $quality . '`, should be between 1-100.');
        }
        if (true !== $this->_imagick->setImageCompressionQuality($quality)) {
            throw new CM_Exception('Cannot set compression quality to `' . $quality . '`.');
        }
        return $this;
    }

    public function freeMemory() {
        $this->_imagick = null;
    }

    /**
     * @return Imagick
     * @throws CM_Exception
     */
    private function _getImagick() {
        if (!extension_loaded('imagick')) {
            throw new CM_Exception('Missing `imagick` extension');
        }
        if (null === $this->_imagick) {
            try {
                $imagick = new Imagick();
                $imagick->readImageBlob($this->read());
                if ($imagick->getIteratorIndex() > 0) {
                    $this->_animated = true;
                    $imagick = $imagick->coalesceImages();
                } else {
                    $this->_animated = false;
                }
            } catch (ImagickException $e) {
                throw new CM_Exception('Cannot load Imagick instance for `' . $this->getPath() . '`: ' . $e->getMessage());
            }
            $this->_imagick = $imagick;
        }
        return $this->_imagick;
    }

    /**
     * @return Imagick
     */
    private function _getImagickClone() {
        $imagick = $this->_getImagick();
        return clone $imagick;
    }

    /**
     * @param Imagick      $imagick
     * @param int          $format
     * @param CM_File|null $fileNew
     * @throws CM_Exception
     */
    private function _writeImagick(Imagick $imagick, $format, CM_File $fileNew = null) {
        if (true !== $imagick->setFormat($this->_getImagickFormat($format))) {
            throw new CM_Exception('Cannot set image format `' . $format . '`');
        }
        $compressionQuality = $this->_getCompressionQuality();
        if (true !== $imagick->setImageCompressionQuality($compressionQuality)) {
            throw new CM_Exception('Cannot set compression quality to `' . $compressionQuality . '`.');
        }
        if (!$this->_getAnimationRequired($format)) {
            try {
                $imageBlob = $imagick->getImageBlob();
            } catch (ImagickException $e) {
                throw new CM_Exception('Cannot get image blob: ' . $e->getMessage());
            }
        } else {
            try {
                $imageBlob = $imagick->getImagesBlob();
            } catch (ImagickException $e) {
                throw new CM_Exception('Cannot get images blob: ' . $e->getMessage());
            }
        }

        $file = $fileNew ? $fileNew : $this;
        $file->write($imageBlob);
        if ($this->equals($file)) {
            $this->_imagick = $imagick;
            $this->_animated = $this->_getAnimationRequired($format);
        }
    }

    /**
     * @return int
     */
    private function _getCompressionQuality() {
        return $this->_compressionQuality;
    }

    /**
     * @param int $format
     * @return string
     * @throws CM_Exception_Invalid
     */
    private function _getImagickFormat($format) {
        switch ($format) {
            case self::FORMAT_JPEG:
                return 'JPEG';
            case self::FORMAT_GIF:
                return 'GIF';
            case self::FORMAT_PNG:
                return 'PNG';
            default:
                throw new CM_Exception_Invalid('Invalid format `' . $format . '`.');
        }
    }

    /**
     * @param int $format
     * @return bool
     */
    private function _getAnimationRequired($format) {
        if (self::FORMAT_GIF === $format && $this->isAnimated()) {
            return true;
        }
        return false;
    }

    /**
     * @param Closure $callback
     */
    private function _invokeOnEveryFrame(Closure $callback) {
        if (!$this->_getAnimationRequired($this->getFormat())) {
            $callback();
        } else {
            /** @var Imagick $frame */
            foreach ($this->_imagick as $frame) {
                $callback();
            }
        }
    }

    /**
     * @param int $format
     * @return string
     * @throws CM_Exception_Invalid
     */
    public static function getExtensionByFormat($format) {
        switch ($format) {
            case self::FORMAT_JPEG:
                return 'jpg';
            case self::FORMAT_GIF:
                return 'gif';
            case self::FORMAT_PNG:
                return 'png';
            default:
                throw new CM_Exception_Invalid('Invalid format `' . $format . '`.');
        }
    }

    /**
     * @param int  $width
     * @param int  $height
     * @param int  $widthMax
     * @param int  $heightMax
     * @param bool $square
     * @return array
     */
    public static function calculateDimensions($width, $height, $widthMax, $heightMax, $square) {
        $offsetX = null;
        $offsetY = null;

        if ($square) {
            if ($width > $height) {
                $offsetX = floor(($width - $height) / 2);
                $offsetY = 0;
                $width = $height;
            } elseif ($width < $height) {
                $offsetX = 0;
                $offsetY = floor(($height - $width) / 2);
                $height = $width;
            }
        }

        if (($width > $widthMax) || ($height > $heightMax)) {
            if ($height / $heightMax > $width / $widthMax) {
                $scaleCoefficient = $heightMax / $height;
            } else {
                $scaleCoefficient = $widthMax / $width;
            }
            $heightResize = $height * $scaleCoefficient;
            $widthResize = $width * $scaleCoefficient;
        } else {
            // Don't blow image up
            $heightResize = $height;
            $widthResize = $width;
        }

        $heightResize = max($heightResize, 1);
        $widthResize = max($widthResize, 1);

        return [
            'offsetX' => (int) $offsetX,
            'offsetY' => (int) $offsetY,
            'width'   => (int) $widthResize,
            'height'  => (int) $heightResize,
        ];
    }

    private function __clone() {
        $this->_imagick = clone $this->_imagick;
    }
}
