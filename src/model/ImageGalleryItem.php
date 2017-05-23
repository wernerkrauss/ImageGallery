<?php

namespace TractorCow\ImageGallery\Model;


use ImageGalleryUI;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use TractorCow\ImageGallery\Pages\ImageGalleryPage;


/**
 * Class \TractorCow\ImageGallery\Model\ImageGalleryItem
 *
 * @property string $Caption
 * @property int $SortOrder
 * @property int $ImageGalleryPageID
 * @property int $AlbumID
 * @property int $ImageID
 * @method \TractorCow\ImageGallery\Pages\ImageGalleryPage ImageGalleryPage()
 * @method \TractorCow\ImageGallery\Model\ImageGalleryAlbum Album()
 * @method \SilverStripe\Assets\Image Image()
 */
class ImageGalleryItem extends DataObject
{

    private static $table_name = 'ImageGalleryItem';

    /**
     * User interface for gallery
     *
     * @var ImageGalleryUI
     */
    protected $UI;

    /**
     * @config
     * @var string
     */
    private static $delete_permission = "CMS_ACCESS_CMSMain";

    public function getTitle()
    {
        if ($this->Caption) {
            return $this->dbObject('Caption')->FirstSentence();
        }
        if ($image = $this->Image()) {
            return $image->Title;
        }
        return parent::getTitle();
    }

    private static $db = [
        'Caption' => 'Text',
        'SortOrder' => 'Int'
    ];

    private static $has_one = [
        'ImageGalleryPage' => ImageGalleryPage::class,
        'Album' => ImageGalleryAlbum::class,
        'Image' => Image::class
    ];

    private static $default_sort = '"SortOrder" ASC';

    private static $summary_fields = [
        'Image.CMSThumbnail' => 'Image',
        'Caption' => 'Image Caption'
    ];

    public function getCMSFields()
    {
        $fields = new FieldList(new TabSet('Root'));

        // Details
        $fields->addFieldToTab('Root.Main',
            new TextareaField('Caption', _t('TractorCow\\ImageGallery\\Model\\ImageGalleryItem.CAPTION', 'Caption')));

        // Create image
        $imageField = new UploadField(Image::class);
        $imageField->getValidator()->setAllowedExtensions(File::config()->app_categories['image']);
        $fields->addFieldToTab('Root.Main', $imageField);

        return $fields;
    }

    public function Thumbnail()
    {
        $page = $this->ImageGalleryPage();
        $image = $this->Image();
        if (!$image) {
            return null;
        } elseif ($page->Square) {
            return $image->CroppedImage($page->ThumbnailSize, $page->ThumbnailSize);
        } else {
            return $image->SetHeight($page->ThumbnailSize);
        }
    }

    public function Medium()
    {
        $page = $this->ImageGalleryPage();
        $image = $this->Image();
        if (!$image) {
            return null;
        } else {
            return $image->SetSizeRatio($page->MediumSize, $page->MediumSize);
        }
    }

    public function Large()
    {
        $page = $this->ImageGalleryPage();
        $image = $this->Image();
        if (!$image) {
            return null;
        } elseif ($image->Landscape()) {
            return $image->SetWidth($this->ImageGalleryPage()->NormalSize);
        } else {
            $height = $page->NormalHeight > 0
                ? $page->NormalHeight
                : $page->NormalSize;
            return $image->SetHeight($height);
        }
    }

    public function setUI(ImageGalleryUI $ui)
    {
        $this->UI = $ui;
    }

    public function GalleryItem()
    {
        if ($this->UI) {
            return $this->renderWith([$this->UI->item_template]);
        }
        return false;
    }

    public function canDelete($member = null)
    {
        return Permission::check(self::config()->delete_permission, 'any', $member);
    }
}
