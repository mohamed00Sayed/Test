<?php

namespace Moham\Test\Main;

class Dimension implements \JsonSerializable
{
    private $height;
    private $width;
    private $length;

    public function __construct($height, $width, $length)
    {
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height
        ];
    }
}
