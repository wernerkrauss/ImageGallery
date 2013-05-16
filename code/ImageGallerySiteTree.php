<?php

/**
 * @see SiteTree
 */
class ImageGallerySiteTree extends SiteTreeExtension {

	function getGalleryFor($urlSegment) {
		$galleries = DataObject::get("ImageGalleryPage");
		if(!empty($urlSegment)) {
			$galleries = $galleries->filter(array('URLSegment' => $urlSegment));
		}
		return $galleries->first();
	}

	function RecentImages($count = 5, $urlSegment = null) {
		$gallery = $this->getGalleryFor($urlSegment);
		if ($gallery) {
			return $gallery->GalleryItems()->sort('"Created" DESC')->limit($count);
		}
		return false;
	}

	function RecentImagesGallery($count = 5, $urlSegment = null) {
		$gallery = $this->getGalleryFor($urlSegment);
		if ($gallery) {
			Requirements::themedCSS('ImageGallery');
			return $this->owner->customise(array(
				'GalleryItems' => $this->RecentImages($count, $urlSegment),
				'PreviousGalleryItems' => new ArrayList(),
				'NextGalleryItems' => new ArrayList()
			))->renderWith(array($gallery->UI->layout_template));
		}
		return false;
	}

}
