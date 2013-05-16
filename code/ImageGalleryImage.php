<?php

/**
 * @see Image
 */
class ImageGalleryImage extends DataExtension {

	public function generateRotateClockwise(GD $gd) {
		return $gd->rotate(90);
	}

	public function generateRotateCounterClockwise(GD $gd) {
		return $gd->rotate(270);
	}

	public function Landscape() {
		return $this->owner->getWidth() > $this->owner->getHeight();
	}

	public function Portrait() {
		return $this->owner->getWidth() < $this->owner->getHeight();
	}

	function BackLinkTracking() {
		return false;
	}

}
