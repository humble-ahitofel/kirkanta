<?php

namespace App\EventListener;

use App\Entity\Picture;
use App\Events;
use App\Event\ImageUploadEvent;
use Imagine\Exception\RuntimeException as InvalidImageException;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Vich\UploaderBundle\Event\Event as VichEvent;
use Vich\UploaderBundle\Event\Events as VichEvents;

/**
 * Handles scaling uploaded images to given sizes.
 */
class ImageResizeSubscriber implements EventSubscriberInterface
{
    private $imagine;
    private $sizes;

    public static function getSubscribedEvents()
    {
        return [
            VichEvents::POST_UPLOAD => 'postUpload',
        ];
    }

    public function __construct(ImagineInterface $imagine, array $size_mappings)
    {
        $this->imagine = $imagine;
        $this->sizes = $size_mappings;
    }

    public function postUpload(VichEvent $event) : void
    {
        $object = $event->getObject();

        if ($object instanceof Picture) {
            $class = get_class($object);
            $basedir = $object->getFile()->getPath();
            $filename = $object->getFile()->getFilename();
            $realpath = realpath(sprintf('%s/%s', $basedir, $filename));

            try {
                $image = $this->imagine->open($realpath);

                foreach (array_reverse($object->getSizes()) as $size) {
                    if (!isset($this->sizes[$size])) {
                        throw new \DomainException(sprintf('Invalid size \'%s\' passed.', $size));
                    }

                    list($width, $height) = explode('x', $this->sizes[$size]);
                    $path = sprintf('%s/%s/%s', dirname($basedir), $size, $filename);

                    $resize = $this->scaleSizeForImage($image, new Box($width, $height));
                    $image->resize($resize);
                    $image->save($path);

                    $object->addMeta([
                        $size => [
                            'dimensions' => [$resize->getWidth(), $resize->getHeight()],
                            'filesize' => filesize($path),
                        ]
                    ]);
                }
            } catch (InvalidImageException $e) {
                // Not a valid image file.
            }
        }
    }

    private function scaleSizeForImage(ImageInterface $image, BoxInterface $max)
    {
        $orig = $image->getSize();
        $r0 = $orig->getWidth() / $orig->getHeight();
        $r1 = $max->getWidth() / $max->getHeight();

        if ($orig->getWidth() < $max->getWidth() && $orig->getHeight() < $max->getHeight()) {
            return new Box($orig->getWidth(), $orig->getHeight());
        }

        if ($r0 > $r1) {
            $height = $orig->getHeight() * ($max->getWidth() / $orig->getWidth());
            $new_size = new Box($max->getWidth(), $height);
        } else {
            $width = $orig->getWidth() * ($max->getHeight() / $orig->getHeight());
            $new_size = new Box($width, $max->getHeight());
        }

        return $new_size;
    }
}
