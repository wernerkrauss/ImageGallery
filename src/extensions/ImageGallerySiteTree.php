<?php

namespace TractorCow\ImageGallery\Extensions;


use SilverStripe\CMS\Model\SiteTreeExtension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;
use TractorCow\ImageGallery\Pages\ImageGalleryPage;


/**
 * @see SiteTree
 */
class ImageGallerySiteTree extends SiteTreeExtension
{

    public function getGalleryFor($urlSegment)
    {
        $galleries = DataObject::get(ImageGalleryPage::class);
        if (!empty($urlSegment)) {
            $galleries = $galleries->filter(['URLSegment' => $urlSegment]);
        }
        return $galleries->first();
    }

    public function RecentImages($count = 5, $urlSegment = null)
    {
        $gallery = $this->getGalleryFor($urlSegment);
        if ($gallery) {
            return $gallery->GalleryItems()->sort('"Created" DESC')->limit($count);
        }
        return false;
    }

    public function RecentImagesGallery($count = 5, $urlSegment = null)
    {
        $gallery = $this->getGalleryFor($urlSegment);
        if ($gallery) {
            Requirements::themedCSS('ImageGallery');
            return $this->owner->customise([
                'GalleryItems' => $this->RecentImages($count, $urlSegment),
                'PreviousGalleryItems' => new ArrayList(),
                'NextGalleryItems' => new ArrayList()
            ])->renderWith([$gallery->UI->layout_template]);
        }
        return false;
    }
}
