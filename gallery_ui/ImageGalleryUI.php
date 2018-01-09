<?php

abstract class ImageGalleryUI {

	public static $link_to_demo;

	public $layout_template = "GalleryUI_layout";

	public $item_template = "GalleryUI_item";

	protected $ImageGalleryPage;

	abstract public function initialize();

	public function setImageGalleryPage(ImageGalleryPage $page) {
		$this->ImageGalleryPage = $page;
	}

    /**
     * @param SS_List $items
     * @return SS_List
     */
	public function updateItems(SS_List $items) {
		return $items;
	}

}
